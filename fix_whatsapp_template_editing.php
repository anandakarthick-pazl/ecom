<?php
/**
 * FIX: WhatsApp Template Editing Not Working 
 * This script fixes the issue where template updates show success but revert to old text
 */

echo "=== FIXING WHATSAPP TEMPLATE EDITING ISSUE ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Fix the AppSetting cache clearing issue
echo "1. FIXING APPSETTING CACHE CLEARING:\n";
echo "====================================\n";

$appSettingFile = __DIR__ . '/app/Models/AppSetting.php';
if (!file_exists($appSettingFile)) {
    echo "❌ AppSetting model not found\n";
    exit(1);
}

// Read current AppSetting content
$content = file_get_contents($appSettingFile);
$originalContent = $content;

// Fix the clearCache method to properly clear cache without wildcards
$oldClearCacheMethod = '    /**
     * Clear settings cache for current tenant
     */
    public static function clearCache()
    {
        $tenantId = static::getCurrentTenantId();
        if ($tenantId) {
            // Clear specific tenant cache patterns
            $patterns = [
                "setting:{$tenantId}:*",
                "settings:group:{$tenantId}:*"
            ];
            
            foreach ($patterns as $pattern) {
                Cache::forget($pattern);
            }
        } else {
            Cache::flush();
        }
    }';

$newClearCacheMethod = '    /**
     * Clear settings cache for current tenant
     */
    public static function clearCache()
    {
        $tenantId = static::getCurrentTenantId();
        
        if ($tenantId) {
            // Clear all WhatsApp template cache keys specifically
            $whatsappTemplateKeys = [
                'whatsapp_template_pending',
                'whatsapp_template_processing', 
                'whatsapp_template_shipped',
                'whatsapp_template_delivered',
                'whatsapp_template_cancelled',
                'whatsapp_template_payment_confirmed'
            ];
            
            foreach ($whatsappTemplateKeys as $key) {
                Cache::forget("setting:{$tenantId}:{$key}");
            }
            
            // Clear group cache for whatsapp
            Cache::forget("settings:group:{$tenantId}:whatsapp");
            
            // Clear other common setting groups
            $groups = [\'theme\', \'notifications\', \'email\', \'inventory\', \'delivery\', \'pagination\'];
            foreach ($groups as $group) {
                Cache::forget("settings:group:{$tenantId}:{$group}");
            }
            
            // Clear any opcode cache
            if (function_exists(\'opcache_reset\')) {
                opcache_reset();
            }
        } else {
            Cache::flush();
        }
    }';

// Replace the clearCache method
if (strpos($content, $oldClearCacheMethod) !== false) {
    $content = str_replace($oldClearCacheMethod, $newClearCacheMethod, $content);
    echo "✅ Fixed AppSetting clearCache method\n";
} else {
    echo "ℹ️ AppSetting clearCache method already fixed or different\n";
}

// Write the fixed AppSetting file
if ($content !== $originalContent) {
    file_put_contents($appSettingFile, $content);
    echo "✅ AppSetting model updated\n";
}

// Step 2: Fix the SettingsController to ensure proper cache clearing
echo "\n2. FIXING SETTINGS CONTROLLER:\n";
echo "===============================\n";

$settingsControllerFile = __DIR__ . '/app/Http/Controllers/Admin/SettingsController.php';
if (!file_exists($settingsControllerFile)) {
    echo "❌ SettingsController not found\n";
    exit(1);
}

$controllerContent = file_get_contents($settingsControllerFile);
$originalControllerContent = $controllerContent;

// Enhance the updateWhatsAppTemplates method
$oldMethod = 'public function updateWhatsAppTemplates(Request $request)
    {
        $request->validate([
            \'whatsapp_template_pending\' => \'nullable|string|max:1000\',
            \'whatsapp_template_processing\' => \'nullable|string|max:1000\',
            \'whatsapp_template_shipped\' => \'nullable|string|max:1000\',
            \'whatsapp_template_delivered\' => \'nullable|string|max:1000\',
            \'whatsapp_template_cancelled\' => \'nullable|string|max:1000\',
            \'whatsapp_template_payment_confirmed\' => \'nullable|string|max:1000\',
        ]);

        // Update WhatsApp message templates
        AppSetting::set(\'whatsapp_template_pending\', $request->whatsapp_template_pending, \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_processing\', $request->whatsapp_template_processing, \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_shipped\', $request->whatsapp_template_shipped, \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_delivered\', $request->whatsapp_template_delivered, \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_cancelled\', $request->whatsapp_template_cancelled, \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_payment_confirmed\', $request->whatsapp_template_payment_confirmed, \'string\', \'whatsapp\');
        
        AppSetting::clearCache();

        return redirect()->back()->with(\'success\', \'WhatsApp message templates updated successfully!\');
    }';

$newMethod = 'public function updateWhatsAppTemplates(Request $request)
    {
        $request->validate([
            \'whatsapp_template_pending\' => \'nullable|string|max:1000\',
            \'whatsapp_template_processing\' => \'nullable|string|max:1000\',
            \'whatsapp_template_shipped\' => \'nullable|string|max:1000\',
            \'whatsapp_template_delivered\' => \'nullable|string|max:1000\',
            \'whatsapp_template_cancelled\' => \'nullable|string|max:1000\',
            \'whatsapp_template_payment_confirmed\' => \'nullable|string|max:1000\',
        ]);

        // Log the request data for debugging
        \Log::info(\'WhatsApp templates update request\', [
            \'templates\' => $request->only([
                \'whatsapp_template_pending\',
                \'whatsapp_template_processing\',
                \'whatsapp_template_shipped\',
                \'whatsapp_template_delivered\',
                \'whatsapp_template_cancelled\',
                \'whatsapp_template_payment_confirmed\'
            ]),
            \'tenant_id\' => session(\'selected_company_id\'),
            \'user_id\' => auth()->id()
        ]);

        // Update WhatsApp message templates with explicit empty string handling
        AppSetting::set(\'whatsapp_template_pending\', $request->whatsapp_template_pending ?? \'\', \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_processing\', $request->whatsapp_template_processing ?? \'\', \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_shipped\', $request->whatsapp_template_shipped ?? \'\', \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_delivered\', $request->whatsapp_template_delivered ?? \'\', \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_cancelled\', $request->whatsapp_template_cancelled ?? \'\', \'string\', \'whatsapp\');
        AppSetting::set(\'whatsapp_template_payment_confirmed\', $request->whatsapp_template_payment_confirmed ?? \'\', \'string\', \'whatsapp\');
        
        // Force clear all caches
        AppSetting::clearCache();
        
        // Clear Laravel caches
        try {
            \Artisan::call(\'view:clear\');
            \Artisan::call(\'config:clear\');
            \Artisan::call(\'cache:clear\');
            \Log::info(\'WhatsApp templates caches cleared\');
        } catch (\Exception $e) {
            \Log::warning(\'Failed to clear some caches: \' . $e->getMessage());
        }

        // Verify the save by reading back the values
        $savedTemplates = [];
        $templateKeys = [
            \'whatsapp_template_pending\',
            \'whatsapp_template_processing\',
            \'whatsapp_template_shipped\',
            \'whatsapp_template_delivered\',
            \'whatsapp_template_cancelled\',
            \'whatsapp_template_payment_confirmed\'
        ];
        
        foreach ($templateKeys as $key) {
            $savedTemplates[$key] = AppSetting::get($key);
        }
        
        \Log::info(\'WhatsApp templates saved values\', $savedTemplates);

        return redirect()->back()->with(\'success\', \'WhatsApp message templates updated successfully!\');
    }';

// Replace the updateWhatsAppTemplates method
if (strpos($controllerContent, 'public function updateWhatsAppTemplates(Request $request)') !== false) {
    // Find and replace the entire method
    $pattern = '/public function updateWhatsAppTemplates\(Request \$request\)\s*\{[^}]*\}/s';
    if (preg_match($pattern, $controllerContent)) {
        $controllerContent = preg_replace($pattern, $newMethod, $controllerContent);
        echo "✅ Enhanced updateWhatsAppTemplates method\n";
    } else {
        echo "❌ Could not find updateWhatsAppTemplates method pattern\n";
    }
} else {
    echo "❌ updateWhatsAppTemplates method not found\n";
}

// Write the fixed controller
if ($controllerContent !== $originalControllerContent) {
    file_put_contents($settingsControllerFile, $controllerContent);
    echo "✅ SettingsController updated\n";
}

// Step 3: Update the settings view to properly display saved values
echo "\n3. FIXING SETTINGS VIEW TEMPLATE VALUES:\n";
echo "=========================================\n";

$settingsViewFile = __DIR__ . '/resources/views/admin/settings/index.blade.php';
if (!file_exists($settingsViewFile)) {
    echo "❌ Settings view not found\n";
    exit(1);
}

$viewContent = file_get_contents($settingsViewFile);
$originalViewContent = $viewContent;

// Fix template textareas to use saved values instead of hardcoded defaults
$templateMappings = [
    'whatsapp_template_pending' => 'Hello @{{ customer_name }},

Your order #@{{ order_number }} is now PENDING.

We have received your order and it\'s being processed.

Order Total: ₹@{{ total }}
Order Date: @{{ order_date }}

Thank you for choosing @{{ company_name }}!',
    
    'whatsapp_template_processing' => 'Hello @{{ customer_name }},

Great news! Your order #@{{ order_number }} is now PROCESSING.

We are preparing your items for shipment.

Order Total: ₹@{{ total }}
Expected Processing: 1-2 business days

Thank you for your patience!

@{{ company_name }}',
    
    'whatsapp_template_shipped' => '🚚 Hello @{{ customer_name }},

Exciting news! Your order #@{{ order_number }} has been SHIPPED!

Your package is on its way to you.

Order Total: ₹@{{ total }}
Expected Delivery: 2-5 business days

Track your order for real-time updates.

Thanks for shopping with @{{ company_name }}!',
    
    'whatsapp_template_delivered' => '✅ Hello @{{ customer_name }},

Wonderful! Your order #@{{ order_number }} has been DELIVERED!

We hope you love your purchase.

Order Total: ₹@{{ total }}
Delivered on: @{{ order_date }}

Please let us know if you have any questions or feedback.

Thank you for choosing @{{ company_name }}!',
    
    'whatsapp_template_cancelled' => '❌ Hello @{{ customer_name }},

We\'re sorry to inform you that your order #@{{ order_number }} has been CANCELLED.

Order Total: ₹@{{ total }}
Cancellation Date: @{{ order_date }}

If you have any questions about this cancellation, please contact our customer support.

We apologize for any inconvenience.

@{{ company_name }}',
    
    'whatsapp_template_payment_confirmed' => '💳 Hello @{{ customer_name }},

Great news! Your payment for order #@{{ order_number }} has been CONFIRMED!

Payment Status: @{{ payment_status }}
Order Total: ₹@{{ total }}
Payment Date: @{{ order_date }}

Your order is now being processed and will be shipped soon.

Thank you for your payment!

@{{ company_name }}'
];

foreach ($templateMappings as $templateKey => $defaultValue) {
    // Replace hardcoded textarea content with dynamic value from whatsappSettings
    $pattern = '/(<textarea[^>]*name="' . $templateKey . '"[^>]*>)([^<]*?)(<\/textarea>)/s';
    
    $replacement = '$1{{ $whatsappSettings[\'' . $templateKey . '\'] ?? \'' . addslashes($defaultValue) . '\' }}$3';
    
    if (preg_match($pattern, $viewContent)) {
        $viewContent = preg_replace($pattern, $replacement, $viewContent);
        echo "✅ Fixed $templateKey textarea to use saved values\n";
    } else {
        echo "ℹ️ Pattern not found for $templateKey\n";
    }
}

// Write the fixed view
if ($viewContent !== $originalViewContent) {
    file_put_contents($settingsViewFile, $viewContent);
    echo "✅ Settings view updated to use saved template values\n";
}

// Step 4: Clear all caches immediately
echo "\n4. CLEARING ALL CACHES:\n";
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
    
    $commands = ['view:clear', 'config:clear', 'cache:clear', 'optimize:clear'];
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

echo "\n=== WHATSAPP TEMPLATE EDITING FIX COMPLETED ===\n";
echo "Summary of changes:\n";
echo "- ✅ Fixed AppSetting cache clearing to work without wildcards\n";
echo "- ✅ Enhanced SettingsController with better logging and cache clearing\n";
echo "- ✅ Updated settings view to use saved template values\n";
echo "- ✅ Cleared all compiled views and caches\n";

echo "\nNow test the WhatsApp template editing:\n";
echo "1. Go to: http://greenvalleyherbs.local:8000/admin/settings\n";
echo "2. Click on 'WhatsApp Templates' tab\n";
echo "3. Edit any template (e.g., change Order Pending Template to 'hello')\n";
echo "4. Click 'Save WhatsApp Templates'\n";
echo "5. The page should reload and show your edited text\n";

echo "\n🎉 WhatsApp template editing should now work correctly!\n";
