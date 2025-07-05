<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
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
        
        return view('cart', compact('cartItems', 'subtotal'));
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

        $cartTotal = Cart::getCartTotal($this->getSessionId());
        $cartCount = Cart::getCartCount($this->getSessionId());

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart_total' => $cartTotal,
            'cart_count' => $cartCount
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Cart::removeFromCart($this->getSessionId(), $request->product_id);

        $cartCount = Cart::getCartCount($this->getSessionId());
        $cartTotal = Cart::getCartTotal($this->getSessionId());

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart!',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    public function clear()
    {
        Cart::clearCart($this->getSessionId());

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
}
