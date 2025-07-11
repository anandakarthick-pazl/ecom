<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Notification;
use App\Models\AppSetting;
use App\Mail\OrderInvoiceMail;
use App\Jobs\SendOrderInvoiceEmail;
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
            $customerEmail = $order->customer_email ?? $order->customer->email ?? null;
            
            if (AppSetting::get('email_notifications', true) && !empty($customerEmail)) {
                
                // Check if we should use queue or send immediately
                $useQueue = AppSetting::get('use_email_queue', true); // Default to using queue
                
                if ($useQueue) {
                    try {
                        Log::info('Dispatching order invoice email to queue', [
                            'email' => $customerEmail,
                            'order_number' => $order->order_number,
                            'order_id' => $order->id
                        ]);
                        
                        // Dispatch to queue for better performance
                        SendOrderInvoiceEmail::dispatch($order, $customerEmail, null, true)
                            ->delay(now()->addSeconds(10)); // Small delay to ensure order is fully committed
                        
                        Log::info('Order invoice email job dispatched to queue successfully');
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to dispatch email job to queue, trying immediate send', [
                            'error' => $e->getMessage(),
                            'order_id' => $order->id,
                            'email' => $customerEmail
                        ]);
                        
                        // Fallback to immediate sending if queue fails
                        $useQueue = false;
                    }
                }
                
                // Send immediately if not using queue or queue failed
                if (!$useQueue) {
                    try {
                        Log::info('Attempting to send order invoice email immediately with PDF', [
                            'email' => $customerEmail,
                            'order_number' => $order->order_number,
                            'order_id' => $order->id
                        ]);
                        
                        // Send email with PDF attachment - using enhanced error handling
                        Mail::to($customerEmail)
                            ->send(new OrderInvoiceMail($order, null, true)); // Auto-generate PDF
                        
                        Log::info('Order invoice email sent successfully', [
                            'email' => $customerEmail,
                            'order_number' => $order->order_number
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to send order invoice email immediately', [
                            'error' => $e->getMessage(),
                            'order_id' => $order->id,
                            'email' => $customerEmail,
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        // Try sending without PDF as fallback
                        try {
                            Log::info('Attempting fallback email without PDF');
                            Mail::to($customerEmail)
                                ->send(new OrderInvoiceMail($order, null, false)); // No PDF generation
                            
                            Log::info('Fallback email sent successfully (without PDF)');
                        } catch (\Exception $fallbackError) {
                            Log::error('Fallback email also failed', [
                                'error' => $fallbackError->getMessage(),
                                'order_id' => $order->id
                            ]);
                        }
                    }
                }
                
            } else {
                Log::info('Email not sent - notifications disabled or no customer email', [
                    'email_notifications_enabled' => AppSetting::get('email_notifications', true),
                    'customer_email' => $customerEmail,
                    'order_id' => $order->id
                ]);
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
