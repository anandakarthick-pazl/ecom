<?php

/**
 * Setup Default App Settings
 * 
 * This script creates default app settings for the application to work properly.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Setting up default AppSettings...\n\n";

try {
    // Get all companies to set up settings for each
    $companies = \App\Models\SuperAdmin\Company::all();
    
    if ($companies->isEmpty()) {
        echo "⚠️  No companies found. Creating global settings...\n";
        $companies = collect([null]); // Create settings with no company_id
    }
    
    $defaultSettings = [
        // Pagination settings
        [
            'key' => 'frontend_pagination_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'pagination',
            'label' => 'Enable Frontend Pagination',
            'description' => 'Enable or disable pagination on frontend product listings'
        ],
        [
            'key' => 'frontend_records_per_page',
            'value' => '12',
            'type' => 'integer',
            'group' => 'pagination',
            'label' => 'Records Per Page',
            'description' => 'Number of products to show per page on frontend'
        ],
        [
            'key' => 'admin_records_per_page',
            'value' => '15',
            'type' => 'integer',
            'group' => 'pagination',
            'label' => 'Admin Records Per Page',
            'description' => 'Number of records to show per page in admin panel'
        ],
        
        // Theme settings
        [
            'key' => 'primary_color',
            'value' => '#10b981',
            'type' => 'string',
            'group' => 'theme',
            'label' => 'Primary Color',
            'description' => 'Main brand color for the application'
        ],
        [
            'key' => 'secondary_color',
            'value' => '#6b7280',
            'type' => 'string',
            'group' => 'theme',
            'label' => 'Secondary Color',
            'description' => 'Secondary brand color for the application'
        ],
        
        // Notification settings
        [
            'key' => 'enable_email_notifications',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'notifications',
            'label' => 'Enable Email Notifications',
            'description' => 'Send email notifications for orders and other events'
        ],
        [
            'key' => 'enable_whatsapp_notifications',
            'value' => 'false',
            'type' => 'boolean',
            'group' => 'notifications',
            'label' => 'Enable WhatsApp Notifications',
            'description' => 'Send WhatsApp notifications for orders'
        ],
        
        // Inventory settings
        [
            'key' => 'low_stock_threshold',
            'value' => '10',
            'type' => 'integer',
            'group' => 'inventory',
            'label' => 'Low Stock Threshold',
            'description' => 'Alert when product stock falls below this number'
        ],
        [
            'key' => 'enable_stock_management',
            'value' => 'true',
            'type' => 'boolean',
            'group' => 'inventory',
            'label' => 'Enable Stock Management',
            'description' => 'Track and manage product inventory'
        ],
        
        // Delivery settings
        [
            'key' => 'minimum_order_amount',
            'value' => '100',
            'type' => 'float',
            'group' => 'delivery',
            'label' => 'Minimum Order Amount',
            'description' => 'Minimum order value required for checkout'
        ],
        [
            'key' => 'delivery_charge',
            'value' => '50',
            'type' => 'float',
            'group' => 'delivery',
            'label' => 'Delivery Charge',
            'description' => 'Standard delivery charge for orders'
        ],
        [
            'key' => 'free_delivery_threshold',
            'value' => '500',
            'type' => 'float',
            'group' => 'delivery',
            'label' => 'Free Delivery Threshold',
            'description' => 'Order amount above which delivery is free'
        ],
    ];
    
    foreach ($companies as $company) {
        $companyId = $company ? $company->id : null;
        $companyName = $company ? $company->name : 'Global';
        
        echo "📊 Setting up settings for: $companyName\n";
        
        foreach ($defaultSettings as $setting) {
            try {
                $data = $setting;
                $data['company_id'] = $companyId;
                
                $existing = \App\Models\AppSetting::where('key', $setting['key'])
                    ->where('company_id', $companyId)
                    ->first();
                
                if (!$existing) {
                    \App\Models\AppSetting::create($data);
                    echo "   ✅ Created: {$setting['key']}\n";
                } else {
                    echo "   ⚠️  Exists: {$setting['key']}\n";
                }
                
            } catch (Exception $e) {
                echo "   ❌ Error creating {$setting['key']}: " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    }
    
    echo "✅ Default app settings setup completed!\n\n";
    
    // Test the AppSetting class methods
    echo "🧪 Testing AppSetting methods...\n";
    
    try {
        $testValue = \App\Models\AppSetting::get('frontend_pagination_enabled', 'false');
        echo "   ✅ AppSetting::get() works: $testValue\n";
        
        \App\Models\AppSetting::set('test_setting', 'test_value', 'string', 'test');
        echo "   ✅ AppSetting::set() works\n";
        
        $groupSettings = \App\Models\AppSetting::getGroup('pagination');
        echo "   ✅ AppSetting::getGroup() works: " . count($groupSettings) . " settings\n";
        
        // Clean up test setting
        \App\Models\AppSetting::where('key', 'test_setting')->delete();
        
    } catch (Exception $e) {
        echo "   ❌ AppSetting method test failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 All done! Your application should now work without AppSetting errors.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure to run 'php artisan migrate' first if you haven't already.\n";
}

?>
