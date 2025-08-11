<?php

/**
 * Add this to your routes/web.php temporarily to test storage
 * Route::get('/test-storage', function() { require base_path('test_storage_route.php'); });
 */

echo "<!DOCTYPE html><html><head><title>Storage Test</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:40px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .warning{color:#ffc107;} pre{background:#fff;padding:15px;border-radius:5px;}</style>";
echo "</head><body>";

echo "<h1>🔧 Laravel Storage Test</h1>";

// Test Laravel storage paths
$appPath = storage_path('app');
$tempPath = storage_path('app') . DIRECTORY_SEPARATOR . 'temp';

echo "<h2>📍 Path Information</h2>";
echo "<pre>";
echo "App Path: {$appPath}\n";
echo "Temp Path: {$tempPath}\n";
echo "Directory Separator: '" . DIRECTORY_SEPARATOR . "'\n";
echo "Current Working Directory: " . getcwd() . "\n";
echo "</pre>";

echo "<h2>🔍 Directory Tests</h2>";

$results = [];

// Test app directory
if (is_dir($appPath)) {
    echo "<p class='success'>✅ App directory exists and is accessible</p>";
    $results['app_dir'] = true;
    echo "<ul>";
    echo "<li>Writable: " . (is_writable($appPath) ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</li>";
    echo "<li>Permissions: " . substr(sprintf('%o', fileperms($appPath)), -4) . "</li>";
    echo "</ul>";
} else {
    echo "<p class='error'>❌ App directory missing or inaccessible</p>";
    $results['app_dir'] = false;
}

// Test temp directory
if (is_dir($tempPath)) {
    echo "<p class='success'>✅ Temp directory exists</p>";
    $results['temp_dir'] = true;
    echo "<ul>";
    echo "<li>Writable: " . (is_writable($tempPath) ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</li>";
    echo "<li>Permissions: " . substr(sprintf('%o', fileperms($tempPath)), -4) . "</li>";
    echo "</ul>";
} else {
    echo "<p class='warning'>⚠️ Temp directory missing</p>";
    echo "<p>Attempting to create...</p>";
    
    if (mkdir($tempPath, 0755, true)) {
        echo "<p class='success'>✅ Temp directory created successfully!</p>";
        $results['temp_dir'] = true;
        echo "<ul>";
        echo "<li>Writable: " . (is_writable($tempPath) ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</li>";
        echo "<li>Permissions: " . substr(sprintf('%o', fileperms($tempPath)), -4) . "</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ Failed to create temp directory</p>";
        echo "<p>Error: " . (error_get_last()['message'] ?? 'Unknown') . "</p>";
        $results['temp_dir'] = false;
    }
}

echo "<h2>📝 File Write Test</h2>";

if (isset($results['temp_dir']) && $results['temp_dir'] && is_writable($tempPath)) {
    $testFile = $tempPath . DIRECTORY_SEPARATOR . 'web_test_' . time() . '.txt';
    $content = "Web storage test - " . date('Y-m-d H:i:s');
    
    if (file_put_contents($testFile, $content)) {
        echo "<p class='success'>✅ File write test successful!</p>";
        echo "<ul>";
        echo "<li>File: " . basename($testFile) . "</li>";
        echo "<li>Size: " . filesize($testFile) . " bytes</li>";
        echo "<li>Content: " . htmlspecialchars(file_get_contents($testFile)) . "</li>";
        echo "</ul>";
        
        // Cleanup
        if (unlink($testFile)) {
            echo "<p class='success'>✅ Test file cleaned up</p>";
        }
        $results['file_write'] = true;
    } else {
        echo "<p class='error'>❌ File write test failed</p>";
        $results['file_write'] = false;
    }
} else {
    echo "<p class='error'>❌ Cannot test file writing - temp directory not available or writable</p>";
    $results['file_write'] = false;
}

echo "<h2>🎯 Summary</h2>";

$allGood = $results['app_dir'] && $results['temp_dir'] && ($results['file_write'] ?? false);

if ($allGood) {
    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:5px;'>";
    echo "<h3>✅ All Tests Passed!</h3>";
    echo "<p>Your storage directory is properly configured and writable. Bulk upload should work now.</p>";
    echo "</div>";
} else {
    echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:15px;border-radius:5px;'>";
    echo "<h3>❌ Issues Found</h3>";
    echo "<p>Please fix the directory issues above before attempting bulk upload.</p>";
    echo "</div>";
}

echo "<h2>🔧 Manual Fix Commands</h2>";
echo "<pre>";
echo "# Run these commands in your project root:\n";
echo "mkdir -p " . $tempPath . "\n";
echo "chmod 755 " . $tempPath . "\n";
echo "chmod 755 " . $appPath . "\n";
echo "</pre>";

echo "<p><a href='" . url('/admin/products') . "'>← Back to Products</a></p>";

echo "</body></html>";
