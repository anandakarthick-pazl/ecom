<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_sale_id', 'product_id', 'product_name', 'quantity',
        'unit_price', 'discount_amount', 'tax_percentage', 'tax_amount', 'total_amount'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function posSale()
    {
        return $this->belongsTo(PosSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getNetUnitPriceAttribute()
    {
        return $this->unit_price - ($this->discount_amount / $this->quantity);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->unit_price == 0) return 0;
        return round(($this->discount_amount / ($this->unit_price * $this->quantity)) * 100, 2);
    }
}
