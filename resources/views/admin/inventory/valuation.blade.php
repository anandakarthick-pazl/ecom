@extends('admin.layouts.app')

@section('title', 'Stock Valuation')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stock Valuation Report</h1>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Valuation Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Cost Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalCostValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Selling Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($totalSellingValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Potential Profit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($totalPotentialProfit, 2) }}
                                @if($totalCostValue > 0)
                                    <small class="text-muted">({{ number_format(($totalPotentialProfit / $totalCostValue) * 100, 1) }}%)</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Valuation Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Product-wise Stock Valuation</h6>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Stock Qty</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Cost Value</th>
                                <th>Selling Value</th>
                                <th>Potential Profit</th>
                                <th>Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $item)
                                @php
                                    $marginPercent = $item['cost_value'] > 0 ? (($item['potential_profit'] / $item['cost_value']) * 100) : 0;
                                    $marginClass = $marginPercent >= 50 ? 'success' : ($marginPercent >= 25 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $item['product']->name }}</strong>
                                            @if($item['product']->barcode)
                                                <br><small class="text-muted">{{ $item['product']->barcode }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $item['product']->category->name ?? 'Uncategorized' }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ $item['product']->stock }}</span>
                                    </td>
                                    <td>₹{{ number_format($item['product']->cost_price, 2) }}</td>
                                    <td>₹{{ number_format($item['product']->price, 2) }}</td>
                                    <td>
                                        <span class="font-weight-bold text-primary">
                                            ₹{{ number_format($item['cost_value'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-success">
                                            ₹{{ number_format($item['selling_value'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-{{ $item['potential_profit'] >= 0 ? 'success' : 'danger' }}">
                                            ₹{{ number_format($item['potential_profit'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $marginClass }}">
                                            {{ number_format($marginPercent, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5>No Stock Found</h5>
                    <p class="text-muted">No products with stock available for valuation.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Insights -->
    @if($products->count() > 0)
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Valuation Insights</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Average Margin:</strong> 
                            {{ $totalCostValue > 0 ? number_format(($totalPotentialProfit / $totalCostValue) * 100, 1) : 0 }}%
                        </li>
                        <li class="mb-2">
                            <strong>Total Products:</strong> {{ $products->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>High Margin Products (>50%):</strong> 
                            {{ $products->filter(function($item) { return $item['cost_value'] > 0 && (($item['potential_profit'] / $item['cost_value']) * 100) > 50; })->count() }}
                        </li>
                        <li class="mb-2">
                            <strong>Low Margin Products (<25%):</strong> 
                            {{ $products->filter(function($item) { return $item['cost_value'] > 0 && (($item['potential_profit'] / $item['cost_value']) * 100) < 25; })->count() }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Value Products</h6>
                </div>
                <div class="card-body">
                    @php
                        $topProducts = $products->sortByDesc('selling_value')->take(5);
                    @endphp
                    <ul class="list-unstyled mb-0">
                        @foreach($topProducts as $item)
                        <li class="mb-2">
                            <strong>{{ $item['product']->name }}</strong><br>
                            <small class="text-muted">
                                Stock: {{ $item['product']->stock }} | 
                                Value: ₹{{ number_format($item['selling_value'], 0) }}
                            </small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function exportToExcel() {
    // Create a temporary table for export
    const table = document.getElementById('dataTable').cloneNode(true);
    const workbook = XLSX.utils.table_to_book(table);
    XLSX.writeFile(workbook, 'stock-valuation-report.xlsx');
}

// Include XLSX library for client-side Excel export
const script = document.createElement('script');
script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
document.head.appendChild(script);
</script>
@endpush
@endsection
