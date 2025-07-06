<?php $__env->startSection('title', 'Products'); ?>
<?php $__env->startSection('page_title', 'Products'); ?>

<?php $__env->startSection('page_actions'); ?>
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
        <option value="20" <?php echo e(request('per_page', 20) == 20 ? 'selected' : ''); ?>>20</option>
        <option value="50" <?php echo e(request('per_page', 20) == 50 ? 'selected' : ''); ?>>50</option>
        <option value="100" <?php echo e(request('per_page', 20) == 100 ? 'selected' : ''); ?>>100</option>
        <option value="200" <?php echo e(request('per_page', 20) == 200 ? 'selected' : ''); ?>>200</option>
    </select>
    
    <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Compact Grid View Styles */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
        height: 120px;
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
        <span class="quick-stat-number"><?php echo e($products->total() ?? 0); ?></span>
        <span class="quick-stat-label">Total Products</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number"><?php echo e($products->where('is_active', 1)->count() ?? 0); ?></span>
        <span class="quick-stat-label">Active</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number"><?php echo e($products->where('stock', '>', 0)->count() ?? 0); ?></span>
        <span class="quick-stat-label">In Stock</span>
    </div>
    <div class="quick-stat">
        <span class="quick-stat-number"><?php echo e($products->where('is_featured', 1)->count() ?? 0); ?></span>
        <span class="quick-stat-label">Featured</span>
    </div>
</div>

<!-- Compact Filters -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="<?php echo e(route('admin.products.index')); ?>" id="filter-form">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category">
                        <option value="">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Status</option>
                        <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>Active</option>
                        <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="stock_status">
                        <option value="">All Stock</option>
                        <option value="in_stock" <?php echo e(request('stock_status') === 'in_stock' ? 'selected' : ''); ?>>In Stock</option>
                        <option value="low_stock" <?php echo e(request('stock_status') === 'low_stock' ? 'selected' : ''); ?>>Low Stock</option>
                        <option value="out_of_stock" <?php echo e(request('stock_status') === 'out_of_stock' ? 'selected' : ''); ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="exportProducts()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="per_page" id="per_page_input" value="<?php echo e(request('per_page', 20)); ?>">
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if($products->count() > 0): ?>
        
        <!-- Grid View -->
        <div id="grid-view" class="view-container" style="display: none;">
            <div class="product-grid">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="product-card">
                    <div class="product-card-header">
                        <?php if($product->featured_image): ?>
                            <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="product-card-image" alt="<?php echo e($product->name); ?>">
                        <?php else: ?>
                            <div class="product-card-image d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image text-muted fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-card-overlay">
                            <?php if($product->is_featured): ?>
                                <span class="badge bg-warning badge-sm">Featured</span>
                            <?php endif; ?>
                            <?php if($product->is_active): ?>
                                <span class="badge bg-success badge-sm">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger badge-sm">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="product-card-body">
                        <h6 class="product-title"><?php echo e($product->name); ?></h6>
                        
                        <div class="product-meta">
                            <span class="badge bg-secondary badge-sm"><?php echo e($product->category->name); ?></span>
                            <small>Stock: <?php echo e($product->stock); ?></small>
                        </div>
                        
                        <div class="product-price">
                            <?php if($product->discount_price): ?>
                                <span class="text-primary">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                                <small class="text-muted text-decoration-line-through ms-1">₹<?php echo e(number_format($product->price, 2)); ?></small>
                            <?php else: ?>
                                <span>₹<?php echo e(number_format($product->price, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($product->sku): ?>
                            <small class="text-muted d-block mb-2">SKU: <?php echo e($product->sku); ?></small>
                        <?php endif; ?>
                        
                        <div class="product-actions">
                            <a href="<?php echo e(route('admin.products.show', $product)); ?>" class="btn btn-outline-info btn-sm" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('admin.products.toggle-status', $product)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="btn btn-outline-<?php echo e($product->is_active ? 'warning' : 'success'); ?> btn-sm" title="<?php echo e($product->is_active ? 'Deactivate' : 'Activate'); ?>">
                                    <i class="fas fa-<?php echo e($product->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php if($product->featured_image): ?>
                                    <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="product-thumb" alt="<?php echo e($product->name); ?>">
                                <?php else: ?>
                                    <div class="product-thumb bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="product-info">
                                    <p class="product-name mb-0"><?php echo e($product->name); ?></p>
                                    <?php if($product->sku): ?>
                                        <p class="product-sku mb-0">SKU: <?php echo e($product->sku); ?></p>
                                    <?php endif; ?>
                                    <?php if($product->is_featured): ?>
                                        <span class="badge bg-warning badge-sm mt-1">Featured</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary badge-sm"><?php echo e($product->category->name); ?></span>
                            </td>
                            <td>
                                <?php if($product->discount_price): ?>
                                    <div>
                                        <span class="text-primary fw-bold">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                                        <br><small class="text-muted text-decoration-line-through">₹<?php echo e(number_format($product->price, 2)); ?></small>
                                        <span class="badge bg-danger badge-sm"><?php echo e($product->discount_percentage); ?>% OFF</span>
                                    </div>
                                <?php else: ?>
                                    <span class="fw-bold">₹<?php echo e(number_format($product->price, 2)); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($product->stock > 10): ?>
                                    <span class="badge bg-success badge-sm"><?php echo e($product->stock); ?></span>
                                <?php elseif($product->stock > 0): ?>
                                    <span class="badge bg-warning badge-sm"><?php echo e($product->stock); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger badge-sm">Out</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-dot <?php echo e($product->is_active ? 'active' : 'inactive'); ?>"></span>
                                <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('admin.products.show', $product)); ?>" class="btn btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.products.toggle-status', $product)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-outline-<?php echo e($product->is_active ? 'warning' : 'success'); ?>" title="<?php echo e($product->is_active ? 'Deactivate' : 'Activate'); ?>">
                                            <i class="fas fa-<?php echo e($product->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('admin.products.toggle-featured', $product)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-outline-<?php echo e($product->is_featured ? 'warning' : 'info'); ?>" title="<?php echo e($product->is_featured ? 'Remove Featured' : 'Mark Featured'); ?>">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Ultra Compact List View -->
        <div id="compact-view" class="view-container" style="display: none;">
            <div class="ultra-compact-view">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="ultra-compact-item">
                    <?php if($product->featured_image): ?>
                        <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="ultra-compact-thumb" alt="<?php echo e($product->name); ?>">
                    <?php else: ?>
                        <div class="ultra-compact-thumb bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="ultra-compact-info">
                        <p class="ultra-compact-name"><?php echo e($product->name); ?></p>
                        <div class="ultra-compact-meta">
                            <span><span class="status-dot <?php echo e($product->is_active ? 'active' : 'inactive'); ?>"></span><?php echo e($product->category->name); ?></span>
                            <span>₹<?php echo e(number_format($product->discount_price ?: $product->price, 2)); ?></span>
                            <span>Stock: <?php echo e($product->stock); ?></span>
                            <?php if($product->is_featured): ?>
                                <span class="badge bg-warning badge-sm">Featured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="ultra-compact-actions">
                        <a href="<?php echo e(route('admin.products.show', $product)); ?>" class="btn btn-outline-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('admin.products.toggle-status', $product)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="btn btn-outline-<?php echo e($product->is_active ? 'warning' : 'success'); ?> btn-sm" title="<?php echo e($product->is_active ? 'Deactivate' : 'Activate'); ?>">
                                <i class="fas fa-<?php echo e($product->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <p class="small text-muted mb-0">
                    Showing <span class="fw-semibold text-primary"><?php echo e($products->firstItem()); ?></span>
                    to <span class="fw-semibold text-primary"><?php echo e($products->lastItem()); ?></span>
                    of <span class="fw-semibold text-primary"><?php echo e($products->total()); ?></span>
                    products
                </p>
            </div>
            <div class="pagination-nav">
                <?php echo e($products->withQueryString()->links()); ?>

            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box fa-4x text-muted mb-3"></i>
            <h5>No products found</h5>
            <p class="text-muted">Start by creating your first product or adjust your filters.</p>
            <div class="mt-3">
                <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Product
                </a>
                <?php if(request()->hasAny(['search', 'category', 'status', 'stock_status'])): ?>
                    <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
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
    window.open(`<?php echo e(route('admin.products.index')); ?>?${params.toString()}`, '_blank');
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/admin/products/index.blade.php ENDPATH**/ ?>