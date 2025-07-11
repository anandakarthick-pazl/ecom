@extends('layouts.app')

@section('title', 'Minimum Order Test - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Current Settings Display --}}
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Current Minimum Order Settings
                    </h4>
                </div>
                <div class="card-body">
                    @php
                        $settings = \App\Services\DeliveryService::getMinOrderValidationSettings();
                        $currentCart = \App\Models\Cart::getCartTotal(session()->getId());
                        $validation = \App\Services\DeliveryService::validateMinimumOrderAmount($currentCart);
                    @endphp
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    @if($settings['min_order_validation_enabled'])
                                        <i class="fas fa-check-circle text-success fs-2"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fs-2"></i>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1">Validation Status</h5>
                                    <p class="mb-0 text-muted">
                                        {{ $settings['min_order_validation_enabled'] ? 'Enabled' : 'Disabled' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($settings['min_order_validation_enabled'])
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="fas fa-rupee-sign text-info fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Minimum Amount</h5>
                                    <p class="mb-0 text-muted">
                                        ₹{{ number_format($settings['min_order_amount'], 0) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($settings['min_order_validation_enabled'])
                    <hr class="my-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Current Message:</strong> {{ $settings['min_order_message'] }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Current Cart Status --}}
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Current Cart Status
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-calculator text-primary fs-1 mb-3"></i>
                                <h5>Cart Total</h5>
                                <h3 class="text-primary">₹{{ number_format($currentCart, 2) }}</h3>
                            </div>
                        </div>
                        
                        @if($settings['min_order_validation_enabled'])
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-{{ $validation['valid'] ? 'check-circle text-success' : 'exclamation-triangle text-warning' }} fs-1 mb-3"></i>
                                <h5>Validation Status</h5>
                                <h4 class="{{ $validation['valid'] ? 'text-success' : 'text-warning' }}">
                                    {{ $validation['valid'] ? 'Valid' : 'Below Minimum' }}
                                </h4>
                            </div>
                        </div>
                        
                        @if(!$validation['valid'])
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-plus-circle text-danger fs-1 mb-3"></i>
                                <h5>Amount Needed</h5>
                                <h3 class="text-danger">₹{{ number_format($validation['shortfall'], 2) }}</h3>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                    
                    @if($settings['min_order_validation_enabled'] && !$validation['valid'])
                    <hr class="my-4">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ $validation['message'] }}</strong>
                        <br>
                        <small class="text-muted">
                            Current total: ₹{{ number_format($validation['current_amount'], 2) }} | 
                            Add ₹{{ number_format($validation['shortfall'], 2) }} more
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Test Actions --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-cart-plus text-primary fs-1 mb-3"></i>
                            <h5>Test Cart Functionality</h5>
                            <p class="text-muted">Add test products to verify minimum order validation</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Go Shopping
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-eye text-info fs-1 mb-3"></i>
                            <h5>View Cart</h5>
                            <p class="text-muted">Check your current cart and validation status</p>
                            <a href="{{ route('cart.index') }}" class="btn btn-info">
                                <i class="fas fa-shopping-cart me-2"></i>View Cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Demo Products for Testing --}}
            @php
                $testProducts = \App\Models\Product::active()->inStock()->limit(4)->get();
            @endphp
            
            @if($testProducts->count() > 0)
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-flask me-2"></i>
                        Test Products (Quick Add to Cart)
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($testProducts as $product)
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center p-3">
                                    @if($product->featured_image)
                                        <img src="{{ $product->featured_image_url }}" class="img-fluid rounded mb-2" style="height: 60px; object-fit: cover;">
                                    @else
                                        <i class="fas fa-box text-muted fs-2 mb-2"></i>
                                    @endif
                                    <h6 class="card-title">{{ Str::limit($product->name, 20) }}</h6>
                                    <p class="text-primary fw-bold">₹{{ number_format($product->selling_price, 0) }}</p>
                                    <button class="btn btn-outline-primary btn-sm" onclick="addToCart({{ $product->id }}, 1)">
                                        <i class="fas fa-cart-plus me-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <hr class="my-4">
                    <div class="text-center">
                        <p class="text-muted mb-3">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            <strong>Testing Tips:</strong>
                        </p>
                        <ul class="list-unstyled text-muted small">
                            <li>• Add products to your cart</li>
                            <li>• Try to proceed to checkout with amounts below ₹{{ number_format($settings['min_order_amount'], 0) }}</li>
                            <li>• See how the validation message appears</li>
                            <li>• Notice how the checkout button becomes disabled</li>
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Admin Settings Link --}}
            <div class="text-center mt-4">
                <a href="/admin/settings" class="btn btn-outline-primary me-3">
                    <i class="fas fa-cog me-2"></i>
                    Configure Settings
                </a>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Go to Cart
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Add quick cart functionality for testing
window.addToCart = function(productId, quantity = 1) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                updateCartCount();
                showToast(response.message, 'success');
                
                // Trigger success animation if available
                if (window.triggerCrackers) {
                    window.triggerCrackers();
                }
                
                // Refresh the page after 1 second to update cart status
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('Something went wrong!', 'error');
        },
        complete: function() {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
};

// Display current settings on load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Minimum Order Test Page Loaded');
    console.log('Validation Enabled: {{ $settings["min_order_validation_enabled"] ? "true" : "false" }}');
    console.log('Minimum Amount: ₹{{ $settings["min_order_amount"] }}');
    console.log('Current Cart: ₹{{ $currentCart }}');
    console.log('Is Valid: {{ $validation["valid"] ? "true" : "false" }}');
});
</script>
@endsection
