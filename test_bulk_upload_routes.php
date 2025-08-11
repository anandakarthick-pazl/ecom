<?php

/**
 * Test script to verify bulk upload routes are working
 * Run this from the project root: php test_bulk_upload_routes.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Bulk Upload Routes...\n";
echo "===============================\n\n";

// Test 1: Check if routes exist
echo "1. Checking routes:\n";
$router = app('router');
$routes = $router->getRoutes();

$bulkUploadRoutes = [
    'admin.products.bulk-upload',
    'admin.products.process-bulk-upload', 
    'admin.products.download-template',
    'admin.products.upload-history'
];

foreach ($bulkUploadRoutes as $routeName) {
    try {
        $route = $routes->getByName($routeName);
        if ($route) {
            echo "✅ Route '{$routeName}' exists: {$route->uri()}\n";
        } else {
            echo "❌ Route '{$routeName}' NOT FOUND\n";
        }
    } catch (Exception $e) {
        echo "❌ Route '{$routeName}' ERROR: {$e->getMessage()}\n";
    }
}

echo "\n2. Checking controller methods:\n";
$controller = new App\Http\Controllers\Admin\ProductController();

$methods = [
    'showBulkUpload',
    'processBulkUpload',
    'downloadTemplate', 
    'uploadHistory'
];

foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✅ Method '{$method}' exists in ProductController\n";
    } else {
        echo "❌ Method '{$method}' NOT FOUND in ProductController\n";
    }
}

echo "\n3. Checking dependencies:\n";

// Check if maatwebsite/excel is installed
if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
    echo "✅ Laravel Excel (maatwebsite/excel) is installed\n";
} else {
    echo "❌ Laravel Excel (maatwebsite/excel) is NOT installed\n";
}

// Check if UploadLog model exists
if (class_exists('\App\Models\UploadLog')) {
    echo "✅ UploadLog model exists\n";
} else {
    echo "❌ UploadLog model NOT FOUND\n";
}

// Check if migration exists
$migrationFile = 'database/migrations/2024_01_01_000030_create_upload_logs_table.php';
if (file_exists($migrationFile)) {
    echo "✅ Upload logs migration file exists\n";
} else {
    echo "❌ Upload logs migration file NOT FOUND\n";
}

echo "\n4. Checking view files:\n";
$viewFiles = [
    'resources/views/admin/products/bulk-upload.blade.php',
    'resources/views/admin/products/upload-history.blade.php'
];

foreach ($viewFiles as $viewFile) {
    if (file_exists($viewFile)) {
        echo "✅ View file exists: {$viewFile}\n";
    } else {
        echo "❌ View file NOT FOUND: {$viewFile}\n";
    }
}

echo "\n5. Testing route generation:\n";
try {
    $bulkUploadUrl = route('admin.products.bulk-upload');
    echo "✅ Bulk upload URL: {$bulkUploadUrl}\n";
} catch (Exception $e) {
    echo "❌ Failed to generate bulk upload URL: {$e->getMessage()}\n";
}

try {
    $templateUrl = route('admin.products.download-template');
    echo "✅ Template download URL: {$templateUrl}\n";
} catch (Exception $e) {
    echo "❌ Failed to generate template URL: {$e->getMessage()}\n";
}

echo "\n6. Checking storage directories:\n";
$directories = [
    'storage/app/temp',
    'storage/app/public/products',
    'public/storage'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory exists: {$dir}\n";
    } else {
        echo "⚠️ Directory missing (will be created): {$dir}\n";
    }
}

echo "\n===============================\n";
echo "Bulk Upload Test Complete!\n";
echo "===============================\n";

// Summary
echo "\nSUMMARY:\n";
echo "If all routes show ✅, your bulk upload functionality should work.\n";
echo "If you see ❌ errors, please check the specific issues mentioned.\n";
echo "\nTo access bulk upload:\n";
echo "1. Go to Admin → Products\n";
echo "2. Click 'Bulk Upload' dropdown button\n";
echo "3. Select 'Upload Products'\n";
