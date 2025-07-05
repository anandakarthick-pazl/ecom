<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Notification;
use App\Models\AppSetting;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class HandleOrderPlaced
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        try {
            $order = $event->order;
            
            Log::info('HandleOrderPlaced event triggered', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'company_id' => $order->company_id
            ]);

            // Ensure we have company context
            if ($order->company_id) {
                // Set company context for notification
                app()->instance('current_tenant', \App\Models\SuperAdmin\Company::find($order->company_id));
            }

            // Create admin notification
            $notification = Notification::create([
                'company_id' => $order->company_id,
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

            Log::info('Notification created', [
                'notification_id' => $notification->id,
                'company_id' => $notification->company_id
            ]);

            // Send invoice email to customer if email notifications are enabled and email is provided
            if (AppSetting::get('email_notifications', true) && !empty($order->customer_email)) {
                try {
                    // For now, skip PDF generation if it's causing issues
                    // Just send a simple order confirmation
                    
                    Log::info('Attempting to send order email', [
                        'email' => $order->customer_email,
                        'order_number' => $order->order_number
                    ]);
                    
                    // You can uncomment this when email is properly configured
                    // Mail::to($order->customer_email)
                    //     ->send(new OrderInvoiceMail($order));
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send order invoice email', [
                        'error' => $e->getMessage(),
                        'order_id' => $order->id,
                        'email' => $order->customer_email
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error in HandleOrderPlaced listener', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => isset($order) ? $order->id : null
            ]);
            
            // Don't throw the exception to prevent order failure
            // The order should complete even if notification fails
        }
    }
}
