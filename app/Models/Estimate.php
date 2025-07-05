<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_number', 'customer_name', 'customer_email', 'customer_phone',
        'customer_address', 'estimate_date', 'valid_until', 'subtotal',
        'tax_amount', 'discount', 'total_amount', 'status', 'notes',
        'terms_conditions', 'created_by', 'sent_at', 'accepted_at'
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($estimate) {
            if (!$estimate->estimate_number) {
                $estimate->estimate_number = 'EST' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired' => 'warning',
            default => 'secondary'
        };
    }

    public function getIsExpiredAttribute()
    {
        return $this->valid_until < today() && $this->status === 'sent';
    }

    public function markAsExpired()
    {
        if ($this->is_expired) {
            $this->update(['status' => 'expired']);
        }
    }
}
