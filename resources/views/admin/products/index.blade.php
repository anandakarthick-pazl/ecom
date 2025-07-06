@extends('admin.layouts.app')

@section('title', 'Products')
@section('page_title', 'Products')

@section('page_actions')
<div class="d-flex gap-2 align-items-center">
    <!-- View Toggle Buttons -->
    <div class="btn-group btn-group-sm" role="group" aria-label="View options">
        <button type="button" class="btn btn-outline-secondary" id="grid-view-btn" title="Grid View">
            <i class="fas fa-th"></i>
        </button>
        <button type="button" class="btn btn-secondary active" id="table-view-btn" title="Table View">
            <i class="fas fa-list"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="compact-view-btn" title="Compact View">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <!-- Items per page selector -->
    <select class="form-select form-select-sm" id="items-per-page" style="width: auto;">
        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
        <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
        <option value="200" {{ request('per_page', 20) == 200 ? 'selected' : '' }}>200</option>
    </select>
    
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>
@endsection

@section('content')
<style>
    /* Compact Grid View Styles */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .product-card {
        background: white;
        border-radius: 8px;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .product-card-header {
        position: relative;
        height: 100px;
        overflow: hidden;
    }

    .product-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-card-image {
        transform: scale(1.05);
    }

    .product-card-overlay {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 4px;
        flex-direction: column;
    }

    .product-card-body {
        padding: 12px;
    }

    .product-title {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 6px 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-meta {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-price {
        font-size: 16px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .product-actions {
        display: flex;
        gap: 4px;
        margin-top: 8px;
    }

    .product-actions .btn {
        flex: 1;
        padding: 4px 8px;
        font-size: 11px;
    }

    /* Compact Table View Styles */
    .compact-table {
        font-size: 13px;
    }

    .compact-table td, .compact-table th {
        padding: 8px 12px;
        vertical-align: middle;
    }

    .compact-table .product-thumb {
        width: 35px;
        height: 35px;
        border-radius: 4px;
        object-fit: cover;
    }

    .compact-table .product-info {
        min-width: 200px;
    }

    .compact-table .product-name {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 13px;
        line-height: 1.3;
    }

    .compact-table .product-sku {
        font-size: 11px;
        color: #6c757d;
        margin: 2px 0 0 0;
    }

    .compact-table .btn-group-sm .btn {
        padding: 2px 6px;
        font-size: 10px;
    }

    /* Ultra Compact List View */
    .ultra-compact-view {
        background: white;
        border-radius: 8px;
        border: 1px solid #e3e6f0;
        overflow: hidden;
    }

    .ultra-compact-item {
        display: flex;
        align-items: center;
        padding: 8px 15px;
        border-bottom: 1px solid #f1f3f4;
        transition: background-color 0.2s ease;
    }

    .ultra-compact-item:hover {
        background-color: #f8f9fa;
    }

    .ultra-compact-item:last-child {
        border-bottom: none;
    }

    .ultra-compact-thumb {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        object-fit: cover;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .ultra-compact-info {
        flex: 1;
        min-width: 0;
    }

    .ultra-compact-name {
        font-size: 13px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ultra-compact-meta {
        font-size: 11px;
        color: #6c757d;
        margin: 2px 0 0 0;
        display: flex;
        gap: 10px;
    }

    .ultra-compact-actions {
        display: flex;
        gap: 4px;
        margin-left: 10px;
    }

    .ultra-compact-actions .btn {
        padding: 3px 6px;
        font-size: 10px;
    }

    /* Badges and Status */
    .badge-sm {
        font-size: 10px;
        padding: 3px 6px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .status-dot.active {
        background-color: #28a745;
    }

    .status-dot.inactive {
        background-color: #dc3545;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.75rem;
        }
        
        .product-card-header {
            height: 100px;
        }
        
        .compact-table {
            font-size: 12px;
        }
        
        .compact-table td, .compact-table th {
            padding: 6px 8px;
        }
    }

    @media (max-width: 576px) {
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    /* Quick stats bar */
    .quick-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-around;
        text-align: center;
    }

    .quick-stat {
        flex: 1;
    }

    .quick-stat-number {
        font-size: 20px;
        font-weight: bold;
        display: block;
    }

    .quick-stat-label {
        font-size: 12px;
        opacity: 0.9;
    }

    /* View transitions */
    .view-container {
        transition: opacity 0.2s ease;
    }

    .view-container.switching {
        opacity: 0.5;
    }
</style>
<!-- Quick Stats Bar -->
<div class="quick-stats">
    <div class="quick-stat">
        <span class="quick-stat-number">{{ $products->total() ?? 0 }}</span>
        <span class="quick-stat-label">Total Products</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number">{{ $products->where('is_active', 1)->count() ?? 0 }}</span>
        <span class="quick-stat-label">Active</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number">{{ $products->where('stock', '>', 0)->count() ?? 0 }}</span>
        <span class="quick-stat-label">In Stock</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number">{{ $products->where('is_featured', 1)->count() ?? 0 }}</span>
        <span class="quick-stat-label">Featured</span>
    </div>
</div>

<!-- Compact Filters -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.products.index') }}" id="filter-form">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="exportProducts()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="per_page" id="per_page_input" value="{{ request('per_page', 20) }}">
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
        
        <!-- Grid View -->
        <div id="grid-view" class="view-container" style="display: none;">
            <div class="product-grid">
                @foreach($products as $product)
                <div class="product-card">
                    <div class="product-card-header">
                        @if($product->featured_image)
                            <img src="{{ Storage::url($product->featured_image) }}" class="product-card-image" alt="{{ $product->name }}">
                        @else
                            <div class="product-card-image d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image text-muted fa-2x"></i>
                            </div>
                        @endif
                        
                        <div class="product-card-overlay">
                            @if($product->is_featured)
                                <span class="badge bg-warning badge-sm">Featured</span>
                            @endif
                            @if($product->is_active)
                                <span class="badge bg-success badge-sm">Active</span>
                            @else
                                <span class="badge bg-danger badge-sm">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="product-card-body">
                        <h6 class="product-title">{{ $product->name }}</h6>
                        
                        <div class="product-meta">
                            <span class="badge bg-secondary badge-sm">{{ $product->category->name }}</span>
                            <small>Stock: {{ $product->stock }}</small>
                        </div>
                        
                        <div class="product-price">
                            @if($product->discount_price)
                                <span class="text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                            @else
                                <span>₹{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($product->sku)
                            <small class="text-muted d-block mb-2">SKU: {{ $product->sku }}</small>
                        @endif
                        
                        <div class="product-actions">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Compact Table View -->
        <div id="table-view" class="view-container">
            <div class="table-responsive">
                <table class="table table-hover compact-table">
                    <thead class="table-light">
                        <tr>
                            <th width="50">Image</th>
                            <th>Product</th>
                            <th width="120">Category</th>
                            <th width="100">Price</th>
                            <th width="80">Stock</th>
                            <th width="80">Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @if($product->featured_image)
                                    <img src="{{ Storage::url($product->featured_image) }}" class="product-thumb" alt="{{ $product->name }}">
                                @else
                                    <div class="product-thumb bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="product-info">
                                    <p class="product-name mb-0">{{ $product->name }}</p>
                                    @if($product->sku)
                                        <p class="product-sku mb-0">SKU: {{ $product->sku }}</p>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="badge bg-warning badge-sm mt-1">Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary badge-sm">{{ $product->category->name }}</span>
                            </td>
                            <td>
                                @if($product->discount_price)
                                    <div>
                                        <span class="text-primary fw-bold">₹{{ number_format($product->discount_price, 2) }}</span>
                                        <br><small class="text-muted text-decoration-line-through">₹{{ number_format($product->price, 2) }}</small>
                                        <span class="badge bg-danger badge-sm">{{ $product->discount_percentage }}% OFF</span>
                                    </div>
                                @else
                                    <span class="fw-bold">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($product->stock > 10)
                                    <span class="badge bg-success badge-sm">{{ $product->stock }}</span>
                                @elseif($product->stock > 0)
                                    <span class="badge bg-warning badge-sm">{{ $product->stock }}</span>
                                @else
                                    <span class="badge bg-danger badge-sm">Out</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-dot {{ $product->is_active ? 'active' : 'inactive' }}"></span>
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }}" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-{{ $product->is_featured ? 'warning' : 'info' }}" title="{{ $product->is_featured ? 'Remove Featured' : 'Mark Featured' }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Ultra Compact List View -->
        <div id="compact-view" class="view-container" style="display: none;">
            <div class="ultra-compact-view">
                @foreach($products as $product)
                <div class="ultra-compact-item">
                    @if($product->featured_image)
                        <img src="{{ Storage::url($product->featured_image) }}" class="ultra-compact-thumb" alt="{{ $product->name }}">
                    @else
                        <div class="ultra-compact-thumb bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="ultra-compact-info">
                        <p class="ultra-compact-name">{{ $product->name }}</p>
                        <div class="ultra-compact-meta">
                            <span><span class="status-dot {{ $product->is_active ? 'active' : 'inactive' }}"></span>{{ $product->category->name }}</span>
                            <span>₹{{ number_format($product->discount_price ?: $product->price, 2) }}</span>
                            <span>Stock: {{ $product->stock }}</span>
                            @if($product->is_featured)
                                <span class="badge bg-warning badge-sm">Featured</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ultra-compact-actions">
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $product->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Enhanced Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <p class="small text-muted mb-0">
                    Showing <span class="fw-semibold text-primary">{{ $products->firstItem() }}</span>
                    to <span class="fw-semibold text-primary">{{ $products->lastItem() }}</span>
                    of <span class="fw-semibold text-primary">{{ $products->total() }}</span>
                    products
                </p>
            </div>
            <div class="pagination-nav">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-box fa-4x text-muted mb-3"></i>
            <h5>No products found</h5>
            <p class="text-muted">Start by creating your first product or adjust your filters.</p>
            <div class="mt-3">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Product
                </a>
                @if(request()->hasAny(['search', 'category', 'status', 'stock_status']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const gridViewBtn = document.getElementById('grid-view-btn');
    const tableViewBtn = document.getElementById('table-view-btn');
    const compactViewBtn = document.getElementById('compact-view-btn');
    
    const gridView = document.getElementById('grid-view');
    const tableView = document.getElementById('table-view');
    const compactView = document.getElementById('compact-view');
    
    // Load saved view preference
    const savedView = localStorage.getItem('admin_products_view') || 'table';
    switchView(savedView);
    
    function switchView(viewType) {
        // Remove active classes
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('btn-secondary', 'active');
            btn.classList.add('btn-outline-secondary');
        });
        
        // Hide all views
        gridView.style.display = 'none';
        tableView.style.display = 'none';
        compactView.style.display = 'none';
        
        // Show selected view and activate button
        switch(viewType) {
            case 'grid':
                gridView.style.display = 'block';
                gridViewBtn.classList.remove('btn-outline-secondary');
                gridViewBtn.classList.add('btn-secondary', 'active');
                break;
            case 'compact':
                compactView.style.display = 'block';
                compactViewBtn.classList.remove('btn-outline-secondary');
                compactViewBtn.classList.add('btn-secondary', 'active');
                break;
            default:
                tableView.style.display = 'block';
                tableViewBtn.classList.remove('btn-outline-secondary');
                tableViewBtn.classList.add('btn-secondary', 'active');
        }
        
        // Save preference
        localStorage.setItem('admin_products_view', viewType);
    }
    
    gridViewBtn.addEventListener('click', () => switchView('grid'));
    tableViewBtn.addEventListener('click', () => switchView('table'));
    compactViewBtn.addEventListener('click', () => switchView('compact'));
    
    // Items per page functionality
    document.getElementById('items-per-page').addEventListener('change', function() {
        document.getElementById('per_page_input').value = this.value;
        document.getElementById('filter-form').submit();
    });
    
    // Auto-submit search on typing (debounced)
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500);
        });
    }
    
    // Quick filter buttons functionality
    document.querySelectorAll('select[name="category"], select[name="status"], select[name="stock_status"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });
});

// Export functionality
function exportProducts() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.open(`{{ route('admin.products.index') }}?${params.toString()}`, '_blank');
}

// Enhanced tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endsection
