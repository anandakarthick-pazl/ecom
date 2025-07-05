@extends('admin.layouts.app')

@section('title', 'Purchase Orders')
@section('page_title', 'Purchase Order Management')

@section('page_actions')
<a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Create Purchase Order
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.purchase-orders.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="supplier_id">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($purchaseOrders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>PO Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrders as $po)
                    <tr>
                        <td>
                            <strong>{{ $po->po_number }}</strong>
                            <br><small class="text-muted">{{ $po->items->count() }} items</small>
                        </td>
                        <td>
                            <strong>{{ $po->supplier->display_name }}</strong>
                            <br><small class="text-muted">{{ $po->supplier->city }}</small>
                        </td>
                        <td>
                            {{ $po->po_date->format('M d, Y') }}
                            <br><small class="text-muted">{{ $po->po_date->diffForHumans() }}</small>
                        </td>
                        <td>
                            <strong>₹{{ number_format($po->total_amount, 2) }}</strong>
                            @if($po->discount > 0)
                                <br><small class="text-success">-₹{{ number_format($po->discount, 2) }} discount</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $po->status_color }}">
                                {{ ucfirst($po->status) }}
                            </span>
                            @if($po->status === 'received')
                                <br><small class="text-success">{{ $po->total_received_quantity }}/{{ $po->total_ordered_quantity }} received</small>
                            @endif
                        </td>
                        <td>
                            @if($po->expected_delivery_date)
                                {{ $po->expected_delivery_date->format('M d, Y') }}
                                @if($po->expected_delivery_date < today() && $po->status !== 'received')
                                    <br><span class="badge bg-warning">Overdue</span>
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($po->status === 'draft')
                                    <a href="{{ route('admin.purchase-orders.edit', $po) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($po->status === 'draft')
                                            <li>
                                                <form action="{{ route('admin.purchase-orders.update-status', $po) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="sent">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-paper-plane"></i> Send to Supplier
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if($po->status === 'sent')
                                            <li>
                                                <form action="{{ route('admin.purchase-orders.update-status', $po) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check"></i> Mark Approved
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if(in_array($po->status, ['draft', 'sent']))
                                            <li>
                                                <form action="{{ route('admin.purchase-orders.update-status', $po) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $purchaseOrders->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
            <h5>No purchase orders found</h5>
            <p class="text-muted">Create your first purchase order to start procurement.</p>
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Purchase Order
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
