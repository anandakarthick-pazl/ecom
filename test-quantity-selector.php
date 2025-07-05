<?php
// Test file to verify quantity selector functionality
// Run this file after clearing cache: php test-quantity-selector.php

echo "=== Testing Quantity Selector Implementation ===\n\n";

// Check if all view files exist and have been updated
$viewFiles = [
    'resources/views/home.blade.php',
    'resources/views/category.blade.php', 
    'resources/views/search.blade.php',
    'resources/views/product.blade.php',
    'resources/views/layouts/app.blade.php'
];

echo "1. Checking view files...\n";
foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for quantity selector HTML
        if (strpos($content, 'quantity-selector') !== false) {
            echo "   ✓ $file - Updated with quantity selector\n";
        } else {
            echo "   ✗ $file - Missing quantity selector\n";
        }
        
        // Check for JavaScript functions
        if ($file === 'resources/views/layouts/app.blade.php') {
            $functions = ['addToCartWithQuantity', 'incrementQuantity', 'decrementQuantity'];
            foreach ($functions as $func) {
                if (strpos($content, "function $func") !== false) {
                    echo "   ✓ Found function: $func\n";
                } else {
                    echo "   ✗ Missing function: $func\n";
                }
            }
        }
    } else {
        echo "   ✗ $file - File not found\n";
    }
}

echo "\n2. Checking CartController...\n";
$controllerFile = 'app/Http/Controllers/CartController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    if (strpos($content, '$request->quantity ?? 1') !== false) {
        echo "   ✓ CartController supports quantity parameter\n";
    } else {
        echo "   ✗ CartController may need quantity support\n";
    }
}

echo "\n3. Manual Testing Checklist:\n";
echo "   □ Clear all caches: php artisan cache:clear && php artisan view:clear\n";
echo "   □ Start server: php artisan serve\n";
echo "   □ Visit home page and check product cards have quantity selectors\n";
echo "   □ Test increment/decrement buttons\n";
echo "   □ Add products with different quantities\n";
echo "   □ Verify cart count updates correctly\n";
echo "   □ Check quantity limits based on stock\n";
echo "   □ Test on mobile view for responsiveness\n";

echo "\n=== Test Complete ===\n";
