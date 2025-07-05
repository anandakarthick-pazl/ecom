@extends('admin.layouts.app')

@section('title', 'Stock Adjustments')
@section('page_title', 'Stock Adjustment Management')

@section('page_actions')
<a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Create Adjustment
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.stock-adjustments.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="increase" {{ request('type') == 'increase' ? 'selected' : '' }}>Increase</option>
                        <option value="decrease" {{ request('type') == 'decrease' ? 'selected' : '' }}>Decrease</option>
                        <option value="recount" {{ request('type') == 'recount' ? 'selected' : '' }}>Recount</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($adjustments->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Adjustment #</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Items</th>
                        <th>Value Impact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($adjustments as $adjustment)
                    <tr>
                        <td>
                            <strong>{{ $adjustment->adjustment_number }}</strong>
                            <br><small class="text-muted">by {{ $adjustment->creator->name }}</small>
                        </td>
                        <td>
                            {{ $adjustment->adjustment_date->format('M d, Y') }}
                            <br><small class="text-muted">{{ $adjustment->adjustment_date->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $adjustment->type === 'increase' ? 'success' : ($adjustment->type === 'decrease' ? 'danger' : 'info') }}">
                                {{ ucfirst($adjustment->type) }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $adjustment->reason }}</strong>
                            @if($adjustment->notes)
                                <br><small class="text-muted">{{ Str::limit($adjustment->notes, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $adjustment->items->count() }} items
                            <br><small class="text-muted">
                                Total Qty: {{ $adjustment->items->sum('adjusted_quantity') }}
                            </small>
                        </td>
                        <td>
                            <strong>₹{{ number_format($adjustment->total_adjustment_value, 2) }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $adjustment->status_color }}">
                                {{ ucfirst($adjustment->status) }}
                            </span>
                            @if($adjustment->approved_at)
                                <br><small class="text-muted">
                                    Approved {{ $adjustment->approved_at->diffForHumans() }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.stock-adjustments.show', $adjustment) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($adjustment->status === 'draft')
                                    <a href="{{ route('admin.stock-adjustments.edit', $adjustment) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.stock-adjustments.approve', $adjustment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success" title="Approve" onclick="return confirm('Are you sure you want to approve this adjustment? This will update the stock levels.')">
                                            <i class="fas fa-check"></i>
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
            {{ $adjustments->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
            <h5>No stock adjustments found</h5>
            <p class="text-muted">Create your first stock adjustment to manage inventory levels.</p>
            <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Adjustment
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
