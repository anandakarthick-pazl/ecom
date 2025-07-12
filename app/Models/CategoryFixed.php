<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenantEnhanced;
use App\Traits\DynamicStorageUrlFixed; // Updated trait
use Illuminate\Support\Facades\Log;

class Category extends Model
{
    use HasFactory, BelongsToTenantEnhanced, DynamicStorageUrlFixed;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'sort_order', 'company_id', 'image_thumb'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get image URL with enhanced debugging
     */
    public function getImageUrlAttribute()
    {
        // If no image, return fallback
        if (empty($this->image)) {
            return $this->getFallbackImageUrl('categories');
        }

        // Get direct URL first
        $directUrl = $this->getDirectFileUrl($this->image);
        
        // Check if file exists
        if ($this->fileExists($this->image)) {
            return $directUrl;
        }

        // Log debugging information
        Log::info('Category image URL debug', [
            'category_id' => $this->id,
            'category_name' => $this->name,
            'image_path' => $this->image,
            'debug_info' => $this->debugFilePath($this->image)
        ]);

        // Return fallback
        return $this->getFallbackImageUrl('categories');
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if (empty($this->image_thumb)) {
            return $this->getImageUrlAttribute(); // Use main image as fallback
        }

        if ($this->fileExists($this->image_thumb)) {
            return $this->getDirectFileUrl($this->image_thumb);
        }

        return $this->getImageUrlAttribute();
    }

    /**
     * Get multiple image URLs
     */
    public function getImageUrls()
    {
        return [
            'original' => $this->getImageUrlAttribute(),
            'thumbnail' => $this->getThumbnailUrlAttribute(),
        ];
    }

    /**
     * Check if category has image
     */
    public function hasImage()
    {
        return !empty($this->image) && $this->fileExists($this->image);
    }

    /**
     * Get image info for admin
     */
    public function getImageInfo()
    {
        if (empty($this->image)) {
            return null;
        }

        return [
            'path' => $this->image,
            'url' => $this->getDirectFileUrl($this->image),
            'exists' => $this->fileExists($this->image),
            'size' => $this->getFileSize($this->image),
            'debug' => $this->debugFilePath($this->image)
        ];
    }
}
