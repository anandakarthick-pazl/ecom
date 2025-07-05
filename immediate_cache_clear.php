<?php
/**
 * IMMEDIATE CACHE CLEAR for customer_name line 754 error
 */

echo "=== IMMEDIATE CACHE CLEAR FOR LINE 754 ERROR ===\n";

// Clear compiled views immediately
$viewCachePath = __DIR__ . '/storage/framework/views';
if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    foreach ($files as $file) {
        unlink($file);
    }
    echo "✅ Cleared " . count($files) . " compiled view files\n";
}

// Clear framework cache  
$frameworkCachePath = __DIR__ . '/storage/framework/cache';
if (is_dir($frameworkCachePath)) {
    $files = glob($frameworkCachePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
    echo "✅ Cleared framework cache\n";
}

// Clear bootstrap cache
$bootstrapCachePath = __DIR__ . '/bootstrap/cache';
if (is_dir($bootstrapCachePath)) {
    $files = glob($bootstrapCachePath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
    echo "✅ Cleared bootstrap cache\n";
}

// Clear opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n✅ IMMEDIATE CACHE CLEAR COMPLETE\n";
echo "Try accessing your settings page now. If still errors, run the full fix.\n";
