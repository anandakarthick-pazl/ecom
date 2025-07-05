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
        'price', 'quantity', 'tax_percentage', 'tax_amount', 'total', 'company_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
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
}
