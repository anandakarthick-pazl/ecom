<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;
use App\Traits\DynamicStorageUrl;
use Illuminate\Support\Facades\Storage;

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
     * Get image URL using Laravel Storage - FIXED VERSION
     * This method handles both new (correct) and legacy (incorrect) file locations
     */
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return $this->getFallbackImageUrl();
        }
        
        $filename = basename($this->image);
        $storagePath = 'banner/banners/' . $filename;
        
        // First, check if file exists in the correct Laravel storage location
        if (Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->url($storagePath);
        }
        
        // Fallback: check legacy location (for existing files)
        $legacyPath = public_path('storage/public/banner/banners/' . $filename);
        if (file_exists($legacyPath)) {
            return asset('storage/public/banner/banners/' . $filename);
        }
        
        // Additional fallback locations
        $fallbackPaths = [
            'storage/banners/' . $filename,
            'storage/public/banners/' . $filename,
            'storage/banner/' . $filename
        ];
        
        foreach ($fallbackPaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }
        
        // If no file found, return placeholder
        return $this->getFallbackImageUrl();
    }
    
    /**
     * Get fallback image URL for missing banners
     */
    private function getFallbackImageUrl()
    {
        // Check if placeholder exists
        $placeholderPath = 'images/fallback/banner-placeholder.png';
        if (file_exists(public_path($placeholderPath))) {
            return asset($placeholderPath);
        }
        
        // Return a simple placeholder if no image exists
        return 'data:image/svg+xml;base64,' . base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200">
                <rect width="300" height="200" fill="#f8f9fa"/>
                <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="14" fill="#6c757d" text-anchor="middle" dy=".3em">Banner Image</text>
            </svg>'
        );
    }
    
    /**
     * Clean the image path to remove redundant folders - ENHANCED
     */
    public function cleanImagePath($imagePath)
    {
        if (!$imagePath) {
            return null;
        }
        
        $cleanPath = $imagePath;
        
        // Extract just the filename if full path is provided
        if (strpos($cleanPath, '/') !== false || strpos($cleanPath, '\\') !== false) {
            $cleanPath = basename($cleanPath);
        }
        
        return $cleanPath;
    }
    
    /**
     * Check if file exists in any storage location
     */
    public function fileExists()
    {
        if (!$this->image) {
            return false;
        }
        
        $filename = basename($this->image);
        $storagePath = 'banner/banners/' . $filename;
        
        // Check Laravel storage
        if (Storage::disk('public')->exists($storagePath)) {
            return true;
        }
        
        // Check legacy locations
        $legacyPaths = [
            public_path('storage/public/banner/banners/' . $filename),
            public_path('storage/banners/' . $filename),
            public_path('storage/public/banners/' . $filename),
        ];
        
        foreach ($legacyPaths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get file size in human readable format
     */
    public function getFileSizeAttribute()
    {
        if (!$this->fileExists()) {
            return 'File not found';
        }
        
        $filename = basename($this->image);
        $storagePath = 'banner/banners/' . $filename;
        
        if (Storage::disk('public')->exists($storagePath)) {
            $bytes = Storage::disk('public')->size($storagePath);
        } else {
            // Check legacy locations
            $legacyPath = public_path('storage/public/banner/banners/' . $filename);
            if (file_exists($legacyPath)) {
                $bytes = filesize($legacyPath);
            } else {
                return 'Unknown';
            }
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
