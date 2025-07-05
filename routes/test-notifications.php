<?php

use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\Notification;
use App\Events\OrderPlaced;

// Add a test route to manually trigger order notification
Route::get('/test-order-notification', function() {
    // Get the latest order
    $order = Order::latest()->first();
    
    if (!$order) {
        return response()->json([
            'error' => 'No orders found in database'
        ]);
    }
    
    // Check if notification already exists for this order
    $existingNotification = Notification::where('type', 'order_placed')
        ->whereJsonContains('data->order_id', $order->id)
        ->first();
    
    if ($existingNotification) {
        return response()->json([
            'message' => 'Notification already exists for this order',
            'notification' => $existingNotification,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'total' => $order->total
            ]
        ]);
    }
    
    // Manually create notification
    $notification = Notification::createForAdmin(
        'order_placed',
        'New Order Received',
        "Order #{$order->order_number} placed by {$order->customer_name} for ₹{$order->total}",
        [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => $order->total,
            'status' => $order->status
        ]
    );
    
    // Also fire the event to test the complete flow
    event(new OrderPlaced($order));
    
    return response()->json([
        'success' => true,
        'message' => 'Notification created successfully',
        'notification' => $notification,
        'order' => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => $order->total
        ]
    ]);
});

// Check notification status
Route::get('/check-notifications', function() {
    $unreadCount = Notification::forAdmin()->unread()->count();
    $latestNotifications = Notification::forAdmin()->latest()->take(5)->get();
    
    return response()->json([
        'unread_count' => $unreadCount,
        'latest_notifications' => $latestNotifications,
        'order_notifications' => Notification::where('type', 'order_placed')->latest()->take(5)->get()
    ]);
});

// Debug: Check if events are firing
Route::get('/debug-events', function() {
    return response()->json([
        'event_listeners' => app('events')->getListeners(\App\Events\OrderPlaced::class),
        'registered_events' => array_keys(app('events')->getListeners()),
        'event_service_provider' => [
            'listen' => (new \App\Providers\EventServiceProvider(app()))->listens()
        ]
    ]);
});
