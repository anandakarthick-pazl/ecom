<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getSessionId()
    {
        return session()->getId();
    }

    public function index()
    {
        // Check if we just placed an order (session flash data)
        if (session()->has('order_success')) {
            $orderData = session('order_success');
            \Log::info('Order success data found in session, redirecting', $orderData);
            
            // Redirect to the proper order success page
            return redirect()->route('order.success', $orderData['order_number']);
        }
        
        $cartItems = Cart::getCartItems($this->getSessionId());
        $subtotal = Cart::getCartTotal($this->getSessionId());
        
        // Get applied coupon discount
        $couponDiscount = CouponService::getCurrentDiscount();
        $appliedCoupon = CouponService::getAppliedCoupon();
        
        // Get minimum order validation settings
        $minOrderValidationSettings = \App\Services\DeliveryService::getMinOrderValidationSettings();
        $minOrderValidation = \App\Services\DeliveryService::validateMinimumOrderAmount($subtotal);
        
        // If cart is empty and no order success data, show helpful message
        if ($cartItems->isEmpty()) {
            // Check if there's a recent order for this session
            $recentOrder = \App\Models\Order::where('created_at', '>', now()->subMinutes(10))
                                            ->latest()
                                            ->first();
            
            if ($recentOrder) {
                \Log::info('Found recent order while cart is empty, redirecting to success page', [
                    'order_number' => $recentOrder->order_number
                ]);
                
                return redirect()->route('order.success', $recentOrder->order_number)
                                 ->with('info', 'Your order has been placed successfully!');
            }
        }
        
        // Check if fabric theme is enabled
        $theme = \App\Models\AppSetting::get('store_theme', 'default');
        $host = request()->getHost();
        
        // Use fabric theme if conditions met
       
            return view('cart-fabric', compact(
                'cartItems', 
                'subtotal', 
                'couponDiscount',
                'appliedCoupon',
                'minOrderValidationSettings', 
                'minOrderValidation'
            ));
       
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $quantity = $request->quantity ?? 1;
        $added = Cart::addToCart($this->getSessionId(), $request->product_id, $quantity);

        if (!$added) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock or insufficient quantity available.'
            ]);
        }

        $cartCount = Cart::getCartCount($this->getSessionId());

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $updated = Cart::updateQuantity($this->getSessionId(), $request->product_id, $request->quantity);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update cart. Please check stock availability.'
            ]);
        }

        // Get complete cart data for Order Summary update
        $cartItems = Cart::getCartItems($this->getSessionId());
        $subtotal = Cart::getCartTotal($this->getSessionId());
        $cartCount = Cart::getCartCount($this->getSessionId());
        
        // Revalidate coupon with updated cart
        $couponResult = CouponService::revalidateExistingCoupon($this->getSessionId());
        $couponDiscount = $couponResult['success'] ? CouponService::getCurrentDiscount() : 0;
        
        // Calculate tax amounts with error handling
        $totalTax = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        
        try {
            foreach($cartItems as $item) {
                if ($item->product && method_exists($item->product, 'getTaxAmount')) {
                    $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                    $totalTax += $itemTax;
                    $cgstAmount += ($itemTax / 2);
                    $sgstAmount += ($itemTax / 2);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating tax amounts in cart update: ' . $e->getMessage());
            // Use default values if calculation fails
            $totalTax = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;
        }
        
        // Calculate delivery charge using DeliveryService
        $deliveryCharge = \App\Services\DeliveryService::calculateDeliveryCharge($subtotal);
        $grandTotal = $subtotal + $totalTax + $deliveryCharge - $couponDiscount;
        
        // Get minimum order validation
        $minOrderValidationSettings = \App\Services\DeliveryService::getMinOrderValidationSettings();
        $minOrderValidation = \App\Services\DeliveryService::validateMinimumOrderAmount($grandTotal);
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart_data' => [
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'grand_total' => $grandTotal,
                'cart_count' => $cartCount,
                'min_order_validation' => $minOrderValidation,
                'min_order_settings' => $minOrderValidationSettings,
                'coupon_applied' => CouponService::hasCouponApplied(),
                'coupon_info' => CouponService::getAppliedCoupon()
            ],
            // Keep backward compatibility
            'cart_total' => $subtotal,
            'cart_count' => $cartCount
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Cart::removeFromCart($this->getSessionId(), $request->product_id);

        // Get complete cart data for Order Summary update
        $cartItems = Cart::getCartItems($this->getSessionId());
        $subtotal = Cart::getCartTotal($this->getSessionId());
        $cartCount = Cart::getCartCount($this->getSessionId());
        
        // Revalidate coupon with updated cart
        $couponResult = CouponService::revalidateExistingCoupon($this->getSessionId());
        $couponDiscount = $couponResult['success'] ? CouponService::getCurrentDiscount() : 0;
        
        // Calculate tax amounts with error handling
        $totalTax = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        
        try {
            foreach($cartItems as $item) {
                if ($item->product && method_exists($item->product, 'getTaxAmount')) {
                    $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                    $totalTax += $itemTax;
                    $cgstAmount += ($itemTax / 2);
                    $sgstAmount += ($itemTax / 2);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating tax amounts in cart remove: ' . $e->getMessage());
            // Use default values if calculation fails
            $totalTax = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;
        }
        
        // Calculate delivery charge using DeliveryService
        $deliveryCharge = \App\Services\DeliveryService::calculateDeliveryCharge($subtotal);
        $grandTotal = $subtotal + $totalTax + $deliveryCharge - $couponDiscount;
        
        // Get minimum order validation
        $minOrderValidationSettings = \App\Services\DeliveryService::getMinOrderValidationSettings();
        $minOrderValidation = \App\Services\DeliveryService::validateMinimumOrderAmount($grandTotal);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart!',
            'cart_data' => [
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'grand_total' => $grandTotal,
                'cart_count' => $cartCount,
                'min_order_validation' => $minOrderValidation,
                'min_order_settings' => $minOrderValidationSettings,
                'coupon_applied' => CouponService::hasCouponApplied(),
                'coupon_info' => CouponService::getAppliedCoupon()
            ],
            // Keep backward compatibility
            'cart_count' => $cartCount,
            'cart_total' => $subtotal
        ]);
    }

    public function clear()
    {
        Cart::clearCart($this->getSessionId());
        
        // Clear applied coupon when cart is cleared
        CouponService::removeCoupon();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!'
        ]);
    }

    public function count()
    {
        $count = Cart::getCartCount($this->getSessionId());
        
        return response()->json([
            'count' => $count
        ]);
    }
    
    public function totalQuantity()
    {
        $totalQuantity = Cart::getCartTotalQuantity($this->getSessionId());
        
        return response()->json([
            'total_quantity' => $totalQuantity
        ]);
    }
    
    public function summary()
    {
        $cartItems = Cart::getCartItems($this->getSessionId());
        $subtotal = Cart::getCartTotal($this->getSessionId());
        $cartCount = Cart::getCartCount($this->getSessionId());
        
        // Get coupon discount
        $couponDiscount = CouponService::getCurrentDiscount();
        
        // Calculate tax amounts with error handling
        $totalTax = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        $items = [];
        
        try {
            foreach($cartItems as $item) {
                if ($item->product && method_exists($item->product, 'getTaxAmount')) {
                    $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                    $totalTax += $itemTax;
                    $cgstAmount += ($itemTax / 2);
                    $sgstAmount += ($itemTax / 2);
                    
                    // Add item details
                    $items[] = [
                        'id' => $item->product_id,
                        'name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'tax_percentage' => $item->product->tax_percentage,
                        'tax_amount' => $itemTax,
                        'subtotal' => $item->price * $item->quantity,
                        'total' => $item->total
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error calculating tax amounts in cart summary: ' . $e->getMessage());
            // Use default values if calculation fails
            $totalTax = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;
        }
        
        // Calculate delivery charge using DeliveryService
        $deliveryCharge = \App\Services\DeliveryService::calculateDeliveryCharge($subtotal);
        $grandTotal = $subtotal + $totalTax + $deliveryCharge - $couponDiscount;
        
        // Get minimum order validation
        $minOrderValidationSettings = \App\Services\DeliveryService::getMinOrderValidationSettings();
        $minOrderValidation = \App\Services\DeliveryService::validateMinimumOrderAmount($grandTotal);
        
        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_data' => [
                'items' => $items,
                'subtotal' => $subtotal,
                'total_tax' => $totalTax,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'delivery_charge' => $deliveryCharge,
                'coupon_discount' => $couponDiscount,
                'payment_charge' => 0, // Default payment charge
                'grand_total' => $grandTotal,
                'cart_count' => $cartCount,
                'min_order_validation' => $minOrderValidation,
                'min_order_settings' => $minOrderValidationSettings,
                'coupon_applied' => CouponService::hasCouponApplied(),
                'coupon_info' => CouponService::getAppliedCoupon()
            ]
        ]);
    }
}
