<?php

/**
 * Test Tenant Context Resolution
 * 
 * This script simulates how TenantMiddleware resolves tenant context
 * for different domains.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Testing Tenant Context Resolution\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test domains
$testDomains = [
    'greenvalleyherbs.local',
    'organicnature.local',
    'localhost'
];

foreach ($testDomains as $domain) {
    echo "🌐 Testing domain: $domain\n";
    
    // Simulate finding company by domain
    $company = \App\Models\SuperAdmin\Company::where('domain', $domain)->first();
    
    if ($company) {
        echo "   ✅ Company found: {$company->name} (ID: {$company->id})\n";
        echo "   📊 Status: {$company->status}\n";
        
        // Simulate setting tenant context
        app()->instance('current_tenant', $company);
        \Config::set('app.current_tenant', $company);
        
        // Test getting current company ID via trait
        $currentId = \App\Models\Product::getCurrentCompanyId();
        echo "   🔧 Current company ID via trait: $currentId\n";
        
        // Test product query
        try {
            $productCount = \App\Models\Product::count();
            echo "   📦 Products accessible: $productCount\n";
            
            // Show sample products
            $sampleProducts = \App\Models\Product::take(3)->get(['id', 'name', 'company_id']);
            if ($sampleProducts->count() > 0) {
                echo "   📝 Sample products:\n";
                foreach ($sampleProducts as $product) {
                    echo "      - {$product->name} (company_id: {$product->company_id})\n";
                }
            } else {
                echo "   📝 No products found for this company\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Error querying products: " . $e->getMessage() . "\n";
        }
        
        // Clear context
        app()->forgetInstance('current_tenant');
        \Config::forget('app.current_tenant');
        
    } else {
        echo "   ❌ No company found for this domain\n";
        
        if ($domain === 'localhost') {
            echo "   💡 This is normal for localhost (main SaaS domain)\n";
        } else {
            echo "   ⚠️  This tenant domain is not configured!\n";
            echo "   💡 Add this company through super admin panel\n";
        }
    }
    
    echo "\n";
}

echo "🔧 Debug Information:\n";
echo "=" . str_repeat("=", 30) . "\n";

// Show all companies
$allCompanies = \App\Models\SuperAdmin\Company::all();
echo "📊 All companies in system:\n";
foreach ($allCompanies as $company) {
    echo "   🏢 {$company->name}:\n";
    echo "      - Domain: {$company->domain}\n";
    echo "      - Status: {$company->status}\n";
    echo "      - ID: {$company->id}\n";
    
    // Check how many products this company has
    $productCount = \DB::table('products')->where('company_id', $company->id)->count();
    echo "      - Products: $productCount\n";
    echo "\n";
}

// Show products without company assignment
$orphanedProducts = \DB::table('products')->whereNull('company_id')->count();
if ($orphanedProducts > 0) {
    echo "⚠️  Found $orphanedProducts products without company assignment!\n";
    echo "💡 These will show on all tenants until assigned to a company\n\n";
}

echo "✨ If both companies should show different products:\n";
echo "1. Ensure products are added through each company's admin panel\n";
echo "2. Check that TenantMiddleware is working on /shop routes\n";
echo "3. Verify BelongsToTenantEnhanced trait is applied to Product model\n";
echo "4. Run fix_tenant_products.php to assign orphaned products\n";

?>
