<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\Theme;

class PackageSeeder extends Seeder
{
    public function run()
    {
        // Create default themes first
        $themes = [
            [
                'name' => 'Modern',
                'slug' => 'modern',
                'description' => 'Clean and modern design perfect for any business',
                'preview_image' => '/images/themes/modern.jpg',
                'config' => [
                    'primary_color' => '#10B981',
                    'secondary_color' => '#6B7280',
                    'layout' => 'modern'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Traditional ecommerce design with proven conversion rates',
                'preview_image' => '/images/themes/classic.jpg',
                'config' => [
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#6B7280',
                    'layout' => 'classic'
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Herbal',
                'slug' => 'herbal',
                'description' => 'Perfect for herbal and natural product stores',
                'preview_image' => '/images/themes/herbal.jpg',
                'config' => [
                    'primary_color' => '#059669',
                    'secondary_color' => '#6B7280',
                    'layout' => 'herbal'
                ],
                'status' => 'active',
            ],
        ];

        foreach ($themes as $theme) {
            Theme::updateOrCreate(['slug' => $theme['slug']], $theme);
        }

        // Create packages
        $packages = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses getting started',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'trial_days' => 15,
                'features' => [
                    'Up to 100 products',
                    'Basic ecommerce features',
                    'Standard themes',
                    'Email support',
                    'Basic inventory management',
                ],
                'limits' => [
                    'products' => 100,
                    'storage' => '1GB',
                    'users' => 2,
                ],
                'is_popular' => false,
                'sort_order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing businesses with advanced needs',
                'price' => 79.00,
                'billing_cycle' => 'monthly',
                'trial_days' => 15,
                'features' => [
                    'Up to 1,000 products',
                    'Full ecommerce features',
                    'All themes',
                    'Priority support',
                    'Advanced inventory management',
                    'POS system',
                    'Analytics & reports',
                ],
                'limits' => [
                    'products' => 1000,
                    'storage' => '10GB',
                    'users' => 10,
                ],
                'is_popular' => true,
                'sort_order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large businesses with unlimited requirements',
                'price' => 199.00,
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'features' => [
                    'Unlimited products',
                    'All features included',
                    'Premium themes',
                    '24/7 dedicated support',
                    'Full inventory management',
                    'Advanced POS system',
                    'Custom integrations',
                    'White-label options',
                ],
                'limits' => [
                    'products' => -1, // Unlimited
                    'storage' => '100GB',
                    'users' => -1, // Unlimited
                ],
                'is_popular' => false,
                'sort_order' => 3,
                'status' => 'active',
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(['slug' => $package['slug']], $package);
        }
    }
}
