<?php

namespace App\Helpers;

class IconClass
{
    /**
     * Get all available social media icons
     */
    public static function getSocialMediaIcons()
    {
        return [
            'fab fa-facebook-f' => 'Facebook',
            'fab fa-twitter' => 'Twitter',
            'fab fa-instagram' => 'Instagram',
            'fab fa-linkedin-in' => 'LinkedIn',
            'fab fa-youtube' => 'YouTube',
            'fab fa-whatsapp' => 'WhatsApp',
            'fab fa-telegram-plane' => 'Telegram',
            'fab fa-pinterest-p' => 'Pinterest',
            'fab fa-tiktok' => 'TikTok',
            'fab fa-snapchat-ghost' => 'Snapchat',
            'fab fa-discord' => 'Discord',
            'fab fa-reddit' => 'Reddit',
            'fab fa-github' => 'GitHub',
            'fab fa-google' => 'Google',
            'fab fa-apple' => 'Apple',
            'fab fa-amazon' => 'Amazon',
            'fab fa-ebay' => 'eBay',
            'fab fa-paypal' => 'PayPal',
            'fab fa-skype' => 'Skype',
            'fab fa-viber' => 'Viber',
        ];
    }

    /**
     * Get all available location icons
     */
    public static function getLocationIcons()
    {
        return [
            'fas fa-map-marker-alt' => 'Location Marker',
            'fas fa-map-pin' => 'Map Pin',
            'fas fa-location-arrow' => 'Location Arrow',
            'fas fa-compass' => 'Compass',
            'fas fa-globe' => 'Globe',
            'fas fa-map' => 'Map',
            'fas fa-route' => 'Route',
            'fas fa-navigation' => 'Navigation',
            'fas fa-crosshairs' => 'Crosshairs',
            'fas fa-map-marked' => 'Map Marked',
            'fas fa-map-marked-alt' => 'Map Marked Alt',
            'fas fa-street-view' => 'Street View',
            'fas fa-location-dot' => 'Location Dot',
            'fas fa-map-location' => 'Map Location',
            'fas fa-map-location-dot' => 'Map Location Dot',
            'fas fa-directions' => 'Directions',
            'fas fa-place-of-worship' => 'Place of Worship',
            'fas fa-building' => 'Building',
            'fas fa-home' => 'Home',
            'fas fa-store' => 'Store',
            'fas fa-hospital' => 'Hospital',
            'fas fa-school' => 'School',
            'fas fa-university' => 'University',
            'fas fa-gas-pump' => 'Gas Station',
            'fas fa-parking' => 'Parking',
            'fas fa-subway' => 'Subway',
            'fas fa-bus' => 'Bus Stop',
            'fas fa-taxi' => 'Taxi',
            'fas fa-plane' => 'Airport',
            'fas fa-train' => 'Train Station',
            'fas fa-ship' => 'Port',
        ];
    }

    /**
     * Get all available general icons
     */
    public static function getGeneralIcons()
    {
        return [
            'fas fa-phone' => 'Phone',
            'fas fa-envelope' => 'Email',
            'fas fa-clock' => 'Clock',
            'fas fa-calendar' => 'Calendar',
            'fas fa-user' => 'User',
            'fas fa-users' => 'Users',
            'fas fa-star' => 'Star',
            'fas fa-heart' => 'Heart',
            'fas fa-thumbs-up' => 'Thumbs Up',
            'fas fa-share' => 'Share',
            'fas fa-download' => 'Download',
            'fas fa-upload' => 'Upload',
            'fas fa-search' => 'Search',
            'fas fa-filter' => 'Filter',
            'fas fa-sort' => 'Sort',
            'fas fa-edit' => 'Edit',
            'fas fa-trash' => 'Delete',
            'fas fa-eye' => 'View',
            'fas fa-eye-slash' => 'Hide',
            'fas fa-lock' => 'Lock',
            'fas fa-unlock' => 'Unlock',
            'fas fa-key' => 'Key',
            'fas fa-cog' => 'Settings',
            'fas fa-bell' => 'Notification',
            'fas fa-flag' => 'Flag',
            'fas fa-bookmark' => 'Bookmark',
            'fas fa-tag' => 'Tag',
            'fas fa-tags' => 'Tags',
            'fas fa-comment' => 'Comment',
            'fas fa-comments' => 'Comments',
            'fas fa-info' => 'Info',
            'fas fa-exclamation' => 'Warning',
            'fas fa-question' => 'Question',
            'fas fa-check' => 'Check',
            'fas fa-times' => 'Close',
            'fas fa-plus' => 'Add',
            'fas fa-minus' => 'Remove',
            'fas fa-arrow-left' => 'Arrow Left',
            'fas fa-arrow-right' => 'Arrow Right',
            'fas fa-arrow-up' => 'Arrow Up',
            'fas fa-arrow-down' => 'Arrow Down',
        ];
    }

    /**
     * Get all icons combined
     */
    public static function getAllIcons()
    {
        return array_merge(
            self::getSocialMediaIcons(),
            self::getLocationIcons(),
            self::getGeneralIcons()
        );
    }

    /**
     * Get icon by category
     */
    public static function getIconsByCategory($category = 'all')
    {
        switch ($category) {
            case 'social':
                return self::getSocialMediaIcons();
            case 'location':
                return self::getLocationIcons();
            case 'general':
                return self::getGeneralIcons();
            default:
                return self::getAllIcons();
        }
    }

    /**
     * Get predefined platform configurations
     */
    public static function getPredefinedPlatforms()
    {
        return [
            'facebook' => [
                'name' => 'Facebook',
                'icon_class' => 'fab fa-facebook-f',
                'color' => '#1877F2',
                'placeholder' => 'https://facebook.com/yourpage'
            ],
            'twitter' => [
                'name' => 'Twitter',
                'icon_class' => 'fab fa-twitter',
                'color' => '#1DA1F2',
                'placeholder' => 'https://twitter.com/yourusername'
            ],
            'instagram' => [
                'name' => 'Instagram',
                'icon_class' => 'fab fa-instagram',
                'color' => '#E4405F',
                'placeholder' => 'https://instagram.com/yourusername'
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'icon_class' => 'fab fa-linkedin-in',
                'color' => '#0A66C2',
                'placeholder' => 'https://linkedin.com/company/yourcompany'
            ],
            'youtube' => [
                'name' => 'YouTube',
                'icon_class' => 'fab fa-youtube',
                'color' => '#FF0000',
                'placeholder' => 'https://youtube.com/channel/yourchannel'
            ],
            'whatsapp' => [
                'name' => 'WhatsApp',
                'icon_class' => 'fab fa-whatsapp',
                'color' => '#25D366',
                'placeholder' => 'https://wa.me/1234567890'
            ],
            'location' => [
                'name' => 'Location',
                'icon_class' => 'fas fa-map-marker-alt',
                'color' => '#DC3545',
                'placeholder' => 'https://maps.google.com/your-location'
            ],
            'store' => [
                'name' => 'Store Location',
                'icon_class' => 'fas fa-store',
                'color' => '#28A745',
                'placeholder' => 'https://maps.google.com/your-store'
            ],
        ];
    }

    /**
     * Get default location icon
     */
    public static function getDefaultLocationIcon()
    {
        return 'fas fa-map-marker-alt';
    }

    /**
     * Get location icon with color
     */
    public static function getLocationIconWithColor($iconClass = null, $color = '#DC3545')
    {
        $icon = $iconClass ?: self::getDefaultLocationIcon();
        return [
            'icon_class' => $icon,
            'color' => $color,
            'style' => "color: {$color};"
        ];
    }

    /**
     * Validate if icon class exists in our predefined icons
     */
    public static function isValidIcon($iconClass)
    {
        return array_key_exists($iconClass, self::getAllIcons());
    }

    /**
     * Get icon name by class
     */
    public static function getIconName($iconClass)
    {
        $allIcons = self::getAllIcons();
        return $allIcons[$iconClass] ?? 'Unknown Icon';
    }

    /**
     * Render icon HTML
     */
    public static function renderIcon($iconClass, $attributes = [])
    {
        $defaultAttributes = [
            'class' => $iconClass,
            'aria-hidden' => 'true'
        ];

        $attributes = array_merge($defaultAttributes, $attributes);
        
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= sprintf(' %s="%s"', $key, htmlspecialchars($value));
        }

        return sprintf('<i%s></i>', $attributeString);
    }

    /**
     * Render location icon with wrapper
     */
    public static function renderLocationIcon($iconClass = null, $color = '#DC3545', $size = '40px')
    {
        $icon = $iconClass ?: self::getDefaultLocationIcon();
        
        return sprintf(
            '<div class="location-icon" style="width: %s; height: %s; background-color: %s; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                <i class="%s text-white"></i>
            </div>',
            $size,
            $size,
            $color,
            $icon
        );
    }
}
