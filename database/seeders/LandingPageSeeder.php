<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\LandingPageSetting;

class LandingPageSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Hero Section
            ['section' => 'hero', 'key' => 'title', 'value' => 'Launch Your E-Commerce Empire', 'type' => 'text'],
            ['section' => 'hero', 'key' => 'subtitle', 'value' => 'Create stunning online stores with our multi-tenant e-commerce platform', 'type' => 'text'],
            ['section' => 'hero', 'key' => 'description', 'value' => 'Choose from 10+ premium themes and start selling in minutes. No technical knowledge required.', 'type' => 'text'],
            ['section' => 'hero', 'key' => 'cta_text', 'value' => 'Start Free Trial', 'type' => 'text'],
            ['section' => 'hero', 'key' => 'cta_link', 'value' => '#get-started', 'type' => 'text'],

            // Features Section
            ['section' => 'features', 'key' => 'section_title', 'value' => 'Everything You Need to Succeed', 'type' => 'text'],
            ['section' => 'features', 'key' => 'section_subtitle', 'value' => 'Powerful features to grow your e-commerce business', 'type' => 'text'],
            ['section' => 'features', 'key' => 'features_list', 'value' => [
                [
                    'title' => 'Complete E-Commerce',
                    'description' => 'Full-featured online store with inventory management, order processing, and customer management.',
                    'icon' => 'fas fa-shopping-cart'
                ],
                [
                    'title' => 'Mobile Responsive',
                    'description' => 'All themes are fully responsive and optimized for mobile devices and tablets.',
                    'icon' => 'fas fa-mobile-alt'
                ],
                [
                    'title' => 'Payment Gateway',
                    'description' => 'Integrated payment processing with support for all major payment methods.',
                    'icon' => 'fas fa-credit-card'
                ],
                [
                    'title' => 'Analytics & Reports',
                    'description' => 'Comprehensive analytics and reporting to track your business performance.',
                    'icon' => 'fas fa-chart-line'
                ],
                [
                    'title' => '24/7 Support',
                    'description' => 'Round-the-clock customer support to help you whenever you need assistance.',
                    'icon' => 'fas fa-headset'
                ],
                [
                    'title' => 'Secure & Reliable',
                    'description' => 'Enterprise-grade security with SSL encryption and regular backups.',
                    'icon' => 'fas fa-shield-alt'
                ]
            ], 'type' => 'array'],

            // Pricing Section
            ['section' => 'pricing', 'key' => 'section_title', 'value' => 'Simple, Transparent Pricing', 'type' => 'text'],
            ['section' => 'pricing', 'key' => 'section_subtitle', 'value' => 'Choose the perfect plan for your business needs', 'type' => 'text'],
            ['section' => 'pricing', 'key' => 'show_packages', 'value' => 'true', 'type' => 'boolean'],

            // Contact Section
            ['section' => 'contact', 'key' => 'section_title', 'value' => 'Get in Touch', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'section_subtitle', 'value' => 'Have questions? We\'d love to hear from you.', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'email', 'value' => 'contact@ecomplatform.com', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'phone', 'value' => '+1 (555) 123-4567', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'address', 'value' => '123 Business Street, Tech City, TC 12345', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'social_facebook', 'value' => 'https://facebook.com/ecomplatform', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'social_twitter', 'value' => 'https://twitter.com/ecomplatform', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/ecomplatform', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'social_instagram', 'value' => 'https://instagram.com/ecomplatform', 'type' => 'text'],

            // Footer Section
            ['section' => 'footer', 'key' => 'company_description', 'value' => 'The ultimate multi-tenant e-commerce platform for modern businesses. Create stunning online stores with ease.', 'type' => 'text'],
            ['section' => 'footer', 'key' => 'copyright_text', 'value' => '© 2024 EcomPlatform. All rights reserved.', 'type' => 'text'],
            ['section' => 'footer', 'key' => 'tagline', 'value' => 'Built with ❤️ for entrepreneurs worldwide', 'type' => 'text']
        ];

        foreach ($settings as $setting) {
            LandingPageSetting::create($setting);
        }
    }
}
