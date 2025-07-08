<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;
use App\Traits\DynamicStorageUrl;

class Banner extends Model
{
    use HasFactory, BelongsToTenantEnhanced, DynamicStorageUrl;

    protected $fillable = [
        'title', 'image', 'link_url', 'position', 'is_active',
        'sort_order', 'start_date', 'end_date', 'alt_text', 'company_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeCurrent($query)
    {
        $today = today();
        return $query->where(function ($q) use ($today) {
            $q->where(function ($q2) use ($today) {
                $q2->whereNull('start_date')
                   ->whereNull('end_date');
            })->orWhere(function ($q2) use ($today) {
                $q2->where('start_date', '<=', $today)
                   ->where('end_date', '>=', $today);
            })->orWhere(function ($q2) use ($today) {
                $q2->whereNull('start_date')
                   ->where('end_date', '>=', $today);
            })->orWhere(function ($q2) use ($today) {
                $q2->where('start_date', '<=', $today)
                   ->whereNull('end_date');
            });
        });
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = today();
        
        if ($this->start_date && $this->start_date > $today) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < $today) {
            return false;
        }
        
        return true;
    }

    /**
     * Get image URL with fallback
     */
    public function getImageUrlAttribute()
    {
        return $this->getImageUrlWithFallback($this->image, 'banners');
    }
}
