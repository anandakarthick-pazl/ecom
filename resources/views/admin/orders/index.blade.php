@extends('admin.layouts.app')

@section('title', 'Orders')
@section('page_title', 'Orders')

@section('page_actions')
<a href="{{ route('admin.orders.export') }}" class="btn btn-success">
    <i class="fas fa-download"></i> Export CSV
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search orders...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="payment_status">
                        <option value="">All Payments</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Payment Pending</option>
                        <option value="processing" {{ request('payment_status') == 'processing' ? 'selected' : '' }}>Payment Processing</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payment Success</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Payment Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $order->customer_name }}</strong>
                                <br><small class="text-muted">{{ $order->customer_mobile }}</small>
                                @if($order->customer_email)
                                    <br><small class="text-success"><i class="fas fa-envelope"></i> {{ $order->customer_email }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $order->items->count() }} items</span>
                        </td>
                        <td>
                            <strong>₹{{ number_format($order->total, 2) }}</strong>
                        </td>
                        <td>
                            <div class="payment-info">
                                @php
                                    $paymentIcon = match($order->payment_method) {
                                        'razorpay' => 'fas fa-credit-card',
                                        'cod' => 'fas fa-money-bill-wave',
                                        'bank_transfer' => 'fas fa-university',
                                        'upi' => 'fas fa-mobile-alt',
                                        default => 'fas fa-wallet'
                                    };
                                    
                                    $paymentStatusColor = match($order->payment_status) {
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'refunded' => 'secondary',
                                        default => 'secondary'
                                    };
                                    
                                    $paymentStatusText = match($order->payment_status) {
                                        'paid' => 'Success',
                                        'failed' => 'Failed',
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'refunded' => 'Refunded',
                                        default => 'Unknown'
                                    };
                                @endphp
                                
                                <div class="d-flex align-items-center mb-1">
                                    <i class="{{ $paymentIcon }} text-primary me-1"></i>
                                    <small>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</small>
                                </div>
                                
                                <span class="badge bg-{{ $paymentStatusColor }} d-flex align-items-center">
                                    @if($order->payment_status === 'paid')
                                        <i class="fas fa-check-circle me-1"></i>
                                    @elseif($order->payment_status === 'failed')
                                        <i class="fas fa-times-circle me-1"></i>
                                    @elseif($order->payment_status === 'processing')
                                        <i class="fas fa-clock me-1"></i>
                                    @else
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                    @endif
                                    {{ $paymentStatusText }}
                                </span>
                                
                                @if($order->payment_status === 'failed' && $order->payment_details)
                                    @php
                                        $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
                                    @endphp
                                    @if(isset($details['error']))
                                        <br><small class="text-danger">
                                            <i class="fas fa-info-circle"></i> 
                                            {{ Str::limit($details['error'], 30) }}
                                        </small>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status_color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            {{ $order->created_at->format('M d, Y') }}
                            <br><small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                {{-- <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a> --}}
                                {{-- @if($order->customer_email)
                                    <form action="{{ route('admin.orders.send-invoice', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Send Invoice Email"
                                                onclick="return confirm('Send invoice PDF to {{ $order->customer_email }}?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif --}}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        {{-- <div class="pagination-container">
            <div class="pagination-info">
                <p class="small text-muted mb-0">
                    Showing <span class="fw-semibold text-primary">{{ $orders->firstItem() }}</span>
                    to <span class="fw-semibold text-primary">{{ $orders->lastItem() }}</span>
                    of <span class="fw-semibold text-primary">{{ $orders->total() }}</span>
                    orders
                </p>
            </div>
            <div class="pagination-nav">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div> --}}
        @else
        <div class="text-center py-4">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h5>No orders found</h5>
            <p class="text-muted">Orders will appear here once customers start placing them.</p>
        </div>
        @endif
    </div>
</div>

<!-- Payment Status Summary Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ \App\Models\Order::where('payment_status', 'paid')->count() }}</h4>
                <p class="mb-0">Successful Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ \App\Models\Order::where('payment_status', 'pending')->count() }}</h4>
                <p class="mb-0">Pending Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4>{{ \App\Models\Order::where('payment_status', 'failed')->count() }}</h4>
                <p class="mb-0">Failed Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ \App\Models\Order::where('payment_status', 'processing')->count() }}</h4>
                <p class="mb-0">Processing Payments</p>
            </div>
        </div>
    </div>
</div>

<style>
.payment-info {
    min-width: 120px;
}
.badge {
    font-size: 0.7rem;
}
</style>
@endsection