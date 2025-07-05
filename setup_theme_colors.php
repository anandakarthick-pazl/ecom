<?php
// Setup default theme colors

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\AppSetting;

echo "Setting up default theme colors...\n\n";

try {
    // Set company context for greenvalleyherbs (company_id = 1)
    session(['selected_company_id' => 1]);
    
    // Set default theme colors
    $themeSettings = [
        'primary_color' => '#2d5016',
        'secondary_color' => '#4a7c28',
        'sidebar_color' => '#2d5016',
        'theme_mode' => 'light',
    ];
    
    foreach ($themeSettings as $key => $value) {
        AppSetting::set($key, $value, 'string', 'theme');
        echo "✓ Set $key = $value\n";
    }
    
    echo "\nTheme colors set successfully!\n";
    echo "The bg-primary class will now use the dark green color (#2d5016)\n";
    
    // Clear cache
    AppSetting::clearCache();
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    
    echo "\nCache cleared. Your theme is ready!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
