<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Order extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'order_number', 'customer_id', 'customer_name', 'customer_mobile', 'customer_email',
        'delivery_address', 'city', 'state', 'pincode',
        'subtotal', 'discount', 'delivery_charge', 'tax_amount', 'cgst_amount', 'sgst_amount', 'total',
        'status', 'notes', 'admin_notes', 'shipped_at', 'delivered_at', 'company_id', 'branch_id',
        'payment_method', 'payment_status', 'payment_transaction_id', 'payment_details', 'paid_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'HB' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByMobile($query, $mobile)
    {
        return $query->where('customer_mobile', $mobile);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        
        if ($status === 'shipped') {
            $this->shipped_at = now();
        } elseif ($status === 'delivered') {
            $this->delivered_at = now();
        }
        
        $this->save();
    }

    public function updatePaymentStatus($status, $transactionId = null, $details = [])
    {
        $this->payment_status = $status;
        
        if ($transactionId) {
            $this->payment_transaction_id = $transactionId;
        }
        
        if (!empty($details)) {
            $this->payment_details = array_merge($this->payment_details ?? [], $details);
        }
        
        if ($status === 'paid') {
            $this->paid_at = now();
        }
        
        $this->save();
    }

    public function getPaymentStatusColorAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'warning',
            'processing' => 'info',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary'
        };
    }

    public function getPaymentStatusTextAttribute()
    {
        return ucfirst($this->payment_status ?? 'pending');
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }
}
