<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppConfig extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_configs';

    protected $fillable = [
        'twilio_account_sid',
        'twilio_auth_token',
        'twilio_phone_number',
        'whatsapp_business_number',
        'is_enabled',
        'default_message_template',
        'test_number',
        'webhook_url',
        'webhook_secret',
        'max_file_size_mb',
        'allowed_file_types',
        'rate_limit_per_minute',
        'company_id'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allowed_file_types' => 'array',
        'max_file_size_mb' => 'integer',
        'rate_limit_per_minute' => 'integer'
    ];

    protected $hidden = [
        'twilio_auth_token',
        'webhook_secret'
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\SuperAdmin\Company::class);
    }

    /**
     * Get the default message template with placeholders
     */
    public function getDefaultMessageTemplate()
    {
        return $this->default_message_template ?: 
            "Hello {{customer_name}},\n\nYour order #{{order_number}} has been processed. Please find your bill attached.\n\nOrder Total: ₹{{total}}\n\nThank you for your business!\n\n{{company_name}}";
    }

    /**
     * Check if WhatsApp is properly configured
     */
    public function isConfigured()
    {
        return $this->is_enabled && 
               !empty($this->twilio_account_sid) && 
               !empty($this->twilio_auth_token) && 
               !empty($this->whatsapp_business_number);
    }

    /**
     * Get masked auth token for display
     */
    public function getMaskedAuthToken()
    {
        if (empty($this->twilio_auth_token)) {
            return '';
        }
        
        $token = $this->twilio_auth_token;
        $length = strlen($token);
        
        if ($length <= 8) {
            return str_repeat('*', $length);
        }
        
        return substr($token, 0, 4) . str_repeat('*', $length - 8) . substr($token, -4);
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneNumber()
    {
        if (empty($this->whatsapp_business_number)) {
            return '';
        }
        
        $number = $this->whatsapp_business_number;
        
        // Remove whatsapp: prefix if present
        if (str_starts_with($number, 'whatsapp:')) {
            $number = substr($number, 9);
        }
        
        return $number;
    }

    /**
     * Get WhatsApp formatted number
     */
    public function getWhatsAppNumber()
    {
        $number = $this->getFormattedPhoneNumber();
        
        if (empty($number)) {
            return '';
        }
        
        // Add whatsapp: prefix if not present
        if (!str_starts_with($number, 'whatsapp:')) {
            $number = 'whatsapp:' . $number;
        }
        
        return $number;
    }

    /**
     * Validate phone number format
     */
    public static function validatePhoneNumber($number)
    {
        // Remove whatsapp: prefix for validation
        $cleanNumber = str_replace('whatsapp:', '', $number);
        
        // Check if it's a valid international format
        return preg_match('/^\+[1-9]\d{1,14}$/', $cleanNumber);
    }

    /**
     * Get allowed file types as string
     */
    public function getAllowedFileTypesString()
    {
        return implode(', ', $this->allowed_file_types ?? ['pdf']);
    }

    /**
     * Check if file type is allowed
     */
    public function isFileTypeAllowed($extension)
    {
        $allowedTypes = $this->allowed_file_types ?? ['pdf'];
        return in_array(strtolower($extension), array_map('strtolower', $allowedTypes));
    }

    /**
     * Get rate limit status
     */
    public function getRateLimitStatus()
    {
        // This could be implemented with Redis/Cache to track actual usage
        return [
            'limit' => $this->rate_limit_per_minute ?? 10,
            'used' => 0, // Would be tracked in cache
            'remaining' => $this->rate_limit_per_minute ?? 10,
            'reset_at' => now()->addMinute()
        ];
    }
}
