<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'gst_number',
        'logo',
        'favicon',
        'theme_id',
        'package_id',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
        'created_by'
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'settings' => 'array'
    ];

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(\App\Models\Branch::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isSubscriptionExpired()
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    public function getRemainingTrialDaysAttribute()
    {
        if (!$this->trial_ends_at) return 0;
        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('trial_ends_at', '<=', now()->addDays($days))
                    ->orWhere('subscription_ends_at', '<=', now()->addDays($days));
    }
}
