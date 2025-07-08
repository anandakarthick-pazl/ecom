<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\UploadedFile;

class DynamicStorageService
{
    /**
     * Get the current storage type from super admin settings
     */
    public function getCurrentStorageType()
    {
        // Cache the storage type for 60 minutes to avoid repeated config reads
        return Cache::remember('current_storage_type', 3600, function () {
            return config('app.storage_type', 'local');
        });
    }

    /**
     * Get the appropriate storage disk based on current settings
     */
    public function getCurrentDisk()
    {
        $storageType = $this->getCurrentStorageType();
        
        if ($storageType === 's3') {
            return Storage::disk('s3');
        }
        
        return Storage::disk('public');
    }

    /**
     * Store a file using the current storage configuration
     */
    public function store(UploadedFile $file, $directory = '', $name = null)
    {
        $storageType = $this->getCurrentStorageType();
        $fileName = $name ?: time() . '_' . $file->getClientOriginalName();
        
        if ($storageType === 's3') {
            // Store in S3
            $path = $file->storeAs($directory, $fileName, 's3');
            $url = $this->generateS3Url($path);
        } else {
            // Store locally
            $path = $file->storeAs($directory, $fileName, 'public');
            $url = Storage::disk('public')->url($path);
        }

        return [
            'path' => $path,
            'url' => $url,
            'storage_type' => $storageType,
            'file_name' => $fileName
        ];
    }

    /**
     * Get file URL for the current storage type
     */
    public function url($path)
    {
        if (empty($path)) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $storageType = $this->getCurrentStorageType();
        
        if ($storageType === 's3') {
            return $this->generateS3Url($path);
        }
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Delete a file from current storage
     */
    public function delete($path)
    {
        $storageType = $this->getCurrentStorageType();
        
        if ($storageType === 's3') {
            return Storage::disk('s3')->delete($path);
        }
        
        return Storage::disk('public')->delete($path);
    }

    /**
     * Check if file exists in current storage
     */
    public function exists($path)
    {
        $storageType = $this->getCurrentStorageType();
        
        if ($storageType === 's3') {
            return Storage::disk('s3')->exists($path);
        }
        
        return Storage::disk('public')->exists($path);
    }

    /**
     * Generate proper S3 URL
     */
    private function generateS3Url($path)
    {
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        $customUrl = config('filesystems.disks.s3.url');
        
        // Use custom URL if provided
        if ($customUrl) {
            return rtrim($customUrl, '/') . '/' . ltrim($path, '/');
        }
        
        // Generate standard S3 URL
        $region = $region ?: 'us-east-1';
        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/{$path}";
        } else {
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
        }
    }

    /**
     * Clear storage type cache (call this when storage type changes)
     */
    public function clearCache()
    {
        Cache::forget('current_storage_type');
    }

    /**
     * Update storage type and clear cache
     */
    public function updateStorageType($storageType)
    {
        // Update the environment file
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        $pattern = "/^STORAGE_TYPE=.*/m";
        $replacement = "STORAGE_TYPE={$storageType}";
        
        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, $replacement, $envContent);
        } else {
            $envContent .= "\nSTORAGE_TYPE={$storageType}";
        }
        
        file_put_contents($envPath, $envContent);
        
        // Clear config cache and storage cache
        \Artisan::call('config:clear');
        $this->clearCache();
        
        return true;
    }
}
