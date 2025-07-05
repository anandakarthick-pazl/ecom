<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CUSTOM DOMAIN SETUP FOR MULTI-TENANT SAAS (FIXED) ===\n\n";

try {
    // Step 1: Create default theme if not exists
    echo "🎨 SETTING UP DEFAULT THEME...\n";
    $defaultTheme = \App\Models\SuperAdmin\Theme::firstOrCreate(
        ['slug' => 'default'],
        [
            'name' => 'Default Theme',
            'description' => 'Default theme for all stores',
            'status' => 'active',
            'config' => json_encode([
                'primary_color' => '#2d5016',
                'secondary_color' => '#6b8e23',
                'font_family' => 'Inter, sans-serif'
            ])
        ]
    );
    echo "   ✅ Theme created: {$defaultTheme->name} (ID: {$defaultTheme->id})\n\n";

    // Step 2: Create default package if not exists
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
            ])
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
                'created_by' => 1
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
                'company_id' => $company->id
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

    echo "\n📋 PRODUCTION SETUP:\n";
    echo "For production, point these domains to your server:\n";
    foreach ($companies as $companyData) {
        $productionDomain = str_replace('.local', '.com', $companyData['domain']);
        echo "Domain: {$productionDomain} → Your server IP\n";
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

    echo "💡 ADVANTAGES OF CUSTOM DOMAINS:\n";
    echo "✅ Professional branding for each client\n";
    echo "✅ Better SEO for each store\n";
    echo "✅ Easy SSL certificate management\n";
    echo "✅ No subdomain limitations\n";
    echo "✅ White-label SaaS appearance\n\n";

    echo "🚀 NEXT STEPS:\n";
    echo "1. Update hosts file with the domains above\n";
    echo "2. Test debug URLs to verify domain resolution\n";
    echo "3. Test store and admin access\n";
    echo "4. For production: Point real domains to your server\n";
    echo "5. Set up SSL certificates for each domain\n\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
