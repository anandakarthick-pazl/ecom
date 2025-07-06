<?php $__env->startSection('title', $product->meta_title ?: $product->name . ' - Herbal Bliss'); ?>
<?php $__env->startSection('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160)); ?>
<?php $__env->startSection('meta_keywords', $product->meta_keywords); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('category', $product->category->slug)); ?>"><?php echo e($product->category->name); ?></a></li>
            <li class="breadcrumb-item active"><?php echo e($product->name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-images">
                <?php if($product->featured_image): ?>
                    <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="img-fluid rounded main-image" alt="<?php echo e($product->name); ?>" id="mainImage">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                        <i class="fas fa-image fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>

                <?php if($product->images && count($product->images) > 0): ?>
                <div class="row mt-3">
                    <?php if($product->featured_image): ?>
                        <div class="col-3">
                            <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="img-fluid rounded thumb-image active" alt="<?php echo e($product->name); ?>" onclick="changeMainImage(this)">
                        </div>
                    <?php endif; ?>
                    <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-3">
                            <img src="<?php echo e(Storage::url($image)); ?>" class="img-fluid rounded thumb-image" alt="<?php echo e($product->name); ?>" onclick="changeMainImage(this)">
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="product-details">
                <div class="mb-2">
                    <span class="badge bg-secondary"><?php echo e($product->category->name); ?></span>
                    <?php if($product->is_featured): ?>
                        <span class="badge bg-warning">Featured</span>
                    <?php endif; ?>
                </div>

                <h1 class="h3 mb-3"><?php echo e($product->name); ?></h1>

                <?php if($product->short_description): ?>
                    <p class="lead text-muted mb-3"><?php echo e($product->short_description); ?></p>
                <?php endif; ?>

                <!-- Price -->
                <div class="price-section mb-4">
                    <?php if($product->discount_price): ?>
                        <h3 class="text-primary d-inline">₹<?php echo e(number_format($product->discount_price, 2)); ?></h3>
                        <span class="h5 text-muted text-decoration-line-through ms-2">₹<?php echo e(number_format($product->price, 2)); ?></span>
                        <span class="badge bg-danger ms-2"><?php echo e($product->discount_percentage); ?>% OFF</span>
                    <?php else: ?>
                        <h3 class="text-primary">₹<?php echo e(number_format($product->price, 2)); ?></h3>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="product-info mb-4">
                    <?php if($product->weight): ?>
                        <p><strong>Weight:</strong> <?php echo e($product->weight); ?> <?php echo e($product->weight_unit); ?></p>
                    <?php endif; ?>
                    <?php if($product->sku): ?>
                        <p><strong>SKU:</strong> <?php echo e($product->sku); ?></p>
                    <?php endif; ?>
                    <p><strong>Stock:</strong> 
                        <?php if($product->stock > 10): ?>
                            <span class="text-success">In Stock (<?php echo e($product->stock); ?> available)</span>
                        <?php elseif($product->stock > 0): ?>
                            <span class="text-warning">Limited Stock (<?php echo e($product->stock); ?> left)</span>
                        <?php else: ?>
                            <span class="text-danger">Out of Stock</span>
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Add to Cart -->
                <?php if($product->isInStock()): ?>
                <div class="add-to-cart mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo e($product->stock); ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <button onclick="addToCartWithQuantity()" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock.
                </div>
                <?php endif; ?>

                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h6>Share this product:</h6>
                    <a href="https://wa.me/?text=Check out this amazing product: <?php echo e($product->name); ?> - <?php echo e(url()->current()); ?>" class="btn btn-success btn-sm me-2" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(url()->current()); ?>" class="btn btn-primary btn-sm me-2" target="_blank">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <button onclick="copyToClipboard()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-link"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Product Description</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br(e($product->description)); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if($relatedProducts->count() > 0): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">Related Products</h4>
            <div class="row">
                <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <?php if($relatedProduct->featured_image): ?>
                            <img src="<?php echo e(Storage::url($relatedProduct->featured_image)); ?>" class="card-img-top" alt="<?php echo e($relatedProduct->name); ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo e($relatedProduct->name); ?></h6>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <?php if($relatedProduct->discount_price): ?>
                                        <span class="h6 text-primary">₹<?php echo e(number_format($relatedProduct->discount_price, 2)); ?></span>
                                        <small class="text-muted text-decoration-line-through">₹<?php echo e(number_format($relatedProduct->price, 2)); ?></small>
                                    <?php else: ?>
                                        <span class="h6 text-primary">₹<?php echo e(number_format($relatedProduct->price, 2)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <?php if($relatedProduct->isInStock()): ?>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="input-group input-group-sm quantity-selector">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity(<?php echo e($relatedProduct->id); ?>)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" id="quantity-<?php echo e($relatedProduct->id); ?>" value="1" min="1" max="<?php echo e($relatedProduct->stock); ?>" style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity(<?php echo e($relatedProduct->id); ?>)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo e(route('product', $relatedProduct->slug)); ?>" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                        <?php if($relatedProduct->isInStock()): ?>
                                            <button onclick="addToCartWithQuantity(<?php echo e($relatedProduct->id); ?>)" class="btn btn-primary btn-sm flex-grow-1">
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
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.thumb-image {
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.2s;
}

.thumb-image:hover,
.thumb-image.active {
    border-color: var(--primary-color);
}

.main-image {
    max-height: 500px;
    object-fit: cover;
}

.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-3px);
}
</style>

<?php $__env->startPush('scripts'); ?>
<script>
function changeMainImage(img) {
    document.getElementById('mainImage').src = img.src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumb-image').forEach(thumb => {
        thumb.classList.remove('active');
    });
    img.classList.add('active');
}

// For the main product page, we use simple functions without product ID
function incrementQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    
    quantityInput.value = currentValue + 1;
}

function decrementQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const minValue = parseInt(quantityInput.min);
    
    if (currentValue > minValue) {
        quantityInput.value = currentValue - 1;
    }
}

function addToCartWithQuantity() {
    const quantity = document.getElementById('quantity').value;
    addToCart(<?php echo e($product->id); ?>, quantity);
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showToast('Link copied to clipboard!', 'success');
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/product.blade.php ENDPATH**/ ?>