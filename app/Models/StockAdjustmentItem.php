<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_adjustment_id', 'product_id', 'current_stock', 'adjusted_quantity',
        'new_stock', 'unit_cost', 'remarks'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getAdjustmentTypeAttribute()
    {
        return $this->adjusted_quantity > 0 ? 'increase' : 'decrease';
    }

    public function getAdjustmentValueAttribute()
    {
        return abs($this->adjusted_quantity) * ($this->unit_cost ?? 0);
    }
}
