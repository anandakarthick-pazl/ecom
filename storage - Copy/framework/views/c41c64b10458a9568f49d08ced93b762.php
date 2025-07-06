<?php $__env->startSection('title', $category->meta_title ?: $category->name . ' - Herbal Bliss'); ?>
<?php $__env->startSection('meta_description', $category->meta_description ?: $category->description); ?>
<?php $__env->startSection('meta_keywords', $category->meta_keywords); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
            <li class="breadcrumb-item active"><?php echo e($category->name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="category-header mb-4">
                <h1 class="h2"><?php echo e($category->name); ?></h1>
                <?php if($category->description): ?>
                    <p class="lead text-muted"><?php echo e($category->description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if($products->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card">
                <?php if($product->featured_image): ?>
                    <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="card-img-top" alt="<?php echo e($product->name); ?>" style="height: 250px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-image fa-2x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <?php if($product->discount_percentage > 0): ?>
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-danger"><?php echo e($product->discount_percentage); ?>% OFF</span>
                    </div>
                <?php endif; ?>
                
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title"><?php echo e($product->name); ?></h6>
                    <p class="card-text text-muted small"><?php echo e(Str::limit($product->short_description, 80)); ?></p>
                    
                    <div class="mt-auto">
                        <div class="price-section mb-2">
                            <?php if($product->discount_price): ?>
                                <span class="h6 text-primary">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                                <small class="text-muted text-decoration-line-through ms-1">₹<?php echo e(number_format($product->price, 2)); ?></small>
                            <?php else: ?>
                                <span class="h6 text-primary">₹<?php echo e(number_format($product->price, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <?php if($product->isInStock()): ?>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="input-group input-group-sm quantity-selector">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity(<?php echo e($product->id); ?>)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="quantity-<?php echo e($product->id); ?>" value="1" min="1" max="<?php echo e($product->stock); ?>" style="max-width: 60px;">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity(<?php echo e($product->id); ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex gap-2">
                                <a href="<?php echo e(route('product', $product->slug)); ?>" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                <?php if($product->isInStock()): ?>
                                    <button onclick="addToCartWithQuantity(<?php echo e($product->id); ?>)" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-cart-plus"></i> Add
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fas fa-times"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Pagination -->
    <?php if($enablePagination && isset($products) && method_exists($products, 'appends')): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($products->links()); ?>

    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
        <h4>No products found</h4>
        <p class="text-muted mb-4">We don't have any products in this category yet.</p>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
            <i class="fas fa-home"></i> Go Home
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/category.blade.php ENDPATH**/ ?>