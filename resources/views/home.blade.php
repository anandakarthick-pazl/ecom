@extends('layouts.app')

@section('title', 'Home - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Discover premium quality products at ' . ($globalCompany->company_name ?? 'Your Store') . '. Shop now for the best deals and exceptional service.')

@section('content')
<!-- Modern Hero Section -->
@if($banners->count() > 0)
<section class="modern-hero-section">
    <div class="hero-container">
        <div id="modernHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                @foreach($banners as $index => $banner)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <div class="hero-slide" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('{{ $banner->image_url }}')">
                        <div class="container">
                            <div class="row align-items-center min-vh-75">
                                <div class="col-lg-8 col-md-10">
                                    <div class="hero-content" data-aos="fade-up" data-aos-delay="{{ $index * 200 }}">
                                        <h1 class="hero-title">{{ $banner->title }}</h1>
                                        @if($banner->description)
                                            <p class="hero-description">{{ $banner->description }}</p>
                                        @endif
                                        @if($banner->link_url)
                                            <div class="hero-actions">
                                                <a href="{{ $banner->link_url }}" class="btn-hero-primary">
                                                    <span>Shop Now</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                <a href="{{ route('products') }}" class="btn-hero-secondary">
                                                    <span>Explore Products</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($banners->count() > 1)
            <!-- Custom Navigation -->
            <div class="hero-navigation">
                <button class="hero-nav-btn hero-prev" type="button" data-bs-target="#modernHeroCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="hero-nav-btn hero-next" type="button" data-bs-target="#modernHeroCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Custom Indicators -->
            <div class="hero-indicators">
                @foreach($banners as $index => $banner)
                <button type="button" data-bs-target="#modernHeroCarousel" data-bs-slide-to="{{ $index }}" 
                        class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $index + 1 }}"></button>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
@else
<!-- Default Hero Section when no banners -->
<section class="default-hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4">Welcome to {{ $globalCompany->company_name ?? 'Your Store' }}</h1>
                    <p class="lead mb-4">Discover premium quality products curated just for you. Experience exceptional service and unbeatable value.</p>
                    <div class="hero-actions">
                        <a href="{{ route('products') }}" class="btn-hero-primary">
                            <span>Shop Now</span>
                            <i class="fas fa-shopping-bag"></i>
                        </a>
                        @if($categories->count() > 0)
                            <a href="#categories" class="btn-hero-secondary">
                                <span>Browse Categories</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-visual">
                    <div class="hero-shape-1"></div>
                    <div class="hero-shape-2"></div>
                    <div class="hero-shape-3"></div>
                    @if($globalCompany->company_logo)
                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="hero-logo">
                    @else
                        <div class="hero-icon">
                            <i class="fas fa-store"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<div class="main-content">
    <!-- Enhanced Cart Summary Widget -->
    <section class="smart-cart-widget" id="smart-cart-summary">
        <div class="container">
            <div class="cart-widget-card" data-aos="fade-up">
                <div class="cart-widget-header" data-bs-toggle="collapse" data-bs-target="#smartCartCollapse">
                    <div class="cart-widget-info">
                        <div class="cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count-badge" id="smart-cart-count">0</span>
                        </div>
                        <div class="cart-text">
                            <h5 class="cart-title">Smart Cart Summary</h5>
                            <p class="cart-subtitle">View your items and totals</p>
                        </div>
                    </div>
                    <div class="cart-toggle">
                        <i class="fas fa-chevron-down" id="smart-cart-chevron"></i>
                    </div>
                </div>
                
                <div class="collapse" id="smartCartCollapse">
                    <div class="cart-widget-body">
                        <!-- Empty State -->
                        <div id="smart-cart-empty" class="cart-empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h6>Your cart is empty</h6>
                            <p>Add some products to get started</p>
                            <a href="{{ route('products') }}" class="btn-empty-cart">Start Shopping</a>
                        </div>
                        
                        <!-- Cart Items -->
                        <div id="smart-cart-items" class="cart-items-container" style="display: none;">
                            <div class="cart-products" id="smart-cart-products"></div>
                            
                            <div class="cart-summary">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span id="smart-cart-subtotal">₹0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span>CGST</span>
                                    <span id="smart-cgst-amount">₹0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span>SGST</span>
                                    <span id="smart-sgst-amount">₹0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span>Delivery</span>
                                    <span id="smart-delivery-charge">₹50.00</span>
                                </div>
                                <div class="summary-row total-row">
                                    <span>Total</span>
                                    <span id="smart-cart-total">₹0.00</span>
                                </div>
                            </div>
                            
                            <div class="cart-actions">
                                <a href="{{ route('cart.index') }}" class="btn-cart-action btn-view">
                                    <i class="fas fa-eye"></i>
                                    <span>View Cart</span>
                                </a>
                                <a href="{{ route('checkout') }}" class="btn-cart-action btn-checkout">
                                    <i class="fas fa-lock"></i>
                                    <span>Checkout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Categories Section -->
    @if($categories->count() > 0)
    <section class="modern-categories-section" id="categories">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-badge">Categories</div>
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-description">Explore our carefully curated collections designed for your needs</p>
            </div>
            
            <div class="categories-grid" data-aos="fade-up" data-aos-delay="200">
                @foreach($categories as $category)
                <div class="category-card-modern" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="category-image-container">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="category-image-modern">
                        @else
                            <div class="category-placeholder-modern">
                                <i class="fas fa-tag"></i>
                            </div>
                        @endif
                        @if($category->products_count > 0)
                            <div class="category-count-badge">{{ $category->products_count }}</div>
                        @endif
                    </div>
                    <div class="category-content">
                        <h3 class="category-name">{{ $category->name }}</h3>
                        <a href="{{ route('category', $category->slug) }}" class="category-link">
                            <span>Explore</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Enhanced Products Section -->
    <section class="modern-products-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-badge">Products</div>
                <h2 class="section-title">Our Premium Collection</h2>
                <p class="section-description">Discover quality products handpicked for excellence</p>
            </div>

            <!-- Modern Product Tabs -->
            <div class="product-tabs-container" data-aos="fade-up" data-aos-delay="200">
                <div class="product-tabs">
                    <a href="{{ route('shop', ['menu' => 'featured']) }}" 
                       class="tab-link {{ $activeMenu === 'featured' ? 'active' : '' }}">
                        <div class="tab-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <span>Featured</span>
                    </a>
                    <a href="{{ route('shop', ['menu' => 'all']) }}" 
                       class="tab-link {{ $activeMenu === 'all' ? 'active' : '' }}">
                        <div class="tab-icon">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <span>All Products</span>
                    </a>
                    <a href="{{ route('shop', ['menu' => 'offers']) }}" 
                       class="tab-link {{ $activeMenu === 'offers' ? 'active' : '' }}">
                        <div class="tab-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <span>Offers</span>
                    </a>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-container" data-aos="fade-up" data-aos-delay="400">
                @if(($activeMenu === 'featured' && $featuredProducts->count() > 0) || 
                    (($activeMenu === 'all' || $activeMenu === 'offers') && $products->count() > 0))
                    
                    <div class="products-grid-modern">
                        @php
                            $displayProducts = $activeMenu === 'featured' ? $featuredProducts : $products;
                        @endphp
                        
                        @foreach($displayProducts as $product)
                        <div class="product-card-modern" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <!-- Product Image -->
                            <div class="product-image-container-modern">
                                @if($product->featured_image)
                                    <img src="{{ $product->featured_image_url }}" alt="{{ $product->name }}" class="product-image-modern">
                                @else
                                    <div class="product-placeholder-modern">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                
                                <!-- Product Badges -->
                                <div class="product-badges">
                                    @if($product->discount_percentage > 0)
                                        <span class="product-badge badge-discount">{{ $product->discount_percentage }}% OFF</span>
                                    @endif
                                    @if($activeMenu === 'offers')
                                        <span class="product-badge badge-offer">OFFER</span>
                                    @endif
                                    @if($activeMenu === 'featured')
                                        <span class="product-badge badge-featured">
                                            <i class="fas fa-star"></i>
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="product-quick-actions">
                                    <a href="{{ route('product', $product->slug) }}" class="quick-action-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Product Content -->
                            <div class="product-content-modern">
                                <div class="product-category">{{ $product->category->name }}</div>
                                <h3 class="product-title">{{ $product->name }}</h3>
                                <p class="product-description">{{ Str::limit($product->short_description, 80) }}</p>
                                
                                <!-- Price Section -->
                                <div class="product-price-section">
                                    @if($product->discount_price)
                                        <div class="price-current">₹{{ number_format($product->discount_price, 2) }}</div>
                                        <div class="price-original">₹{{ number_format($product->price, 2) }}</div>
                                        @if($activeMenu === 'offers')
                                            <div class="price-savings">Save ₹{{ number_format($product->price - $product->discount_price, 2) }}</div>
                                        @endif
                                    @else
                                        <div class="price-current">₹{{ number_format($product->price, 2) }}</div>
                                    @endif
                                </div>
                                
                                <!-- Product Actions -->
                                <div class="product-actions-modern">
                                    @if($product->isInStock())
                                        <!-- Quantity Selector -->
                                        <div class="quantity-selector-modern">
                                            <button type="button" class="qty-btn qty-minus" onclick="decrementQuantityModern({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" id="quantity-{{ $product->id }}" class="qty-input" value="1" min="1" max="{{ $product->stock }}" readonly>
                                            <button type="button" class="qty-btn qty-plus" onclick="incrementQuantityModern({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Add to Cart Button -->
                                        <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn-add-to-cart-modern">
                                            <div class="btn-content">
                                                <i class="fas fa-shopping-cart"></i>
                                                <span>Add to Cart</span>
                                            </div>
                                            <div class="btn-loading">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </div>
                                        </button>
                                    @else
                                        <button class="btn-out-of-stock" disabled>
                                            <i class="fas fa-times"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                    <div class="pagination-container" data-aos="fade-up">
                        {{ $products->appends(['menu' => $activeMenu])->links() }}
                    </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="products-empty-state" data-aos="fade-up">
                        <div class="empty-icon">
                            @if($activeMenu === 'featured')
                                <i class="fas fa-star"></i>
                            @elseif($activeMenu === 'offers')
                                <i class="fas fa-tags"></i>
                            @else
                                <i class="fas fa-box"></i>
                            @endif
                        </div>
                        <h3>No {{ ucfirst($activeMenu) }} Products Available</h3>
                        <p>Check back later for new additions to our collection.</p>
                        <a href="{{ route('products') }}" class="btn-explore-all">Explore All Products</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Modern Features Section -->
    <section class="modern-features-section">
        <div class="container">
            <div class="features-grid" data-aos="fade-up">
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Quality Assured</h3>
                    <p class="feature-description">Premium products with guaranteed quality and authenticity</p>
                </div>
                
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="feature-title">Fast Delivery</h3>
                    <p class="feature-description">Quick and reliable shipping to your doorstep</p>
                </div>
                
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-description">Round-the-clock customer service for your peace of mind</p>
                </div>
                
                <div class="feature-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h3 class="feature-title">Easy Returns</h3>
                    <p class="feature-description">Hassle-free return policy within 30 days</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modern Home Page Styles -->
<style>
/* =================================================================
   MODERN HERO SECTION
   ================================================================= */
.modern-hero-section {
    position: relative;
    min-height: 70vh;
    overflow: hidden;
}

.hero-slide {
    min-height: 70vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    display: flex;
    align-items: center;
}

.hero-content {
    color: white;
    z-index: 2;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-description {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    line-height: 1.6;
    opacity: 0.9;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-hero-primary, .btn-hero-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-hero-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
    color: white;
}

.btn-hero-secondary {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
}

.btn-hero-secondary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-2px);
    color: white;
}

.hero-navigation {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 2rem;
    z-index: 3;
}

.hero-nav-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.hero-nav-btn:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
    transform: scale(1.1);
}

.hero-indicators {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 3;
}

.hero-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.5);
    background: transparent;
    transition: all 0.3s ease;
}

.hero-indicators button.active,
.hero-indicators button:hover {
    background: white;
    border-color: white;
}

/* Default Hero Section */
.default-hero-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, var(--background-color) 0%, rgba(255,255,255,0.5) 100%);
    position: relative;
    overflow: hidden;
}

.hero-visual {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-shape-1, .hero-shape-2, .hero-shape-3 {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.hero-shape-1 {
    width: 200px;
    height: 200px;
    top: 10%;
    right: 10%;
    animation-delay: 0s;
}

.hero-shape-2 {
    width: 150px;
    height: 150px;
    bottom: 20%;
    left: 20%;
    animation-delay: 2s;
}

.hero-shape-3 {
    width: 100px;
    height: 100px;
    top: 50%;
    left: 50%;
    animation-delay: 4s;
}

.hero-logo {
    max-width: 200px;
    max-height: 200px;
    z-index: 2;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
}

.hero-icon {
    font-size: 8rem;
    color: var(--primary-color);
    z-index: 2;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
}

/* =================================================================
   SMART CART WIDGET
   ================================================================= */
.smart-cart-widget {
    padding: 2rem 0;
    position: sticky;
    top: 90px;
    z-index: 100;
}

.cart-widget-card {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    overflow: hidden;
    transition: all 0.3s ease;
}

.cart-widget-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    transition: all 0.3s ease;
}

.cart-widget-header:hover {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
}

.cart-widget-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cart-icon {
    position: relative;
    font-size: 1.5rem;
}

.cart-count-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

.cart-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.cart-subtitle {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

.cart-toggle {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.cart-widget-card .collapsed .cart-toggle {
    transform: rotate(-90deg);
}

.cart-widget-body {
    padding: 1.5rem;
}

.cart-empty-state {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-icon {
    font-size: 3rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.cart-empty-state h6 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.cart-empty-state p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.btn-empty-cart {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-empty-cart:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    color: white;
}

.cart-products {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1rem;
}

.cart-product-item {
    background: rgba(var(--primary-color), 0.05);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-left: 3px solid var(--primary-color);
}

.cart-product-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.cart-product-details {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.cart-product-price {
    font-weight: 600;
    color: var(--primary-color);
    text-align: right;
}

.cart-summary {
    background: rgba(0,0,0,0.02);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.total-row {
    border-top: 1px solid rgba(0,0,0,0.1);
    padding-top: 0.5rem;
    margin-top: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
}

.cart-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.btn-cart-action {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view {
    background: rgba(var(--primary-color), 0.1);
    color: var(--primary-color);
    border: 1px solid rgba(var(--primary-color), 0.2);
}

.btn-view:hover {
    background: rgba(var(--primary-color), 0.2);
    color: var(--primary-color);
}

.btn-checkout {
    background: var(--primary-color);
    color: white;
}

.btn-checkout:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    color: white;
}

/* =================================================================
   MODERN CATEGORIES SECTION
   ================================================================= */
.modern-categories-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,249,250,0.8) 100%);
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-badge {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.section-description {
    font-size: 1.1rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.category-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.category-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.category-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.category-image-modern {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-card-modern:hover .category-image-modern {
    transform: scale(1.1);
}

.category-placeholder-modern {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
}

.category-count-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255,255,255,0.9);
    color: var(--primary-color);
    padding: 0.5rem;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.85rem;
    min-width: 35px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.category-content {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.category-name {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.category-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.category-link:hover {
    color: var(--secondary-color);
    transform: translateX(5px);
}

/* =================================================================
   MODERN PRODUCTS SECTION
   ================================================================= */
.modern-products-section {
    padding: 5rem 0;
}

.product-tabs-container {
    display: flex;
    justify-content: center;
    margin-bottom: 3rem;
}

.product-tabs {
    display: flex;
    background: rgba(255,255,255,0.5);
    border-radius: 50px;
    padding: 0.5rem;
    gap: 0.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.tab-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-radius: 40px;
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.tab-link.active {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.tab-link:hover:not(.active) {
    background: rgba(255,255,255,0.8);
    color: var(--primary-color);
}

.tab-icon {
    font-size: 1.1rem;
}

.products-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
}

.product-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
}

.product-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

.product-image-container-modern {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image-modern {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card-modern:hover .product-image-modern {
    transform: scale(1.05);
}

.product-placeholder-modern {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-secondary);
}

.product-badges {
    position: absolute;
    top: 1rem;
    left: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.product-badge {
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.badge-discount {
    background: rgba(220, 53, 69, 0.9);
    color: white;
}

.badge-offer {
    background: rgba(40, 167, 69, 0.9);
    color: white;
}

.badge-featured {
    background: rgba(255, 193, 7, 0.9);
    color: white;
}

.product-quick-actions {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card-modern:hover .product-quick-actions {
    opacity: 1;
}

.quick-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.quick-action-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.product-content-modern {
    padding: 1.5rem;
}

.product-category {
    color: var(--text-secondary);
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.product-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.product-price-section {
    margin-bottom: 1.5rem;
}

.price-current {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.price-original {
    font-size: 1rem;
    color: var(--text-secondary);
    text-decoration: line-through;
    margin-bottom: 0.25rem;
}

.price-savings {
    font-size: 0.85rem;
    color: #28a745;
    font-weight: 600;
}

.product-actions-modern {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.quantity-selector-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.05);
    border-radius: 10px;
    overflow: hidden;
}

.qty-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    color: var(--primary-color);
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.qty-btn:hover {
    background: var(--primary-color);
    color: white;
}

.qty-input {
    width: 60px;
    height: 40px;
    border: none;
    background: transparent;
    text-align: center;
    font-weight: 600;
    color: var(--text-primary);
}

.btn-add-to-cart-modern {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    border-radius: 10px;
    padding: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-add-to-cart-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.btn-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: opacity 0.3s ease;
}

.btn-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-add-to-cart-modern.loading .btn-content {
    opacity: 0;
}

.btn-add-to-cart-modern.loading .btn-loading {
    opacity: 1;
}

.btn-out-of-stock {
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 1rem;
    font-weight: 600;
    cursor: not-allowed;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.products-empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.products-empty-state .empty-icon {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.products-empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.products-empty-state p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.btn-explore-all {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-explore-all:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
    color: white;
}

/* =================================================================
   MODERN FEATURES SECTION
   ================================================================= */
.modern-features-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(248,249,250,0.8) 100%);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-card {
    text-align: center;
    padding: 2rem 1rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.feature-description {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* =================================================================
   RESPONSIVE DESIGN
   ================================================================= */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-description {
        font-size: 1.1rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary {
        justify-content: center;
        padding: 1rem 1.5rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .products-grid-modern {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .product-tabs {
        flex-direction: column;
        padding: 1rem;
        border-radius: 20px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .smart-cart-widget {
        position: relative;
        top: auto;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .products-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .cart-actions {
        grid-template-columns: 1fr;
    }
}

/* =================================================================
   ANIMATIONS
   ================================================================= */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* AOS Animation Overrides */
[data-aos="fade-up"] {
    transform: translateY(30px);
    opacity: 0;
}

[data-aos="fade-up"].aos-animate {
    transform: translateY(0);
    opacity: 1;
}

[data-aos="zoom-in"] {
    transform: scale(0.8);
    opacity: 0;
}

[data-aos="zoom-in"].aos-animate {
    transform: scale(1);
    opacity: 1;
}
</style>

<!-- Enhanced JavaScript -->
<script>
// Modern quantity controls
function incrementQuantityModern(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.max);
    
    if (currentValue < maxValue) {
        input.value = currentValue + 1;
        input.dispatchEvent(new Event('change'));
    }
}

function decrementQuantityModern(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    const currentValue = parseInt(input.value);
    const minValue = parseInt(input.min) || 1;
    
    if (currentValue > minValue) {
        input.value = currentValue - 1;
        input.dispatchEvent(new Event('change'));
    }
}

// Enhanced cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Load smart cart summary
    loadSmartCartSummary();
    
    // Handle smart cart collapse
    const smartCartCollapse = document.getElementById('smartCartCollapse');
    const smartCartChevron = document.getElementById('smart-cart-chevron');
    
    if (smartCartCollapse && smartCartChevron) {
        smartCartCollapse.addEventListener('show.bs.collapse', function() {
            smartCartChevron.style.transform = 'rotate(180deg)';
        });
        
        smartCartCollapse.addEventListener('hide.bs.collapse', function() {
            smartCartChevron.style.transform = 'rotate(0deg)';
        });
    }
    
    // Enhanced add to cart buttons
    document.querySelectorAll('.btn-add-to-cart-modern').forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
            setTimeout(() => {
                this.classList.remove('loading');
            }, 1000);
        });
    });
});

// Load smart cart summary
function loadSmartCartSummary() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            updateSmartCartCount(data.count);
            
            if (data.count > 0) {
                loadSmartCartDetails();
            } else {
                showSmartCartEmpty();
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            showSmartCartEmpty();
        });
}

// Load detailed cart data
function loadSmartCartDetails() {
    fetch('{{ route("cart.summary") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cart_data) {
                updateSmartCartDisplay(data.cart_data);
            } else {
                showSmartCartEmpty();
            }
        })
        .catch(error => {
            console.error('Error loading cart details:', error);
            showSmartCartEmpty();
        });
}

// Update smart cart display
function updateSmartCartDisplay(cartData) {
    document.getElementById('smart-cart-empty').style.display = 'none';
    document.getElementById('smart-cart-items').style.display = 'block';
    
    // Update products
    updateSmartCartProducts(cartData.items);
    
    // Update totals
    document.getElementById('smart-cart-subtotal').textContent = '₹' + cartData.subtotal.toFixed(2);
    document.getElementById('smart-cgst-amount').textContent = '₹' + cartData.cgst_amount.toFixed(2);
    document.getElementById('smart-sgst-amount').textContent = '₹' + cartData.sgst_amount.toFixed(2);
    
    const deliveryElement = document.getElementById('smart-delivery-charge');
    if (cartData.delivery_charge === 0) {
        deliveryElement.innerHTML = '<span style="color: #28a745;">FREE</span>';
    } else {
        deliveryElement.textContent = '₹' + cartData.delivery_charge.toFixed(2);
    }
    
    document.getElementById('smart-cart-total').textContent = '₹' + cartData.grand_total.toFixed(2);
}

// Update smart cart products
function updateSmartCartProducts(items) {
    const container = document.getElementById('smart-cart-products');
    
    if (!items || items.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No items in cart</p>';
        return;
    }
    
    let html = '';
    items.forEach(item => {
        html += `
            <div class="cart-product-item">
                <div class="cart-product-name">${item.name}</div>
                <div class="cart-product-details">
                    Qty: ${item.quantity} × ₹${item.price.toFixed(2)}
                    ${item.tax_percentage > 0 ? ` | GST: ${item.tax_percentage}%` : ''}
                </div>
                <div class="cart-product-price">₹${item.subtotal.toFixed(2)}</div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Show empty cart state
function showSmartCartEmpty() {
    document.getElementById('smart-cart-empty').style.display = 'block';
    document.getElementById('smart-cart-items').style.display = 'none';
    updateSmartCartCount(0);
}

// Update cart count
function updateSmartCartCount(count) {
    const countElement = document.getElementById('smart-cart-count');
    if (countElement) {
        countElement.textContent = count;
        
        if (count === 0) {
            countElement.style.display = 'none';
        } else {
            countElement.style.display = 'flex';
        }
    }
}

// Override original add to cart function
if (typeof window.addToCart !== 'undefined') {
    const originalAddToCart = window.addToCart;
    window.addToCart = function(productId, quantity = 1) {
        originalAddToCart(productId, quantity);
        setTimeout(() => {
            loadSmartCartSummary();
        }, 300);
    };
}

// Enhanced addToCartWithQuantity function
if (typeof window.addToCartWithQuantity === 'undefined') {
    window.addToCartWithQuantity = function(productId) {
        const quantity = document.getElementById(`quantity-${productId}`)?.value || 1;
        const button = document.querySelector(`button[onclick*="addToCartWithQuantity(${productId})"]`);
        
        if (button) {
            button.classList.add('loading');
        }
        
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
                loadSmartCartSummary();
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount();
                }
                if (typeof window.showToast === 'function') {
                    window.showToast(data.message, 'success');
                }
                // Reset quantity
                const input = document.getElementById(`quantity-${productId}`);
                if (input) input.value = 1;
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
        })
        .finally(() => {
            if (button) {
                button.classList.remove('loading');
            }
        });
    };
}
</script>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-out-quart',
        once: true,
        offset: 50
    });
</script>
@endsection
