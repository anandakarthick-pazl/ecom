<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\AppSetting;
use App\Models\Commission;
use App\Models\Offer;
use App\Services\BillPDFService;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PosController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }
    public function index(Request $request)
    {
        // Base query for products
        $query = Product::active()
            ->currentTenant()
            ->with(['category', 'offers' => function($query) {
                $query->active()->current();
            }])
            ->orderBy('name');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Category filter - Fixed to use category ID
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Paginate products - 25 per page
        $products = $query->paginate(25)->appends($request->query());

        // Apply offers to paginated products
        $productsWithOffers = $this->offerService->applyOffersToProducts($products->getCollection());
        $products->setCollection($productsWithOffers);
        
        // Get all categories for filter - Fixed to get from Category model
        $categories = \App\Models\Category::active()
            ->currentTenant()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $customers = Customer::currentTenant()->orderBy('name')->get();

        // Get active offers for display
        $activeOffers = Offer::active()->current()->currentTenant()->get();

        return view('admin.pos.index', compact('products', 'categories', 'customers', 'activeOffers'));
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
            'notes' => 'nullable|string',
            // Commission fields
            'commission_enabled' => 'sometimes|in:0,1,true,false',
            'reference_name' => 'nullable|string|max:255',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals and tax
            $subtotal = 0;
            $totalTax = 0;
            $totalOfferSavings = 0;
            $customTaxEnabled = $request->boolean('custom_tax_enabled', false);

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Get effective price with offers
                $effectivePrice = $this->offerService->getEffectivePrice($product);
                $offerSavings = ($product->price - $effectivePrice) * $item['quantity'];
                $totalOfferSavings += $offerSavings;

                // Calculate item amounts using effective price
                $itemGrossAmount = $effectivePrice * $item['quantity'];
                $itemDiscountAmount = $item['discount_amount'] ?? 0;
                $itemNetAmount = $itemGrossAmount - $itemDiscountAmount;

                $subtotal += $itemNetAmount;

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

            // Create POS sale with offer savings
            $sale = PosSale::create([
                'company_id' => $companyId,
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
                'notes' => $request->notes . ($totalOfferSavings > 0 ? "\nOffer Savings: ₹" . number_format($totalOfferSavings, 2) : ''),
                'tax_notes' => $customTaxEnabled ? $request->tax_notes : null,
                'cashier_id' => Auth::id()
            ]);

            // Create sale items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Get effective price with offers
                $effectivePrice = $this->offerService->getEffectivePrice($product);
                $offerDetails = $this->offerService->getOfferDetails($product);

                // Calculate item amounts using effective price
                $itemSubtotal = $effectivePrice * $item['quantity'];
                $discountAmount = $item['discount_amount'] ?? 0;
                $discountPercentage = $itemSubtotal > 0 ? round(($discountAmount / $itemSubtotal) * 100, 2) : 0;
                $netAmount = $itemSubtotal - $discountAmount;

                // Calculate tax based on net amount (after discount)
                $itemTax = 0;
                if (!$customTaxEnabled) {
                    $itemTax = ($netAmount * $product->tax_percentage) / 100;
                }

                $totalAmount = $netAmount + $itemTax;

                // Store original price and offer information
                $notes = '';
                if ($offerDetails && $effectivePrice < $product->price) {
                    $offerSavings = ($product->price - $effectivePrice) * $item['quantity'];
                    $notes = "Offer Applied: {$offerDetails['offer']->name} - Saved ₹" . number_format($offerSavings, 2);
                }

                PosSaleItem::create([
                    'pos_sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $effectivePrice, // Store effective price
                    'original_price' => $product->price, // Store original price for reference
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'tax_percentage' => $product->tax_percentage,
                    'tax_amount' => $itemTax,
                    'total_amount' => $totalAmount,
                    'offer_applied' => $offerDetails ? $offerDetails['offer']->name : null,
                    'offer_savings' => $offerDetails ? ($product->price - $effectivePrice) * $item['quantity'] : 0,
                    'notes' => $notes,
                    'company_id' => $companyId
                ]);

                // Update product stock
                $product->decrement('stock', $item['quantity']);

                // Update offer usage count if applicable
                if ($offerDetails && $offerDetails['offer']->usage_limit) {
                    $offerDetails['offer']->increment('used_count');
                }
            }

            // Create commission record if enabled
            $commissionEnabled = in_array($request->get('commission_enabled'), ['1', 1, true, 'true', 'on', 'yes'], true);
            
            if ($commissionEnabled && 
                !empty($request->reference_name) && 
                !empty($request->commission_percentage)) {
                
                Commission::createFromPosSale(
                    $sale,
                    $request->reference_name,
                    $request->commission_percentage,
                    $request->commission_notes
                );

                Log::info('Commission created for POS sale', [
                    'sale_id' => $sale->id,
                    'reference_name' => $request->reference_name,
                    'commission_percentage' => $request->commission_percentage,
                    'commission_amount' => ($sale->total_amount * $request->commission_percentage) / 100
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total_savings' => $totalOfferSavings,
                'commission_created' => $commissionEnabled && 
                    !empty($request->reference_name) && 
                    !empty($request->commission_percentage)
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
        $query = PosSale::with(['items.product', 'cashier', 'commission'])
            ->currentTenant(); // Filter by current company

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
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

        // Add commission filter
        if ($request->commission_status) {
            switch ($request->commission_status) {
                case 'with_commission':
                    $query->whereHas('commission');
                    break;
                case 'without_commission':
                    $query->whereDoesntHave('commission');
                    break;
                case 'pending':
                    $query->whereHas('commission', function($q) {
                        $q->where('status', 'pending');
                    });
                    break;
                case 'paid':
                    $query->whereHas('commission', function($q) {
                        $q->where('status', 'paid');
                    });
                    break;
            }
        }

        $sales = $query->latest()->paginate(20);
        
        // Get the default bill format setting for the current company
        $companyId = session('selected_company_id');
        $defaultBillFormat = AppSetting::getForTenant('default_bill_format', $companyId) ?? 'a4_sheet';
        
        return view('admin.pos.sales', compact('sales', 'defaultBillFormat'));
    }

    public function show(PosSale $sale)
    {
        $sale->load(['items.product', 'cashier', 'commission']);
        return view('admin.pos.show', compact('sale'));
    }

    public function receipt(PosSale $sale)
    {
        try {
            // Load sale relationships with all necessary data including original_price
            $sale->load([
                'items' => function ($query) {
                    $query->select([
                        'id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 
                        'unit_price', 'original_price', 'discount_amount', 'discount_percentage', 
                        'tax_percentage', 'tax_amount', 'total_amount', 'offer_applied', 
                        'offer_savings', 'notes', 'company_id'
                    ]);
                },
                'items.product' => function ($query) {
                    $query->select(['id', 'name', 'sku', 'tax_percentage', 'price']);
                },
                'cashier' => function ($query) {
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

    public function enhancedReceipt(PosSale $sale)
    {
        try {
            // Load sale relationships with enhanced data including original prices and offers
            $sale->load([
                'items' => function ($query) {
                    $query->select([
                        'id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 
                        'unit_price', 'original_price', 'discount_amount', 'discount_percentage', 
                        'tax_percentage', 'tax_amount', 'total_amount', 'offer_applied', 
                        'offer_savings', 'notes'
                    ]);
                },
                'items.product' => function ($query) {
                    $query->select(['id', 'name', 'sku', 'tax_percentage', 'price']);
                },
                'cashier' => function ($query) {
                    $query->select(['id', 'name', 'email']);
                }
            ]);

            // Get enhanced company data
            $globalCompany = $this->getEnhancedCompanyData($sale->company_id);
            $globalCompany = $this->normalizeCompanyData($globalCompany);

            // Determine format from request
            $format = request()->get('format', 'a4');
            
            // Use enhanced templates
            if ($format === 'thermal') {
                $viewName = view()->exists('admin.pos.receipt-thermal-enhanced') 
                    ? 'admin.pos.receipt-thermal-enhanced' 
                    : 'admin.pos.receipt';
            } else {
                $viewName = view()->exists('admin.pos.receipt-a4-enhanced') 
                    ? 'admin.pos.receipt-a4-enhanced' 
                    : 'admin.pos.receipt-a4';
            }

            return view($viewName, compact('sale', 'globalCompany'));
        } catch (\Exception $e) {
            Log::error('Enhanced receipt display failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Fallback to regular receipt
            return $this->receipt($sale);
        }
    }

    public function downloadEnhancedBill(PosSale $sale)
    {
        try {
            Log::info('Enhanced bill download started', ['sale_id' => $sale->id]);

            // Load sale data with enhanced relationships
            $sale->load([
                'items' => function ($query) {
                    $query->select([
                        'id', 'pos_sale_id', 'product_id', 'product_name', 'quantity', 
                        'unit_price', 'original_price', 'discount_amount', 'discount_percentage', 
                        'tax_percentage', 'tax_amount', 'total_amount', 'offer_applied', 
                        'offer_savings', 'notes'
                    ]);
                },
                'items.product' => function ($query) {
                    $query->select(['id', 'name', 'sku', 'tax_percentage', 'price']);
                },
                'cashier' => function ($query) {
                    $query->select(['id', 'name', 'email']);
                }
            ]);

            // Get enhanced company data
            $globalCompany = $this->getEnhancedCompanyData($sale->company_id);
            $globalCompany = $this->normalizeCompanyData($globalCompany);

            // Get format preference
            $format = request()->get('format', 'a4_enhanced');

            // Select enhanced template
            if ($format === 'thermal') {
                $viewName = view()->exists('admin.pos.receipt-thermal-enhanced') 
                    ? 'admin.pos.receipt-thermal-enhanced' 
                    : 'admin.pos.receipt';
                $paperSize = [0, 0, 226.77, 841.89]; // 80mm thermal paper
            } else {
                $viewName = view()->exists('admin.pos.receipt-a4-enhanced') 
                    ? 'admin.pos.receipt-a4-enhanced' 
                    : 'admin.pos.receipt-a4';
                $paperSize = 'A4';
            }

            Log::info('Using enhanced template: ' . $viewName);

            // Create PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);

            $pdf->setPaper($paperSize, 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => $format === 'thermal' ? 'Courier' : 'DejaVu Sans',
                'dpi' => $format === 'thermal' ? 96 : 150,
                'debugKeepTemp' => false
            ]);

            // Generate enhanced filename
            $companySlug = str_slug($globalCompany->company_name ?? 'receipt');
            $formatSuffix = $format === 'thermal' ? 'thermal' : 'enhanced';
            $filename = "enhanced_{$formatSuffix}_receipt_{$companySlug}_{$sale->invoice_number}_" . date('Y-m-d_H-i-s') . '.pdf';

            Log::info('Enhanced bill generated successfully', [
                'filename' => $filename,
                'company' => $globalCompany->company_name,
                'template' => $viewName,
                'format' => $format
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Enhanced bill download failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            // Fallback to regular bill download
            return $this->downloadBill($sale);
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
        // Apply offers to get effective price
        $offerDetails = $this->offerService->getOfferDetails($product);
        $effectivePrice = $this->offerService->getEffectivePrice($product);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'effective_price' => $effectivePrice,
            'stock' => $product->stock,
            'barcode' => $product->barcode ?? null,
            'has_offer' => $effectivePrice < $product->price,
            'offer_details' => $offerDetails,
            'discount_percentage' => $offerDetails ? $offerDetails['discount_percentage'] : 0
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');

        $products = Product::active()
            ->where('stock', '>', 0)
            ->currentTenant()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            })
            ->limit(10)
            ->get();

        // Apply offers to products
        $products = $this->offerService->applyOffersToProducts($products);

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'effective_price' => $product->effective_price,
                'stock' => $product->stock,
                'barcode' => $product->barcode,
                'has_offer' => $product->has_offer ?? false,
                'discount_percentage' => $product->discount_percentage ?? 0
            ];
        }));
    }

    public function calculateOffers(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $offerCalculations = [];
        $totalSavings = 0;

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = $item['quantity'];
            
            // Get best offer for this product
            $offerDetails = $this->offerService->getOfferDetails($product);
            
            if ($offerDetails) {
                $itemTotal = $product->price * $quantity;
                $discountedTotal = $offerDetails['discounted_price'] * $quantity;
                $savings = $itemTotal - $discountedTotal;
                
                $offerCalculations[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'original_price' => $product->price,
                    'discounted_price' => $offerDetails['discounted_price'],
                    'quantity' => $quantity,
                    'original_total' => $itemTotal,
                    'discounted_total' => $discountedTotal,
                    'savings' => $savings,
                    'offer_name' => $offerDetails['offer']->name,
                    'discount_percentage' => $offerDetails['discount_percentage']
                ];
                
                $totalSavings += $savings;
            }
        }

        return response()->json([
            'success' => true,
            'offer_calculations' => $offerCalculations,
            'total_savings' => $totalSavings,
            'has_offers' => !empty($offerCalculations)
        ]);
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

            // Select template - prioritize enhanced versions first
            $viewName = 'admin.pos.receipt-a4';
            if ($format === 'thermal') {
                $viewName = 'admin.pos.receipt';
            }

            // Try enhanced templates first (best option with product-wise details)
            if ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-enhanced')) {
                $viewName = 'admin.pos.receipt-a4-enhanced';
            } elseif ($format === 'thermal' && view()->exists('admin.pos.receipt-thermal-enhanced')) {
                $viewName = 'admin.pos.receipt-thermal-enhanced';
            } elseif ($format !== 'thermal' && view()->exists('admin.pos.receipt-a4-clean')) {
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
            // return view($viewName, [
            //     'sale' => $sale,
            //     'globalCompany' => $globalCompany
            // ]);
            

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
            return response()->streamDownload(function () use ($pdf) {
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
            return response()->streamDownload(function () use ($pdf) {
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
            'website' => '',
            'gst_number' => '',
            'company_logo' => '',
            'company_logo_pdf' => null, // FIXED: Added missing property
            'full_address' => 'Natural & Organic Products Store',
            'contact_info' => '',
            'display_name' => 'Green Valley Herbs',
            'company_id' => null,
            'domain' => '',
            'tagline' => '',
            'established_year' => null
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
     * Ensure all required properties exist in company data object
     */
    private function normalizeCompanyData($companyData)
    {
        $requiredProperties = [
            'company_name' => 'Your Store',
            'company_address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => '',
            'company_phone' => '',
            'company_email' => '',
            'website' => '',
            'gst_number' => '',
            'company_logo' => '',
            'company_logo_pdf' => null,
            'full_address' => '',
            'contact_info' => '',
            'display_name' => 'Your Store',
            'company_id' => null,
            'domain' => '',
            'tagline' => '',
            'established_year' => null
        ];

        // Convert to array for easier manipulation
        $dataArray = is_object($companyData) ? (array)$companyData : $companyData;

        // Ensure all required properties exist
        foreach ($requiredProperties as $property => $defaultValue) {
            if (!isset($dataArray[$property])) {
                $dataArray[$property] = $defaultValue;
            }
        }

        // Convert back to object
        return (object)$dataArray;
    }
    public function testEnhancedCompanyData(PosSale $sale)
    {
        try {
            $companyData = $this->getEnhancedCompanyData($sale->company_id);
            
            // Normalize the data
            $normalizedData = $this->normalizeCompanyData($companyData);

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'sale_company_id' => $sale->company_id,
                'raw_company_data' => $companyData,
                'normalized_company_data' => $normalizedData,
                'current_tenant_id' => $this->getCurrentTenantId(),
                'has_logo_pdf' => isset($normalizedData->company_logo_pdf),
                'logo_pdf_length' => isset($normalizedData->company_logo_pdf) ? strlen($normalizedData->company_logo_pdf ?? '') : 0,
                'all_properties' => array_keys((array)$normalizedData),
                'missing_properties_before' => array_diff(
                    ['company_name', 'full_address', 'contact_info', 'company_logo_pdf'],
                    array_keys((array)$companyData)
                ),
                'missing_properties_after' => array_diff(
                    ['company_name', 'full_address', 'contact_info', 'company_logo_pdf'],
                    array_keys((array)$normalizedData)
                )
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'sale_id' => $sale->id,
                'trace' => $e->getTraceAsString()
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
                return response()->streamDownload(function () use ($pdfOutput) {
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

            return response()->streamDownload(function () use ($pdf) {
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
                'sales' => $sales->map(function ($sale) {
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
     * Enhanced PDF Invoice Download - NEW METHOD
     */
    public function downloadEnhancedInvoice(PosSale $sale)
    {
        try {
            Log::info('Enhanced PDF invoice download started', ['sale_id' => $sale->id]);

            // Load sale data with relationships
            $sale->load(['items.product', 'cashier']);

            // Get enhanced company data with tenant information
            $globalCompany = $this->getEnhancedCompanyData($sale->company_id);
            
            // Normalize company data to ensure all properties exist
            $globalCompany = $this->normalizeCompanyData($globalCompany);

            // Get format preference (default to A4)
            $format = request()->get('format', 'a4_enhanced');

            // Select the appropriate template based on format - prioritize enhanced versions
            $viewName = 'admin.pos.receipt-a4-enhanced';
            $paperSize = 'A4';
            $orientation = 'portrait';
            
            if ($format === 'thermal') {
                $viewName = 'admin.pos.receipt-thermal-enhanced';
                $paperSize = [0, 0, 226.77, 841.89]; // 80mm thermal paper
            } elseif ($format === 'simple') {
                $viewName = 'admin.pos.receipt-a4-enhanced';
            }
            
            // Fallback to existing templates if enhanced doesn't exist
            if (!view()->exists($viewName)) {
                if ($format === 'thermal') {
                    $viewName = 'admin.pos.receipt';
                    if (!view()->exists($viewName)) {
                        $viewName = 'admin.pos.receipt-pdf';
                    }
                } else {
                    $viewName = 'admin.pos.receipt-a4';
                    if (!view()->exists($viewName)) {
                        $viewName = 'admin.pos.receipt-a4-clean';
                    }
                }
            }

            Log::info('Using enhanced invoice template: ' . $viewName);

            // Create PDF with enhanced settings
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);

            // Set paper size and orientation
            $pdf->setPaper($paperSize, $orientation);

            // Enhanced PDF options for better quality
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => $format === 'thermal' ? 'Courier' : 'DejaVu Sans',
                'dpi' => $format === 'thermal' ? 96 : 150, // Lower DPI for thermal
                'debugKeepTemp' => false,
                'chroot' => public_path(), // Allow access to public assets
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true
            ]);

            // Generate filename with tenant info and format
            $companySlug = str_slug($globalCompany->company_name ?? 'invoice');
            $formatSuffix = $format === 'thermal' ? 'receipt' : 'invoice';
            $filename = "{$formatSuffix}_{$companySlug}_{$sale->invoice_number}_" . date('Y-m-d_H-i-s') . '.pdf';

            Log::info('Enhanced PDF invoice generated successfully', [
                'filename' => $filename,
                'company' => $globalCompany->company_name,
                'template' => $viewName,
                'format' => $format
            ]);

            // Return enhanced PDF download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public'
            ]);

        } catch (\Exception $e) {
            Log::error('Enhanced PDF invoice download failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Fallback to simple download
            return $this->downloadBillSimple($sale);
        }
    }

    /**
     * Get enhanced company data with complete tenant information
     */
    private function getEnhancedCompanyData($companyId = null)
    {
        try {
            if (!$companyId) {
                $companyId = $this->getCurrentTenantId();
            }

            if (!$companyId) {
                return $this->getFallbackCompanyData();
            }

            // Get company with all details
            $company = \App\Models\SuperAdmin\Company::find($companyId);

            if (!$company) {
                Log::warning('Company not found for enhanced data', ['company_id' => $companyId]);
                return $this->getFallbackCompanyData();
            }

            // Format complete address
            $addressParts = array_filter([
                $company->address ?? '',
                $company->city ?? '',
                ($company->state ?? '') . (($company->postal_code ?? '') ? ' - ' . ($company->postal_code ?? '') : ''),
                $company->country ?? ''
            ]);
            $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : '';

            // Format contact information
            $contactParts = [];
            if (!empty($company->phone)) {
                $contactParts[] = "Phone: {$company->phone}";
            }
            if (!empty($company->email)) {
                $contactParts[] = "Email: {$company->email}";
            }
            if (!empty($company->website)) {
                $contactParts[] = "Web: {$company->website}";
            }
            $contactInfo = !empty($contactParts) ? implode(' | ', $contactParts) : '';

            // Get logo for PDF (base64 encoded)
            $logoForPdf = null;
            if (!empty($company->logo)) {
                try {
                    $logoForPdf = $this->getCompanyLogoForPDF((object)['company_logo' => $company->logo]);
                } catch (\Exception $e) {
                    Log::warning('Failed to process company logo for PDF', [
                        'company_id' => $companyId,
                        'logo' => $company->logo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Return complete company data object with ALL properties
            return (object) [
                'company_name' => $company->name ?? 'Your Store',
                'company_address' => $company->address ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'country' => $company->country ?? '',
                'postal_code' => $company->postal_code ?? '',
                'company_phone' => $company->phone ?? '',
                'company_email' => $company->email ?? '',
                'website' => $company->website ?? '',
                'gst_number' => $company->gst_number ?? '',
                'company_logo' => $company->logo ?? '',
                'company_logo_pdf' => $logoForPdf,
                'full_address' => $fullAddress,
                'contact_info' => $contactInfo,
                'display_name' => $company->name ?? 'Your Store',
                'company_id' => $company->id ?? null,
                'domain' => $company->domain ?? '',
                'tagline' => $company->tagline ?? '',
                'established_year' => $company->established_year ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error getting enhanced company data', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getFallbackCompanyData();
        }
    }

    /**
     * Preview enhanced invoice (for testing)
     */
    public function previewEnhancedInvoice(PosSale $sale)
    {
        try {
            $sale->load(['items.product', 'cashier']);
            $globalCompany = $this->getEnhancedCompanyData($sale->company_id);
            
            // Normalize company data to ensure all properties exist
            $globalCompany = $this->normalizeCompanyData($globalCompany);

            $viewName = 'admin.pos.invoices.enhanced-a4-invoice';
            
            if (!view()->exists($viewName)) {
                $viewName = 'admin.pos.receipt-a4-clean';
            }

            return view($viewName, [
                'sale' => $sale,
                'globalCompany' => $globalCompany
            ]);
        } catch (\Exception $e) {
            Log::error('Enhanced invoice preview failed', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to preview enhanced invoice: ' . $e->getMessage());
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

            return response()->streamDownload(function () use ($pdf) {
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
