<?php
/**
 * Quick fix for "Undefined constant customer_name" error
 * Simple cache clearing script
 */

echo "=== QUICK FIX: CLEARING CACHES FOR CUSTOMER_NAME ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Clear compiled views (most important)
echo "1. Clearing compiled Blade views...\n";
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

// Step 2: Clear other cache directories
echo "\n2. Clearing other caches...\n";
$cacheDirectories = [
    __DIR__ . '/storage/framework/cache' => 'Framework cache',
    __DIR__ . '/bootstrap/cache' => 'Bootstrap cache'
];

foreach ($cacheDirectories as $dir => $name) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        $deletedCount = 0;
        foreach ($files as $file) {
            if (is_file($file) && unlink($file)) {
                $deletedCount++;
            }
        }
        echo "✅ Cleared {$deletedCount} files from {$name}\n";
    }
}

// Step 3: Clear PHP opcode cache
echo "\n3. Clearing PHP opcode cache...\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
} else {
    echo "ℹ️ OPcache not available\n";
}

echo "\n=== QUICK FIX COMPLETE ===\n";
echo "If you're still getting the error:\n";
echo "1. Run the full fix: php fix_customer_name_constant_error.php\n";
echo "2. Restart your web server\n";
echo "3. Clear browser cache\n";
