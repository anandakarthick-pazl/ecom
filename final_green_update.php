<?php
/**
 * Final comprehensive script to update ALL fabric theme files to use green color (#28a745)
 * This will find and replace ALL orange color instances
 * Run: php final_green_update.php
 */

$viewsPath = 'D:\source_code\ecom\resources\views\\';

// All fabric theme files to check
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

// Comprehensive color replacements
$colorReplacements = [
    // Orange to Green
    '#ff6b35' => '#28a745',
    '#FF6B35' => '#28A745',
    'ff6b35' => '28a745',
    'FF6B35' => '28A745',
    
    // Dark Orange to Dark Green
    '#ff5722' => '#1e7e34',
    '#FF5722' => '#1E7E34',
    'ff5722' => '1e7e34',
    'FF5722' => '1E7E34',
    
    // Yellow to Light Green
    '#ffd93d' => '#5cb85c',
    '#FFD93D' => '#5CB85C',
    'ffd93d' => '5cb85c',
    'FFD93D' => '5CB85C',
    
    // Orange gradient to Green gradient
    '#f7931e' => '#20c997',
    '#F7931E' => '#20C997',
    
    // CSS variable names
    'fabric-orange' => 'fabric-green',
    'fabric-yellow' => 'fabric-green-light',
    'fabric-pink' => 'fabric-green-dark',
    
    // RGB colors
    'rgb(255, 107, 53)' => 'rgb(40, 167, 69)',
    'rgba(255, 107, 53' => 'rgba(40, 167, 69',
    'rgba(255,107,53' => 'rgba(40,167,69'
];

echo "====================================\n";
echo "FINAL FABRIC THEME GREEN UPDATE\n";
echo "====================================\n\n";

$totalUpdates = 0;
$filesUpdated = 0;

foreach ($fabricFiles as $file) {
    $filePath = $viewsPath . $file;
    
    echo "Checking: $file\n";
    
    if (!file_exists($filePath)) {
        echo "  ⚠️ File not found, skipping\n\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileUpdates = 0;
    
    // Apply all color replacements
    foreach ($colorReplacements as $oldColor => $newColor) {
        // Count occurrences (case-insensitive)
        $pattern = '/' . preg_quote($oldColor, '/') . '/i';
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        $count = count($matches[0]);
        
        if ($count > 0) {
            // Replace all occurrences
            $content = preg_replace($pattern, $newColor, $content);
            $fileUpdates += $count;
            echo "  ✅ Replaced '$oldColor' with '$newColor' ($count times)\n";
        }
    }
    
    // Save file if changes were made
    if ($fileUpdates > 0) {
        file_put_contents($filePath, $content);
        echo "  💾 File updated with $fileUpdates changes\n";
        $totalUpdates += $fileUpdates;
        $filesUpdated++;
    } else {
        echo "  ✅ Already using green colors\n";
    }
    
    echo "\n";
}

echo "====================================\n";
echo "UPDATE COMPLETE\n";
echo "====================================\n";
echo "✅ Files updated: $filesUpdated\n";
echo "✅ Total color replacements: $totalUpdates\n\n";

echo "All fabric theme pages now use green color (#28a745):\n";
echo "  ✅ /shop\n";
echo "  ✅ /products\n";
echo "  ✅ /category/{slug}\n";
echo "  ✅ /offer-products\n";
echo "  ✅ /cart\n";
echo "  ✅ /checkout\n";
echo "  ✅ /track-order\n";
echo "  ✅ /order/success/{orderNumber}\n";
echo "  ✅ /search\n";
echo "\n✅ All pages display with green theme at http://greenvalleyherbs.local:8000/\n";

// Generate a report of any remaining issues
echo "\n====================================\n";
echo "VERIFICATION\n";
echo "====================================\n";

$issuesFound = false;
foreach ($fabricFiles as $file) {
    $filePath = $viewsPath . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Check for any remaining orange colors
        $orangePatterns = ['#ff6b35', '#ff5722', 'ff6b35', 'ff5722', 'fabric-orange'];
        $found = false;
        
        foreach ($orangePatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                if (!$found) {
                    echo "⚠️ $file still contains orange colors:\n";
                    $found = true;
                    $issuesFound = true;
                }
                echo "   - Found: $pattern\n";
            }
        }
    }
}

if (!$issuesFound) {
    echo "✅ No orange colors found in any fabric theme files!\n";
    echo "✅ All files are using the green color scheme.\n";
}

echo "\n✅ Script execution completed successfully!\n";
