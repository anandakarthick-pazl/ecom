<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockNotificationController;
use App\Http\Controllers\StockNotificationControllerSimple;

// Emergency fix routes for stock notifications
Route::group(['prefix' => 'stock-notifications'], function () {
    // Test route to verify basic functionality
    Route::get('/test', function() {
        return response()->json([
            'success' => true,
            'message' => 'Stock notification routes are accessible!',
            'timestamp' => now(),
            'url_tested' => request()->url()
        ]);
    });
    
    // Backup original subscribe route with simple controller
    Route::post('/subscribe-simple', [App\Http\Controllers\StockNotificationControllerSimple::class, 'subscribe'])->name('stock-notification.subscribe-simple');
    
    // Test route for the simple controller
    Route::get('/test-simple', [App\Http\Controllers\StockNotificationControllerSimple::class, 'test'])->name('stock-notification.test-simple');
    
    // Override the main subscribe route temporarily
    Route::post('/subscribe', function(\Illuminate\Http\Request $request) {
        // Log the request
        \Log::info('Stock notification emergency route hit', [
            'data' => $request->all(),
            'ip' => $request->ip()
        ]);
        
        // Try to use the simple controller
        try {
            $controller = new App\Http\Controllers\StockNotificationControllerSimple();
            return $controller->subscribe($request);
        } catch (Exception $e) {
            \Log::error('Even simple controller failed', ['error' => $e->getMessage()]);
            
            // Basic fallback response
            return response()->json([
                'success' => false,
                'message' => 'Service temporarily unavailable. Error: ' . $e->getMessage(),
                'debug_info' => [
                    'model_exists' => class_exists('App\\Models\\ProductStockNotification'),
                    'table_exists' => \Schema::hasTable('product_stock_notifications'),
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    })->name('stock-notification.subscribe-emergency');
});

// Add emergency diagnostic routes
Route::get('/debug-stock-notifications', function() {
    $info = [
        'model_exists' => class_exists('App\\Models\\ProductStockNotification'),
        'service_exists' => class_exists('App\\Services\\EnhancedStockNotificationService'),
        'controller_exists' => class_exists('App\\Http\\Controllers\\StockNotificationController'),
        'simple_controller_exists' => class_exists('App\\Http\\Controllers\\StockNotificationControllerSimple'),
        'table_exists' => false,
        'sample_product_exists' => false,
        'routes_registered' => [],
        'errors' => []
    ];
    
    try {
        $info['table_exists'] = \Schema::hasTable('product_stock_notifications');
    } catch (Exception $e) {
        $info['errors']['table_check'] = $e->getMessage();
    }
    
    try {
        $info['sample_product_exists'] = \App\Models\Product::count() > 0;
        $info['product_count'] = \App\Models\Product::count();
    } catch (Exception $e) {
        $info['errors']['product_check'] = $e->getMessage();
    }
    
    // Check specific routes
    $routes = ['stock-notifications.subscribe', 'stock-notification.subscribe-simple', 'stock-notification.test-simple'];
    foreach ($routes as $routeName) {
        try {
            $info['routes_registered'][$routeName] = !is_null(\Illuminate\Support\Facades\Route::getRoutes()->getByName($routeName));
        } catch (Exception $e) {
            $info['routes_registered'][$routeName] = false;
        }
    }
    
    return response()->json($info, 200, [], JSON_PRETTY_PRINT);
});
