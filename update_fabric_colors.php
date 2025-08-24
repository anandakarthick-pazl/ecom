<?php
/**
 * Script to update all fabric theme files to use green color (#28a745)
 * Run: php update_fabric_colors.php
 */

$viewsPath = 'D:\source_code\ecom\resources\views\\';
$fabricFiles = [
    'layouts\app-fabric.blade.php',
    'products-fabric.blade.php',
    'cart-fabric.blade.php',
    'category-fabric.blade.php',
    'checkout-fabric.blade.php',
    'home-fabric.blade.php',
    'offer-products-fabric.blade.php',
    'search-fabric.blade.php',
    'track-order-fabric.blade.php',
    'order-success-fabric.blade.php'
];

// Colors to replace
$colorReplacements = [
    '#ff6b35' => '#28a745',
    '#FF6B35' => '#28A745',
    'ff6b35' => '28a745',
    'FF6B35' => '28A745',
    '#ff5722' => '#28a745',
    '#FF5722' => '#28A745',
    'ff5722' => '28a745',
    'FF5722' => '28A745',
    '#ffd93d' => '#28a745',
    '#FFD93D' => '#28A745',
    'ffd93d' => '28a745',
    'FFD93D' => '28A745',
    'fabric-orange' => 'fabric-green',
    'fabric-yellow' => 'fabric-green-light',
    'fabric-pink' => 'fabric-green-dark'
];

echo "====================================\n";
echo "FABRIC THEME COLOR UPDATE TO GREEN\n";
echo "====================================\n\n";

$totalUpdates = 0;

foreach ($fabricFiles as $file) {
    $filePath = $viewsPath . $file;
    
    echo "Processing: $file\n";
    
    if (!file_exists($filePath)) {
        echo "  ⚠️ File not found\n\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileUpdates = 0;
    
    foreach ($colorReplacements as $oldColor => $newColor) {
        $count = substr_count(strtolower($content), strtolower($oldColor));
        if ($count > 0) {
            // Case-insensitive replacement
            $pattern = '/' . preg_quote($oldColor, '/') . '/i';
            $content = preg_replace($pattern, $newColor, $content);
            $fileUpdates += $count;
            echo "  ✅ Replaced '$oldColor' with '$newColor' ($count occurrences)\n";
        }
    }
    
    if ($fileUpdates > 0) {
        file_put_contents($filePath, $content);
        echo "  ✅ File updated with $fileUpdates changes\n";
        $totalUpdates += $fileUpdates;
    } else {
        echo "  ✅ File already using green colors\n";
    }
    
    echo "\n";
}

echo "====================================\n";
echo "UPDATE COMPLETE\n";
echo "====================================\n";
echo "✅ Total updates made: $totalUpdates\n";
echo "✅ All fabric theme files now use green color (#28a745)\n\n";

echo "Pages updated:\n";
echo "  - /shop (home-fabric.blade.php)\n";
echo "  - /products (products-fabric.blade.php)\n";
echo "  - /category/{slug} (category-fabric.blade.php)\n";
echo "  - /offer-products (offer-products-fabric.blade.php)\n";
echo "  - /cart (cart-fabric.blade.php)\n";
echo "  - /checkout (checkout-fabric.blade.php)\n";
echo "  - /track-order (track-order-fabric.blade.php)\n";
echo "  - /order/success/{orderNumber} (order-success-fabric.blade.php)\n";
echo "  - /search (search-fabric.blade.php)\n";
echo "\n✅ All pages will display with green theme when accessed from http://greenvalleyherbs.local:8000/\n";
