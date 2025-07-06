<?php
/**
 * Complete Laravel Cache & Error Fix Script
 * This script clears all Laravel caches and fixes common issues
 */

echo "=== Laravel Cache & Error Fix Script ===\n\n";

// 1. Clear View Cache
$viewCachePath = __DIR__ . '/storage/framework/views';
echo "1. Clearing view cache...\n";

if (is_dir($viewCachePath)) {
    $files = glob($viewCachePath . '/*.php');
    $deletedCount = 0;
    
    foreach ($files as $file) {
        if (basename($file) !== '.gitignore') {
            if (unlink($file)) {
                $deletedCount++;
            }
        }
    }
    echo "   ✓ Deleted $deletedCount cached view files\n";
} else {
    echo "   ✗ View cache directory not found\n";
}

// 2. Clear Route Cache
$routeCachePath = __DIR__ . '/bootstrap/cache/routes-v7.php';
echo "\n2. Clearing route cache...\n";
if (file_exists($routeCachePath)) {
    if (unlink($routeCachePath)) {
        echo "   ✓ Route cache cleared\n";
    } else {
        echo "   ✗ Failed to clear route cache\n";
    }
} else {
    echo "   ℹ No route cache found\n";
}

// 3. Clear Config Cache
$configCachePath = __DIR__ . '/bootstrap/cache/config.php';
echo "\n3. Clearing config cache...\n";
if (file_exists($configCachePath)) {
    if (unlink($configCachePath)) {
        echo "   ✓ Config cache cleared\n";
    } else {
        echo "   ✗ Failed to clear config cache\n";
    }
} else {
    echo "   ℹ No config cache found\n";
}

// 4. Clear Session Files
$sessionPath = __DIR__ . '/storage/framework/sessions';
echo "\n4. Clearing old session files...\n";
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    $deletedCount = 0;
    
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            // Only delete files older than 1 hour to avoid disrupting active sessions
            if (filemtime($file) < (time() - 3600)) {
                if (unlink($file)) {
                    $deletedCount++;
                }
            }
        }
    }
    echo "   ✓ Deleted $deletedCount old session files\n";
} else {
    echo "   ✗ Session directory not found\n";
}

// 5. Clear Compiled Classes
$compiledPath = __DIR__ . '/bootstrap/cache/packages.php';
echo "\n5. Clearing compiled classes...\n";
if (file_exists($compiledPath)) {
    if (unlink($compiledPath)) {
        echo "   ✓ Compiled classes cleared\n";
    } else {
        echo "   ✗ Failed to clear compiled classes\n";
    }
} else {
    echo "   ℹ No compiled classes cache found\n";
}

// 6. Check for problematic files
echo "\n6. Checking for common issues...\n";

// Check if .env exists
if (file_exists(__DIR__ . '/.env')) {
    echo "   ✓ .env file exists\n";
} else {
    echo "   ⚠ .env file missing - copy from .env.example\n";
}

// Check if storage directories are writable
$storageDir = __DIR__ . '/storage';
if (is_writable($storageDir)) {
    echo "   ✓ Storage directory is writable\n";
} else {
    echo "   ⚠ Storage directory is not writable\n";
}

// Check if bootstrap/cache is writable
$bootstrapCacheDir = __DIR__ . '/bootstrap/cache';
if (is_writable($bootstrapCacheDir)) {
    echo "   ✓ Bootstrap cache directory is writable\n";
} else {
    echo "   ⚠ Bootstrap cache directory is not writable\n";
}

echo "\n=== Cache Clear Complete ===\n";
echo "Your Laravel application should now work properly.\n";
echo "If you still get errors, check the Laravel log file:\n";
echo "storage/logs/laravel.log\n\n";

echo "Next steps:\n";
echo "1. Refresh your browser\n";
echo "2. If still getting errors, restart your web server\n";
echo "3. Check file permissions if needed\n\n";

echo "Done! ✨\n";
