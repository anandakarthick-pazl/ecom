@extends('layouts.app')

@section('title', 'Track Your Order - Herbal Bliss')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Track Your Estimate</h4>
                </div>
                <div class="card-body">
                    @if(!isset($orders))
                    <form method="POST" action="{{ route('track.order') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number *</label>
                            <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" 
                                   id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" 
                                   pattern="[0-9]{10}" maxlength="10" required placeholder="Enter your 10-digit mobile number">
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_number" class="form-label">Order Number (Optional)</label>
                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" 
                                   id="order_number" name="order_number" value="{{ old('order_number') }}" 
                                   placeholder="Enter order number for specific order">
                            @error('order_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track Estimate
                        </button>
                    </form>
                    @else
                    <div class="mb-4">
                        <h6>Showing orders for: <strong>{{ request('mobile_number') }}</strong></h6>
                        <a href="{{ route('track.order') }}" class="btn btn-sm btn-outline-secondary">Track Different Number</a>
                    </div>

                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                        <div class="order-card border rounded mb-4 p-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Order #{{ $order->order_number }}</h6>
                                    <p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                                    <p class="mb-1"><strong>Total:</strong> ₹{{ number_format($order->total, 2) }}</p>
                                    <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-{{ $order->status_color }} mb-2">{{ ucfirst($order->status) }}</span>
                                    @if($order->shipped_at)
                                        <p class="mb-1"><small>Shipped: {{ $order->shipped_at->format('M d, Y') }}</small></p>
                                    @endif
                                    @if($order->delivered_at)
                                        <p class="mb-1"><small>Delivered: {{ $order->delivered_at->format('M d, Y') }}</small></p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Order Progress -->
                            <div class="order-progress mt-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    @php
                                        $progress = match($order->status) {
                                            'pending' => 25,
                                            'processing' => 50,
                                            'shipped' => 75,
                                            'delivered' => 100,
                                            'cancelled' => 0,
                                            default => 0
                                        };
                                    @endphp
                                    <div class="progress-bar bg-{{ $order->status === 'cancelled' ? 'danger' : 'success' }}" 
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <small class="text-{{ $progress >= 25 ? 'success' : 'muted' }}">
                                            <i class="fas fa-check-circle"></i> Ordered
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-{{ $progress >= 50 ? 'success' : 'muted' }}">
                                            <i class="fas fa-cogs"></i> Processing
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-{{ $progress >= 75 ? 'success' : 'muted' }}">
                                            <i class="fas fa-truck"></i> Shipped
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-{{ $progress >= 100 ? 'success' : 'muted' }}">
                                            <i class="fas fa-home"></i> Delivered
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="order-items mt-3">
                                <h6>Order Items:</h6>
                                @foreach($order->items as $item)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <br><small class="text-muted">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}</small>
                                    </div>
                                    <div>
                                        <strong>₹{{ number_format($item->total, 2) }}</strong>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Delivery Address -->
                            <div class="delivery-address mt-3">
                                <h6>Delivery Address:</h6>
                                <p class="mb-0">{{ $order->delivery_address }}, {{ $order->city }}, {{ $order->state }} {{ $order->pincode }}</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>No orders found</h5>
                            <p class="text-muted">We couldn't find any orders for this mobile number.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ROBUST jQuery waiting for Track Order Page
console.log('=== TRACK ORDER PAGE JQUERY CHECK ===');
console.log('Track Order Page - jQuery loaded:', typeof $ !== 'undefined');

// Function to wait for jQuery (same as layout)
function waitForTrackJQuery(callback, maxRetries = 50, currentRetry = 0) {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        console.log('✅ Track Order Page - jQuery loaded successfully on attempt:', currentRetry + 1);
        callback();
    } else if (currentRetry < maxRetries) {
        console.log('⏳ Track Order Page - Waiting for jQuery... attempt:', currentRetry + 1);
        setTimeout(() => {
            waitForTrackJQuery(callback, maxRetries, currentRetry + 1);
        }, 100);
    } else {
        console.error('❌ Track Order Page - FAILED: jQuery could not load after', maxRetries, 'attempts');
        console.error('Track order functionality will be limited.');
    }
}

// Initialize track order functionality only when jQuery is ready
waitForTrackJQuery(function() {
    console.log('🚀 Track Order Page - Initializing functionality...');
    
    // Auto-format mobile number
    $('#mobile_number').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });
    
    console.log('✅ Track order functionality initialized successfully!');
    console.log('===============================================');
});
</script>
@endpush
@endsection
