<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdminMenuService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MenuManagementController extends Controller
{
    /**
     * Display the menu management interface
     */
    public function index(): View
    {
        $menuConfig = SuperAdminMenuService::getMenuConfig();
        $menuStats = SuperAdminMenuService::getMenuStats();
        
        // Group menus by category for better organization
        $groupedMenus = $this->groupMenusByCategory($menuConfig);
        
        return view('super-admin.menu-management.index', compact('groupedMenus', 'menuStats'));
    }

    /**
     * Update menu configuration
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'menus' => 'array',
            'menus.*' => 'boolean'
        ]);

        try {
            // Get current config
            $currentConfig = SuperAdminMenuService::getMenuConfig();
            
            // Update only the provided menu items
            $updatedConfig = [];
            foreach ($currentConfig as $key => $value) {
                $updatedConfig[$key] = $request->has("menus.{$key}") ? 
                    (bool) $request->input("menus.{$key}") : false;
            }
            
            SuperAdminMenuService::updateMenuConfig($updatedConfig);
            
            return redirect()->route('super-admin.menu-management.index')
                ->with('success', 'Menu configuration updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.menu-management.index')
                ->with('error', 'Failed to update menu configuration: ' . $e->getMessage());
        }
    }

    /**
     * Enable all menus
     */
    public function enableAll(): RedirectResponse
    {
        try {
            $currentConfig = SuperAdminMenuService::getMenuConfig();
            $enabledConfig = array_map(fn($value) => true, $currentConfig);
            
            SuperAdminMenuService::updateMenuConfig($enabledConfig);
            
            return redirect()->route('super-admin.menu-management.index')
                ->with('success', 'All menus have been enabled!');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.menu-management.index')
                ->with('error', 'Failed to enable all menus: ' . $e->getMessage());
        }
    }

    /**
     * Disable all menus except essential ones
     */
    public function disableAll(): RedirectResponse
    {
        try {
            $currentConfig = SuperAdminMenuService::getMenuConfig();
            
            // Essential menus from user's specific requirements that should always remain enabled
            $essentialMenus = [
                'companies',          // Companies
                'packages',          // Package and plans
                'billing_management', // Billing Management
                'system_settings',   // System Setting
                'cache_management',  // Cache Management
                'view_main_site'     // View main site
            ];
            
            $disabledConfig = [];
            foreach ($currentConfig as $key => $value) {
                $disabledConfig[$key] = in_array($key, $essentialMenus);
            }
            
            SuperAdminMenuService::updateMenuConfig($disabledConfig);
            
            return redirect()->route('super-admin.menu-management.index')
                ->with('success', 'All non-essential menus have been disabled! Only core menus from your requirements remain enabled.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.menu-management.index')
                ->with('error', 'Failed to disable menus: ' . $e->getMessage());
        }
    }

    /**
     * Reset to recommended configuration
     */
    public function resetToRecommended(): RedirectResponse
    {
        try {
            // CUSTOM CONFIGURATION: Only specific menus enabled as requested by user
            $recommendedConfig = [
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
                'cache_management' => false,     // ✅ ENABLED: Cache Management
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
            
            SuperAdminMenuService::updateMenuConfig($recommendedConfig);
            
            return redirect()->route('super-admin.menu-management.index')
                ->with('success', 'Menu configuration reset to your custom recommended settings! Only the requested menus are now enabled.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.menu-management.index')
                ->with('error', 'Failed to reset configuration: ' . $e->getMessage());
        }
    }

    /**
     * Group menus by category for better organization
     */
    private function groupMenusByCategory(array $menuConfig): array
    {
        $categories = [
            'Main Dashboard' => [
                'dashboard', 'analytics_overview'
            ],
            'Company & Tenant Management' => [
                'companies', 'subscriptions', 'subscriptions_expiring', 'packages',
                'domain_management', 'multi_tenant_config', 'resource_allocation'
            ],
            'Financial & Billing Management' => [
                'billing_management', 'billing_reports', 'invoice_generator', 'revenue_analytics',
                'payment_gateway', 'subscription_billing', 'tax_configuration', 
                'discount_management', 'currency_settings'
            ],
            'Theme & Design Management' => [
                'theme_library', 'theme_assignment', 'theme_statistics', 'theme_reports'
            ],
            'Data Management & Transfer' => [
                'data_import', 'import_history', 'data_export', 'database_manager',
                'backup_management', 'data_migration', 'data_synchronization', 
                'bulk_operations', 'data_cleanup'
            ],
            'Customer Support & Communication' => [
                'support_tickets', 'whatsapp_config', 'email_settings', 'email_templates',
                'notifications', 'live_chat_config', 'sms_configuration', 
                'push_notifications', 'social_media_integration', 'marketing_automation'
            ],
            'Content & Website Management' => [
                'landing_page', 'hero_section', 'features_section', 'pricing_section',
                'contact_section', 'blog_management', 'media_library'
            ],
            'User & Security Management' => [
                'user_management', 'admin_users', 'blocked_users', 'api_keys',
                'security_settings', 'access_control', 'role_management'
            ],
            'System Configuration' => [
                'system_settings', 'general_settings', 'cache_management', 'system_health',
                'performance_monitor', 'task_scheduler', 'queue_monitor', 'application_config',
                'environment_settings', 'feature_flags', 'maintenance_mode', 'localization'
            ],
            'Logs & Monitoring' => [
                'system_logs', 'error_logs', 'security_logs', 'activity_logs'
            ],
            'Integration & Third-Party Services' => [
                'api_management', 'api_documentation', 'api_webhooks', 'integrations',
                'mobile_app_settings'
            ],
            'Reports & Analytics' => [
                'analytics_dashboard', 'user_analytics', 'sales_reports', 'growth_metrics',
                'custom_reports'
            ],
            'System Debug & Development' => [
                'debug_console', 'artisan_commands', 'database_query_builder', 'version_info'
            ],
            'Quick Actions' => [
                'view_main_site', 'quick_setup_wizard', 'deployment_tools'
            ]
        ];

        $groupedMenus = [];
        foreach ($categories as $categoryName => $menuKeys) {
            $groupedMenus[$categoryName] = [];
            foreach ($menuKeys as $key) {
                if (isset($menuConfig[$key])) {
                    $groupedMenus[$categoryName][$key] = [
                        'enabled' => $menuConfig[$key],
                        'label' => $this->getMenuLabel($key)
                    ];
                }
            }
        }

        return $groupedMenus;
    }

    /**
     * Get human-readable label for menu key
     */
    private function getMenuLabel(string $key): string
    {
        $labels = [
            'dashboard' => 'Main Dashboard',
            'analytics_overview' => 'Analytics Overview',
            'companies' => 'Companies',
            'subscriptions' => 'Subscriptions',
            'subscriptions_expiring' => 'Expiring Soon',
            'packages' => 'Packages & Plans',
            'domain_management' => 'Domain Management',
            'multi_tenant_config' => 'Multi-Tenant Config',
            'resource_allocation' => 'Resource Allocation',
            'billing_management' => 'Billing Management',
            'billing_reports' => 'Billing Reports',
            'invoice_generator' => 'Invoice Generator',
            'revenue_analytics' => 'Revenue Analytics',
            'payment_gateway' => 'Payment Gateway',
            'subscription_billing' => 'Subscription Billing',
            'tax_configuration' => 'Tax Configuration',
            'discount_management' => 'Discount Management',
            'currency_settings' => 'Currency Settings',
            'theme_library' => 'Theme Library',
            'theme_assignment' => 'Theme Assignment',
            'theme_statistics' => 'Theme Statistics',
            'theme_reports' => 'Theme Reports',
            'data_import' => 'Data Import',
            'import_history' => 'Import History',
            'data_export' => 'Data Export',
            'database_manager' => 'Database Manager',
            'backup_management' => 'Backup Management',
            'data_migration' => 'Data Migration',
            'data_synchronization' => 'Data Synchronization',
            'bulk_operations' => 'Bulk Operations',
            'data_cleanup' => 'Data Cleanup',
            'support_tickets' => 'Support Tickets',
            'whatsapp_config' => 'WhatsApp Config',
            'email_settings' => 'Email Settings',
            'email_templates' => 'Email Templates',
            'notifications' => 'Notifications',
            'live_chat_config' => 'Live Chat Config',
            'sms_configuration' => 'SMS Configuration',
            'push_notifications' => 'Push Notifications',
            'social_media_integration' => 'Social Media Integration',
            'marketing_automation' => 'Marketing Automation',
            'landing_page' => 'Landing Page',
            'hero_section' => 'Hero Section',
            'features_section' => 'Features Section',
            'pricing_section' => 'Pricing Section',
            'contact_section' => 'Contact Section',
            'blog_management' => 'Blog Management',
            'media_library' => 'Media Library',
            'user_management' => 'User Management',
            'admin_users' => 'Admin Users',
            'blocked_users' => 'Blocked Users',
            'api_keys' => 'API Keys',
            'security_settings' => 'Security Settings',
            'access_control' => 'Access Control',
            'role_management' => 'Role Management',
            'system_settings' => 'System Settings',
            'general_settings' => 'General Settings',
            'cache_management' => 'Cache Management',
            'system_health' => 'System Health',
            'performance_monitor' => 'Performance Monitor',
            'task_scheduler' => 'Task Scheduler',
            'queue_monitor' => 'Queue Monitor',
            'application_config' => 'Application Config',
            'environment_settings' => 'Environment Settings',
            'feature_flags' => 'Feature Flags',
            'maintenance_mode' => 'Maintenance Mode',
            'localization' => 'Localization',
            'system_logs' => 'System Logs',
            'error_logs' => 'Error Logs',
            'security_logs' => 'Security Logs',
            'activity_logs' => 'Activity Logs',
            'api_management' => 'API Management',
            'api_documentation' => 'API Documentation',
            'api_webhooks' => 'Webhooks',
            'integrations' => 'Integrations',
            'mobile_app_settings' => 'Mobile App Settings',
            'analytics_dashboard' => 'Analytics Dashboard',
            'user_analytics' => 'User Analytics',
            'sales_reports' => 'Sales Reports',
            'growth_metrics' => 'Growth Metrics',
            'custom_reports' => 'Custom Reports',
            'debug_console' => 'Debug Console',
            'artisan_commands' => 'Artisan Commands',
            'database_query_builder' => 'DB Query Builder',
            'version_info' => 'Version Info',
            'view_main_site' => 'View Main Site',
            'quick_setup_wizard' => 'Quick Setup Wizard',
            'deployment_tools' => 'Deployment Tools',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }
}
