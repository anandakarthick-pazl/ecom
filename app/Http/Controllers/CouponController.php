<?php

namespace App\Http\Controllers;

use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    private function getSessionId()
    {
        return session()->getId();
    }

    /**
     * Apply coupon code
     */
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $sessionId = $this->getSessionId();

        $result = CouponService::validateAndApplyCoupon($couponCode, $sessionId);

        return response()->json($result);
    }

    /**
     * Remove applied coupon
     */
    public function remove()
    {
        $result = CouponService::removeCoupon();

        return response()->json($result);
    }

    /**
     * Get current coupon info
     */
    public function current()
    {
        $coupon = CouponService::getAppliedCoupon();
        $discount = CouponService::getCurrentDiscount();

        return response()->json([
            'has_coupon' => CouponService::hasCouponApplied(),
            'coupon' => $coupon,
            'discount' => $discount
        ]);
    }

    /**
     * Get available coupons for display
     */
    public function available()
    {
        $coupons = CouponService::getActiveCoupons();

        $formattedCoupons = $coupons->map(function ($offer) {
            return [
                'id' => $offer->id,
                'code' => $offer->code,
                'name' => $offer->name,
                'type' => $offer->type,
                'discount_type' => $offer->discount_type,
                'value' => $offer->value,
                'minimum_amount' => $offer->minimum_amount,
                'discount_display' => $offer->getDiscountValueDisplayAttribute(),
                'type_display' => $offer->getDiscountTypeDisplayAttribute(),
                'description' => $this->getCouponDescription($offer),
                'valid_until' => $offer->end_date->format('M j, Y')
            ];
        });

        return response()->json([
            'success' => true,
            'coupons' => $formattedCoupons
        ]);
    }

    /**
     * Generate coupon description
     */
    private function getCouponDescription($offer)
    {
        $description = '';

        if ($offer->type === 'percentage' || $offer->discount_type === 'percentage') {
            $description = "Get {$offer->value}% off";
        } else {
            $description = "Get ₹{$offer->value} off";
        }

        if ($offer->minimum_amount > 0) {
            $description .= " on orders above ₹" . number_format($offer->minimum_amount, 0);
        }

        if ($offer->type === 'category' && $offer->category) {
            $description .= " on " . $offer->category->name . " products";
        }

        if ($offer->type === 'product' && $offer->product) {
            $description .= " on " . $offer->product->name;
        }

        return $description;
    }
}
