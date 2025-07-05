<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_number', 'purchase_order_id', 'supplier_id', 'received_date',
        'invoice_number', 'invoice_date', 'invoice_amount', 'status',
        'notes', 'received_by'
    ];

    protected $casts = [
        'received_date' => 'date',
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($grn) {
            if (!$grn->grn_number) {
                $grn->grn_number = 'GRN' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class, 'grn_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'partial' => 'info',
            'completed' => 'success',
            default => 'secondary'
        };
    }
}
