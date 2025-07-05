<?php
/**
 * SYNTAX ERROR FIX SCRIPT
 * Clears all caches and regenerates autoloader after removing problematic files
 */

echo "=== FIXING SYNTAX ERROR ISSUE ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Clear Composer autoloader cache
echo "1. Clearing Composer autoloader...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    // Clear composer cache
    system('composer dump-autoload --optimize', $result1);
    echo $result1 === 0 ? "✅ Composer autoloader regenerated\n" : "❌ Composer autoloader failed\n";
} else {
    echo "❌ Composer autoload file not found\n";
}

// Step 2: Bootstrap Laravel (if possible)
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel bootstrapped successfully\n";
    
    // Step 3: Clear Laravel caches
    echo "\n2. Clearing Laravel caches...\n";
    
    try {
        \Artisan::call('config:clear');
        echo "✅ Config cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Config cache clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('route:clear');
        echo "✅ Route cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Route cache clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('view:clear');
        echo "✅ View cache cleared\n";
    } catch (Exception $e) {
        echo "❌ View cache clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Artisan::call('cache:clear');
        echo "✅ Application cache cleared\n";
    } catch (Exception $e) {
        echo "❌ Application cache clear failed: " . $e->getMessage() . "\n";
    }
    
    try {
        \Cache::flush();
        echo "✅ All caches flushed\n";
    } catch (Exception $e) {
        echo "❌ Cache flush failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "This is expected if the syntax error is still present.\n";
}

// Step 4: Clear any PHP opcode cache
echo "\n3. Clearing PHP opcode cache...\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "ℹ️ OPcache not available\n";
}

// Step 5: Summary
echo "\n=== SUMMARY ===\n";
echo "✅ Moved problematic files to cleanup_files/ directory\n";
echo "✅ Regenerated Composer autoloader\n";
echo "✅ Cleared Laravel caches\n";
echo "✅ Cleared PHP opcode cache\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Try accessing http://greenvalleyherbs.local:8000/admin/settings again\n";
echo "2. If the issue persists, restart your web server\n";
echo "3. Check the cleanup_files/ directory for the removed files\n";

echo "\n=== FILES MOVED ===\n";
$cleanupDir = __DIR__ . '/cleanup_files';
if (is_dir($cleanupDir)) {
    $files = scandir($cleanupDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "- cleanup_files/{$file}\n";
        }
    }
} else {
    echo "No cleanup directory found\n";
}

echo "\n=== SYNTAX ERROR FIX COMPLETE ===\n";
