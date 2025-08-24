<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\Commission;
use App\Services\PaymentMethodService;
use App\Services\DeliveryService;
use App\Services\CouponService;
use App\Events\OrderPlaced;
use App\Services\OrderItemPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function getSessionId()
    {
        return session()->getId();
    }

    public function index()
    {
        $cartItems = Cart::getCartItems($this->getSessionId());
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = Cart::getCartTotal($this->getSessionId());
        
        // Get minimum order validation settings
        $minOrderValidationSettings = DeliveryService::getMinOrderValidationSettings();
        
        $deliveryCharge = DeliveryService::calculateDeliveryCharge($subtotal);
        $deliveryInfo = DeliveryService::getDeliveryInfo($subtotal);
        
        // Get coupon discount
        $discount = CouponService::getCurrentDiscount();
        $appliedCoupon = CouponService::getAppliedCoupon();
        
        $total = $subtotal + $deliveryCharge - $discount;
        
        // Get active payment methods using the service
        $paymentMethods = PaymentMethodService::getActiveForCheckout($total);

        // Check if fabric theme is enabled
        $theme = \App\Models\AppSetting::get('store_theme', 'default');
        $host = request()->getHost();
        
        // Use fabric theme if conditions met
        if ($host === 'greenvalleyherbs.local' || request()->get('theme') === 'fabric' || $theme === 'fabric') {
            return view('checkout-fabric', compact(
                'cartItems', 
                'subtotal', 
                'deliveryCharge', 
                'deliveryInfo', 
                'discount',
                'appliedCoupon',
                'total', 
                'paymentMethods',
                'minOrderValidationSettings'
            ));
        }
        
        return view('checkout', compact(
            'cartItems', 
            'subtotal', 
            'deliveryCharge', 
            'deliveryInfo', 
            'discount',
            'appliedCoupon',
            'total', 
            'paymentMethods',
            'minOrderValidationSettings'
        ));
    }

    public function store(Request $request)
    {
        // Add debugging
        \Log::info('Checkout process started', [
            'session_id' => $this->getSessionId(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|size:10',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'required|string|size:6',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|exists:payment_methods,id',
            'formatted_mobile' => 'nullable|string', // Optional formatted mobile with +91
            // Commission fields
            'commission_enabled' => 'nullable|boolean',
            'reference_name' => 'nullable|string|max:255',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_notes' => 'nullable|string|max:500'
        ]);

        // WHATSAPP MOBILE NUMBER FORMATTING
        // Use formatted mobile if provided, otherwise auto-format the regular mobile
        $whatsappMobile = $request->formatted_mobile;
        if (empty($whatsappMobile)) {
            // Auto-add +91 if not provided
            $whatsappMobile = '+91' . $request->customer_mobile;
        }
        
        \Log::info('Mobile number processing', [
            'original_mobile' => $request->customer_mobile,
            'formatted_mobile' => $request->formatted_mobile,
            'whatsapp_mobile' => $whatsappMobile
        ]);

        $cartItems = Cart::getCartItems($this->getSessionId());
        
        if ($cartItems->isEmpty()) {
            \Log::warning('Checkout attempted with empty cart', ['session_id' => $this->getSessionId()]);
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if (!$item->product) {
                \Log::error('Product not found in cart', ['item' => $item]);
                return redirect()->route('cart.index')->with('error', 'Some products in your cart no longer exist!');
            }
            if (!$item->product->isInStock($item->quantity)) {
                \Log::warning('Stock insufficient', ['product' => $item->product->name, 'requested' => $item->quantity, 'available' => $item->product->stock]);
                return redirect()->route('cart.index')->with('error', "Product '{$item->product->name}' is out of stock!");
            }
        }

        // Validate minimum order amount for online orders using DeliveryService
        $subtotalForValidation = Cart::getCartTotal($this->getSessionId());
        $validationResult = DeliveryService::validateMinimumOrderAmount($subtotalForValidation);
        
        if (!$validationResult['valid']) {
            \Log::warning('Order below minimum amount', [
                'subtotal' => $validationResult['current_amount'],
                'min_amount' => $validationResult['min_amount'],
                'shortfall' => $validationResult['shortfall'],
                'session_id' => $this->getSessionId()
            ]);
            return redirect()->route('cart.index')
                           ->with('error', $validationResult['message'])
                           ->with('min_order_validation', $validationResult);
        }

        try {
            DB::beginTransaction();

            // Create or get customer
            $customerData = [
                'name' => $request->customer_name,
                'address' => $request->delivery_address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
            ];
            
            // Add email to customer data if provided
            if ($request->customer_email) {
                $customerData['email'] = $request->customer_email;
            }
            
            // Use WhatsApp formatted mobile for customer lookup/creation
            $customer = Customer::findOrCreateByMobile($whatsappMobile, $customerData);
            \Log::info('Customer found/created', [
                'customer_id' => $customer->id,
                'mobile_used' => $whatsappMobile
            ]);

            // Calculate totals
            $subtotal = Cart::getCartTotal($this->getSessionId());
            
            // Calculate tax amounts
            $totalTax = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;
            
            foreach ($cartItems as $item) {
                $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                $totalTax += $itemTax;
                $cgstAmount += ($itemTax / 2);
                $sgstAmount += ($itemTax / 2);
            }
            
            $deliveryCharge = DeliveryService::calculateDeliveryCharge($subtotal);
            $discount = CouponService::getCurrentDiscount(); // Get coupon discount
            $appliedCoupon = CouponService::getAppliedCoupon();
            
            // Get payment method and calculate charges
            $paymentMethod = PaymentMethod::findOrFail($request->payment_method);
            $paymentCharge = 0;
            
            if ($paymentMethod->extra_charge > 0 || $paymentMethod->extra_charge_percentage > 0) {
                $baseTotal = $subtotal + $totalTax + $deliveryCharge - $discount;
                $paymentCharge = $paymentMethod->extra_charge + ($baseTotal * $paymentMethod->extra_charge_percentage / 100);
            }
            
            $total = $subtotal + $totalTax + $deliveryCharge + $paymentCharge - $discount;

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_mobile' => $whatsappMobile, // Use WhatsApp formatted mobile (+91 prefix)
                'customer_email' => $request->customer_email, // Store email in order
                'delivery_address' => $request->delivery_address,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $appliedCoupon ? $appliedCoupon['code'] : null,
                'coupon_discount' => $discount,
                'delivery_charge' => $deliveryCharge,
                'tax_amount' => $totalTax,
                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'total' => $total,
                'notes' => $request->notes,
                'status' => 'pending',
                'payment_method' => $paymentMethod->type,
                'payment_status' => $paymentMethod->type === 'cod' ? 'pending' : 'pending',
                // Commission fields
                'commission_enabled' => $request->boolean('commission_enabled'),
                'reference_name' => $request->reference_name,
                'commission_percentage' => $request->commission_percentage,
                'commission_notes' => $request->commission_notes
            ]);

            \Log::info('Order created', ['order_id' => $order->id, 'order_number' => $order->order_number]);

            // Create order items with pricing details and update stock
            foreach ($cartItems as $item) {
                // Use the new pricing service to create order item with MRP and offer details
                OrderItemPricingService::createOrderItemWithPricing(
                    ['order_id' => $order->id],
                    $item
                );

                // Update product stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Create commission record if enabled
            if ($order->commission_enabled && !empty($order->reference_name) && !empty($order->commission_percentage)) {
                try {
                    $commission = $order->createCommissionRecord();
                    if ($commission) {
                        \Log::info('Commission created for order', [
                            'order_id' => $order->id,
                            'commission_id' => $commission->id,
                            'reference_name' => $order->reference_name,
                            'commission_percentage' => $order->commission_percentage,
                            'commission_amount' => $commission->commission_amount
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to create commission for order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the order if commission creation fails
                }
            }

            // Update customer statistics
            $customer->updateOrderStats();
            
            // Increment coupon usage count if coupon was applied
            if ($appliedCoupon) {
                CouponService::incrementUsageCount($appliedCoupon['code']);
            }

            // Clear cart and coupon
            Cart::clearCart($this->getSessionId());
            CouponService::removeCoupon();
            \Log::info('Cart and coupon cleared after order creation');

            DB::commit();
            \Log::info('Order transaction committed successfully');

            // Fire order placed event
            event(new OrderPlaced($order));

            // Store order details in session for success page
            session()->flash('order_success', [
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'total' => $order->total,
                'payment_method' => $paymentMethod
            ]);

            \Log::info('Redirecting based on payment method', [
                'order_number' => $order->order_number,
                'payment_method' => $paymentMethod->type
            ]);
            
            // Clear the cart and coupon one more time to be absolutely sure
            Cart::clearCart($this->getSessionId());
            CouponService::removeCoupon();
            
            // Handle payment method specific redirects
            if ($paymentMethod->type === 'razorpay') {
                // For Razorpay, store order ID and redirect to payment page
                session(['razorpay_order_id' => $order->id]);
                return redirect()->route('order.success', $order->order_number)
                               ->with('initiate_payment', true)
                               ->with('payment_method', 'razorpay');
            } else {
                // For other payment methods, redirect to success page
                $successUrl = route('order.success', $order->order_number);
                \Log::info('Success URL generated', ['url' => $successUrl]);
                
                return redirect($successUrl)
                               ->with('success', 'Order placed successfully!')
                               ->with('order_number', $order->order_number);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage(), [
                'exception' => $e,
                'session_id' => $this->getSessionId(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')->with('error', 'Unable to process order. Please try again.');
        }
    }

    public function success($orderNumber)
    {
        \Log::info('Order success page accessed', ['order_number' => $orderNumber]);
        
        try {
            $order = Order::where('order_number', $orderNumber)
                         ->with('items.product')
                         ->firstOrFail();
            
            \Log::info('Order found for success page', ['order_id' => $order->id]);
            
            // Check if fabric theme is enabled
            $theme = \App\Models\AppSetting::get('store_theme', 'default');
            $host = request()->getHost();
            
            // Use fabric theme if conditions met
            if ($host === 'greenvalleyherbs.local' || request()->get('theme') === 'fabric' || $theme === 'fabric') {
                return view('order-success-fabric', compact('order'));
            }
            
            return view('order-success', compact('order'));
        } catch (\Exception $e) {
            \Log::error('Order success page error', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('shop')->with('error', 'Order not found. Please contact support if you believe this is an error.');
        }
    }

    /**
     * @deprecated Use DeliveryService::calculateDeliveryCharge() instead
     * Calculate delivery charge based on subtotal (legacy method)
     */
    private function calculateDeliveryCharge($subtotal)
    {
        // Use the new DeliveryService for consistent calculation
        return DeliveryService::calculateDeliveryCharge($subtotal);
    }
}
