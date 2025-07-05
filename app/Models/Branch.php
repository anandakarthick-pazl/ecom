<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SuperAdmin\Company;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'manager_name',
        'manager_email',
        'manager_phone',
        'status',
        'description',
        'settings',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'settings' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function posSales()
    {
        return $this->hasMany(PosSale::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function goodsReceiptNotes()
    {
        return $this->hasMany(GoodsReceiptNote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $addressParts);
    }

    public function getManagerContactAttribute()
    {
        if ($this->manager_name) {
            $contact = $this->manager_name;
            if ($this->manager_phone) {
                $contact .= ' (' . $this->manager_phone . ')';
            }
            return $contact;
        }
        return null;
    }

    // Generate unique branch code
    public static function generateBranchCode($companyId)
    {
        $lastBranch = static::where('company_id', $companyId)
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastBranch) {
            return 'BR001';
        }

        // Extract number from last code (e.g., BR005 -> 5)
        $lastNumber = (int) substr($lastBranch->code, 2);
        $nextNumber = $lastNumber + 1;

        return 'BR' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Branch stats
    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getTotalCustomersAttribute()
    {
        return $this->customers()->count();
    }

    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    public function getTotalUsersAttribute()
    {
        return $this->users()->count();
    }

    public function getMonthlyOrdersAttribute()
    {
        return $this->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function getMonthlyRevenueAttribute()
    {
        return $this->orders()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
    }

    // Location helper
    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function getDistanceFrom($latitude, $longitude)
    {
        if (!$this->hasLocation()) {
            return null;
        }

        // Haversine formula for calculating distance
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // Override boot method to auto-generate code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($branch) {
            if (empty($branch->code)) {
                $branch->code = static::generateBranchCode($branch->company_id);
            }
        });
    }
}
