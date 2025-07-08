<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class StorageManagementService
{
    protected $defaultStorageType;

    public function __construct()
    {
        $this->defaultStorageType = config('app.storage_type', 'local');
    }

    /**
     * Get storage configuration
     */
    public function getStorageConfig()
    {
        return [
            'current_storage' => $this->defaultStorageType,
            'local_config' => [
                'path' => storage_path('app/public'),
                'url' => asset('storage'),
                'available' => true
            ],
            's3_config' => [
                'bucket' => config('filesystems.disks.s3.bucket'),
                'region' => config('filesystems.disks.s3.region'),
                'url' => config('filesystems.disks.s3.url'),
                'available' => $this->isS3Available()
            ]
        ];
    }

    /**
     * Check if S3 is available
     */
    private function isS3Available()
    {
        try {
            return !empty(config('filesystems.disks.s3.key')) && 
                   !empty(config('filesystems.disks.s3.secret')) &&
                   class_exists('Aws\S3\S3Client');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update storage configuration
     */
    public function updateStorageConfig($config)
    {
        // Update environment variables
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $updates = [
            'STORAGE_TYPE' => $config['storage_type'],
        ];

        if ($config['storage_type'] === 's3' && $this->isS3Available()) {
            $updates = array_merge($updates, [
                'AWS_ACCESS_KEY_ID' => $config['aws_access_key_id'] ?? '',
                'AWS_SECRET_ACCESS_KEY' => $config['aws_secret_access_key'] ?? '',
                'AWS_DEFAULT_REGION' => $config['aws_default_region'] ?? '',
                'AWS_BUCKET' => $config['aws_bucket'] ?? '',
                'AWS_URL' => $config['aws_url'] ?? '',
            ]);
        }

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envPath, $envContent);

        // Clear config cache
        \Artisan::call('config:clear');

        return true;
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats()
    {
        return [
            'local' => $this->getLocalStorageStats(),
            's3' => $this->getS3StorageStats(),
            'total_files' => $this->getTotalFileCount(),
            'categories' => $this->getFileCountByCategory()
        ];
    }

    /**
     * Get local storage statistics
     */
    public function getLocalStorageStats()
    {
        $path = storage_path('app/public');
        $totalSize = 0;
        $fileCount = 0;

        if (is_dir($path)) {
            try {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $totalSize += $file->getSize();
                        $fileCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error calculating local storage stats: ' . $e->getMessage());
            }
        }

        return [
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'file_count' => $fileCount,
            'available_space' => disk_free_space($path),
            'available_space_formatted' => $this->formatBytes(disk_free_space($path))
        ];
    }

    /**
     * Get S3 storage statistics
     */
    public function getS3StorageStats()
    {
        if (!$this->isS3Available()) {
            return [
                'total_size' => 0,
                'total_size_formatted' => '0 B',
                'file_count' => 0,
                'available' => false
            ];
        }

        try {
            // For now, return placeholder data
            // Will implement actual S3 stats once Flysystem is properly configured
            return [
                'total_size' => 0,
                'total_size_formatted' => '0 B',
                'file_count' => 0,
                'available' => true
            ];
        } catch (\Exception $e) {
            Log::error('S3 stats error: ' . $e->getMessage());
            return [
                'total_size' => 0,
                'total_size_formatted' => '0 B',
                'file_count' => 0,
                'available' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload file to specified storage
     */
    public function uploadFile(UploadedFile $file, $storageType, $directory = null, $category = 'general')
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $category . '/' . ($directory ? $directory . '/' : '') . $fileName;

        if ($storageType === 's3' && $this->isS3Available()) {
            try {
                $filePath = Storage::disk('s3')->putFileAs($category . '/' . $directory, $file, $fileName);
                $url = Storage::disk('s3')->url($filePath);
            } catch (\Exception $e) {
                throw new \Exception('S3 upload failed: ' . $e->getMessage());
            }
        } else {
            $filePath = $file->storeAs('public/' . $category . '/' . $directory, $fileName);
            $url = Storage::url($filePath);
        }

        // Store file record in database
        try {
            DB::table('storage_files')->insertOrIgnore([
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'storage_type' => $storageType,
                'category' => $category,
                'directory' => $directory,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'url' => $url,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to save file record to database: ' . $e->getMessage());
        }

        return [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'url' => $url,
            'storage_type' => $storageType,
            'category' => $category
        ];
    }

    /**
     * Get local files
     */
    public function getLocalFiles($directory = '')
    {
        $path = 'public/' . $directory;
        
        try {
            $files = Storage::disk('local')->allFiles($path);
        } catch (\Exception $e) {
            Log::warning('Error getting local files: ' . $e->getMessage());
            return [];
        }
        
        $fileList = [];
        foreach ($files as $file) {
            $fullPath = storage_path('app/' . $file);
            if (is_file($fullPath)) {
                $fileList[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::url($file),
                    'size' => filesize($fullPath),
                    'size_formatted' => $this->formatBytes(filesize($fullPath)),
                    'modified' => Carbon::createFromTimestamp(filemtime($fullPath)),
                    'type' => pathinfo($file, PATHINFO_EXTENSION)
                ];
            }
        }

        return $fileList;
    }

    /**
     * Get S3 files (placeholder for now)
     */
    public function getS3Files($directory = '')
    {
        if (!$this->isS3Available()) {
            return [];
        }

        // Return empty array for now - will implement when S3 is properly configured
        return [];
    }

    /**
     * Get local directories
     */
    public function getLocalDirectories()
    {
        try {
            $directories = Storage::disk('local')->allDirectories('public');
            return array_map(function($dir) {
                return [
                    'name' => basename($dir),
                    'path' => $dir,
                    'file_count' => count(Storage::disk('local')->allFiles($dir))
                ];
            }, $directories);
        } catch (\Exception $e) {
            Log::warning('Error getting local directories: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get S3 configuration
     */
    public function getS3Config()
    {
        return [
            'bucket' => config('filesystems.disks.s3.bucket'),
            'region' => config('filesystems.disks.s3.region'),
            'url' => config('filesystems.disks.s3.url'),
            'available' => $this->isS3Available()
        ];
    }

    /**
     * Get S3 buckets (placeholder)
     */
    public function getS3Buckets()
    {
        if (!$this->isS3Available()) {
            return [];
        }
        return [];
    }

    /**
     * Test storage connection
     */
    public function testConnection($storageType)
    {
        if ($storageType === 's3') {
            if (!$this->isS3Available()) {
                throw new \Exception('S3 client not configured or Flysystem AWS adapter missing');
            }

            try {
                // Simple test - try to list bucket contents
                Storage::disk('s3')->allFiles('', 1);
                return ['status' => 'success', 'message' => 'S3 connection successful'];
            } catch (\Exception $e) {
                throw new \Exception('S3 connection failed: ' . $e->getMessage());
            }
        } else {
            $path = storage_path('app/public');
            if (!is_dir($path) || !is_writable($path)) {
                throw new \Exception('Local storage path not accessible or writable');
            }
            return ['status' => 'success', 'message' => 'Local storage accessible'];
        }
    }

    /**
     * Get total file count
     */
    private function getTotalFileCount()
    {
        $localCount = count($this->getLocalFiles());
        $s3Count = count($this->getS3Files());
        return $localCount + $s3Count;
    }

    /**
     * Get file count by category
     */
    private function getFileCountByCategory()
    {
        try {
            return DB::table('storage_files')
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === null || $bytes === false) {
            return 'Unknown';
        }
        
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Placeholder methods for compatibility
    public function deleteFile($filePath, $storageType) { return true; }
    public function createDirectory($directoryName, $storageType, $parentDirectory = null) { return ['path' => $directoryName]; }
    public function syncFiles($direction, $category = null) { return ['synced_count' => 0, 'errors' => [], 'error_count' => 0]; }
    public function getFileUrl($filePath, $storageType = null) { return Storage::url($filePath); }
    public function backupStorage($storageType, $backupType = 'full') { return ['backup_path' => '', 'file_count' => 0]; }
    public function cleanupOldFiles($storageType, $daysOld, $dryRun = false) { return ['files_to_delete' => [], 'file_count' => 0]; }
}
