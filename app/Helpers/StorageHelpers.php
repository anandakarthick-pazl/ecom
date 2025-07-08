<?php

if (!function_exists('generate_s3_url')) {
    /**
     * Generate proper S3 URL
     *
     * @param string $key
     * @param string $bucket
     * @param string $region
     * @param string|null $customUrl
     * @return string
     */
    function generate_s3_url($key, $bucket, $region, $customUrl = null)
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
}

if (!function_exists('storage_url')) {
    /**
     * Get file URL based on current storage configuration
     *
     * @param string|null $path
     * @param string|null $storageType
     * @return string|null
     */
    function storage_url($path, $storageType = null)
    {
        if (empty($path)) {
            return null;
        }

        // Get current storage type if not specified
        if (!$storageType) {
            $storageType = config('app.storage_type', 'local');
        }

        // Generate URL based on storage type
        if ($storageType === 's3') {
            try {
                // Check if path already contains full URL
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }
                
                // Generate proper S3 URL
                $bucket = config('filesystems.disks.s3.bucket');
                $region = config('filesystems.disks.s3.region');
                $customUrl = config('filesystems.disks.s3.url');
                
                return generate_s3_url($path, $bucket, $region, $customUrl);
            } catch (\Exception $e) {
                // Fallback to local if S3 fails
                return storage_url_local($path);
            }
        } else {
            return storage_url_local($path);
        }
    }
}

if (!function_exists('storage_url_local')) {
    /**
     * Get local storage URL
     *
     * @param string $path
     * @return string
     */
    function storage_url_local($path)
    {
        // Check if path already contains full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Remove 'public/' prefix if present
        $cleanPath = str_replace('public/', '', $path);
        
        // Generate local URL
        return asset('storage/' . $cleanPath);
    }
}

if (!function_exists('image_url')) {
    /**
     * Get image URL with fallback
     *
     * @param string|null $path
     * @param string $category
     * @param string|null $storageType
     * @return string
     */
    function image_url($path, $category = 'general', $storageType = null)
    {
        if (empty($path)) {
            return fallback_image_url($category);
        }

        $url = storage_url($path, $storageType);
        
        if ($url && file_exists_in_storage($path, $storageType)) {
            return $url;
        }

        return fallback_image_url($category);
    }
}

if (!function_exists('fallback_image_url')) {
    /**
     * Get fallback image URL
     *
     * @param string $category
     * @return string
     */
    function fallback_image_url($category = 'general')
    {
        $fallbackImages = [
            'products' => '/images/fallback/product-placeholder.png',
            'banners' => '/images/fallback/banner-placeholder.png',
            'categories' => '/images/fallback/category-placeholder.png',
            'general' => '/images/fallback/default-placeholder.png'
        ];

        return asset($fallbackImages[$category] ?? $fallbackImages['general']);
    }
}

if (!function_exists('file_exists_in_storage')) {
    /**
     * Check if file exists in current storage
     *
     * @param string $path
     * @param string|null $storageType
     * @return bool
     */
    function file_exists_in_storage($path, $storageType = null)
    {
        if (empty($path)) {
            return false;
        }

        if (!$storageType) {
            $storageType = config('app.storage_type', 'local');
        }

        try {
            if ($storageType === 's3') {
                return \Illuminate\Support\Facades\Storage::disk('s3')->exists($path);
            } else {
                // For local storage, remove 'public/' prefix if present
                $cleanPath = str_replace('public/', '', $path);
                return \Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath);
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('upload_to_storage')) {
    /**
     * Upload file to current storage
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $category
     * @param string|null $directory
     * @param string|null $storageType
     * @return array|null
     */
    function upload_to_storage($file, $category = 'general', $directory = null, $storageType = null)
    {
        if (!$storageType) {
            $storageType = config('app.storage_type', 'local');
        }

        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $category . '/' . ($directory ? $directory . '/' : '') . $fileName;

            if ($storageType === 's3') {
                $filePath = \Illuminate\Support\Facades\Storage::disk('s3')->putFileAs($category . '/' . $directory, $file, $fileName);
                $url = \Illuminate\Support\Facades\Storage::disk('s3')->url($filePath);
            } else {
                $filePath = $file->storeAs('public/' . $category . '/' . $directory, $fileName);
                $url = \Illuminate\Support\Facades\Storage::url($filePath);
            }

            // Store file record in database
            \Illuminate\Support\Facades\DB::table('storage_files')->insertOrIgnore([
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File upload failed: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('delete_from_storage')) {
    /**
     * Delete file from storage
     *
     * @param string $filePath
     * @param string|null $storageType
     * @return bool
     */
    function delete_from_storage($filePath, $storageType = null)
    {
        if (!$storageType) {
            $storageType = config('app.storage_type', 'local');
        }

        try {
            if ($storageType === 's3') {
                \Illuminate\Support\Facades\Storage::disk('s3')->delete($filePath);
            } else {
                \Illuminate\Support\Facades\Storage::delete($filePath);
            }

            // Remove from database
            \Illuminate\Support\Facades\DB::table('storage_files')->where('file_path', $filePath)->delete();

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function format_file_size($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

if (!function_exists('get_storage_type')) {
    /**
     * Get current storage type
     *
     * @return string
     */
    function get_storage_type()
    {
        return config('app.storage_type', 'local');
    }
}

if (!function_exists('is_s3_enabled')) {
    /**
     * Check if S3 storage is enabled and configured
     *
     * @return bool
     */
    function is_s3_enabled()
    {
        return !empty(config('filesystems.disks.s3.key')) && 
               !empty(config('filesystems.disks.s3.secret')) && 
               !empty(config('filesystems.disks.s3.bucket'));
    }
}
