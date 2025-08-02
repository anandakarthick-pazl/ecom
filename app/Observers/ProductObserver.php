<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\EnhancedStockNotificationService;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    protected $notificationService;

    public function __construct(EnhancedStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product)
    {
        // Check if stock was updated and product is now in stock
        if ($product->isDirty('stock')) {
            $originalStock = $product->getOriginal('stock');
            $newStock = $product->stock;
            
            Log::info('Product stock updated', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'original_stock' => $originalStock,
                'new_stock' => $newStock
            ]);
            
            // If product was out of stock and now has stock, send notifications
            if ($originalStock <= 0 && $newStock > 0) {
                Log::info('Product back in stock, sending notifications', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'new_stock' => $newStock
                ]);
                
                // Send notifications asynchronously
                $this->sendStockNotifications($product->id);
            }
        }
    }

    /**
     * Send stock notifications for a product
     */
    private function sendStockNotifications($productId)
    {
        try {
            // Use dispatch or queue if you have queue workers set up
            // For now, we'll do it synchronously but you can improve this
            
            dispatch(function() use ($productId) {
                $result = $this->notificationService->notifyCustomers($productId);
                
                Log::info('Stock notification dispatch completed', [
                    'product_id' => $productId,
                    'result' => $result
                ]);
            });
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch stock notifications', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
