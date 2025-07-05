<?php

/**
 * Route Testing Script for Cart Issues
 * Run this with: php test_routes.php
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

// Get the router instance
$router = $app['router'];

echo "=== CART ROUTE TESTING ===\n\n";

// Get all registered routes
$routes = $router->getRoutes();

// Filter cart routes
$cartRoutes = [];
foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && str_contains($name, 'cart')) {
        $cartRoutes[] = [
            'name' => $name,
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName()
        ];
    }
}

if (empty($cartRoutes)) {
    echo "❌ NO CART ROUTES FOUND!\n";
    echo "This indicates a route registration problem.\n\n";
    
    // Check if CartController exists
    if (class_exists('App\\Http\\Controllers\\CartController')) {
        echo "✅ CartController class exists\n";
    } else {
        echo "❌ CartController class NOT found\n";
    }
    
    // Check if web routes file exists
    if (file_exists(__DIR__ . '/routes/web.php')) {
        echo "✅ routes/web.php exists\n";
        
        // Read and check for cart routes in file
        $webRoutes = file_get_contents(__DIR__ . '/routes/web.php');
        if (str_contains($webRoutes, 'cart.index')) {
            echo "✅ cart.index route definition found in web.php\n";
        } else {
            echo "❌ cart.index route definition NOT found in web.php\n";
        }
    } else {
        echo "❌ routes/web.php NOT found\n";
    }
    
} else {
    echo "✅ CART ROUTES FOUND:\n\n";
    foreach ($cartRoutes as $route) {
        echo sprintf(
            "Route: %s\nURI: %s\nMethods: %s\nController: %s\n\n",
            $route['name'],
            $route['uri'],
            $route['methods'],
            $route['action']
        );
    }
}

// Test specific route
echo "=== TESTING cart.index ROUTE ===\n";
try {
    $url = route('cart.index');
    echo "✅ cart.index route resolves to: $url\n";
} catch (Exception $e) {
    echo "❌ cart.index route error: " . $e->getMessage() . "\n";
}

echo "\n=== ROUTE TESTING COMPLETE ===\n";
