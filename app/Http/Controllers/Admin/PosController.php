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
            // 'custom_tax_enabled' => 'nullable|boolean',
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
            $customTaxEnabled = $request->boolean('custom_tax_enabled', false);
            
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Calculate item amounts properly
                $itemGrossAmount = $item['quantity'] * $item['unit_price'];
                $itemDiscountAmount = $item['discount_amount'] ?? 0;
                $itemNetAmount = $itemGrossAmount - $itemDiscountAmount;
                
                $subtotal += $itemNetAmount; // Subtotal is net amount after item-level discounts
                
                // Calculate tax for this item only if not using custom tax
                if (!$customTaxEnabled) {
                    $itemTax = ($itemNetAmount * $product->tax_percentage) / 100;
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
                
                // Calculate item amounts
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $item['discount_amount'] ?? 0;
                $discountPercentage = $itemSubtotal > 0 ? round(($discountAmount / $itemSubtotal) * 100, 2) : 0;
                $netAmount = $itemSubtotal - $discountAmount;
                
                // Calculate tax based on net amount (after discount)
                $itemTax = 0;
                if (!$customTaxEnabled) {
                    $itemTax = ($netAmount * $product->tax_percentage) / 100;
                }
                
                $totalAmount = $netAmount + $itemTax;
                
                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => $itemTax,
                    'total_amount' => $totalAmount,
                    'company_id' => $companyId
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
        try {
            // Load sale relationships with all necessary data
            $sale->load([
                'items' => function($query) {
                    $query->select(['id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'discount_amount', 'discount_percentage', 'tax_percentage', 'tax_amount', 'total_amount']);
                },
                'items.product' => function($query) {
                    $query->select(['id', 'name', 'sku', 'tax_percentage']);
                },
                'cashier' => function($query) {
                    $query->select(['id', 'name', 'email']);
                }
            ]);
            
            // Get company data - simplified approach
            $globalCompany = $this->getSimpleCompanyData();
            
            return view('admin.pos.receipt', compact('sale', 'globalCompany'));
            
        } catch (\Exception $e) {
            Log::error('Receipt display failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            // Simple fallback - load basic data and use minimal company info
            $sale->load(['items.product', 'cashier']);
            
            $globalCompany = (object) [
                'company_name' => 'Green Valley Herbs',
                'company_address' => 'Natural & Organic Products Store',
                'company_phone' => '',
                'company_email' => '',
                'gst_number' => '',
                'company_logo' => null
            ];
            
            return view('admin.pos.receipt', compact('sale', 'globalCompany'));
        }
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
     * Generate and download bill PDF - Simplified Working Version
     */
    public function downloadBill(PosSale $sale)
    {
        try {
            Log::info('POS bill download started', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number
            ]);
            
            // Load sale relationships with all necessary data
            $sale->load([
                'items' => function($query) {
                    $query->select(['id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'discount_amount', 'discount_percentage', 'tax_percentage', 'tax_amount', 'total_amount']);
                },
                'items.product' => function($query) {
                    $query->select(['id', 'name', 'sku', 'tax_percentage']);
                },
                'cashier' => function($query) {
                    $query->select(['id', 'name', 'email']);
                }
            ]);
            
            // Get format from request (convert 'a4' to 'a4_sheet' for compatibility)
            $format = request()->get('format', 'a4_sheet');
            if ($format === 'a4') {
                $format = 'a4_sheet';
            }
            
            Log::info('POS bill data loaded', [
                'sale_id' => $sale->id,
                'items_count' => $sale->items->count(),
                'format' => $format
            ]);
            
            // Get company data using the simple method
            $globalCompany = $this->getSimpleCompanyData();
            
            // Try direct PDF generation first (most reliable)
            try {
                Log::info('Attempting direct PDF generation');
                
                // Select view based on format
                $viewName = ($format === 'thermal') ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
                
                // Generate PDF directly using dompdf
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('sale', 'globalCompany'));
                
                // Set paper size based on format
                if ($format === 'thermal') {
                    $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm thermal width
                } else {
                    $pdf->setPaper('A4', 'portrait');
                }
                
                // Set optimized options
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 96,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false,
                    'debugKeepTemp' => false
                ]);
                
                // Generate PDF output
                $pdfOutput = $pdf->output();
                
                // Verify it's a valid PDF
                if (substr($pdfOutput, 0, 4) !== '%PDF') {
                    throw new \Exception('Generated content is not a valid PDF');
                }
                
                // Generate filename
                $filename = 'bill_' . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
                
                Log::info('Direct PDF generation successful', [
                    'sale_id' => $sale->id,
                    'pdf_size' => strlen($pdfOutput),
                    'filename' => $filename
                ]);
                
                // Return PDF with proper headers
                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($pdfOutput),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
                
            } catch (\Exception $directError) {
                Log::warning('Direct PDF generation failed, trying BillPDFService', [
                    'sale_id' => $sale->id,
                    'error' => $directError->getMessage()
                ]);
                
                // Fallback to BillPDFService methods
                try {
                    $billService = app(BillPDFService::class);
                    
                    // Try ultra-fast generation
                    try {
                        Log::info('Trying BillPDFService ultra-fast generation');
                        return $billService->generateUltraFastPDF($sale, $format);
                    } catch (\Exception $e) {
                        Log::warning('Ultra-fast PDF failed, trying fast method', ['error' => $e->getMessage()]);
                        
                        try {
                            Log::info('Trying BillPDFService fast generation');
                            return $billService->downloadPosSaleBillFast($sale, $format);
                        } catch (\Exception $e2) {
                            Log::warning('Fast PDF failed, trying standard method', ['error' => $e2->getMessage()]);
                            
                            Log::info('Trying BillPDFService standard generation');
                            return $billService->downloadPosSaleBill($sale, $format);
                        }
                    }
                } catch (\Exception $serviceError) {
                    Log::error('All BillPDFService methods failed', [
                        'sale_id' => $sale->id,
                        'error' => $serviceError->getMessage()
                    ]);
                    throw $serviceError;
                }
            }
            
        } catch (\Throwable $e) {
            Log::error('POS bill download failed completely', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Final fallback: redirect to web receipt with error message
            return redirect()->route('admin.pos.receipt', $sale->id)
                           ->with('error', 'PDF download failed: ' . $e->getMessage() . '. Showing web receipt instead.');
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
     * Get simple company data with minimal dependencies
     */
    private function getSimpleCompanyData()
    {
        try {
            // Try to get company from session first
            $companyId = session('selected_company_id', 1);
            
            // Basic company data without complex dependencies
            $company = null;
            if (class_exists('\App\Models\SuperAdmin\Company')) {
                $company = \App\Models\SuperAdmin\Company::find($companyId);
            }
            
            if ($company) {
                return (object) [
                    'company_name' => $company->name ?? 'Green Valley Herbs',
                    'company_address' => $company->address ?? 'Natural & Organic Products Store',
                    'company_phone' => $company->phone ?? '',
                    'company_email' => $company->email ?? '',
                    'gst_number' => $company->gst_number ?? '',
                    'company_logo' => $company->logo ?? null
                ];
            }
            
        } catch (\Exception $e) {
            Log::warning('Simple company data fetch failed', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Final fallback with default data
        return (object) [
            'company_name' => 'Green Valley Herbs',
            'company_address' => 'Natural & Organic Products Store',
            'company_phone' => '',
            'company_email' => '',
            'gst_number' => '',
            'company_logo' => null
        ];
    }

    /**
     * Get company data for receipt/bill display (legacy method)
     */
    private function getCompanyData($companyId)
    {
        try {
            // Check if we have a BillPDFService available
            if (class_exists('\App\Services\BillPDFService')) {
                $billService = app(BillPDFService::class);
                $companySettings = $billService->getCompanySettingsCache($companyId);
                return (object) $companySettings;
            }
            
            // Fallback: Get company data directly
            $company = Cache::remember("company_data_{$companyId}", 300, function() use ($companyId) {
                return \App\Models\SuperAdmin\Company::find($companyId);
            });
            
            if ($company) {
                return (object) [
                    'company_name' => $company->name ?? 'Store',
                    'company_address' => $company->address ?? '',
                    'company_phone' => $company->phone ?? '',
                    'company_email' => $company->email ?? '',
                    'gst_number' => $company->gst_number ?? '',
                    'company_logo' => $company->logo ?? null
                ];
            }
            
        } catch (\Exception $e) {
            Log::warning('Failed to get company data', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
        }
        
        // Final fallback
        return (object) [
            'company_name' => 'Green Valley Herbs',
            'company_address' => 'Natural & Organic Products Store',
            'company_phone' => '',
            'company_email' => '',
            'gst_number' => '',
            'company_logo' => null
        ];
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

    /**
     * Debug version of downloadBill method for testing
     * Temporary method to help diagnose PDF issues
     */
    public function downloadBillDebug(PosSale $sale)
    {
        try {
            Log::info('Debug: downloadBillDebug started', ['sale_id' => $sale->id]);
            
            // Load sale relationships
            $sale->load(['items.product', 'cashier']);
            
            // Get simple company data
            $globalCompany = (object) [
                'company_name' => 'Green Valley Herbs',
                'company_address' => 'Natural & Organic Products Store',
                'company_phone' => '',
                'company_email' => '',
                'gst_number' => '',
                'company_logo' => null
            ];
            
            Log::info('Debug: Data prepared', [
                'sale_id' => $sale->id,
                'items_count' => $sale->items->count(),
                'company_name' => $globalCompany->company_name
            ]);
            
            // Test view rendering first
            try {
                $html = view('admin.pos.receipt-a4', compact('sale', 'globalCompany'))->render();
                Log::info('Debug: View rendered successfully', ['html_length' => strlen($html)]);
            } catch (\Exception $e) {
                Log::error('Debug: View rendering failed', ['error' => $e->getMessage()]);
                return response('View rendering failed: ' . $e->getMessage(), 500);
            }
            
            // Test PDF generation
            try {
                Log::info('Debug: Starting PDF generation');
                
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
                $pdf->setPaper('A4', 'portrait');
                
                // Set PDF options for better compatibility
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 96,
                    'debugKeepTemp' => false,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false
                ]);
                
                Log::info('Debug: PDF object created, generating output');
                
                $pdfOutput = $pdf->output();
                
                Log::info('Debug: PDF output generated', [
                    'size' => strlen($pdfOutput),
                    'is_pdf' => substr($pdfOutput, 0, 4) === '%PDF'
                ]);
                
                if (substr($pdfOutput, 0, 4) !== '%PDF') {
                    Log::error('Debug: Generated content is not a valid PDF', [
                        'first_100_chars' => substr($pdfOutput, 0, 100)
                    ]);
                    return response('Invalid PDF generated', 500);
                }
                
                // Generate filename
                $filename = 'debug_bill_' . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
                
                Log::info('Debug: Returning PDF response', ['filename' => $filename]);
                
                // Return PDF with explicit headers
                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($pdfOutput),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-PDF-Debug' => 'true'
                ]);
                
            } catch (\Exception $e) {
                Log::error('Debug: PDF generation failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                return response('PDF generation failed: ' . $e->getMessage(), 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Debug: downloadBillDebug failed completely', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response('Complete failure: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test method to return just the HTML view
     * Temporary method for debugging
     */
    public function viewBillDebug(PosSale $sale)
    {
        try {
            // Load sale relationships
            $sale->load(['items.product', 'cashier']);
            
            // Get simple company data
            $globalCompany = (object) [
                'company_name' => 'Green Valley Herbs',
                'company_address' => 'Natural & Organic Products Store',
                'company_phone' => '',
                'company_email' => '',
                'gst_number' => '',
                'company_logo' => null
            ];
            
            // Return just the HTML view for testing
            return view('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
            
        } catch (\Exception $e) {
            return response('View debug failed: ' . $e->getMessage(), 500);
        }
    }
}
