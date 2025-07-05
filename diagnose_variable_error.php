<?php
/**
 * Diagnostic script to identify the "Undefined constant variable" error
 */

echo "=== DIAGNOSING UNDEFINED CONSTANT 'variable' ERROR ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Check syntax of key files
echo "1. CHECKING SYNTAX OF KEY FILES:\n";
echo "=================================\n";

$filesToCheck = [
    __DIR__ . '/app/Http/Controllers/Admin/SettingsController.php',
    __DIR__ . '/app/Models/AppSetting.php',
    __DIR__ . '/resources/views/admin/settings/index.blade.php',
    __DIR__ . '/routes/web.php',
    __DIR__ . '/app/Http/Middleware/EnsureCompanyContext.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        echo "Checking: " . basename($file) . "\n";
        
        // Use php -l to check syntax
        $output = [];
        $returnCode = 0;
        exec("php -l \"$file\"", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "✅ Syntax OK\n";
        } else {
            echo "❌ SYNTAX ERROR FOUND:\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
    } else {
        echo "❌ File not found: $file\n";
    }
    echo "-------------------\n";
}

// Step 2: Try to load the specific controller
echo "\n2. TESTING CONTROLLER LOADING:\n";
echo "===============================\n";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ Autoloader loaded\n";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ App bootstrapped\n";
    
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    echo "✅ Laravel bootstrapped\n";
    
    // Try to instantiate the SettingsController
    $controller = new \App\Http\Controllers\Admin\SettingsController();
    echo "✅ SettingsController instantiated successfully\n";
    
} catch (ParseError $e) {
    echo "❌ PARSE ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

// Step 3: Check for specific pattern that might cause the error
echo "\n3. SEARCHING FOR POTENTIAL ISSUES:\n";
echo "===================================\n";

$searchPatterns = [
    'variable' => 'Bare word "variable"',
    '\$variable' => 'Undefined variable',
    'Variable' => 'Undefined constant Variable',
    '{{variable}}' => 'Blade syntax issue'
];

foreach ($searchPatterns as $pattern => $description) {
    echo "Searching for: $description\n";
    
    $command = "findstr /R /S /N \"$pattern\" \"" . __DIR__ . "\\app\\*\" \"" . __DIR__ . "\\resources\\*\"";
    $output = [];
    exec($command, $output);
    
    if (!empty($output)) {
        echo "Found matches:\n";
        foreach (array_slice($output, 0, 5) as $match) {
            echo "  $match\n";
        }
        if (count($output) > 5) {
            echo "  ... and " . (count($output) - 5) . " more\n";
        }
    } else {
        echo "No matches found\n";
    }
    echo "-------------------\n";
}

// Step 4: Check recent Laravel log for the specific error
echo "\n4. CHECKING RECENT LOGS FOR VARIABLE ERROR:\n";
echo "============================================\n";

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    
    $variableErrors = [];
    foreach (array_reverse($lines) as $index => $line) {
        if (stripos($line, 'variable') !== false && 
            (stripos($line, 'undefined') !== false || stripos($line, 'constant') !== false)) {
            $variableErrors[] = $line;
            if (count($variableErrors) >= 5) break;
        }
    }
    
    if (!empty($variableErrors)) {
        echo "Recent variable-related errors:\n";
        foreach ($variableErrors as $error) {
            echo "- " . substr($error, 0, 200) . "...\n";
        }
    } else {
        echo "No recent variable-related errors in logs\n";
    }
} else {
    echo "Log file not found\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "If no issues found above, the error might be in a cached file.\n";
echo "Try running: php artisan config:clear && php artisan route:clear\n";
