<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Supplier extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'name', 'company_name', 'email', 'phone', 'mobile', 'address',
        'city', 'state', 'pincode', 'gst_number', 'pan_number',
        'credit_limit', 'credit_days', 'opening_balance', 'is_active', 'notes', 'company_id', 'branch_id'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function grns()
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->purchaseOrders()
                   ->where('status', '!=', 'cancelled')
                   ->sum('total_amount');
    }

    public function getOutstandingAmountAttribute()
    {
        // Calculate based on invoices and payments (to be implemented)
        return 0;
    }

    public function getDisplayNameAttribute()
    {
        return $this->company_name ?: $this->name;
    }
}
