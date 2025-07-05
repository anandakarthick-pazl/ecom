@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-primary" style="background: blue !important;">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">{{ $totalOrders }}</h3>
                    <p class="mb-0 text-white">Total Orders</p>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">₹{{ number_format($totalRevenue, 0) }}</h3>
                    <p class="mb-0 text-white">Total Revenue</p>
                </div>
                <div>
                    <i class="fas fa-rupee-sign fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">{{ $totalCustomers }}</h3>
                    <p class="mb-0 text-white">Total Customers</p>
                </div>
                <div>
                    <i class="fas fa-users fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">{{ $totalProducts }}</h3>
                    <p class="mb-0 text-white">Total Products</p>
                </div>
                <div>
                    <i class="fas fa-box fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Today's Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $todayOrders }}</h4>
                            <p class="text-muted mb-0">Orders Today</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">₹{{ number_format($todayRevenue, 0) }}</h4>
                        <p class="text-muted mb-0">Revenue Today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">This Month's Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $monthlyOrders }}</h4>
                            <p class="text-muted mb-0">Orders This Month</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">₹{{ number_format($monthlyRevenue, 0) }}</h4>
                        <p class="text-muted mb-0">Revenue This Month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $order->customer_name }}</td>
                                <td>₹{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-3">No orders yet</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Low Stock Alert</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-warning">Manage</a>
            </div>
            <div class="card-body">
                @if($lowStockProducts->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($lowStockProducts as $product)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ Str::limit($product->name, 20) }}</h6>
                            <small class="text-muted">{{ $product->category->name }}</small>
                        </div>
                        <span class="badge bg-{{ $product->stock == 0 ? 'danger' : 'warning' }}">
                            {{ $product->stock }} left
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center py-3">All products are well stocked!</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order Status Chart -->
@if($orderStatusStats->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Status Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($orderStatusStats as $status => $count)
                    <div class="col-md-2 col-sm-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-{{ 
                                $status == 'pending' ? 'warning' : 
                                ($status == 'processing' ? 'info' : 
                                ($status == 'shipped' ? 'primary' : 
                                ($status == 'delivered' ? 'success' : 'danger'))) 
                            }}">{{ $count }}</h4>
                            <p class="text-muted mb-0">{{ ucfirst($status) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
