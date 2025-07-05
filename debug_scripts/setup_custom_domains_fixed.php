<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CUSTOM DOMAIN SETUP - COMPLETE FIX ===\n\n";

try {
    // Step 1: Create default theme with ALL required fields
    echo "🎨 SETTING UP DEFAULT THEME...\n";
    $defaultTheme = \App\Models\SuperAdmin\Theme::firstOrCreate(
        ['slug' => 'default'],
        [
            'name' => 'Default Theme',
            'description' => 'Default theme for all stores',
            'category' => 'default',  // Added missing required field
            'status' => 'active',
            'config' => json_encode([
                'primary_color' => '#2d5016',
                'secondary_color' => '#6b8e23',
                'font_family' => 'Inter, sans-serif'
            ])
        ]
    );
    echo "   ✅ Theme created: {$defaultTheme->name} (ID: {$defaultTheme->id})\n\n";

    // Step 2: Create default package with ALL required fields
    echo "📦 SETTING UP DEFAULT PACKAGE...\n";
    $defaultPackage = \App\Models\SuperAdmin\Package::firstOrCreate(
        ['slug' => 'basic'],
        [
            'name' => 'Basic Plan',
            'description' => 'Basic ecommerce features',
            'price' => 29.99,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'features' => json_encode([
                'products' => 100,
                'storage' => '5GB',
                'support' => 'email'
            ]),
            'sort_order' => 1  // Add if required
        ]
    );
    echo "   ✅ Package created: {$defaultPackage->name} (ID: {$defaultPackage->id})\n\n";

    // Step 3: Create companies with all required fields
    echo "🏢 CREATING COMPANIES...\n";
    $companies = [
        [
            'name' => 'Green Valley Herbs',
            'slug' => 'green-valley-herbs',
            'email' => 'admin@greenvalleyherbs.com',
            'domain' => 'greenvalleyherbs.local'
        ],
        [
            'name' => 'Organic Nature Store', 
            'slug' => 'organic-nature-store',
            'email' => 'admin@organicnature.com',
            'domain' => 'organicnature.local'
        ],
        [
            'name' => 'Herbal Wellness Co',
            'slug' => 'herbal-wellness-co', 
            'email' => 'admin@herbalwellness.com',
            'domain' => 'herbalwellness.local'
        ],
        [
            'name' => 'Natural Remedies Shop',
            'slug' => 'natural-remedies-shop',
            'email' => 'admin@naturalremedies.com', 
            'domain' => 'naturalremedies.local'
        ]
    ];

    foreach ($companies as $companyData) {
        // Create or update company with all required fields
        $company = \App\Models\SuperAdmin\Company::updateOrCreate(
            ['slug' => $companyData['slug']],
            [
                'name' => $companyData['name'],
                'email' => $companyData['email'],
                'domain' => $companyData['domain'],
                'status' => 'active',
                'trial_ends_at' => now()->addDays(30),
                'theme_id' => $defaultTheme->id,
                'package_id' => $defaultPackage->id,
                'created_by' => 1,
                'phone' => null,  // Add if required
                'address' => null  // Add if required
            ]
        );
        
        echo "✅ Company: {$company->name}\n";
        echo "   ID: {$company->id}\n";
        echo "   Slug: '{$company->slug}'\n";
        echo "   Domain: '{$company->domain}'\n";
        echo "   Theme: {$defaultTheme->name}\n";
        echo "   Package: {$defaultPackage->name}\n";
        echo "   Status: {$company->status}\n";
        
        // Create admin user if doesn't exist
        $adminUser = \App\Models\User::updateOrCreate(
            ['email' => $companyData['email']],
            [
                'name' => $companyData['name'] . ' Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active',
                'company_id' => $company->id,
                'email_verified_at' => now()  // Add if required
            ]
        );
        
        echo "   Admin Email: {$adminUser->email}\n";
        echo "   Admin Password: password123\n";
        echo "   Admin Panel: http://{$company->domain}:8000/admin/login\n";
        echo "   Store: http://{$company->domain}:8000/shop\n";
        echo "   Debug: http://{$company->domain}:8000/debug-tenant\n\n";
    }

    echo "🌐 HOSTS FILE SETUP:\n";
    echo "Add these entries to your hosts file:\n";
    echo "Location: C:\\Windows\\System32\\drivers\\etc\\hosts\n\n";

    echo "# Multi-tenant SaaS - Custom Domains\n";
    foreach ($companies as $companyData) {
        echo "127.0.0.1 {$companyData['domain']}\n";
    }

    echo "\n🔧 DEVELOPMENT TESTING:\n";
    echo "After updating hosts file, test these URLs:\n\n";

    foreach ($companies as $companyData) {
        echo "Company: {$companyData['name']}\n";
        echo "  Debug: http://{$companyData['domain']}:8000/debug-tenant\n";
        echo "  Store: http://{$companyData['domain']}:8000/shop\n";
        echo "  Admin: http://{$companyData['domain']}:8000/admin/login\n";
        echo "  Credentials: {$companyData['email']} / password123\n\n";
    }

    echo "✅ SETUP COMPLETE! All required database fields included.\n\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "This likely means there are additional required fields.\n";
    echo "Run: php debug_scripts/check_all_tables.php\n";
    echo "To see exactly which fields are required.\n\n";
}
