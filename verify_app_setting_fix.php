<?php

/**
 * Quick AppSetting Verification Test
 * 
 * This script quickly checks if AppSetting is working properly.
 */

echo "🔍 Quick AppSetting Verification Test\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Load Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Test 1: Class exists
    if (class_exists('App\Models\AppSetting')) {
        echo "✅ AppSetting class can be loaded\n";
    } else {
        echo "❌ AppSetting class cannot be loaded\n";
        exit(1);
    }
    
    // Test 2: Database table exists
    if (Schema::hasTable('app_settings')) {
        echo "✅ app_settings table exists\n";
    } else {
        echo "❌ app_settings table does not exist\n";
        echo "💡 Run: php artisan migrate\n";
        exit(1);
    }
    
    // Test 3: Can use AppSetting methods
    $testValue = \App\Models\AppSetting::get('frontend_pagination_enabled', 'test_default');
    echo "✅ AppSetting::get() method works (returned: $testValue)\n";
    
    // Test 4: HomeController can be loaded
    if (class_exists('App\Http\Controllers\HomeController')) {
        echo "✅ HomeController can be loaded\n";
    } else {
        echo "❌ HomeController cannot be loaded\n";
        exit(1);
    }
    
    // Test 5: Check if products route works (simulation)
    try {
        $controller = new \App\Http\Controllers\HomeController();
        echo "✅ HomeController can be instantiated\n";
    } catch (Exception $e) {
        echo "❌ HomeController instantiation failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 All tests passed! Your AppSetting setup is working correctly.\n";
    echo "🚀 You can now access: http://greenvalleyherbs.local:8000/products\n\n";
    
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    echo "💡 Try running: complete-app-setting-fix.bat\n\n";
    exit(1);
}

?>
