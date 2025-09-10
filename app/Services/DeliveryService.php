<?php

namespace App\Services;

use App\Models\AppSetting;

class DeliveryService
{
    /**
     * Calculate delivery charge based on subtotal and current settings
     */
    public static function calculateDeliveryCharge($subtotal)
    {
        // Check if delivery is enabled
        $deliveryEnabled = AppSetting::get('delivery_enabled', true);
        
        if (!$deliveryEnabled) {
            return 0;
        }
        
        // Get delivery settings
        $deliveryCharge = (float) AppSetting::get('delivery_charge', 50.00);
        $freeDeliveryEnabled = AppSetting::get('free_delivery_enabled', true);
        $freeDeliveryThreshold = (float) AppSetting::get('free_delivery_threshold', 500.00);
        $deliveryMaxAmount = (float) AppSetting::get('delivery_max_amount', null);
        
        // Check if order qualifies for free delivery
        if ($freeDeliveryEnabled && $subtotal >= $freeDeliveryThreshold) {
            return 0;
        }
        
        // Check if order exceeds maximum delivery amount (if set)
        if ($deliveryMaxAmount && $subtotal > $deliveryMaxAmount) {
            return 0; // Free delivery for very large orders
        }
        
        return $deliveryCharge;
    }
    
    /**
     * Get delivery information for display
     */
    public static function getDeliveryInfo($subtotal)
    {
        $deliveryEnabled = AppSetting::get('delivery_enabled', true);
        $deliveryCharge = static::calculateDeliveryCharge($subtotal);
        $freeDeliveryEnabled = AppSetting::get('free_delivery_enabled', true);
        $freeDeliveryThreshold = (float) AppSetting::get('free_delivery_threshold', 500.00);
        $deliveryTimeEstimate = AppSetting::get('delivery_time_estimate', '3-5 business days');
        $deliveryDescription = AppSetting::get('delivery_description', '');
        
        return [
            'enabled' => $deliveryEnabled,
            'charge' => $deliveryCharge,
            'is_free' => $deliveryCharge == 0,
            'free_delivery_enabled' => $freeDeliveryEnabled,
            'free_delivery_threshold' => $freeDeliveryThreshold,
            'time_estimate' => $deliveryTimeEstimate,
            'description' => $deliveryDescription,
            'amount_needed_for_free' => $freeDeliveryEnabled && $subtotal < $freeDeliveryThreshold 
                ? $freeDeliveryThreshold - $subtotal 
                : 0
        ];
    }
    
    /**
     * Get delivery settings for admin
     */
    public static function getDeliverySettings()
    {
        return [
            'delivery_enabled' => AppSetting::get('delivery_enabled', true),
            'delivery_charge' => AppSetting::get('delivery_charge', 50.00),
            'free_delivery_enabled' => AppSetting::get('free_delivery_enabled', true),
            'free_delivery_threshold' => AppSetting::get('free_delivery_threshold', 500.00),
            'delivery_max_amount' => AppSetting::get('delivery_max_amount', null),
            'delivery_time_estimate' => AppSetting::get('delivery_time_estimate', '3-5 business days'),
            'delivery_description' => AppSetting::get('delivery_description', ''),
        ];
    }
    
    /**
     * Validate minimum order amount for online orders
     */
    public static function validateMinimumOrderAmount($orderTotal)
    {
        $validationEnabled = AppSetting::get('min_order_validation_enabled', false);
        
        if (!$validationEnabled) {
            return ['valid' => true, 'message' => ''];
        }
        
        $minOrderAmount = (float) AppSetting::get('min_order_amount', 1000.00);
        $minOrderMessage = AppSetting::get('min_order_message', 'Minimum order amount is ₹'.$minOrderAmount.' for online orders.');
        
        if ($orderTotal < $minOrderAmount) {
            return [
                'valid' => false,
                'message' => $minOrderMessage,
                'min_amount' => $minOrderAmount,
                'current_amount' => $orderTotal,
                'shortfall' => $minOrderAmount - $orderTotal
            ];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    /**
     * Get minimum order validation settings
     */
    public static function getMinOrderValidationSettings()
    {
        return [
            'min_order_validation_enabled' => AppSetting::get('min_order_validation_enabled', false),
            'min_order_amount' => AppSetting::get('min_order_amount', 1000.00),
            'min_order_message' => AppSetting::get('min_order_message', 'Minimum order amount is ₹'.AppSetting::get('min_order_amount', 1000.00).' for online orders.')
        ];
    }
    
    /**
     * Set default delivery settings for new companies
     */
    public static function createDefaultForCompany($companyId)
    {
        $defaults = [
            'delivery_enabled' => true,
            'delivery_charge' => 50.00,
            'free_delivery_enabled' => true,
            'free_delivery_threshold' => 500.00,
            'delivery_time_estimate' => '3-5 business days',
            'delivery_description' => 'Fast and secure delivery to your doorstep',
            // Minimum order validation settings
            'min_order_validation_enabled' => true,
            'min_order_amount' => 1000.00,
            'min_order_message' => 'Minimum order amount is ₹1000 for online orders.'
        ];
        
        foreach ($defaults as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_float($value) ? 'float' : 'string');
            AppSetting::setForTenant($key, $value, $companyId, $type, 'delivery');
        }
    }
    
    /**
     * Validate delivery settings
     */
    public static function validateSettings($data)
    {
        $rules = [
            'delivery_enabled' => 'boolean',
            'delivery_charge' => 'required|numeric|min:0|max:9999.99',
            'free_delivery_enabled' => 'boolean',
            'free_delivery_threshold' => 'nullable|numeric|min:0.01|max:999999.99',
            'delivery_max_amount' => 'nullable|numeric|min:0.01|max:999999.99',
            'delivery_time_estimate' => 'nullable|string|max:255',
            'delivery_description' => 'nullable|string|max:500',
        ];
        
        return $rules;
    }
}
