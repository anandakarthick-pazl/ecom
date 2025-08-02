<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class OrderItem extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_slug',
        'price', 'mrp_price', 'discount_amount', 'discount_percentage', 'offer_id', 'offer_name',
        'quantity', 'tax_percentage', 'tax_amount', 'total', 'company_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Check if this item has a discount applied
     */
    public function hasDiscount()
    {
        return $this->discount_amount > 0 || $this->discount_percentage > 0;
    }

    /**
     * Get the effective price per unit (after discount)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->price; // This is already the discounted price
    }

    /**
     * Get the MRP total (MRP * quantity)
     */
    public function getMrpTotalAttribute()
    {
        return $this->mrp_price * $this->quantity;
    }

    /**
     * Get the total discount amount for this line item
     */
    public function getTotalDiscountAttribute()
    {
        return $this->discount_amount * $this->quantity;
    }

    /**
     * Get the savings amount for this line item
     */
    public function getSavingsAttribute()
    {
        return $this->mrp_total - ($this->price * $this->quantity);
    }

    /**
     * Get the effective discount percentage for display
     */
    public function getEffectiveDiscountPercentageAttribute()
    {
        if ($this->mrp_price > 0 && $this->mrp_price > $this->price) {
            return round((($this->mrp_price - $this->price) / $this->mrp_price) * 100, 2);
        }
        return $this->discount_percentage;
    }
}
