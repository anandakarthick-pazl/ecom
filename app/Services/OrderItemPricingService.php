<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Offer;

class OrderItemPricingService
{
    /**
     * Create an order item with proper pricing details including MRP and offers
     */
    public static function createOrderItemWithPricing($orderData, $cartItem)
    {
        $product = $cartItem->product;
        
        // Get the current pricing details
        $pricingDetails = self::calculateItemPricing($product, $cartItem->quantity, $cartItem->price);
        
        // Calculate tax
        $itemTaxAmount = $product->getTaxAmount($cartItem->price) * $cartItem->quantity;
        
        return OrderItem::create([
            'order_id' => $orderData['order_id'],
            'product_id' => $cartItem->product_id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            
            // Pricing fields
            'price' => $cartItem->price, // This is the effective/discounted price
            'mrp_price' => $pricingDetails['mrp_price'],
            'discount_amount' => $pricingDetails['discount_amount'],
            'discount_percentage' => $pricingDetails['discount_percentage'],
            'offer_id' => $pricingDetails['offer_id'],
            'offer_name' => $pricingDetails['offer_name'],
            
            'quantity' => $cartItem->quantity,
            'tax_percentage' => $product->tax_percentage,
            'tax_amount' => $itemTaxAmount,
            'total' => $cartItem->total + $itemTaxAmount
        ]);
    }
    
    /**
     * Calculate comprehensive pricing details for a product
     */
    public static function calculateItemPricing(Product $product, $quantity = 1, $effectivePrice = null)
    {
        // Use the provided effective price or get it from product
        $effectivePrice = $effectivePrice ?: $product->getEffectiveFinalPrice();
        
        // Determine MRP (Maximum Retail Price)
        $mrpPrice = $product->price; // The original product price is the MRP
        
        // Calculate discount details
        $discountAmount = 0;
        $discountPercentage = 0;
        $offerId = null;
        $offerName = null;
        
        // Check if there's a manual discount first
        if ($product->discount_price && $product->discount_price > 0 && $product->discount_price < $product->price) {
            $discountAmount = $product->price - $product->discount_price;
            $discountPercentage = round(($discountAmount / $product->price) * 100, 2);
            $offerName = 'Manual Discount';
        }
        // Check for offer-based discounts
        else {
            $bestOffer = $product->getBestOffer();
            if ($bestOffer) {
                // Check if this is a virtual offer (product onboarding discount)
                if (isset($bestOffer->is_virtual) && $bestOffer->is_virtual) {
                    // For virtual offers, use the pre-calculated discount amount
                    $offerDiscount = $bestOffer->discount_amount;
                } else {
                    // For regular offers, call the calculateDiscount method
                    $offerDiscount = $bestOffer->calculateDiscount($product->price, $product, $product->category);
                }
                
                if ($offerDiscount > 0) {
                    $discountAmount = $offerDiscount;
                    $discountPercentage = round(($offerDiscount / $product->price) * 100, 2);
                    $offerId = property_exists($bestOffer, 'id') ? $bestOffer->id : null;
                    $offerName = property_exists($bestOffer, 'name') ? $bestOffer->name : 'Discount';
                }
            }
        }
        
        // If there's still a difference between MRP and effective price not accounted for
        if ($mrpPrice > $effectivePrice && $discountAmount == 0) {
            $discountAmount = $mrpPrice - $effectivePrice;
            $discountPercentage = round(($discountAmount / $mrpPrice) * 100, 2);
            $offerName = $offerName ?: 'Price Discount';
        }
        
        return [
            'mrp_price' => $mrpPrice,
            'effective_price' => $effectivePrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'offer_id' => $offerId,
            'offer_name' => $offerName,
            'total_mrp' => $mrpPrice * $quantity,
            'total_savings' => $discountAmount * $quantity
        ];
    }
    
    /**
     * Update existing order items to include pricing details (for migration)
     */
    public static function updateExistingOrderItemsWithPricing()
    {
        $orderItems = OrderItem::whereNull('mrp_price')
                              ->orWhere('mrp_price', 0)
                              ->with('product')
                              ->get();
        
        $updated = 0;
        $failed = 0;
        
        foreach ($orderItems as $item) {
            try {
                if (!$item->product) {
                    $failed++;
                    continue;
                }
                
                $pricingDetails = self::calculateItemPricing($item->product, $item->quantity, $item->price);
                
                $item->update([
                    'mrp_price' => $pricingDetails['mrp_price'],
                    'discount_amount' => $pricingDetails['discount_amount'],
                    'discount_percentage' => $pricingDetails['discount_percentage'],
                    'offer_name' => $pricingDetails['offer_name']
                ]);
                
                $updated++;
                
            } catch (\Exception $e) {
                \Log::error('Failed to update order item pricing', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }
        
        return [
            'updated' => $updated,
            'failed' => $failed,
            'total' => $orderItems->count()
        ];
    }
    
    /**
     * Get pricing summary for an order
     */
    public static function getOrderPricingSummary($order)
    {
        $orderItems = $order->items;
        
        $summary = [
            'total_mrp' => 0,
            'total_effective' => 0,
            'total_savings' => 0,
            'total_discount_percentage' => 0,
            'items_with_offers' => 0,
            'offer_names' => []
        ];
        
        foreach ($orderItems as $item) {
            $itemMrp = ($item->mrp_price > 0 ? $item->mrp_price : $item->price) * $item->quantity;
            $itemEffective = $item->price * $item->quantity;
            $itemSavings = $itemMrp - $itemEffective;
            
            $summary['total_mrp'] += $itemMrp;
            $summary['total_effective'] += $itemEffective;
            $summary['total_savings'] += $itemSavings;
            
            if ($item->offer_name) {
                $summary['items_with_offers']++;
                if (!in_array($item->offer_name, $summary['offer_names'])) {
                    $summary['offer_names'][] = $item->offer_name;
                }
            }
        }
        
        if ($summary['total_mrp'] > 0) {
            $summary['total_discount_percentage'] = round(($summary['total_savings'] / $summary['total_mrp']) * 100, 2);
        }
        
        return $summary;
    }
}
