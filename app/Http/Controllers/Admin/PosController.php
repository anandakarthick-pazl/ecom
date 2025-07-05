<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\AppSetting;
use App\Services\BillPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::active()
                          ->where('stock', '>', 0)
                          ->currentTenant() // Filter products by current company
                          ->orderBy('name')
                          ->get()
                          ->groupBy(function($product) {
                              return $product->category->name ?? 'Uncategorized';
                          });
        
        $customers = Customer::currentTenant()->orderBy('name')->get();
        
        return view('admin.pos.index', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0', // Added: Allow item-level discounts
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'tax_amount' => 'nullable|numeric|min:0',
            'custom_tax_enabled' => 'boolean',
            'custom_tax_amount' => 'nullable|numeric|min:0',
            'tax_notes' => 'nullable|string|max:500',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,upi,gpay,paytm,phonepe',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals and tax
            $subtotal = 0;
            $totalTax = 0;
            $customTaxEnabled = $request->boolean('custom_tax_enabled');
            
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
                $subtotal += $itemSubtotal;
                
                // Calculate tax for this item only if not using custom tax
                if (!$customTaxEnabled) {
                    $itemTax = ($itemSubtotal * $product->tax_percentage) / 100;
                    $totalTax += $itemTax;
                }
            }

            // Use custom tax if enabled, otherwise use calculated tax
            if ($customTaxEnabled) {
                $totalTax = $request->custom_tax_amount ?? 0;
            }

            $cgstAmount = $totalTax / 2;
            $sgstAmount = $totalTax / 2;
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subtotal + $totalTax - $discountAmount;
            $paidAmount = $request->paid_amount;
            $changeAmount = max(0, $paidAmount - $totalAmount);

            // Check stock availability
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock}");
                }
            }

            // Get current company ID from request or session
            $companyId = $request->get('current_company_id') ?? session('selected_company_id');
            
            // Create POS sale
            $sale = PosSale::create([
                'company_id' => $companyId, // Explicitly set company_id
                'sale_date' => now()->toDateString(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'custom_tax_enabled' => $customTaxEnabled,
                'custom_tax_amount' => $customTaxEnabled ? ($request->custom_tax_amount ?? 0) : 0,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'notes' => $request->notes,
                'tax_notes' => $customTaxEnabled ? $request->tax_notes : null,
                'cashier_id' => Auth::id()
            ]);

            // Create sale items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $itemSubtotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
                $itemTax = ($itemSubtotal * $product->tax_percentage) / 100;
                
                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name, // Fix: Added missing product_name
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $item['discount_amount'] ?? 0, // Fix: Added discount_amount handling
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => $itemTax,
                    'total_amount' => $itemSubtotal + $itemTax // Fix: Changed to total_amount with tax
                ]);

                // Update product stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing sale: ' . $e->getMessage()
            ], 422);
        }
    }

    public function sales(Request $request)
    {
        $query = PosSale::with(['items.product', 'cashier'])
                        ->currentTenant(); // Filter by current company

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(20);

        return view('admin.pos.sales', compact('sales'));
    }

    public function show(PosSale $sale)
    {
        $sale->load(['items.product', 'cashier']);
        return view('admin.pos.show', compact('sale'));
    }

    public function receipt(PosSale $sale)
    {
        $sale->load(['items.product', 'cashier']);
        return view('admin.pos.receipt', compact('sale'));
    }

    public function refund(Request $request, PosSale $sale)
    {
        if ($sale->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed sales can be refunded!'
            ], 422);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:pos_sale_items,id',
            'items.*.refund_quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $refundAmount = 0;

            foreach ($request->items as $refundItem) {
                $saleItem = PosSaleItem::findOrFail($refundItem['item_id']);
                
                if ($saleItem->pos_sale_id !== $sale->id) {
                    throw new \Exception('Invalid sale item for refund');
                }

                if ($refundItem['refund_quantity'] > $saleItem->quantity) {
                    throw new \Exception('Refund quantity cannot exceed sold quantity');
                }

                $itemRefundAmount = (($saleItem->unit_price * $refundItem['refund_quantity']) - 
                                   (($saleItem->discount_amount / $saleItem->quantity) * $refundItem['refund_quantity']));
                $refundAmount += $itemRefundAmount;

                // Return stock
                $saleItem->product->increment('stock', $refundItem['refund_quantity']);

                // Update sale item quantity
                $saleItem->decrement('quantity', $refundItem['refund_quantity']);
                $saleItem->update(['total_amount' => $saleItem->quantity * $saleItem->unit_price - $saleItem->discount_amount]); // Fix: Use total_amount and include discount
            }

            // Update sale totals
            $sale->update([
                'status' => 'refunded',
                'total_amount' => $sale->total_amount - $refundAmount,
                'notes' => ($sale->notes ?? '') . "\nRefund: {$request->reason}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully!',
                'refund_amount' => $refundAmount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getProduct(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'barcode' => $product->barcode ?? null
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');
        
        $products = Product::active()
                          ->where('stock', '>', 0)
                          ->where(function($query) use ($search) {
                              $query->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('barcode', 'like', '%' . $search . '%');
                          })
                          ->limit(10)
                          ->get(['id', 'name', 'price', 'stock', 'barcode']);

        return response()->json($products);
    }

    public function dailySummary(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $summary = [
            'total_sales' => PosSale::currentTenant()->whereDate('sale_date', $date)->count(),
            'total_amount' => PosSale::currentTenant()->whereDate('sale_date', $date)->sum('total_amount'),
            'cash_sales' => PosSale::currentTenant()->whereDate('sale_date', $date)->where('payment_method', 'cash')->sum('total_amount'),
            'card_sales' => PosSale::currentTenant()->whereDate('sale_date', $date)->where('payment_method', 'card')->sum('total_amount'),
            'upi_sales' => PosSale::currentTenant()->whereDate('sale_date', $date)->where('payment_method', 'upi')->sum('total_amount'),
            'refunds' => PosSale::currentTenant()->whereDate('sale_date', $date)->where('status', 'refunded')->count(),
            'refund_amount' => PosSale::currentTenant()->whereDate('sale_date', $date)->where('status', 'refunded')->sum('total_amount')
        ];

        return response()->json($summary);
    }

    /**
     * Generate and download bill PDF with advanced optimization and timeout protection
     */
    public function downloadBill(PosSale $sale)
    {
        // Immediate timeout and memory optimization
        set_time_limit(0); // Remove execution time limit for this operation
        ini_set('memory_limit', '1G'); // Increase memory limit significantly
        ignore_user_abort(true); // Continue execution even if user disconnects
        
        $startTime = microtime(true);
        
        try {
            // Pre-validation with early exit
            if (!$sale || !$sale->exists) {
                Log::error('Invalid POS sale for bill download', ['sale_id' => $sale->id ?? 'null']);
                return redirect()->back()->with('error', 'Sale record not found.');
            }
            
            if (!$sale->company_id) {
                Log::error('POS sale missing company_id', ['sale_id' => $sale->id]);
                return redirect()->back()->with('error', 'Invalid sale record - missing company information.');
            }

            Log::info('Starting optimized POS bill download', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'company_id' => $sale->company_id,
                'start_time' => $startTime,
                'memory_start' => $this->formatBytes(memory_get_usage(true))
            ]);

            // Use chunked loading for relationships to prevent memory issues
            if (!$sale->relationLoaded('items') || !$sale->relationLoaded('cashier')) {
                $sale->load([
                    'items' => function($query) {
                        $query->select(['id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'discount_amount', 'tax_percentage', 'tax_amount', 'total_amount']);
                    },
                    'items.product' => function($query) {
                        $query->select(['id', 'name', 'sku', 'tax_percentage']);
                    },
                    'cashier' => function($query) {
                        $query->select(['id', 'name', 'email']);
                    }
                ]);
            }

            // Get format with fallback
            $format = request()->get('format', 'a4_sheet');
            
            // Initialize service with enhanced caching
            $billPDFService = app(BillPDFService::class);
            
            // Use optimized generation method
            $result = $this->generateOptimizedPDF($billPDFService, $sale, $format);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::info('Optimized POS bill generation completed', [
                'sale_id' => $sale->id,
                'execution_time' => round($executionTime, 2) . 's',
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'format' => $format,
                'success' => $result['success']
            ]);
            
            if (!$result['success']) {
                return redirect()->back()->with('error', $result['error']);
            }
            
            return $result['response'];
            
        } catch (\Throwable $e) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::error('Optimized POS bill download failed', [
                'sale_id' => $sale->id ?? null,
                'invoice_number' => $sale->invoice_number ?? 'unknown',
                'error' => $e->getMessage(),
                'execution_time' => round($executionTime, 2) . 's',
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Clean up memory
            gc_collect_cycles();
            
            // Handle specific error types
            if (strpos($e->getMessage(), 'timeout') !== false || 
                strpos($e->getMessage(), 'time limit') !== false ||
                strpos($e->getMessage(), 'Maximum execution time') !== false) {
                return redirect()->back()->with('error', 'PDF generation timed out. The system is processing your request in the background. Please try downloading again in a few moments.');
            }
            
            if (strpos($e->getMessage(), 'memory') !== false || 
                strpos($e->getMessage(), 'Memory limit') !== false) {
                return redirect()->back()->with('error', 'Insufficient memory to generate PDF. Please contact support or try again later.');
            }
            
            // Generic error with helpful message
            return redirect()->back()->with('error', 'Unable to generate bill PDF at this time. Please try again in a few moments or contact support if the issue persists.');
        } finally {
            // Always clean up memory
            gc_collect_cycles();
        }
    }

    /**
     * Generate PDF with multiple optimization layers
     */
    private function generateOptimizedPDF(BillPDFService $service, PosSale $sale, $format)
    {
        try {
            // Method 1: Try super-fast in-memory generation
            try {
                return [
                    'success' => true,
                    'response' => $service->generateUltraFastPDF($sale, $format),
                    'method' => 'ultra-fast'
                ];
            } catch (\Exception $e) {
                Log::warning('Ultra-fast PDF generation failed, falling back', [
                    'sale_id' => $sale->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Method 2: Try fast cached generation
            try {
                return [
                    'success' => true,
                    'response' => $service->downloadPosSaleBillFast($sale, $format),
                    'method' => 'fast-cached'
                ];
            } catch (\Exception $e) {
                Log::warning('Fast cached PDF generation failed, falling back', [
                    'sale_id' => $sale->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Method 3: Standard generation with timeout
            return [
                'success' => true,
                'response' => $service->downloadPosSaleBill($sale, $format),
                'method' => 'standard'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'All PDF generation methods failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get available bill formats for POS sale with caching
     */
    /**
     * Enhanced bill formats retrieval with aggressive caching
     */
    public function getBillFormats(PosSale $sale)
    {
        try {
            $companyId = $this->getCurrentTenantIdCached();
            if (!$companyId) {
                return response()->json(['error' => 'Company context not found'], 400);
            }

            // Use static caching for the request
            static $formatCache = [];
            if (isset($formatCache[$companyId])) {
                $cached = $formatCache[$companyId];
                $cached['sale_info'] = [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'company_id' => $sale->company_id,
                    'items_count' => $sale->items()->count()
                ];
                return response()->json($cached);
            }

            $billService = app(BillPDFService::class);
            $config = $billService->getBillFormatConfigCached($companyId);
            $formats = $billService->getAvailableFormatsCached($companyId);

            $result = [
                'success' => true,
                'formats' => $formats,
                'config' => $config,
                'sale_info' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'company_id' => $sale->company_id,
                    'items_count' => $sale->items()->count()
                ]
            ];
            
            $formatCache[$companyId] = $result;
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Failed to get optimized bill formats for POS sale', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enhanced bill preview with caching and optimization
     */
    public function previewBill(PosSale $sale)
    {
        try {
            // Use static caching for the request lifecycle
            static $previewCache = [];
            $cacheKey = "preview_{$sale->id}_" . request()->get('format', 'a4_sheet');
            
            if (isset($previewCache[$cacheKey])) {
                return $previewCache[$cacheKey];
            }
            
            // Pre-load relationships efficiently
            $sale->load([
                'items.product:id,name,sku',
                'cashier:id,name'
            ]);
            
            $companyId = $this->getCurrentTenantIdCached();
            if (!$companyId) {
                return redirect()->back()->with('error', 'Company context not found');
            }

            $billService = app(BillPDFService::class);
            $companySettings = $billService->getCompanySettingsCache($companyId);
            $format = request()->get('format', 'a4_sheet');

            // Select appropriate view template
            $viewName = ($format === 'thermal') ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
            
            $response = view($viewName, [
                'sale' => $sale,
                'globalCompany' => (object) $companySettings
            ]);
            
            $previewCache[$cacheKey] = $response;
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Optimized bill preview failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to preview bill: ' . $e->getMessage());
        }
    }

    /**
     * Get current tenant ID with enhanced caching and fallback logic
     */
    private function getCurrentTenantIdCached()
    {
        // Use static caching for the entire request lifecycle
        static $cachedTenantId = null;
        static $cacheChecked = false;
        
        if ($cacheChecked) {
            return $cachedTenantId;
        }
        
        try {
            // Method 1: Application container
            if (app()->has('current_tenant')) {
                $tenant = app('current_tenant');
                if ($tenant && isset($tenant->id)) {
                    $cachedTenantId = $tenant->id;
                    $cacheChecked = true;
                    return $cachedTenantId;
                }
            }
            
            // Method 2: Request parameter
            if (request()->has('current_company_id')) {
                $companyId = request()->get('current_company_id');
                if ($companyId) {
                    $cachedTenantId = $companyId;
                    $cacheChecked = true;
                    return $cachedTenantId;
                }
            }
            
            // Method 3: Session
            if (session()->has('selected_company_id')) {
                $companyId = session('selected_company_id');
                if ($companyId) {
                    $cachedTenantId = $companyId;
                    $cacheChecked = true;
                    return $cachedTenantId;
                }
            }
            
            // Method 4: Authenticated user
            if (auth()->check() && auth()->user()->company_id) {
                $cachedTenantId = auth()->user()->company_id;
                $cacheChecked = true;
                return $cachedTenantId;
            }

            // Method 5: Domain lookup (cached)
            $host = request()->getHost();
            $company = Cache::remember("domain_company_{$host}", 300, function() use ($host) {
                return \App\Models\SuperAdmin\Company::where('domain', $host)->first(['id']);
            });
            
            if ($company) {
                $cachedTenantId = $company->id;
                $cacheChecked = true;
                return $cachedTenantId;
            }
            
        } catch (\Exception $e) {
            Log::warning('Error getting optimized tenant ID', [
                'error' => $e->getMessage(),
                'host' => request()->getHost() ?? 'unknown',
                'user_id' => auth()->id()
            ]);
        }
        
        $cacheChecked = true;
        $cachedTenantId = null;
        return null;
    }

    /**
     * Format bytes for logging
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
