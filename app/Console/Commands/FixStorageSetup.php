<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FixStorageSetup extends Command
{
    protected $signature = 'storage:fix-setup {--force : Force recreation of symlinks}';
    protected $description = 'Fix storage setup for production deployment';

    public function handle()
    {
        $this->info('🔧 Fixing storage setup for production...');
        
        // 1. Check and create storage symlink
        $this->createStorageSymlink();
        
        // 2. Ensure required directories exist
        $this->createRequiredDirectories();
        
        // 3. Set proper permissions
        $this->setProperPermissions();
        
        // 4. Test file access
        $this->testFileAccess();
        
        $this->info('✅ Storage setup completed successfully!');
        
        // 5. Display helpful information
        $this->displayStorageInfo();
    }

    protected function createStorageSymlink()
    {
        $this->info('📁 Creating storage symlink...');
        
        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');
        
        // Remove existing symlink if force option is used
        if ($this->option('force') && (is_link($publicPath) || is_dir($publicPath))) {
            if (is_link($publicPath)) {
                unlink($publicPath);
                $this->info('🗑️  Removed existing symlink');
            } elseif (is_dir($publicPath)) {
                File::deleteDirectory($publicPath);
                $this->info('🗑️  Removed existing directory');
            }
        }
        
        // Create symlink if it doesn't exist
        if (!file_exists($publicPath)) {
            try {
                // For Windows, use junction
                if (PHP_OS_FAMILY === 'Windows') {
                    $cmd = 'mklink /J "' . $publicPath . '" "' . $storagePath . '"';
                    exec($cmd, $output, $returnVar);
                    
                    if ($returnVar === 0) {
                        $this->info('✅ Storage symlink created successfully (Windows junction)');
                    } else {
                        $this->error('❌ Failed to create symlink on Windows');
                        $this->line('Command: ' . $cmd);
                        $this->line('Output: ' . implode("\n", $output));
                    }
                } else {
                    // For Unix-like systems
                    symlink($storagePath, $publicPath);
                    $this->info('✅ Storage symlink created successfully');
                }
            } catch (\Exception $e) {
                $this->error('❌ Failed to create storage symlink: ' . $e->getMessage());
                
                // Alternative: Copy files instead of symlink
                $this->warn('⚠️  Attempting to copy files instead of symlink...');
                if (File::copyDirectory($storagePath, $publicPath)) {
                    $this->info('✅ Files copied successfully as fallback');
                } else {
                    $this->error('❌ Failed to copy files as well');
                }
            }
        } else {
            $this->info('ℹ️  Storage symlink already exists');
        }
    }

    protected function createRequiredDirectories()
    {
        $this->info('📂 Creating required directories...');
        
        $directories = [
            'storage/app/public',
            'storage/app/public/whatsapp-bills',
            'storage/app/public/invoices',
            'storage/app/public/receipts',
            'storage/app/public/uploads',
            'storage/app/temp',
            'storage/app/temp/bills',
            'storage/logs',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'public/storage',
        ];
        
        foreach ($directories as $directory) {
            $fullPath = base_path($directory);
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
                $this->info("✅ Created directory: {$directory}");
            } else {
                $this->line("ℹ️  Directory already exists: {$directory}");
            }
        }
    }

    protected function setProperPermissions()
    {
        $this->info('🔐 Setting proper permissions...');
        
        if (PHP_OS_FAMILY !== 'Windows') {
            $directories = [
                storage_path(),
                storage_path('app'),
                storage_path('app/public'),
                storage_path('logs'),
                storage_path('framework'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
                public_path('storage'),
            ];
            
            foreach ($directories as $directory) {
                if (File::exists($directory)) {
                    chmod($directory, 0755);
                    $this->line("✅ Set permissions for: " . basename($directory));
                }
            }
        } else {
            $this->info('ℹ️  Skipping permission setting on Windows');
        }
    }

    protected function testFileAccess()
    {
        $this->info('🧪 Testing file access...');
        
        try {
            // Create a test file
            $testContent = 'Test file for storage access - ' . date('Y-m-d H:i:s');
            $testPath = 'test-access.txt';
            
            Storage::disk('public')->put($testPath, $testContent);
            
            // Check if file is accessible via URL
            $baseUrl = config('app.url');
            $testUrl = $baseUrl . '/storage/' . $testPath;
            
            $this->info("✅ Test file created: {$testPath}");
            $this->info("🌐 Test URL: {$testUrl}");
            
            // Try to access the file via HTTP
            $headers = @get_headers($testUrl);
            if ($headers && strpos($headers[0], '200') !== false) {
                $this->info('✅ File is publicly accessible via URL');
            } else {
                $this->warn('⚠️  File may not be accessible via URL - check web server configuration');
            }
            
            // Clean up test file
            Storage::disk('public')->delete($testPath);
            $this->info('🗑️  Test file cleaned up');
            
        } catch (\Exception $e) {
            $this->error('❌ File access test failed: ' . $e->getMessage());
        }
    }

    protected function displayStorageInfo()
    {
        $this->info('');
        $this->info('=== Storage Configuration Info ===');
        $this->line('APP_URL: ' . config('app.url'));
        $this->line('Storage Path: ' . storage_path('app/public'));
        $this->line('Public Path: ' . public_path('storage'));
        $this->line('Public URL: ' . asset('storage'));
        
        $this->info('');
        $this->info('=== Next Steps ===');
        $this->line('1. Ensure your web server serves files from public/storage');
        $this->line('2. Check that .htaccess or nginx config allows access to storage files');
        $this->line('3. Test WhatsApp bill sending: /admin/orders/{id} -> Send Bill via WhatsApp');
        $this->line('4. If issues persist, check storage/logs/laravel.log for errors');
        
        $this->info('');
        $this->info('=== Troubleshooting Commands ===');
        $this->line('• Clear config cache: php artisan config:clear');
        $this->line('• Clear all caches: php artisan optimize:clear');
        $this->line('• Re-run this command: php artisan storage:fix-setup --force');
    }
}
