@extends('layouts.app')

@section('title', 'Shopping Cart - Herbal Bliss')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>
    
    @if($cartItems->count() > 0)
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @foreach($cartItems as $item)
                    <div class="cart-item border-bottom py-3" data-product-id="{{ $item->product_id }}">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                @if($item->product->featured_image)
                                    <img src="{{ $item->product->featured_image_url }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-md-4">
                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                <small class="text-muted">{{ $item->product->category->name }}</small>
                                @if($item->product->weight)
                                    <br><small class="text-muted">{{ $item->product->weight }} {{ $item->product->weight_unit }}</small>
                                @endif
                            </div>
                            
                            <div class="col-md-2 text-center">
                                <strong>₹{{ number_format($item->price, 2) }}</strong>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})">-</button>
                                    <input type="number" class="form-control text-center quantity-input" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" data-product-id="{{ $item->product_id }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})">+</button>
                                </div>
                                <small class="text-muted">Max: {{ $item->product->stock }}</small>
                            </div>
                            
                            <div class="col-md-1 text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart({{ $item->product_id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-6">
                                @if($item->product->tax_percentage > 0)
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Tax: {{ $item->product->tax_percentage }}% 
                                        (CGST: ₹{{ number_format($item->product->getCgstAmount($item->price) * $item->quantity, 2) }} + 
                                        SGST: ₹{{ number_format($item->product->getSgstAmount($item->price) * $item->quantity, 2) }})
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Subtotal: ₹<span class="item-total">{{ number_format($item->total, 2) }}</span></strong>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-3">
                <button class="btn btn-outline-secondary" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>
                <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @php
                        // Calculate tax amounts
                        $totalTax = 0;
                        $cgstAmount = 0;
                        $sgstAmount = 0;
                        
                        foreach($cartItems as $item) {
                            $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                            $totalTax += $itemTax;
                            $cgstAmount += ($itemTax / 2);
                            $sgstAmount += ($itemTax / 2);
                        }
                        
                        $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                        $grandTotal = $subtotal + $totalTax + $deliveryCharge;
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST:</span>
                        <span id="cgst-amount">₹{{ number_format($cgstAmount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST:</span>
                        <span id="sgst-amount">₹{{ number_format($sgstAmount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Tax:</span>
                        <span id="total-tax">₹{{ number_format($totalTax, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery:</span>
                        <span id="delivery-charge">
                            @if($subtotal >= 500)
                                <span class="text-success">FREE</span>
                            @else
                                ₹50.00
                            @endif
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="cart-total">
                            ₹{{ number_format($grandTotal, 2) }}
                        </strong>
                    </div>
                    
                    @if($subtotal < 500)
                        <div class="alert alert-info py-2">
                            <small>Add ₹{{ number_format(500 - $subtotal, 2) }} more for FREE delivery!</small>
                        </div>
                    @endif
                    
                    <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
        <h4>Your cart is empty</h4>
        <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
        <a href="{{ route('shop') }}" class="btn btn-primary">
            <i class="fas fa-leaf"></i> Start Shopping
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateCartQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: 'PUT',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                location.reload(); // Reload to update cart display
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('Something went wrong!', 'error');
        }
    });
}

function removeFromCart(productId) {
    if(confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                    updateCartCount();
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                showToast('Something went wrong!', 'error');
            }
        });
    }
}

function clearCart() {
    if(confirm('Are you sure you want to clear your entire cart?')) {
        $.ajax({
            url: '{{ route("cart.clear") }}',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                    updateCartCount();
                    showToast(response.message, 'success');
                }
            },
            error: function() {
                showToast('Something went wrong!', 'error');
            }
        });
    }
}

// Handle quantity input changes
$(document).on('change', '.quantity-input', function() {
    const productId = $(this).data('product-id');
    const quantity = parseInt($(this).val());
    updateCartQuantity(productId, quantity);
});
</script>
@endpush
@endsection
