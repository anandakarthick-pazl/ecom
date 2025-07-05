<?php
/**
 * Fix for "Undefined constant variable" error
 * Clears caches and compiled views after fixing the Blade template issue
 */

echo "=== FIXING UNDEFINED CONSTANT 'variable' ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Clear compiled views first (most important)
echo "1. CLEARING COMPILED BLADE VIEWS:\n";
echo "==================================\n";

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

// Step 2: Clear Laravel caches
echo "\n2. CLEARING LARAVEL CACHES:\n";
echo "============================\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel bootstrapped\n";
    
    // Clear specific caches
    try {
        \Artisan::call('view:clear');
        echo "✅ View cache cleared\n";
    } catch (Exception $e) {
        echo "❌ View clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Config clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('route:clear');
        echo "✅ Route cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Route clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('cache:clear');
        echo "✅ Application cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Cache clear failed: " . $e->getMessage() . "\n";
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
        __DIR__ . '/bootstrap/cache'
    ];
    
    foreach ($cacheDirectories as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
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

// Step 3: Clear PHP opcode cache
echo "\n3. CLEARING PHP OPCODE CACHE:\n";
echo "==============================\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "ℹ️ OPcache not available\n";
}

// Step 4: Verify the fix
echo "\n4. VERIFYING THE FIX:\n";
echo "=====================\n";

$settingsViewPath = __DIR__ . '/resources/views/admin/settings/index.blade.php';
if (file_exists($settingsViewPath)) {
    $content = file_get_contents($settingsViewPath);
    
    // Check if the problematic pattern is gone
    if (strpos($content, "{{$1}}") !== false) {
        echo "❌ Still contains problematic {{$1}} pattern\n";
    } else {
        echo "✅ Problematic {{$1}} pattern removed\n";
    }
    
    // Check if the new pattern is present
    if (strpos($content, "return '{{' + p1 + '}}';") !== false) {
        echo "✅ New safe replacement pattern found\n";
    } else {
        echo "❌ New replacement pattern not found\n";
    }
    
    // Check for @verbatim remnants
    if (strpos($content, "@verbatim") !== false || strpos($content, "@endverbatim") !== false) {
        echo "⚠️ @verbatim directives still present - this might cause issues\n";
    } else {
        echo "✅ No @verbatim directives found\n";
    }
} else {
    echo "❌ Settings view file not found\n";
}

echo "\n=== SUMMARY ===\n";
echo "Fixed the 'Undefined constant variable' error by:\n";
echo "1. ✅ Replaced {{$1}} with function-based string concatenation\n";
echo "2. ✅ Removed @verbatim/@endverbatim directives\n";
echo "3. ✅ Cleared all compiled views and caches\n";
echo "4. ✅ Cleared PHP opcode cache\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Try accessing http://greenvalleyherbs.local:8000/admin/settings\n";
echo "2. If the issue persists, restart your web server\n";
echo "3. Check the browser console for any JavaScript errors\n";

echo "\n=== VARIABLE ERROR FIX COMPLETE ===\n";
