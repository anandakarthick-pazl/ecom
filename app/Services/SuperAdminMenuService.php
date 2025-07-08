<?php

namespace App\Services;

/**
 * Super Admin Menu Configuration Service
 * This service manages which menu items are displayed in the super admin panel
 */
class SuperAdminMenuService
{
    /**
     * Menu configuration array
     * Set to true to show the menu item, false to hide it
     * 
     * CUSTOM CONFIGURATION: Only specific menus enabled as requested
     */
    protected static $menuConfig = [
        // Main Dashboard - Disabled per request
        'dashboard' => false,
        'analytics_overview' => false,

        // Company & Tenant Management - Only Companies enabled
        'companies' => true,  // ✅ ENABLED: Companies
        'subscriptions' => false,
        'subscriptions_expiring' => false,
        'packages' => true,  // ✅ ENABLED: Package and plans
        'domain_management' => false,
        'multi_tenant_config' => false,
        'resource_allocation' => false,

        // Financial & Billing Management - Only specific items enabled
        'billing_management' => true,  // ✅ ENABLED: Billing Management
        'billing_reports' => true,     // ✅ ENABLED: Billing Reports
        'invoice_generator' => false,
        'revenue_analytics' => false,
        'payment_gateway' => false,
        'subscription_billing' => false,
        'tax_configuration' => false,
        'discount_management' => false,
        'currency_settings' => false,

        // Theme & Design Management - Specific items enabled
        'theme_library' => true,      // ✅ ENABLED: Themes Management
        'theme_assignment' => true,   // ✅ ENABLED: Theme Assignment Management
        'theme_statistics' => false,
        'theme_reports' => false,

        // Data Management & Transfer - Only Data Import enabled
        'data_import' => true,        // ✅ ENABLED: Data Import Management
        'import_history' => false,
        'data_export' => false,
        'database_manager' => false,
        'backup_management' => false,
        'data_migration' => false,
        'data_synchronization' => false,
        'bulk_operations' => false,
        'data_cleanup' => false,
        
        // Storage Management - NEW SECTION
        'storage_management' => true,   // ✅ ENABLED: Storage Management
        'local_storage' => true,        // ✅ ENABLED: Local File Storage
        's3_storage' => true,           // ✅ ENABLED: AWS S3 Storage

        // Customer Support & Communication - Specific items enabled
        'support_tickets' => true,    // ✅ ENABLED: Support Tickets
        'whatsapp_config' => true,    // ✅ ENABLED: WhatsApp config
        'email_settings' => true,     // ✅ ENABLED: Email Settings
        'email_templates' => false,
        'notifications' => false,
        'live_chat_config' => false,
        'sms_configuration' => false,
        'push_notifications' => false,
        'social_media_integration' => false,
        'marketing_automation' => false,

        // Content & Website Management - Disabled
        'landing_page' => false,
        'hero_section' => false,
        'features_section' => false,
        'pricing_section' => false,
        'contact_section' => false,
        'blog_management' => false,
        'media_library' => false,

        // User & Security Management - Disabled
        'user_management' => false,
        'admin_users' => false,
        'blocked_users' => false,
        'api_keys' => false,
        'security_settings' => false,
        'access_control' => false,
        'role_management' => false,

        // System Configuration - Specific items enabled
        'system_settings' => true,      // ✅ ENABLED: System Setting
        'general_settings' => true,     // ✅ ENABLED: General Settings
        'cache_management' => true,     // ✅ ENABLED: Cache Management
        'system_health' => false,
        'performance_monitor' => false,
        'task_scheduler' => false,
        'queue_monitor' => false,
        'application_config' => false,
        'environment_settings' => false,
        'feature_flags' => false,
        'maintenance_mode' => false,
        'localization' => false,

        // Logs & Monitoring - Disabled
        'system_logs' => false,
        'error_logs' => false,
        'security_logs' => false,
        'activity_logs' => false,

        // Integration & Third-Party Services - Disabled
        'api_management' => false,
        'api_documentation' => false,
        'api_webhooks' => false,
        'integrations' => false,
        'mobile_app_settings' => false,

        // Reports & Analytics - Disabled
        'analytics_dashboard' => false,
        'user_analytics' => false,
        'sales_reports' => false,
        'growth_metrics' => false,
        'custom_reports' => false,

        // System Debug & Development - Disabled
        'debug_console' => false,
        'artisan_commands' => false,
        'database_query_builder' => false,
        'version_info' => false,

        // Quick Actions - Only View main site enabled
        'view_main_site' => true,      // ✅ ENABLED: View main site
        'quick_setup_wizard' => false,
        'deployment_tools' => false,
    ];

    /**
     * Check if a menu item should be displayed
     */
    public static function isMenuEnabled(string $menuKey): bool
    {
        return self::$menuConfig[$menuKey] ?? false;
    }

    /**
     * Get all enabled menu items
     */
    public static function getEnabledMenus(): array
    {
        return array_filter(self::$menuConfig, function($enabled) {
            return $enabled === true;
        });
    }

    /**
     * Get all disabled menu items
     */
    public static function getDisabledMenus(): array
    {
        return array_filter(self::$menuConfig, function($enabled) {
            return $enabled === false;
        });
    }

    /**
     * Enable a menu item
     */
    public static function enableMenu(string $menuKey): void
    {
        self::$menuConfig[$menuKey] = true;
    }

    /**
     * Disable a menu item
     */
    public static function disableMenu(string $menuKey): void
    {
        self::$menuConfig[$menuKey] = false;
    }

    /**
     * Get menu configuration for admin interface
     */
    public static function getMenuConfig(): array
    {
        return self::$menuConfig;
    }

    /**
     * Update menu configuration
     */
    public static function updateMenuConfig(array $config): void
    {
        self::$menuConfig = array_merge(self::$menuConfig, $config);
    }

    /**
     * Check if any menu in an array is enabled
     */
    public static function hasAnyMenuEnabled(array $menuKeys): bool
    {
        foreach ($menuKeys as $key) {
            if (self::isMenuEnabled($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get menu statistics
     */
    public static function getMenuStats(): array
    {
        $total = count(self::$menuConfig);
        $enabled = count(self::getEnabledMenus());
        $disabled = count(self::getDisabledMenus());

        return [
            'total' => $total,
            'enabled' => $enabled,
            'disabled' => $disabled,
            'enabled_percentage' => round(($enabled / $total) * 100, 1),
            'disabled_percentage' => round(($disabled / $total) * 100, 1),
        ];
    }
}
