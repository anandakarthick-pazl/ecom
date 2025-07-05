<?php
/**
 * Quick cache clear for customer_name error
 */

echo "=== QUICK CACHE CLEAR FOR CUSTOMER_NAME ERROR ===\n";

// Clear compiled views immediately
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
        echo "✅ Cleared " . count($files) . " files from " . basename($dir) . "\n";
    }
}

// Clear opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n✅ QUICK CACHE CLEAR COMPLETE\n";
echo "Now try accessing your admin settings page.\n";
