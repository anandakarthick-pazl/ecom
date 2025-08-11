<?php

namespace App\Traits;

use App\Services\StorageManagementService;
use Illuminate\Support\Facades\Storage;

trait DynamicStorageUrl
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
            // Get from database setting instead of config file
            $storageType = $this->getCurrentStorageTypeFromDatabase();
        }

        // Generate URL based on storage type
        if ($storageType === 's3') {
            // For S3 storage
            try {
                // Check if path already contains full URL
                if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                    return $filePath;
                }
                
                // Generate S3 URL using the storage service
                $storageService = app(StorageManagementService::class);
                return $storageService->getFileUrl($filePath, 's3');
            } catch (\Exception $e) {
                // Fallback to local if S3 fails
                return $this->getLocalUrl($filePath);
            }
        } else {
            // For local storage
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
     * Get local storage URL
     */
    private function getLocalUrl($filePath)
    {
        // Check if path already contains full URL
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        // Remove 'public/' prefix if present
        $cleanPath = str_replace('public/', '', $filePath);
        
        // Generate local URL
        return asset('storage/' . $cleanPath);
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
     * Check if file exists in current storage
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
                return Storage::disk('public')->exists($cleanPath);
            }
        } catch (\Exception $e) {
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
                $size = Storage::disk('public')->size($cleanPath);
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
            'products' => '/images/fallback/product-placeholder.png',
            'banners' => '/images/fallback/banner-placeholder.png',
            'categories' => '/images/fallback/category-placeholder.png',
            'general' => '/images/fallback/default-placeholder.png'
        ];

        return asset($fallbackImages[$category] ?? $fallbackImages['general']);
    }

    /**
     * Get image URL with fallback
     */
    public function getImageUrlWithFallback($filePath, $category = 'general', $storageType = null)
    {
        $url = $this->getFileUrl($filePath, $storageType);
        
        if ($url && $this->fileExists($filePath, $storageType)) {
            return $url;
        }

        return $this->getFallbackImageUrl($category);
    }
}
