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
                <div class="col-md-3">
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
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-3">
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
                                <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-outline-secondary" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($order->customer_email)
                                    <form action="{{ route('admin.orders.send-invoice', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Send Invoice Email"
                                                onclick="return confirm('Send invoice PDF to {{ $order->customer_email }}?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h5>No orders found</h5>
            <p class="text-muted">Orders will appear here once customers start placing them.</p>
        </div>
        @endif
    </div>
</div>
@endsection
