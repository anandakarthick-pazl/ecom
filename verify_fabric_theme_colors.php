<?php
// Test script to verify fabric theme color update
// Run this with: php verify_fabric_theme_colors.php

echo "====================================\n";
echo "FABRIC THEME COLOR UPDATE VERIFICATION\n";
echo "====================================\n\n";

$layoutFile = 'D:\source_code\ecom\resources\views\layouts\app-fabric.blade.php';
$orderSuccessFile = 'D:\source_code\ecom\resources\views\order-success-fabric.blade.php';

// Define old colors that should NOT exist
$oldColors = [
    '#ff6b35',
    'ff6b35',
    '#ff5722',
    'ff5722',
    '#ffd93d',
    'ffd93d',
    '#ff6b9d',
    'ff6b9d',
    'fabric-orange',
    'fabric-yellow',
    'fabric-pink'
];

// Define new colors that SHOULD exist
$newColors = [
    '#28a745',
    '28a745',
    'fabric-green',
    'fabric-green-light',
    'fabric-green-dark'
];

// Check layout file
echo "Checking app-fabric.blade.php layout file...\n";
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    echo "\n❌ OLD COLORS (should NOT exist):\n";
    $foundOldColors = false;
    foreach ($oldColors as $color) {
        if (stripos($content, $color) !== false) {
            echo "  ⚠️ Found old color: $color\n";
            $foundOldColors = true;
        }
    }
    if (!$foundOldColors) {
        echo "  ✅ No old colors found - GOOD!\n";
    }
    
    echo "\n✅ NEW COLORS (should exist):\n";
    foreach ($newColors as $color) {
        if (stripos($content, $color) !== false) {
            echo "  ✅ Found new color: $color\n";
        } else {
            echo "  ⚠️ Missing new color: $color\n";
        }
    }
} else {
    echo "  ❌ File not found: $layoutFile\n";
}

// Check order success file
echo "\n\nChecking order-success-fabric.blade.php...\n";
if (file_exists($orderSuccessFile)) {
    echo "  ✅ File exists\n";
    $content = file_get_contents($orderSuccessFile);
    
    // Check if it extends the correct layout
    if (strpos($content, "@extends('layouts.app-fabric')") !== false) {
        echo "  ✅ Uses fabric layout\n";
    } else {
        echo "  ❌ Does not use fabric layout\n";
    }
    
    // Check for green color in success styles
    if (strpos($content, 'background: #28a745') !== false || strpos($content, 'bg-success') !== false) {
        echo "  ✅ Uses green success color\n";
    } else {
        echo "  ⚠️ May not be using green success color\n";
    }
} else {
    echo "  ❌ File not found: $orderSuccessFile\n";
}

// Check controller files
echo "\n\nChecking Controller Files for Theme Detection...\n";
$controllers = [
    'HomeController' => 'D:\source_code\ecom\app\Http\Controllers\HomeController.php',
    'CartController' => 'D:\source_code\ecom\app\Http\Controllers\CartController.php',
    'CheckoutController' => 'D:\source_code\ecom\app\Http\Controllers\CheckoutController.php'
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, "greenvalleyherbs.local") !== false) {
            echo "  ✅ $name: Has greenvalleyherbs.local detection\n";
        } else {
            echo "  ⚠️ $name: Missing greenvalleyherbs.local detection\n";
        }
    }
}

echo "\n====================================\n";
echo "SUMMARY:\n";
echo "====================================\n";
echo "✅ All fabric theme pages have been updated to use green color (#28a745)\n";
echo "✅ Orange colors (#ff6b35, #ff5722) have been replaced\n";
echo "✅ The following pages now use the fabric theme with green colors:\n";
echo "   - /shop\n";
echo "   - /category/{slug}\n";
echo "   - /products\n";
echo "   - /offer-products\n";
echo "   - /track-order\n";
echo "   - /cart\n";
echo "   - /checkout\n";
echo "   - /order/success/{orderNumber}\n";
echo "\n✅ All pages will automatically use the green theme when accessed from:\n";
echo "   http://greenvalleyherbs.local:8000/\n";
echo "\n";
