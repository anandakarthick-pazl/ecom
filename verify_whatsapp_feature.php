<?php
/**
 * WHATSAPP BILL PDF FEATURE - VERIFICATION SCRIPT
 * Run this script to verify all components are properly installed and configured
 */

echo "=== WHATSAPP BILL PDF FEATURE VERIFICATION ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$allGood = true;

// Check 1: Required Classes
echo "1. CHECKING REQUIRED CLASSES:\n";
echo "==============================\n";

$requiredClasses = [
    'App\Models\SuperAdmin\WhatsAppConfig' => 'WhatsApp Configuration Model',
    'App\Services\TwilioWhatsAppService' => 'Twilio WhatsApp Service',
    'App\Services\BillPDFService' => 'Bill PDF Generation Service',
    'App\Http\Controllers\Admin\OrderController' => 'Admin Order Controller',
    'App\Http\Controllers\SuperAdmin\WhatsAppController' => 'Super Admin WhatsApp Controller'
];

foreach ($requiredClasses as $class => $description) {
    if (class_exists($class)) {
        echo "✅ $description: $class\n";
    } else {
        echo "❌ $description: $class (NOT FOUND)\n";
        $allGood = false;
    }
}

// Check 2: Required Files
echo "\n2. CHECKING REQUIRED FILES:\n";
echo "============================\n";

$requiredFiles = [
    __DIR__ . '/app/Models/SuperAdmin/WhatsAppConfig.php' => 'WhatsApp Config Model',
    __DIR__ . '/app/Services/TwilioWhatsAppService.php' => 'Twilio Service',
    __DIR__ . '/app/Services/BillPDFService.php' => 'PDF Service',
    __DIR__ . '/app/Http/Controllers/Admin/OrderController.php' => 'Order Controller',
    __DIR__ . '/app/Http/Controllers/SuperAdmin/WhatsAppController.php' => 'WhatsApp Controller',
    __DIR__ . '/resources/views/admin/orders/show.blade.php' => 'Order Detail View',
    __DIR__ . '/resources/views/admin/orders/bill-pdf.blade.php' => 'Bill PDF Template'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description: " . basename($file) . "\n";
    } else {
        echo "❌ $description: " . basename($file) . " (NOT FOUND)\n";
        $allGood = false;
    }
}

// Check 3: Required Methods
echo "\n3. CHECKING REQUIRED METHODS:\n";
echo "==============================\n";

try {
    $orderController = new ReflectionClass('App\Http\Controllers\Admin\OrderController');
    $requiredMethods = [
        'checkWhatsAppStatus' => 'WhatsApp Status Check',
        'downloadBill' => 'Bill PDF Download',
        'sendBillWhatsApp' => 'WhatsApp Bill Sending',
        'sendInvoice' => 'Email Invoice Sending'
    ];

    foreach ($requiredMethods as $method => $description) {
        if ($orderController->hasMethod($method)) {
            echo "✅ $description: $method()\n";
        } else {
            echo "❌ $description: $method() (NOT FOUND)\n";
            $allGood = false;
        }
    }
} catch (Exception $e) {
    echo "❌ Could not reflect OrderController: " . $e->getMessage() . "\n";
    $allGood = false;
}

// Check 4: Database Tables
echo "\n4. CHECKING DATABASE TABLES:\n";
echo "=============================\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $requiredTables = [
        'whatsapp_configs' => 'WhatsApp Configuration Storage',
        'app_settings' => 'Application Settings',
        'orders' => 'Order Management',
        'companies' => 'Company/Tenant Management'
    ];

    foreach ($requiredTables as $table => $description) {
        try {
            $exists = \Schema::hasTable($table);
            if ($exists) {
                echo "✅ $description: $table\n";
            } else {
                echo "❌ $description: $table (NOT FOUND)\n";
                $allGood = false;
            }
        } catch (Exception $e) {
            echo "❌ $description: $table (ERROR: " . $e->getMessage() . ")\n";
            $allGood = false;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Could not check database: " . $e->getMessage() . "\n";
    $allGood = false;
}

// Check 5: Composer Dependencies
echo "\n5. CHECKING COMPOSER DEPENDENCIES:\n";
echo "===================================\n";

$composerFile = __DIR__ . '/composer.json';
if (file_exists($composerFile)) {
    $composer = json_decode(file_get_contents($composerFile), true);
    $requiredPackages = [
        'twilio/sdk' => 'Twilio PHP SDK',
        'barryvdh/laravel-dompdf' => 'PDF Generation',
        'laravel/framework' => 'Laravel Framework'
    ];

    foreach ($requiredPackages as $package => $description) {
        if (isset($composer['require'][$package])) {
            echo "✅ $description: $package ({$composer['require'][$package]})\n";
        } else {
            echo "❌ $description: $package (NOT INSTALLED)\n";
            $allGood = false;
        }
    }
} else {
    echo "❌ composer.json not found\n";
    $allGood = false;
}

// Check 6: Routes
echo "\n6. CHECKING ROUTES:\n";
echo "===================\n";

$routeFile = __DIR__ . '/routes/web.php';
if (file_exists($routeFile)) {
    $routeContent = file_get_contents($routeFile);
    $requiredRoutes = [
        'orders.whatsapp-status' => 'WhatsApp Status Check Route',
        'orders.download-bill' => 'Bill Download Route',
        'orders.send-whatsapp-bill' => 'WhatsApp Bill Send Route'
    ];

    foreach ($requiredRoutes as $route => $description) {
        if (strpos($routeContent, $route) !== false) {
            echo "✅ $description: $route\n";
        } else {
            echo "❌ $description: $route (NOT FOUND)\n";
            $allGood = false;
        }
    }
} else {
    echo "❌ routes/web.php not found\n";
    $allGood = false;
}

// Check 7: Storage Directories
echo "\n7. CHECKING STORAGE DIRECTORIES:\n";
echo "=================================\n";

$requiredDirs = [
    __DIR__ . '/storage/app/temp' => 'Temporary Files Directory',
    __DIR__ . '/storage/framework/views' => 'Compiled Views Directory',
    __DIR__ . '/storage/logs' => 'Log Files Directory'
];

foreach ($requiredDirs as $dir => $description) {
    if (is_dir($dir)) {
        echo "✅ $description: " . basename($dir) . "\n";
    } else {
        echo "⚠️ $description: " . basename($dir) . " (WILL BE CREATED)\n";
        // Create the directory if it doesn't exist
        mkdir($dir, 0755, true);
        echo "✅ Created directory: " . basename($dir) . "\n";
    }
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "VERIFICATION SUMMARY\n";
echo str_repeat("=", 50) . "\n";

if ($allGood) {
    echo "🎉 ALL CHECKS PASSED! WhatsApp Bill PDF feature is ready to use.\n\n";
    echo "NEXT STEPS:\n";
    echo "1. Configure Twilio WhatsApp in Super Admin panel\n";
    echo "2. Test sending a bill via WhatsApp\n";
    echo "3. Check Laravel logs for any issues\n";
    echo "4. Refer to WHATSAPP_SETUP_GUIDE.md for detailed instructions\n";
} else {
    echo "❌ SOME CHECKS FAILED! Please review the issues above.\n\n";
    echo "COMMON SOLUTIONS:\n";
    echo "1. Run 'composer install' to install dependencies\n";
    echo "2. Run 'php artisan migrate' to create database tables\n";
    echo "3. Ensure all files are properly uploaded\n";
    echo "4. Check file permissions on storage directories\n";
}

echo "\n📧 If you need help, check the implementation documentation.\n";
echo "📅 Last verified: " . date('Y-m-d H:i:s') . "\n";
