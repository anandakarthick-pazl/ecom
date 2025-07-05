<?php
/**
 * Debug script to test order success flow
 * Run this with: php debug_checkout.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Create a kernel and handle a dummy request to bootstrap routes
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Request::create('/test-route-loading', 'GET')
);

echo "=== CHECKOUT FLOW DEBUG ===\n\n";

// Test order.success route
echo "Testing order.success route:\n";
try {
    $url = route('order.success', 'TEST123');
    echo "✅ order.success route resolves to: $url\n";
} catch (Exception $e) {
    echo "❌ order.success route error: " . $e->getMessage() . "\n";
}

// Check if CheckoutController exists
echo "\nChecking CheckoutController:\n";
if (class_exists('App\\Http\\Controllers\\CheckoutController')) {
    echo "✅ CheckoutController exists\n";
    
    // Check if success method exists
    $reflection = new ReflectionClass('App\\Http\\Controllers\\CheckoutController');
    if ($reflection->hasMethod('success')) {
        echo "✅ success method exists\n";
    } else {
        echo "❌ success method missing\n";
    }
} else {
    echo "❌ CheckoutController missing\n";
}

// Check if Order model exists and has order_number field
echo "\nChecking Order model:\n";
if (class_exists('App\\Models\\Order')) {
    echo "✅ Order model exists\n";
} else {
    echo "❌ Order model missing\n";
}

// Check if order-success view exists
echo "\nChecking views:\n";
if (file_exists(__DIR__ . '/resources/views/order-success.blade.php')) {
    echo "✅ order-success.blade.php exists\n";
} else {
    echo "❌ order-success.blade.php missing\n";
}

if (file_exists(__DIR__ . '/resources/views/checkout.blade.php')) {
    echo "✅ checkout.blade.php exists\n";
} else {
    echo "❌ checkout.blade.php missing\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
