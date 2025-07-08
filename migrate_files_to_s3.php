<?php

/*
|--------------------------------------------------------------------------
| File Migration Script - Local to S3
|--------------------------------------------------------------------------
|
| This script helps migrate existing local files to S3 storage and updates
| the database paths accordingly. Run this after switching to S3 storage
| to migrate your existing images.
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Category; 
use App\Models\Banner;
use App\Services\StorageManagementService;
use Illuminate\Support\Facades\Storage;

try {
    echo "=== File Migration: Local to S3 ===\n\n";
    
    // Check current storage setting
    $currentStorageType = \DB::table('app_settings')
        ->where('key', 'primary_storage_type')
        ->whereNull('company_id')
        ->value('value') ?? 'local';
    
    echo "Current Storage Type: {$currentStorageType}\n";
    
    if ($currentStorageType !== 's3') {
        echo "❌ Storage type is not set to S3. Please set it to S3 first in super admin settings.\n";
        exit(1);
    }
    
    $storageService = app(StorageManagementService::class);
    $migratedFiles = [];
    $errors = [];
    
    echo "\n🔄 Starting migration process...\n\n";
    
    // Migrate Product Images
    echo "=== Migrating Product Images ===\n";
    $products = Product::whereNotNull('featured_image')->get();
    
    foreach ($products as $product) {
        echo "Processing Product: {$product->name}\n";
        
        // Migrate featured image
        if ($product->featured_image && !str_contains($product->featured_image, 'http')) {
            $localPath = $product->featured_image;
            $cleanPath = str_replace('public/', '', $localPath);
            
            if (Storage::disk('public')->exists($cleanPath)) {
                try {
                    // Read file from local storage
                    $fileContent = Storage::disk('public')->get($cleanPath);
                    
                    // Upload to S3
                    $s3Path = 'products/' . basename($cleanPath);
                    Storage::disk('s3')->put($s3Path, $fileContent);
                    
                    // Update database
                    $product->update(['featured_image' => $s3Path]);
                    
                    echo "  ✅ Migrated featured image: {$localPath} → {$s3Path}\n";
                    $migratedFiles[] = $s3Path;
                } catch (Exception $e) {
                    echo "  ❌ Failed to migrate {$localPath}: " . $e->getMessage() . "\n";
                    $errors[] = "Product {$product->id}: {$e->getMessage()}";
                }
            }
        }
        
        // Migrate gallery images
        if ($product->images && is_array($product->images)) {
            $newImages = [];
            foreach ($product->images as $imagePath) {
                if (!str_contains($imagePath, 'http')) {
                    $cleanPath = str_replace('public/', '', $imagePath);
                    
                    if (Storage::disk('public')->exists($cleanPath)) {
                        try {
                            $fileContent = Storage::disk('public')->get($cleanPath);
                            $s3Path = 'products/' . basename($cleanPath);
                            Storage::disk('s3')->put($s3Path, $fileContent);
                            $newImages[] = $s3Path;
                            
                            echo "  ✅ Migrated gallery image: {$imagePath} → {$s3Path}\n";
                            $migratedFiles[] = $s3Path;
                        } catch (Exception $e) {
                            echo "  ❌ Failed to migrate {$imagePath}: " . $e->getMessage() . "\n";
                            $errors[] = "Product {$product->id} gallery: {$e->getMessage()}";
                            $newImages[] = $imagePath; // Keep original if migration fails
                        }
                    } else {
                        $newImages[] = $imagePath; // Keep original if file doesn't exist
                    }
                } else {
                    $newImages[] = $imagePath; // Already S3 URL
                }
            }
            
            if (!empty($newImages)) {
                $product->update(['images' => $newImages]);
            }
        }
        
        echo "\n";
    }
    
    // Migrate Category Images
    echo "=== Migrating Category Images ===\n";
    $categories = Category::whereNotNull('image')->get();
    
    foreach ($categories as $category) {
        echo "Processing Category: {$category->name}\n";
        
        if ($category->image && !str_contains($category->image, 'http')) {
            $localPath = $category->image;
            $cleanPath = str_replace('public/', '', $localPath);
            
            if (Storage::disk('public')->exists($cleanPath)) {
                try {
                    $fileContent = Storage::disk('public')->get($cleanPath);
                    $s3Path = 'categories/' . basename($cleanPath);
                    Storage::disk('s3')->put($s3Path, $fileContent);
                    
                    $category->update(['image' => $s3Path]);
                    
                    echo "  ✅ Migrated: {$localPath} → {$s3Path}\n";
                    $migratedFiles[] = $s3Path;
                } catch (Exception $e) {
                    echo "  ❌ Failed to migrate {$localPath}: " . $e->getMessage() . "\n";
                    $errors[] = "Category {$category->id}: {$e->getMessage()}";
                }
            }
        }
        echo "\n";
    }
    
    // Migrate Banner Images
    echo "=== Migrating Banner Images ===\n";
    $banners = Banner::whereNotNull('image')->get();
    
    foreach ($banners as $banner) {
        echo "Processing Banner: {$banner->title}\n";
        
        if ($banner->image && !str_contains($banner->image, 'http')) {
            $localPath = $banner->image;
            $cleanPath = str_replace('public/', '', $localPath);
            
            if (Storage::disk('public')->exists($cleanPath)) {
                try {
                    $fileContent = Storage::disk('public')->get($cleanPath);
                    $s3Path = 'banners/' . basename($cleanPath);
                    Storage::disk('s3')->put($s3Path, $fileContent);
                    
                    $banner->update(['image' => $s3Path]);
                    
                    echo "  ✅ Migrated: {$localPath} → {$s3Path}\n";
                    $migratedFiles[] = $s3Path;
                } catch (Exception $e) {
                    echo "  ❌ Failed to migrate {$localPath}: " . $e->getMessage() . "\n";
                    $errors[] = "Banner {$banner->id}: {$e->getMessage()}";
                }
            }
        }
        echo "\n";
    }
    
    // Summary
    echo "=== Migration Summary ===\n";
    echo "✅ Successfully migrated: " . count($migratedFiles) . " files\n";
    echo "❌ Errors encountered: " . count($errors) . " files\n\n";
    
    if (!empty($errors)) {
        echo "❌ Error Details:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
        echo "\n";
    }
    
    echo "🎉 Migration completed!\n";
    echo "📋 All migrated files are now stored in S3\n";
    echo "🔗 Database paths have been updated to point to S3\n";
    echo "✨ Your admin panel should now show S3 URLs for all images\n\n";
    
    echo "📝 Next Steps:\n";
    echo "1. Check your S3 bucket to verify files were uploaded\n";
    echo "2. Test admin panel - images should load from S3\n";
    echo "3. Consider cleaning up local files after verification\n";
    echo "4. All new uploads will automatically go to S3\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration Failed: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
