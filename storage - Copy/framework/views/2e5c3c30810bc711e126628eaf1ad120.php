<?php if($products->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card offer-card">
                <?php if($product->featured_image): ?>
                    <img src="<?php echo e(Storage::url($product->featured_image)); ?>" 
                         class="card-img-top" 
                         alt="<?php echo e($product->name); ?>" 
                         style="height: 250px; object-fit: cover;">
                <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-image fa-2x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <!-- Offer Badge -->
                <div class="position-absolute top-0 start-0 m-2">
                    <span class="badge bg-danger fs-6"><?php echo e($product->discount_percentage); ?>% OFF</span>
                </div>
                
                <!-- Special Offer Ribbon -->
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success">
                        <i class="fas fa-tag me-1"></i>OFFER
                    </span>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <small class="text-muted"><?php echo e($product->category->name); ?></small>
                    </div>
                    <h6 class="card-title"><?php echo e($product->name); ?></h6>
                    <p class="card-text text-muted small"><?php echo e(Str::limit($product->short_description, 60)); ?></p>
                    
                    <div class="mt-auto">
                        <div class="price-section mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="h6 text-primary">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                                    <small class="text-muted text-decoration-line-through ms-1">₹<?php echo e(number_format($product->price, 2)); ?></small>
                                </div>
                                <div class="text-end">
                                    <small class="text-success fw-bold">
                                        <i class="fas fa-rupee-sign"></i> Save ₹<?php echo e(number_format($product->price - $product->discount_price, 2)); ?>

                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if($product->stock <= 5): ?>
                            <div class="mb-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Only <?php echo e($product->stock); ?> left!
                                </small>
                            </div>
                        <?php endif; ?>
                        
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
                                <a href="<?php echo e(route('product', $product->slug)); ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <?php if($product->isInStock()): ?>
                                    <button onclick="addToCartWithQuantity(<?php echo e($product->id); ?>)" class="btn btn-danger btn-sm flex-grow-1">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                        <i class="fas fa-times me-1"></i>Out of Stock
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
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-tags fa-4x text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">No Offers Found</h3>
                <p class="text-muted mb-4">Try adjusting your filters or check back later for new offers.</p>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\source_code\herbal-ecom\resources\views/partials/offer-products-grid.blade.php ENDPATH**/ ?>