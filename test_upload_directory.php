<?php

/**
 * Test directory setup and file upload permissions
 * Run this: php test_upload_directory.php
 */

echo "Testing Upload Directory Setup\n";
echo "==============================\n\n";

// Define paths
$storagePath = 'storage/app';
$tempDir = $storagePath . DIRECTORY_SEPARATOR . 'temp';

echo "🔍 System Information:\n";
echo "- OS: " . PHP_OS . "\n";
echo "- PHP Version: " . PHP_VERSION . "\n";
echo "- Directory Separator: '" . DIRECTORY_SEPARATOR . "'\n";
echo "- Current Working Directory: " . getcwd() . "\n";
echo "- Storage Path: " . realpath($storagePath) . "\n\n";

echo "📁 Directory Tests:\n";

// Test 1: Check if storage/app exists
if (is_dir($storagePath)) {
    echo "✅ Storage directory exists: {$storagePath}\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n";
    echo "   Writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Storage directory missing: {$storagePath}\n";
}

// Test 2: Check temp directory
if (is_dir($tempDir)) {
    echo "✅ Temp directory exists: {$tempDir}\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($tempDir)), -4) . "\n";
    echo "   Writable: " . (is_writable($tempDir) ? 'Yes' : 'No') . "\n";
} else {
    echo "⚠️  Temp directory missing: {$tempDir}\n";
    echo "   Attempting to create...\n";
    
    if (mkdir($tempDir, 0755, true)) {
        echo "✅ Temp directory created successfully\n";
        echo "   Permissions: " . substr(sprintf('%o', fileperms($tempDir)), -4) . "\n";
        echo "   Writable: " . (is_writable($tempDir) ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Failed to create temp directory\n";
    }
}

echo "\n📝 File Write Test:\n";

// Test 3: Try writing a test file
$testFile = $tempDir . DIRECTORY_SEPARATOR . 'test_' . time() . '.txt';
$testContent = "Test file created at " . date('Y-m-d H:i:s');

if (is_dir($tempDir)) {
    if (file_put_contents($testFile, $testContent)) {
        echo "✅ Test file created: " . basename($testFile) . "\n";
        echo "   File size: " . filesize($testFile) . " bytes\n";
        echo "   File exists: " . (file_exists($testFile) ? 'Yes' : 'No') . "\n";
        echo "   Readable: " . (is_readable($testFile) ? 'Yes' : 'No') . "\n";
        
        // Clean up test file
        if (unlink($testFile)) {
            echo "✅ Test file cleaned up\n";
        } else {
            echo "⚠️  Failed to clean up test file\n";
        }
    } else {
        echo "❌ Failed to create test file in: {$tempDir}\n";
        echo "   Error: " . error_get_last()['message'] ?? 'Unknown error' . "\n";
    }
} else {
    echo "❌ Cannot test file writing - temp directory doesn't exist\n";
}

echo "\n🔧 PHP Configuration:\n";
echo "- upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "- post_max_size: " . ini_get('post_max_size') . "\n";
echo "- max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "- memory_limit: " . ini_get('memory_limit') . "\n";
echo "- file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "\n";
echo "- upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default') . "\n";

echo "\n💡 Recommendations:\n";
if (!is_dir($tempDir)) {
    echo "- Create temp directory: mkdir -p {$tempDir}\n";
}
if (is_dir($tempDir) && !is_writable($tempDir)) {
    echo "- Fix permissions: chmod 755 {$tempDir}\n";
}

echo "\n==============================\n";
echo "Directory test complete!\n";

// Additional Laravel-specific test if we're in Laravel context
if (function_exists('storage_path')) {
    echo "\n🚀 Laravel Storage Test:\n";
    $laravelStoragePath = storage_path('app');
    $laravelTempDir = storage_path('app/temp');
    
    echo "- Laravel storage_path('app'): {$laravelStoragePath}\n";
    echo "- Laravel temp directory: {$laravelTempDir}\n";
    echo "- Laravel temp dir exists: " . (is_dir($laravelTempDir) ? 'Yes' : 'No') . "\n";
    echo "- Laravel temp dir writable: " . (is_dir($laravelTempDir) && is_writable($laravelTempDir) ? 'Yes' : 'No') . "\n";
}
