<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

return new class extends Migration
{
    public function up()
    {
        // Seed themes
        $themes = [
            [
                'name' => 'Fashion Store',
                'slug' => 'fashion-store',
                'description' => 'Modern and elegant theme perfect for clothing and fashion stores',
                'category' => 'clothing',
                'price' => 0,
                'is_free' => true,
                'features' => json_encode(['Responsive Design', 'Product Gallery', 'Wishlist', 'Quick View']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Electronics Hub',
                'slug' => 'electronics-hub',
                'description' => 'High-tech theme designed for electronics and gadget stores',
                'category' => 'electronics',
                'price' => 49.99,
                'is_free' => false,
                'features' => json_encode(['Product Comparison', 'Reviews & Ratings', 'Advanced Filters']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Fresh Market',
                'slug' => 'fresh-market',
                'description' => 'Clean and fresh theme ideal for grocery and food stores',
                'category' => 'grocery',
                'price' => 39.99,
                'is_free' => false,
                'features' => json_encode(['Recipe Integration', 'Nutrition Facts', 'Delivery Zones']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Universal Store',
                'slug' => 'universal-store',
                'description' => 'Versatile theme suitable for any type of general merchandise',
                'category' => 'general',
                'price' => 0,
                'is_free' => true,
                'features' => json_encode(['Multi-Category Support', 'Flexible Layout', 'Custom Sections']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($themes as $theme) {
            DB::table('themes')->updateOrInsert(
                ['slug' => $theme['slug']], 
                $theme
            );
        }

        // Seed packages
        $packages = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses just getting started',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 15,
                'features' => json_encode(['Up to 100 products', '1 theme included', 'Basic analytics', 'Email support']),
                'limits' => json_encode(['products' => 100, 'storage' => '1GB']),
                'is_popular' => false,
                'sort_order' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses with advanced features',
                'price' => 59.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 15,
                'features' => json_encode(['Up to 1,000 products', 'All themes included', 'Advanced analytics', 'Priority support']),
                'limits' => json_encode(['products' => 1000, 'storage' => '10GB']),
                'is_popular' => true,
                'sort_order' => 2,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large businesses requiring maximum performance',
                'price' => 149.99,
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'features' => json_encode(['Unlimited products', 'Premium themes', 'Advanced analytics', '24/7 support']),
                'limits' => json_encode(['products' => 'unlimited', 'storage' => '100GB']),
                'is_popular' => false,
                'sort_order' => 3,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($packages as $package) {
            DB::table('packages')->updateOrInsert(
                ['slug' => $package['slug']], 
                $package
            );
        }

        // Create sample companies
        $superAdmin = DB::table('users')->where('is_super_admin', true)->first();
        if (!$superAdmin) {
            // Create super admin if doesn't exist
            $superAdminId = DB::table('users')->insertGetId([
                'name' => 'Super Administrator',
                'email' => 'superadmin@ecomplatform.com',
                'password' => Hash::make('password123'),
                'is_super_admin' => true,
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $superAdminId = $superAdmin->id;
        }

        $themes = DB::table('themes')->get();
        $packages = DB::table('packages')->get();

        // Create 5 sample companies
        for ($i = 1; $i <= 5; $i++) {
            $companyId = DB::table('companies')->updateOrInsert(
                ['email' => "company{$i}@example.com"],
                [
                    'name' => "Sample Company {$i}",
                    'slug' => "sample-company-{$i}",
                    'domain' => "company{$i}.example.com",
                    'email' => "company{$i}@example.com",
                    'phone' => "+1-555-{$i}00-0000",
                    'address' => "{$i}23 Business Street",
                    'city' => 'Business City',
                    'state' => 'BC',
                    'country' => 'USA',
                    'postal_code' => "1234{$i}",
                    'theme_id' => $themes->random()->id,
                    'package_id' => $packages->random()->id,
                    'status' => $i <= 3 ? 'active' : 'inactive',
                    'trial_ends_at' => now()->addDays(15 - ($i * 2)),
                    'subscription_ends_at' => now()->addMonths(1),
                    'created_by' => $superAdminId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            if (is_array($companyId)) {
                $companyId = DB::table('companies')->where('email', "company{$i}@example.com")->first()->id;
            }

            // Create company admin user
            DB::table('users')->updateOrInsert(
                ['email' => "admin@company{$i}.com"],
                [
                    'name' => "Company {$i} Admin",
                    'email' => "admin@company{$i}.com",
                    'password' => Hash::make('password123'),
                    'company_id' => $companyId,
                    'role' => 'admin',
                    'is_super_admin' => false,
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Create billing records
            $package = $packages->random();
            
            // Past billing (paid)
            DB::table('billings')->updateOrInsert(
                [
                    'company_id' => $companyId,
                    'invoice_number' => "INV-2024-" . str_pad($companyId, 5, '0', STR_PAD_LEFT)
                ],
                [
                    'package_id' => $package->id,
                    'amount' => $package->price,
                    'billing_cycle' => $package->billing_cycle,
                    'status' => 'paid',
                    'payment_method' => 'credit_card',
                    'billing_date' => now()->subMonth(),
                    'due_date' => now()->subMonth()->addDays(30),
                    'paid_at' => now()->subMonth()->addDays(5),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Current billing (pending) for active companies
            if ($i <= 3) {
                DB::table('billings')->updateOrInsert(
                    [
                        'company_id' => $companyId,
                        'invoice_number' => "INV-2024-" . str_pad($companyId + 100, 5, '0', STR_PAD_LEFT)
                    ],
                    [
                        'package_id' => $package->id,
                        'amount' => $package->price,
                        'billing_cycle' => $package->billing_cycle,
                        'status' => 'pending',
                        'billing_date' => now(),
                        'due_date' => now()->addDays(30),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            // Create support tickets
            $companyAdmin = DB::table('users')->where('company_id', $companyId)->where('role', 'admin')->first();
            if ($companyAdmin && $i <= 3) {
                DB::table('support_tickets')->updateOrInsert(
                    [
                        'company_id' => $companyId,
                        'title' => "Setup Help for Sample Company {$i}"
                    ],
                    [
                        'user_id' => $companyAdmin->id,
                        'description' => 'Need assistance with initial store setup and configuration.',
                        'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                        'status' => ['open', 'in_progress'][array_rand(['open', 'in_progress'])],
                        'category' => 'technical',
                        'assigned_to' => $superAdminId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }

        // Seed landing page settings
        $landingPageSettings = [
            ['section' => 'hero', 'key' => 'title', 'value' => '"Launch Your E-Commerce Empire"', 'type' => 'text'],
            ['section' => 'hero', 'key' => 'subtitle', 'value' => '"Create stunning online stores with our multi-tenant e-commerce platform"', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'email', 'value' => '"contact@ecomplatform.com"', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'phone', 'value' => '"+1 (555) 123-4567"', 'type' => 'text'],
            ['section' => 'contact', 'key' => 'address', 'value' => '"123 Business Street, Tech City, TC 12345"', 'type' => 'text']
        ];

        foreach ($landingPageSettings as $setting) {
            DB::table('landing_page_settings')->updateOrInsert(
                ['section' => $setting['section'], 'key' => $setting['key']],
                array_merge($setting, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }

    public function down()
    {
        // Clean up sample data
        DB::table('support_tickets')->truncate();
        DB::table('billings')->truncate();
        DB::table('companies')->truncate();
        DB::table('landing_page_settings')->truncate();
        DB::table('themes')->truncate();
        DB::table('packages')->truncate();
        
        // Remove sample users (keep super admin)
        DB::table('users')->where('email', 'LIKE', '%@company%.com')->delete();
    }
};
