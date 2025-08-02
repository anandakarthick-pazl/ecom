<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Commission extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'company_id',
        'branch_id',
        'reference_type',
        'reference_id',
        'reference_name',
        'commission_percentage',
        'base_amount',
        'commission_amount',
        'status',
        'notes',
        'paid_at',
        'paid_by'
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(\App\Models\SuperAdmin\Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Dynamic relationships based on reference_type
    public function posSale()
    {
        return $this->belongsTo(PosSale::class, 'reference_id')
            ->where('reference_type', 'pos_sale');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'reference_id')
            ->where('reference_type', 'order');
    }

    // Get the referenced model dynamically
    public function getReferencedModelAttribute()
    {
        switch ($this->reference_type) {
            case 'pos_sale':
                return $this->posSale;
            case 'order':
                return $this->order;
            default:
                return null;
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByReferenceName($query, $name)
    {
        return $query->where('reference_name', 'like', '%' . $name . '%');
    }

    public function scopeByReferenceType($query, $type)
    {
        return $query->where('reference_type', $type);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeLastMonth($query)
    {
        return $query->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Mutators and Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getFormattedCommissionPercentageAttribute()
    {
        return number_format($this->commission_percentage, 2) . '%';
    }

    public function getFormattedBaseAmountAttribute()
    {
        return '₹' . number_format($this->base_amount, 2);
    }

    public function getFormattedCommissionAmountAttribute()
    {
        return '₹' . number_format($this->commission_amount, 2);
    }

    // Methods
    public function markAsPaid($paidBy = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $paidBy ?? auth()->id()
        ]);
    }

    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function canBePaid()
    {
        return $this->status === 'pending';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    // Static methods
    public static function createFromPosSale(PosSale $sale, $referenceName, $commissionPercentage, $notes = null)
    {
        // Calculate commission based on sale total
        $baseAmount = $sale->total_amount;
        $commissionAmount = ($baseAmount * $commissionPercentage) / 100;

        return static::create([
            'company_id' => $sale->company_id,
            'branch_id' => $sale->branch_id ?? null,
            'reference_type' => 'pos_sale',
            'reference_id' => $sale->id,
            'reference_name' => $referenceName,
            'commission_percentage' => $commissionPercentage,
            'base_amount' => $baseAmount,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
            'notes' => $notes
        ]);
    }

    public static function createFromOrder(Order $order, $referenceName, $commissionPercentage, $notes = null)
    {
        // Calculate commission based on order total
        $baseAmount = $order->total;
        $commissionAmount = ($baseAmount * $commissionPercentage) / 100;

        return static::create([
            'company_id' => $order->company_id,
            'branch_id' => $order->branch_id ?? null,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'reference_name' => $referenceName,
            'commission_percentage' => $commissionPercentage,
            'base_amount' => $baseAmount,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
            'notes' => $notes
        ]);
    }

    // Get summary stats for dashboard
    public static function getSummaryStats($companyId = null)
    {
        $companyId = $companyId ?? session('selected_company_id');
        
        $query = static::where('company_id', $companyId);

        return [
            'total_pending' => $query->clone()->pending()->sum('commission_amount'),
            'total_paid' => $query->clone()->paid()->sum('commission_amount'),
            'total_this_month' => $query->clone()->thisMonth()->sum('commission_amount'),
            'count_pending' => $query->clone()->pending()->count(),
            'count_paid' => $query->clone()->paid()->count(),
            'count_this_month' => $query->clone()->thisMonth()->count(),
        ];
    }

    // Get top performers
    public static function getTopPerformers($companyId = null, $limit = 10)
    {
        $companyId = $companyId ?? session('selected_company_id');
        
        return static::where('company_id', $companyId)
            ->selectRaw('reference_name, 
                SUM(commission_amount) as total_commission,
                COUNT(*) as total_sales,
                AVG(commission_percentage) as avg_percentage')
            ->groupBy('reference_name')
            ->orderByDesc('total_commission')
            ->limit($limit)
            ->get();
    }
}
