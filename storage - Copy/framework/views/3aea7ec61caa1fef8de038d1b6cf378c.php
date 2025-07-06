<?php $__env->startSection('title', 'Home - Herbal Bliss'); ?>
<?php $__env->startSection('meta_description', 'Discover pure, natural herbal products made with love. Shop organic teas, skincare, and wellness products at Herbal Bliss.'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Banners -->
<?php if($banners->count() > 0): ?>
<section class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="carousel-item <?php echo e($loop->first ? 'active' : ''); ?>">
                <img src="<?php echo e(Storage::url($banner->image)); ?>" class="d-block w-100" alt="<?php echo e($banner->alt_text ?: $banner->title); ?>" style="height: 400px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block">
                    <h5><?php echo e($banner->title); ?></h5>
                    <?php if($banner->link_url): ?>
                        <a href="<?php echo e($banner->link_url); ?>" class="btn btn-primary">Shop Now</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($banners->count() > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<div class="container">
    <!-- Featured Categories -->
    <?php if($categories->count() > 0): ?>
    <section class="categories-section mb-5">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if($category->image): ?>
                        <img src="<?php echo e(Storage::url($category->image)); ?>" class="card-img-top" alt="<?php echo e($category->name); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light-green d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-leaf fa-3x text-success"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo e($category->name); ?></h5>
                        <p class="card-text text-muted"><?php echo e(Str::limit($category->description, 80)); ?></p>
                        <a href="<?php echo e(route('category', $category->slug)); ?>" class="btn btn-primary">Browse Products</a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Product Menu Tabs -->
    <section class="product-menu-section mb-5">
        <div class="text-center mb-4">
            <h2 class="mb-3">Our Products</h2>
            <ul class="nav nav-pills justify-content-center" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo e($activeMenu === 'featured' ? 'active' : ''); ?>" 
                       href="<?php echo e(route('shop', ['menu' => 'featured'])); ?>" 
                       role="tab">
                        <i class="fas fa-star me-1"></i>
                        Featured Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo e($activeMenu === 'all' ? 'active' : ''); ?>" 
                       href="<?php echo e(route('shop', ['menu' => 'all'])); ?>" 
                       role="tab">
                        <i class="fas fa-th-large me-1"></i>
                        All Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php echo e($activeMenu === 'offers' ? 'active' : ''); ?>" 
                       href="<?php echo e(route('shop', ['menu' => 'offers'])); ?>" 
                       role="tab">
                        <i class="fas fa-tags me-1"></i>
                        Offer Products
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content" id="productTabsContent">
            <!-- Featured Products Tab -->
            <?php if($activeMenu === 'featured'): ?>
            <div class="tab-pane fade show active" id="featured" role="tabpanel">
                <?php if($featuredProducts->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                <div class="mb-2">
                                    <small class="text-muted"><?php echo e($product->category->name); ?></small>
                                </div>
                                <h6 class="card-title"><?php echo e($product->name); ?></h6>
                                <p class="card-text text-muted small"><?php echo e(Str::limit($product->short_description, 60)); ?></p>
                                
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
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Featured Products Available</h4>
                    <p class="text-muted">Check back later for our featured products.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- All Products Tab -->
            <?php if($activeMenu === 'all'): ?>
            <div class="tab-pane fade show active" id="all" role="tabpanel">
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
                                <div class="mb-2">
                                    <small class="text-muted"><?php echo e($product->category->name); ?></small>
                                </div>
                                <h6 class="card-title"><?php echo e($product->name); ?></h6>
                                <p class="card-text text-muted small"><?php echo e(Str::limit($product->short_description, 60)); ?></p>
                                
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
                    <?php echo e($products->appends(['menu' => 'all'])->links()); ?>

                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Products Available</h4>
                    <p class="text-muted">Check back later for our products.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Offer Products Tab -->
            <?php if($activeMenu === 'offers'): ?>
            <div class="tab-pane fade show active" id="offers" role="tabpanel">
                <?php if($products->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm product-card position-relative">
                            <?php if($product->featured_image): ?>
                                <img src="<?php echo e(Storage::url($product->featured_image)); ?>" class="card-img-top" alt="<?php echo e($product->name); ?>" style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Offer Badge -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-danger"><?php echo e($product->discount_percentage); ?>% OFF</span>
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
                                        <span class="h6 text-primary">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                                        <small class="text-muted text-decoration-line-through ms-1">₹<?php echo e(number_format($product->price, 2)); ?></small>
                                        <div class="text-success small mt-1">
                                            <i class="fas fa-rupee-sign"></i> You Save: ₹<?php echo e(number_format($product->price - $product->discount_price, 2)); ?>

                                        </div>
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
                    <?php echo e($products->appends(['menu' => 'offers'])->links()); ?>

                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Offers Available</h4>
                    <p class="text-muted">Check back later for special offers and deals.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section bg-light-green py-5 my-5 rounded">
        <div class="row text-center">
            <div class="col-md-3 col-sm-6 mb-3">
                <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                <h5>100% Natural</h5>
                <p class="text-muted">Pure herbal ingredients</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <i class="fas fa-shipping-fast fa-3x text-success mb-3"></i>
                <h5>Free Delivery</h5>
                <p class="text-muted">On orders above ₹500</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <i class="fas fa-award fa-3x text-success mb-3"></i>
                <h5>Quality Assured</h5>
                <p class="text-muted">Handmade with care</p>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <i class="fas fa-phone fa-3x text-success mb-3"></i>
                <h5>24/7 Support</h5>
                <p class="text-muted">Call us anytime</p>
            </div>
        </div>
    </section>
</div>

<style>
.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.carousel-item img {
    border-radius: 10px;
}

.card {
    border-radius: 10px;
}

/* Product Menu Tabs Styling */
.nav-pills .nav-link {
    background-color: transparent;
    border: 2px solid #e0e0e0;
    color: #666;
    margin: 0 5px;
    border-radius: 25px;
    padding: 10px 20px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    border-color: #28a745;
    color: #28a745;
    transform: translateY(-2px);
}

.nav-pills .nav-link.active {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.nav-pills .nav-link.active:hover {
    background-color: #218838;
    border-color: #218838;
    color: white;
}

/* Product Cards for Offers */
.offers .product-card {
    border: 2px solid #ffc107;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
}

.offers .product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-pills .nav-link {
        margin: 5px 0;
        padding: 8px 15px;
        font-size: 14px;
    }
    
    .nav-pills {
        flex-direction: column;
        align-items: center;
    }
    
    .nav-pills .nav-item {
        width: 200px;
    }
    
    .nav-pills .nav-link {
        text-align: center;
    }
}

/* Animation for tab content */
.tab-pane {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/home.blade.php ENDPATH**/ ?>