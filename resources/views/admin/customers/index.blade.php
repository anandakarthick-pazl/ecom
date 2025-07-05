@extends('admin.layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers')

@section('page_actions')
<a href="{{ route('admin.customers.export') }}" class="btn btn-success">
    <i class="fas fa-download"></i> Export CSV
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search customers...">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="city" value="{{ request('city') }}" placeholder="Filter by city">
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Last Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $customer->name ?: 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $customer->mobile_number }}</small>
                                @if($customer->email)
                                    <br><small class="text-muted">{{ $customer->email }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($customer->city)
                                {{ $customer->city }}@if($customer->state), {{ $customer->state }}@endif
                                @if($customer->pincode)
                                    <br><small class="text-muted">{{ $customer->pincode }}</small>
                                @endif
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $customer->total_orders }}</span>
                        </td>
                        <td>
                            <strong>₹{{ number_format($customer->total_spent, 2) }}</strong>
                        </td>
                        <td>
                            @if($customer->last_order_at)
                                {{ $customer->last_order_at->format('M d, Y') }}
                                <br><small class="text-muted">{{ $customer->last_order_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <p class="small text-muted mb-0">
                    Showing <span class="fw-semibold text-primary">{{ $customers->firstItem() }}</span>
                    to <span class="fw-semibold text-primary">{{ $customers->lastItem() }}</span>
                    of <span class="fw-semibold text-primary">{{ $customers->total() }}</span>
                    customers
                </p>
            </div>
            <div class="pagination-nav">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5>No customers found</h5>
            <p class="text-muted">Customers will appear here once they start placing orders.</p>
        </div>
        @endif
    </div>
</div>
@endsection
