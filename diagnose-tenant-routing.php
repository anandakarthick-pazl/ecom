<?php
// Quick diagnostic script for tenant routing issues
// Run: php diagnose-tenant-routing.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;

echo "\n===============================================\n";
echo "Tenant Domain Routing Diagnostic\n";
echo "===============================================\n\n";

// Test 1: Check companies in database
echo "1. Checking Companies in Database:\n";
echo "----------------------------------\n";
$companies = Company::all(['id', 'name', 'domain', 'status']);

if ($companies->count() > 0) {
    foreach ($companies as $company) {
        echo "✓ {$company->name}\n";
        echo "  Domain: {$company->domain}\n";
        echo "  Status: {$company->status}\n\n";
    }
} else {
    echo "✗ NO COMPANIES FOUND!\n";
    echo "  Run: mysql -u root -p your_database < setup-multitenant-data.sql\n\n";
}

// Test 2: Check specific domains
echo "2. Testing Domain Lookups:\n";
echo "--------------------------\n";
$testDomains = ['greenvalleyherbs.local', 'organicnature.local'];

foreach ($testDomains as $domain) {
    $company = Company::where('domain', $domain)->first();
    if ($company) {
        echo "✓ {$domain} -> {$company->name} (ID: {$company->id})\n";
    } else {
        echo "✗ {$domain} -> NOT FOUND\n";
    }
}

// Test 3: Products check
echo "\n3. Checking Products:\n";
echo "---------------------\n";
$productCounts = DB::table('products')
    ->selectRaw('company_id, COUNT(*) as count')
    ->groupBy('company_id')
    ->get();

if ($productCounts->count() > 0) {
    foreach ($productCounts as $pc) {
        echo "Company #{$pc->company_id}: {$pc->count} products\n";
    }
} else {
    echo "✗ No products found\n";
}

// Test 4: Check if middleware is registered
echo "\n4. Checking Middleware Registration:\n";
echo "------------------------------------\n";
$middlewareAliases = app()->make('router')->getMiddleware();
$requiredMiddleware = ['tenant', 'main.domain', 'company.context'];

foreach ($requiredMiddleware as $mw) {
    if (isset($middlewareAliases[$mw])) {
        echo "✓ '{$mw}' middleware registered\n";
    } else {
        echo "✗ '{$mw}' middleware NOT registered\n";
    }
}

echo "\n5. URLs to Test:\n";
echo "----------------\n";
echo "Main: http://localhost:8000 (should show SaaS landing)\n";
echo "Shop 1: http://greenvalleyherbs.local:8000 (should redirect to shop)\n";
echo "Shop 2: http://organicnature.local:8000 (should redirect to shop)\n";

echo "\n6. Quick Fixes:\n";
echo "---------------\n";
echo "1. Clear cache: php artisan cache:clear && php artisan config:clear\n";
echo "2. Check hosts file has: 127.0.0.1 greenvalleyherbs.local\n";
echo "3. Use incognito mode to avoid browser cache\n";
echo "4. Check Laravel logs: storage/logs/laravel.log\n";

echo "\n✓ Diagnostic complete!\n\n";
