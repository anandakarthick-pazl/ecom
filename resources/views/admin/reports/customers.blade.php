@extends('admin.layouts.app')

@section('title', 'Customer Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Customer Reports</h1>
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
            <form method="GET" action="{{ route('admin.reports.customers') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" 
                                   placeholder="Search by name, email, or phone">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.customers') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Customer Analytics</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>POS Sales</th>
                            <th>Online Spent</th>
                            <th>POS Spent</th>
                            <th>Total Spent</th>
                            <th>Avg Order Value</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $totalSpent = ($customer->total_online_spent ?? 0) + ($customer->total_pos_spent ?? 0);
                                $totalOrders = ($customer->orders_count ?? 0) + ($customer->pos_sales_count ?? 0);
                                $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $customer->name }}</strong>
                                        @if($customer->city)
                                            <br><small class="text-muted">{{ $customer->city }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>{{ $customer->mobile_number ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $customer->orders_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $customer->pos_sales_count ?? 0 }}</span>
                                </td>
                                <td>₹{{ number_format($customer->total_online_spent ?? 0, 2) }}</td>
                                <td>₹{{ number_format($customer->total_pos_spent ?? 0, 2) }}</td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($totalSpent, 2) }}</strong>
                                </td>
                                <td>₹{{ number_format($avgOrderValue, 2) }}</td>
                                <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No customers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($customers->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $customers->appends(request()->query())->links() }}
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
