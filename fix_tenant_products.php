<?php

/**
 * Fix Multi-Tenant Product Isolation
 * 
 * This script fixes product isolation issues by ensuring all products
 * are properly assigned to their respective companies.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Fixing Multi-Tenant Product Isolation\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Step 1: Ensure migration is run
    echo "📊 Step 1: Ensuring database schema is up to date...\n";
    
    echo "   Running migrations...\n";
    \Artisan::call('migrate', ['--force' => true]);
    echo "   ✅ Migrations completed\n\n";
    
    // Step 2: Check companies
    echo "📊 Step 2: Checking companies...\n";
    
    $companies = \App\Models\SuperAdmin\Company::all();
    
    if ($companies->isEmpty()) {
        echo "   ❌ No companies found! Cannot proceed without companies.\n";
        echo "   💡 Please create companies first through the super admin panel.\n";
        return;
    }
    
    echo "   Found " . $companies->count() . " companies:\n";
    foreach ($companies as $company) {
        echo "      🏢 {$company->name} (ID: {$company->id}) - {$company->domain}\n";
    }
    echo "\n";
    
    // Step 3: Check products table structure
    echo "📊 Step 3: Checking products table structure...\n";
    
    if (!\Schema::hasColumn('products', 'company_id')) {
        echo "   ❌ products table still missing company_id column\n";
        echo "   💡 The migration may have failed. Check Laravel logs.\n";
        return;
    }
    
    echo "   ✅ products table has company_id column\n\n";
    
    // Step 4: Find and fix orphaned products
    echo "📊 Step 4: Finding and fixing orphaned products...\n";
    
    $orphanedProducts = \DB::table('products')->whereNull('company_id')->get();
    
    if ($orphanedProducts->isEmpty()) {
        echo "   ✅ No orphaned products found\n";
    } else {
        echo "   ⚠️  Found " . $orphanedProducts->count() . " orphaned products\n";
        
        // Get the first company (primary company) or ask user to specify
        $primaryCompany = $companies->first();
        
        echo "   🔧 Assigning orphaned products to: {$primaryCompany->name}\n";
        
        $updated = \DB::table('products')
                     ->whereNull('company_id')
                     ->update(['company_id' => $primaryCompany->id]);
        
        echo "   ✅ Assigned $updated products to {$primaryCompany->name}\n";
    }
    
    echo "\n";
    
    // Step 5: Fix related tables that might have orphaned records
    echo "📊 Step 5: Fixing related tables...\n";
    
    $relatedTables = [
        'categories' => 'Categories',
        'banners' => 'Banners', 
        'offers' => 'Offers',
        'customers' => 'Customers',
        'orders' => 'Orders',
        'carts' => 'Cart items'
    ];
    
    foreach ($relatedTables as $table => $displayName) {
        if (\Schema::hasTable($table) && \Schema::hasColumn($table, 'company_id')) {
            $orphaned = \DB::table($table)->whereNull('company_id')->count();
            
            if ($orphaned > 0) {
                echo "   🔧 Fixing $orphaned orphaned records in $displayName...\n";
                
                \DB::table($table)
                   ->whereNull('company_id')
                   ->update(['company_id' => $companies->first()->id]);
                   
                echo "      ✅ Fixed $orphaned records\n";
            } else {
                echo "   ✅ No orphaned records in $displayName\n";
            }
        }
    }
    
    echo "\n";
    
    // Step 6: Verify tenant isolation
    echo "📊 Step 6: Verifying tenant isolation...\n";
    
    foreach ($companies as $company) {
        echo "   🧪 Testing: {$company->name}\n";
        
        // Simulate tenant context
        app()->instance('current_tenant', $company);
        \Config::set('app.current_tenant', $company);
        
        // Count products for this tenant
        $productCount = \App\Models\Product::count();
        $directCount = \DB::table('products')->where('company_id', $company->id)->count();
        
        echo "      📦 Products via model: $productCount\n";
        echo "      📦 Products via direct query: $directCount\n";
        
        if ($productCount === $directCount) {
            echo "      ✅ Tenant isolation working correctly\n";
        } else {
            echo "      ❌ Tenant isolation issue detected!\n";
            echo "      💡 Model scope may not be working properly\n";
        }
        
        // Clear context
        app()->forgetInstance('current_tenant');
        \Config::forget('app.current_tenant');
    }
    
    echo "\n";
    
    // Step 7: Clear caches
    echo "📊 Step 7: Clearing caches...\n";
    
    \Artisan::call('config:clear');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    \Artisan::call('optimize:clear');
    
    echo "   ✅ All caches cleared\n\n";
    
    // Step 8: Final verification
    echo "📊 Step 8: Final verification...\n";
    
    $totalProducts = \DB::table('products')->count();
    $productsWithCompany = \DB::table('products')->whereNotNull('company_id')->count();
    
    echo "   📊 Total products: $totalProducts\n";
    echo "   📊 Products with company assignment: $productsWithCompany\n";
    
    if ($totalProducts === $productsWithCompany) {
        echo "   ✅ All products are properly assigned to companies\n";
    } else {
        echo "   ⚠️  Some products still missing company assignment\n";
    }
    
    echo "\n";
    
    // Step 9: Display results per company
    echo "📊 Final Results:\n";
    echo "-" . str_repeat("-", 50) . "\n";
    
    foreach ($companies as $company) {
        $companyProducts = \DB::table('products')->where('company_id', $company->id)->count();
        $companyCategories = \DB::table('categories')->where('company_id', $company->id)->count();
        
        echo "🏢 {$company->name} ({$company->domain}):\n";
        echo "   📦 Products: $companyProducts\n";
        echo "   📂 Categories: $companyCategories\n";
        echo "\n";
    }
    
    echo "🎉 Multi-tenant product isolation fix completed!\n\n";
    
    echo "🔍 Next Steps:\n";
    echo "1. Test Company 1: http://greenvalleyherbs.local:8000/shop\n";
    echo "2. Test Company 2: http://organicnature.local:8000/shop\n";
    echo "3. Each should show only their own products\n";
    echo "4. If issues persist, check Laravel logs in storage/logs/\n\n";
    
} catch (Exception $e) {
    echo "❌ Fix failed: " . $e->getMessage() . "\n";
    echo "💡 Check the error details and try again\n";
}

?>
