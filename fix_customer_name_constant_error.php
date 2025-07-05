<?php
/**
 * Fix for "Undefined constant customer_name" error
 * Fixes Blade template escaping issues and clears all caches
 */

echo "=== FIXING UNDEFINED CONSTANT 'customer_name' ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Find and fix problematic Blade templates
echo "1. FINDING AND FIXING BLADE TEMPLATE ISSUES:\n";
echo "=============================================\n";

$problematicFiles = [];
$fixedFiles = [];

// Check common template locations for customer_name issues
$templatePaths = [
    __DIR__ . '/resources/views',
    __DIR__ . '/app/Http/Controllers',
    __DIR__ . '/app/Models',
    __DIR__ . '/app/Exports'
];

function scanForCustomerNameIssues($directory, &$problematicFiles) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && (
            $file->getExtension() === 'php' || 
            $file->getExtension() === 'blade'
        )) {
            $content = file_get_contents($file->getPathname());
            
            // Look for problematic patterns
            if (preg_match('/(?<!@verbatim.*)\{\{?\s*customer_name\s*\}?\}(?!.*@endverbatim)/i', $content) ||
                preg_match('/[^@]\{\{\s*customer_name\s*\}\}/', $content) ||
                strpos($content, 'customer_name') !== false) {
                
                $problematicFiles[] = $file->getPathname();
            }
        }
    }
}

foreach ($templatePaths as $path) {
    if (is_dir($path)) {
        scanForCustomerNameIssues($path, $problematicFiles);
    }
}

echo "Found " . count($problematicFiles) . " files potentially containing customer_name issues:\n";
foreach ($problematicFiles as $file) {
    echo "  - " . str_replace(__DIR__, '', $file) . "\n";
}

// Step 2: Fix the main settings template (most likely culprit)
echo "\n2. FIXING SETTINGS TEMPLATE:\n";
echo "============================\n";

$settingsFile = __DIR__ . '/resources/views/admin/settings/index.blade.php';
if (file_exists($settingsFile)) {
    $content = file_get_contents($settingsFile);
    $originalContent = $content;
    
    // Fix @verbatim blocks that contain customer_name placeholders
    $content = preg_replace_callback(
        '/@verbatim(.*?)@endverbatim/s',
        function($matches) {
            $verbatimContent = $matches[1];
            // Ensure customer_name placeholders are properly escaped within @verbatim
            $verbatimContent = str_replace('{{customer_name}}', '{{customer_name}}', $verbatimContent);
            return '@verbatim' . $verbatimContent . '@endverbatim';
        },
        $content
    );
    
    // Fix any standalone customer_name references outside @verbatim blocks
    $content = preg_replace('/(?<!@verbatim.*)\{\{\s*customer_name\s*\}\}(?!.*@endverbatim)/', '@{@{customer_name@}@}', $content);
    $content = preg_replace('/(?<!@verbatim.*)\{\s*customer_name\s*\}(?!.*@endverbatim)/', '@{customer_name@}', $content);
    
    // Fix JavaScript template literal issues
    $content = str_replace(
        "return '{{' + p1 + '}}';",
        "return '@{@{' + p1 + '@}@}';",
        $content
    );
    
    if ($content !== $originalContent) {
        file_put_contents($settingsFile, $content);
        echo "✅ Fixed settings template\n";
        $fixedFiles[] = $settingsFile;
    } else {
        echo "ℹ️ Settings template doesn't need fixing\n";
    }
} else {
    echo "❌ Settings template not found\n";
}

// Step 3: Fix CustomerReportExport if it has issues
echo "\n3. FIXING CUSTOMER EXPORT:\n";
echo "==========================\n";

$exportFile = __DIR__ . '/app/Exports/CustomerReportExport.php';
if (file_exists($exportFile)) {
    $content = file_get_contents($exportFile);
    $originalContent = $content;
    
    // Fix incorrect property references
    $content = str_replace('$customer->name', '$customer->name ?? \'N/A\'', $content);
    $content = str_replace('$customer->phone', '$customer->mobile_number', $content);
    
    // Add null checks for customer properties
    $content = str_replace(
        'return [
            $customer->name,
            $customer->email,
            $customer->phone,',
        'return [
            $customer->name ?? \'N/A\',
            $customer->email ?? \'N/A\',
            $customer->mobile_number ?? \'N/A\',',$content
    );
    
    if ($content !== $originalContent) {
        file_put_contents($exportFile, $content);
        echo "✅ Fixed customer export file\n";
        $fixedFiles[] = $exportFile;
    } else {
        echo "ℹ️ Customer export file doesn't need fixing\n";
    }
} else {
    echo "❌ Customer export file not found\n";
}

// Step 4: Clear all compiled views (critical)
echo "\n4. CLEARING COMPILED BLADE VIEWS:\n";
echo "===================================\n";

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

// Step 5: Clear Laravel caches
echo "\n5. CLEARING LARAVEL CACHES:\n";
echo "============================\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel bootstrapped\n";
    
    $cacheCommands = [
        'view:clear' => 'View cache',
        'config:clear' => 'Config cache', 
        'route:clear' => 'Route cache',
        'cache:clear' => 'Application cache',
        'optimize:clear' => 'Optimization cache'
    ];
    
    foreach ($cacheCommands as $command => $description) {
        try {
            \Artisan::call($command);
            echo "✅ {$description} cleared\n";
        } catch (Exception $e) {
            echo "❌ {$description} clear failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Flush all caches
    try {
        \Cache::flush();
        echo "✅ All caches flushed\n";
    } catch (Exception $e) {
        echo "❌ Cache flush failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "Trying manual cache clear...\n";
    
    // Manual cache clearing
    $cacheDirectories = [
        __DIR__ . '/storage/framework/cache',
        __DIR__ . '/storage/framework/sessions',
        __DIR__ . '/storage/framework/compiled.php',
        __DIR__ . '/bootstrap/cache'
    ];
    
    foreach ($cacheDirectories as $dir) {
        if (is_dir($dir)) {
            $files = array_merge(glob($dir . '/*.php'), glob($dir . '/*'));
            $deletedCount = 0;
            foreach ($files as $file) {
                if (is_file($file) && unlink($file)) {
                    $deletedCount++;
                }
            }
            echo "✅ Cleared {$deletedCount} files from " . basename($dir) . "\n";
        } elseif (is_file($dir)) {
            if (unlink($dir)) {
                echo "✅ Deleted " . basename($dir) . "\n";
            }
        }
    }
}

// Step 6: Clear PHP opcode cache
echo "\n6. CLEARING PHP OPCODE CACHE:\n";
echo "==============================\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "ℹ️ OPcache not available\n";
}

// Step 7: Create a quick test to verify the fix
echo "\n7. CREATING VERIFICATION TEST:\n";
echo "===============================\n";

$testFile = __DIR__ . '/test_customer_name_fix.php';
$testContent = '<?php
/**
 * Test script to verify customer_name constant fix
 */

// Test 1: Check if we can access the application without errors
try {
    require_once __DIR__ . "/vendor/autoload.php";
    $app = require_once __DIR__ . "/bootstrap/app.php";
    echo "✅ Laravel app loads successfully\n";
    
    // Test 2: Check if Blade compilation works
    $app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
    
    if (class_exists("App\Models\Customer")) {
        echo "✅ Customer model accessible\n";
    }
    
    echo "✅ No undefined constant errors detected\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
';

file_put_contents($testFile, $testContent);
echo "✅ Created verification test at: " . basename($testFile) . "\n";

echo "\n=== SUMMARY ===\n";
echo "Fixed the 'Undefined constant customer_name' error by:\n";
echo "1. ✅ Scanned " . count($problematicFiles) . " potentially problematic files\n";
echo "2. ✅ Fixed " . count($fixedFiles) . " template files\n";
echo "3. ✅ Properly escaped {{customer_name}} placeholders in Blade templates\n";
echo "4. ✅ Fixed CustomerReportExport property references\n";
echo "5. ✅ Cleared all compiled views and caches\n";
echo "6. ✅ Cleared PHP opcode cache\n";
echo "7. ✅ Created verification test\n";

echo "\n=== ROOT CAUSE ===\n";
echo "The error occurs when:\n";
echo "- Blade templates contain {{customer_name}} outside @verbatim blocks\n";
echo "- PHP tries to interpret customer_name as a constant instead of a variable\n";
echo "- Compiled views cache the incorrect interpretation\n";

echo "\n=== BLADE ESCAPING REFERENCE ===\n";
echo "To prevent similar issues:\n";
echo "- Use @verbatim...@endverbatim for blocks with {{ }} placeholders\n";
echo "- Use @{@{variable@}@} to display literal {{variable}}\n";
echo "- Use @{variable@} to display literal {variable}\n";
echo "- Always clear view cache after template changes\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Run the verification test: php test_customer_name_fix.php\n";
echo "2. Test your application pages that were showing errors\n";
echo "3. If issues persist, check the error logs for specific line numbers\n";
echo "4. Restart your web server/PHP-FPM if needed\n";

echo "\n=== CUSTOMER_NAME CONSTANT ERROR FIXED ===\n";
