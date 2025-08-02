@extends('admin.layouts.app')

@section('title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sales Reports</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <a href="{{ route('admin.commissions.index') }}" class="btn btn-info btn-sm">
                <i class="fas fa-percentage"></i> Manage Commissions
            </a>
            {{-- @if(config('app.debug'))
                <a href="{{ route('admin.reports.test-excel-export') }}" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-flask"></i> Test Export
                </a>
            @endif --}}
        </div>

    <!-- Top Commission Performers -->
    @if(isset($commissionStats['top_performers']) && $commissionStats['top_performers']->count() > 0)
    {{-- <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top Commission Performers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Reference Name</th>
                            <th>Total Commission</th>
                            <th>Transaction Count</th>
                            <th>Average Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissionStats['top_performers'] as $performer)
                            <tr>
                                <td><strong>{{ $performer['name'] }}</strong></td>
                                <td class="text-success">₹{{ number_format($performer['total_commission'], 2) }}</td>
                                <td>{{ $performer['count'] }}</td>
                                <td>₹{{ number_format($performer['avg_commission'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}
    @endif
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.sales') }}" id="filterForm">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">All Methods</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="upi" {{ request('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="mixed" {{ request('payment_method') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Commission Status</label>
                            <select name="commission_status" class="form-control">
                                <option value="">All Sales</option>
                                <option value="with_commission" {{ request('commission_status') == 'with_commission' ? 'selected' : '' }}>With Commission</option>
                                <option value="without_commission" {{ request('commission_status') == 'without_commission' ? 'selected' : '' }}>Without Commission</option>
                                <option value="pending" {{ request('commission_status') == 'pending' ? 'selected' : '' }}>Pending Commission</option>
                                <option value="paid" {{ request('commission_status') == 'paid' ? 'selected' : '' }}>Paid Commission</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">POS Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['pos_sales_count'] }}</div>
                            <div class="text-xs text-gray-500">₹{{ number_format($summary['pos_sales_total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Online Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['online_sales_count'] }}</div>
                            <div class="text-xs text-gray-500">₹{{ number_format($summary['online_sales_total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['total_sales'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cash Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['cash_sales'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['total_commission_amount'] ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $summary['total_commission_count'] ?? 0 }} records</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['pending_commission_amount'] ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $summary['pending_commission_count'] ?? 0 }} pending</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($summary['paid_commission_amount'] ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $summary['paid_commission_count'] ?? 0 }} paid</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Commission Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($summary['total_sales'] > 0 && ($summary['total_commission_amount'] ?? 0) > 0)
                                    {{ number_format((($summary['total_commission_amount'] ?? 0) / $summary['total_sales']) * 100, 2) }}%
                                @else
                                    0.00%
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">of total sales</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- POS Sales Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">POS Sales</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Payment Method</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Commission</th>
                    <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($posSales as $sale)
                <tr>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->sale_date }}</td>
                <td>{{ $sale->customer_name ?? 'Walk-in' }}</td>
                <td>{{ $sale->items->count() }}</td>
                <td>
                    <span class="badge badge-secondary">{{ ucfirst($sale->payment_method) }}</span>
                </td>
                <td>₹{{ number_format($sale->subtotal, 2) }}</td>
                <td>₹{{ number_format($sale->tax_amount, 2) }}</td>
                <td><strong>₹{{ number_format($sale->total_amount, 2) }}</strong></td>
                <td>
                    @if($sale->commission)
                            <div class="text-success">
                                    <strong>₹{{ number_format($sale->commission->commission_amount, 2) }}</strong><br>
                                <small>{{ $sale->commission->reference_name }}</small><br>
                            <span class="badge badge-{{ $sale->commission->status_color }} badge-sm">
                                    {{ $sale->commission->status_text }}
                                    </span>
                                    </div>
                                    @else
                                            <span class="text-muted">No Commission</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ ucfirst($sale->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No POS sales found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
        </div>
    </div>

    <!-- Online Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Online Orders</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Delivery</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($onlineOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->items->count() }}</td>
                                <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                <td>₹{{ number_format($order->delivery_charge, 2) }}</td>
                                <td>₹{{ number_format($order->discount, 2) }}</td>
                                <td><strong>₹{{ number_format($order->total, 2) }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $order->status == 'delivered' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No online orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
