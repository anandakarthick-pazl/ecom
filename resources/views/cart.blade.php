@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4>Shopping Cart ({{ $cartItems->count() }} items)</h4>
                </div>
                <div class="card-body">
                    @if($cartItems->count() > 0)
                        @foreach($cartItems as $item)
                            @php
                                // Get offer details using the new priority system (same logic as product-card-modern)
                                $offerDetails = $item->product->getOfferDetails();
                                $hasOffer = $offerDetails !== null;
                                $effectivePrice = $hasOffer ? $offerDetails['discounted_price'] : $item->product->price;
                                $discountPercentage = $hasOffer ? $offerDetails['discount_percentage'] : 0;
                                $offerSource = $hasOffer ? $offerDetails['source'] : null;
                                $savings = $hasOffer ? $offerDetails['savings'] : 0;
                            @endphp
                            
                            <div class="cart-item mb-3 pb-3 border-bottom" data-product-id="{{ $item->product_id }}">
                                <div class="row">
                                    <div class="col-md-2">
                                        @if($item->product->featured_image)
                                            <img src="{{ $item->product->featured_image_url }}" 
                                                 class="img-fluid rounded" 
                                                 alt="{{ $item->product->name }}">
                                        @else
                                            <div class="product-placeholder bg-light rounded p-4 text-center">
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            </div>
                                        @endif
                                        
                                        @if($hasOffer && $discountPercentage > 0)
                                            <span class="badge bg-danger position-absolute">
                                                {{ round($discountPercentage) }}% OFF
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-1">{{ $item->product->name }}</h5>
                                        <p class="text-muted mb-2">{{ $item->product->category->name }}</p>
                                        
                                        @if($item->product->short_description)
                                            <p class="small text-muted">{{ Str::limit($item->product->short_description, 100) }}</p>
                                        @endif
                                        
                                        {{-- Show offer details with priority information (same as product card) --}}
                                        @if($hasOffer && $offerDetails)
                                            <div class="offer-info alert alert-success p-2 mb-2">
                                                @if($offerSource === 'offers_page')
                                                    <i class="fas fa-fire text-danger"></i> 
                                                    <small class="text-success fw-bold">{{ $offerDetails['offer_name'] }}</small>
                                                    <span class="badge bg-success ms-1">
                                                        {{ round($offerDetails['discount_percentage']) }}% OFF
                                                    </span>
                                                    <br><small class="text-muted">🎯 Special Offer</small>
                                                @elseif($offerSource === 'product_onboarding')
                                                    <i class="fas fa-tag text-info"></i> 
                                                    <small class="text-info fw-bold">Product Discount</small>
                                                    <span class="badge bg-info ms-1">
                                                        {{ round($offerDetails['discount_percentage']) }}% OFF
                                                    </span>
                                                    <br><small class="text-muted">💰 Regular Discount</small>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex align-items-center mt-3">
                                            <div class="quantity-selector me-3">
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="form-control form-control-sm d-inline-block mx-2" 
                                                       style="width: 60px;" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock }}"
                                                       onchange="updateQuantity({{ $item->product_id }}, this.value)">
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeFromCart({{ $item->product_id }})">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                        
                                        @if($item->product->stock <= 5)
                                            <small class="text-warning d-block mt-2">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Only {{ $item->product->stock }} left in stock!
                                            </small>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-4 text-end">
                                        {{-- Price Display with Offer Logic --}}
                                        <div class="price-section mb-2">
                                            @if($hasOffer)
                                                <h5 class="mb-0 text-primary">₹{{ number_format($effectivePrice, 2) }}</h5>
                                                <small class="text-muted text-decoration-line-through">
                                                    ₹{{ number_format($item->product->price, 2) }}
                                                </small>
                                            @else
                                                <h5 class="mb-0 text-primary">₹{{ number_format($item->product->price, 2) }}</h5>
                                            @endif
                                        </div>
                                        
                                        {{-- Item Total --}}
                                        <div class="item-total">
                                            <small class="text-muted d-block">Item Total:</small>
                                            <h4 class="mb-0">₹{{ number_format($effectivePrice * $item->quantity, 2) }}</h4>
                                        </div>
                                        
                                        {{-- Savings Display --}}
                                        @if($hasOffer)
                                            <div class="savings-info mt-2">
                                                <small class="text-success">
                                                    <i class="fas fa-tags"></i> You save ₹{{ number_format($savings * $item->quantity, 2) }}
                                                    @if($offerSource === 'offers_page')
                                                        <span class="badge badge-sm bg-success ms-1">Special Offer</span>
                                                    @elseif($offerSource === 'product_onboarding')
                                                        <span class="badge badge-sm bg-info ms-1">Product Discount</span>
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                        
                                        {{-- Tax Information --}}
                                        @if(isset($item->product->tax_percentage) && $item->product->tax_percentage > 0)
                                            <div class="tax-info mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i>
                                                    Tax: {{ $item->product->tax_percentage }}% included
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="cart-actions mt-3">
                            <a href="{{ route('products') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            <button class="btn btn-outline-danger float-end" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h5>Your cart is empty</h5>
                            <p class="text-muted">Add some products to get started!</p>
                            <a href="{{ route('products') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            {{-- Order Summary --}}
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Estimate Summary</h5>
                </div>
                <div class="card-body">
                    @if($cartItems->count() > 0)
                        @php
                            $subtotal = 0;
                            $totalSavings = 0;
                            $totalTax = 0;
                            
                            foreach($cartItems as $item) {
                                $offerDetails = $item->product->getOfferDetails();
                                $effectivePrice = $offerDetails ? $offerDetails['discounted_price'] : $item->product->price;
                                $subtotal += $effectivePrice * $item->quantity;
                                
                                if($offerDetails) {
                                    $totalSavings += $offerDetails['savings'] * $item->quantity;
                                }
                                
                                if(isset($item->product->tax_percentage)) {
                                    $totalTax += ($effectivePrice * $item->quantity * $item->product->tax_percentage / 100);
                                }
                            }
                            
                            $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                            $grandTotal = $subtotal;
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                                <span>₹{{ number_format($subtotal-$totalTax, 2) }}</span>
                            </div>
                            
                            @if($totalSavings > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Total Savings</span>
                                    <span>-₹{{ number_format($totalSavings, 2) }}</span>
                                </div>
                            @endif
                            
                            {{-- @if($totalTax > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax</span>
                                    <span>₹{{ number_format($totalTax, 2) }}</span>
                                </div>
                            @endif --}}
                            
                            {{-- <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Charges</span>
                                <span>
                                    @if($deliveryCharge == 0)
                                        <span class="text-success">FREE</span>
                                    @else
                                        ₹{{ number_format($deliveryCharge, 2) }}
                                    @endif
                                </span>
                            </div> --}}
                            
                            @if($subtotal < 500)
                                <div class="alert alert-info p-2 mb-3">
                                    <small>
                                        <i class="fas fa-truck"></i>
                                        Add ₹{{ number_format(500 - $subtotal, 2) }} more for FREE delivery!
                                    </small>
                                </div>
                            @endif
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total Amount</h5>
                                <h5 class="text-primary">₹{{ number_format($grandTotal, 2) }}</h5>
                            </div>
                            
                            <button class="btn btn-success btn-block w-100" onclick="proceedToCheckout()">
                                <i class="fas fa-lock"></i> Proceed to Checkout
                            </button>
                            
                            {{-- <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i> Secure Checkout
                                </small>
                            </div> --}}
                        </div>
                        
                        {{-- Coupon Code Section --}}
                        {{-- <div class="mt-3 pt-3 border-top">
                            <h6>Have a Coupon?</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter coupon code" id="couponCode">
                                <button class="btn btn-outline-primary" onclick="applyCoupon()">
                                    Apply
                                </button>
                            </div>
                        </div> --}}
                        
                        {{-- Payment Methods --}}
                        {{-- <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">We Accept:</small>
                            <div class="payment-icons mt-2">
                                <i class="fab fa-cc-visa fa-2x me-2"></i>
                                <i class="fab fa-cc-mastercard fa-2x me-2"></i>
                                <i class="fab fa-cc-paypal fa-2x me-2"></i>
                                <i class="fab fa-google-pay fa-2x me-2"></i>
                                <i class="fas fa-university fa-2x"></i>
                            </div> --}}
                        </div>
                    @else
                        <p class="text-muted text-center">Your cart is empty</p>
                    @endif
                </div>
            </div>
            
            {{-- Features --}}
            {{-- <div class="card mt-3">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                            <small class="d-block">Free Shipping</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                            <small class="d-block">Secure Payment</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo fa-2x text-primary mb-2"></i>
                            <small class="d-block">Easy Returns</small>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

<style>
/* Custom styles for cart page */
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    margin: -10px;
}

.quantity-selector {
    display: inline-flex;
    align-items: center;
}

.quantity-selector input {
    text-align: center;
}

.product-placeholder {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.offer-info {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 8px;
    border: 1px solid rgba(34, 197, 94, 0.2);
}

.payment-icons i {
    color: #6c757d;
}

.badge {
    font-size: 0.75rem;
}

.savings-info .badge-sm {
    font-size: 0.65rem;
    padding: 2px 6px;
}

/* Animation for cart updates */
@keyframes cartUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.cart-updating {
    animation: cartUpdate 0.3s ease-in-out;
}
</style>

<script>
// Cart functionality
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    // Add loading state
    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    cartItem.classList.add('cart-updating');
    
    fetch('{{ route("cart.update") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to update totals
        } else {
            alert(data.message || 'Failed to update quantity');
            cartItem.classList.remove('cart-updating');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update cart');
        cartItem.classList.remove('cart-updating');
    });
}

function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from cart?')) {
        return;
    }
    
    fetch('{{ route("cart.remove") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }
    
    fetch('{{ route("cart.clear") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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

function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    
    if (!couponCode) {
        alert('Please enter a coupon code');
        return;
    }
    
    fetch('{{ route('coupon.apply')  }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Coupon applied successfully!');
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

function proceedToCheckout() {
    window.location.href = '{{ route("checkout") }}';
}
</script>
@endsection