<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Product;
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
            ->with(['category', 'offers' => function($query) {
                $query->active()->current();
            }])
            ->orderBy('name')
            ->get();
        
        // Apply offers to products to get effective prices
        $products = $this->offerService->applyOffersToProducts($products);
        
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
            ->with(['category', 'offers' => function($query) {
                $query->active()->current();
            }])
            ->orderBy('name')
            ->get();
        
        // Apply offers to products to get effective prices
        $products = $this->offerService->applyOffersToProducts($products);
        
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

        return redirect()->back()
                        ->with('success', 'Estimate status updated successfully!');
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
            // Load estimate with items and relationships
            $estimate->load(['items.product', 'creator']);

            // Get company data
            $companyId = session('selected_company_id');
            $globalCompany = $this->getCompanyData($companyId);
            
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
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                           ->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get company data for PDF generation
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

            if (!$company) {
                return $this->getFallbackCompanyData();
            }

            // Build full address - ensure we handle null values properly
            $addressParts = array_filter([
                $company->address,
                $company->city,
                $company->state,
                $company->postal_code,
                $company->country
            ], function($value) {
                return !empty(trim($value));
            });
            $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : '';

            // Build contact info
            $contactParts = [];
            if (!empty($company->phone)) {
                $contactParts[] = "Phone: {$company->phone}";
            }
            if (!empty($company->email)) {
                $contactParts[] = "Email: {$company->email}";
            }
            // Note: Company model doesn't have website field based on the model structure
            $contactInfo = !empty($contactParts) ? implode(' | ', $contactParts) : '';

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
                'full_address' => $fullAddress,
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

        if ($company->website) {
            $contactParts[] = "Web: {$company->website}";
        }

        return implode(' | ', $contactParts);
    }

    /**
     * Fallback company data when actual data is not available
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
            'full_address' => '',
            'contact_info' => '',
        ];
    }
}
