@extends('admin.layouts.app')

@section('title', 'Income & Loss Report')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Income & Loss Report</h1>
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
            <h6 class="m-0 font-weight-bold text-primary">Date Range</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.income') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from', $data['period']['from']) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to', $data['period']['to']) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Period Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-calendar"></i> Report Period</h5>
                <p class="mb-0">
                    <strong>From:</strong> {{ \Carbon\Carbon::parse($data['period']['from'])->format('d M Y') }} 
                    <strong>To:</strong> {{ \Carbon\Carbon::parse($data['period']['to'])->format('d M Y') }}
                    ({{ \Carbon\Carbon::parse($data['period']['from'])->diffInDays(\Carbon\Carbon::parse($data['period']['to'])) + 1 }} days)
                </p>
            </div>
        </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Income</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($data['income']['total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Expenses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($data['expenses']['total'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $data['net_profit'] >= 0 ? 'success' : 'danger' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $data['net_profit'] >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">Net {{ $data['net_profit'] >= 0 ? 'Profit' : 'Loss' }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format(abs($data['net_profit']), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Margin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['profit_margin'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Income Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-cash-register text-primary"></i> POS Sales</td>
                                    <td>₹{{ number_format($data['income']['pos_sales'], 2) }}</td>
                                    <td>{{ $data['income']['total'] > 0 ? number_format(($data['income']['pos_sales'] / $data['income']['total']) * 100, 1) : 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-shopping-cart text-success"></i> Online Sales</td>
                                    <td>₹{{ number_format($data['income']['online_sales'], 2) }}</td>
                                    <td>{{ $data['income']['total'] > 0 ? number_format(($data['income']['online_sales'] / $data['income']['total']) * 100, 1) : 0 }}%</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Total Income</td>
                                    <td>₹{{ number_format($data['income']['total'], 2) }}</td>
                                    <td>100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expense Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fas fa-shopping-bag text-info"></i> Purchases</td>
                                    <td>₹{{ number_format($data['expenses']['purchases'], 2) }}</td>
                                    <td>{{ $data['expenses']['total'] > 0 ? number_format(($data['expenses']['purchases'] / $data['expenses']['total']) * 100, 1) : 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-exclamation-triangle text-warning"></i> Stock Loss</td>
                                    <td>₹{{ number_format($data['expenses']['stock_loss'], 2) }}</td>
                                    <td>{{ $data['expenses']['total'] > 0 ? number_format(($data['expenses']['stock_loss'] / $data['expenses']['total']) * 100, 1) : 0 }}%</td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Total Expenses</td>
                                    <td>₹{{ number_format($data['expenses']['total'], 2) }}</td>
                                    <td>100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit/Loss Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Summary</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-lg">
                            <tbody>
                                <tr class="bg-light">
                                    <td><strong>Total Income</strong></td>
                                    <td class="text-right"><strong class="text-success">₹{{ number_format($data['income']['total'], 2) }}</strong></td>
                                </tr>
                                <tr class="bg-light">
                                    <td><strong>Total Expenses</strong></td>
                                    <td class="text-right"><strong class="text-danger">₹{{ number_format($data['expenses']['total'], 2) }}</strong></td>
                                </tr>
                                <tr class="{{ $data['net_profit'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                    <td><strong>Net {{ $data['net_profit'] >= 0 ? 'Profit' : 'Loss' }}</strong></td>
                                    <td class="text-right"><strong>₹{{ number_format(abs($data['net_profit']), 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Profit Margin</strong></td>
                                    <td class="text-right"><strong>{{ number_format($data['profit_margin'], 2) }}%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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
