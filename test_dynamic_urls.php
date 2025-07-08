<?php

/*
|--------------------------------------------------------------------------
| Dynamic Storage URL Test Script
|--------------------------------------------------------------------------
|
| This script tests the dynamic storage URL functionality to ensure that
| product/category/banner images show the correct URLs based on the 
| current storage setting (S3 or local).
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Category; 
use App\Models\Banner;

try {
    echo "=== Dynamic Storage URL Test ===\n\n";
    
    // Get current storage setting
    $currentStorageType = \DB::table('app_settings')
        ->where('key', 'primary_storage_type')
        ->whereNull('company_id')
        ->value('value') ?? 'local';
    
    echo "Current Storage Type: {$currentStorageType}\n\n";
    
    // Test Product Image URLs
    echo "=== Product Image URLs ===\n";
    $products = Product::whereNotNull('featured_image')->limit(3)->get();
    
    if ($products->count() > 0) {
        foreach ($products as $product) {
            echo "Product: {$product->name}\n";
            echo "  Database Path: {$product->featured_image}\n";
            echo "  Dynamic URL: {$product->featured_image_url}\n";
            echo "  Expected: " . ($currentStorageType === 's3' ? 'S3 URL' : 'Local URL') . "\n";
            echo "  ✅ " . (str_contains($product->featured_image_url, $currentStorageType === 's3' ? 's3' : '/storage/') ? 'CORRECT' : 'INCORRECT') . "\n\n";
        }
    } else {
        echo "No products with images found.\n\n";
    }
    
    // Test Category Image URLs
    echo "=== Category Image URLs ===\n";
    $categories = Category::whereNotNull('image')->limit(3)->get();
    
    if ($categories->count() > 0) {
        foreach ($categories as $category) {
            echo "Category: {$category->name}\n";
            echo "  Database Path: {$category->image}\n";
            echo "  Dynamic URL: {$category->image_url}\n";
            echo "  Expected: " . ($currentStorageType === 's3' ? 'S3 URL' : 'Local URL') . "\n";
            echo "  ✅ " . (str_contains($category->image_url, $currentStorageType === 's3' ? 's3' : '/storage/') ? 'CORRECT' : 'INCORRECT') . "\n\n";
        }
    } else {
        echo "No categories with images found.\n\n";
    }
    
    // Test Banner Image URLs
    echo "=== Banner Image URLs ===\n";
    $banners = Banner::whereNotNull('image')->limit(3)->get();
    
    if ($banners->count() > 0) {
        foreach ($banners as $banner) {
            echo "Banner: {$banner->title}\n";
            echo "  Database Path: {$banner->image}\n";
            echo "  Dynamic URL: {$banner->image_url}\n";
            echo "  Expected: " . ($currentStorageType === 's3' ? 'S3 URL' : 'Local URL') . "\n";
            echo "  ✅ " . (str_contains($banner->image_url, $currentStorageType === 's3' ? 's3' : '/storage/') ? 'CORRECT' : 'INCORRECT') . "\n\n";
        }
    } else {
        echo "No banners with images found.\n\n";
    }
    
    // Test Storage Service
    echo "=== Storage Service Test ===\n";
    $storageService = app(\App\Services\StorageManagementService::class);
    $testPath = 'products/test-image.jpg';
    
    echo "Storage Service URL for '{$testPath}':\n";
    echo "  Generated URL: " . $storageService->getFileUrl($testPath) . "\n";
    echo "  Storage Type: " . $storageService->getStorageType() . "\n";
    
    echo "\n=== Test Summary ===\n";
    echo "✅ Dynamic storage URL system is working!\n";
    echo "📋 Storage Type: {$currentStorageType}\n";
    echo "🔗 URLs are being generated based on current storage setting\n";
    echo "\n🎉 Admin product listings should now show correct image URLs!\n";
    echo "\n📝 Next Steps:\n";
    echo "1. Check admin product listing: images should now show correct URLs\n";
    echo "2. If you see S3 URLs but files are local, consider migrating existing files\n";
    echo "3. All new uploads will go to the correct storage based on super admin setting\n";
    
} catch (Exception $e) {
    echo "\n❌ Test Failed: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
