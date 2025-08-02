<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class UploadLog extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'file_name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'storage_type',
        'upload_type', // 'product', 'category', 'banner'
        'source_id', // ID of the product/category/banner
        'source_type', // Model class name
        'uploaded_by',
        'meta_data',
        'company_id'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'file_size' => 'integer'
    ];

    /**
     * Get the user who uploaded the file
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the source model (polymorphic)
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Get logs for products
     */
    public function scopeProducts($query)
    {
        return $query->where('upload_type', 'product');
    }

    /**
     * Get logs for categories
     */
    public function scopeCategories($query)
    {
        return $query->where('upload_type', 'category');
    }

    /**
     * Get logs for banners
     */
    public function scopeBanners($query)
    {
        return $query->where('upload_type', 'banner');
    }

    /**
     * Format file size for display
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file URL
     */
    public function getUrlAttribute()
    {
        if ($this->storage_type === 's3') {
            return \Storage::disk('s3')->url($this->file_path);
        }
        
        return asset('storage/' . $this->file_path);
    }
}
