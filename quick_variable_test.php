<?php
/**
 * Quick test to isolate the variable error
 */

echo "=== QUICK VARIABLE ERROR TEST ===\n";

// Test 1: Basic PHP syntax check
echo "1. Testing basic PHP execution...\n";
try {
    echo "✅ PHP is working\n";
} catch (Error $e) {
    echo "❌ PHP Error: " . $e->getMessage() . "\n";
}

// Test 2: Try to load Laravel with minimal setup
echo "\n2. Testing Laravel bootstrap...\n";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ Autoloader works\n";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ App bootstrap works\n";
    
    // Test if we can resolve a simple service
    $config = $app->make('config');
    echo "✅ Config service works\n";
    
} catch (ParseError $e) {
    echo "❌ PARSE ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

// Test 3: Check for problematic constants
echo "\n3. Checking for undefined constants...\n";
$definedConstants = get_defined_constants(true)['user'];
if (array_key_exists('variable', $definedConstants)) {
    echo "⚠️ Found 'variable' constant: " . $definedConstants['variable'] . "\n";
} else {
    echo "✅ No 'variable' constant found\n";
}

// Test 4: Simple route test
echo "\n4. Testing route resolution...\n";
try {
    if (isset($app)) {
        $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
        echo "✅ HTTP Kernel resolved\n";
        
        // Check if routes are loadable
        $router = $app->make('router');
        echo "✅ Router resolved\n";
    }
} catch (Exception $e) {
    echo "❌ Route test failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "If this script runs without errors, the issue is likely in:\n";
echo "1. The specific controller or view being accessed\n";
echo "2. A cached configuration file\n";
echo "3. A Blade template compilation issue\n";
