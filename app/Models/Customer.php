<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Customer extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'mobile_number', 'name', 'email', 'address', 'city', 'state', 'pincode',
        'total_orders', 'total_spent', 'last_order_at', 'company_id', 'branch_id'
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'last_order_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function posSales()
    {
        return $this->hasMany(PosSale::class, 'customer_phone', 'mobile_number');
    }

    public function getFormattedMobileAttribute()
    {
        return '+91 ' . $this->mobile_number;
    }

    public static function findOrCreateByMobile($mobileNumber, $additionalData = [])
    {
        $customer = self::where('mobile_number', $mobileNumber)->first();
        
        if (!$customer) {
            $customer = self::create(array_merge([
                'mobile_number' => $mobileNumber,
            ], $additionalData));
        }
        
        return $customer;
    }

    public function updateOrderStats()
    {
        $this->total_orders = $this->orders()->count();
        $this->total_spent = $this->orders()->where('status', '!=', 'cancelled')->sum('total');
        $this->last_order_at = $this->orders()->latest()->first()?->created_at;
        $this->save();
    }
}
