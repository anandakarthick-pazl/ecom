<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Session;

class CouponService
{
    /**
     * Validate and apply coupon code
     *
     * @param string $couponCode
     * @param string $sessionId
     * @return array
     */
    public static function validateAndApplyCoupon(string $couponCode, string $sessionId): array
    {
        try {
            // Find the offer by code
            $offer = Offer::where('code', $couponCode)
                         ->active()
                         ->current()
                         ->first();

            if (!$offer) {
                return [
                    'success' => false,
                    'message' => 'Invalid coupon code or coupon has expired.',
                    'discount' => 0
                ];
            }

            // Check if coupon is valid
            if (!$offer->isValid()) {
                return [
                    'success' => false,
                    'message' => 'This coupon is no longer valid or has reached its usage limit.',
                    'discount' => 0
                ];
            }

            // Get cart items and calculate subtotal
            $cartItems = Cart::getCartItems($sessionId);
            $subtotal = Cart::getCartTotal($sessionId);

            if ($cartItems->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Your cart is empty. Add items to apply coupon.',
                    'discount' => 0
                ];
            }

            // Check minimum amount requirement
            if ($offer->minimum_amount && $subtotal < $offer->minimum_amount) {
                return [
                    'success' => false,
                    'message' => "Minimum order amount of ₹" . number_format($offer->minimum_amount, 2) . " required for this coupon.",
                    'discount' => 0
                ];
            }

            // Calculate discount based on offer type
            $discountAmount = self::calculateDiscountAmount($offer, $cartItems, $subtotal);

            if ($discountAmount <= 0) {
                return [
                    'success' => false,
                    'message' => 'This coupon is not applicable to items in your cart.',
                    'discount' => 0
                ];
            }

            // Store coupon in session
            Session::put('applied_coupon', [
                'code' => $couponCode,
                'offer_id' => $offer->id,
                'discount_amount' => $discountAmount,
                'offer_name' => $offer->name,
                'offer_type' => $offer->type,
                'discount_type' => $offer->discount_type,
                'value' => $offer->value
            ]);

            return [
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'discount' => $discountAmount,
                'coupon_info' => [
                    'code' => $couponCode,
                    'name' => $offer->name,
                    'discount_display' => $offer->getDiscountValueDisplayAttribute(),
                    'type' => $offer->getDiscountTypeDisplayAttribute()
                ]
            ];

        } catch (\Exception $e) {
            \Log::error('Coupon validation error: ' . $e->getMessage(), [
                'coupon_code' => $couponCode,
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Unable to apply coupon. Please try again.',
                'discount' => 0
            ];
        }
    }

    /**
     * Calculate discount amount based on offer type
     *
     * @param Offer $offer
     * @param \Illuminate\Support\Collection $cartItems
     * @param float $subtotal
     * @return float
     */
    private static function calculateDiscountAmount(Offer $offer, $cartItems, float $subtotal): float
    {
        $discountAmount = 0;

        switch ($offer->type) {
            case 'general':
            case 'percentage':
                // General percentage discount on entire cart
                if ($offer->discount_type === 'percentage' || $offer->type === 'percentage') {
                    $discountAmount = $subtotal * ($offer->value / 100);
                } else {
                    // Fixed amount discount
                    $discountAmount = min($offer->value, $subtotal);
                }
                break;

            case 'fixed':
                // Fixed amount discount
                $discountAmount = min($offer->value, $subtotal);
                break;

            case 'category':
                // Category-specific discount
                $categoryDiscountAmount = 0;
                foreach ($cartItems as $item) {
                    if ($item->product && $item->product->category_id == $offer->category_id) {
                        $itemTotal = $item->price * $item->quantity;
                        if ($offer->discount_type === 'percentage') {
                            $categoryDiscountAmount += $itemTotal * ($offer->value / 100);
                        } else {
                            $categoryDiscountAmount += min($offer->value, $itemTotal);
                        }
                    }
                }
                $discountAmount = $categoryDiscountAmount;
                break;

            case 'product':
                // Product-specific discount
                $productDiscountAmount = 0;
                foreach ($cartItems as $item) {
                    if ($item->product && $item->product_id == $offer->product_id) {
                        $itemTotal = $item->price * $item->quantity;
                        if ($offer->discount_type === 'percentage') {
                            $productDiscountAmount += $itemTotal * ($offer->value / 100);
                        } else {
                            $productDiscountAmount += min($offer->value * $item->quantity, $itemTotal);
                        }
                    }
                }
                $discountAmount = $productDiscountAmount;
                break;
        }

        // Ensure discount doesn't exceed cart total
        return min($discountAmount, $subtotal);
    }

    /**
     * Remove applied coupon from session
     *
     * @return array
     */
    public static function removeCoupon(): array
    {
        Session::forget('applied_coupon');

        return [
            'success' => true,
            'message' => 'Coupon removed successfully.',
            'discount' => 0
        ];
    }

    /**
     * Get current applied coupon info
     *
     * @return array|null
     */
    public static function getAppliedCoupon(): ?array
    {
        return Session::get('applied_coupon');
    }

    /**
     * Check if coupon is applied
     *
     * @return bool
     */
    public static function hasCouponApplied(): bool
    {
        return Session::has('applied_coupon');
    }

    /**
     * Get current discount amount
     *
     * @return float
     */
    public static function getCurrentDiscount(): float
    {
        $coupon = self::getAppliedCoupon();
        return $coupon ? (float) $coupon['discount_amount'] : 0;
    }

    /**
     * Validate existing coupon on cart changes
     *
     * @param string $sessionId
     * @return array
     */
    public static function revalidateExistingCoupon(string $sessionId): array
    {
        $appliedCoupon = self::getAppliedCoupon();
        
        if (!$appliedCoupon) {
            return [
                'success' => true,
                'discount' => 0
            ];
        }

        // Re-validate the coupon with current cart
        $result = self::validateAndApplyCoupon($appliedCoupon['code'], $sessionId);
        
        if (!$result['success']) {
            // Remove invalid coupon
            self::removeCoupon();
        }

        return $result;
    }

    /**
     * Get all active coupons for display
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveCoupons()
    {
        return Offer::where('code', '!=', null)
                   ->where('code', '!=', '')
                   ->active()
                   ->current()
                   ->orderBy('value', 'desc')
                   ->get();
    }

    /**
     * Increment coupon usage count when order is placed
     *
     * @param string $couponCode
     * @return bool
     */
    public static function incrementUsageCount(string $couponCode): bool
    {
        try {
            $offer = Offer::where('code', $couponCode)->first();
            
            if ($offer) {
                $offer->increment('used_count');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to increment coupon usage: ' . $e->getMessage());
            return false;
        }
    }
}
