<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'trial_days',
        'features',
        'limits',
        'status',
        'is_popular',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'is_popular' => 'boolean'
    ];

    const BILLING_CYCLES = [
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'lifetime' => 'Lifetime'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function getBillingCycleNameAttribute()
    {
        return self::BILLING_CYCLES[$this->billing_cycle] ?? $this->billing_cycle;
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2);
    }
}
