<?php

namespace App\Http\Controllers;

use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\LandingPageSetting;
use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoRequest;
use App\Traits\HasPagination;

class LandingPageController extends Controller
{
    use HasPagination;
    public function index()
    {
        // Get settings with fallback defaults
        $heroSettings = [];
        $featureSettings = [];
        $contactSettings = [];
        
        try {
            $heroSettings = LandingPageSetting::where('section', 'hero')->get()->pluck('value', 'key')->toArray();
            $featureSettings = LandingPageSetting::where('section', 'features')->get()->pluck('value', 'key')->toArray();
            $contactSettings = LandingPageSetting::where('section', 'contact')->get()->pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            // If tables don't exist yet, use default values
            \Log::info('Landing page settings not available yet: ' . $e->getMessage());
        }
        
        // Default values if settings are empty
        $heroSettings = array_merge([
            'title' => 'Launch Your E-Commerce Empire',
            'subtitle' => 'Create stunning online stores with our multi-tenant e-commerce platform',
            'description' => 'Choose from 10+ premium themes and start selling in minutes. No technical knowledge required.',
            'cta_text' => 'Start Free Trial',
            'cta_link' => '#get-started'
        ], $heroSettings);
        
        $contactSettings = array_merge([
            'email' => 'contact@ecomplatform.com',
            'phone' => '+1 (555) 123-4567',
            'address' => '123 Business Street, Tech City, TC 12345'
        ], $contactSettings);
        
        $themes = Theme::active()->take(10)->get();
        $packages = Package::active()->orderBy('sort_order')->get();
        
        return view('landing-page.index', compact(
            'heroSettings', 
            'featureSettings', 
            'contactSettings', 
            'themes', 
            'packages'
        ));
    }

    public function themes(Request $request)
    {
        $query = Theme::active();
        
        // Apply frontend pagination using the trait
        $themes = $this->applyFrontendPagination($query, $request, '12');
        
        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');
        
        $categories = Theme::CATEGORIES;
        
        return view('landing-page.themes', compact(
            'themes', 
            'categories', 
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function themesByCategory($category, Request $request)
    {
        $query = Theme::active()->where('category', $category);
        
        // Apply frontend pagination using the trait
        $themes = $this->applyFrontendPagination($query, $request, '12');
        
        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');
        
        $categories = Theme::CATEGORIES;
        $currentCategory = $category;
        
        return view('landing-page.themes', compact(
            'themes', 
            'categories', 
            'currentCategory',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function pricing()
    {
        $packages = Package::active()->orderBy('sort_order')->get();
        $pricingSettings = LandingPageSetting::where('section', 'pricing')->get()->pluck('value', 'key');
        
        return view('landing-page.pricing', compact('packages', 'pricingSettings'));
    }

    public function contact()
    {
        $contactSettings = LandingPageSetting::where('section', 'contact')->get()->pluck('value', 'key');
        
        return view('landing-page.contact', compact('contactSettings'));
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
            // Send contact email to admin
            $adminEmail = LandingPageSetting::where('section', 'contact')
                                          ->where('key', 'email')
                                          ->first()
                                          ->value ?? config('mail.from.address');

            Mail::raw($request->message, function ($message) use ($request, $adminEmail) {
                $message->to($adminEmail)
                        ->subject('Contact Form: ' . $request->subject)
                        ->replyTo($request->email, $request->name);
            });

            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            return back()->with('error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }

    public function requestDemo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'company' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'theme_id' => 'required|exists:themes,id',
            'message' => 'nullable|string|max:500'
        ]);

        try {
            $theme = Theme::findOrFail($request->theme_id);
            
            // Send demo request email
            $adminEmail = config('mail.from.address');
            Mail::to($adminEmail)->send(new DemoRequest($request->all(), $theme));

            return response()->json([
                'success' => true, 
                'message' => 'Demo request submitted successfully! We will contact you soon.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Sorry, there was an error submitting your request. Please try again.'
            ]);
        }
    }

    public function about()
    {
        return view('landing-page.about');
    }

    public function features()
    {
        $featureSettings = LandingPageSetting::where('section', 'features')->get()->pluck('value', 'key');
        
        return view('landing-page.features', compact('featureSettings'));
    }

    public function getStarted()
    {
        $themes = Theme::active()->take(6)->get();
        $packages = Package::active()->orderBy('sort_order')->get();
        
        return view('landing-page.get-started', compact('themes', 'packages'));
    }

    public function signup(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'domain' => 'required|string|unique:companies,domain',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'theme_id' => 'required|exists:themes,id',
            'package_id' => 'required|exists:packages,id',
            'phone' => 'nullable|string|max:20'
        ]);

        try {
            $package = Package::findOrFail($request->package_id);
            $password = Str::random(8);

            // Create company
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'domain' => $request->domain,
                'email' => $request->admin_email,
                'phone' => $request->phone,
                'theme_id' => $request->theme_id,
                'package_id' => $request->package_id,
                'status' => 'active',
                'trial_ends_at' => now()->addDays($package->trial_days)
            ]);

            // Create admin user
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($password),
                'company_id' => $company->id,
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active'
            ]);

            // Send welcome email
            Mail::to($request->admin_email)->send(new CompanyCreated($company, $request->admin_email, $password));

            return redirect()->route('landing.signup-success', $company->slug)
                            ->with('success', 'Your account has been created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'There was an error creating your account. Please try again.');
        }
    }

    public function signupSuccess($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        
        return view('landing-page.signup-success', compact('company'));
    }
}
