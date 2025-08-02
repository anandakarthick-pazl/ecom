<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Offer extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'name', 'code', 'type', 'discount_type', 'value', 'minimum_amount',
        'category_id', 'product_id', 'start_date', 'end_date',
        'is_active', 'usage_limit', 'used_count', 'company_id',
        'is_flash_offer', 'banner_image', 'banner_title', 'banner_description',
        'banner_button_text', 'banner_button_url', 'show_popup', 'popup_frequency', 'popup_delay',
        'show_countdown', 'countdown_text'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_flash_offer' => 'boolean',
        'show_popup' => 'boolean',
        'show_countdown' => 'boolean',
        'popup_delay' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = today();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    public function scopeFlashOffers($query)
    {
        return $query->where('is_flash_offer', true);
    }

    public function scopeActiveFlashOffers($query)
    {
        return $query->flashOffers()->active()->current();
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = today();
        if ($this->start_date > $today || $this->end_date < $today) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount, $product = null, $category = null)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        // Check if offer applies to specific product
        if ($this->type === 'product') {
            if (!$product || $product->id !== $this->product_id) {
                return 0;
            }
        }

        // Check if offer applies to specific category
        if ($this->type === 'category') {
            if (!$category || $category->id !== $this->category_id) {
                return 0;
            }
        }

        // Calculate discount based on type and discount_type
        if ($this->type === 'percentage' || $this->discount_type === 'percentage') {
            return min($amount * ($this->value / 100), $amount);
        }

        // Fixed/flat amount discount
        return min($this->value, $amount);
    }

    // Helper method to get discount type display name
    public function getDiscountTypeDisplayAttribute()
    {
        if ($this->type === 'percentage') return 'Percentage';
        if ($this->type === 'fixed') return 'Fixed Amount';
        if ($this->type === 'category') return 'Category (' . ucfirst($this->discount_type) . ')';
        if ($this->type === 'product') return 'Product (' . ucfirst($this->discount_type) . ')';
        return ucfirst($this->type);
    }

    // Helper method to get discount value display
    public function getDiscountValueDisplayAttribute()
    {
        if ($this->type === 'percentage' || $this->discount_type === 'percentage') {
            return $this->value . '%';
        }
        return '₹' . number_format($this->value, 2);
    }

    // Flash offer helper methods
    public function getBannerImageUrlAttribute()
    {
        if ($this->banner_image) {
            return asset('storage/' . $this->banner_image);
        }
        return null;
    }

    public function isFlashOfferActive()
    {
        return $this->is_flash_offer && $this->isValid();
    }

    public function getRemainingTimeInSeconds()
    {
        if (!$this->isFlashOfferActive()) {
            return 0;
        }
        
        $now = now();
        $endDate = $this->end_date->endOfDay();
        
        return $endDate->greaterThan($now) ? $endDate->diffInSeconds($now) : 0;
    }

    public function getTimeRemaining()
    {
        $seconds = $this->getRemainingTimeInSeconds();
        
        if ($seconds <= 0) {
            return ['expired' => true];
        }
        
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        return [
            'expired' => false,
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $remainingSeconds,
            'total_seconds' => $seconds
        ];
    }
}
