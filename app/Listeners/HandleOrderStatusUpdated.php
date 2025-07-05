<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdate;
use App\Models\Notification;
use App\Models\AppSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class HandleOrderStatusUpdated implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderStatusUpdated $event)
    {
        $order = $event->order;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        // Create admin notification
        Notification::createForAdmin(
            'order_updated',
            'Order Status Updated',
            "Order #{$order->order_number} status changed from {$oldStatus} to {$newStatus}",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]
        );

        // Send email notification if enabled
        if (AppSetting::get('email_notifications', true)) {
            if ($order->customer && $order->customer->email) {
                Mail::to($order->customer->email)->send(new OrderStatusUpdate($order, $oldStatus, $newStatus));
            }
        }
    }
}
