<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id', 'product_id', 'quantity', 'received_quantity',
        'unit_price', 'total_price', 'description'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function getPendingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function getIsFullyReceivedAttribute()
    {
        return $this->received_quantity >= $this->quantity;
    }
}
