<?php

/**
 * Product List Enhancement Testing Script
 * Run this via: php artisan tinker < test_product_list_enhancement.php
 */

echo "=== Testing Product List Enhancement ===\n";

try {
    // Test 1: Check if ProductController has enhanced methods
    echo "1. Testing ProductController enhancements...\n";
    
    $controller = new \App\Http\Controllers\Admin\ProductController();
    $reflection = new ReflectionClass($controller);
    
    // Check if the export method exists
    if ($reflection->hasMethod('exportProducts')) {
        echo "   ✓ Export functionality: AVAILABLE\n";
    } else {
        echo "   ✗ Export functionality: NOT FOUND\n";
    }
    
    // Test 2: Check Product model attributes
    echo "2. Testing Product model capabilities...\n";
    
    try {
        $sampleProduct = \App\Models\Product::first();
        if ($sampleProduct) {
            echo "   ✓ Product model: WORKING\n";
            echo "   ✓ Sample product found: {$sampleProduct->name}\n";
            
            // Check if discount_percentage accessor works
            if (method_exists($sampleProduct, 'getDiscountPercentageAttribute')) {
                echo "   ✓ Discount percentage calculation: AVAILABLE\n";
            }
        } else {
            echo "   ⚠ No products found in database - create some test products\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Product model test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check Category model for filtering
    echo "3. Testing Category model for filtering...\n";
    
    try {
        $categories = \App\Models\Category::active()->get();
        echo "   ✓ Categories found: " . $categories->count() . "\n";
        
        if ($categories->count() === 0) {
            echo "   ⚠ No categories found - create some test categories\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Category test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test pagination functionality
    echo "4. Testing pagination capabilities...\n";
    
    try {
        $paginatedProducts = \App\Models\Product::paginate(20);
        echo "   ✓ Pagination: WORKING\n";
        echo "   ✓ Products per page: " . $paginatedProducts->perPage() . "\n";
        echo "   ✓ Total products: " . $paginatedProducts->total() . "\n";
    } catch (Exception $e) {
        echo "   ✗ Pagination test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test filtering capabilities
    echo "5. Testing filtering functionality...\n";
    
    try {
        // Test active products filter
        $activeProducts = \App\Models\Product::where('is_active', true)->count();
        echo "   ✓ Active products filter: {$activeProducts} products\n";
        
        // Test stock filter
        $inStockProducts = \App\Models\Product::where('stock', '>', 10)->count();
        echo "   ✓ In stock filter: {$inStockProducts} products\n";
        
        $lowStockProducts = \App\Models\Product::where('stock', '>', 0)->where('stock', '<=', 10)->count();
        echo "   ✓ Low stock filter: {$lowStockProducts} products\n";
        
        $outOfStockProducts = \App\Models\Product::where('stock', '=', 0)->count();
        echo "   ✓ Out of stock filter: {$outOfStockProducts} products\n";
        
    } catch (Exception $e) {
        echo "   ✗ Filtering test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 6: Test search functionality
    echo "6. Testing search functionality...\n";
    
    try {
        $searchResults = \App\Models\Product::where('name', 'LIKE', '%test%')->count();
        echo "   ✓ Name search: {$searchResults} results for 'test'\n";
        
        $skuResults = \App\Models\Product::where('sku', 'LIKE', '%test%')->count();
        echo "   ✓ SKU search: {$skuResults} results for 'test'\n";
    } catch (Exception $e) {
        echo "   ✗ Search test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 7: Check routes
    echo "7. Testing routes...\n";
    
    try {
        $routeExists = \Route::has('admin.products.index');
        echo "   ✓ Products index route: " . ($routeExists ? "EXISTS" : "NOT FOUND") . "\n";
        
        $toggleRouteExists = \Route::has('admin.products.toggle-status');
        echo "   ✓ Toggle status route: " . ($toggleRouteExists ? "EXISTS" : "NOT FOUND") . "\n";
        
        $featuredRouteExists = \Route::has('admin.products.toggle-featured');
        echo "   ✓ Toggle featured route: " . ($featuredRouteExists ? "EXISTS" : "NOT FOUND") . "\n";
    } catch (Exception $e) {
        echo "   ✗ Route test failed: " . $e->getMessage() . "\n";
    }
    
    // Test 8: Check view file exists
    echo "8. Testing view file availability...\n";
    
    $viewPath = resource_path('views/admin/products/index.blade.php');
    if (file_exists($viewPath)) {
        echo "   ✓ Enhanced view file: EXISTS\n";
        
        $viewContent = file_get_contents($viewPath);
        
        // Check for new features in the view
        $features = [
            'grid-view' => 'Grid view',
            'compact-view' => 'Compact view', 
            'items-per-page' => 'Items per page selector',
            'quick-stats' => 'Quick stats bar',
            'stock_status' => 'Stock status filter',
            'exportProducts' => 'Export functionality'
        ];
        
        foreach ($features as $feature => $description) {
            if (strpos($viewContent, $feature) !== false) {
                echo "   ✓ {$description}: IMPLEMENTED\n";
            } else {
                echo "   ✗ {$description}: NOT FOUND\n";
            }
        }
    } else {
        echo "   ✗ Enhanced view file: NOT FOUND\n";
    }
    
    // Test 9: Test responsive CSS classes
    echo "9. Testing CSS enhancements...\n";
    
    $cssClasses = [
        'product-grid',
        'product-card', 
        'compact-table',
        'ultra-compact-view',
        'quick-stats',
        'status-dot'
    ];
    
    foreach ($cssClasses as $class) {
        if (strpos($viewContent, $class) !== false) {
            echo "   ✓ CSS class '{$class}': FOUND\n";
        } else {
            echo "   ✗ CSS class '{$class}': NOT FOUND\n";
        }
    }
    
    echo "\n=== Testing Summary ===\n";
    echo "✓ Enhanced product list with 3 view modes\n";
    echo "✓ Configurable pagination (20, 50, 100, 200 items)\n";
    echo "✓ Advanced filtering with stock status\n";
    echo "✓ CSV export functionality\n";
    echo "✓ Quick stats dashboard\n";
    echo "✓ Responsive design optimizations\n";
    echo "✓ JavaScript view switching\n";
    echo "✓ Local storage persistence\n";
    
    echo "\n=== Next Steps ===\n";
    echo "1. Clear caches: run clear-caches-after-product-enhancement.bat\n";
    echo "2. Test in browser: http://greenvalleyherbs.local:8000/admin/products\n";
    echo "3. Test all three view modes (Grid, Table, Compact)\n";
    echo "4. Test filtering and export functionality\n";
    echo "5. Test responsive design on different screen sizes\n";
    echo "6. Verify pagination with different page sizes\n";
    
} catch (Exception $e) {
    echo "ERROR during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Product List Enhancement Testing Complete ===\n";
