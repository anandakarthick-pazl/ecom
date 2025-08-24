@extends('layouts.app-foodie')

@section('title', 'Shopping Cart - ' . ($globalCompany->company_name ?? 'Food Delivery'))
@section('meta_description', 'Review your order items and proceed to checkout.')

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Your Cart</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Review your order and proceed to checkout</p>
        </div>
    </div>
</section>

<!-- Cart Content -->
<section style="padding: 60px 0;">
    <div class="container">
        @if($cartItems->count() > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Order Items</h3>
                    
                    @foreach($cartItems as $item)
                    <div class="cart-item" style="border-bottom: 1px solid var(--border); padding: 1.5rem 0;">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                         alt="{{ $item->product->name }}"
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px;">
                                @else
                                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f5f5f5, #e0e0e0); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                                        🍽️
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <h5 style="font-weight: 600; margin-bottom: 0.5rem;">{{ $item->product->name }}</h5>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="color: var(--primary-color); font-weight: 600; font-size: 0.95rem;">₹{{ $item->price }}</span>
                                    @if($item->product->price > $item->price)
                                        <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.85rem;">₹{{ $item->product->price }}</span>
                                        <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">{{ round((($item->product->price - $item->price) / $item->product->price) * 100) }}% OFF</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="quantity-controls" style="display: flex; align-items: center; gap: 1rem;">
                                    <button class="btn-quantity" onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})" 
                                            style="width: 30px; height: 30px; border: 1px solid var(--border); background: white; border-radius: 50%; cursor: pointer;">
                                        <i class="fas fa-minus" style="font-size: 0.75rem;"></i>
                                    </button>
                                    <span style="font-weight: 600; min-width: 30px; text-align: center;">{{ $item->quantity }}</span>
                                    <button class="btn-quantity" onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})"
                                            style="width: 30px; height: 30px; border: 1px solid var(--border); background: white; border-radius: 50%; cursor: pointer;">
                                        <i class="fas fa-plus" style="font-size: 0.75rem;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <div>
                                    <p style="font-weight: 700; font-size: 1.25rem; color: var(--primary-color); margin: 0;">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    @if($item->product->price > $item->price)
                                        <p style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.85rem; margin: 0;">₹{{ number_format($item->product->price * $item->quantity, 2) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button onclick="removeFromCart({{ $item->product_id }})" 
                                        style="background: none; border: none; color: var(--danger-color); cursor: pointer; font-size: 1.25rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Continue Shopping -->
                <a href="{{ route('products') }}" class="btn-foodie btn-foodie-outline">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Order Summary</h3>
                    
                    @php
                        // Calculate total MRP and savings
                        $totalMRP = 0;
                        $totalSavings = 0;
                        foreach($cartItems as $item) {
                            $totalMRP += $item->product->price * $item->quantity;
                            if($item->product->price > $item->price) {
                                $totalSavings += ($item->product->price - $item->price) * $item->quantity;
                            }
                        }
                        $deliveryCharge = 40;
                    @endphp
                    
                    <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total MRP</span>
                            <span style="font-weight: 600;">₹{{ number_format($totalMRP, 2) }}</span>
                        </div>
                        @if($totalSavings > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--success-color);">
                            <span>Product Discount</span>
                            <span style="font-weight: 600;">-₹{{ number_format($totalSavings, 2) }}</span>
                        </div>
                        @endif
                        @if($couponDiscount > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--success-color);">
                            <span>Coupon Discount</span>
                            <span style="font-weight: 600;">-₹{{ number_format($couponDiscount, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Charges</span>
                            <span style="font-weight: 600;">₹{{ number_format($deliveryCharge, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-size: 1.25rem; font-weight: 700;">Total Amount</span>
                        <span style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color);">₹{{ number_format($totalMRP - $totalSavings - $couponDiscount + $deliveryCharge, 2) }}</span>
                    </div>
                    
                    @if($totalSavings + $couponDiscount > 0)
                    <div style="background: #e8f5e9; padding: 10px; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                        <span style="color: var(--success-color); font-weight: 600;">
                            🎉 You saved ₹{{ number_format($totalSavings + $couponDiscount, 2) }} on this order!
                        </span>
                    </div>
                    @endif
                    
                    <!-- Coupon Code -->
                    @if(!$appliedCoupon)
                    <div style="margin-bottom: 1.5rem;">
                        <div class="input-group">
                            <input type="text" id="coupon-code" placeholder="Enter coupon code" 
                                   style="flex: 1; padding: 10px; border: 1px solid var(--border); border-radius: 8px 0 0 8px; outline: none;">
                            <button onclick="applyCoupon()" 
                                    style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 0 8px 8px 0; cursor: pointer; font-weight: 600;">
                                Apply
                            </button>
                        </div>
                    </div>
                    @else
                    <div style="background: #e8f5e9; padding: 10px; border-radius: 8px; margin-bottom: 1rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="color: var(--success-color); font-weight: 600;">
                                <i class="fas fa-check-circle"></i> {{ $appliedCoupon['code'] }} applied
                            </span>
                            <button onclick="removeCoupon()" style="background: none; border: none; color: var(--danger-color); cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    @php
                        $finalTotal = $totalMRP - $totalSavings - $couponDiscount + $deliveryCharge;
                        $minOrderAmount = $minOrderValidationSettings['min_order_amount'] ?? 0;
                        $isValidOrder = $finalTotal >= $minOrderAmount;
                        $amountNeeded = max(0, $minOrderAmount - $finalTotal);
                    @endphp
                    
                    @if($isValidOrder)
                    <a href="{{ route('checkout') }}" class="btn-foodie btn-foodie-primary w-100">
                        Proceed to Checkout
                    </a>
                    @else
                    <div style="background: #fff3cd; padding: 10px; border-radius: 8px; margin-bottom: 1rem;">
                        <p style="color: #856404; font-size: 0.9rem; margin: 0;">
                            <i class="fas fa-info-circle"></i> Minimum order amount is ₹{{ number_format($minOrderAmount, 2) }}
                        </p>
                    </div>
                    <button class="btn-foodie btn-foodie-primary w-100" disabled style="opacity: 0.5;">
                        Add ₹{{ number_format($amountNeeded, 2) }} more
                    </button>
                    @endif
                    
                    <!-- Trust Badges -->
                    <div style="margin-top: 2rem; text-align: center;">
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                            <i class="fas fa-shield-alt"></i> Secure Checkout
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <i class="fab fa-cc-visa" style="font-size: 2rem; color: #1a1f71;"></i>
                            <i class="fab fa-cc-mastercard" style="font-size: 2rem; color: #eb001b;"></i>
                            <i class="fas fa-wallet" style="font-size: 2rem; color: var(--primary-color);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div style="font-size: 6rem; color: var(--text-secondary); margin-bottom: 1rem;">🛒</div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">Your cart is empty</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Looks like you haven't added anything to your cart yet</p>
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
    function updateQuantity(productId, quantity) {
        if (quantity < 1) {
            removeFromCart(productId);
            return;
        }
        
        fetch('{{ route("cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function removeFromCart(productId) {
        if (confirm('Remove this item from cart?')) {
            fetch('{{ route("cart.remove") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
    
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                alert(data.message);
            }
        });
    }
    
    function removeCoupon() {
        fetch('{{ route("coupon.remove") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
@endpush
