<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenantEnhanced;
use App\Traits\DynamicStorageUrl;

class Product extends Model
{
    use HasFactory, BelongsToTenantEnhanced, DynamicStorageUrl;

    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'price', 'discount_price',
        'stock', 'sku', 'featured_image', 'images', 'category_id',
        'meta_title', 'meta_description', 'meta_keywords',
        'is_active', 'is_featured', 'sort_order', 'weight', 'weight_unit',
        'cost_price', 'barcode', 'code', 'low_stock_threshold', 'company_id', 'branch_id', 'tax_percentage'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function posSaleItems()
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function stockAdjustmentItems()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?: $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->discount_price < $this->price) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isInStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }

    /**
     * Get the tax amount for the product
     */
    public function getTaxAmount($price = null)
    {
        $basePrice = $price ?: $this->final_price;
        return round(($basePrice * $this->tax_percentage) / 100, 2);
    }

    /**
     * Get CGST amount (Central GST - half of total tax)
     */
    public function getCgstAmount($price = null)
    {
        return round($this->getTaxAmount($price) / 2, 2);
    }

    /**
     * Get SGST amount (State GST - half of total tax)
     */
    public function getSgstAmount($price = null)
    {
        return round($this->getTaxAmount($price) / 2, 2);
    }

    /**
     * Get price including tax
     */
    public function getPriceWithTax($price = null)
    {
        $basePrice = $price ?: $this->final_price;
        return round($basePrice + $this->getTaxAmount($basePrice), 2);
    }

    /**
     * Get featured image URL with fallback
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->getImageUrlWithFallback($this->featured_image, 'products');
    }

    /**
     * Get all product images URLs
     */
    public function getImageUrlsAttribute()
    {
        if (empty($this->images) || !is_array($this->images)) {
            return [];
        }

        return $this->getMultipleFileUrls($this->images);
    }

    /**
     * Get first available image URL
     */
    public function getImageUrlAttribute()
    {
        // Try featured image first
        if ($this->featured_image) {
            return $this->featured_image_url;
        }

        // Try first image from images array
        if (!empty($this->images) && is_array($this->images)) {
            $firstImage = $this->images[0] ?? null;
            if ($firstImage) {
                return $this->getImageUrlWithFallback($firstImage, 'products');
            }
        }

        // Return fallback
        return $this->getFallbackImageUrl('products');
    }

    /**
     * Get optimized image URL for specific size
     */
    public function getOptimizedImageUrl($width = null, $height = null, $quality = 85)
    {
        $imagePath = $this->featured_image ?: ($this->images[0] ?? null);
        
        if (!$imagePath) {
            return $this->getFallbackImageUrl('products');
        }

        return $this->getOptimizedImageUrl($imagePath, $width, $height, $quality);
    }

    /**
     * Add image to product
     */
    public function addImage($imagePath, $isFeatured = false)
    {
        if ($isFeatured) {
            $this->featured_image = $imagePath;
        }

        $images = $this->images ?: [];
        if (!in_array($imagePath, $images)) {
            $images[] = $imagePath;
            $this->images = $images;
        }

        $this->save();
    }

    /**
     * Remove image from product
     */
    public function removeImage($imagePath)
    {
        // Remove from featured image
        if ($this->featured_image === $imagePath) {
            $this->featured_image = null;
        }

        // Remove from images array
        if ($this->images && is_array($this->images)) {
            $images = array_filter($this->images, function($img) use ($imagePath) {
                return $img !== $imagePath;
            });
            $this->images = array_values($images);
        }

        $this->save();

        // Optionally delete the actual file
        // delete_from_storage($imagePath);
    }

    /**
     * Get all images (featured + gallery)
     */
    public function getAllImages()
    {
        $allImages = [];

        if ($this->featured_image) {
            $allImages[] = $this->featured_image;
        }

        if ($this->images && is_array($this->images)) {
            foreach ($this->images as $image) {
                if ($image !== $this->featured_image) {
                    $allImages[] = $image;
                }
            }
        }

        return $allImages;
    }

    /**
     * Get image count
     */
    public function getImageCountAttribute()
    {
        return count($this->getAllImages());
    }
}
