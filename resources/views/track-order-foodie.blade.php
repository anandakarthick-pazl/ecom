@extends('layouts.app-foodie')

@section('title', 'Track Your Order - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', 'Track your crackers order status and delivery information.')

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Track Your Order</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Enter your details to track your order status</p>
        </div>
    </div>
</section>

<!-- Track Order Form/Results -->
<section style="padding: 60px 0;">
    <div class="container">
        @if(!isset($orders))
        <!-- Track Order Form -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm);">
                    <form method="POST" action="{{ route('track.order') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="mobile_number" style="font-weight: 600; margin-bottom: 0.5rem;">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: var(--background); border: 1px solid var(--border);">+91</span>
                                <input type="text" 
                                       class="form-control @error('mobile_number') is-invalid @enderror" 
                                       id="mobile_number" 
                                       name="mobile_number" 
                                       placeholder="Enter 10-digit mobile number"
                                       pattern="[0-9]{10}"
                                       maxlength="10"
                                       required
                                       style="border: 1px solid var(--border); padding: 12px;">
                            </div>
                            @error('mobile_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="order_number" style="font-weight: 600; margin-bottom: 0.5rem;">Order Number (Optional)</label>
                            <input type="text" 
                                   class="form-control @error('order_number') is-invalid @enderror" 
                                   id="order_number" 
                                   name="order_number" 
                                   placeholder="e.g., ORD-XXXXXX"
                                   style="border: 1px solid var(--border); padding: 12px;">
                            <small style="color: var(--text-secondary);">Leave empty to see all orders for your mobile number</small>
                            @error('order_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn-foodie btn-foodie-primary w-100">
                            <i class="fas fa-search me-2"></i>Track Order
                        </button>
                    </form>
                    
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                        <h5 style="font-weight: 600; margin-bottom: 1rem;">How it works:</h5>
                        <ul style="color: var(--text-secondary); padding-left: 1.5rem;">
                            <li>Enter your registered mobile number</li>
                            <li>Optionally add your order number for specific order</li>
                            <li>Click track order to see your order status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Order Results -->
        <div class="row">
            <div class="col-12">
                @if($orders->count() > 0)
                <h3 style="font-weight: 600; margin-bottom: 2rem;">Your Orders</h3>
                @foreach($orders as $order)
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 style="font-weight: 600; color: var(--text-primary);">Order #{{ $order->order_number }}</h4>
                                    <p style="color: var(--text-secondary); margin-bottom: 0.5rem;">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}" style="padding: 8px 16px; font-size: 0.9rem;">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            
                            <!-- Order Items -->
                            <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-bottom: 1rem;">
                                <h6 style="font-weight: 600; margin-bottom: 1rem;">Order Items</h6>
                                @foreach($order->items as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span>{{ $item->product->name ?? 'Product' }}</span>
                                        <span style="color: var(--text-secondary);"> x {{ $item->quantity }}</span>
                                    </div>
                                    <span style="font-weight: 600;">₹{{ $item->price * $item->quantity }}</span>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Delivery Details -->
                            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                                <h6 style="font-weight: 600; margin-bottom: 0.5rem;">Delivery Address</h6>
                                <p style="color: var(--text-secondary); margin: 0;">
                                    {{ $order->delivery_address }}, {{ $order->city }}<br>
                                    {{ $order->state }} - {{ $order->pincode }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div style="background: var(--background); padding: 1.5rem; border-radius: 12px;">
                                <h6 style="font-weight: 600; margin-bottom: 1rem;">Order Summary</h6>
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
                                    <span>Delivery</span>
                                    <span>₹{{ $order->delivery_charge }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span style="font-weight: 600; font-size: 1.1rem;">Total</span>
                                    <span style="font-weight: 600; font-size: 1.1rem; color: var(--primary-color);">₹{{ $order->total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <div class="text-center">
                    <a href="{{ route('track.order') }}" class="btn-foodie btn-foodie-outline">
                        Track Another Order
                    </a>
                </div>
                @else
                <div class="text-center py-5">
                    <div style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;">📦</div>
                    <h3 style="color: var(--text-primary); margin-bottom: 1rem;">No orders found</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">We couldn't find any orders with the provided details</p>
                    <a href="{{ route('track.order') }}" class="btn-foodie btn-foodie-primary">
                        Try Again
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</section>

@endsection
