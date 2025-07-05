<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenantEnhanced;

class Product extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

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
}
