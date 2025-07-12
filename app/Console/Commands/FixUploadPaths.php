<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class FixUploadPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:upload-paths {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix incorrect upload paths and move files to correct locations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN - No changes will be made');
        } else {
            $this->info('🔧 FIXING upload paths and moving files...');
        }
        
        $this->newLine();
        
        $this->fixCategories($dryRun);
        $this->fixProducts($dryRun);
        $this->fixBanners($dryRun);
        $this->cleanupEmptyDirectories($dryRun);
        
        $this->newLine();
        $this->info('✅ Done!');
        
        if ($dryRun) {
            $this->info('💡 Run without --dry-run to actually make the changes');
        }
        
        return 0;
    }

    private function fixCategories($dryRun)
    {
        $this->info('📁 Fixing Category images...');
        
        $categories = Category::whereNotNull('image')->get();
        $fixed = 0;
        $moved = 0;
        
        foreach ($categories as $category) {
            $currentPath = $category->image;
            
            // Check if path has wrong structure (public/categories/categories/ or similar)
            if (str_contains($currentPath, 'public/categories/categories') || 
                str_contains($currentPath, 'categories/categories')) {
                
                $this->line("  Category: {$category->name}");
                $this->line("    Current path: {$currentPath}");
                
                // Try to find the actual file
                $possiblePaths = [
                    storage_path('app/public/public/categories/categories/' . basename($currentPath)),
                    storage_path('app/public/categories/categories/' . basename($currentPath)),
                    storage_path('app/public/' . $currentPath),
                ];
                
                $sourceFile = null;
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $sourceFile = $path;
                        break;
                    }
                }
                
                if ($sourceFile) {
                    $newFilename = time() . '_' . basename($sourceFile);
                    $targetPath = storage_path('app/public/categories/' . $newFilename);
                    $newDbPath = 'categories/' . $newFilename;
                    
                    $this->line("    Found file: {$sourceFile}");
                    $this->line("    New path: {$newDbPath}");
                    
                    if (!$dryRun) {
                        // Ensure target directory exists
                        $targetDir = dirname($targetPath);
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        
                        // Move file
                        if (copy($sourceFile, $targetPath)) {
                            // Update database
                            $category->update(['image' => $newDbPath]);
                            
                            // Delete old file
                            unlink($sourceFile);
                            
                            $moved++;
                            $this->line("    ✅ Moved and updated");
                        } else {
                            $this->error("    ❌ Failed to move file");
                        }
                    } else {
                        $this->line("    Would move to: {$newDbPath}");
                    }
                } else {
                    // File not found, just fix the database path
                    $cleanPath = 'categories/' . basename($currentPath);
                    $this->line("    File not found, would update DB path to: {$cleanPath}");
                    
                    if (!$dryRun) {
                        $category->update(['image' => $cleanPath]);
                    }
                }
                
                $fixed++;
                $this->newLine();
            }
        }
        
        $this->info("  Categories processed: {$fixed}, Files moved: {$moved}");
        $this->newLine();
    }

    private function fixProducts($dryRun)
    {
        $this->info('📦 Fixing Product images...');
        
        $products = Product::where(function($query) {
            $query->whereNotNull('featured_image')
                  ->orWhereNotNull('images');
        })->get();
        
        $fixed = 0;
        $moved = 0;
        
        foreach ($products as $product) {
            $needsUpdate = false;
            $newData = [];
            
            // Fix featured image
            if ($product->featured_image && 
                (str_contains($product->featured_image, 'public/products/products') ||
                 str_contains($product->featured_image, 'products/products'))) {
                
                $this->line("  Product: {$product->name} (Featured Image)");
                $currentPath = $product->featured_image;
                
                $sourceFile = $this->findProductFile($currentPath);
                if ($sourceFile) {
                    $newFilename = time() . '_featured_' . basename($sourceFile);
                    $newDbPath = 'products/' . $newFilename;
                    
                    if (!$dryRun) {
                        $targetPath = storage_path('app/public/products/' . $newFilename);
                        $this->ensureDirectoryExists(dirname($targetPath));
                        
                        if (copy($sourceFile, $targetPath)) {
                            $newData['featured_image'] = $newDbPath;
                            unlink($sourceFile);
                            $moved++;
                        }
                    } else {
                        $this->line("    Would move featured image to: {$newDbPath}");
                    }
                }
                $needsUpdate = true;
            }
            
            // Fix additional images
            if ($product->images && is_array($product->images)) {
                $newImages = [];
                
                foreach ($product->images as $index => $imagePath) {
                    if (str_contains($imagePath, 'public/products/products') ||
                        str_contains($imagePath, 'products/products')) {
                        
                        $sourceFile = $this->findProductFile($imagePath);
                        if ($sourceFile) {
                            $newFilename = time() . '_img' . $index . '_' . basename($sourceFile);
                            $newDbPath = 'products/' . $newFilename;
                            
                            if (!$dryRun) {
                                $targetPath = storage_path('app/public/products/' . $newFilename);
                                $this->ensureDirectoryExists(dirname($targetPath));
                                
                                if (copy($sourceFile, $targetPath)) {
                                    $newImages[] = $newDbPath;
                                    unlink($sourceFile);
                                    $moved++;
                                }
                            } else {
                                $newImages[] = $newDbPath;
                                $this->line("    Would move image {$index} to: {$newDbPath}");
                            }
                        } else {
                            $newImages[] = 'products/' . basename($imagePath);
                        }
                        $needsUpdate = true;
                    } else {
                        $newImages[] = $imagePath;
                    }
                }
                
                if ($needsUpdate) {
                    $newData['images'] = $newImages;
                }
            }
            
            if ($needsUpdate) {
                if (!$dryRun && !empty($newData)) {
                    $product->update($newData);
                }
                $fixed++;
            }
        }
        
        $this->info("  Products processed: {$fixed}, Files moved: {$moved}");
        $this->newLine();
    }

    private function fixBanners($dryRun)
    {
        $this->info('🎯 Fixing Banner images...');
        
        $banners = Banner::whereNotNull('image')->get();
        $fixed = 0;
        $moved = 0;
        
        foreach ($banners as $banner) {
            $currentPath = $banner->image;
            
            if (str_contains($currentPath, 'public/banners/banners') || 
                str_contains($currentPath, 'banners/banners')) {
                
                $this->line("  Banner: {$banner->title}");
                
                $sourceFile = $this->findBannerFile($currentPath);
                if ($sourceFile) {
                    $newFilename = time() . '_banner_' . basename($sourceFile);
                    $newDbPath = 'banners/' . $newFilename;
                    
                    if (!$dryRun) {
                        $targetPath = storage_path('app/public/banners/' . $newFilename);
                        $this->ensureDirectoryExists(dirname($targetPath));
                        
                        if (copy($sourceFile, $targetPath)) {
                            $banner->update(['image' => $newDbPath]);
                            unlink($sourceFile);
                            $moved++;
                        }
                    } else {
                        $this->line("    Would move to: {$newDbPath}");
                    }
                }
                $fixed++;
            }
        }
        
        $this->info("  Banners processed: {$fixed}, Files moved: {$moved}");
        $this->newLine();
    }

    private function findProductFile($dbPath)
    {
        $possiblePaths = [
            storage_path('app/public/public/products/products/' . basename($dbPath)),
            storage_path('app/public/products/products/' . basename($dbPath)),
            storage_path('app/public/' . $dbPath),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    private function findBannerFile($dbPath)
    {
        $possiblePaths = [
            storage_path('app/public/public/banners/banners/' . basename($dbPath)),
            storage_path('app/public/banners/banners/' . basename($dbPath)),
            storage_path('app/public/' . $dbPath),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    private function ensureDirectoryExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function cleanupEmptyDirectories($dryRun)
    {
        $this->info('🧹 Cleaning up empty directories...');
        
        $emptyDirs = [
            storage_path('app/public/public'),
            storage_path('app/public/categories/categories'),
            storage_path('app/public/products/products'),
            storage_path('app/public/banners/banners'),
        ];
        
        foreach ($emptyDirs as $dir) {
            if (is_dir($dir)) {
                $isEmpty = count(glob($dir . '/*')) === 0;
                
                if ($isEmpty) {
                    $this->line("  Empty directory: {$dir}");
                    
                    if (!$dryRun) {
                        rmdir($dir);
                        $this->line("    ✅ Removed");
                    } else {
                        $this->line("    Would remove");
                    }
                } else {
                    $fileCount = count(glob($dir . '/*'));
                    $this->line("  Directory not empty: {$dir} ({$fileCount} files)");
                }
            }
        }
    }
}
