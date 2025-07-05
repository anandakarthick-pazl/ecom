<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'package_id',
        'amount',
        'billing_cycle',
        'status',
        'payment_method',
        'transaction_id',
        'invoice_number',
        'billing_date',
        'due_date',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'billing_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }
}
