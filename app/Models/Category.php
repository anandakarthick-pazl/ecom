<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenantEnhanced;

class Category extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

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
}
