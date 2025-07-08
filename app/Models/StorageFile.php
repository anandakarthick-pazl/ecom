<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DynamicStorageUrl;

class StorageFile extends Model
{
    use HasFactory, DynamicStorageUrl;

    protected $table = 'storage_files';

    protected $fillable = [
        'file_name',
        'original_name',
        'file_path',
        'storage_type',
        'category',
        'directory',
        'file_size',
        'mime_type',
        'url',
        'alt_text',
        'description',
        'metadata',
        'is_active',
        'uploaded_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'file_size' => 'integer'
    ];

    /**
     * Get the URL for this file
     */
    public function getUrlAttribute($value)
    {
        return $this->getFileUrl($this->file_path, $this->storage_type);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        return format_file_size($this->file_size);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $imageTypes);
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute()
    {
        return strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
    }

    /**
     * Scope for active files
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific storage type
     */
    public function scopeStorageType($query, $storageType)
    {
        return $query->where('storage_type', $storageType);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->whereIn('mime_type', [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ]);
    }

    /**
     * Get files by category with statistics
     */
    public static function getCategoryStats()
    {
        return self::selectRaw('category, count(*) as count, sum(file_size) as total_size')
                   ->groupBy('category')
                   ->get()
                   ->mapWithKeys(function ($item) {
                       return [$item->category => [
                           'count' => $item->count,
                           'total_size' => $item->total_size,
                           'formatted_size' => format_file_size($item->total_size)
                       ]];
                   });
    }

    /**
     * Get files by storage type with statistics
     */
    public static function getStorageStats()
    {
        return self::selectRaw('storage_type, count(*) as count, sum(file_size) as total_size')
                   ->groupBy('storage_type')
                   ->get()
                   ->mapWithKeys(function ($item) {
                       return [$item->storage_type => [
                           'count' => $item->count,
                           'total_size' => $item->total_size,
                           'formatted_size' => format_file_size($item->total_size)
                       ]];
                   });
    }

    /**
     * Delete file from storage and database
     */
    public function deleteFromStorage()
    {
        try {
            // Delete physical file
            delete_from_storage($this->file_path, $this->storage_type);
            
            // Delete database record
            $this->delete();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Move file to different storage
     */
    public function moveToStorage($targetStorageType)
    {
        try {
            if ($this->storage_type === $targetStorageType) {
                return true; // Already in target storage
            }

            // Get file content from current storage
            if ($this->storage_type === 's3') {
                $content = \Storage::disk('s3')->get($this->file_path);
            } else {
                $content = \Storage::get($this->file_path);
            }

            // Save to target storage
            if ($targetStorageType === 's3') {
                \Storage::disk('s3')->put($this->file_path, $content);
                $newUrl = \Storage::disk('s3')->url($this->file_path);
            } else {
                \Storage::put($this->file_path, $content);
                $newUrl = \Storage::url($this->file_path);
            }

            // Update database record
            $this->update([
                'storage_type' => $targetStorageType,
                'url' => $newUrl
            ]);

            // Delete from old storage
            if ($this->storage_type === 's3') {
                \Storage::disk('s3')->delete($this->file_path);
            } else {
                \Storage::delete($this->file_path);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to move file to storage: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create thumbnail for image files
     */
    public function createThumbnail($width = 300, $height = 300)
    {
        if (!$this->is_image) {
            return null;
        }

        // This would require an image manipulation library like Intervention Image
        // For now, return the original URL
        return $this->url;
    }
}
