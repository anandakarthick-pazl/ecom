<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnhancedFileUploadService;
use App\Traits\DynamicStorage; // Use existing trait instead
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ValidateEnhancedUpload extends Command
{
    use DynamicStorage; // Use existing trait
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:validate {--fix : Fix any issues found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate the upload system installation and configuration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Validating Upload System...');
        $this->newLine();
        
        $issues = [];
        $fixes = [];
        
        // Check if directories exist
        $this->info('📁 Checking directory structure...');
        $requiredDirs = [
            'storage/app/public/products',
            'storage/app/public/categories', 
            'storage/app/public/banners',
            'storage/app/public/invoices'
        ];
        
        foreach ($requiredDirs as $dir) {
            $fullPath = base_path($dir);
            if (!File::exists($fullPath)) {
                $issues[] = "Missing directory: {$dir}";
                if ($this->option('fix')) {
                    File::makeDirectory($fullPath, 0755, true);
                    $fixes[] = "Created directory: {$dir}";
                    $this->info("  ✅ Created: {$dir}");
                } else {
                    $this->warn("  ❌ Missing: {$dir}");
                }
            } else {
                $this->info("  ✅ Exists: {$dir}");
            }
        }
        
        $this->newLine();
        
        // Check storage link
        $this->info('🔗 Checking storage symlink...');
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        if (is_link($linkPath)) {
            $linkTarget = readlink($linkPath);
            $this->info("  ✅ Symlink exists: {$linkPath} -> {$linkTarget}");
            
            if (realpath($linkTarget) === realpath($targetPath)) {
                $this->info("  ✅ Symlink points to correct location");
            } else {
                $issues[] = "Symlink points to wrong location";
                $this->warn("  ⚠️ Symlink points to wrong location");
                $this->warn("    Expected: {$targetPath}");
                $this->warn("    Actual: {$linkTarget}");
                
                if ($this->option('fix')) {
                    unlink($linkPath);
                    symlink($targetPath, $linkPath);
                    $fixes[] = "Fixed symlink target";
                    $this->info("  ✅ Fixed symlink");
                }
            }
        } else {
            $issues[] = "Storage symlink missing";
            $this->error("  ❌ Storage symlink missing!");
            
            if ($this->option('fix')) {
                symlink($targetPath, $linkPath);
                $fixes[] = "Created storage symlink";
                $this->info("  ✅ Created symlink");
            } else {
                $this->info("  💡 Run: php artisan storage:link");
            }
        }
        
        $this->newLine();
        
        // Check file permissions
        $this->info('🔐 Checking file permissions...');
        $storagePublicPath = storage_path('app/public');
        if (is_writable($storagePublicPath)) {
            $this->info('  ✅ Storage directory is writable');
        } else {
            $issues[] = 'Storage directory is not writable';
            $this->warn('  ❌ Storage directory is not writable');
            if ($this->option('fix')) {
                chmod($storagePublicPath, 0755);
                $fixes[] = 'Fixed storage directory permissions';
                $this->info('  ✅ Fixed permissions');
            }
        }
        
        $this->newLine();
        
        // Check PHP extensions
        $this->info('🔧 Checking PHP extensions...');
        $requiredExtensions = ['gd', 'fileinfo', 'mbstring'];
        
        foreach ($requiredExtensions as $ext) {
            if (extension_loaded($ext)) {
                $this->info("  ✅ {$ext} extension loaded");
            } else {
                $issues[] = "Missing PHP extension: {$ext}";
                $this->warn("  ❌ Missing: {$ext} extension");
            }
        }
        
        $this->newLine();
        
        // Check storage space
        $this->info('💾 Checking storage space...');
        $freeSpace = disk_free_space(storage_path('app/public'));
        $freeSpaceGB = round($freeSpace / (1024 * 1024 * 1024), 2);
        
        if ($freeSpaceGB > 1) {
            $this->info("  ✅ Available space: {$freeSpaceGB} GB");
        } else {
            $issues[] = "Low disk space: {$freeSpaceGB} GB";
            $this->warn("  ⚠️ Low space: {$freeSpaceGB} GB");
        }
        
        $this->newLine();
        
        // Check upload limits
        $this->info('📊 Checking PHP upload limits...');
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');
        $memoryLimit = ini_get('memory_limit');
        
        $this->info("  📄 upload_max_filesize: {$uploadMaxFilesize}");
        $this->info("  📮 post_max_size: {$postMaxSize}");
        $this->info("  🧠 memory_limit: {$memoryLimit}");
        
        $this->newLine();
        
        // Check recent uploads
        $this->info('📸 Checking recent uploads...');
        $this->checkRecentUploads();
        
        $this->newLine();
        
        // Summary
        if (empty($issues)) {
            $this->info('🎉 All checks passed! Upload system is ready to use.');
            $this->newLine();
            $this->info('Next steps:');
            $this->info('1. Test file uploads in your application');
            $this->info('2. Monitor logs for any issues');
            return 0;
        } else {
            $this->error('❌ Issues found:');
            foreach ($issues as $issue) {
                $this->error('  • ' . $issue);
            }
            
            if (!empty($fixes)) {
                $this->newLine();
                $this->info('✅ Fixes applied:');
                foreach ($fixes as $fix) {
                    $this->info('  • ' . $fix);
                }
            }
            
            if (!$this->option('fix')) {
                $this->newLine();
                $this->info('💡 Run with --fix option to automatically fix some issues:');
                $this->info('php artisan upload:validate --fix');
            }
            
            return 1;
        }
    }
    
    private function checkRecentUploads()
    {
        try {
            // Check categories
            $categories = DB::table('categories')
                ->whereNotNull('image')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get(['id', 'name', 'image']);
            
            if ($categories->count() > 0) {
                $this->info('  Recent category uploads:');
                foreach ($categories as $category) {
                    $imagePath = $category->image;
                    $cleanPath = str_replace('public/', '', $imagePath);
                    $fullPath = storage_path('app/public/' . $cleanPath);
                    $exists = file_exists($fullPath);
                    
                    $status = $exists ? '✅' : '❌';
                    $this->line("    {$status} {$category->name}: {$imagePath}");
                    
                    if (!$exists) {
                        $this->warn("      File missing: {$fullPath}");
                    }
                }
            } else {
                $this->info('  No category uploads found');
            }
            
        } catch (\Exception $e) {
            $this->warn('  Could not check recent uploads: ' . $e->getMessage());
        }
    }
}
