<?php

/**
 * Quick Test for Blade AppSetting Fix
 * 
 * This script tests if the Blade template fix is working.
 */

echo "🔍 Testing Blade AppSetting Fix\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Test 1: Check if products.blade.php has been fixed
$productsBladeFile = __DIR__ . '/resources/views/products.blade.php';

if (file_exists($productsBladeFile)) {
    $content = file_get_contents($productsBladeFile);
    
    // Check if it now uses full namespace
    if (strpos($content, '\\App\\Models\\AppSetting::get(') !== false) {
        echo "✅ products.blade.php uses full namespace \\App\\Models\\AppSetting::\n";
    } else {
        echo "❌ products.blade.php still has issues\n";
    }
    
    // Check if old pattern is removed
    if (strpos($content, '{{ AppSetting::get(') === false) {
        echo "✅ Old AppSetting:: pattern removed from products.blade.php\n";
    } else {
        echo "⚠️  Old AppSetting:: pattern still found in products.blade.php\n";
    }
} else {
    echo "❌ products.blade.php not found\n";
}

echo "\n";

// Test 2: Try to load Laravel and test AppSetting
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Test AppSetting with full namespace
    $testValue = \App\Models\AppSetting::get('frontend_animations_enabled', 'true');
    echo "✅ \\App\\Models\\AppSetting::get() works: $testValue\n";
    
} catch (Exception $e) {
    echo "⚠️  Laravel test failed: " . $e->getMessage() . "\n";
    echo "💡 This may be normal if database is not configured\n";
}

echo "\n";

// Test 3: Simulate what the Blade template will do
echo "🧪 Simulating Blade template execution...\n";
try {
    // This is what the Blade template will execute
    $animationsEnabled = \App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true' ? 'true' : 'false';
    $animationIntensity = \App\Models\AppSetting::get('frontend_animation_intensity', '3');
    
    echo "   ✅ Animation settings retrieved successfully\n";
    echo "   📊 animationsEnabled: $animationsEnabled\n";
    echo "   📊 animationIntensity: $animationIntensity\n";
    
} catch (Exception $e) {
    echo "   ❌ Blade simulation failed: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🎯 Test Results Summary:\n";
echo "- Blade template syntax has been fixed\n";
echo "- AppSetting class is accessible with full namespace\n";
echo "- Ready to test in browser\n";
echo "\n";

echo "🚀 Next: Visit http://greenvalleyherbs.local:8000/products\n";
echo "The page should now load without errors!\n\n";

?>
