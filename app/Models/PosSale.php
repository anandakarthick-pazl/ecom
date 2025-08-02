<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;
use App\Services\InvoiceNumberService;

class PosSale extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'company_id', 'invoice_number', 'sale_date', 'customer_name', 'customer_phone',
        'subtotal', 'tax_amount', 'custom_tax_enabled', 'custom_tax_amount', 'cgst_amount', 
        'sgst_amount', 'discount_amount', 'total_amount', 'paid_amount', 'change_amount', 
        'payment_method', 'status', 'notes', 'tax_notes', 'cashier_id'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'custom_tax_enabled' => 'boolean',
        'custom_tax_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($sale) {
            if (!$sale->invoice_number) {
                try {
                    $invoiceService = new InvoiceNumberService();
                    $sale->invoice_number = $invoiceService->generatePosInvoiceNumber($sale->company_id);
                } catch (\Exception $e) {
                    \Log::error('Failed to generate POS invoice number, using fallback', [
                        'error' => $e->getMessage(),
                        'company_id' => $sale->company_id
                    ]);
                    // Fallback to original random generation
                    $sale->invoice_number = 'INV' . date('Ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    public function items()
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function commission()
    {
        return $this->hasOne(Commission::class, 'reference_id')
                    ->where('reference_type', 'pos_sale');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'refunded' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->paid_amount >= $this->total_amount) {
            return 'paid';
        } elseif ($this->paid_amount > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }
}
