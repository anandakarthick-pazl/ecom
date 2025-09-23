<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Product;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EstimateController extends Controller
{
    protected $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }
    public function index(Request $request)
    {
        $query = Estimate::with('creator');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('estimate_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('estimate_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('estimate_date', '<=', $request->date_to);
        }

        $estimates = $query->latest()->paginate(20);

        return view('admin.estimates.index', compact('estimates'));
    }

    public function create()
    {
        $products = Product::active()
            ->with(['category'])
            ->orderBy('name')
            ->get();
        
        // Apply offer details using Product model's getOfferDetails method
        foreach ($products as $product) {
            // Use the Product model's getOfferDetails() method which has the priority system
            $offerDetails = $product->getOfferDetails();
            
            if ($offerDetails) {
                $product->effective_price = $offerDetails['discounted_price'];
                $product->has_offer = true;
                $product->discount_percentage = $offerDetails['discount_percentage'];
                $product->offer_details = $offerDetails;
            } else {
                // No offers, use regular price
                $product->effective_price = $product->price;
                $product->has_offer = false;
                $product->discount_percentage = 0;
                $product->offer_details = null;
            }
        }
        
        return view('admin.estimates.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'estimate_date' => 'required|date',
            'valid_until' => 'required|date|after:estimate_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            // Create estimate
            $estimate = Estimate::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]);

            // Create estimate items
            foreach ($request->items as $item) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('success', 'Estimate created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error creating estimate: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(Estimate $estimate)
    {
        $estimate->load(['items.product', 'creator']);
        return view('admin.estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('error', 'Only draft estimates can be edited!');
        }

        $products = Product::active()
            ->with(['category'])
            ->orderBy('name')
            ->get();
        
        // Apply offer details using Product model's getOfferDetails method
        foreach ($products as $product) {
            // Use the Product model's getOfferDetails() method which has the priority system
            $offerDetails = $product->getOfferDetails();
            
            if ($offerDetails) {
                $product->effective_price = $offerDetails['discounted_price'];
                $product->has_offer = true;
                $product->discount_percentage = $offerDetails['discount_percentage'];
                $product->offer_details = $offerDetails;
            } else {
                // No offers, use regular price
                $product->effective_price = $product->price;
                $product->has_offer = false;
                $product->discount_percentage = 0;
                $product->offer_details = null;
            }
        }
        
        $estimate->load(['items.product']);
        
        return view('admin.estimates.edit', compact('estimate', 'products'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('error', 'Only draft estimates can be updated!');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'estimate_date' => 'required|date',
            'valid_until' => 'required|date|after:estimate_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            // Update estimate
            $estimate->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions
            ]);

            // Delete existing items and create new ones
            $estimate->items()->delete();
            foreach ($request->items as $item) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('success', 'Estimate updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error updating estimate: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateStatus(Request $request, Estimate $estimate)
{
    $request->validate([
        'status' => 'required|in:draft,sent,accepted,rejected,expired'
    ]);

    $estimate->update(['status' => $request->status]);

    if ($request->status === 'sent') {
        $estimate->update(['sent_at' => now()]);
    }

    if ($request->status === 'accepted') {
        $estimate->update(['accepted_at' => now()]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Estimate status updated successfully!'
    ]);
}


    public function destroy(Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.index')
                           ->with('error', 'Only draft estimates can be deleted!');
        }

        $estimate->delete();

        return redirect()->route('admin.estimates.index')
                        ->with('success', 'Estimate deleted successfully!');
    }

    public function duplicate(Estimate $estimate)
    {
        try {
            DB::beginTransaction();

            $newEstimate = $estimate->replicate();
            $newEstimate->estimate_number = null; // Will be auto-generated
            $newEstimate->status = 'draft';
            $newEstimate->sent_at = null;
            $newEstimate->accepted_at = null;
            $newEstimate->created_by = Auth::id();
            $newEstimate->save();

            // Duplicate items
            foreach ($estimate->items as $item) {
                $newItem = $item->replicate();
                $newItem->estimate_id = $newEstimate->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('admin.estimates.edit', $newEstimate)
                           ->with('success', 'Estimate duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error duplicating estimate: ' . $e->getMessage());
        }
    }

    /**
     * Download estimate as PDF
     */
    public function download(Estimate $estimate)
    {
        try {
            // Load estimate with items, products, and their offer details
            $estimate->load([
                'items.product.category',
                'items.product.offers' => function($query) {
                    $query->active()->current();
                },
                'creator'
            ]);
            
            // Apply offers to each product in estimate items
            foreach ($estimate->items as $item) {
                if ($item->product) {
                    // Get offer details using Product model's method
                    $offerDetails = $item->product->getOfferDetails();
                    
                    if ($offerDetails) {
                        $item->product->effective_price = $offerDetails['discounted_price'];
                        $item->product->has_offer = true;
                        $item->product->discount_percentage = $offerDetails['discount_percentage'];
                        $item->product->offer_details = $offerDetails;
                    } else {
                        $item->product->effective_price = $item->product->price;
                        $item->product->has_offer = false;
                        $item->product->discount_percentage = 0;
                        $item->product->offer_details = null;
                    }
                    
                    // Calculate tax amounts
                    $item->product->item_tax_amount = $item->product->getTaxAmount($item->unit_price);
                    $item->product->item_tax_percentage = $item->product->tax_percentage ?? 0;
                    
                    // Store pricing details for PDF
                    $item->mrp_price = $item->product->price; // Original MRP
                    $item->offer_price = $item->product->effective_price ?? $item->unit_price; // Discounted price
                    $item->discount_amount = max(0, $item->mrp_price - $item->offer_price);
                    $item->discount_percentage = $item->discount_amount > 0 
                        ? round(($item->discount_amount / $item->mrp_price) * 100, 1)
                        : 0;
                    $item->tax_amount = ($item->unit_price * $item->quantity * $item->product->item_tax_percentage) / 100;
                    $item->line_total_with_tax = $item->total_price + $item->tax_amount;
                }
            }

            // Get company data with error handling
            $companyId = session('selected_company_id');
            $globalCompany = $this->getCompanyData($companyId);
            
            // Log company data for debugging
            \Log::info('Company data for estimate PDF', [
                'estimate_id' => $estimate->id,
                'company_id' => $companyId,
                'has_full_address' => isset($globalCompany->full_address),
                'company_name' => $globalCompany->company_name ?? 'N/A'
            ]);

            // Create PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.estimates.pdf', [
                'estimate' => $estimate,
                'globalCompany' => $globalCompany
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 150
            ]);

            // Generate filename
            $filename = 'estimate_' . $estimate->estimate_number . '_' . date('Y-m-d') . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf'
            ]);

        } catch (\Exception $e) {
            \Log::error('Estimate PDF download failed', [
                'estimate_id' => $estimate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get company data for PDF generation
     * FIXED VERSION - properly handles null values and missing properties
     */
    private function getCompanyData($companyId = null)
    {
        try {
            if (!$companyId) {
                $companyId = session('selected_company_id');
            }

            if (!$companyId) {
                return $this->getFallbackCompanyData();
            }

            // Get company from database
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            // echo "<pre>";print_r($company);exit;
            if (!$company) {
                return $this->getFallbackCompanyData();
            }

            // Build full address - properly handle null values
            $addressParts = array_filter([
                $company->address ?? '',
                $company->city ?? '',
                $company->state ?? '',
                $company->postal_code ?? '',
                $company->country ?? ''
            ], function($value) {
                return !empty(trim((string)$value));
            });
            
            $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : 'Address not configured';

            // Build contact info
            $contactParts = [];
            if (!empty($company->phone ?? '')) {
                $contactParts[] = "Phone: {$company->phone}";
            }
            if (!empty($company->email ?? '')) {
                $contactParts[] = "Email: {$company->email}";
            }
            $contactInfo = !empty($contactParts) ? implode(' | ', $contactParts) : 'Contact info not configured';

            // Return properly structured object with all required properties
            
            return (object) [
                'company_name' => $company->name ?? 'Your Company',
                'company_address' => $company->address ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'country' => $company->country ?? '',
                'postal_code' => $company->postal_code ?? '',
                'company_phone' => $company->phone ?? '',
                'company_email' => $company->email ?? '',
                'gst_number' => $company->gst_number ?? '',
                'website' => '', // Company model doesn't have website field
                'company_logo' => $company->logo ?? '',
                'full_address' => $fullAddress, // This is the key fix
                'contact_info' => $contactInfo
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting company data for estimate', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getFallbackCompanyData();
        }
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
     * Convert accepted estimate to POS sale
     */
    public function convertToSale(Estimate $estimate)
    {
        // Check if estimate is accepted
        if ($estimate->status !== 'accepted') {
            return redirect()->back()
                           ->with('error', 'Only accepted estimates can be converted to sales!');
        }

        try {
            DB::beginTransaction();

            // Create POS sale from estimate
            $posSale = PosSale::create([
                'company_id' => $estimate->company_id,
                'sale_date' => now(),
                'customer_name' => $estimate->customer_name,
                'customer_phone' => $estimate->customer_phone,
                'subtotal' => $estimate->subtotal,
                'tax_amount' => $estimate->tax_amount,
                'discount_amount' => $estimate->discount ?? 0,
                'total_amount' => $estimate->total_amount,
                'paid_amount' => $estimate->total_amount, // Assume full payment
                'change_amount' => 0,
                'payment_method' => 'cash', // Default to cash, can be changed later
                'status' => 'completed',
                'notes' => 'Converted from Estimate #' . $estimate->estimate_number . ($estimate->notes ? "\n" . $estimate->notes : ''),
                'cashier_id' => Auth::id()
            ]);

            // Create POS sale items from estimate items
            foreach ($estimate->items as $item) {
                PosSaleItem::create([
                    'pos_sale_id' => $posSale->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product ? $item->product->name : 'Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'original_price' => $item->unit_price,
                    'discount_amount' => 0,
                    'discount_percentage' => 0,
                    'tax_percentage' => 0,
                    'tax_amount' => 0,
                    'total_amount' => $item->quantity * $item->unit_price,
                    'offer_applied' => false,
                    'offer_savings' => 0,
                    'notes' => $item->description,
                    'company_id' => $estimate->company_id
                ]);

                // Update product stock if needed
                if ($item->product) {
                    $product = $item->product;
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            // Mark estimate as converted
            $estimate->update([
                'status' => 'converted',
                'converted_at' => now(),
                'converted_to_sale_id' => $posSale->id
            ]);

            DB::commit();

            return redirect()->route('admin.pos.show', $posSale)
                           ->with('success', 'Estimate successfully converted to sale! Invoice #' . $posSale->invoice_number);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error converting estimate to sale: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Error converting estimate to sale: ' . $e->getMessage());
        }
    }

    /**
     * Fallback company data when actual data is not available
     * FIXED VERSION - includes all required properties with safe defaults
     */
    private function getFallbackCompanyData()
    {
        return (object) [
            'company_name' => 'Your Company',
            'company_address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => '',
            'company_phone' => '',
            'company_email' => '',
            'gst_number' => '',
            'website' => '',
            'company_logo' => '',
            'full_address' => 'Please configure company address in settings',
            'contact_info' => 'Please configure company contact information',
        ];
    }
}
