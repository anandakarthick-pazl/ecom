<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\Billing;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }

    public function index()
    {
        $companies = Company::with(['package', 'theme'])
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super_admin.subscriptions.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['package', 'theme', 'users', 'billings']);
        $packages = Package::where('status', 'active')->get();

        return view('super_admin.subscriptions.show', compact('company', 'packages'));
    }

    public function extendTrial(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $company->update([
            'trial_ends_at' => $company->trial_ends_at 
                ? $company->trial_ends_at->addDays($request->days)
                : now()->addDays($request->days),
        ]);

        return redirect()->back()->with('success', "Trial extended by {$request->days} days.");
    }

    public function updateSubscription(Request $request, Company $company)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'subscription_type' => 'required|in:trial,paid',
            'subscription_months' => 'required_if:subscription_type,paid|integer|min:1|max:24',
        ]);

        try {
            DB::beginTransaction();

            $package = Package::findOrFail($request->package_id);
            
            $updateData = [
                'package_id' => $request->package_id,
            ];

            if ($request->subscription_type === 'trial') {
                $updateData['trial_ends_at'] = now()->addDays($package->trial_days);
                $updateData['subscription_ends_at'] = null;
            } else {
                $updateData['trial_ends_at'] = null;
                $updateData['subscription_ends_at'] = now()->addMonths($request->subscription_months);
                
                // Create billing record
                Billing::create([
                    'company_id' => $company->id,
                    'package_id' => $package->id,
                    'amount' => $package->price * $request->subscription_months,
                    'billing_cycle' => $package->billing_cycle,
                    'billing_date' => now(),
                    'status' => 'paid',
                    'payment_method' => 'manual',
                    'transaction_id' => 'MANUAL_' . uniqid(),
                    'notes' => 'Manual subscription update by super admin',
                ]);
            }

            $company->update($updateData);

            DB::commit();

            return redirect()->back()->with('success', 'Subscription updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update subscription: ' . $e->getMessage()]);
        }
    }

    public function suspend(Company $company)
    {
        $company->update(['status' => 'suspended']);
        
        return redirect()->back()->with('success', 'Company suspended successfully.');
    }

    public function activate(Company $company)
    {
        $company->update(['status' => 'active']);
        
        return redirect()->back()->with('success', 'Company activated successfully.');
    }

    public function checkExpired()
    {
        $expiredCompanies = Company::where('status', 'active')
            ->where(function($query) {
                $query->where('trial_ends_at', '<', now())
                      ->orWhere('subscription_ends_at', '<', now());
            })
            ->whereDoesntHave('billings', function($query) {
                $query->where('status', 'paid')
                      ->where('billing_date', '>', now()->subMonth());
            })
            ->get();

        foreach ($expiredCompanies as $company) {
            $company->update(['status' => 'suspended']);
        }

        return redirect()->back()->with('success', "Checked and suspended {$expiredCompanies->count()} expired companies.");
    }

    public function expiringSoon()
    {
        $expiringSoon = Company::where('status', 'active')
            ->where(function($query) {
                $query->whereBetween('trial_ends_at', [now(), now()->addDays(7)])
                      ->orWhereBetween('subscription_ends_at', [now(), now()->addDays(7)]);
            })
            ->with(['package', 'theme'])
            ->get();

        return view('super_admin.subscriptions.expiring', compact('expiringSoon'));
    }
}
