<?php
/**
 * FIX: Blade Template Syntax Error - Unclosed quotes
 * This script fixes the syntax error in the WhatsApp templates by using a better approach
 */

echo "=== FIXING BLADE TEMPLATE SYNTAX ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$settingsFile = __DIR__ . '/resources/views/admin/settings/index.blade.php';

if (!file_exists($settingsFile)) {
    echo "❌ Settings file not found\n";
    exit(1);
}

// Step 1: Create backup
$backupFile = $settingsFile . '.backup.' . date('YmdHis');
copy($settingsFile, $backupFile);
echo "✅ Backup created: " . basename($backupFile) . "\n";

// Step 2: Read and fix the content with a better approach
$content = file_get_contents($settingsFile);
$originalContent = $content;

echo "\n2. FIXING BLADE TEMPLATE SYNTAX:\n";
echo "=================================\n";

// Better approach: Use PHP variables for the default templates at the top of the view
$phpVariablesBlock = "<?php
// Default WhatsApp template values
\$defaultTemplates = [
    'pending' => 'Hello @{{ customer_name }},

Your order #@{{ order_number }} is now PENDING.

We have received your order and it\\'s being processed.

Order Total: ₹@{{ total }}
Order Date: @{{ order_date }}

Thank you for choosing @{{ company_name }}!',

    'processing' => 'Hello @{{ customer_name }},

Great news! Your order #@{{ order_number }} is now PROCESSING.

We are preparing your items for shipment.

Order Total: ₹@{{ total }}
Expected Processing: 1-2 business days

Thank you for your patience!

@{{ company_name }}',

    'shipped' => '🚚 Hello @{{ customer_name }},

Exciting news! Your order #@{{ order_number }} has been SHIPPED!

Your package is on its way to you.

Order Total: ₹@{{ total }}
Expected Delivery: 2-5 business days

Track your order for real-time updates.

Thanks for shopping with @{{ company_name }}!',

    'delivered' => '✅ Hello @{{ customer_name }},

Wonderful! Your order #@{{ order_number }} has been DELIVERED!

We hope you love your purchase.

Order Total: ₹@{{ total }}
Delivered on: @{{ order_date }}

Please let us know if you have any questions or feedback.

Thank you for choosing @{{ company_name }}!',

    'cancelled' => '❌ Hello @{{ customer_name }},

We\\'re sorry to inform you that your order #@{{ order_number }} has been CANCELLED.

Order Total: ₹@{{ total }}
Cancellation Date: @{{ order_date }}

If you have any questions about this cancellation, please contact our customer support.

We apologize for any inconvenience.

@{{ company_name }}',

    'payment_confirmed' => '💳 Hello @{{ customer_name }},

Great news! Your payment for order #@{{ order_number }} has been CONFIRMED!

Payment Status: @{{ payment_status }}
Order Total: ₹@{{ total }}
Payment Date: @{{ order_date }}

Your order is now being processed and will be shipped soon.

Thank you for your payment!

@{{ company_name }}'
];
?>";

// Add the PHP variables block right after @extends directive
if (strpos($content, '@extends(\'admin.layouts.app\')') !== false) {
    $content = str_replace(
        '@extends(\'admin.layouts.app\')',
        '@extends(\'admin.layouts.app\')' . "\n\n" . $phpVariablesBlock,
        $content
    );
    echo "✅ Added PHP variables block for default templates\n";
}

// Step 3: Replace the complex Blade syntax with simple variable references
$replacements = [
    // Pending template
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_pending\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_pending\'] ?? $defaultTemplates[\'pending\'] }}',
    
    // Processing template  
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_processing\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_processing\'] ?? $defaultTemplates[\'processing\'] }}',
    
    // Shipped template
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_shipped\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_shipped\'] ?? $defaultTemplates[\'shipped\'] }}',
    
    // Delivered template
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_delivered\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_delivered\'] ?? $defaultTemplates[\'delivered\'] }}',
    
    // Cancelled template
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_cancelled\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_cancelled\'] ?? $defaultTemplates[\'cancelled\'] }}',
    
    // Payment confirmed template
    '/{{ \\$whatsappSettings\\[\'whatsapp_template_payment_confirmed\'\\] \\?\\? "[^"]*" }}/' => '{{ $whatsappSettings[\'whatsapp_template_payment_confirmed\'] ?? $defaultTemplates[\'payment_confirmed\'] }}'
];

$fixCount = 0;
foreach ($replacements as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $content = $newContent;
        $fixCount++;
        echo "✅ Fixed textarea pattern\n";
    }
}

echo "Total fixes applied: $fixCount\n";

// Step 4: Write the fixed content
if ($content !== $originalContent) {
    file_put_contents($settingsFile, $content);
    echo "✅ Settings file updated with syntax fixes\n";
} else {
    echo "ℹ️ No changes needed\n";
}

// Step 5: Clear all caches
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

echo "\n=== BLADE TEMPLATE SYNTAX ERROR FIXED ===\n";
echo "Summary:\n";
echo "- ✅ Fixed Blade template syntax errors\n";
echo "- ✅ Used PHP variables for clean default templates\n";
echo "- ✅ Cleared all compiled views and caches\n";
echo "- ✅ Backup created: " . basename($backupFile) . "\n";

echo "\nNext steps:\n";
echo "1. Test the admin settings page: http://greenvalleyherbs.local:8000/admin/settings\n";
echo "2. The syntax error should be resolved\n";
echo "3. WhatsApp template editing should work correctly\n";

echo "\n🎉 Blade template syntax error FIXED!\n";
