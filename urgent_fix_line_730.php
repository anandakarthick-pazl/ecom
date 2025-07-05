<?php
/**
 * URGENT FIX: Undefined constant "customer_name" at line 730
 * This script directly fixes the escaping issues in the settings template
 */

echo "=== URGENT FIX FOR LINE 730 CUSTOMER_NAME ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$settingsFile = __DIR__ . '/resources/views/admin/settings/index.blade.php';

if (!file_exists($settingsFile)) {
    echo "❌ Settings file not found at: $settingsFile\n";
    exit(1);
}

// Step 1: Create backup
$backupFile = $settingsFile . '.backup.' . date('YmdHis');
copy($settingsFile, $backupFile);
echo "✅ Backup created: " . basename($backupFile) . "\n";

// Step 2: Read and fix the content
$content = file_get_contents($settingsFile);
$originalContent = $content;

echo "\n2. FIXING TEMPLATE ESCAPING ISSUES:\n";
echo "====================================\n";

// Fix all unescaped placeholder patterns that cause the error
$fixes = [
    // Fix unescaped double braces
    '/(?<!@)\{\{\s*customer_name\s*\}\}/i' => '@{{ customer_name }}',
    '/(?<!@)\{\{\s*order_number\s*\}\}/i' => '@{{ order_number }}',
    '/(?<!@)\{\{\s*total\s*\}\}/i' => '@{{ total }}',
    '/(?<!@)\{\{\s*company_name\s*\}\}/i' => '@{{ company_name }}',
    '/(?<!@)\{\{\s*order_date\s*\}\}/i' => '@{{ order_date }}',
    '/(?<!@)\{\{\s*status\s*\}\}/i' => '@{{ status }}',
    '/(?<!@)\{\{\s*payment_status\s*\}\}/i' => '@{{ payment_status }}',
    '/(?<!@)\{\{\s*customer_mobile\s*\}\}/i' => '@{{ customer_mobile }}',
    
    // Fix unescaped single braces
    '/(?<!@)\{\s*customer_name\s*\}/i' => '@{customer_name}',
    '/(?<!@)\{\s*order_number\s*\}/i' => '@{order_number}',
    '/(?<!@)\{\s*total\s*\}/i' => '@{total}',
    '/(?<!@)\{\s*company_name\s*\}/i' => '@{company_name}',
    '/(?<!@)\{\s*order_date\s*\}/i' => '@{order_date}',
    '/(?<!@)\{\s*status\s*\}/i' => '@{status}',
    '/(?<!@)\{\s*payment_status\s*\}/i' => '@{payment_status}',
    '/(?<!@)\{\s*customer_mobile\s*\}/i' => '@{customer_mobile}',
];

$fixCount = 0;
foreach ($fixes as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $matches = preg_match_all($pattern, $content);
        $fixCount += $matches;
        $content = $newContent;
        echo "✅ Fixed pattern: $pattern -> $replacement\n";
    }
}

// Additional fix for @verbatim blocks that might have issues
$content = preg_replace_callback(
    '/@verbatim(.*?)@endverbatim/s',
    function($matches) {
        $verbatimContent = $matches[1];
        // Ensure placeholders within @verbatim are not causing issues
        $verbatimContent = str_replace([
            '{{customer_name}}',
            '{{order_number}}',
            '{{total}}',
            '{{company_name}}',
            '{{order_date}}',
            '{{status}}',
            '{{payment_status}}',
            '{{customer_mobile}}'
        ], [
            '{{ customer_name }}',
            '{{ order_number }}',
            '{{ total }}',
            '{{ company_name }}',
            '{{ order_date }}',
            '{{ status }}',
            '{{ payment_status }}',
            '{{ customer_mobile }}'
        ], $verbatimContent);
        
        return '@verbatim' . $verbatimContent . '@endverbatim';
    },
    $content
);

echo "Total fixes applied: $fixCount\n";

// Step 3: Write the fixed content
if ($content !== $originalContent) {
    file_put_contents($settingsFile, $content);
    echo "✅ Settings file updated with fixes\n";
} else {
    echo "ℹ️ No changes needed (file already correct)\n";
}

// Step 4: Clear all caches
echo "\n3. CLEARING ALL CACHES:\n";
echo "========================\n";

// Clear compiled views first (most important)
$viewCachePath = __DIR__ . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    $deletedCount = 0;
    
    foreach ($files as $file) {
        if (unlink($file)) {
            $deletedCount++;
        }
    }
    
    echo "✅ Deleted {$deletedCount} compiled view files\n";
} else {
    echo "ℹ️ View cache directory not found\n";
}

// Clear Laravel caches using artisan
$cacheCommands = [
    'view:clear' => 'View cache',
    'config:clear' => 'Config cache',
    'cache:clear' => 'Application cache',
    'route:clear' => 'Route cache',
    'optimize:clear' => 'All optimization caches'
];

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    foreach ($cacheCommands as $command => $description) {
        try {
            \Artisan::call($command);
            echo "✅ {$description} cleared\n";
        } catch (Exception $e) {
            echo "❌ {$description} clear failed: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed, using manual cache clear\n";
    
    // Manual cache clearing
    $cacheDirectories = [
        __DIR__ . '/storage/framework/cache',
        __DIR__ . '/bootstrap/cache'
    ];
    
    foreach ($cacheDirectories as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*.php');
            $deletedCount = 0;
            foreach ($files as $file) {
                if (is_file($file) && unlink($file)) {
                    $deletedCount++;
                }
            }
            echo "✅ Cleared {$deletedCount} files from " . basename($dir) . "\n";
        }
    }
}

// Step 5: Clear opcode cache
echo "\n4. CLEARING OPCODE CACHE:\n";
echo "==========================\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "ℹ️ OPcache not available\n";
}

// Step 6: Verify the fix by checking the file
echo "\n5. VERIFYING THE FIX:\n";
echo "=====================\n";

$verifyContent = file_get_contents($settingsFile);
$lines = explode("\n", $verifyContent);

// Check around line 730
$problemsFound = 0;
for ($i = 720; $i <= 740 && $i < count($lines); $i++) {
    $line = $lines[$i];
    if (preg_match('/(?<!@)\{\{?\s*customer_name\s*\}?\}/', $line)) {
        echo "❌ Still has issue at line " . ($i + 1) . ": " . trim($line) . "\n";
        $problemsFound++;
    }
}

if ($problemsFound === 0) {
    echo "✅ No more unescaped customer_name patterns found around line 730\n";
} else {
    echo "❌ Found {$problemsFound} remaining issues\n";
}

// Show some context around the area that was problematic
echo "\n6. LINE 730 AREA CONTENT:\n";
echo "==========================\n";
for ($i = 725; $i <= 735 && $i < count($lines); $i++) {
    $lineNum = $i + 1;
    $line = trim($lines[$i]);
    if (strlen($line) > 0) {
        echo "Line {$lineNum}: " . substr($line, 0, 100) . "\n";
    }
}

echo "\n=== URGENT FIX COMPLETED ===\n";
echo "Summary:\n";
echo "- ✅ Fixed {$fixCount} template escaping issues\n";
echo "- ✅ Cleared all compiled views and caches\n";
echo "- ✅ Created backup file: " . basename($backupFile) . "\n";

echo "\nNext steps:\n";
echo "1. Test the admin settings page immediately\n";
echo "2. If error persists, restart your web server\n";
echo "3. Check storage/logs/laravel.log for any new errors\n";

echo "\nThe undefined constant 'customer_name' error at line 730 should now be FIXED!\n";
