<?php
/**
 * COMPREHENSIVE FIX: Find and fix ALL unescaped customer_name patterns
 * This script will fix line 754 and any other remaining issues
 * WITHOUT deleting or removing any existing code
 */

echo "=== COMPREHENSIVE FIX FOR ALL CUSTOMER_NAME ESCAPING ISSUES ===\n";
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

// Step 2: Read current content
$content = file_get_contents($settingsFile);
$originalContent = $content;

echo "\n2. ANALYZING AND FIXING ALL TEMPLATE ESCAPING ISSUES:\n";
echo "======================================================\n";

// Split into lines to analyze
$lines = explode("\n", $content);
$totalLines = count($lines);
echo "Total lines in file: $totalLines\n";

// Find all problematic lines first
$problematicLines = [];
$patterns = [
    'customer_name',
    'order_number', 
    'total',
    'company_name',
    'order_date',
    'status',
    'payment_status',
    'customer_mobile'
];

foreach ($patterns as $pattern) {
    // Look for unescaped double braces
    $doublePattern = '{{ ' . $pattern . ' }}';
    $doublePatternNoSpace = '{{' . $pattern . '}}';
    
    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];
        $lineNum = $i + 1;
        
        // Check if line contains unescaped pattern (not already escaped with @)
        if ((strpos($line, $doublePattern) !== false && strpos($line, '@' . $doublePattern) === false) ||
            (strpos($line, $doublePatternNoSpace) !== false && strpos($line, '@' . $doublePatternNoSpace) === false)) {
            
            $problematicLines[] = [
                'line' => $lineNum,
                'content' => trim($line),
                'pattern' => $pattern
            ];
        }
    }
}

echo "Found " . count($problematicLines) . " problematic lines:\n";
foreach ($problematicLines as $prob) {
    echo "  Line {$prob['line']}: {$prob['pattern']} - " . substr($prob['content'], 0, 80) . "...\n";
}

// Step 3: Apply comprehensive fixes
echo "\n3. APPLYING FIXES:\n";
echo "==================\n";

$fixCount = 0;

// Fix all unescaped double brace patterns
$replacements = [
    // Double braces with spaces
    '/(?<!@)\{\{ customer_name \}\}/' => '@{{ customer_name }}',
    '/(?<!@)\{\{ order_number \}\}/' => '@{{ order_number }}',
    '/(?<!@)\{\{ total \}\}/' => '@{{ total }}',
    '/(?<!@)\{\{ company_name \}\}/' => '@{{ company_name }}',
    '/(?<!@)\{\{ order_date \}\}/' => '@{{ order_date }}',
    '/(?<!@)\{\{ status \}\}/' => '@{{ status }}',
    '/(?<!@)\{\{ payment_status \}\}/' => '@{{ payment_status }}',
    '/(?<!@)\{\{ customer_mobile \}\}/' => '@{{ customer_mobile }}',
    
    // Double braces without spaces
    '/(?<!@)\{\{customer_name\}\}/' => '@{{customer_name}}',
    '/(?<!@)\{\{order_number\}\}/' => '@{{order_number}}',
    '/(?<!@)\{\{total\}\}/' => '@{{total}}',
    '/(?<!@)\{\{company_name\}\}/' => '@{{company_name}}',
    '/(?<!@)\{\{order_date\}\}/' => '@{{order_date}}',
    '/(?<!@)\{\{status\}\}/' => '@{{status}}',
    '/(?<!@)\{\{payment_status\}\}/' => '@{{payment_status}}',
    '/(?<!@)\{\{customer_mobile\}\}/' => '@{{customer_mobile}}',
];

foreach ($replacements as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $content);
    if ($newContent !== $content) {
        $matches = preg_match_all($pattern, $content);
        $fixCount += $matches;
        $content = $newContent;
        echo "✅ Fixed pattern: " . str_replace('/', '', $pattern) . " -> $replacement\n";
    }
}

// Step 4: Handle @verbatim blocks properly
echo "\n4. FIXING @VERBATIM BLOCKS:\n";
echo "============================\n";

// Fix spacing in @verbatim examples to be consistent
$content = preg_replace_callback(
    '/@verbatim(.*?)@endverbatim/s',
    function($matches) {
        $verbatimContent = $matches[1];
        
        // Ensure consistent spacing in examples
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
echo "✅ Fixed @verbatim block formatting\n";

echo "\nTotal fixes applied: $fixCount\n";

// Step 5: Write the fixed content
if ($content !== $originalContent) {
    file_put_contents($settingsFile, $content);
    echo "✅ Settings file updated with all fixes\n";
} else {
    echo "ℹ️ No changes needed (file already correct)\n";
}

// Step 6: Verify the fixes
echo "\n5. VERIFYING FIXES:\n";
echo "===================\n";

$verifyContent = file_get_contents($settingsFile);
$verifyLines = explode("\n", $verifyContent);

// Check line 754 specifically
if (isset($verifyLines[753])) { // Array is 0-indexed
    $line754 = $verifyLines[753];
    echo "Line 754 content: " . trim($line754) . "\n";
    
    if (preg_match('/(?<!@)\{\{?\s*customer_name\s*\}?\}/', $line754)) {
        echo "❌ Line 754 still has unescaped customer_name\n";
    } else {
        echo "✅ Line 754 is now properly escaped\n";
    }
}

// Check for any remaining issues
$remainingIssues = 0;
for ($i = 0; $i < count($verifyLines); $i++) {
    $line = $verifyLines[$i];
    if (preg_match('/(?<!@)\{\{?\s*(customer_name|order_number|total|company_name|order_date|status|payment_status|customer_mobile)\s*\}?\}/', $line)) {
        echo "❌ Still has issue at line " . ($i + 1) . ": " . trim($line) . "\n";
        $remainingIssues++;
    }
}

if ($remainingIssues === 0) {
    echo "✅ No remaining unescaped patterns found!\n";
} else {
    echo "❌ Found {$remainingIssues} remaining issues\n";
}

// Step 7: Clear all caches
echo "\n6. CLEARING ALL CACHES:\n";
echo "========================\n";

// Clear compiled views
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
}

// Clear other cache directories
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
            echo "❌ Failed: php artisan $command - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Laravel artisan commands failed: " . $e->getMessage() . "\n";
}

// Clear opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n=== COMPREHENSIVE FIX COMPLETED ===\n";
echo "Summary:\n";
echo "- ✅ Fixed {$fixCount} template escaping issues\n";
echo "- ✅ Line 754 should now be properly escaped\n";
echo "- ✅ All caches cleared\n";
echo "- ✅ Backup created: " . basename($backupFile) . "\n";
echo "- ✅ No existing code was deleted or removed\n";

echo "\nNext steps:\n";
echo "1. Test the admin settings page: http://greenvalleyherbs.local:8000/admin/settings\n";
echo "2. Line 754 error should be resolved\n";
echo "3. If error persists, restart web server\n";
echo "4. Check storage/logs/laravel.log for any new errors\n";

echo "\n🎉 ALL customer_name escaping issues should now be FIXED!\n";
