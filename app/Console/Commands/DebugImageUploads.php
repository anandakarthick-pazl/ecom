<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DebugImageUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:images {--category-id= : Debug specific category ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug image upload issues and file paths';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Debugging Image Upload Issues...');
        $this->newLine();

        // Check storage link
        $this->checkStorageLink();
        
        // Check directory structure
        $this->checkDirectories();
        
        // Check specific category if provided
        if ($categoryId = $this->option('category-id')) {
            $this->debugCategory($categoryId);
        } else {
            $this->debugRecentCategories();
        }

        $this->newLine();
        $this->info('✅ Debug complete. Check the information above.');
        
        return 0;
    }

    private function checkStorageLink()
    {
        $this->info('📁 Checking storage symlink...');
        
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        if (is_link($linkPath)) {
            $linkTarget = readlink($linkPath);
            $this->info("  ✅ Symlink exists: {$linkPath} -> {$linkTarget}");
            
            if (realpath($linkTarget) === realpath($targetPath)) {
                $this->info("  ✅ Symlink points to correct location");
            } else {
                $this->warn("  ⚠️ Symlink points to wrong location");
                $this->warn("    Expected: {$targetPath}");
                $this->warn("    Actual: {$linkTarget}");
            }
        } else {
            $this->error("  ❌ Storage symlink missing!");
            $this->info("  💡 Run: php artisan storage:link");
        }
        
        $this->newLine();
    }

    private function checkDirectories()
    {
        $this->info('📂 Checking directory structure...');
        
        $directories = [
            'storage/app/public',
            'storage/app/public/categories',
            'storage/app/public/categories/thumbs',
            'storage/app/public/products',
            'storage/app/public/banners'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = base_path($dir);
            
            if (File::exists($fullPath)) {
                $permissions = substr(sprintf('%o', fileperms($fullPath)), -4);
                $this->info("  ✅ {$dir} (permissions: {$permissions})");
                
                // Check if writable
                if (!is_writable($fullPath)) {
                    $this->warn("    ⚠️ Directory not writable!");
                }
            } else {
                $this->error("  ❌ {$dir} - Missing!");
            }
        }
        
        $this->newLine();
    }

    private function debugCategory($categoryId)
    {
        $this->info("🔍 Debugging Category ID: {$categoryId}");
        
        $category = Category::find($categoryId);
        
        if (!$category) {
            $this->error("Category not found!");
            return;
        }
        
        $this->table(['Field', 'Value'], [
            ['ID', $category->id],
            ['Name', $category->name],
            ['Image Path', $category->image ?: 'NULL'],
            ['Image Thumb', $category->image_thumb ?: 'NULL'],
            ['Created At', $category->created_at],
        ]);
        
        if ($category->image) {
            $this->debugImagePath($category->image, 'Main Image');
        }
        
        if ($category->image_thumb) {
            $this->debugImagePath($category->image_thumb, 'Thumbnail');
        }
        
        $this->newLine();
    }

    private function debugRecentCategories()
    {
        $this->info('🔍 Debugging recent categories...');
        
        $categories = Category::latest()->take(5)->get();
        
        if ($categories->isEmpty()) {
            $this->warn('No categories found.');
            return;
        }
        
        foreach ($categories as $category) {
            $this->info("Category: {$category->name} (ID: {$category->id})");
            
            if ($category->image) {
                $this->debugImagePath($category->image, '  Image');
            } else {
                $this->warn('  No image path stored');
            }
            
            $this->newLine();
        }
    }

    private function debugImagePath($imagePath, $label = 'Image')
    {
        $this->info("{$label} Debug:");
        $this->line("  Path stored in DB: {$imagePath}");
        
        // Clean the path for checking
        $cleanPath = str_replace('public/', '', $imagePath);
        $cleanPath = ltrim($cleanPath, '/');
        
        $this->line("  Clean path: {$cleanPath}");
        
        // Check in storage/app/public directly
        $fullPath = storage_path('app/public/' . $cleanPath);
        $this->line("  Full filesystem path: {$fullPath}");
        $this->line("  File exists (filesystem): " . (file_exists($fullPath) ? '✅ YES' : '❌ NO'));
        
        if (file_exists($fullPath)) {
            $fileSize = filesize($fullPath);
            $this->line("  File size: " . $this->formatBytes($fileSize));
            $this->line("  File permissions: " . substr(sprintf('%o', fileperms($fullPath)), -4));
        }
        
        // Check via Storage facade
        try {
            $existsViaStorage = Storage::disk('public')->exists($cleanPath);
            $this->line("  File exists (Storage): " . ($existsViaStorage ? '✅ YES' : '❌ NO'));
            
            if ($existsViaStorage) {
                $storageUrl = Storage::disk('public')->url($cleanPath);
                $this->line("  Storage URL: {$storageUrl}");
            }
        } catch (\Exception $e) {
            $this->error("  Storage check failed: " . $e->getMessage());
        }
        
        // Generate expected URL
        $expectedUrl = asset('storage/' . $cleanPath);
        $this->line("  Expected URL: {$expectedUrl}");
        
        // Test URL accessibility
        $this->testUrlAccessibility($expectedUrl);
    }

    private function testUrlAccessibility($url)
    {
        // Simple check if running locally
        if (str_contains($url, 'localhost') || str_contains($url, '.local')) {
            $this->line("  URL accessibility: (Local URL - check manually)");
        } else {
            $this->line("  URL: {$url}");
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
