<?php

namespace App\Traits;

use App\Services\StorageManagementService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait DynamicStorageUrlFixed
{
    /**
     * Get file URL based on current storage configuration
     */
    public function getFileUrl($filePath, $storageType = null)
    {
        if (empty($filePath)) {
            return null;
        }

        // Get current storage type if not specified
        if (!$storageType) {
            $storageType = $this->getCurrentStorageTypeFromDatabase();
        }

        // Generate URL based on storage type
        if ($storageType === 's3') {
            return $this->getS3Url($filePath);
        } else {
            return $this->getLocalUrl($filePath);
        }
    }

    /**
     * Get current storage type from database
     */
    private function getCurrentStorageTypeFromDatabase()
    {
        try {
            // Check for super admin global setting (company_id = null for global settings)
            $globalSetting = \DB::table('app_settings')
                ->where('key', 'primary_storage_type')
                ->whereNull('company_id')
                ->value('value');

            if ($globalSetting) {
                return $globalSetting;
            }

            // Fallback to env config if no database setting
            return config('app.storage_type', env('STORAGE_TYPE', 'local'));
        } catch (\Exception $e) {
            // Fallback to local if database query fails
            return 'local';
        }
    }

    /**
     * Get S3 storage URL
     */
    private function getS3Url($filePath)
    {
        try {
            // Check if path already contains full URL
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                return $filePath;
            }
            
            // Generate S3 URL using the storage service
            $storageService = app(StorageManagementService::class);
            return $storageService->getFileUrl($filePath, 's3');
        } catch (\Exception $e) {
            Log::warning('S3 URL generation failed, falling back to local: ' . $e->getMessage());
            return $this->getLocalUrl($filePath);
        }
    }

    /**
     * Get local storage URL - FIXED VERSION
     */
    private function getLocalUrl($filePath)
    {
        // Check if path already contains full URL
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        // Remove 'public/' prefix if present for URL generation
        $cleanPath = str_replace('public/', '', $filePath);
        
        // Remove leading slash if present
        $cleanPath = ltrim($cleanPath, '/');
        
        // Generate local URL
        $url = asset('storage/' . $cleanPath);
        
        // Debug logging (remove in production)
        Log::debug('URL Generation Debug', [
            'original_path' => $filePath,
            'clean_path' => $cleanPath,
            'generated_url' => $url,
            'file_exists' => $this->checkLocalFileExists($cleanPath)
        ]);
        
        return $url;
    }

    /**
     * Check if local file exists
     */
    private function checkLocalFileExists($cleanPath)
    {
        try {
            // Check in public disk
            if (Storage::disk('public')->exists($cleanPath)) {
                return true;
            }
            
            // Also check direct file system path
            $fullPath = storage_path('app/public/' . $cleanPath);
            return file_exists($fullPath);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if file exists in current storage - IMPROVED VERSION
     */
    public function fileExists($filePath, $storageType = null)
    {
        if (empty($filePath)) {
            return false;
        }

        if (!$storageType) {
            $storageType = $this->getCurrentStorageTypeFromDatabase();
        }

        try {
            if ($storageType === 's3') {
                return Storage::disk('s3')->exists($filePath);
            } else {
                // For local storage, remove 'public/' prefix if present
                $cleanPath = str_replace('public/', '', $filePath);
                $cleanPath = ltrim($cleanPath, '/');
                
                // Check both ways to be sure
                if (Storage::disk('public')->exists($cleanPath)) {
                    return true;
                }
                
                // Direct filesystem check
                $fullPath = storage_path('app/public/' . $cleanPath);
                return file_exists($fullPath);
            }
        } catch (\Exception $e) {
            Log::warning('File exists check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSize($filePath, $storageType = null)
    {
        if (empty($filePath)) {
            return null;
        }

        if (!$storageType) {
            $storageType = $this->getCurrentStorageTypeFromDatabase();
        }

        try {
            if ($storageType === 's3') {
                $size = Storage::disk('s3')->size($filePath);
            } else {
                $cleanPath = str_replace('public/', '', $filePath);
                $cleanPath = ltrim($cleanPath, '/');
                
                if (Storage::disk('public')->exists($cleanPath)) {
                    $size = Storage::disk('public')->size($cleanPath);
                } else {
                    $fullPath = storage_path('app/public/' . $cleanPath);
                    $size = file_exists($fullPath) ? filesize($fullPath) : 0;
                }
            }

            return $this->formatBytes($size);
        } catch (\Exception $e) {
            return null;
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

    /**
     * Get multiple file URLs at once
     */
    public function getMultipleFileUrls(array $filePaths, $storageType = null)
    {
        $urls = [];
        
        foreach ($filePaths as $key => $path) {
            $urls[$key] = $this->getFileUrl($path, $storageType);
        }

        return $urls;
    }

    /**
     * Get fallback image URL
     */
    public function getFallbackImageUrl($category = 'general')
    {
        $fallbackImages = [
            'products' => '/images/fallback/product-placeholder.jpg',
            'banners' => '/images/fallback/banner-placeholder.jpg',
            'categories' => '/images/fallback/category-placeholder.jpg',
            'general' => '/images/fallback/default-placeholder.jpg'
        ];

        return asset($fallbackImages[$category] ?? $fallbackImages['general']);
    }

    /**
     * Get image URL with fallback - IMPROVED VERSION
     */
    public function getImageUrlWithFallback($filePath, $category = 'general', $storageType = null)
    {
        // If no file path, return fallback immediately
        if (empty($filePath)) {
            return $this->getFallbackImageUrl($category);
        }

        // Generate the URL
        $url = $this->getFileUrl($filePath, $storageType);
        
        // Check if file actually exists
        if ($url && $this->fileExists($filePath, $storageType)) {
            return $url;
        }

        // Log missing file for debugging
        Log::info('File not found, using fallback', [
            'file_path' => $filePath,
            'category' => $category,
            'storage_type' => $storageType,
            'generated_url' => $url
        ]);

        return $this->getFallbackImageUrl($category);
    }

    /**
     * Get optimized image URL with optional transformations
     */
    public function getOptimizedImageUrl($filePath, $width = null, $height = null, $quality = 85, $storageType = null)
    {
        $baseUrl = $this->getFileUrl($filePath, $storageType);
        
        if (!$baseUrl) {
            return null;
        }

        // For now, return base URL
        // In the future, this could be enhanced with image optimization services
        // like Cloudinary, ImageKit, or AWS Lambda for image resizing
        
        return $baseUrl;
    }

    /**
     * Get direct file URL (bypasses existence check)
     */
    public function getDirectFileUrl($filePath, $storageType = null)
    {
        if (empty($filePath)) {
            return null;
        }

        return $this->getFileUrl($filePath, $storageType);
    }

    /**
     * Debug file path information
     */
    public function debugFilePath($filePath)
    {
        $storageType = $this->getCurrentStorageTypeFromDatabase();
        $cleanPath = str_replace('public/', '', $filePath);
        $cleanPath = ltrim($cleanPath, '/');
        $fullPath = storage_path('app/public/' . $cleanPath);
        
        return [
            'original_path' => $filePath,
            'clean_path' => $cleanPath,
            'full_filesystem_path' => $fullPath,
            'storage_type' => $storageType,
            'file_exists_storage' => Storage::disk('public')->exists($cleanPath),
            'file_exists_filesystem' => file_exists($fullPath),
            'generated_url' => $this->getFileUrl($filePath, $storageType),
            'storage_url' => Storage::disk('public')->url($cleanPath)
        ];
    }
}
