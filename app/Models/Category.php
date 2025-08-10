<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenantEnhanced;
use App\Traits\DynamicStorageUrl;

class Category extends Model
{
    use HasFactory, BelongsToTenantEnhanced, DynamicStorageUrl;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'sort_order', 'company_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    public function getActiveProductsCountAttribute()
    {
        return $this->activeProducts()->count();
    }

    public function getProductsCountAttribute()
    {
        // If products_count is already set (from controller), use it
        if (isset($this->attributes['products_count'])) {
            return $this->attributes['products_count'];
        }
        
        // Otherwise calculate it
        return $this->activeProducts()->count();
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
     * Get image URL with fallback
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrlWithFallback($this->image, 'categories');
    }
}
