@extends('admin.layouts.app')

@section('title', 'Low Stock Alert')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Low Stock Alert</h1>
        <div>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
        <!-- Alert Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Low Stock Alert!</h4>
                    <p>You have <strong>{{ $lowStockProducts->count() }}</strong> products with stock levels below their threshold.</p>
                    <hr>
                    <p class="mb-0">Consider reordering these items to avoid stockouts.</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Products Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Products Below Stock Threshold</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Low Stock Threshold</th>
                                <th>Shortage</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                                @php
                                    $shortage = $product->low_stock_threshold - $product->stock;
                                    $statusClass = $product->stock <= 0 ? 'danger' : 'warning';
                                    $statusText = $product->stock <= 0 ? 'Out of Stock' : 'Low Stock';
                                @endphp
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
                                        <span class="badge badge-{{ $statusClass }} font-weight-bold">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>{{ $product->low_stock_threshold ?? 0 }}</td>
                                    <td>
                                        <span class="text-danger font-weight-bold">
                                            {{ $shortage > 0 ? $shortage : 0 }}
                                        </span>
                                    </td>
                                    <td>₹{{ number_format($product->cost_price, 2) }}</td>
                                    <td>₹{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.inventory.movements', $product) }}" class="btn btn-outline-info" title="Stock Movements">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <a href="{{ route('admin.purchase-orders.create') }}?product_id={{ $product->id }}" class="btn btn-outline-primary" title="Create Purchase Order">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-success" title="Update Stock" 
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
            </div>
        </div>
    @else
        <!-- No Low Stock -->
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4 class="text-success">All Good! 🎉</h4>
                <p class="text-muted">No products are currently below their stock threshold.</p>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary">
                    <i class="fas fa-warehouse"></i> View All Inventory
                </a>
            </div>
        </div>
    @endif
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
@endsection
