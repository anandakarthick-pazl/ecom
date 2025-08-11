<?php

/**
 * Laravel-specific directory test
 * Run this: php artisan tinker --execute="require 'test_laravel_storage.php';"
 * Or create a route to access this
 */

// Bootstrap Laravel (if running outside artisan)
if (!function_exists('storage_path')) {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
}

echo "Laravel Storage Directory Test\n";
echo "=============================\n\n";

// Test Laravel storage paths
$appPath = storage_path('app');
$tempPath = storage_path('app') . DIRECTORY_SEPARATOR . 'temp';

echo "📍 Laravel Paths:\n";
echo "- storage_path('app'): {$appPath}\n";
echo "- Temp directory: {$tempPath}\n";
echo "- Directory separator: '" . DIRECTORY_SEPARATOR . "'\n\n";

echo "🔍 Directory Status:\n";

// Check app directory
if (is_dir($appPath)) {
    echo "✅ App directory exists\n";
    echo "   Path: {$appPath}\n";
    echo "   Writable: " . (is_writable($appPath) ? 'Yes' : 'No') . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($appPath)), -4) . "\n";
} else {
    echo "❌ App directory missing: {$appPath}\n";
}

// Check temp directory
if (is_dir($tempPath)) {
    echo "✅ Temp directory exists\n";
    echo "   Path: {$tempPath}\n";
    echo "   Writable: " . (is_writable($tempPath) ? 'Yes' : 'No') . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($tempPath)), -4) . "\n";
} else {
    echo "⚠️  Temp directory missing\n";
    echo "   Attempting to create: {$tempPath}\n";
    
    if (mkdir($tempPath, 0755, true)) {
        echo "✅ Temp directory created\n";
        echo "   Writable: " . (is_writable($tempPath) ? 'Yes' : 'No') . "\n";
        echo "   Permissions: " . substr(sprintf('%o', fileperms($tempPath)), -4) . "\n";
    } else {
        echo "❌ Failed to create temp directory\n";
        echo "   Last error: " . (error_get_last()['message'] ?? 'Unknown') . "\n";
    }
}

echo "\n📝 File Operations Test:\n";

if (is_dir($tempPath) && is_writable($tempPath)) {
    $testFile = $tempPath . DIRECTORY_SEPARATOR . 'laravel_test_' . time() . '.txt';
    $content = "Laravel storage test - " . date('Y-m-d H:i:s');
    
    if (file_put_contents($testFile, $content)) {
        echo "✅ Test file created successfully\n";
        echo "   File: " . basename($testFile) . "\n";
        echo "   Size: " . filesize($testFile) . " bytes\n";
        echo "   Content: " . file_get_contents($testFile) . "\n";
        
        // Cleanup
        if (unlink($testFile)) {
            echo "✅ Test file cleaned up\n";
        }
    } else {
        echo "❌ Failed to create test file\n";
    }
} else {
    echo "❌ Cannot test file operations - directory not writable\n";
}

echo "\n🛠️  Filesystem Configuration:\n";
try {
    $config = config('filesystems.disks.local');
    echo "- Local disk root: " . ($config['root'] ?? 'Not set') . "\n";
    echo "- Default disk: " . config('filesystems.default') . "\n";
} catch (Exception $e) {
    echo "- Could not read filesystem config: " . $e->getMessage() . "\n";
}

echo "\n=============================\n";
echo "Laravel storage test complete!\n";

// Return success/failure status
return is_dir($tempPath) && is_writable($tempPath);
