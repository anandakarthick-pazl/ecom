@extends('admin.layouts.app')

@section('title', 'Customer: ' . ($customer->name ?: $customer->mobile_number))
@section('page_title', 'Customer: ' . ($customer->name ?: $customer->mobile_number))

@section('page_actions')
<a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Customers
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Customer Details -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Customer Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>{{ $customer->name ?: 'N/A' }}</h5>
                        <p class="mb-1"><strong>Mobile:</strong> {{ $customer->mobile_number }}</p>
                        @if($customer->email)
                            <p class="mb-1"><strong>Email:</strong> {{ $customer->email }}</p>
                        @endif
                        <p class="mb-1"><strong>Customer Since:</strong> {{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                    
                    <div class="col-md-6">
                        @if($customer->address)
                            <h6>Address</h6>
                            <address>
                                {{ $customer->address }}<br>
                                @if($customer->city){{ $customer->city }}@endif
                                @if($customer->state), {{ $customer->state }}@endif
                                @if($customer->pincode) {{ $customer->pincode }}@endif
                            </address>
                        @else
                            <p class="text-muted">No address provided</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order History -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Order History</h6>
                <span class="badge bg-primary">{{ $customer->orders->count() }} orders</span>
            </div>
            <div class="card-body">
                @if($customer->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>
                                    {{ $order->created_at->format('M d, Y') }}
                                    <br><small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $order->items->count() }} items</span>
                                </td>
                                <td>
                                    <strong>₹{{ number_format($order->total, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                    <p class="text-muted">No orders yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Customer Statistics -->
        <div class="card">
            <div class="card-body">
                <h6>Customer Statistics</h6>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $customer->total_orders }}</h4>
                        <p class="text-muted mb-0">Total Orders</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">₹{{ number_format($customer->total_spent, 0) }}</h4>
                        <p class="text-muted mb-0">Total Spent</p>
                    </div>
                </div>
                
                <hr>
                
                @if($customer->total_orders > 0)
                    <p><strong>Average Order Value:</strong> ₹{{ number_format($customer->total_spent / $customer->total_orders, 2) }}</p>
                @endif
                
                @if($customer->last_order_at)
                    <p><strong>Last Order:</strong> {{ $customer->last_order_at->diffForHumans() }}</p>
                @endif
                
                <p><strong>Customer Type:</strong>
                    @if($customer->total_orders == 0)
                        <span class="badge bg-secondary">New Customer</span>
                    @elseif($customer->total_orders == 1)
                        <span class="badge bg-info">First-time Buyer</span>
                    @elseif($customer->total_orders <= 5)
                        <span class="badge bg-primary">Regular Customer</span>
                    @else
                        <span class="badge bg-success">Loyal Customer</span>
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Recent Activity -->
        @if($customer->orders->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h6>Recent Activity</h6>
                @foreach($customer->orders->take(5) as $order)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <small><strong>{{ $order->order_number }}</strong></small>
                            <br><small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                        </div>
                        <div class="text-end">
                            <small class="text-success">₹{{ number_format($order->total, 2) }}</small>
                            <br><span class="badge bg-{{ $order->status_color }} badge-sm">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Customer Insights -->
        @if($customer->orders->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h6>Shopping Insights</h6>
                
                @php
                    $categoryPurchases = $customer->orders->load('items.product.category')
                        ->flatMap->items
                        ->groupBy('product.category.name')
                        ->map->count()
                        ->sortDesc()
                        ->take(3);
                @endphp
                
                @if($categoryPurchases->count() > 0)
                    <p><strong>Favorite Categories:</strong></p>
                    <ul class="list-unstyled">
                        @foreach($categoryPurchases as $category => $count)
                            <li><small>{{ $category }} ({{ $count }} items)</small></li>
                        @endforeach
                    </ul>
                @endif
                
                <p><strong>Status Distribution:</strong></p>
                @php
                    $statusCounts = $customer->orders->groupBy('status')->map->count();
                @endphp
                <ul class="list-unstyled">
                    @foreach($statusCounts as $status => $count)
                        <li><small>{{ ucfirst($status) }}: {{ $count }} orders</small></li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
