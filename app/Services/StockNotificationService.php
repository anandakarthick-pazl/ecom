<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class StockNotificationService
{
    /**
     * Subscribe a customer for stock notifications
     */
    public function subscribe($productId, $customerData, $sessionId = null)
    {
        try {
            // Validate product exists and is out of stock
            $product = Product::findOrFail($productId);
            
            if ($product->isInStock()) {
                return [
                    'success' => false,
                    'message' => 'This product is currently in stock. You can add it to cart now!',
                    'in_stock' => true
                ];
            }

            // Create notification
            $notification = ProductStockNotification::createNotification(
                $productId, 
                $customerData, 
                $sessionId
            );

            Log::info('Stock notification subscription created', [
                'product_id' => $productId,
                'product_name' => $product->name,
                'customer_email' => $customerData['email'] ?? null,
                'customer_mobile' => $customerData['mobile'] ?? null,
                'notification_id' => $notification->id
            ]);

            return [
                'success' => true,
                'message' => "Great! We'll notify you when \"{$product->name}\" is back in stock.",
                'notification_id' => $notification->id,
                'product_name' => $product->name,
                'channels' => $notification->notification_channels
            ];

        } catch (Exception $e) {
            Log::error('Failed to create stock notification', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'customer_data' => $customerData
            ]);

            return [
                'success' => false,
                'message' => 'Unable to set up notification. Please try again later.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Notify customers when a product is back in stock
     */
    public function notifyCustomers($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            if (!$product->isInStock()) {
                Log::info('Product still out of stock, skipping notifications', [
                    'product_id' => $productId,
                    'stock' => $product->stock
                ]);
                return ['success' => false, 'message' => 'Product is still out of stock'];
            }

            $notifications = ProductStockNotification::getActiveNotificationsForProduct($productId);
            
            if ($notifications->isEmpty()) {
                Log::info('No pending notifications for product', ['product_id' => $productId]);
                return ['success' => true, 'message' => 'No pending notifications', 'count' => 0];
            }

            $emailCount = 0;
            $whatsappCount = 0;
            $errorCount = 0;

            foreach ($notifications as $notification) {
                $result = $this->sendNotification($notification);
                
                if ($result['success']) {
                    if (in_array('email', $result['channels'])) $emailCount++;
                    if (in_array('whatsapp', $result['channels'])) $whatsappCount++;
                    
                    $notification->markAsNotified();
                } else {
                    $errorCount++;
                }
            }

            Log::info('Stock notification batch completed', [
                'product_id' => $productId,
                'product_name' => $product->name,
                'total_notifications' => $notifications->count(),
                'email_sent' => $emailCount,
                'whatsapp_sent' => $whatsappCount,
                'errors' => $errorCount
            ]);

            return [
                'success' => true,
                'message' => 'Notifications sent successfully',
                'stats' => [
                    'total' => $notifications->count(),
                    'email_sent' => $emailCount,
                    'whatsapp_sent' => $whatsappCount,
                    'errors' => $errorCount
                ]
            ];

        } catch (Exception $e) {
            Log::error('Failed to send stock notifications', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send individual notification
     */
    private function sendNotification($notification)
    {
        $channels = $notification->notification_channels;
        $sentChannels = [];
        $errors = [];

        // Send Email Notification
        if (in_array('email', $channels)) {
            try {
                $this->sendEmailNotification($notification);
                $sentChannels[] = 'email';
                Log::info('Email notification sent', [
                    'notification_id' => $notification->id,
                    'email' => $notification->customer_email
                ]);
            } catch (Exception $e) {
                $errors[] = 'Email: ' . $e->getMessage();
                Log::error('Failed to send email notification', [
                    'notification_id' => $notification->id,
                    'email' => $notification->customer_email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Send WhatsApp Notification
        if (in_array('whatsapp', $channels)) {
            try {
                $this->sendWhatsAppNotification($notification);
                $sentChannels[] = 'whatsapp';
                Log::info('WhatsApp notification sent', [
                    'notification_id' => $notification->id,
                    'mobile' => $notification->formatted_mobile
                ]);
            } catch (Exception $e) {
                $errors[] = 'WhatsApp: ' . $e->getMessage();
                Log::error('Failed to send WhatsApp notification', [
                    'notification_id' => $notification->id,
                    'mobile' => $notification->formatted_mobile,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => !empty($sentChannels),
            'channels' => $sentChannels,
            'errors' => $errors
        ];
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($notification)
    {
        if (!$notification->customer_email) {
            throw new Exception('No email address provided');
        }

        $product = $notification->product;
        
        Mail::send('emails.stock-notification', [
            'customer_name' => $notification->customer_name ?? 'Valued Customer',
            'product' => $product,
            'notification' => $notification
        ], function($message) use ($notification, $product) {
            $message->to($notification->customer_email, $notification->customer_name)
                   ->subject("🎉 Great News! \"{$product->name}\" is Back in Stock!");
        });
    }

    /**
     * Send WhatsApp notification
     */
    private function sendWhatsAppNotification($notification)
    {
        if (!$notification->customer_mobile) {
            throw new Exception('No mobile number provided');
        }

        $product = $notification->product;
        $mobile = $notification->formatted_mobile;
        
        // Format WhatsApp message
        $message = $this->formatWhatsAppMessage($product, $notification);
        
        // Here you can integrate with WhatsApp API
        // For now, we'll log it and you can implement your preferred WhatsApp service
        
        Log::info('WhatsApp notification prepared', [
            'mobile' => $mobile,
            'product_name' => $product->name,
            'message' => $message
        ]);

        // Example integrations (uncomment and configure as needed):
        
        // Option 1: Use Twilio WhatsApp API
        // $this->sendTwilioWhatsApp($mobile, $message);
        
        // Option 2: Use WhatsApp Business API
        // $this->sendWhatsAppBusinessAPI($mobile, $message);
        
        // Option 3: Use a third-party service like Gupshup, etc.
        // $this->sendThirdPartyWhatsApp($mobile, $message);
        
        // For demonstration, we'll just log it
        // In production, replace this with actual WhatsApp sending logic
        $this->logWhatsAppMessage($mobile, $message);
    }

    /**
     * Format WhatsApp message
     */
    private function formatWhatsAppMessage($product, $notification)
    {
        $customerName = $notification->customer_name ?? 'Customer';
        $productUrl = route('product', $product->slug);
        
        return "🎉 *Great News, {$customerName}!*\n\n" .
               "📦 *\"{$product->name}\"* is now back in stock!\n\n" .
               "💰 Price: ₹" . number_format($product->final_price, 2) . "\n" .
               "📋 Stock: {$product->stock} available\n\n" .
               "🛒 *Order now before it's gone again!*\n" .
               "👆 Click here to order: {$productUrl}\n\n" .
               "📞 Need help? Contact us:\n" .
               "🌐 " . config('app.url') . "\n" .
               "📧 " . config('mail.from.address') . "\n\n" .
               "Thank you for choosing us! 🙏";
    }

    /**
     * Log WhatsApp message (placeholder for actual sending)
     */
    private function logWhatsAppMessage($mobile, $message)
    {
        // This is a placeholder. Replace with actual WhatsApp API integration
        Log::info('WhatsApp message ready to send', [
            'mobile' => $mobile,
            'message_length' => strlen($message),
            'message_preview' => substr($message, 0, 100) . '...'
        ]);
        
        // You can also store this in a queue for processing
        // Or integrate with your preferred WhatsApp service here
    }

    /**
     * Get notification statistics
     */
    public function getStats($productId = null)
    {
        $query = ProductStockNotification::query();
        
        if ($productId) {
            $query->where('product_id', $productId);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('is_notified', false)->count(),
            'sent' => $query->clone()->where('is_notified', true)->count(),
            'email_subscribers' => $query->clone()->whereNotNull('customer_email')->count(),
            'whatsapp_subscribers' => $query->clone()->whereNotNull('customer_mobile')->count(),
            'recent' => $query->clone()->where('created_at', '>=', now()->subDays(7))->count()
        ];
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        $deleted = ProductStockNotification::where('is_notified', true)
            ->where('notified_at', '<', now()->subDays($days))
            ->delete();

        Log::info('Cleaned up old stock notifications', ['deleted_count' => $deleted]);
        
        return $deleted;
    }
}
