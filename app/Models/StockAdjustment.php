<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number', 'adjustment_date', 'type', 'status', 'reason',
        'notes', 'created_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($adjustment) {
            if (!$adjustment->adjustment_number) {
                $adjustment->adjustment_number = 'ADJ' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'approved' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getTotalAdjustmentValueAttribute()
    {
        return $this->items->sum(function ($item) {
            return abs($item->adjusted_quantity) * ($item->unit_cost ?? 0);
        });
    }

    public function approve($approver_id)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver_id,
            'approved_at' => now()
        ]);

        // Update product stock
        foreach ($this->items as $item) {
            $item->product->update([
                'stock' => $item->new_stock
            ]);
        }
    }
}
