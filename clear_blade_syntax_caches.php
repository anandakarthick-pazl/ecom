<?php
// Quick cache clear for Blade syntax error
echo "=== CLEARING CACHES FOR BLADE SYNTAX FIX ===\n";

// Clear compiled views (most important for Blade syntax errors)
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

// Clear opcode cache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared\n";
}

echo "\n✅ BLADE SYNTAX ERROR CACHES CLEARED\n";
echo "The syntax error should be resolved now!\n";
