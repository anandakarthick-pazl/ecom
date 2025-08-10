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

    // Add virtual attribute for discount percentage calculation
    protected $appends = ['calculated_discount_percentage'];

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

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function categoryOffers()
    {
        return $this->hasMany(Offer::class, 'category_id', 'category_id')->where('type', 'category');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get applicable offers for this product with priority system
     * Priority: 1. Offers page offers, 2. Product onboarding discount
     */
    public function getApplicableOffers()
    {
        return Offer::where(function($query) {
            // Product-specific offers
            $query->where('type', 'product')
                  ->where('product_id', $this->id);
        })->orWhere(function($query) {
            // Category-specific offers
            $query->where('type', 'category')
                  ->where('category_id', $this->category_id);
        })->orWhere(function($query) {
            // General offers (percentage/fixed)
            $query->whereIn('type', ['percentage', 'fixed', 'flash'])
                  ->whereNull('category_id')
                  ->whereNull('product_id');
        })->active()
          ->current()
          ->orderBy('value', 'desc') // Higher discount first
          ->get();
    }

    /**
     * Get the best applicable offer for this product with priority system
     * Priority: 1. Offers page offers (highest discount), 2. Product onboarding discount
     */
    public function getBestOffer()
    {
        // Get all applicable offers from the offers page/menu
        $offersPageOffers = $this->getApplicableOffers();
        
        $bestOffer = null;
        $bestDiscount = 0;
        $bestOfferSource = null;

        // First priority: Check offers from the offers page/menu
        foreach ($offersPageOffers as $offer) {
            $discount = $offer->calculateDiscount($this->price, $this, $this->category);
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestOffer = $offer;
                $bestOfferSource = 'offers_page';
            }
        }

        // Second priority: If no offers page offer found, check product onboarding discount
        if (!$bestOffer && $this->discount_price && $this->discount_price < $this->price) {
            $productDiscount = $this->price - $this->discount_price;
            if ($productDiscount > 0) {
                // Create a virtual offer object for product onboarding discount
                $bestOffer = (object) [
                    'id' => 'product_onboarding_' . $this->id,
                    'name' => 'Product Discount',
                    'type' => 'product_onboarding',
                    'discount_type' => 'fixed', // Add this property
                    'value' => $productDiscount,
                    'source' => 'product_onboarding',
                    'discount_amount' => $productDiscount,
                    'discounted_price' => $this->discount_price,
                    'is_virtual' => true,
                    'start_date' => null,
                    'end_date' => null,
                    'is_active' => true,
                    'minimum_amount' => 0
                ];
                $bestDiscount = $productDiscount;
                $bestOfferSource = 'product_onboarding';
            }
        }

        return $bestOffer ? (object) array_merge(
            (array) $bestOffer,
            [
                'calculated_discount' => $bestDiscount,
                'source' => $bestOfferSource,
                'final_price' => $this->price - $bestDiscount
            ]
        ) : null;
    }

    /**
     * Get the dynamic discount price based on priority system
     * Priority: 1. Offers page offers, 2. Product onboarding discount
     */
    public function getDynamicDiscountPrice()
    {
        // Get the best offer using our priority system
        $bestOffer = $this->getBestOffer();
        
        if ($bestOffer) {
            return $bestOffer->final_price;
        }

        // No discounts available
        return null;
    }

    /**
     * Get the effective final price (manual discount or offer-based)
     */
    public function getEffectiveFinalPrice()
    {
        $dynamicPrice = $this->getDynamicDiscountPrice();
        return $dynamicPrice ?: $this->price;
    }

    /**
     * Get the effective discount percentage
     */
    public function getEffectiveDiscountPercentage()
    {
        $finalPrice = $this->getEffectiveFinalPrice();
        if ($finalPrice < $this->price) {
            return round((($this->price - $finalPrice) / $this->price) * 100);
        }
        return 0;
    }

    /**
     * Check if product has any active offers (either from offers page or product onboarding)
     */
    public function hasActiveOffers()
    {
        return $this->getBestOffer() !== null;
    }

    /**
     * Check if product has active offers from the offers page/menu
     */
    public function hasActiveOffersPageOffers()
    {
        return $this->getApplicableOffers()->count() > 0;
    }

    /**
     * Check if product has onboarding discount
     */
    public function hasProductOnboardingDiscount()
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    /**
     * Get a safe offer property value with fallback
     */
    public function getOfferProperty($offer, $property, $default = null)
    {
        if (is_object($offer) && property_exists($offer, $property)) {
            return $offer->$property;
        }
        
        if (is_array($offer) && isset($offer[$property])) {
            return $offer[$property];
        }
        
        return $default;
    }

    /**
     * Get the current active offer source
     * Returns: 'offers_page', 'product_onboarding', or null
     */
    public function getActiveOfferSource()
    {
        $bestOffer = $this->getBestOffer();
        return $bestOffer ? ($bestOffer->source ?? null) : null;
    }

    /**
     * Get offer details for display with priority system
     * Priority: 1. Offers page offers, 2. Product onboarding discount
     */
    public function getOfferDetails()
    {
        $bestOffer = $this->getBestOffer();
        if (!$bestOffer) {
            return null;
        }

        $discountAmount = $bestOffer->calculated_discount;
        $discountedPrice = $bestOffer->final_price;
        $discountPercentage = round(($discountAmount / $this->price) * 100, 1);

        return [
            'offer' => $bestOffer,
            'source' => $bestOffer->source ?? 'unknown',
            'original_price' => $this->price,
            'discounted_price' => $discountedPrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'savings' => $discountAmount,
            'is_offers_page' => ($bestOffer->source ?? '') === 'offers_page',
            'is_product_onboarding' => ($bestOffer->source ?? '') === 'product_onboarding',
            'offer_name' => $bestOffer->name ?? 'Discount',
            'offer_type' => $bestOffer->type ?? 'unknown'
        ];
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

    public function getCalculatedDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->discount_price < $this->price) {
            return round((($this->price - $this->discount_price) / $this->price) * 100, 2);
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
