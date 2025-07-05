<?php

/**
 * Verify Multi-Tenant Product Isolation
 * 
 * This script verifies that products are properly isolated between tenants
 * by simulating requests to different tenant domains.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Verifying Multi-Tenant Product Isolation\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Get all companies
    $companies = \App\Models\SuperAdmin\Company::all();
    
    if ($companies->isEmpty()) {
        echo "❌ No companies found in system\n";
        return;
    }
    
    echo "📊 Testing " . $companies->count() . " companies:\n\n";
    
    $results = [];
    
    foreach ($companies as $company) {
        echo "🏢 Testing: {$company->name} ({$company->domain})\n";
        
        // Clear any existing tenant context
        app()->forgetInstance('current_tenant');
        \Config::forget('app.current_tenant');
        
        // Set up tenant context (simulate TenantMiddleware)
        app()->instance('current_tenant', $company);
        \Config::set('app.current_tenant', $company);
        
        // Test product queries
        try {
            // Test 1: Get all products using the model (should use tenant scope)
            $products = \App\Models\Product::all();
            $productCount = $products->count();
            
            // Test 2: Get products directly from database
            $directProducts = \DB::table('products')->where('company_id', $company->id)->count();
            
            // Test 3: Check if any products belong to other companies
            $wrongCompanyProducts = $products->filter(function($product) use ($company) {
                return $product->company_id != $company->id;
            });
            
            // Test 4: Get categories
            $categories = \App\Models\Category::all();
            $categoryCount = $categories->count();
            
            echo "   📦 Products (via model): $productCount\n";
            echo "   📦 Products (direct query): $directProducts\n";
            echo "   📂 Categories: $categoryCount\n";
            
            if ($productCount === $directProducts) {
                echo "   ✅ Tenant scoping working correctly\n";
            } else {
                echo "   ❌ Tenant scoping issue! Model returned $productCount but database has $directProducts\n";
            }
            
            if ($wrongCompanyProducts->count() === 0) {
                echo "   ✅ All products belong to this company\n";
            } else {
                echo "   ❌ Found " . $wrongCompanyProducts->count() . " products from other companies!\n";
                foreach ($wrongCompanyProducts as $product) {
                    echo "      - Product: {$product->name} (belongs to company_id: {$product->company_id})\n";
                }
            }
            
            $results[$company->id] = [
                'company' => $company->name,
                'domain' => $company->domain,
                'products' => $productCount,
                'categories' => $categoryCount,
                'isolation_working' => $productCount === $directProducts && $wrongCompanyProducts->count() === 0
            ];
            
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
            $results[$company->id] = [
                'company' => $company->name,
                'domain' => $company->domain,
                'error' => $e->getMessage()
            ];
        }
        
        echo "\n";
    }
    
    // Clear tenant context
    app()->forgetInstance('current_tenant');
    \Config::forget('app.current_tenant');
    
    // Summary
    echo "📊 Summary:\n";
    echo "=" . str_repeat("=", 40) . "\n";
    
    $workingCount = 0;
    $totalCount = count($results);
    
    foreach ($results as $result) {
        if (isset($result['error'])) {
            echo "❌ {$result['company']}: Error - {$result['error']}\n";
        } else {
            $status = $result['isolation_working'] ? "✅ Working" : "❌ Issues";
            echo "$status {$result['company']}: {$result['products']} products, {$result['categories']} categories\n";
            
            if ($result['isolation_working']) {
                $workingCount++;
            }
        }
    }
    
    echo "\n";
    
    if ($workingCount === $totalCount) {
        echo "🎉 SUCCESS: All companies have proper tenant isolation!\n";
        echo "\n";
        echo "✅ Next Steps:\n";
        echo "1. Test in browser:\n";
        foreach ($companies as $company) {
            echo "   - http://{$company->domain}:8000/shop\n";
        }
        echo "2. Each company should show only their own products\n";
        echo "3. Add products to different companies through their admin panels\n";
    } else {
        echo "⚠️  Issues found in " . ($totalCount - $workingCount) . " out of $totalCount companies\n";
        echo "\n";
        echo "💡 Troubleshooting steps:\n";
        echo "1. Check if migrations have been run: php artisan migrate\n";
        echo "2. Verify tenant middleware is active\n";
        echo "3. Clear caches: php artisan optimize:clear\n";
        echo "4. Check Laravel logs for errors\n";
    }
    
    echo "\n";
    
    // Test without tenant context to show the difference
    echo "🔍 Test without tenant context (should show all products):\n";
    $allProducts = \DB::table('products')->count();
    echo "   📦 Total products in system: $allProducts\n";
    
    if ($allProducts > 0) {
        $companiesWithProducts = \DB::table('products')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->selectRaw('companies.name, COUNT(*) as product_count')
            ->groupBy('companies.id', 'companies.name')
            ->get();
            
        echo "   📊 Products by company:\n";
        foreach ($companiesWithProducts as $row) {
            echo "      - {$row->name}: {$row->product_count} products\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
}

?>
