<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get dynamic values from super admin settings or use generic defaults
        $superAdminSettings = cache('super_admin_settings', []);
        $defaultSiteName = $superAdminSettings['site_name'] ?? config('app.name', 'Your Store Name');
        $defaultEmail = $superAdminSettings['admin_email'] ?? 'admin@example.com';
        $defaultPhone = $superAdminSettings['company_phone'] ?? '';
        $defaultAddress = $superAdminSettings['company_address'] ?? '';
        $defaultColor = $superAdminSettings['primary_color'] ?? '#667eea';
        
        $defaultSettings = [
            // General Settings
            ['key' => 'company_name', 'value' => $defaultSiteName, 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_email', 'value' => $defaultEmail, 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => $defaultPhone, 'type' => 'string', 'group' => 'general'],
            ['key' => 'company_address', 'value' => $defaultAddress, 'type' => 'string', 'group' => 'general'],
            
            // Appearance Settings
            ['key' => 'primary_color', 'value' => $defaultColor, 'type' => 'string', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#6b8e23', 'type' => 'string', 'group' => 'appearance'],
            ['key' => 'sidebar_color', 'value' => $defaultColor, 'type' => 'string', 'group' => 'appearance'],
            ['key' => 'theme_mode', 'value' => 'light', 'type' => 'string', 'group' => 'appearance'],
            
            // Notification Settings
            ['key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'sound_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'popup_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'order_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'low_stock_alert', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            
            // Email Settings
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => $defaultEmail, 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => $defaultSiteName, 'type' => 'string', 'group' => 'email'],
            
            // Inventory Settings
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'integer', 'group' => 'inventory'],
        ];

        foreach ($defaultSettings as $setting) {
            AppSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Default settings created successfully!');
    }
}
