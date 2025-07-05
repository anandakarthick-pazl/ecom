@extends('admin.layouts.app')

@section('title', 'Stock Adjustments Report')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stock Adjustments Report</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.stock-adjustments') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="increase" {{ request('type') == 'increase' ? 'selected' : '' }}>Increase</option>
                                <option value="decrease" {{ request('type') == 'decrease' ? 'selected' : '' }}>Decrease</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.reports.stock-adjustments') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Adjustments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_adjustments']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Value Impact</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['total_value_impact'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Increase Adjustments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['increase_adjustments']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Decrease Adjustments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['decrease_adjustments']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Stock Adjustments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>Adjustment Number</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Items</th>
                            <th>Value Impact</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Approved By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adjustment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.stock-adjustments.show', $adjustment) }}" class="text-decoration-none">
                                        {{ $adjustment->adjustment_number }}
                                    </a>
                                </td>
                                <td>{{ $adjustment->adjustment_date }}</td>
                                <td>
                                    @if($adjustment->type == 'increase')
                                        <span class="badge badge-success">
                                            <i class="fas fa-arrow-up"></i> Increase
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-arrow-down"></i> Decrease
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $adjustment->reason }}</td>
                                <td>{{ $adjustment->items->count() }}</td>
                                <td>
                                    <strong class="text-{{ $adjustment->type == 'increase' ? 'success' : 'danger' }}">
                                        ₹{{ number_format($adjustment->total_adjustment_value ?? 0, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($adjustment->status) {
                                            'draft' => 'secondary',
                                            'approved' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ ucfirst($adjustment->status) }}</span>
                                </td>
                                <td>{{ $adjustment->creator->name ?? 'N/A' }}</td>
                                <td>{{ $adjustment->approver->name ?? 'Pending' }}</td>
                                <td>
                                    <a href="{{ route('admin.stock-adjustments.show', $adjustment) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No stock adjustments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($adjustments->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $adjustments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    const form = document.getElementById('filterForm');
    const exportInput = document.createElement('input');
    exportInput.type = 'hidden';
    exportInput.name = 'export';
    exportInput.value = '1';
    form.appendChild(exportInput);
    form.submit();
    form.removeChild(exportInput);
}
</script>
@endpush
@endsection
