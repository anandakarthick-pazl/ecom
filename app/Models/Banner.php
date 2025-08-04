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
     * Get image URL with specific format - FIXED VERSION
     * Format: http://domain/storage/public/banner/banners/filename.jpeg
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return $this->getFallbackImageUrl('banners');
        }
        
        // Extract just the filename from the stored path
        $filename = basename($this->image);
        
        // Check if file exists in the expected location
        $publicPath = public_path('storage/public/banner/banners/' . $filename);
        
        if (file_exists($publicPath)) {
            // File exists in public/storage/public/banner/banners/
            return asset('storage/public/banner/banners/' . $filename);
        }
        
        // Fallback: try other possible locations
        $alternatePaths = [
            'storage/banners/' . $filename,
            'storage/public/banners/' . $filename,
            'storage/banner/' . $filename
        ];
        
        foreach ($alternatePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }
        
        // If file doesn't exist anywhere, return the original path for debugging
        return asset('storage/public/banner/banners/' . $filename);
    }
    
    /**
     * Clean the image path to remove redundant folders
     */
    public function cleanImagePath($imagePath)
    {
        if (!$imagePath) {
            return null;
        }
        
        // Remove multiple path variations that might be wrong
        $cleanPath = $imagePath;
        
        // Remove 'public/' prefix if present
        $cleanPath = str_replace('public/', '', $cleanPath);
        
        // Remove duplicate 'banners/' if present (like 'banners/banners/')
        $cleanPath = preg_replace('/banners\/banners\//', 'banners/', $cleanPath);
        
        // Remove 'banner/' prefix if it exists (should be 'banners/')
        $cleanPath = str_replace('banner/banners/', 'banners/', $cleanPath);
        
        // Ensure it starts with 'banners/' if it doesn't already
        if (strpos($cleanPath, 'banners/') !== 0) {
            $filename = basename($cleanPath);
            $cleanPath = 'banners/' . $filename;
        }
        
        return $cleanPath;
    }
    
    /**
     * Check if file exists in storage
     */
    private function fileExistsInStorage($cleanPath)
    {
        if (!$cleanPath) {
            return false;
        }
        
        try {
            // Check if file exists in the public storage disk
            return \Storage::disk('public')->exists($cleanPath);
        } catch (\Exception $e) {
            // If storage check fails, check physical file
            $fullPath = storage_path('app/public/' . $cleanPath);
            return file_exists($fullPath);
        }
    }
}
