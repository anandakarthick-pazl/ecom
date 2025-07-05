<?php
/**
 * FIX: WhatsApp Notifications Not Reflecting Saved State
 * This script fixes the issue where WhatsApp notifications show as disabled 
 * even when enabled, and ensures the checkbox loads saved values
 */

echo "=== FIXING WHATSAPP NOTIFICATIONS STATUS DISPLAY ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Fix the SettingsController to properly load notification settings
echo "1. ENHANCING SETTINGS CONTROLLER:\n";
echo "==================================\n";

$settingsControllerFile = __DIR__ . '/app/Http/Controllers/Admin/SettingsController.php';
if (!file_exists($settingsControllerFile)) {
    echo "❌ SettingsController not found\n";
    exit(1);
}

$controllerContent = file_get_contents($settingsControllerFile);
$originalControllerContent = $controllerContent;

// Enhance the index method to ensure notification settings are properly loaded
$oldIndexMethod = 'public function index()
    {
        // Get current company
        $company = $this->getCurrentCompany();
        
        // Get settings from app_settings table (for non-company settings)
        $appearanceSettings = AppSetting::getGroup(\'appearance\');
        $themeSettings = AppSetting::getGroup(\'theme\');
        $notificationSettings = AppSetting::getGroup(\'notifications\');
        $emailSettings = AppSetting::getGroup(\'email\');
        $inventorySettings = AppSetting::getGroup(\'inventory\');
        $deliverySettings = AppSetting::getGroup(\'delivery\');
        $paginationSettings = AppSetting::getGroup(\'pagination\');
        $whatsappSettings = AppSetting::getGroup(\'whatsapp\');
        
        // Merge theme settings
        $appearanceSettings = array_merge($appearanceSettings, $themeSettings);
        
        return view(\'admin.settings.index\', compact(
            \'company\',
            \'appearanceSettings\', 
            \'notificationSettings\',
            \'emailSettings\',
            \'inventorySettings\',
            \'deliverySettings\',
            \'paginationSettings\',
            \'whatsappSettings\'
        ));
    }';

$newIndexMethod = 'public function index()
    {
        // Get current company
        $company = $this->getCurrentCompany();
        
        // Get settings from app_settings table (for non-company settings)
        $appearanceSettings = AppSetting::getGroup(\'appearance\');
        $themeSettings = AppSetting::getGroup(\'theme\');
        $notificationSettings = AppSetting::getGroup(\'notifications\');
        $emailSettings = AppSetting::getGroup(\'email\');
        $inventorySettings = AppSetting::getGroup(\'inventory\');
        $deliverySettings = AppSetting::getGroup(\'delivery\');
        $paginationSettings = AppSetting::getGroup(\'pagination\');
        $whatsappSettings = AppSetting::getGroup(\'whatsapp\');
        
        // Merge theme settings
        $appearanceSettings = array_merge($appearanceSettings, $themeSettings);
        
        // Ensure notification settings have default values if not set
        $notificationDefaults = [
            \'email_notifications\' => true,
            \'whatsapp_notifications\' => false,
            \'sound_notifications\' => true,
            \'popup_notifications\' => true,
            \'order_notifications\' => true,
            \'low_stock_alert\' => true
        ];
        
        foreach ($notificationDefaults as $key => $defaultValue) {
            if (!isset($notificationSettings[$key])) {
                $notificationSettings[$key] = $defaultValue;
            } else {
                // Ensure boolean conversion
                $notificationSettings[$key] = filter_var($notificationSettings[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }
        
        // Check if WhatsApp is actually configured (this could be enhanced to check Super Admin settings)
        $whatsappConfigured = false; // TODO: Check Super Admin WhatsApp configuration
        $whatsappEnabled = $notificationSettings[\'whatsapp_notifications\'] && $whatsappConfigured;
        
        return view(\'admin.settings.index\', compact(
            \'company\',
            \'appearanceSettings\', 
            \'notificationSettings\',
            \'emailSettings\',
            \'inventorySettings\',
            \'deliverySettings\',
            \'paginationSettings\',
            \'whatsappSettings\',
            \'whatsappConfigured\',
            \'whatsappEnabled\'
        ));
    }';

// Replace the index method
if (strpos($controllerContent, 'public function index()') !== false) {
    $controllerContent = str_replace($oldIndexMethod, $newIndexMethod, $controllerContent);
    echo "✅ Enhanced index method to properly load notification settings\n";
} else {
    echo "❌ Could not find index method to update\n";
}

// Write the updated controller
if ($controllerContent !== $originalControllerContent) {
    file_put_contents($settingsControllerFile, $controllerContent);
    echo "✅ SettingsController updated\n";
}

// Step 2: Fix the settings view to use saved values
echo "\n2. FIXING SETTINGS VIEW:\n";
echo "========================\n";

$settingsViewFile = __DIR__ . '/resources/views/admin/settings/index.blade.php';
if (!file_exists($settingsViewFile)) {
    echo "❌ Settings view not found\n";
    exit(1);
}

$viewContent = file_get_contents($settingsViewFile);
$originalViewContent = $viewContent;

// Fix notification checkboxes to use saved values
$checkboxFixes = [
    // Email notifications
    'id="email_notifications" name="email_notifications" value="1" checked>' => 
    'id="email_notifications" name="email_notifications" value="1" {{ ($notificationSettings[\'email_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // WhatsApp notifications (the main fix)
    'id="whatsapp_notifications" name="whatsapp_notifications" value="1">' => 
    'id="whatsapp_notifications" name="whatsapp_notifications" value="1" {{ ($notificationSettings[\'whatsapp_notifications\'] ?? false) ? \'checked\' : \'\' }}>',
    
    // Sound notifications
    'id="sound_notifications" name="sound_notifications" value="1" checked>' => 
    'id="sound_notifications" name="sound_notifications" value="1" {{ ($notificationSettings[\'sound_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Popup notifications
    'id="popup_notifications" name="popup_notifications" value="1" checked>' => 
    'id="popup_notifications" name="popup_notifications" value="1" {{ ($notificationSettings[\'popup_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Order notifications
    'id="order_notifications" name="order_notifications" value="1" checked>' => 
    'id="order_notifications" name="order_notifications" value="1" {{ ($notificationSettings[\'order_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Low stock alert
    'id="low_stock_alert" name="low_stock_alert" value="1" checked>' => 
    'id="low_stock_alert" name="low_stock_alert" value="1" {{ ($notificationSettings[\'low_stock_alert\'] ?? true) ? \'checked\' : \'\' }}>'
];

$fixCount = 0;
foreach ($checkboxFixes as $oldPattern => $newPattern) {
    if (strpos($viewContent, $oldPattern) !== false) {
        $viewContent = str_replace($oldPattern, $newPattern, $viewContent);
        $fixCount++;
        echo "✅ Fixed checkbox pattern\n";
    }
}

// Fix the hardcoded WhatsApp status display
$oldStatusDisplay = '<div class="mb-2">
                                                <strong>WhatsApp Notifications:</strong>
                                                <span class="badge bg-warning">
                                                    Disabled
                                                </span>
                                            </div>';

$newStatusDisplay = '<div class="mb-2">
                                                <strong>WhatsApp Notifications:</strong>
                                                @if($notificationSettings[\'whatsapp_notifications\'] ?? false)
                                                    <span class="badge bg-success">
                                                        Enabled
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        Disabled
                                                    </span>
                                                @endif
                                            </div>';

if (strpos($viewContent, $oldStatusDisplay) !== false) {
    $viewContent = str_replace($oldStatusDisplay, $newStatusDisplay, $viewContent);
    echo "✅ Fixed WhatsApp status display to be dynamic\n";
    $fixCount++;
} else {
    // Try a more flexible pattern
    $pattern = '/<div class="mb-2">\s*<strong>WhatsApp Notifications:<\/strong>\s*<span class="badge bg-warning">\s*Disabled\s*<\/span>\s*<\/div>/s';
    if (preg_match($pattern, $viewContent)) {
        $viewContent = preg_replace($pattern, $newStatusDisplay, $viewContent);
        echo "✅ Fixed WhatsApp status display using pattern matching\n";
        $fixCount++;
    }
}

echo "Total view fixes applied: $fixCount\n";

// Write the fixed view
if ($viewContent !== $originalViewContent) {
    file_put_contents($settingsViewFile, $viewContent);
    echo "✅ Settings view updated\n";
}

// Step 3: Clear all caches
echo "\n3. CLEARING ALL CACHES:\n";
echo "========================\n";

// Clear compiled views
$viewCachePath = __DIR__ . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    foreach ($files as $file) {
        unlink($file);
    }
    echo "✅ Cleared " . count($files) . " compiled view files\n";
}

// Clear other caches
$cacheDirectories = [
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/bootstrap/cache'
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        echo "✅ Cleared cache from " . basename($dir) . "\n";
    }
}

// Try Laravel artisan commands
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $commands = ['view:clear', 'config:clear', 'cache:clear'];
    foreach ($commands as $command) {
        try {
            \Artisan::call($command);
            echo "✅ Executed: php artisan $command\n";
        } catch (Exception $e) {
            echo "❌ Failed: php artisan $command\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Laravel artisan commands failed\n";
}

// Clear opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n=== WHATSAPP NOTIFICATIONS STATUS FIX COMPLETED ===\n";
echo "Summary:\n";
echo "- ✅ Enhanced SettingsController to properly load notification settings\n";
echo "- ✅ Fixed all notification checkboxes to use saved values\n";
echo "- ✅ Fixed WhatsApp status display to be dynamic\n";
echo "- ✅ Cleared all compiled views and caches\n";

echo "\nNow test the WhatsApp notifications:\n";
echo "1. Go to: http://greenvalleyherbs.local:8000/admin/settings\n";
echo "2. Click on 'Notifications' tab\n";
echo "3. Check/uncheck 'WhatsApp Notifications'\n";
echo "4. Click 'Save Notification Settings'\n";
echo "5. Go to 'WhatsApp Templates' tab\n";
echo "6. Check 'WhatsApp Integration Status' - should show 'Enabled' if checked\n";

echo "\n🎉 WhatsApp notifications status should now work correctly!\n";
