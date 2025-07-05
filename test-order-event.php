<?php
// Test script to verify OrderPlaced event functionality
// Run: php test-order-event.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Events\OrderPlaced;
use App\Models\Notification;

echo "\n================================================\n";
echo "Testing OrderPlaced Event\n";
echo "================================================\n\n";

// Get the latest order
$order = Order::with('items')->latest()->first();

if (!$order) {
    echo "❌ No orders found in database!\n";
    echo "Please place an order first through the website.\n";
    exit;
}

echo "✓ Found order: #{$order->order_number}\n";
echo "  Customer: {$order->customer_name}\n";
echo "  Total: ₹{$order->total}\n";
echo "  Company ID: " . ($order->company_id ?: 'NOT SET') . "\n\n";

// Check notifications before
$notificationsBefore = Notification::where('type', 'order_placed')->count();
echo "Notifications before test: $notificationsBefore\n\n";

// Test firing the event
echo "Testing event dispatch...\n";
try {
    // Set company context if needed
    if ($order->company_id) {
        $company = \App\Models\SuperAdmin\Company::find($order->company_id);
        if ($company) {
            app()->instance('current_tenant', $company);
            echo "✓ Company context set: {$company->name}\n";
        }
    }
    
    // Fire the event
    event(new OrderPlaced($order));
    echo "✓ Event dispatched successfully\n\n";
    
    // Wait a moment for processing
    sleep(1);
    
    // Check notifications after
    $notificationsAfter = Notification::where('type', 'order_placed')->count();
    echo "Notifications after test: $notificationsAfter\n";
    
    if ($notificationsAfter > $notificationsBefore) {
        echo "✓ New notification created!\n";
        
        $latestNotification = Notification::where('type', 'order_placed')
                                         ->latest()
                                         ->first();
        echo "  Title: {$latestNotification->title}\n";
        echo "  Message: {$latestNotification->message}\n";
        echo "  Company ID: {$latestNotification->company_id}\n";
    } else {
        echo "❌ No new notification created\n";
        echo "Check storage/logs/laravel.log for errors\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n================================================\n";
echo "Test complete! Check storage/logs/laravel.log for details.\n";
echo "================================================\n\n";
