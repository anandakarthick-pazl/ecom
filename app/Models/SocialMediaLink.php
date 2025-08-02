<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class SocialMediaLink extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'company_id',
        'name',
        'icon_class',
        'url',
        'color',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope for active social media links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered social media links
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get formatted URL with https if needed
     */
    public function getFormattedUrlAttribute()
    {
        $url = $this->url;
        
        if (empty($url)) {
            return '';
        }
        
        // Add https:// if no protocol specified
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }

    /**
     * Get predefined social media platforms
     */
    public static function getPredefinedPlatforms()
    {
        return [
            'Facebook' => [
                'name' => 'Facebook',
                'icon_class' => 'fab fa-facebook-f',
                'color' => '#1877f2',
                'placeholder' => 'https://facebook.com/yourpage'
            ],
            'Twitter' => [
                'name' => 'Twitter',
                'icon_class' => 'fab fa-twitter',
                'color' => '#1da1f2',
                'placeholder' => 'https://twitter.com/yourusername'
            ],
            'Instagram' => [
                'name' => 'Instagram',
                'icon_class' => 'fab fa-instagram',
                'color' => '#e4405f',
                'placeholder' => 'https://instagram.com/yourusername'
            ],
            'LinkedIn' => [
                'name' => 'LinkedIn',
                'icon_class' => 'fab fa-linkedin-in',
                'color' => '#0077b5',
                'placeholder' => 'https://linkedin.com/company/yourcompany'
            ],
            'YouTube' => [
                'name' => 'YouTube',
                'icon_class' => 'fab fa-youtube',
                'color' => '#ff0000',
                'placeholder' => 'https://youtube.com/channel/yourchannel'
            ],
            'WhatsApp' => [
                'name' => 'WhatsApp',
                'icon_class' => 'fab fa-whatsapp',
                'color' => '#25d366',
                'placeholder' => 'https://wa.me/1234567890'
            ],
            'Telegram' => [
                'name' => 'Telegram',
                'icon_class' => 'fab fa-telegram-plane',
                'color' => '#0088cc',
                'placeholder' => 'https://t.me/yourchannel'
            ],
            'Pinterest' => [
                'name' => 'Pinterest',
                'icon_class' => 'fab fa-pinterest-p',
                'color' => '#bd081c',
                'placeholder' => 'https://pinterest.com/yourprofile'
            ],
            'TikTok' => [
                'name' => 'TikTok',
                'icon_class' => 'fab fa-tiktok',
                'color' => '#000000',
                'placeholder' => 'https://tiktok.com/@yourusername'
            ],
            'Snapchat' => [
                'name' => 'Snapchat',
                'icon_class' => 'fab fa-snapchat-ghost',
                'color' => '#fffc00',
                'placeholder' => 'https://snapchat.com/add/yourusername'
            ]
        ];
    }

    /**
     * Get the brand color for display
     */
    public function getBrandColorAttribute()
    {
        return $this->color ?: $this->getDefaultColorForPlatform();
    }

    /**
     * Get default color based on platform name
     */
    private function getDefaultColorForPlatform()
    {
        $platforms = self::getPredefinedPlatforms();
        
        foreach ($platforms as $platform) {
            if (stripos($this->name, $platform['name']) !== false) {
                return $platform['color'];
            }
        }
        
        return '#6c757d'; // Default gray
    }

    /**
     * Validate URL format
     */
    public function isValidUrl()
    {
        $url = $this->formatted_url;
        return !empty($url) && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
