<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'demo_url',
        'category',
        'price',
        'is_free',
        'features',
        'status',
        'settings',
        'color_scheme',
        'layout_type',
        'components',
        'screenshots',
        'tags',
        'difficulty_level',
        'responsive',
        'rtl_support',
        'dark_mode',
        'author',
        'rating',
        'downloads_count'
    ];

    protected $casts = [
        'features' => 'array',
        'settings' => 'array',
        'color_scheme' => 'array',
        'components' => 'array',
        'screenshots' => 'array',
        'tags' => 'array',
        'is_free' => 'boolean',
        'responsive' => 'boolean',
        'rtl_support' => 'boolean',
        'dark_mode' => 'boolean',
        'rating' => 'decimal:1',
        'downloads_count' => 'integer'
    ];

    const CATEGORIES = [
        'fashion' => 'Fashion & Clothing',
        'electronics' => 'Electronics & Gadgets',
        'food' => 'Food & Beverages',
        'beauty' => 'Beauty & Cosmetics',
        'home' => 'Home & Garden',
        'sports' => 'Sports & Fitness',
        'books' => 'Books & Education',
        'jewelry' => 'Jewelry & Accessories',
        'automotive' => 'Automotive & Parts',
        'toys' => 'Toys & Games',
        'health' => 'Health & Medical',
        'art' => 'Art & Crafts',
        'music' => 'Music & Audio',
        'pets' => 'Pets & Animals',
        'travel' => 'Travel & Tourism',
        'flowers' => 'Flowers & Gifts',
        'pharmacy' => 'Pharmacy & Medicine',
        'grocery' => 'Grocery & Supermarket',
        'bakery' => 'Bakery & Sweets',
        'restaurant' => 'Restaurant & Food Service',
        'general' => 'General Store',
        'luxury' => 'Luxury & Premium',
        'minimal' => 'Minimal & Clean',
        'vintage' => 'Vintage & Retro',
        'modern' => 'Modern & Contemporary'
    ];

    const LAYOUT_TYPES = [
        'grid' => 'Grid Layout',
        'list' => 'List Layout',
        'masonry' => 'Masonry Layout',
        'carousel' => 'Carousel Layout',
        'sidebar' => 'Sidebar Layout',
        'fullwidth' => 'Full Width Layout',
        'boxed' => 'Boxed Layout',
        'split' => 'Split Layout'
    ];

    const DIFFICULTY_LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getLayoutTypeNameAttribute()
    {
        return self::LAYOUT_TYPES[$this->layout_type] ?? $this->layout_type;
    }

    public function getDifficultyLevelNameAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? $this->difficulty_level;
    }

    public function getPrimaryColorAttribute()
    {
        return $this->color_scheme['primary'] ?? '#007bff';
    }

    public function getSecondaryColorAttribute()
    {
        return $this->color_scheme['secondary'] ?? '#6c757d';
    }

    public function getAccentColorAttribute()
    {
        return $this->color_scheme['accent'] ?? '#28a745';
    }

    public function getRatingStarsAttribute()
    {
        $rating = $this->rating ?: 0;
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars[] = 'fas fa-star text-warning';
            } elseif ($i - 0.5 <= $rating) {
                $stars[] = 'fas fa-star-half-alt text-warning';
            } else {
                $stars[] = 'far fa-star text-muted';
            }
        }
        return $stars;
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLayout($query, $layout)
    {
        return $query->where('layout_type', $layout);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeResponsive($query)
    {
        return $query->where('responsive', true);
    }

    public function scopeWithDarkMode($query)
    {
        return $query->where('dark_mode', true);
    }

    public function scopeWithRtlSupport($query)
    {
        return $query->where('rtl_support', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('downloads_count', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
