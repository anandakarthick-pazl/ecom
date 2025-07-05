<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyCreated;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['theme', 'package', 'createdBy'])
                           ->latest()
                           ->paginate(15);

        return view('super-admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $themes = Theme::active()->get();
        $packages = Package::active()->get();

        return view('super-admin.companies.create', compact('themes', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'domain' => 'required|string|unique:companies,domain',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'theme_id' => 'required|exists:themes,id',
            'package_id' => 'required|exists:packages,id',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $package = Package::findOrFail($request->package_id);
        
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
        }

        $company = Company::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'domain' => $request->domain,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'logo' => $logoPath,
            'theme_id' => $request->theme_id,
            'package_id' => $request->package_id,
            'status' => 'active',
            'trial_ends_at' => now()->addDays($package->trial_days),
            'created_by' => auth()->id()
        ]);

        // Create admin user for the company
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'company_id' => $company->id,
            'role' => 'admin',
            'is_super_admin' => false,
            'status' => 'active'
        ]);

        // Send welcome email
        try {
            Mail::to($request->admin_email)->send(new CompanyCreated($company, $request->admin_email, $request->admin_password));
        } catch (\Exception $e) {
            // Log error but don't fail the creation
            \Log::error('Failed to send company creation email: ' . $e->getMessage());
        }

        return redirect()->route('super-admin.companies.index')
                        ->with('success', 'Company created successfully!');
    }

    public function show(Company $company)
    {
        $company->load(['theme', 'package', 'users', 'billings', 'supportTickets']);
        
        return view('super-admin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $themes = Theme::active()->get();
        $packages = Package::active()->get();

        return view('super-admin.companies.edit', compact('company', 'themes', 'packages'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'domain' => 'required|string|unique:companies,domain,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'theme_id' => 'required|exists:themes,id',
            'package_id' => 'required|exists:packages,id',
            'status' => 'required|in:active,inactive,suspended',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['logo']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $data['logo'] = $logoPath;
        }

        $company->update($data);

        return redirect()->route('super-admin.companies.index')
                        ->with('success', 'Company updated successfully!');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('super-admin.companies.index')
                        ->with('success', 'Company deleted successfully!');
    }

    public function updateStatus(Request $request, Company $company)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $company->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function extendTrial(Request $request, Company $company)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $trialEnd = $company->trial_ends_at ?: now();
        $company->update([
            'trial_ends_at' => $trialEnd->addDays($request->days)
        ]);

        return response()->json(['success' => true]);
    }
}
