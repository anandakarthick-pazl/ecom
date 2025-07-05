@extends('admin.layouts.app')

@section('title', 'Product Performance Report')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Performance Report</h1>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
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
            <form method="GET" action="{{ route('admin.reports.products') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.products') }}" class="btn btn-secondary">
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
            <h6 class="m-0 font-weight-bold text-primary">Product Performance Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Total Sold</th>
                            <th>Stock Value</th>
                            <th>Potential Revenue</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $item)
                            @php
                                $product = $item['product'];
                                $performance = $item['total_sold'] > 0 ? 'High' : ($product->stock > 0 ? 'Medium' : 'Low');
                                $performanceClass = match($performance) {
                                    'High' => 'success',
                                    'Medium' => 'warning',
                                    'Low' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $index < 3 ? 'gold' : 'secondary' }}">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <br><small class="text-muted">₹{{ number_format($product->price, 2) }}</small>
                                    </div>
                                </td>
                                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td>
                                    <span class="badge badge-{{ $product->stock > 0 ? 'info' : 'danger' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $item['total_sold'] }}</strong>
                                </td>
                                <td>₹{{ number_format($item['stock_value'], 2) }}</td>
                                <td>₹{{ number_format($item['potential_revenue'], 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $performanceClass }}">{{ $performance }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No products found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    @if($products->count() > 0)
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performers</h6>
                </div>
                <div class="card-body">
                    @php
                        $topPerformers = $products->take(5);
                    @endphp
                    <ul class="list-unstyled mb-0">
                        @foreach($topPerformers as $item)
                        <li class="mb-2">
                            <strong>{{ $item['product']->name }}</strong><br>
                            <small class="text-muted">
                                Sold: {{ $item['total_sold'] }} | 
                                Revenue Potential: ₹{{ number_format($item['potential_revenue'], 0) }}
                            </small>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Summary</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalProducts = $products->count();
                        $highPerformers = $products->filter(function($item) { return $item['total_sold'] > 10; })->count();
                        $zeroSales = $products->filter(function($item) { return $item['total_sold'] == 0; })->count();
                        $totalStockValue = $products->sum('stock_value');
                        $totalRevenuePotential = $products->sum('potential_revenue');
                    @endphp
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Total Products:</strong> {{ $totalProducts }}
                        </li>
                        <li class="mb-2">
                            <strong>High Performers (10+ sales):</strong> {{ $highPerformers }} ({{ $totalProducts > 0 ? number_format(($highPerformers / $totalProducts) * 100, 1) : 0 }}%)
                        </li>
                        <li class="mb-2">
                            <strong>Zero Sales:</strong> {{ $zeroSales }} ({{ $totalProducts > 0 ? number_format(($zeroSales / $totalProducts) * 100, 1) : 0 }}%)
                        </li>
                        <li class="mb-2">
                            <strong>Total Stock Value:</strong> ₹{{ number_format($totalStockValue, 0) }}
                        </li>
                        <li class="mb-2">
                            <strong>Revenue Potential:</strong> ₹{{ number_format($totalRevenuePotential, 0) }}
                        </li>
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
    XLSX.writeFile(workbook, 'product-performance-report.xlsx');
}

// Include XLSX library for client-side Excel export
const script = document.createElement('script');
script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
document.head.appendChild(script);
</script>

<style>
.badge-gold {
    background-color: #ffd700 !important;
    color: #000 !important;
}
</style>
@endpush
@endsection
