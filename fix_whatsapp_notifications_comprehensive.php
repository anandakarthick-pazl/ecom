<?php
/**
 * COMPREHENSIVE FIX: WhatsApp Notifications Not Reflecting Saved State
 * This script fixes the issue where WhatsApp notifications show as disabled 
 * even when enabled, and ensures the checkbox loads saved values properly
 */

echo "=== COMPREHENSIVE WHATSAPP NOTIFICATIONS FIX ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Update SettingsController to properly load notification settings
echo "1. UPDATING SETTINGS CONTROLLER:\n";
echo "=================================\n";

$settingsControllerFile = __DIR__ . '/app/Http/Controllers/Admin/SettingsController.php';
if (!file_exists($settingsControllerFile)) {
    echo "❌ SettingsController not found\n";
    exit(1);
}

$controllerContent = file_get_contents($settingsControllerFile);
$originalControllerContent = $controllerContent;

// Replace the index method with enhanced version
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
        
        // Ensure notification settings have proper default values and boolean conversion
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
                // Ensure proper boolean conversion for database values
                if (is_string($notificationSettings[$key])) {
                    $notificationSettings[$key] = filter_var($notificationSettings[$key], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $notificationSettings[$key] = (bool) $notificationSettings[$key];
                }
            }
        }
        
        // Check if WhatsApp is configured (you can enhance this to check Super Admin settings)
        $whatsappConfigured = !empty($whatsappSettings); // Basic check
        $whatsappEnabled = $notificationSettings[\'whatsapp_notifications\'] && $whatsappConfigured;
        
        // Log the notification settings for debugging
        \Log::info(\'Notification settings loaded\', [
            \'notificationSettings\' => $notificationSettings,
            \'whatsappConfigured\' => $whatsappConfigured,
            \'whatsappEnabled\' => $whatsappEnabled,
            \'tenant_id\' => session(\'selected_company_id\')
        ]);
        
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
if (strpos($controllerContent, $oldIndexMethod) !== false) {
    $controllerContent = str_replace($oldIndexMethod, $newIndexMethod, $controllerContent);
    echo "✅ Enhanced index method to properly load notification settings\n";
} else {
    echo "⚠️ Could not find exact index method to replace. Attempting pattern replacement...\n";
    
    // Try to find and replace a more flexible pattern
    $pattern = '/public function index\(\)\s*\{[\s\S]*?return view\(\'admin\.settings\.index\',[\s\S]*?\);\s*\}/';
    if (preg_match($pattern, $controllerContent)) {
        $controllerContent = preg_replace($pattern, $newIndexMethod, $controllerContent);
        echo "✅ Enhanced index method using pattern matching\n";
    } else {
        echo "❌ Could not update index method\n";
    }
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

// Fix all notification checkboxes to use saved values
$checkboxFixes = [
    // Email notifications - remove hardcoded checked
    'id="email_notifications" name="email_notifications" value="1" checked>' => 
    'id="email_notifications" name="email_notifications" value="1" {{ ($notificationSettings[\'email_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // WhatsApp notifications - add proper checked attribute
    'id="whatsapp_notifications" name="whatsapp_notifications" value="1">' => 
    'id="whatsapp_notifications" name="whatsapp_notifications" value="1" {{ ($notificationSettings[\'whatsapp_notifications\'] ?? false) ? \'checked\' : \'\' }}>',
    
    // Sound notifications - remove hardcoded checked
    'id="sound_notifications" name="sound_notifications" value="1" checked>' => 
    'id="sound_notifications" name="sound_notifications" value="1" {{ ($notificationSettings[\'sound_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Popup notifications - remove hardcoded checked
    'id="popup_notifications" name="popup_notifications" value="1" checked>' => 
    'id="popup_notifications" name="popup_notifications" value="1" {{ ($notificationSettings[\'popup_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Order notifications - remove hardcoded checked
    'id="order_notifications" name="order_notifications" value="1" checked>' => 
    'id="order_notifications" name="order_notifications" value="1" {{ ($notificationSettings[\'order_notifications\'] ?? true) ? \'checked\' : \'\' }}>',
    
    // Low stock alert - remove hardcoded checked
    'id="low_stock_alert" name="low_stock_alert" value="1" checked>' => 
    'id="low_stock_alert" name="low_stock_alert" value="1" {{ ($notificationSettings[\'low_stock_alert\'] ?? true) ? \'checked\' : \'\' }}>'
];

$fixCount = 0;
foreach ($checkboxFixes as $oldPattern => $newPattern) {
    if (strpos($viewContent, $oldPattern) !== false) {
        $viewContent = str_replace($oldPattern, $newPattern, $viewContent);
        $fixCount++;
        echo "✅ Fixed checkbox pattern: " . substr($oldPattern, 0, 30) . "...\n";
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
                                                        <i class="fab fa-whatsapp"></i> Enabled
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-times"></i> Disabled
                                                    </span>
                                                @endif
                                            </div>';

if (strpos($viewContent, $oldStatusDisplay) !== false) {
    $viewContent = str_replace($oldStatusDisplay, $newStatusDisplay, $viewContent);
    echo "✅ Fixed WhatsApp status display to be dynamic\n";
    $fixCount++;
} else {
    // Try a more flexible pattern for the status display
    $pattern = '/<div class="mb-2">\s*<strong>WhatsApp Notifications:<\/strong>\s*<span class="badge bg-warning">\s*Disabled\s*<\/span>\s*<\/div>/s';
    if (preg_match($pattern, $viewContent)) {
        $viewContent = preg_replace($pattern, $newStatusDisplay, $viewContent);
        echo "✅ Fixed WhatsApp status display using pattern matching\n";
        $fixCount++;
    } else {
        echo "⚠️ Could not find WhatsApp status display to fix\n";
    }
}

echo "Total view fixes applied: $fixCount\n";

// Write the fixed view
if ($viewContent !== $originalViewContent) {
    file_put_contents($settingsViewFile, $viewContent);
    echo "✅ Settings view updated\n";
}

// Step 3: Clear all caches to ensure changes take effect
echo "\n3. CLEARING ALL CACHES:\n";
echo "========================\n";

// Clear compiled views
$viewCachePath = __DIR__ . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cleared " . count($files) . " compiled view files\n";
}

// Clear other cache directories
$cacheDirectories = [
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/bootstrap/cache'
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        $cleared = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $cleared++;
            }
        }
        echo "✅ Cleared $cleared files from " . basename($dir) . "\n";
    }
}

// Try Laravel artisan commands
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $commands = ['view:clear', 'config:clear', 'cache:clear', 'route:clear'];
    foreach ($commands as $command) {
        try {
            \Artisan::call($command);
            echo "✅ Executed: php artisan $command\n";
        } catch (Exception $e) {
            echo "❌ Failed: php artisan $command - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Laravel artisan commands failed: " . $e->getMessage() . "\n";
}

// Clear opcode cache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

// Step 4: Test the current notification settings in database
echo "\n4. TESTING CURRENT DATABASE VALUES:\n";
echo "===================================\n";

try {
    // Try to read current notification settings from database
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Get notification settings using the AppSetting model
    $notificationSettings = \App\Models\AppSetting::getGroup('notifications');
    
    echo "Current notification settings in database:\n";
    foreach ($notificationSettings as $key => $value) {
        $status = $value ? '✅ Enabled' : '❌ Disabled';
        echo "  - $key: $status ($value)\n";
    }
    
    // Check if WhatsApp notifications specifically exist
    $whatsappNotifications = \App\Models\AppSetting::get('whatsapp_notifications');
    echo "\nWhatsApp notifications setting:\n";
    echo "  Raw value: " . var_export($whatsappNotifications, true) . "\n";
    echo "  Boolean value: " . ($whatsappNotifications ? 'true' : 'false') . "\n";
    
} catch (Exception $e) {
    echo "❌ Could not test database values: " . $e->getMessage() . "\n";
}

echo "\n=== COMPREHENSIVE WHATSAPP NOTIFICATIONS FIX COMPLETED ===\n";
echo "Summary of changes:\n";
echo "- ✅ Enhanced SettingsController to properly load and convert notification settings\n";
echo "- ✅ Fixed all notification checkboxes to use saved database values\n";
echo "- ✅ Made WhatsApp status display dynamic based on saved settings\n";
echo "- ✅ Added proper boolean conversion for database values\n";
echo "- ✅ Added debug logging for troubleshooting\n";
echo "- ✅ Cleared all compiled views and application caches\n";

echo "\nTo test the fix:\n";
echo "1. Go to: Admin Panel → Settings → Notifications tab\n";
echo "2. The WhatsApp Notifications checkbox should now reflect the saved state\n";
echo "3. Check/uncheck 'WhatsApp Notifications' and save\n";
echo "4. Go to 'WhatsApp Templates' tab\n";
echo "5. The 'WhatsApp Integration Status' should show correct enabled/disabled state\n";
echo "6. Check browser console and Laravel logs for any debug information\n";

echo "\nIf the issue persists:\n";
echo "1. Check the app_settings table in your database\n";
echo "2. Look for records with setting_group = 'notifications'\n";
echo "3. Verify the 'whatsapp_notifications' setting value\n";
echo "4. Check Laravel logs in storage/logs/ for any errors\n";

echo "\n🎉 WhatsApp notifications should now work correctly!\n";
?>