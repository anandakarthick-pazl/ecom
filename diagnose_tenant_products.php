<?php

/**
 * Multi-Tenant Product Isolation Diagnostic and Fix
 * 
 * This script diagnoses and fixes product isolation issues in the multi-tenant system.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Multi-Tenant Product Isolation Diagnostic\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Step 1: Check if products table has company_id column
    echo "📊 Step 1: Checking products table structure...\n";
    
    $hasCompanyId = \Schema::hasColumn('products', 'company_id');
    
    if ($hasCompanyId) {
        echo "   ✅ products table has company_id column\n";
    } else {
        echo "   ❌ products table missing company_id column\n";
        echo "   💡 Need to run migration: php artisan migrate\n";
    }
    
    echo "\n";
    
    // Step 2: Check companies in the system
    echo "📊 Step 2: Checking companies in system...\n";
    
    $companies = \App\Models\SuperAdmin\Company::all();
    
    if ($companies->isEmpty()) {
        echo "   ❌ No companies found in system\n";
        echo "   💡 Need to create companies first\n";
        return;
    }
    
    foreach ($companies as $company) {
        echo "   🏢 Company: {$company->name} (ID: {$company->id})\n";
        echo "      Domain: {$company->domain}\n";
        echo "      Status: {$company->status}\n";
    }
    
    echo "\n";
    
    // Step 3: Check products and their company assignments
    echo "📊 Step 3: Checking product company assignments...\n";
    
    if ($hasCompanyId) {
        $allProducts = \DB::table('products')->select('id', 'name', 'company_id')->get();
        $productsWithoutCompany = \DB::table('products')->whereNull('company_id')->count();
        $productsWithCompany = \DB::table('products')->whereNotNull('company_id')->count();
        
        echo "   📦 Total products: " . $allProducts->count() . "\n";
        echo "   ✅ Products with company_id: $productsWithCompany\n";
        echo "   ❌ Products without company_id: $productsWithoutCompany\n";
        
        if ($productsWithoutCompany > 0) {
            echo "   ⚠️  Products without company_id will show on all tenants!\n";
        }
        
        // Show products grouped by company
        foreach ($companies as $company) {
            $companyProducts = \DB::table('products')->where('company_id', $company->id)->count();
            echo "   🏢 {$company->name}: $companyProducts products\n";
        }
        
    } else {
        echo "   ⚠️  Cannot check product assignments without company_id column\n";
    }
    
    echo "\n";
    
    // Step 4: Test tenant isolation
    echo "📊 Step 4: Testing tenant isolation...\n";
    
    foreach ($companies as $company) {
        echo "   🧪 Testing isolation for: {$company->name}\n";
        
        // Simulate setting current tenant
        app()->instance('current_tenant', $company);
        
        try {
            // Use the tenant-scoped query
            $products = \App\Models\Product::all();
            echo "      📦 Products visible: " . $products->count() . "\n";
            
            if ($hasCompanyId) {
                // Check if all products belong to this company
                $wrongCompanyProducts = $products->filter(function($product) use ($company) {
                    return $product->company_id != $company->id;
                });
                
                if ($wrongCompanyProducts->count() > 0) {
                    echo "      ❌ Found " . $wrongCompanyProducts->count() . " products from other companies!\n";
                } else {
                    echo "      ✅ All products belong to this company\n";
                }
            }
            
        } catch (Exception $e) {
            echo "      ❌ Error: " . $e->getMessage() . "\n";
        }
        
        // Clear tenant context
        app()->forgetInstance('current_tenant');
    }
    
    echo "\n";
    
    // Step 5: Suggest fixes
    echo "🔧 Step 5: Recommended fixes...\n";
    
    if (!$hasCompanyId) {
        echo "   1. Run migration to add company_id column:\n";
        echo "      php artisan migrate\n\n";
    }
    
    if ($hasCompanyId && $productsWithoutCompany > 0) {
        echo "   2. Assign existing products to companies:\n";
        echo "      Run the fix script to assign orphaned products\n\n";
    }
    
    echo "   3. Ensure tenant middleware is working:\n";
    echo "      Check that TenantMiddleware is applied to web routes\n\n";
    
    echo "   4. Clear all caches:\n";
    echo "      php artisan optimize:clear\n\n";
    
} catch (Exception $e) {
    echo "❌ Diagnostic failed: " . $e->getMessage() . "\n";
}

?>
