<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\LandingPageSetting;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $sections = LandingPageSetting::all()->groupBy('section');
        
        return view('super-admin.landing-page.index', compact('sections'));
    }

    public function edit($section)
    {
        $settings = LandingPageSetting::where('section', $section)->get();
        
        return view('super-admin.landing-page.edit', compact('section', 'settings'));
    }

    public function update(Request $request, $section)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            LandingPageSetting::updateOrCreate(
                ['section' => $section, 'key' => $key],
                ['value' => $value, 'is_active' => true]
            );
        }

        return redirect()->route('super-admin.landing-page.index')
                        ->with('success', ucfirst($section) . ' section updated successfully!');
    }

    public function hero()
    {
        $heroSection = (object) [
            'title' => 'Build Your E-commerce Empire',
            'subtitle' => 'Create stunning online stores with our powerful multi-tenant e-commerce platform. Get started today and launch your business in minutes.',
            'primary_button_text' => 'Start Free Trial',
            'primary_button_url' => '/register',
            'secondary_button_text' => 'View Demo',
            'secondary_button_url' => '/demo',
            'trust_text' => 'Trusted by 1000+ businesses worldwide',
            'trust_logos' => [],
            'hero_image' => null,
            'background_type' => 'gradient',
            'gradient_start' => '#667eea',
            'gradient_end' => '#764ba2',
            'is_active' => true,
            'show_trust_indicators' => true
        ];
        
        return view('super-admin.landing-page.sections.hero', compact('heroSection'));
    }

    public function updateHero(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:500',
            'description' => 'required|string|max:1000',
            'cta_text' => 'required|string|max:50',
            'cta_link' => 'required|url',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $settings = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'cta_text' => $request->cta_text,
            'cta_link' => $request->cta_link
        ];

        if ($request->hasFile('background_image')) {
            $imagePath = $request->file('background_image')->store('landing-page/hero', 'public');
            $settings['background_image'] = $imagePath;
        }

        foreach ($settings as $key => $value) {
            LandingPageSetting::updateOrCreate(
                ['section' => 'hero', 'key' => $key],
                ['value' => $value, 'type' => 'text', 'is_active' => true]
            );
        }

        return redirect()->route('super-admin.landing-page.index')
                        ->with('success', 'Hero section updated successfully!');
    }

    public function features()
    {
        $featuresSection = (object) [
            'section_title' => 'Powerful Features',
            'section_subtitle' => 'Everything you need to build and grow your online business',
            'layout_style' => 'grid',
            'features_per_row' => '3',
            'is_active' => true,
            'features' => [
                [
                    'title' => 'Easy Setup',
                    'icon' => 'fas fa-rocket',
                    'description' => 'Get your store up and running in minutes with our intuitive setup wizard.',
                    'link_text' => '',
                    'link_url' => '',
                    'is_active' => true,
                    'sort_order' => 1
                ]
            ]
        ];
        
        return view('super-admin.landing-page.sections.features', compact('featuresSection'));
    }

    public function updateFeatures(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
            'section_subtitle' => 'required|string|max:500',
            'features' => 'required|array|min:1',
            'features.*.title' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:500',
            'features.*.icon' => 'required|string|max:50'
        ]);

        // Update section title and subtitle
        LandingPageSetting::updateOrCreate(
            ['section' => 'features', 'key' => 'section_title'],
            ['value' => $request->section_title, 'type' => 'text', 'is_active' => true]
        );

        LandingPageSetting::updateOrCreate(
            ['section' => 'features', 'key' => 'section_subtitle'],
            ['value' => $request->section_subtitle, 'type' => 'text', 'is_active' => true]
        );

        // Update features
        LandingPageSetting::updateOrCreate(
            ['section' => 'features', 'key' => 'features_list'],
            ['value' => $request->features, 'type' => 'array', 'is_active' => true]
        );

        return redirect()->route('super-admin.landing-page.index')
                        ->with('success', 'Features section updated successfully!');
    }

    public function pricing()
    {
        $packages = \App\Models\SuperAdmin\Package::active()->orderBy('sort_order')->get();
        $pricingSection = (object) [
            'section_title' => 'Choose Your Plan',
            'section_subtitle' => 'Select the perfect plan for your business needs',
            'is_active' => true,
            'show_annual_toggle' => true,
            'show_features' => true,
            'annual_discount' => 20,
            'selected_packages' => $packages->pluck('id')->toArray(),
            'cta_text' => 'Need a custom plan? Contact us!',
            'cta_button_text' => 'Contact Sales',
            'cta_button_url' => '/contact',
            'faqs' => []
        ];
        
        return view('super-admin.landing-page.sections.pricing', compact('pricingSection', 'packages'));
    }

    public function updatePricing(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
            'section_subtitle' => 'required|string|max:500',
            'show_packages' => 'boolean'
        ]);

        $settings = [
            'section_title' => $request->section_title,
            'section_subtitle' => $request->section_subtitle,
            'show_packages' => $request->boolean('show_packages')
        ];

        foreach ($settings as $key => $value) {
            LandingPageSetting::updateOrCreate(
                ['section' => 'pricing', 'key' => $key],
                ['value' => $value, 'type' => 'text', 'is_active' => true]
            );
        }

        return redirect()->route('super-admin.landing-page.index')
                        ->with('success', 'Pricing section updated successfully!');
    }

    public function contact()
    {
        $contactSection = (object) [
            'section_title' => 'Get In Touch',
            'section_subtitle' => 'Ready to start your e-commerce journey? Contact us today and let\'s build something amazing together.',
            'phone' => '+1 (555) 123-4567',
            'email' => 'hello@yourdomain.com',
            'address' => '123 Business Street, Suite 100\nCity, State 12345\nUnited States',
            'business_hours' => 'Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 10:00 AM - 4:00 PM\nSunday: Closed',
            'response_time' => 'Within 24 hours',
            'timezone' => 'UTC',
            'facebook_url' => '',
            'twitter_url' => '',
            'linkedin_url' => '',
            'instagram_url' => '',
            'show_contact_form' => true,
            'form_title' => 'Send us a message',
            'form_subtitle' => 'We\'ll get back to you within 24 hours',
            'success_message' => 'Thank you for your message! We\'ll get back to you soon.',
            'required_fields' => ['name', 'email', 'message'],
            'show_map' => false,
            'map_latitude' => '40.7128',
            'map_longitude' => '-74.0060',
            'map_zoom' => 15,
            'is_active' => true,
            'show_social_links' => true,
            'layout_style' => 'side-by-side',
            'notification_email' => config('mail.from.address'),
            'send_auto_reply' => true,
            'auto_reply_subject' => 'Thank you for contacting us!'
        ];
        
        return view('super-admin.landing-page.sections.contact', compact('contactSection'));
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'section_title' => 'required|string|max:255',
            'section_subtitle' => 'required|string|max:500',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_linkedin' => 'nullable|url',
            'social_instagram' => 'nullable|url'
        ]);

        $settings = $request->only([
            'section_title', 'section_subtitle', 'email', 'phone', 'address',
            'social_facebook', 'social_twitter', 'social_linkedin', 'social_instagram'
        ]);

        foreach ($settings as $key => $value) {
            LandingPageSetting::updateOrCreate(
                ['section' => 'contact', 'key' => $key],
                ['value' => $value, 'type' => 'text', 'is_active' => true]
            );
        }

        return redirect()->route('super-admin.landing-page.index')
                        ->with('success', 'Contact section updated successfully!');
    }
}
