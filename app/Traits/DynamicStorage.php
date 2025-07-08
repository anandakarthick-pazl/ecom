<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Services\StorageManagementService;

trait DynamicStorage
{
    /**
     * Get the current storage disk based on super admin settings
     */
    protected function getStorageDisk()
    {
        $storageType = $this->getCurrentStorageType();
        return $storageType === 's3' ? 's3' : 'public';
    }

    /**
     * Get current storage type from super admin settings
     */
    protected function getCurrentStorageType()
    {
        // Check for super admin global setting (company_id = null for global settings)
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
     * Store file using dynamic storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $category
     * @return array
     */
    protected function storeFileDynamically($file, $directory = 'general', $category = 'general')
    {
        $storageService = app(StorageManagementService::class);
        return $storageService->uploadFile($file, null, $directory, $category);
    }

    /**
     * Store file as with dynamic storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string $name
     * @return string
     */
    protected function storeFileAsDynamically($file, $path, $name = null)
    {
        $disk = $this->getStorageDisk();
        $fileName = $name ?: time() . '_' . $file->getClientOriginalName();
        
        if ($disk === 's3') {
            // For S3, don't add 'public/' prefix and set public-read ACL
            return $file->storeAs(
                $path, 
                $fileName, 
                [
                    'disk' => 's3',
                    'visibility' => 'public',
                    'ACL' => 'public-read'
                ]
            );
        } else {
            // For local storage, use 'public/' prefix
            return $file->storeAs('public/' . $path, $fileName, 'local');
        }
    }

    /**
     * Delete file from dynamic storage
     * 
     * @param string $filePath
     * @return bool
     */
    protected function deleteFileDynamically($filePath)
    {
        if (!$filePath) {
            return false;
        }

        $storageType = $this->getCurrentStorageType();
        
        if ($storageType === 's3') {
            return \Storage::disk('s3')->delete($filePath);
        } else {
            // Remove 'public/' prefix if present for deletion
            $path = str_replace('public/', '', $filePath);
            return \Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get file URL from dynamic storage
     * 
     * @param string $filePath
     * @return string|null
     */
    protected function getFileUrlDynamically($filePath)
    {
        if (!$filePath) {
            return null;
        }

        $storageService = app(StorageManagementService::class);
        return $storageService->getFileUrl($filePath);
    }
}
