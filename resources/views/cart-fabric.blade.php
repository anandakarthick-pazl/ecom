@extends('layouts.app-fabric')

@section('title', 'Shopping Cart - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<section style="padding: 3rem 0; background: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Shopping Cart</h2>
        
        @if($cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    @foreach($cartItems as $item)
                    <div style="display: flex; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e0e0e0; {{ $loop->last ? 'border-bottom: none;' : '' }}">
                        <!-- Product Image -->
                        <div style="width: 100px; height: 100px; flex-shrink: 0;">
                            <img src="{{ $item->product->image_url }}" 
                                 alt="{{ $item->product->name }}" 
                                 style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;">
                        </div>
                        
                        <!-- Product Details -->
                        <div style="flex: 1;">
                            <h5 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                                {{ $item->product->name }}
                            </h5>
                            
                            @if($item->product->category)
                            <p style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.5rem;">
                                Category: {{ $item->product->category->name }}
                            </p>
                            @endif
                            
                            <div style="display: flex; align-items: center; gap: 2rem;">
                                <!-- Price -->
                                <div>
                                    <span style="font-size: 1.1rem; font-weight: 600; color: #ff6b35;">
                                        ₹{{ number_format($item->price, 2) }}
                                    </span>
                                    @if($item->product->price > $item->price)
                                        <span style="font-size: 0.85rem; color: #999; text-decoration: line-through; margin-left: 0.5rem;">
                                            ₹{{ number_format($item->product->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})" 
                                            style="width: 30px; height: 30px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <input type="number" 
                                           value="{{ $item->quantity }}" 
                                           min="1"
                                           id="qty-{{ $item->product_id }}"
                                           onchange="updateQuantity({{ $item->product_id }}, this.value)"
                                           style="width: 50px; text-align: center; border: 1px solid #ddd; border-radius: 4px; padding: 0.25rem;">
                                    <button onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})" 
                                            style="width: 30px; height: 30px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer;">
                                        <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                                
                                <!-- Subtotal -->
                                <div style="margin-left: auto;">
                                    <span style="font-size: 1.1rem; font-weight: 600;">
                                        ₹{{ number_format($item->price * $item->quantity, 2) }}
                                    </span>
                                </div>
                                
                                <!-- Remove Button -->
                                <button onclick="removeFromCart({{ $item->product_id }})" 
                                        style="color: #dc3545; background: none; border: none; cursor: pointer; padding: 0.5rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Clear Cart Button -->
                    <div style="margin-top: 1rem; text-align: right;">
                        <button onclick="clearCart()" 
                                style="padding: 0.5rem 1rem; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 100px;">
                    <h4 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">Order Summary</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($couponDiscount > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: #28a745;">
                            <span>Coupon Discount</span>
                            <span>-₹{{ number_format($couponDiscount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span>Delivery Charge</span>
                            <span>₹{{ number_format(\App\Services\DeliveryService::calculateDeliveryCharge($subtotal), 2) }}</span>
                        </div>
                        
                        <hr style="margin: 1rem 0;">
                        
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700;">
                            <span>Total</span>
                            <span style="color: #ff6b35;">
                                ₹{{ number_format($subtotal + \App\Services\DeliveryService::calculateDeliveryCharge($subtotal) - $couponDiscount, 2) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Coupon Code -->
                    @if(!$appliedCoupon)
                    <div style="margin: 1.5rem 0;">
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" 
                                   id="coupon-code" 
                                   placeholder="Enter coupon code"
                                   style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 6px;">
                            <button onclick="applyCoupon()" 
                                    style="padding: 0.5rem 1rem; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                Apply
                            </button>
                        </div>
                    </div>
                    @else
                    <div style="margin: 1.5rem 0; padding: 0.75rem; background: #d4edda; border-radius: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #155724;">
                                <i class="fas fa-check-circle"></i> {{ $appliedCoupon->code }}
                            </span>
                            <button onclick="removeCoupon()" style="background: none; border: none; color: #dc3545; cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Minimum Order Message -->
                    @if(isset($minOrderValidation['valid']) && !$minOrderValidation['valid'])
                    <div style="padding: 0.75rem; background: #f8d7da; border-radius: 6px; margin-bottom: 1rem;">
                        <p style="margin: 0; color: #721c24; font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i> {{ $minOrderValidation['message'] ?? 'Minimum order amount not met' }}
                        </p>
                    </div>
                    @endif
                    
                    <!-- Checkout Button -->
                    <a href="{{ route('checkout') }}" 
                       style="display: block; width: 100%; padding: 1rem; background: {{ ($minOrderValidation['valid'] ?? true) ? '#ff6b35' : '#6c757d' }}; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; {{ isset($minOrderValidation['valid']) && !$minOrderValidation['valid'] ? 'pointer-events: none;' : '' }}">
                        Proceed to Checkout
                    </a>
                    
                    <!-- Continue Shopping -->
                    <a href="{{ route('products') }}" 
                       style="display: block; width: 100%; padding: 0.75rem; background: transparent; color: #ff6b35; text-align: center; text-decoration: none; border: 2px solid #ff6b35; border-radius: 8px; font-weight: 600; margin-top: 0.75rem;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div style="background: white; border-radius: 12px; padding: 4rem 2rem; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ddd; margin-bottom: 1.5rem;"></i>
            <h3 style="margin-bottom: 1rem;">Your cart is empty</h3>
            <p style="color: #6c757d; margin-bottom: 2rem;">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('products') }}" 
               style="display: inline-block; padding: 0.75rem 2rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</section>
@endsection

@section('scripts')
<script>
// Update quantity
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;
    
    fetch('{{ route("cart.update") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to update cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update cart');
    });
}

// Remove from cart
function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    fetch('{{ route("cart.remove") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to remove item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove item');
    });
}

// Clear cart
function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) return;
    
    fetch('{{ route("cart.clear") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to clear cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to clear cart');
    });
}

// Apply coupon
function applyCoupon() {
    const code = document.getElementById('coupon-code').value;
    if (!code) {
        alert('Please enter a coupon code');
        return;
    }
    
    fetch('{{ route("coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            code: code
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Invalid coupon code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to apply coupon');
    });
}

// Remove coupon
function removeCoupon() {
    fetch('{{ route("coupon.remove") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
