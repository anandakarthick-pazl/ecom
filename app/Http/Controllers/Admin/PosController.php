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
            
            // Get enhanced company data
            $globalCompany = $this->getCompanyData($sale->company_id);
            
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
     * FIXED: Simple and reliable PDF download method
     */
    public function downloadBill(PosSale $sale)
    {
        try {
            Log::info('PDF download started', ['sale_id' => $sale->id]);
            
            // Load sale data
            $sale->load(['items.product', 'cashier']);
            
            // Get company data
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            // Get format (default to A4)
            $format = request()->get('format', 'a4_sheet');
            
            // Select template - try clean version first, then fixed version
            $viewName = 'admin.pos.receipt-a4';
            if ($format === 'thermal') {
                $viewName = 'admin.pos.receipt-pdf';
            }
            
            // Try clean template first (best option)
            if ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-clean')) {
                $viewName = 'admin.pos.receipt-a4-clean';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-pdf-clean')) {
                $viewName = 'admin.pos.receipt-pdf-clean';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-pdf-fixed')) {
                $viewName = 'admin.pos.receipt-pdf-fixed';
            } elseif ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-fixed')) {
                $viewName = 'admin.pos.receipt-a4-fixed';
            }   
            

            
            // echo "Using view: $viewName\n"; exit;
            
            Log::info('Using view template: ' . $viewName);
           
            // echo "<pre>"; print_r($globalCompany); exit;
            
            // Create PDF with basic settings
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);
            
            // Set paper size
            if ($format === 'thermal') {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm thermal
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Basic PDF options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'debugKeepTemp' => false
            ]);
            
            // Generate filename
            $filename = 'bill_' . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            Log::info('PDF generated, returning download', [
                'filename' => $filename,
                'format' => $format,
                'view' => $viewName
            ]);
            
            // FIXED: Use streamDownload for proper PDF download
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF download failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            // Return error response instead of redirect
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SIMPLE PDF DOWNLOAD FIX - Alternative method
     */
    public function downloadBillSimple(PosSale $sale)
    {
        try {
            Log::info('Simple PDF download started', ['sale_id' => $sale->id]);
            
            // Load sale data
            $sale->load(['items.product', 'cashier']);
            
            // Get simple company data
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            // Get format (default to A4)
            $format = request()->get('format', 'a4_sheet');
            
            // Select template - try clean version first
            $viewName = 'admin.pos.receipt-a4';
            if ($format === 'thermal') {
                $viewName = 'admin.pos.receipt-pdf';
            }
            
            // Try clean template first (best option)
            if ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-clean')) {
                $viewName = 'admin.pos.receipt-a4-clean';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-pdf-clean')) {
                $viewName = 'admin.pos.receipt-pdf-clean';
            }
            
            Log::info('Generating PDF with view: ' . $viewName);
            
            // Create PDF with basic settings
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);
            
            // Set paper size
            if ($format === 'thermal') {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm thermal
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Basic PDF options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'debugKeepTemp' => false
            ]);
            
            // Generate filename
            $filename = 'bill_' . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            Log::info('PDF generated successfully', [
                'filename' => $filename,
                'format' => $format
            ]);
            
            // Return PDF download response - THIS IS THE KEY FIX
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Simple PDF download failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            // Return error response
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company logo for PDF generation with proper encoding
     */
    private function getCompanyLogoForPDF($globalCompany)
    {
        if (empty($globalCompany->company_logo)) {
            return null;
        }
        
        // Try different possible paths for the logo
        $possiblePaths = [
            storage_path('app/public/' . $globalCompany->company_logo),
            public_path('storage/' . $globalCompany->company_logo),
            storage_path('app/' . $globalCompany->company_logo),
            public_path($globalCompany->company_logo),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                try {
                    // Get the file contents and encode as base64
                    $imageData = file_get_contents($path);
                    $mimeType = mime_content_type($path);
                    
                    // Create data URI for PDF
                    $base64 = base64_encode($imageData);
                    $dataUri = 'data:' . $mimeType . ';base64,' . $base64;
                    
                    Log::info('Logo found and encoded for PDF', [
                        'path' => $path,
                        'size' => strlen($imageData),
                        'mime_type' => $mimeType
                    ]);
                    
                    return $dataUri;
                } catch (\Exception $e) {
                    Log::error('Error processing logo', [
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }
        
        Log::warning('Logo not found in any expected location', [
            'logo_path' => $globalCompany->company_logo,
            'searched_paths' => $possiblePaths
        ]);
        
        return null;
    }

    /**
     * Get company logo - uses base64 for PDF, asset URL for web
     */
    private function getLogoForDisplay($globalCompany, $isPDF = true)
    {
        if (empty($globalCompany->company_logo)) {
            return null;
        }
        
        if ($isPDF) {
            // For PDF generation, use base64 encoding
            return $this->getCompanyLogoForPDF($globalCompany);
        } else {
            // For web display, use asset URL
            return asset('storage/' . $globalCompany->company_logo);
        }
    }

    /**
     * Get simple company data without complex caching
     */
    private function getSimpleCompanyData($companyId = null)
    {
        try {
            if (!$companyId) {
                $companyId = $this->getCurrentTenantId();
            }
            
            if (!$companyId) {
                return $this->getFallbackCompanyData();
            }
            
            // Get company directly from database
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            
            if (!$company) {
                return $this->getFallbackCompanyData();
            }
            
            return (object) [
                'company_name' => $company->name ?? 'Green Valley Herbs',
                'company_address' => $company->address ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'country' => $company->country ?? '',
                'postal_code' => $company->postal_code ?? '',
                'company_phone' => $company->phone ?? '',
                'company_email' => $company->email ?? '',
                'gst_number' => $company->gst_number ?? '',
                'company_logo' => $company->logo ?? '',
                'company_logo_pdf' => $this->getCompanyLogoForPDF((object)['company_logo' => $company->logo ?? '']),
                'full_address' => $this->formatFullAddress($company),
                'contact_info' => $this->formatContactInfo($company),
                'display_name' => $company->name ?? 'Your Store'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting simple company data', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackCompanyData();
        }
    }

    /**
     * Get current tenant ID with simple logic
     */
    private function getCurrentTenantId()
    {
        try {
            // Try session first
            if (session()->has('selected_company_id')) {
                return session('selected_company_id');
            }
            
            // Try authenticated user
            if (auth()->check() && auth()->user()->company_id) {
                return auth()->user()->company_id;
            }
            
            // Try domain lookup
            $host = request()->getHost();
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            
            if ($company) {
                return $company->id;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error getting tenant ID', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Test PDF generation without download
     */
    public function testPdfGeneration(PosSale $sale)
    {
        try {
            $sale->load(['items.product', 'cashier']);
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            // Test view rendering
            $html = view('admin.pos.receipt-a4', [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ])->render();
            
            // Test PDF creation
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-a4', [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96
            ]);
            
            $pdfOutput = $pdf->output();
            
            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'company_data' => $globalCompany,
                'html_length' => strlen($html),
                'pdf_size' => strlen($pdfOutput),
                'is_valid_pdf' => substr($pdfOutput, 0, 4) === '%PDF',
                'pdf_header' => substr($pdfOutput, 0, 20)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get company data for receipt/bill display (legacy method)
     */
    private function getCompanyData($companyId = null)
    {
        return $this->getSimpleCompanyData($companyId);
    }
    
    /**
     * Format full address from company data
     */
    private function formatFullAddress($company)
    {
        $addressParts = array_filter([
            $company->address,
            $company->city,
            $company->state,
            $company->postal_code,
            $company->country
        ]);
        
        return implode(', ', $addressParts);
    }
    
    /**
     * Format contact information
     */
    private function formatContactInfo($company)
    {
        $contactParts = [];
        
        if ($company->phone) {
            $contactParts[] = "Phone: {$company->phone}";
        }
        
        if ($company->email) {
            $contactParts[] = "Email: {$company->email}";
        }
        
        return implode(' | ', $contactParts);
    }
    
    /**
     * Fallback company data when actual data is not available
     */
    private function getFallbackCompanyData()
    {
        return (object) [
            'company_name' => 'Green Valley Herbs',
            'company_address' => 'Natural & Organic Products Store',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => '',
            'company_phone' => '',
            'company_email' => '',
            'gst_number' => '',
            'company_logo' => '',
            'company_logo_pdf' => null,
            'full_address' => 'Natural & Organic Products Store',
            'contact_info' => '',
            'display_name' => 'Green Valley Herbs'
        ];
    }

    /**
     * Enhanced bill formats retrieval with aggressive caching
     */
    public function getBillFormats(PosSale $sale)
    {
        try {
            $companyId = $this->getCurrentTenantId();
            if (!$companyId) {
                return response()->json(['error' => 'Company context not found'], 400);
            }

            // Simple formats for now
            $formats = [
                'a4_sheet' => 'A4 Sheet PDF',
                'thermal' => 'Thermal Printer (80mm)'
            ];

            $result = [
                'success' => true,
                'formats' => $formats,
                'sale_info' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'company_id' => $sale->company_id,
                    'items_count' => $sale->items()->count()
                ]
            ];
            
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Failed to get bill formats for POS sale', [
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
     * Bill preview method
     */
    public function previewBill(PosSale $sale)
    {
        try {
            // Pre-load relationships efficiently
            $sale->load([
                'items.product:id,name,sku',
                'cashier:id,name'
            ]);
            
            $companyId = $this->getCurrentTenantId();
            if (!$companyId) {
                return redirect()->back()->with('error', 'Company context not found');
            }

            $format = request()->get('format', 'a4_sheet');

            // Use simple company data
            $globalCompany = $this->getSimpleCompanyData($companyId);

            // Select appropriate view template (try clean version first)
            $viewName = ($format === 'thermal') ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
            
            // Try clean template first (best option)
            if ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-clean')) {
                $viewName = 'admin.pos.receipt-a4-clean';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-pdf-clean')) {
                $viewName = 'admin.pos.receipt-pdf-clean';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-pdf-fixed')) {
                $viewName = 'admin.pos.receipt-pdf-fixed';
            } elseif ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-fixed')) {
                $viewName = 'admin.pos.receipt-a4-fixed';
            }
            
            return view($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bill preview failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to preview bill: ' . $e->getMessage());
        }
    }

    /**
     * Test enhanced company data retrieval (for debugging)
     */
    public function testEnhancedCompanyData(PosSale $sale)
    {
        try {
            $companyData = $this->getSimpleCompanyData($sale->company_id);
            
            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'sale_company_id' => $sale->company_id,
                'company_data' => $companyData,
                'current_tenant_id' => $this->getCurrentTenantId()
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'sale_id' => $sale->id
            ], 500);
        }
    }

    /**
     * Clear company data cache
     */
    public function clearCompanyCache($companyId = null)
    {
        try {
            if (!$companyId) {
                $companyId = $this->getCurrentTenantId();
            }
            
            // Clear Laravel cache
            Cache::flush();
            
            Log::info('Company cache cleared', ['company_id' => $companyId]);
            
            return response()->json(['success' => true, 'message' => 'Company cache cleared']);
        } catch (\Exception $e) {
            Log::error('Failed to clear company cache', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Enhanced debug version of downloadBill method for testing
     */
    public function downloadBillDebug(PosSale $sale)
    {
        try {
            Log::info('Debug: downloadBillDebug started', ['sale_id' => $sale->id]);
            
            // Load sale relationships
            $sale->load(['items.product', 'cashier']);
            
            // Get company data
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            Log::info('Debug: Data prepared', [
                'sale_id' => $sale->id,
                'items_count' => $sale->items->count(),
                'company_name' => $globalCompany->company_name
            ]);
            
            // Test view rendering first
            try {
                $viewName = 'admin.pos.receipt-a4';
                
                // Try clean version first (best option)
                if (view()->exists('admin.pos.receipt-a4-clean')) {
                    $viewName = 'admin.pos.receipt-a4-clean';
                } elseif (view()->exists('admin.pos.receipt-a4-fixed')) {
                    $viewName = 'admin.pos.receipt-a4-fixed';
                }
                
                $html = view($viewName, compact('sale', 'globalCompany'))->render();
                Log::info('Debug: View rendered successfully', [
                    'html_length' => strlen($html),
                    'view_used' => $viewName
                ]);
            } catch (\Exception $e) {
                Log::error('Debug: View rendering failed', ['error' => $e->getMessage()]);
                return response('View rendering failed: ' . $e->getMessage(), 500);
            }
            
            // Test PDF generation
            try {
                Log::info('Debug: Starting PDF generation');
                
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('sale', 'globalCompany'));
                $pdf->setPaper('A4', 'portrait');
                
                // Basic PDF options for debug
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 96,
                    'debugKeepTemp' => false
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
                
                // Return PDF with streamDownload
                return response()->streamDownload(function() use ($pdfOutput) {
                    echo $pdfOutput;
                }, $filename, [
                    'Content-Type' => 'application/pdf'
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
     * Download multiple receipts in compact format (up to 20 per page)
     */
    public function downloadMultipleReceipts(Request $request)
    {
        try {
            $request->validate([
                'sale_ids' => 'required|array|min:1|max:20',
                'sale_ids.*' => 'required|integer|exists:pos_sales,id',
                'format' => 'nullable|in:compact,list'
            ]);

            Log::info('Multiple receipts download started', [
                'sale_ids' => $request->sale_ids,
                'count' => count($request->sale_ids)
            ]);
            
            // Load sales with relationships
            $sales = PosSale::with(['items.product', 'cashier'])
                           ->whereIn('id', $request->sale_ids)
                           ->currentTenant()
                           ->get();

            if ($sales->isEmpty()) {
                return response()->json(['error' => 'No sales found'], 404);
            }

            // Get company data
            $globalCompany = $this->getSimpleCompanyData($sales->first()->company_id);
            
            // Get format
            $format = $request->get('format', 'compact');
            
            // Select template
            $viewName = 'admin.pos.receipt-multi-compact';
            
            Log::info('Generating multi-receipt PDF', [
                'sales_count' => $sales->count(),
                'format' => $format,
                'view' => $viewName
            ]);
            
            // Create PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'sales' => $sales,
                'globalCompany' => $globalCompany
            ]);
            
            // Set paper size
            $pdf->setPaper('A4', 'portrait');
            
            // Basic PDF options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'debugKeepTemp' => false
            ]);
            
            // Generate filename
            $filename = 'receipts_' . count($request->sale_ids) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            Log::info('Multi-receipt PDF generated', [
                'filename' => $filename,
                'sales_count' => $sales->count()
            ]);
            
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Multi-receipt download failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'error' => 'Multi-receipt generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales for multi-receipt download
     */
    public function getMultiReceiptSales(Request $request)
    {
        try {
            $query = PosSale::with(['items.product', 'cashier'])
                           ->currentTenant()
                           ->latest();

            // Apply filters
            if ($request->date_from) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }

            $sales = $query->limit(100)->get();

            return response()->json([
                'success' => true,
                'sales' => $sales->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'invoice_number' => $sale->invoice_number,
                        'customer_name' => $sale->customer_name ?? 'Walk-in',
                        'total_amount' => $sale->total_amount,
                        'payment_method' => $sale->payment_method,
                        'status' => $sale->status,
                        'created_at' => $sale->created_at->format('d/m/Y H:i'),
                        'items_count' => $sale->items->count(),
                        'items_quantity' => $sale->items->sum('quantity')
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get multi-receipt sales', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug company logo paths and data for troubleshooting
     */
    public function debugLogoIssue(PosSale $sale)
    {
        try {
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            $debug = [
                'company_data' => [
                    'company_id' => $sale->company_id,
                    'company_name' => $globalCompany->company_name,
                    'company_logo' => $globalCompany->company_logo,
                    'logo_empty' => empty($globalCompany->company_logo),
                ],
                'urls_and_paths' => [],
                'file_checks' => [],
                'storage_status' => [
                    'symlink_exists' => is_link(public_path('storage')),
                    'symlink_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : null,
                    'public_storage_dir_exists' => is_dir(public_path('storage')),
                    'app_storage_dir_exists' => is_dir(storage_path('app/public')),
                ],
                'recommendations' => []
            ];
            
            if (!empty($globalCompany->company_logo)) {
                // Test different URL and path combinations
                $logo = $globalCompany->company_logo;
                
                $debug['urls_and_paths'] = [
                    'asset_url' => asset('storage/' . $logo),
                    'public_path' => public_path('storage/' . $logo),
                    'storage_path' => storage_path('app/public/' . $logo),
                    'direct_public' => public_path($logo),
                ];
                
                // Check if files actually exist
                foreach ($debug['urls_and_paths'] as $type => $path) {
                    if (str_contains($type, 'path')) {
                        $debug['file_checks'][$type] = [
                            'path' => $path,
                            'exists' => file_exists($path),
                            'readable' => file_exists($path) && is_readable($path),
                            'size' => file_exists($path) ? filesize($path) : 0,
                            'mime_type' => file_exists($path) ? mime_content_type($path) : null
                        ];
                    }
                }
                
                // Add recommendations based on findings
                $anyFileExists = collect($debug['file_checks'])->contains('exists', true);
                
                if (!$anyFileExists) {
                    $debug['recommendations'][] = 'Logo file not found in any expected location';
                    $debug['recommendations'][] = 'Upload logo to storage/app/public/ directory';
                }
                
                if (!$debug['storage_status']['symlink_exists']) {
                    $debug['recommendations'][] = 'Run: php artisan storage:link';
                }
                
                if (!$debug['storage_status']['public_storage_dir_exists'] && !$debug['storage_status']['symlink_exists']) {
                    $debug['recommendations'][] = 'Storage symlink is missing and public/storage directory does not exist';
                }
                
            } else {
                $debug['recommendations'][] = 'No logo configured in company settings';
                $debug['recommendations'][] = 'Go to /admin/settings to upload a company logo';
            }
            
            return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    

    /**
     * Bulk download receipts by date range
     */
    public function downloadReceiptsByDateRange(Request $request)
    {
        try {
            $request->validate([
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            $limit = $request->get('limit', 20);
            
            $sales = PosSale::with(['items.product', 'cashier'])
                           ->currentTenant()
                           ->whereDate('sale_date', '>=', $request->date_from)
                           ->whereDate('sale_date', '<=', $request->date_to)
                           ->where('status', 'completed')
                           ->latest()
                           ->limit($limit)
                           ->get();

            if ($sales->isEmpty()) {
                return response()->json(['error' => 'No sales found for the selected date range'], 404);
            }

            // Get company data
            $globalCompany = $this->getSimpleCompanyData($sales->first()->company_id);
            
            Log::info('Bulk receipt download by date range', [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'sales_count' => $sales->count()
            ]);
            
            // Create PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-multi-compact', [
                'sales' => $sales,
                'globalCompany' => $globalCompany
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96
            ]);
            
            $filename = 'receipts_' . $request->date_from . '_to_' . $request->date_to . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk receipt download failed', [
                'error' => $e->getMessage(),
                'date_from' => $request->date_from ?? null,
                'date_to' => $request->date_to ?? null
            ]);
            
            return response()->json([
                'error' => 'Bulk receipt generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test logo display in HTML format (for debugging)
     */
    public function testLogoDisplay(PosSale $sale)
    {
        try {
            $globalCompany = $this->getSimpleCompanyData($sale->company_id);
            
            $html = '<!DOCTYPE html><html><head><title>Logo Test</title>';
            $html .= '<style>body{font-family:Arial,sans-serif;margin:40px;} .logo{max-height:100px;border:1px solid #ddd;margin:10px 0;} .info{background:#f5f5f5;padding:15px;margin:10px 0;border-radius:5px;} .success{background:#d4edda;color:#155724;} .error{background:#f8d7da;color:#721c24;}</style>';
            $html .= '</head><body>';
            $html .= '<h1>🔍 Logo Display Test for Sale #' . $sale->id . '</h1>';
            
            $html .= '<div class="info">';
            $html .= '<h3>Company Information</h3>';
            $html .= '<p><strong>Company:</strong> ' . ($globalCompany->company_name ?? 'N/A') . '</p>';
            $html .= '<p><strong>Logo Path:</strong> ' . ($globalCompany->company_logo ?? 'N/A') . '</p>';
            $html .= '<p><strong>PDF Logo Generated:</strong> ' . ($globalCompany->company_logo_pdf ? 'Yes (' . strlen($globalCompany->company_logo_pdf) . ' bytes)' : 'No') . '</p>';
            $html .= '</div>';
            
            if ($globalCompany->company_logo_pdf) {
                $html .= '<div class="success">';
                $html .= '<h3>✅ PDF Logo (Base64 Encoded)</h3>';
                $html .= '<p>This is how the logo will appear in PDFs:</p>';
                $html .= '<img src="' . $globalCompany->company_logo_pdf . '" alt="PDF Logo" class="logo">';
                $html .= '<p><strong>Data URI Size:</strong> ' . strlen($globalCompany->company_logo_pdf) . ' characters</p>';
                $html .= '<p><strong>Preview:</strong> ' . substr($globalCompany->company_logo_pdf, 0, 100) . '...</p>';
                $html .= '</div>';
            } else {
                $html .= '<div class="error">';
                $html .= '<h3>❌ PDF Logo Not Generated</h3>';
                $html .= '<p>Logo file not found or unreadable for PDF generation.</p>';
                $html .= '</div>';
            }
            
            if ($globalCompany->company_logo) {
                $html .= '<div class="info">';
                $html .= '<h3>📱 Web Logo (Asset URL)</h3>';
                $html .= '<p>This is how the logo appears on web pages:</p>';
                $html .= '<img src="' . asset('storage/' . $globalCompany->company_logo) . '" alt="Web Logo" class="logo" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">';
                $html .= '<p style="display:none;color:red;">❌ Web logo failed to load</p>';
                $html .= '<p><strong>Asset URL:</strong> ' . asset('storage/' . $globalCompany->company_logo) . '</p>';
                $html .= '</div>';
            }
            
            $html .= '<div class="info">';
            $html .= '<h3>🔗 Test Links</h3>';
            $html .= '<p><a href="/admin/pos/sales/' . $sale->id . '/download-bill" target="_blank">Download PDF Receipt</a></p>';
            $html .= '<p><a href="/admin/pos/sales/' . $sale->id . '/debug-logo" target="_blank">Debug Logo JSON</a></p>';
            $html .= '<p><a href="/logo-debug.html" target="_blank">Logo Debug Tool</a></p>';
            $html .= '</div>';
            
            $html .= '</body></html>';
            
            return response($html);
            
        } catch (\Exception $e) {
            return response('Logo test failed: ' . $e->getMessage(), 500);
        }
    }
}