@extends('admin.layouts.app')

@section('title', 'Inventory Management')
@section('page_title', 'Inventory Management')

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-warning">
        <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
    </a>
    <a href="{{ route('admin.inventory.valuation') }}" class="btn btn-info">
        <i class="fas fa-calculator"></i> Stock Valuation
    </a>
    <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Stock Adjustment
    </a>
</div>
@endsection

@section('content')
<!-- Stock Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title">Total Products</h5>
                        <h2 class="mb-0">{{ number_format($stockSummary['total_products']) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title">In Stock</h5>
                        <h2 class="mb-0">{{ number_format($stockSummary['in_stock']) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title">Low Stock</h5>
                        <h2 class="mb-0">{{ number_format($stockSummary['low_stock']) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="card-title">Out of Stock</h5>
                        <h2 class="mb-0">{{ number_format($stockSummary['out_of_stock']) }}</h2>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Stock Value -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <h4>Total Stock Value</h4>
                <h2 class="text-success">₹{{ number_format($stockSummary['total_value'], 2) }}</h2>
                <small class="text-muted">Based on cost price</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.inventory.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <select class="form-select" name="stock_status">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                           placeholder="Search by product name or barcode">
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

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Low Stock Threshold</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->barcode)
                                        <br><small class="text-muted">{{ $product->barcode }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td>
                            <span class="badge bg-{{ $product->stock > $product->low_stock_threshold ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>{{ $product->low_stock_threshold }}</td>
                        <td>₹{{ number_format($product->cost_price, 2) }}</td>
                        <td>₹{{ number_format($product->price, 2) }}</td>
                        <td>₹{{ number_format($product->stock * $product->cost_price, 2) }}</td>
                        <td>
                            @if($product->stock <= 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->stock <= $product->low_stock_threshold)
                                <span class="badge bg-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.inventory.movements', $product) }}" class="btn btn-outline-info" title="Stock Movements">
                                    <i class="fas fa-history"></i>
                                </a>
                                <a href="{{ route('admin.inventory.stock-card', $product) }}" class="btn btn-outline-secondary" title="Stock Card">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                                <button type="button" class="btn btn-outline-primary" title="Update Stock" 
                                        onclick="showUpdateStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $products->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
            <h5>No products found</h5>
            <p class="text-muted">No products match your current filters.</p>
        </div>
        @endif
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStockForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" id="currentStock" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="newStock" class="form-label">New Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="newStock" name="new_stock" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="reason" name="reason" 
                               placeholder="Reason for stock update" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showUpdateStockModal(productId, productName, currentStock) {
    document.getElementById('updateStockForm').action = `/admin/inventory/product/${productId}/update-stock`;
    document.getElementById('productName').value = productName;
    document.getElementById('currentStock').value = currentStock;
    document.getElementById('newStock').value = currentStock;
    document.getElementById('reason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('updateStockModal'));
    modal.show();
    
    setTimeout(() => {
        document.getElementById('newStock').focus();
        document.getElementById('newStock').select();
    }, 500);
}
</script>
@endpush
