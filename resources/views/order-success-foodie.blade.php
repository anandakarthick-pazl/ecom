@extends('layouts.app-foodie')

@section('title', 'Order Successful - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', 'Your order has been successfully placed.')

@section('content')

<!-- Success Message -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div style="background: white; border-radius: 16px; padding: 3rem; box-shadow: var(--shadow-sm); text-align: center;">
                    <!-- Success Icon -->
                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; animation: scaleIn 0.5s ease;">
                        <i class="fas fa-check" style="font-size: 3rem; color: white;"></i>
                    </div>
                    
                    <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Order Placed Successfully!</h1>
                    <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">
                        Thank you for your order. We've received your order and will process it soon.
                    </p>
                    
                    <!-- Order Number -->
                    <div style="background: var(--background); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                        <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Order Number</p>
                        <h3 style="font-weight: 700; color: var(--primary-color); margin: 0;">{{ $order->order_number }}</h3>
                    </div>
                    
                    <!-- Order Details -->
                    <div style="text-align: left; margin-bottom: 2rem;">
                        <h4 style="font-weight: 600; margin-bottom: 1.5rem;">Order Details</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Customer Name</p>
                                <p style="font-weight: 600;">{{ $order->customer_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Mobile Number</p>
                                <p style="font-weight: 600;">{{ $order->customer_mobile }}</p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Delivery Address</p>
                            <p style="font-weight: 600;">
                                {{ $order->delivery_address }}<br>
                                {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">Payment Method</p>
                            <p style="font-weight: 600;">{{ $order->payment_method }}</p>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div style="text-align: left; margin-bottom: 2rem;">
                        <h4 style="font-weight: 600; margin-bottom: 1rem;">Order Items</h4>
                        <div style="border: 1px solid var(--border); border-radius: 12px; padding: 1rem;">
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div>
                                    <p style="font-weight: 500; margin: 0;">{{ $item->product->name ?? 'Product' }}</p>
                                    <small style="color: var(--text-secondary);">Qty: {{ $item->quantity }} × ₹{{ $item->price }}</small>
                                </div>
                                <span style="font-weight: 600;">₹{{ $item->total }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div style="background: var(--background); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₹{{ $order->subtotal }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--success-color);">
                            <span>Discount</span>
                            <span>-₹{{ $order->discount }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Charge</span>
                            <span>₹{{ $order->delivery_charge }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span style="font-weight: 700; font-size: 1.1rem;">Total Amount</span>
                            <span style="font-weight: 700; font-size: 1.1rem; color: var(--primary-color);">₹{{ $order->total }}</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('track.order') }}" class="btn-foodie btn-foodie-outline">
                            <i class="fas fa-truck me-2"></i>Track Order
                        </a>
                        <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Show celebration animation
    document.addEventListener('DOMContentLoaded', function() {
        // You can add confetti or other celebration effects here
        console.log('Order placed successfully!');
    });
</script>
@endpush
