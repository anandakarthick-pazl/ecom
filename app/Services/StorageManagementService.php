<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Aws\S3\S3Client;
use Carbon\Carbon;
use App\Models\AppSetting;

class StorageManagementService
{
    protected $defaultStorageType;
    protected $s3Client;

    public function __construct()
    {
        $this->defaultStorageType = $this->getCurrentStorageType();
        $this->initializeS3Client();
    }

    /**
     * Get current storage type from super admin settings
     */
    private function getCurrentStorageType()
    {
        // First check for super admin global setting (company_id = null for global settings)
        $globalSetting = DB::table('app_settings')
            ->where('key', 'primary_storage_type')
            ->whereNull('company_id')
            ->first();

        if ($globalSetting) {
            return $globalSetting->value;
        }

        // Fallback to env config if no database setting
        return config('app.storage_type', env('STORAGE_TYPE', 'local'));
    }

    /**
     * Get the appropriate storage disk based on current storage type
     */
    public function getStorageDisk()
    {
        $storageType = $this->getCurrentStorageType();
        return $storageType === 's3' ? 's3' : 'public';
    }

    /**
     * Get current storage type (public method for external use)
     */
    public function getStorageType()
    {
        return $this->getCurrentStorageType();
    }

    /**
     * Initialize S3 Client
     */
    private function initializeS3Client()
    {
        try {
            $accessKey = config('filesystems.disks.s3.key');
            $secretKey = config('filesystems.disks.s3.secret');
            $region = config('filesystems.disks.s3.region');
            
            if ($accessKey && $secretKey && $region) {
                $this->s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => $region,
                    'credentials' => [
                        'key' => $accessKey,
                        'secret' => $secretKey,
                    ],
                ]);
                
                Log::info('S3 Client initialized successfully for region: ' . $region);
            } else {
                Log::info('S3 Client not initialized - missing credentials or region');
            }
        } catch (\Exception $e) {
            Log::warning('S3 Client initialization failed (this is normal if credentials are invalid): ' . $e->getMessage());
            $this->s3Client = null;
        }
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
                'available' => !empty(config('filesystems.disks.s3.key'))
            ]
        ];
    }

    /**
     * Update storage configuration
     */
    public function updateStorageConfig($config)
    {
        // Save primary storage type to database as global setting
        DB::table('app_settings')->updateOrInsert(
            [
                'key' => 'primary_storage_type',
                'company_id' => null // Global setting for all tenants
            ],
            [
                'value' => $config['storage_type'],
                'type' => 'string',
                'group' => 'storage',
                'label' => 'Primary Storage Type',
                'description' => 'Default storage type for file uploads (local or s3)',
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        // Update environment variables
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        $updates = [
            'STORAGE_TYPE' => $config['storage_type'],
        ];

        if ($config['storage_type'] === 's3') {
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
        
        // Update default storage type
        $this->defaultStorageType = $config['storage_type'];
        
        // Reinitialize S3 client
        $this->initializeS3Client();

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
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
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
        if (!$this->s3Client) {
            return [
                'total_size' => 0,
                'total_size_formatted' => '0 B',
                'file_count' => 0,
                'available' => false
            ];
        }

        try {
            $bucket = config('filesystems.disks.s3.bucket');
            
            // Skip if no bucket configured
            if (!$bucket) {
                return [
                    'total_size' => 0,
                    'total_size_formatted' => '0 B',
                    'file_count' => 0,
                    'available' => false,
                    'error' => 'No S3 bucket configured'
                ];
            }
            
            $totalSize = 0;
            $fileCount = 0;

            // Try to list objects in the specific bucket only
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $bucket,
                'MaxKeys' => 1000 // Limit to avoid timeout
            ]);

            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    $totalSize += $object['Size'];
                    $fileCount++;
                }
            }

            return [
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'file_count' => $fileCount,
                'available' => true,
                'bucket' => $bucket
            ];
        } catch (\Exception $e) {
            Log::warning('S3 stats error (this is normal if user lacks ListAllMyBuckets permission): ' . $e->getMessage());
            
            // Return minimal info - S3 might still work for uploads even if we can't get stats
            return [
                'total_size' => 0,
                'total_size_formatted' => 'Unable to calculate',
                'file_count' => 0,
                'available' => true, // Assume available for uploads
                'error' => 'Limited permissions - stats unavailable but uploads should work',
                'bucket' => config('filesystems.disks.s3.bucket')
            ];
        }
    }

    /**
     * Upload file to specified storage
     */
    public function uploadFile(UploadedFile $file, $storageType = null, $directory = null, $category = 'general')
    {
        // Use current storage type if not specified
        if (!$storageType) {
            $storageType = $this->getCurrentStorageType();
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $category . '/' . ($directory ? $directory . '/' : '') . $fileName;

        if ($storageType === 's3') {
            // Upload with public-read ACL for public access
            $filePath = Storage::disk('s3')->putFileAs(
                $category . '/' . $directory, 
                $file, 
                $fileName,
                ['visibility' => 'public', 'ACL' => 'public-read']
            );
            
            // Generate proper S3 URL
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $customUrl = config('filesystems.disks.s3.url');
            $url = $this->generateS3Url($filePath, $bucket, $region, $customUrl);
        } else {
            $filePath = $file->storeAs('public/' . $category . '/' . $directory, $fileName);
            $url = Storage::url($filePath);
        }

        // Store file record in database
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

        return [
            'file_name' => $fileName,
            'file_path' => $filePath,
            'url' => $url,
            'storage_type' => $storageType,
            'category' => $category
        ];
    }

    /**
     * Delete file from storage
     */
    public function deleteFile($filePath, $storageType)
    {
        if ($storageType === 's3') {
            Storage::disk('s3')->delete($filePath);
        } else {
            Storage::delete($filePath);
        }

        // Remove from database
        DB::table('storage_files')->where('file_path', $filePath)->delete();

        return true;
    }

    /**
     * Get local files
     */
    public function getLocalFiles($directory = '')
    {
        $path = 'public/' . $directory;
        $files = Storage::disk('local')->allFiles($path);
        
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
     * Get S3 files
     */
    public function getS3Files($directory = '')
    {
        if (!$this->s3Client) {
            return [];
        }

        try {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $customUrl = config('filesystems.disks.s3.url');
            $prefix = $directory ? $directory . '/' : '';

            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $prefix
            ]);

            $fileList = [];
            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    // Generate proper S3 URL
                    $s3Url = $this->generateS3Url($object['Key'], $bucket, $region, $customUrl);
                    
                    $fileList[] = [
                        'name' => basename($object['Key']),
                        'path' => $object['Key'],
                        'url' => $s3Url,
                        'size' => $object['Size'],
                        'size_formatted' => $this->formatBytes($object['Size']),
                        'modified' => Carbon::parse($object['LastModified']),
                        'type' => pathinfo($object['Key'], PATHINFO_EXTENSION)
                    ];
                }
            }

            return $fileList;
        } catch (\Exception $e) {
            Log::error('S3 file listing error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate proper S3 URL
     */
    private function generateS3Url($key, $bucket, $region, $customUrl = null)
    {
        // Use custom URL if provided
        if ($customUrl) {
            return rtrim($customUrl, '/') . '/' . ltrim($key, '/');
        }
        
        // Generate standard S3 URL
        $region = $region ?: 'us-east-1';
        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/{$key}";
        } else {
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$key}";
        }
    }

    /**
     * Get local directories
     */
    public function getLocalDirectories()
    {
        $directories = Storage::disk('local')->allDirectories('public');
        return array_map(function($dir) {
            return [
                'name' => basename($dir),
                'path' => $dir,
                'file_count' => count(Storage::disk('local')->allFiles($dir))
            ];
        }, $directories);
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
            'available' => !empty(config('filesystems.disks.s3.key'))
        ];
    }

    /**
     * Get S3 buckets (with permission handling)
     */
    public function getS3Buckets()
    {
        if (!$this->s3Client) {
            return [];
        }

        try {
            // Check if we have ListAllMyBuckets permission
            $result = $this->s3Client->listBuckets();
            return array_map(function($bucket) {
                return [
                    'name' => $bucket['Name'],
                    'creation_date' => Carbon::parse($bucket['CreationDate'])
                ];
            }, $result['Buckets']);
        } catch (\Exception $e) {
            // If we don't have ListAllMyBuckets permission, return the configured bucket
            Log::info('S3 ListBuckets permission not available, using configured bucket: ' . $e->getMessage());
            
            $configuredBucket = config('filesystems.disks.s3.bucket');
            if ($configuredBucket) {
                return [
                    [
                        'name' => $configuredBucket,
                        'creation_date' => Carbon::now(),
                        'note' => 'Configured bucket (ListBuckets permission not available)'
                    ]
                ];
            }
            
            return [];
        }
    }

    /**
     * Create directory
     */
    public function createDirectory($directoryName, $storageType, $parentDirectory = null)
    {
        $path = $parentDirectory ? $parentDirectory . '/' . $directoryName : $directoryName;

        if ($storageType === 's3') {
            // S3 doesn't have real directories, but we can create a placeholder file
            Storage::disk('s3')->put($path . '/.gitkeep', '');
        } else {
            Storage::disk('local')->makeDirectory('public/' . $path);
        }

        return ['path' => $path];
    }

    /**
     * Test storage connection
     */
    public function testConnection($storageType)
    {
        if ($storageType === 's3') {
            if (!$this->s3Client) {
                throw new \Exception('S3 client not configured');
            }

            try {
                $bucket = config('filesystems.disks.s3.bucket');
                
                if (!$bucket) {
                    throw new \Exception('S3 bucket not configured');
                }
                
                // Try to access the specific bucket instead of listing all buckets
                // This requires less permissions
                $this->s3Client->headBucket(['Bucket' => $bucket]);
                
                return [
                    'status' => 'success', 
                    'message' => "S3 connection successful to bucket: {$bucket}",
                    'bucket' => $bucket
                ];
            } catch (\Exception $e) {
                // If headBucket fails, try a simple operation to test basic connectivity
                try {
                    // Try to list objects with limit 1 - this is a minimal test
                    $this->s3Client->listObjectsV2([
                        'Bucket' => $bucket,
                        'MaxKeys' => 1
                    ]);
                    
                    return [
                        'status' => 'success', 
                        'message' => "S3 connection successful (limited permissions) to bucket: {$bucket}",
                        'bucket' => $bucket,
                        'note' => 'Connection works but user has limited permissions'
                    ];
                } catch (\Exception $e2) {
                    throw new \Exception('S3 connection failed: ' . $e->getMessage());
                }
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
     * Sync files between storages
     */
    public function syncFiles($direction, $category = null)
    {
        $syncedFiles = [];
        $errors = [];

        if ($direction === 'local_to_s3') {
            $files = $this->getLocalFiles($category);
            foreach ($files as $file) {
                try {
                    $content = Storage::disk('local')->get($file['path']);
                    $s3Path = str_replace('public/', '', $file['path']);
                    Storage::disk('s3')->put($s3Path, $content);
                    $syncedFiles[] = $file['name'];
                } catch (\Exception $e) {
                    $errors[] = $file['name'] . ': ' . $e->getMessage();
                }
            }
        } else {
            $files = $this->getS3Files($category);
            foreach ($files as $file) {
                try {
                    $content = Storage::disk('s3')->get($file['path']);
                    $localPath = 'public/' . $file['path'];
                    Storage::disk('local')->put($localPath, $content);
                    $syncedFiles[] = $file['name'];
                } catch (\Exception $e) {
                    $errors[] = $file['name'] . ': ' . $e->getMessage();
                }
            }
        }

        return [
            'synced_files' => $syncedFiles,
            'synced_count' => count($syncedFiles),
            'errors' => $errors,
            'error_count' => count($errors)
        ];
    }

    /**
     * Get file URL based on current storage configuration
     */
    public function getFileUrl($filePath, $storageType = null)
    {
        $storageType = $storageType ?: $this->getCurrentStorageType();

        if ($storageType === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $customUrl = config('filesystems.disks.s3.url');
            
            return $this->generateS3Url($filePath, $bucket, $region, $customUrl);
        } else {
            return Storage::url($filePath);
        }
    }

    /**
     * Backup storage
     */
    public function backupStorage($storageType, $backupType = 'full')
    {
        $backupPath = 'backups/' . $storageType . '/' . date('Y-m-d_H-i-s');
        $backedUpFiles = [];

        if ($storageType === 'local') {
            $files = $this->getLocalFiles();
            foreach ($files as $file) {
                $content = Storage::disk('local')->get($file['path']);
                $backupFilePath = $backupPath . '/' . $file['path'];
                Storage::disk('local')->put($backupFilePath, $content);
                $backedUpFiles[] = $file['name'];
            }
        } else {
            $files = $this->getS3Files();
            foreach ($files as $file) {
                $content = Storage::disk('s3')->get($file['path']);
                $backupFilePath = $backupPath . '/' . $file['path'];
                Storage::disk('local')->put($backupFilePath, $content);
                $backedUpFiles[] = $file['name'];
            }
        }

        return [
            'backup_path' => $backupPath,
            'backed_up_files' => $backedUpFiles,
            'file_count' => count($backedUpFiles),
            'backup_type' => $backupType
        ];
    }

    /**
     * Clean up old files
     */
    public function cleanupOldFiles($storageType, $daysOld, $dryRun = false)
    {
        $cutoffDate = Carbon::now()->subDays($daysOld);
        $filesToDelete = [];

        if ($storageType === 'local') {
            $files = $this->getLocalFiles();
            foreach ($files as $file) {
                if ($file['modified']->lt($cutoffDate)) {
                    $filesToDelete[] = $file;
                    if (!$dryRun) {
                        Storage::disk('local')->delete($file['path']);
                    }
                }
            }
        } else {
            $files = $this->getS3Files();
            foreach ($files as $file) {
                if ($file['modified']->lt($cutoffDate)) {
                    $filesToDelete[] = $file;
                    if (!$dryRun) {
                        Storage::disk('s3')->delete($file['path']);
                    }
                }
            }
        }

        return [
            'files_to_delete' => $filesToDelete,
            'file_count' => count($filesToDelete),
            'dry_run' => $dryRun,
            'cutoff_date' => $cutoffDate
        ];
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
            // If table doesn't exist, return empty array
            return [];
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
