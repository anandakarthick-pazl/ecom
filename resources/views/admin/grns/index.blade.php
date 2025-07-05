@extends('admin.layouts.app')

@section('title', 'Goods Receipt Notes')
@section('page_title', 'GRN Management')

@section('page_actions')
<a href="{{ route('admin.grns.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Create GRN
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.grns.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search GRNs...">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.grns.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($grns->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>GRN Number</th>
                        <th>Purchase Order</th>
                        <th>Supplier</th>
                        <th>Received Date</th>
                        <th>Invoice Details</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grns as $grn)
                    <tr>
                        <td>
                            <strong>{{ $grn->grn_number }}</strong>
                            <br><small class="text-muted">{{ $grn->items->count() }} items</small>
                        </td>
                        <td>
                            @if($grn->purchaseOrder)
                                <a href="{{ route('admin.purchase-orders.show', $grn->purchaseOrder) }}" class="text-decoration-none">
                                    <strong>{{ $grn->purchaseOrder->po_number }}</strong>
                                </a>
                                <br><small class="text-muted">{{ $grn->purchaseOrder->po_date->format('M d, Y') }}</small>
                            @else
                                <span class="text-muted">No PO</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $grn->supplier->display_name }}</strong>
                            <br><small class="text-muted">{{ $grn->supplier->city }}</small>
                        </td>
                        <td>
                            {{ $grn->received_date->format('M d, Y') }}
                            <br><small class="text-muted">{{ $grn->received_date->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if($grn->invoice_number)
                                <strong>{{ $grn->invoice_number }}</strong>
                                @if($grn->invoice_date)
                                    <br><small class="text-muted">{{ $grn->invoice_date->format('M d, Y') }}</small>
                                @endif
                                @if($grn->invoice_amount)
                                    <br><small class="text-info">₹{{ number_format($grn->invoice_amount, 2) }}</small>
                                @endif
                            @else
                                <span class="text-muted">No invoice</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $grn->status_color }}">
                                {{ ucfirst($grn->status) }}
                            </span>
                            @if($grn->receiver)
                                <br><small class="text-muted">By {{ $grn->receiver->name }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.grns.show', $grn) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($grn->status !== 'completed')
                                    <a href="{{ route('admin.grns.edit', $grn) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $grns->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
            <h5>No goods receipt notes found</h5>
            <p class="text-muted">Create your first GRN to record goods received from suppliers.</p>
            <a href="{{ route('admin.grns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create GRN
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
