<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    /**
     * Get the current company ID from various sources with enhanced fallback logic
     */
    private function getCurrentCompanyId()
    {
        $attempts = [];
        
        // Try multiple sources for company ID in order of preference
        $companyId = null;
        
        // 1. Session selected company (highest priority)
        if (session('selected_company_id')) {
            $companyId = session('selected_company_id');
            $attempts[] = "session_selected: {$companyId}";
        }
        
        // 2. Session current company
        if (!$companyId && session('current_company_id')) {
            $companyId = session('current_company_id');
            $attempts[] = "session_current: {$companyId}";
        }
        
        // 3. Authenticated user's company
        if (!$companyId && auth()->check() && auth()->user()->company_id) {
            $companyId = auth()->user()->company_id;
            $attempts[] = "user_company: {$companyId}";
        }
        
        // 4. Tenant context from app container
        if (!$companyId && app()->has('current_tenant')) {
            $tenantId = app('current_tenant')->id;
            if ($tenantId) {
                $companyId = $tenantId;
                $attempts[] = "tenant_context: {$companyId}";
            }
        }
        
        // 5. Request parameter
        if (!$companyId && request()->has('company_id')) {
            $requestCompanyId = request()->get('company_id');
            if ($requestCompanyId) {
                $companyId = $requestCompanyId;
                $attempts[] = "request_param: {$companyId}";
            }
        }
        
        // 6. Domain-based company lookup
        if (!$companyId) {
            $host = request()->getHost();
            $company = Company::where('domain', $host)
                ->orWhere('domain', 'LIKE', "%{$host}%")
                ->where('status', 'active')
                ->first();
            if ($company) {
                $companyId = $company->id;
                $attempts[] = "domain_lookup: {$companyId} (domain: {$company->domain})";
            }
        }
        
        // 7. Fallback to first active company
        if (!$companyId) {
            $firstCompany = Company::where('status', 'active')->orderBy('id')->first();
            if ($firstCompany) {
                $companyId = $firstCompany->id;
                $attempts[] = "fallback_first_active: {$companyId} ({$firstCompany->name})";
                Log::warning('PaymentMethod: Using first active company as fallback', [
                    'company_id' => $companyId,
                    'company_name' => $firstCompany->name,
                    'host' => request()->getHost(),
                    'user_id' => auth()->id()
                ]);
            }
        }
        
        // Validate the company exists and is active
        if ($companyId) {
            $validCompany = Company::where('id', $companyId)
                ->where('status', 'active')
                ->first();
            
            if (!$validCompany) {
                Log::error('PaymentMethod: Resolved company_id is invalid', [
                    'invalid_company_id' => $companyId,
                    'attempts' => $attempts
                ]);
                
                // Try to get a valid company as final fallback
                $fallbackCompany = Company::where('status', 'active')->first();
                if ($fallbackCompany) {
                    $companyId = $fallbackCompany->id;
                    $attempts[] = "final_fallback: {$companyId}";
                } else {
                    $companyId = null;
                }
            }
        }
        
        Log::info('PaymentMethod: Company ID resolution complete', [
            'final_company_id' => $companyId,
            'attempts' => $attempts,
            'session_data' => [
                'selected_company_id' => session('selected_company_id'),
                'current_company_id' => session('current_company_id')
            ],
            'user_data' => [
                'id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? null
            ],
            'request_data' => [
                'host' => request()->getHost(),
                'has_company_param' => request()->has('company_id')
            ]
        ]);
        
        // Throw exception if no company can be determined
        if (!$companyId) {
            throw new \Exception('Unable to determine company_id for payment method operation. Please ensure you are logged in and have access to a valid company.');
        }
        
        return $companyId;
    }

    public function index()
    {
        $companyId = $this->getCurrentCompanyId();
            
        $paymentMethods = PaymentMethod::when($companyId, function($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:razorpay,cod,bank_transfer,upi,gpay',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Payment method image
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'extra_charge' => 'nullable|numeric|min:0',
            'extra_charge_percentage' => 'nullable|numeric|min:0|max:100',
            
            // Razorpay fields
            'razorpay_key_id' => 'required_if:type,razorpay|nullable|string',
            'razorpay_key_secret' => 'required_if:type,razorpay|nullable|string',
            'razorpay_webhook_secret' => 'nullable|string',
            
            // Bank Transfer fields
            'bank_name' => 'required_if:type,bank_transfer|nullable|string',
            'account_name' => 'required_if:type,bank_transfer|nullable|string',
            'account_number' => 'required_if:type,bank_transfer|nullable|string',
            'ifsc_code' => 'required_if:type,bank_transfer|nullable|string',
            'branch_name' => 'nullable|string',
            
            // UPI fields (used for both UPI and G Pay)
            'upi_id' => 'required_if:type,upi,gpay|nullable|string',
            'upi_qr_code' => 'nullable|image|max:2048',
        ]);

        // Set name based on type
        $validated['name'] = $request->type;
        
        // ENHANCED: Get company_id with better logic and logging
        $companyId = $this->getCurrentCompanyId();
        $validated['company_id'] = $companyId;
        
        Log::info('PaymentMethod: Creating new payment method', [
            'type' => $validated['type'],
            'display_name' => $validated['display_name'],
            'company_id' => $companyId,
            'user_id' => auth()->id()
        ]);
        
        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active');
        
        // Ensure numeric fields have default values instead of null
        $validated['minimum_amount'] = $validated['minimum_amount'] ?? 0.00;
        $validated['maximum_amount'] = $validated['maximum_amount'] ?: null;
        $validated['extra_charge'] = $validated['extra_charge'] ?? 0.00;
        $validated['extra_charge_percentage'] = $validated['extra_charge_percentage'] ?? 0.00;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        // Process bank details if bank transfer
        if ($request->type === 'bank_transfer') {
            $validated['bank_details'] = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'branch_name' => $request->branch_name,
            ];
        }
        
        // Handle payment method image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment-methods', 'public');
            $validated['image'] = $path;
        }
        
        // Handle UPI/G Pay QR code upload
        if (in_array($request->type, ['upi', 'gpay']) && $request->hasFile('upi_qr_code')) {
            $path = $request->file('upi_qr_code')->store('payment-methods/upi', 'public');
            $validated['upi_qr_code'] = $path;
        }

        $paymentMethod = PaymentMethod::create($validated);

        Log::info('PaymentMethod: Created successfully', [
            'id' => $paymentMethod->id,
            'type' => $paymentMethod->type,
            'company_id' => $paymentMethod->company_id,
            'is_active' => $paymentMethod->is_active
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        // Build validation rules based on payment method type
        $rules = [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Payment method image
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
            'extra_charge' => 'nullable|numeric|min:0',
            'extra_charge_percentage' => 'nullable|numeric|min:0|max:100',
        ];

        // Add conditional validation rules based on payment method type
        if ($paymentMethod->type === 'razorpay') {
            $rules['razorpay_key_id'] = 'required|string';
            $rules['razorpay_key_secret'] = 'nullable|string'; // Optional on update
            $rules['razorpay_webhook_secret'] = 'nullable|string';
        } elseif ($paymentMethod->type === 'bank_transfer') {
            $rules['bank_name'] = 'required|string';
            $rules['account_name'] = 'required|string';
            $rules['account_number'] = 'required|string';
            $rules['ifsc_code'] = 'required|string';
            $rules['branch_name'] = 'nullable|string';
        } elseif ($paymentMethod->type === 'upi' || $paymentMethod->type === 'gpay') {
            $rules['upi_id'] = 'required|string';
            $rules['upi_qr_code'] = 'nullable|image|max:2048';
        }

        $validated = $request->validate($rules);

        // ENHANCED: Ensure company_id is set if missing
        if (empty($paymentMethod->company_id)) {
            $companyId = $this->getCurrentCompanyId();
            $validated['company_id'] = $companyId;
            
            Log::info('PaymentMethod: Adding missing company_id on update', [
                'payment_method_id' => $paymentMethod->id,
                'company_id' => $companyId
            ]);
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active');
        
        // Ensure numeric fields have default values instead of null
        $validated['minimum_amount'] = $validated['minimum_amount'] ?? 0.00;
        $validated['maximum_amount'] = $validated['maximum_amount'] ?: null;
        $validated['extra_charge'] = $validated['extra_charge'] ?? 0.00;
        $validated['extra_charge_percentage'] = $validated['extra_charge_percentage'] ?? 0.00;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        // Only update Razorpay secret if provided
        if ($paymentMethod->type === 'razorpay' && empty($request->razorpay_key_secret)) {
            unset($validated['razorpay_key_secret']);
        }
        
        // Process bank details if bank transfer
        if ($paymentMethod->type === 'bank_transfer') {
            $validated['bank_details'] = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'branch_name' => $request->branch_name,
            ];
        }
        
        // Handle payment method image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($paymentMethod->image) {
                Storage::disk('public')->delete($paymentMethod->image);
            }
            
            $path = $request->file('image')->store('payment-methods', 'public');
            $validated['image'] = $path;
        }
        
        // Handle UPI/G Pay QR code upload
        if (in_array($paymentMethod->type, ['upi', 'gpay']) && $request->hasFile('upi_qr_code')) {
            // Delete old QR code
            if ($paymentMethod->upi_qr_code) {
                Storage::disk('public')->delete($paymentMethod->upi_qr_code);
            }
            
            $path = $request->file('upi_qr_code')->store('payment-methods/upi', 'public');
            $validated['upi_qr_code'] = $path;
        }

        $paymentMethod->update($validated);

        Log::info('PaymentMethod: Updated successfully', [
            'id' => $paymentMethod->id,
            'type' => $paymentMethod->type,
            'company_id' => $paymentMethod->company_id,
            'is_active' => $paymentMethod->is_active
        ]);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Delete payment method image if exists
        if ($paymentMethod->image) {
            Storage::disk('public')->delete($paymentMethod->image);
        }
        
        // Delete QR code if exists
        if ($paymentMethod->upi_qr_code) {
            Storage::disk('public')->delete($paymentMethod->upi_qr_code);
        }
        
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $paymentMethod->is_active
        ]);
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:payment_methods,id',
            'items.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->items as $item) {
            PaymentMethod::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
