<?php
/**
 * Quick cache clear after syntax fix
 */

echo "=== CLEARING CACHES AFTER SYNTAX FIX ===\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Clear all Laravel caches
    \Artisan::call('config:clear');
    echo "✅ Config cache cleared\n";
    
    \Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
    
    \Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
    
    \Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
    
    // Flush all caches
    \Cache::flush();
    echo "✅ All caches flushed\n";
    
    echo "\n✅ SYNTAX ERROR FIXED AND CACHES CLEARED!\n";
    echo "You can now access http://greenvalleyherbs.local:8000/admin/settings\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Try manually clearing caches:\n";
    echo "php artisan config:clear\n";
    echo "php artisan route:clear\n";
    echo "php artisan view:clear\n";
    echo "php artisan cache:clear\n";
}
