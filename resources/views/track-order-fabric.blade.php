@extends('layouts.app-fabric')

@section('title', 'Track Order - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<section style="padding: 3rem 0; background: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div style="background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <h2 style="text-align: center; margin-bottom: 2rem; font-weight: 700;">Track Your Order</h2>
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(!isset($orders))
                        <!-- Track Order Form -->
                        <form method="POST" action="{{ route('track.order') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="mobile_number" style="font-weight: 600; margin-bottom: 0.5rem;">Mobile Number <span style="color: red;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: #f8f9fa; border-right: none;">+91</span>
                                    <input type="text" 
                                           class="form-control @error('mobile_number') is-invalid @enderror" 
                                           id="mobile_number" 
                                           name="mobile_number" 
                                           placeholder="Enter 10 digit mobile number"
                                           value="{{ old('mobile_number') }}"
                                           maxlength="10"
                                           pattern="[0-9]{10}"
                                           required
                                           style="border-left: none;">
                                </div>
                                @error('mobile_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="order_number" style="font-weight: 600; margin-bottom: 0.5rem;">Order Number (Optional)</label>
                                <input type="text" 
                                       class="form-control @error('order_number') is-invalid @enderror" 
                                       id="order_number" 
                                       name="order_number" 
                                       placeholder="Enter order number (e.g., ORD-XXXXXX)"
                                       value="{{ old('order_number') }}">
                                @error('order_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" style="width: 100%; padding: 0.75rem; background: #ff6b35; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem;">
                                Track Order
                            </button>
                        </form>
                        
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e0e0e0;">
                            <p style="color: #6c757d; text-align: center; margin-bottom: 0;">
                                <i class="fas fa-info-circle"></i> Enter your registered mobile number to track all your orders
                            </p>
                        </div>
                    @else
                        <!-- Order Results -->
                        @if($orders->count() > 0)
                            <div class="mb-3">
                                <h4 style="font-weight: 600;">Found {{ $orders->count() }} Order(s)</h4>
                            </div>
                            
                            @foreach($orders as $order)
                            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Order Number:</strong> {{ $order->order_number }}
                                        </p>
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}
                                        </p>
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Total Amount:</strong> ₹{{ number_format($order->total_amount, 2) }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Status:</strong> 
                                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Payment:</strong> 
                                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </p>
                                        <p style="margin-bottom: 0.5rem;">
                                            <strong>Items:</strong> {{ $order->items->count() }}
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Order Items -->
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                                    <h6 style="font-weight: 600; margin-bottom: 0.5rem;">Order Items:</h6>
                                    @foreach($order->items as $item)
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                                            <div>
                                                <span>{{ $item->product->name ?? 'Product' }}</span>
                                                <span style="color: #6c757d;"> x {{ $item->quantity }}</span>
                                            </div>
                                            <span>₹{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Delivery Address -->
                                @if($order->delivery_address)
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                                    <h6 style="font-weight: 600; margin-bottom: 0.5rem;">Delivery Address:</h6>
                                    <p style="margin-bottom: 0; color: #6c757d;">{{ $order->delivery_address }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach
                            
                            <a href="{{ route('track.order') }}" style="display: inline-block; padding: 0.75rem 1.5rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                Track Another Order
                            </a>
                        @else
                            <div class="text-center" style="padding: 2rem 0;">
                                <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                                <h4>No Orders Found</h4>
                                <p style="color: #6c757d;">No orders found for the provided mobile number.</p>
                                <a href="{{ route('track.order') }}" style="display: inline-block; padding: 0.75rem 1.5rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 1rem;">
                                    Try Again
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Cart Button -->
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <a href="{{ route('cart.index') }}" 
       style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: #ff6b35; color: white; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-decoration: none; position: relative;">
        <i class="fas fa-shopping-cart" style="font-size: 1.5rem;"></i>
        <span id="floating-cart-count" 
              style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.75rem; font-weight: 600; padding: 2px 6px; border-radius: 50%; min-width: 20px; text-align: center; display: none;">
            0
        </span>
    </a>
</div>
@endsection

@section('scripts')
<script>
// Update cart count
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;
            
            const navbarBadge = document.getElementById('cart-count-badge');
            if (navbarBadge) {
                navbarBadge.textContent = count;
                navbarBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
            
            const floatingBadge = document.getElementById('floating-cart-count');
            if (floatingBadge) {
                floatingBadge.textContent = count;
                floatingBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Format mobile number input
    const mobileInput = document.getElementById('mobile_number');
    if (mobileInput) {
        mobileInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
        });
    }
});
</script>
@endsection
