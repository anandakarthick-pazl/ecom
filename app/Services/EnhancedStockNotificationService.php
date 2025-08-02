<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockNotification;
use App\Models\SuperAdmin\WhatsAppConfig;
use App\Mail\BackInStockNotification;
use App\Services\TwilioWhatsAppService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EnhancedStockNotificationService
{
    protected $whatsAppService;

    public function __construct(TwilioWhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

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
            $product = Product::with('company')->findOrFail($productId);
            
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

            // Initialize WhatsApp service with company config
            $whatsAppConfig = WhatsAppConfig::where('company_id', $product->company_id)->first();
            $this->whatsAppService = new TwilioWhatsAppService($whatsAppConfig);

            foreach ($notifications as $notification) {
                $result = $this->sendNotification($notification);
                
                if ($result['success']) {
                    if (in_array('email', $result['channels'])) $emailCount++;
                    if (in_array('whatsapp', $result['channels'])) $whatsappCount++;
                    
                    $notification->markAsNotified();
                } else {
                    $errorCount++;
                }

                // Add small delay to prevent rate limiting
                usleep(500000); // 0.5 second delay
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
     * Send email notification using the new Mailable class
     */
    private function sendEmailNotification($notification)
    {
        if (!$notification->customer_email) {
            throw new Exception('No email address provided');
        }

        $product = $notification->product;
        
        Mail::to($notification->customer_email, $notification->customer_name)
            ->send(new BackInStockNotification($product, $notification));
    }

    /**
     * Send WhatsApp notification using TwilioWhatsAppService
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
        
        // Use the enhanced TwilioWhatsAppService to send message
        $result = $this->sendDirectWhatsAppMessage($mobile, $message);
        
        if (!$result['success']) {
            throw new Exception($result['error'] ?? 'Failed to send WhatsApp message');
        }

        return $result;
    }

    /**
     * Send WhatsApp message directly using Twilio API
     */
    private function sendDirectWhatsAppMessage($mobile, $message)
    {
        try {
            // Remove whatsapp: prefix if present and format correctly
            $cleanMobile = str_replace('whatsapp:', '', $mobile);
            
            // Use the existing TwilioWhatsAppService method structure
            // but create a simple message sending method
            return $this->whatsAppService->sendSimpleMessage($cleanMobile, $message);
            
        } catch (Exception $e) {
            Log::error('Direct WhatsApp sending failed', [
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'WhatsApp sending failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format WhatsApp message for back-in-stock notification
     */
    private function formatWhatsAppMessage($product, $notification)
    {
        $customerName = $notification->customer_name ?? 'Customer';
        $productUrl = route('product', $product->slug);
        $companyName = $product->company->name ?? config('app.name', 'Our Store');
        
        return "🎉 *Great News, {$customerName}!*\n\n" .
               "📦 *\"{$product->name}\"* is now back in stock!\n\n" .
               "💰 Price: ₹" . number_format($product->final_price, 2) . "\n" .
               "📋 Stock: {$product->stock} available\n" .
               ($product->discount_percentage > 0 ? "🔥 *{$product->discount_percentage}% OFF* - Limited time!\n" : "") .
               "\n🛒 *Order now before it's gone again!*\n" .
               "👆 Click here to order: {$productUrl}\n\n" .
               "📞 Need help? Contact us:\n" .
               "🌐 " . config('app.url') . "\n" .
               "📧 " . config('mail.from.address') . "\n\n" .
               "Thank you for choosing {$companyName}! 🙏";
    }

    /**
     * Automatically check for restocked products and send notifications
     */
    public function checkAndNotifyRestockedProducts()
    {
        $results = [];
        
        try {
            // Get all products that have pending notifications
            $productsWithNotifications = ProductStockNotification::select('product_id')
                ->where('is_notified', false)
                ->groupBy('product_id')
                ->get()
                ->pluck('product_id');

            if ($productsWithNotifications->isEmpty()) {
                Log::info('No products with pending notifications found');
                return [
                    'success' => true,
                    'message' => 'No products with pending notifications',
                    'processed' => 0
                ];
            }

            // Check each product for stock availability
            $restockedProducts = Product::whereIn('id', $productsWithNotifications)
                ->where('stock', '>', 0)
                ->where('is_active', true)
                ->get();

            Log::info('Checking restocked products', [
                'total_products_with_notifications' => $productsWithNotifications->count(),
                'restocked_products_found' => $restockedProducts->count()
            ]);

            foreach ($restockedProducts as $product) {
                $result = $this->notifyCustomers($product->id);
                $results[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'result' => $result
                ];

                // Add delay between products to prevent rate limiting
                sleep(2);
            }

            return [
                'success' => true,
                'message' => 'Automatic stock notification check completed',
                'processed' => count($results),
                'results' => $results
            ];

        } catch (Exception $e) {
            Log::error('Failed to check and notify restocked products', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to check restocked products: ' . $e->getMessage(),
                'processed' => count($results),
                'results' => $results
            ];
        }
    }

    /**
     * Trigger notifications when product stock is updated
     */
    public function triggerNotificationsOnStockUpdate($productId, $oldStock, $newStock)
    {
        try {
            // Only trigger if product went from out of stock to in stock
            if ($oldStock > 0 || $newStock <= 0) {
                return [
                    'success' => false,
                    'message' => 'No notification trigger needed',
                    'reason' => 'Product was not restocked (old: ' . $oldStock . ', new: ' . $newStock . ')'
                ];
            }

            Log::info('Product restocked, triggering notifications', [
                'product_id' => $productId,
                'old_stock' => $oldStock,
                'new_stock' => $newStock
            ]);

            return $this->notifyCustomers($productId);

        } catch (Exception $e) {
            Log::error('Failed to trigger notifications on stock update', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to trigger notifications: ' . $e->getMessage()
            ];
        }
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
            'recent' => $query->clone()->where('created_at', '>=', now()->subDays(7))->count(),
            'today_sent' => $query->clone()->where('notified_at', '>=', now()->startOfDay())->count()
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

    /**
     * Get pending notifications summary for admin dashboard
     */
    public function getPendingNotificationsSummary()
    {
        $pendingByProduct = ProductStockNotification::select('product_id')
            ->selectRaw('COUNT(*) as notification_count')
            ->selectRaw('COUNT(CASE WHEN customer_email IS NOT NULL THEN 1 END) as email_count')
            ->selectRaw('COUNT(CASE WHEN customer_mobile IS NOT NULL THEN 1 END) as whatsapp_count')
            ->where('is_notified', false)
            ->with(['product:id,name,stock,final_price'])
            ->groupBy('product_id')
            ->orderBy('notification_count', 'desc')
            ->get();

        return [
            'total_products' => $pendingByProduct->count(),
            'total_notifications' => $pendingByProduct->sum('notification_count'),
            'total_email_subscribers' => $pendingByProduct->sum('email_count'),
            'total_whatsapp_subscribers' => $pendingByProduct->sum('whatsapp_count'),
            'products' => $pendingByProduct->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Unknown Product',
                    'current_stock' => $item->product->stock ?? 0,
                    'price' => $item->product->final_price ?? 0,
                    'is_in_stock' => ($item->product->stock ?? 0) > 0,
                    'notification_count' => $item->notification_count,
                    'email_subscribers' => $item->email_count,
                    'whatsapp_subscribers' => $item->whatsapp_count
                ];
            })
        ];
    }
}
