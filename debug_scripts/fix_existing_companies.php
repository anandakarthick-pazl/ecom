<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== QUICK FIX: ADDING MISSING FIELDS TO EXISTING COMPANIES ===\n\n";

try {
    // Create default theme if not exists
    $defaultTheme = \App\Models\SuperAdmin\Theme::firstOrCreate(
        ['slug' => 'default'],
        [
            'name' => 'Default Theme',
            'description' => 'Default theme for all stores',
            'status' => 'active',
            'config' => json_encode(['primary_color' => '#2d5016'])
        ]
    );

    // Create default package if not exists  
    $defaultPackage = \App\Models\SuperAdmin\Package::firstOrCreate(
        ['slug' => 'basic'],
        [
            'name' => 'Basic Plan',
            'description' => 'Basic ecommerce features',
            'price' => 29.99,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'features' => json_encode(['products' => 100])
        ]
    );

    echo "✅ Default theme: {$defaultTheme->name} (ID: {$defaultTheme->id})\n";
    echo "✅ Default package: {$defaultPackage->name} (ID: {$defaultPackage->id})\n\n";

    // Update existing companies that have null theme_id or package_id
    $companies = \App\Models\SuperAdmin\Company::whereNull('theme_id')
                                              ->orWhereNull('package_id')
                                              ->get();

    if ($companies->count() > 0) {
        echo "🔧 FIXING EXISTING COMPANIES:\n";
        foreach ($companies as $company) {
            $company->update([
                'theme_id' => $company->theme_id ?? $defaultTheme->id,
                'package_id' => $company->package_id ?? $defaultPackage->id,
                'created_by' => $company->created_by ?? 1
            ]);
            echo "   ✅ Fixed: {$company->name}\n";
        }
    } else {
        echo "✅ All companies already have required fields\n";
    }

    echo "\n🎯 NOW RUN THE MAIN SETUP:\n";
    echo "php debug_scripts/setup_custom_domains.php\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
