<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Cart;
use App\Models\Product;

class OrderValidationService
{
    /**
     * Validate complete order for online checkout
     */
    public static function validateOnlineOrder($sessionId, $skipMinAmount = false)
    {
        $errors = [];
        $warnings = [];
        
        // 1. Validate cart is not empty
        $cartItems = Cart::getCartItems($sessionId);
        if ($cartItems->isEmpty()) {
            $errors[] = 'Your cart is empty!';
            return [
                'valid' => false,
                'errors' => $errors,
                'warnings' => $warnings,
                'data' => null
            ];
        }
        
        // 2. Validate stock availability
        foreach ($cartItems as $item) {
            if (!$item->product) {
                $errors[] = "Product '{$item->product_name}' is no longer available!";
                continue;
            }
            
            if (!$item->product->isInStock($item->quantity)) {
                $errors[] = "Product '{$item->product->name}' is out of stock! Available: {$item->product->stock}";
            }
        }
        
        // 3. Calculate totals
        $subtotal = Cart::getCartTotal($sessionId);
        
        // 4. Validate minimum order amount (if not skipped)
        if (!$skipMinAmount) {
            $minOrderValidation = DeliveryService::validateMinimumOrderAmount($subtotal);
            if (!$minOrderValidation['valid']) {
                $errors[] = $minOrderValidation['message'];
            }
        }
        
        // 5. Additional validations
        $deliveryInfo = DeliveryService::getDeliveryInfo($subtotal);
        
        // Check if any items have price changes
        foreach ($cartItems as $item) {
            if ($item->product && $item->price != $item->product->price) {
                $warnings[] = "Price of '{$item->product->name}' has changed from ₹{$item->price} to ₹{$item->product->price}";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => [
                'cart_items' => $cartItems,
                'subtotal' => $subtotal,
                'delivery_info' => $deliveryInfo,
                'min_order_validation' => $minOrderValidation ?? ['valid' => true]
            ]
        ];
    }
    
    /**
     * Validate order amount based on settings
     */
    public static function validateOrderAmount($amount, $orderType = 'online')
    {
        // Only validate online orders
        if ($orderType !== 'online') {
            return ['valid' => true, 'message' => ''];
        }
        
        return DeliveryService::validateMinimumOrderAmount($amount);
    }
    
    /**
     * Get validation settings for display
     */
    public static function getValidationSettings()
    {
        return [
            'min_order' => DeliveryService::getMinOrderValidationSettings(),
            'delivery' => DeliveryService::getDeliverySettings()
        ];
    }
    
    /**
     * Check if order meets all requirements
     */
    public static function checkOrderRequirements($orderData)
    {
        $requirements = [
            'min_amount' => false,
            'stock_available' => false,
            'delivery_possible' => false
        ];
        
        $issues = [];
        
        // Check minimum amount
        $minOrderValidation = self::validateOrderAmount($orderData['subtotal'], 'online');
        $requirements['min_amount'] = $minOrderValidation['valid'];
        if (!$minOrderValidation['valid']) {
            $issues[] = $minOrderValidation['message'];
        }
        
        // Check stock availability
        $stockIssues = self::checkStockAvailability($orderData['items']);
        $requirements['stock_available'] = empty($stockIssues);
        $issues = array_merge($issues, $stockIssues);
        
        // Check delivery possibility
        $deliveryInfo = DeliveryService::getDeliveryInfo($orderData['subtotal']);
        $requirements['delivery_possible'] = $deliveryInfo['enabled'];
        if (!$deliveryInfo['enabled']) {
            $issues[] = 'Delivery service is currently unavailable';
        }
        
        return [
            'all_met' => !in_array(false, $requirements),
            'requirements' => $requirements,
            'issues' => $issues
        ];
    }
    
    /**
     * Check stock availability for items
     */
    private static function checkStockAvailability($items)
    {
        $issues = [];
        
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                $issues[] = "Product '{$item['product_name']}' is no longer available";
                continue;
            }
            
            if (!$product->isInStock($item['quantity'])) {
                $issues[] = "Insufficient stock for '{$product->name}'. Available: {$product->stock}, Requested: {$item['quantity']}";
            }
        }
        
        return $issues;
    }
    
    /**
     * Get user-friendly validation messages
     */
    public static function getValidationMessages()
    {
        $settings = DeliveryService::getMinOrderValidationSettings();
        
        return [
            'min_order_enabled' => $settings['min_order_validation_enabled'],
            'min_order_amount' => $settings['min_order_amount'],
            'min_order_message' => $settings['min_order_message'],
            'stock_error' => 'Some items in your cart are out of stock',
            'cart_empty' => 'Your cart is empty. Please add items before checkout.',
            'delivery_unavailable' => 'Delivery service is currently unavailable'
        ];
    }
}
