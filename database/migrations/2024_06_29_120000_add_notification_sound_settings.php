<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Insert default notification settings if they don't exist
        $defaultSettings = [
            ['key' => 'notification_sound', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications'],
            ['key' => 'order_notification_sound', 'value' => '/admin/sounds/notification.mp3', 'type' => 'string', 'group' => 'notifications'],
            ['key' => 'low_stock_threshold', 'value' => '10', 'type' => 'integer', 'group' => 'inventory'],
            ['key' => 'smtp_host', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => '', 'type' => 'string', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => '', 'type' => 'string', 'group' => 'email'],
        ];

        foreach ($defaultSettings as $setting) {
            \App\Models\AppSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down()
    {
        $settingsToRemove = [
            'notification_sound', 'order_notification_sound', 'low_stock_threshold',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 
            'smtp_encryption', 'mail_from_address', 'mail_from_name'
        ];

        \App\Models\AppSetting::whereIn('key', $settingsToRemove)->delete();
    }
};
