<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number', 'supplier_id', 'po_date', 'expected_delivery_date',
        'subtotal', 'tax_amount', 'discount', 'total_amount', 'status',
        'notes', 'terms_conditions', 'created_by', 'approved_at', 'approved_by',
        'company_id', 'branch_id'
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($po) {
            if (!$po->po_number) {
                $po->po_number = 'PO' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'info',
            'approved' => 'primary',
            'received' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getTotalReceivedQuantityAttribute()
    {
        return $this->items->sum('received_quantity');
    }

    public function getTotalOrderedQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getIsFullyReceivedAttribute()
    {
        return $this->total_received_quantity >= $this->total_ordered_quantity;
    }
}
