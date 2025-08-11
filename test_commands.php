<?php
/**
 * Test Script to Check if Commands Work
 */

require_once __DIR__ . '/vendor/autoload.php';

try {
    // Load Laravel application
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel application loaded successfully!\n";
    echo "🔧 Testing command registration...\n\n";
    
    // Test if our command is registered
    $commands = $kernel->all();
    
    if (isset($commands['company:update'])) {
        echo "✅ company:update command is registered!\n";
        echo "📋 Command signature: " . $commands['company:update']->getSignature() . "\n";
        echo "📝 Description: " . $commands['company:update']->getDescription() . "\n";
    } else {
        echo "❌ company:update command is NOT registered.\n";
        echo "Available commands starting with 'company:':\n";
        foreach ($commands as $name => $command) {
            if (strpos($name, 'company:') === 0) {
                echo "  - $name\n";
            }
        }
    }
    
    echo "\n🏢 You can now update your company name using:\n";
    echo "php artisan company:update \"Your Company Name\" --email=\"your@email.com\"\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Suggestion: Please run 'php artisan optimize:clear' first\n";
}
