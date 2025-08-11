<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🖼️ Category Image Diagnostic\n";
echo "=============================\n\n";

try {
    // Find a company to use for testing
    $company = \App\Models\SuperAdmin\Company::first();
    
    if (!$company) {
        echo "❌ No companies found. Please create a company first.\n";
        exit(1);
    }
    
    echo "✅ Found company: {$company->name} (ID: {$company->id})\n";
    
    // Set the current tenant context
    app()->instance('current_tenant', $company);
    
    // Get categories
    $categories = \App\Models\Category::currentTenant()->get();
    
    echo "📊 Total categories found: " . $categories->count() . "\n\n";
    
    foreach ($categories as $category) {
        echo "📂 Category: {$category->name}\n";
        echo "   - ID: {$category->id}\n";
        echo "   - Slug: {$category->slug}\n";
        echo "   - Image path in DB: " . ($category->image ?: 'NULL') . "\n";
        
        if ($category->image) {
            $fullImagePath = public_path('storage/' . $category->image);
            $imageExists = file_exists($fullImagePath);
            echo "   - Full path: {$fullImagePath}\n";
            echo "   - File exists: " . ($imageExists ? '✅ YES' : '❌ NO') . "\n";
            echo "   - Generated URL: {$category->image_url}\n";
            
            if ($imageExists) {
                $fileSize = filesize($fullImagePath);
                echo "   - File size: " . number_format($fileSize / 1024, 2) . " KB\n";
            }
        } else {
            echo "   - No image set, should show fallback\n";
            echo "   - Fallback URL: {$category->image_url}\n";
        }
        
        echo "\n";
    }
    
    // Check storage structure
    echo "📁 Storage Structure Check:\n";
    echo "===========================\n";
    
    $publicStoragePath = public_path('storage');
    echo "Public storage path: {$publicStoragePath}\n";
    echo "Public storage exists: " . (is_dir($publicStoragePath) ? '✅ YES' : '❌ NO') . "\n";
    
    $categoriesPath = public_path('storage/categories');
    echo "Categories path: {$categoriesPath}\n";
    echo "Categories directory exists: " . (is_dir($categoriesPath) ? '✅ YES' : '❌ NO') . "\n";
    
    if (is_dir($categoriesPath)) {
        $categoryFiles = array_diff(scandir($categoriesPath), ['.', '..']);
        echo "Category files found: " . count($categoryFiles) . "\n";
        foreach ($categoryFiles as $file) {
            if ($file !== '.gitignore') {
                $filePath = $categoriesPath . '/' . $file;
                $fileSize = filesize($filePath);
                echo "   - {$file} (" . number_format($fileSize / 1024, 2) . " KB)\n";
            }
        }
    }
    
    echo "\n📍 Fallback Images Check:\n";
    echo "==========================\n";
    
    $fallbackPath = public_path('images/fallback');
    echo "Fallback path: {$fallbackPath}\n";
    echo "Fallback directory exists: " . (is_dir($fallbackPath) ? '✅ YES' : '❌ NO') . "\n";
    
    if (is_dir($fallbackPath)) {
        $fallbackFiles = array_diff(scandir($fallbackPath), ['.', '..']);
        echo "Fallback files found: " . count($fallbackFiles) . "\n";
        foreach ($fallbackFiles as $file) {
            echo "   - {$file}\n";
        }
    }
    
    // Test the image URL generation
    echo "\n🧪 URL Generation Test:\n";
    echo "=======================\n";
    
    if ($categories->count() > 0) {
        $testCategory = $categories->first();
        echo "Testing category: {$testCategory->name}\n";
        
        // Test the trait method directly
        $trait = new class {
            use \App\Traits\DynamicStorageUrl;
        };
        
        if ($testCategory->image) {
            $directUrl = $trait->getFileUrl($testCategory->image);
            echo "Direct URL (trait): {$directUrl}\n";
            echo "Model URL (accessor): {$testCategory->image_url}\n";
            
            // Check URL accessibility
            $urlWorks = @file_get_contents($directUrl, false, stream_context_create([
                'http' => ['timeout' => 5]
            ])) !== false;
            echo "URL accessible: " . ($urlWorks ? '✅ YES' : '❌ NO') . "\n";
        }
        
        // Test fallback
        $fallbackUrl = $trait->getFallbackImageUrl('categories');
        echo "Fallback URL: {$fallbackUrl}\n";
        
        $fallbackWorks = @file_get_contents($fallbackUrl, false, stream_context_create([
            'http' => ['timeout' => 5]
        ])) !== false;
        echo "Fallback accessible: " . ($fallbackWorks ? '✅ YES' : '❌ NO') . "\n";
    }
    
    echo "\n✅ Diagnostic complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
