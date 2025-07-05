<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id', 'product_id', 'purchase_order_item_id', 'ordered_quantity',
        'received_quantity', 'unit_price', 'total_amount', 'remarks'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function grn()
    {
        return $this->belongsTo(GoodsReceiptNote::class, 'grn_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function getVarianceQuantityAttribute()
    {
        return $this->received_quantity - $this->ordered_quantity;
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->ordered_quantity == 0) return 0;
        return round(($this->variance_quantity / $this->ordered_quantity) * 100, 2);
    }
}
