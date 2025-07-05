<?php
// Quick patch to add direct notification creation in CheckoutController
// This ensures notifications work even if events are not firing

// Add this code block after line 133 (after event(new OrderPlaced($order));) in CheckoutController:

// Direct notification creation as failsafe
try {
    \App\Models\Notification::create([
        'company_id' => $order->company_id ?? app('current_tenant')->id ?? null,
        'type' => 'order_placed',
        'title' => 'New Order Received',
        'message' => "Order #{$order->order_number} placed by {$order->customer_name} for ₹{$order->total}",
        'data' => [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => $order->total,
            'status' => $order->status
        ],
        'user_id' => null,
        'is_read' => false
    ]);
    \Log::info('Direct notification created for order', ['order_id' => $order->id]);
} catch (\Exception $e) {
    \Log::error('Failed to create direct notification', ['error' => $e->getMessage()]);
}
