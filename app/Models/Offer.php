<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Offer extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'name', 'code', 'type', 'value', 'minimum_amount',
        'category_id', 'product_id', 'start_date', 'end_date',
        'is_active', 'usage_limit', 'used_count', 'company_id'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
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

        if ($this->type === 'product' && (!$product || $product->id !== $this->product_id)) {
            return 0;
        }

        if ($this->type === 'category' && (!$category || $category->id !== $this->category_id)) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return min($amount * ($this->value / 100), $amount);
        }

        return min($this->value, $amount);
    }
}
