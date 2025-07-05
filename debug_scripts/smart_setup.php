<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SMART SETUP: COPY FROM EXISTING RECORDS ===\n\n";

try {
    echo "🔍 ANALYZING EXISTING RECORDS...\n";
    
    // Check if there are existing themes
    $existingTheme = \App\Models\SuperAdmin\Theme::first();
    if ($existingTheme) {
        echo "✅ Found existing theme: {$existingTheme->name}\n";
        echo "   Using theme ID: {$existingTheme->id}\n";
        $defaultTheme = $existingTheme;
    } else {
        echo "❌ No existing themes found\n";
        echo "📋 THEMES TABLE STRUCTURE:\n";
        $themeColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE themes");
        foreach ($themeColumns as $column) {
            $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
            echo "     {$column->Field} - {$required}\n";
        }
        return;
    }
    
    // Check if there are existing packages
    $existingPackage = \App\Models\SuperAdmin\Package::first();
    if ($existingPackage) {
        echo "✅ Found existing package: {$existingPackage->name}\n";
        echo "   Using package ID: {$existingPackage->id}\n";
        $defaultPackage = $existingPackage;
    } else {
        echo "❌ No existing packages found\n";
        echo "📋 PACKAGES TABLE STRUCTURE:\n";
        $packageColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE packages");
        foreach ($packageColumns as $column) {
            $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
            echo "     {$column->Field} - {$required}\n";
        }
        return;
    }

    echo "\n🏢 CREATING COMPANIES WITH EXISTING THEME/PACKAGE...\n";
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
        ]
    ];

    foreach ($companies as $companyData) {
        // Create company using existing theme and package
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
        
        echo "✅ Company: {$company->name} (ID: {$company->id})\n";
        echo "   Domain: {$company->domain}\n";
        
        // Create admin user
        $adminUser = \App\Models\User::updateOrCreate(
            ['email' => $companyData['email']],
            [
                'name' => $companyData['name'] . ' Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active',
                'company_id' => $company->id,
                'email_verified_at' => now()
            ]
        );
        
        echo "   Admin: {$adminUser->email} / password123\n";
        echo "   Test: http://{$company->domain}:8000/debug-tenant\n\n";
    }

    echo "🌐 HOSTS FILE ENTRIES NEEDED:\n";
    foreach ($companies as $companyData) {
        echo "127.0.0.1 {$companyData['domain']}\n";
    }

    echo "\n✅ SETUP COMPLETE!\n";
    echo "Add the hosts entries above, then test the debug URLs.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n🔧 FALLBACK SOLUTION:\n";
    echo "1. Check what themes/packages exist:\n";
    echo "   SELECT * FROM themes LIMIT 1;\n";
    echo "   SELECT * FROM packages LIMIT 1;\n\n";
    echo "2. Or create them manually in your database\n";
    echo "3. Then run this script again\n";
}
