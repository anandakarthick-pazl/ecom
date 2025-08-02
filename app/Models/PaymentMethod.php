<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'display_name',
        'description',
        'image', // Payment method image/logo
        'is_active',
        'sort_order',
        'razorpay_key_id',
        'razorpay_key_secret',
        'razorpay_webhook_secret',
        'bank_details',
        'upi_id',
        'upi_qr_code',
        'settings',
        'minimum_amount',
        'maximum_amount',
        'extra_charge',
        'extra_charge_percentage'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bank_details' => 'array',
        'settings' => 'array',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'extra_charge' => 'decimal:2',
        'extra_charge_percentage' => 'decimal:2'
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(\App\Models\SuperAdmin\Company::class);
    }

    // Encrypt sensitive data
    public function setRazorpayKeySecretAttribute($value)
    {
        if ($value) {
            $this->attributes['razorpay_key_secret'] = Crypt::encryptString($value);
        }
    }

    // Decrypt sensitive data
    public function getRazorpayKeySecretAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    // Encrypt webhook secret
    public function setRazorpayWebhookSecretAttribute($value)
    {
        if ($value) {
            $this->attributes['razorpay_webhook_secret'] = Crypt::encryptString($value);
        }
    }

    // Decrypt webhook secret
    public function getRazorpayWebhookSecretAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function calculateTotalCharge($amount)
    {
        $extraCharge = $this->extra_charge;
        $percentageCharge = ($amount * $this->extra_charge_percentage) / 100;
        
        return $amount + $extraCharge + $percentageCharge;
    }

    public function isAmountValid($amount)
    {
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return false;
        }
        
        if ($this->maximum_amount && $amount > $this->maximum_amount) {
            return false;
        }
        
        return true;
    }

    public function getIcon()
    {
        return match($this->type) {
            'razorpay' => 'fas fa-credit-card',
            'cod' => 'fas fa-money-bill-wave',
            'bank_transfer' => 'fas fa-university',
            'upi' => 'fas fa-mobile-alt',
            'gpay' => 'fab fa-google-pay',
            default => 'fas fa-wallet'
        };
    }

    public function getColor()
    {
        return match($this->type) {
            'razorpay' => 'primary',
            'cod' => 'success',
            'bank_transfer' => 'info',
            'upi' => 'warning',
            'gpay' => 'danger',
            default => 'secondary'
        };
    }

    // Check if payment method has an image
    public function hasImage()
    {
        return !empty($this->image);
    }

    // Get image URL with custom format
    public function getImageUrl()
    {
        if (!$this->image) {
            return null;
        }
        
        // Handle both old format (just filename) and new format (payment-methods/filename)
        if (strpos($this->image, 'payment-methods/') === 0) {
            // New format: payment-methods/filename.jpg
            return asset('storage/' . $this->image);
        } else {
            // Old format: just filename.jpg (for backward compatibility)
            return asset('storage/payment-methods/' . $this->image);
        }
    }
    
    // Get admin-friendly image URL for display in admin panel
    public function getAdminImageUrl()
    {
        return $this->getImageUrl();
    }
    
    // Get QR code URL for UPI/GPay payments
    public function getQrCodeUrl()
    {
        if (!$this->upi_qr_code) {
            return null;
        }
        
        // Handle both old format (just filename) and new format (payment-methods/filename)
        if (strpos($this->upi_qr_code, 'payment-methods/') === 0) {
            // New format: payment-methods/filename.jpg
            return asset('storage/' . $this->upi_qr_code);
        } else {
            // Old format: just filename.jpg (for backward compatibility)
            return asset('storage/payment-methods/' . $this->upi_qr_code);
        }
    }

    // Get configuration based on type
    public function getConfiguration()
    {
        return match($this->type) {
            'razorpay' => [
                'key_id' => $this->razorpay_key_id,
                'key_secret' => $this->razorpay_key_secret,
                'webhook_secret' => $this->razorpay_webhook_secret
            ],
            'bank_transfer' => $this->bank_details,
            'upi', 'gpay' => [
                'upi_id' => $this->upi_id,
                'qr_code' => $this->upi_qr_code
            ],
            default => []
        };
    }

    // Static helper method to get payment methods for a company
    public static function getForCompany($companyId, $type = null)
    {
        $query = self::where('company_id', $companyId)->where('is_active', true);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('sort_order')->get();
    }

    // Static helper method to get active Razorpay method for a company
    public static function getRazorpayForCompany($companyId)
    {
        return self::where('company_id', $companyId)
                   ->where('type', 'razorpay')
                   ->where('is_active', true)
                   ->first();
    }
}
