@extends('layouts.app')

@section('title', 'Home - Herbal Bliss')
@section('meta_description', 'Discover pure, natural herbal products made with love. Shop organic teas, skincare, and wellness products at Herbal Bliss.')

@section('content')
<!-- Hero Banners -->
@if($banners->count() > 0)
<section class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <img src="{{ $banner->image_url }}" class="d-block w-100" alt="{{ $banner->alt_text ?: $banner->title }}" style="height: 250px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block">
                    <h5>{{ $banner->title }}</h5>
                    @if($banner->link_url)
                        <a href="{{ $banner->link_url }}" class="btn btn-primary">Shop Now</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        @endif
    </div>
</section>
@endif

<div class="container">
    <!-- Cart Summary Widget (New Addition) -->
    <section class="cart-summary-widget mb-4" id="home-cart-summary">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" 
                 data-bs-toggle="collapse" data-bs-target="#cartSummaryCollapse" 
                 style="cursor: pointer;">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Cart Summary (<span id="home-cart-count">0</span> items)
                </h5>
                <i class="fas fa-chevron-down" id="cart-summary-chevron"></i>
            </div>
            <div class="collapse" id="cartSummaryCollapse">
                <div class="card-body" id="home-cart-body">
                    <!-- Cart content will be loaded here -->
                    <div id="cart-empty-message" class="text-center py-3">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Your cart is empty</p>
                        <small class="text-muted">Add products to see cart summary</small>
                    </div>
                    
                    <!-- Cart items will be dynamically loaded here -->
                    <div id="home-cart-items" style="display: none;">
                        <!-- Detailed Product Breakdown -->
                        <div id="home-detailed-product-breakdown"></div>
                        
                        <hr class="my-3">
                        
                        <!-- Order Totals Section -->
                        <div class="order-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="home-cart-subtotal">₹0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>CGST:</span>
                                <span id="home-cgst-amount">₹0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>SGST:</span>
                                <span id="home-sgst-amount">₹0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Tax:</span>
                                <span id="home-total-tax">₹0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Charge:</span>
                                <span id="home-delivery-charge">₹50.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Payment Charge:</span>
                                <span id="home-payment-charge">+₹0.00</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong id="home-cart-total">₹0.00</strong>
                            </div>
                            
                            <!-- Cart Actions -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> View Full Cart
                                </a>
                                <a href="{{ route('checkout') }}" class="btn btn-primary" id="home-checkout-btn">
                                    <i class="fas fa-lock me-1"></i> Proceed to Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    @if($categories->count() > 0)
    <section class="categories-section mb-4">
        <h3 class="text-center mb-3">Shop by Category</h3>
        <p class="text-center text-muted mb-3">Explore our carefully curated collections</p>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm category-card-compact">
                    @if($category->image)
                        <div class="category-image-wrapper-compact">
                            <img src="{{ $category->image_url }}" 
                                 class="card-img-top category-image-compact" 
                                 alt="{{ $category->name }}" 
                                 style="height: 100px; object-fit: cover;"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ asset('images/fallback/category-placeholder.png') }}'; this.parentElement.classList.add('fallback-image');">
                            @if($category->products_count > 0)
                                <div class="category-badge-compact">
                                    <span class="badge bg-primary">{{ $category->products_count }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="card-img-top bg-light-green d-flex align-items-center justify-content-center category-placeholder-compact" style="height: 100px;">
                            <i class="fas fa-leaf fa-2x text-success"></i>
                            @if($category->products_count > 0)
                                <div class="category-badge-compact">
                                    <span class="badge bg-primary">{{ $category->products_count }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="card-body text-center p-3">
                        <h6 class="card-title mb-2">{{ $category->name }}</h6>
                        <a href="{{ route('category', $category->slug) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Browse
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @else
    <section class="categories-section mb-5">
        <div class="text-center py-5">
            <i class="fas fa-th-large fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">Categories Coming Soon</h3>
            <p class="text-muted">We're organizing our products into categories. Check back soon!</p>
        </div>
    </section>
    @endif

    <!-- Product Menu Tabs -->
    <section class="product-menu-section mb-5">
        <div class="text-center mb-4">
            <h2 class="mb-3">Our Products</h2>
            <ul class="nav nav-pills justify-content-center" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeMenu === 'featured' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'featured']) }}" 
                       role="tab">
                        <i class="fas fa-star me-1"></i>
                        Featured Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeMenu === 'all' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'all']) }}" 
                       role="tab">
                        <i class="fas fa-th-large me-1"></i>
                        All Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeMenu === 'offers' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'offers']) }}" 
                       role="tab">
                        <i class="fas fa-tags me-1"></i>
                        Offer Products
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content" id="productTabsContent">
            <!-- Featured Products Tab -->
            @if($activeMenu === 'featured')
            <div class="tab-pane fade show active" id="featured" role="tabpanel">
                @if($featuredProducts->count() > 0)
                <div class="row">
                    @foreach($featuredProducts as $product)
                    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            @if($product->featured_image)
                                <img src="{{ $product->featured_image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                            
                            @if($product->discount_percentage > 0)
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <small class="text-muted">{{ $product->category->name }}</small>
                                </div>
                                <h6 class="card-title">{{ $product->name }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                                
                                <div class="mt-auto">
                                    <div class="price-section mb-2">
                                        @if($product->discount_price)
                                            <span class="h6 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                            <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                                        @else
                                            <span class="h6 text-primary">₹{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="product-actions">
                                        @if($product->isInStock())
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="input-group input-group-sm quantity-selector">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" style="max-width: 60px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                            @if($product->isInStock())
                                                <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="fas fa-cart-plus"></i> Add
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times"></i> Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Featured Products Available</h4>
                    <p class="text-muted">Check back later for our featured products.</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- All Products Tab -->
            @if($activeMenu === 'all')
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            @if($product->featured_image)
                                <img src="{{ Storage::url($product->featured_image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                            
                            @if($product->discount_percentage > 0)
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <small class="text-muted">{{ $product->category->name }}</small>
                                </div>
                                <h6 class="card-title">{{ $product->name }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                                
                                <div class="mt-auto">
                                    <div class="price-section mb-2">
                                        @if($product->discount_price)
                                            <span class="h6 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                            <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                                        @else
                                            <span class="h6 text-primary">₹{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="product-actions">
                                        @if($product->isInStock())
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="input-group input-group-sm quantity-selector">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" style="max-width: 60px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                            @if($product->isInStock())
                                                <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="fas fa-cart-plus"></i> Add
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times"></i> Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(['menu' => 'all'])->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Products Available</h4>
                    <p class="text-muted">Check back later for our products.</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Offer Products Tab -->
            @if($activeMenu === 'offers')
            <div class="tab-pane fade show active" id="offers" role="tabpanel">
                @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                        <div class="card h-100 border-0 shadow-sm product-card position-relative">
                            @if($product->featured_image)
                                <img src="{{ Storage::url($product->featured_image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Offer Badge -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                            </div>
                            
                            <!-- Special Offer Ribbon -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-tag me-1"></i>OFFER
                                </span>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <small class="text-muted">{{ $product->category->name }}</small>
                                </div>
                                <h6 class="card-title">{{ $product->name }}</h6>
                                <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                                
                                <div class="mt-auto">
                                    <div class="price-section mb-2">
                                        <span class="h6 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                        <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                                        <div class="text-success small mt-1">
                                            <i class="fas fa-rupee-sign"></i> You Save: ₹{{ number_format($product->price - $product->discount_price, 2) }}
                                        </div>
                                    </div>
                                    
                                    <div class="product-actions">
                                        @if($product->isInStock())
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="input-group input-group-sm quantity-selector">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" style="max-width: 60px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity({{ $product->id }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                            @if($product->isInStock())
                                                <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="fas fa-cart-plus"></i> Add
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-times"></i> Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(['menu' => 'offers'])->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Offers Available</h4>
                    <p class="text-muted">Check back later for special offers and deals.</p>
                </div>
                @endif
            </div>
            @endif
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
/* Home Cart Summary Widget Styles */
.cart-summary-widget {
    position: sticky;
    top: 80px;
    z-index: 100;
}

.cart-summary-widget .card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    overflow: hidden;
}

.cart-summary-widget .card-header {
    border-radius: 12px 12px 0 0 !important;
    transition: all 0.3s ease;
}

.cart-summary-widget .card-header:hover {
    background-color: #0056b3 !important;
}

#cart-summary-chevron {
    transition: transform 0.3s ease;
}

.cart-summary-widget .collapsed #cart-summary-chevron {
    transform: rotate(-90deg);
}

/* Home Cart Product Breakdown Styles */
.home-cart-product-item {
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 8px;
    border-left: 3px solid #007bff;
}

.home-cart-product-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.home-cart-product-calculation {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 2px;
    line-height: 1.2;
}

.home-cart-product-subtotal {
    font-size: 0.85rem;
    color: #2c3e50;
    font-weight: 600;
    text-align: right;
}

#home-detailed-product-breakdown {
    max-height: 200px;
    overflow-y: auto;
}

#home-detailed-product-breakdown::-webkit-scrollbar {
    width: 4px;
}

#home-detailed-product-breakdown::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

#home-detailed-product-breakdown::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

#home-detailed-product-breakdown::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive cart summary */
@media (max-width: 768px) {
    .cart-summary-widget {
        position: relative;
        top: auto;
    }
}

.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}

/* Enhanced Category Card Styles */
.category-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/* Compact Category Card Styles */
.category-card-compact {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    max-height: 200px;
}

.category-card-compact:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.category-image-wrapper {
    position: relative;
    overflow: hidden;
}

.category-image-wrapper-compact {
    position: relative;
    overflow: hidden;
    height: 100px;
}

.category-image {
    transition: transform 0.3s ease;
    border-radius: 0;
}

.category-image-compact {
    transition: transform 0.3s ease;
    border-radius: 0;
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.category-card:hover .category-image {
    transform: scale(1.05);
}

.category-card-compact:hover .category-image-compact {
    transform: scale(1.08);
}

.category-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.category-badge-compact {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 2;
}

.category-badge .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
    border-radius: 20px;
    background: rgba(13, 110, 253, 0.9) !important;
    backdrop-filter: blur(10px);
}

.category-badge-compact .badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.4rem;
    border-radius: 15px;
    background: rgba(13, 110, 253, 0.9) !important;
    backdrop-filter: blur(10px);
    min-width: 22px;
    text-align: center;
}

.category-placeholder {
    position: relative;
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    border-radius: 0;
}

.category-placeholder-compact {
    position: relative;
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8f0 100%);
    border-radius: 0;
    height: 100px;
}

.category-placeholder .category-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.category-placeholder-compact .category-badge-compact {
    position: absolute;
    top: 5px;
    right: 5px;
}

.fallback-image {
    background: rgba(248, 249, 250, 0.8);
}

.fallback-image .category-image {
    opacity: 0.7;
    border: 2px dashed #dee2e6;
}

.fallback-image .category-image-compact {
    opacity: 0.7;
    border: 2px dashed #dee2e6;
}

/* Category section header improvements */
.categories-section h2 {
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 1rem;
}

.categories-section h3 {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.categories-section .text-muted {
    font-size: 1rem;
    margin-bottom: 1.5rem;
}

/* Category card body improvements */
.category-card .card-body {
    padding: 1.5rem;
}

.category-card-compact .card-body {
    padding: 0.75rem;
}

.category-card .card-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.category-card-compact .card-title {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    line-height: 1.2;
}

.category-card .btn {
    border-radius: 25px;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    transition: all 0.3s ease;
}

.category-card-compact .btn {
    border-radius: 20px;
    font-weight: 500;
    padding: 0.375rem 0.8rem;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.category-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

.category-card-compact .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(13, 110, 253, 0.3);
}

/* Responsive category adjustments */
@media (max-width: 768px) {
    .category-card {
        margin-bottom: 1.5rem;
    }
    
    .category-card-compact {
        margin-bottom: 1rem;
        max-height: 180px;
    }
    
    .categories-section h2 {
        font-size: 1.75rem;
    }
    
    .categories-section h3 {
        font-size: 1.4rem;
    }
    
    .category-badge {
        top: 5px;
        right: 5px;
    }
    
    .category-badge-compact {
        top: 3px;
        right: 3px;
    }
    
    .category-badge .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }
    
    .category-badge-compact .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.3rem;
        min-width: 18px;
    }
    
    .category-image-wrapper-compact {
        height: 80px;
    }
    
    .category-image-compact {
        height: 80px;
    }
    
    .category-placeholder-compact {
        height: 80px;
    }
    
    .category-card-compact .card-title {
        font-size: 0.9rem;
    }
    
    .category-card-compact .btn {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
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

<script>
// Home page cart summary functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load cart summary on page load
    loadHomeCartSummary();
    
    // Handle cart summary collapse chevron rotation
    const cartSummaryCollapse = document.getElementById('cartSummaryCollapse');
    const cartSummaryChevron = document.getElementById('cart-summary-chevron');
    
    if (cartSummaryCollapse && cartSummaryChevron) {
        cartSummaryCollapse.addEventListener('show.bs.collapse', function() {
            cartSummaryChevron.style.transform = 'rotate(180deg)';
        });
        
        cartSummaryCollapse.addEventListener('hide.bs.collapse', function() {
            cartSummaryChevron.style.transform = 'rotate(0deg)';
        });
    }
});

// Load cart summary data for home page
function loadHomeCartSummary() {
    console.log('Loading home cart summary...');
    
    // First get cart count
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            updateHomeCartCount(data.count);
            
            if (data.count > 0) {
                // Load detailed cart data
                loadHomeCartDetails();
            } else {
                showHomeCartEmpty();
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
            showHomeCartEmpty();
        });
}

// Load detailed cart data for home page
function loadHomeCartDetails() {
    console.log('Loading detailed cart data for home page...');
    
    // Use the new cart summary API endpoint
    fetch('{{ route("cart.summary") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cart_data) {
                updateHomeCartDisplay(data.cart_data);
            } else {
                showHomeCartEmpty();
            }
        })
        .catch(error => {
            console.error('Error loading cart summary:', error);
            showHomeCartEmpty();
        });
}

// Update home cart display with data
function updateHomeCartDisplay(cartData) {
    console.log('Updating home cart display:', cartData);
    
    // Show cart items section
    document.getElementById('cart-empty-message').style.display = 'none';
    document.getElementById('home-cart-items').style.display = 'block';
    
    // Update detailed product breakdown
    updateHomeProductBreakdown(cartData.items);
    
    // Update totals
    document.getElementById('home-cart-subtotal').textContent = '₹' + cartData.subtotal.toFixed(2);
    document.getElementById('home-cgst-amount').textContent = '₹' + cartData.cgst_amount.toFixed(2);
    document.getElementById('home-sgst-amount').textContent = '₹' + cartData.sgst_amount.toFixed(2);
    document.getElementById('home-total-tax').textContent = '₹' + cartData.total_tax.toFixed(2);
    
    // Update delivery charge
    const homeDeliveryElement = document.getElementById('home-delivery-charge');
    if (cartData.delivery_charge === 0) {
        homeDeliveryElement.innerHTML = '<span class="text-success">FREE</span>';
    } else {
        homeDeliveryElement.textContent = '₹' + cartData.delivery_charge.toFixed(2);
    }
    
    // Update payment charge
    document.getElementById('home-payment-charge').textContent = '+₹' + cartData.payment_charge.toFixed(2);
    
    // Update grand total
    document.getElementById('home-cart-total').textContent = '₹' + cartData.grand_total.toFixed(2);
}

// Update home product breakdown
function updateHomeProductBreakdown(items) {
    const breakdownContainer = document.getElementById('home-detailed-product-breakdown');
    
    if (!items || items.length === 0) {
        breakdownContainer.innerHTML = '<p class="text-muted text-center">No items in cart</p>';
        return;
    }
    
    let breakdownHTML = '';
    items.forEach(item => {
        breakdownHTML += `
            <div class="home-cart-product-item" data-product-id="${item.id}">
                <div class="home-cart-product-name">${item.name}</div>
                <div class="home-cart-product-calculation">
                    Qty: ${item.quantity} × ₹${item.price.toFixed(2)}
                    ${item.tax_percentage > 0 ? ` GST: ${item.tax_percentage}% = ₹${item.tax_amount.toFixed(2)}` : ''}
                </div>
                <div class="home-cart-product-subtotal">₹${item.subtotal.toFixed(2)} +Tax</div>
            </div>
        `;
    });
    
    breakdownContainer.innerHTML = breakdownHTML;
}

// Show empty cart state
function showHomeCartEmpty() {
    document.getElementById('cart-empty-message').style.display = 'block';
    document.getElementById('home-cart-items').style.display = 'none';
    updateHomeCartCount(0);
}

// Update cart count in home page header
function updateHomeCartCount(count) {
    const countElement = document.getElementById('home-cart-count');
    if (countElement) {
        countElement.textContent = count;
        
        // Update the card header text
        const headerText = count === 0 ? 'Cart Summary (Empty)' : `Cart Summary (${count} item${count !== 1 ? 's' : ''})`;
        const headerElement = countElement.closest('h5');
        if (headerElement) {
            headerElement.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>${headerText.replace(count, `<span id="home-cart-count">${count}</span>`)}`;
        }
    }
}

// Override the original addToCart function to update home cart summary
if (typeof window.addToCart !== 'undefined') {
    const originalAddToCart = window.addToCart;
    window.addToCart = function(productId, quantity = 1) {
        originalAddToCart(productId, quantity);
        // Reload cart summary after adding item (faster refresh)
        setTimeout(() => {
            loadHomeCartSummary();
        }, 300);
    };
}

// Also override addToCartWithQuantity
if (typeof window.addToCartWithQuantity !== 'undefined') {
    const originalAddToCartWithQuantity = window.addToCartWithQuantity;
    window.addToCartWithQuantity = function(productId) {
        originalAddToCartWithQuantity(productId);
        // Reload cart summary after adding item (faster refresh)
        setTimeout(() => {
            loadHomeCartSummary();
        }, 300);
    };
}

// Fallback: If functions are not defined yet, define them
if (typeof window.addToCartWithQuantity === 'undefined') {
    window.addToCartWithQuantity = function(productId) {
        const quantity = document.getElementById(`quantity-${productId}`)?.value || 1;
        
        // Call the main addToCart function from the layout
        if (typeof window.addToCart === 'function') {
            window.addToCart(productId, quantity);
        } else {
            // Fallback implementation
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in navbar
                    if (typeof window.updateCartCount === 'function') {
                        window.updateCartCount();
                    }
                    
                    // Update home cart summary
                    loadHomeCartSummary();
                    
                    // Show success message
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message, 'success');
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('Error adding to cart', 'error');
                }
            });
        }
    };
}
</script>
@endsection
