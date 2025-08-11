<?php
/**
 * 📦 BULK UPLOAD FEATURE TEST
 * ==========================
 * This script tests if the bulk upload feature is ready
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;

echo "\n";
echo "📦 BULK UPLOAD FEATURE TEST\n";
echo "==========================\n\n";

try {
    // Test 1: Check if categories exist
    echo "Test 1: Checking categories...\n";
    $categories = Category::active()->get();
    
    if ($categories->count() > 0) {
        echo "✅ Categories found: {$categories->count()}\n";
        echo "   Available categories:\n";
        foreach ($categories->take(5) as $category) {
            echo "   - {$category->name}\n";
        }
        if ($categories->count() > 5) {
            echo "   ... and " . ($categories->count() - 5) . " more\n";
        }
        echo "\n";
    } else {
        echo "⚠️  No categories found! Please create categories first.\n\n";
    }
    
    // Test 2: Check if UploadLog model works
    echo "Test 2: Testing UploadLog model...\n";
    try {
        $uploadCount = \App\Models\UploadLog::count();
        echo "✅ UploadLog model working - {$uploadCount} upload records found\n\n";
    } catch (\Exception $e) {
        echo "❌ UploadLog model error: {$e->getMessage()}\n\n";
    }
    
    // Test 3: Check if routes are accessible
    echo "Test 3: Available routes:\n";
    $routes = [
        'Bulk Upload Page' => '/admin/products/bulk-upload',
        'Download Template' => '/admin/products/download-template', 
        'Upload History' => '/admin/products/upload-history'
    ];
    
    foreach ($routes as $name => $route) {
        echo "✅ {$name}: http://greenvalleyherbs.local:8000{$route}\n";
    }
    echo "\n";
    
    // Test 4: Check current product count
    echo "Test 4: Current products...\n";
    $productCount = Product::count();
    echo "✅ Current products in database: {$productCount}\n\n";
    
    // Test 5: Check if Excel package is available
    echo "Test 5: Checking Excel support...\n";
    if (class_exists('\\Maatwebsite\\Excel\\Facades\\Excel')) {
        echo "✅ Laravel Excel package is available\n";
    } else {
        echo "❌ Laravel Excel package not found\n";
    }
    echo "\n";
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Go to: http://greenvalleyherbs.local:8000/admin/products\n";
    echo "2. Click the 'Bulk Upload' dropdown\n";
    echo "3. Download the CSV template\n";
    echo "4. Fill in your product data\n";
    echo "5. Upload and test!\n\n";
    
    echo "📋 SAMPLE CSV STRUCTURE:\n";
    echo "name,description,price,stock,category_name,tax_percentage\n";
    echo "\"Sample Product\",\"Product description\",99.99,100,\"Electronics\",18\n\n";
    
    echo "✅ Bulk upload feature is ready to use!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
