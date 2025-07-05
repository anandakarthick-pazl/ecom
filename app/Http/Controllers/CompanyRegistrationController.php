<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\Theme;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CompanyRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        $packages = Package::where('status', 'active')->orderBy('sort_order')->get();
        $themes = Theme::where('status', 'active')->get();
        $companies = Company::where('status', 'active')->select('name', 'slug')->get();
        
        return view('landing.index', compact('packages', 'themes', 'companies'));
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_slug' => 'required|string|max:255|unique:companies,slug|regex:/^[a-z0-9-]+$/',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'package_id' => 'required|exists:packages,id',
            'theme_id' => 'required|exists:themes,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'terms' => 'accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Get selected package
            $package = Package::findOrFail($request->package_id);
            
            // Calculate trial and subscription dates
            $trialEndsAt = now()->addDays($package->trial_days);
            $subscriptionEndsAt = null;
            
            if ($package->billing_cycle === 'monthly') {
                $subscriptionEndsAt = $trialEndsAt->addMonth();
            } elseif ($package->billing_cycle === 'yearly') {
                $subscriptionEndsAt = $trialEndsAt->addYear();
            }

            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => $request->company_slug,
                'domain' => $request->company_slug . '.' . config('app.base_domain', 'yourdomain.com'),
                'email' => $request->admin_email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'theme_id' => $request->theme_id,
                'package_id' => $request->package_id,
                'status' => 'active',
                'trial_ends_at' => $trialEndsAt,
                'subscription_ends_at' => $subscriptionEndsAt,
                'created_by' => 1, // Assuming super admin user ID is 1
            ]);

            // Create admin user for the company
            $adminUser = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'company_id' => $company->id,
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active',
            ]);

            // Create default app settings for the company
            \App\Models\AppSetting::create([
                'company_id' => $company->id,
                'key' => 'company_name',
                'value' => $company->name,
            ]);

            \App\Models\AppSetting::create([
                'company_id' => $company->id,
                'key' => 'company_email',
                'value' => $company->email,
            ]);

            \App\Models\AppSetting::create([
                'company_id' => $company->id,
                'key' => 'company_phone',
                'value' => $company->phone,
            ]);

            DB::commit();

            // Send welcome email
            $this->sendWelcomeEmail($company, $adminUser);

            return redirect()->route('registration.success', $company->slug)
                           ->with('success', 'Company registered successfully! Please check your email for login details.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function registrationSuccess($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        return view('landing.success', compact('company'));
    }

    public function checkSlugAvailability(Request $request)
    {
        $slug = Str::slug($request->slug);
        $exists = Company::where('slug', $slug)->exists();
        
        return response()->json([
            'available' => !$exists,
            'slug' => $slug,
        ]);
    }

    private function sendWelcomeEmail(Company $company, User $adminUser)
    {
        // Implementation for sending welcome email
        // You can use Laravel's Mail facade here
    }
    
    public function pricing()
    {
        $packages = Package::where('status', 'active')->orderBy('sort_order')->get();
        return view('landing.pricing', compact('packages'));
    }
    
    public function features()
    {
        return view('landing.features');
    }
    
    public function contact()
    {
        return view('landing.contact');
    }
    
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        try {
            // Send contact email
            // Implementation here
            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }
}
