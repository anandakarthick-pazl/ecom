@extends('layouts.app')

@section('title', 'Home - Herbal Bliss')
@section('meta_description', 'Discover pure, natural herbal products made with love. Shop organic teas, skincare, and wellness products at Herbal Bliss.')

@section('content')
<!-- Hero Banners with Modern Design -->
@if($banners->count() > 0)
<section class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach($banners as $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" 
                    class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <div class="banner-container">
                    <img src="{{ Storage::url($banner->image) }}" class="d-block w-100 banner-image" alt="{{ $banner->alt_text ?: $banner->title }}">
                    <div class="banner-overlay"></div>
                    <div class="carousel-caption modern-caption">
                        <div class="caption-content">
                            <h1 class="display-4 fw-bold mb-3">{{ $banner->title }}</h1>
                            @if($banner->description)
                                <p class="lead mb-4">{{ $banner->description }}</p>
                            @endif
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" class="btn btn-primary btn-lg modern-btn">
                                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev modern-control" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <i class="fas fa-chevron-left fa-2x"></i>
        </button>
        <button class="carousel-control-next modern-control" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <i class="fas fa-chevron-right fa-2x"></i>
        </button>
        @endif
    </div>
</section>
@endif

<div class="container-fluid px-lg-5">
    <!-- Compact Categories Section -->
    @if($categories->count() > 0)
    <section class="categories-section mb-5">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle text-muted">Discover our curated collection of natural products</p>
        </div>
        <div class="categories-grid">
            @foreach($categories->take(6) as $category)
            <div class="category-card-modern">
                <a href="{{ route('category', $category->slug) }}" class="category-link">
                    <div class="category-image-container">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" class="category-image" alt="{{ $category->name }}">
                        @else
                            <div class="category-placeholder">
                                <i class="fas fa-leaf"></i>
                            </div>
                        @endif
                        <div class="category-overlay">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                    <div class="category-content">
                        <h6 class="category-name">{{ $category->name }}</h6>
                        <small class="category-count text-muted">{{ $category->products_count ?? 0 }} products</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @if($categories->count() > 6)
        <div class="text-center mt-4">
            <a href="{{ route('categories') }}" class="btn btn-outline-primary">
                <i class="fas fa-th-large me-2"></i>View All Categories
            </a>
        </div>
        @endif
    </section>
    @endif

    <!-- Enhanced Product Menu Tabs -->
    <section class="product-menu-section mb-5">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Our Products</h2>
            <p class="section-subtitle text-muted">Handpicked natural products for your wellness journey</p>
        </div>
        
        <div class="modern-tabs-container">
            <ul class="modern-tabs" id="productTabs" role="tablist">
                <li class="modern-tab-item" role="presentation">
                    <a class="modern-tab-link {{ $activeMenu === 'featured' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'featured']) }}" 
                       role="tab">
                        <i class="fas fa-star"></i>
                        <span>Featured</span>
                        <div class="tab-indicator"></div>
                    </a>
                </li>
                <li class="modern-tab-item" role="presentation">
                    <a class="modern-tab-link {{ $activeMenu === 'all' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'all']) }}" 
                       role="tab">
                        <i class="fas fa-th-large"></i>
                        <span>All Products</span>
                        <div class="tab-indicator"></div>
                    </a>
                </li>
                <li class="modern-tab-item" role="presentation">
                    <a class="modern-tab-link {{ $activeMenu === 'offers' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'offers']) }}" 
                       role="tab">
                        <i class="fas fa-fire"></i>
                        <span>Hot Deals</span>
                        <div class="tab-indicator"></div>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content modern-tab-content" id="productTabsContent">
            <!-- Featured Products Tab -->
            @if($activeMenu === 'featured')
            <div class="tab-pane fade show active" id="featured" role="tabpanel">
                @if($featuredProducts->count() > 0)
                <div class="products-grid">
                    @foreach($featuredProducts as $product)
                    <div class="product-card-modern">
                        <div class="product-image-container">
                            @if($product->featured_image)
                                <img src="{{ Storage::url($product->featured_image) }}" class="product-image" alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            
                            @if($product->discount_percentage > 0)
                                <div class="product-badge sale-badge">
                                    <span>{{ $product->discount_percentage }}% OFF</span>
                                </div>
                            @endif
                            
                            <div class="product-badges">
                                <span class="badge-featured">
                                    <i class="fas fa-star"></i>
                                </span>
                            </div>
                            
                            <div class="product-overlay">
                                <div class="quick-actions">
                                    <a href="{{ route('product', $product->slug) }}" class="quick-btn" title="Quick View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" class="quick-btn" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-content">
                            <div class="product-category">
                                <small>{{ $product->category->name }}</small>
                            </div>
                            <h6 class="product-title">
                                <a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
                            </h6>
                            <p class="product-description">{{ Str::limit($product->short_description, 60) }}</p>
                            
                            <div class="product-footer">
                                <div class="price-section">
                                    @if($product->discount_price)
                                        <span class="current-price">₹{{ number_format($product->discount_price, 2) }}</span>
                                        <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <div class="product-actions-modern">
                                    @if($product->isInStock())
                                        <div class="quantity-controls">
                                            <button class="qty-btn" onclick="decrementQuantity({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="qty-input" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                                            <button class="qty-btn" onclick="incrementQuantity({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button onclick="addToCartWithQuantity({{ $product->id }})" class="add-to-cart-btn">
                                            <i class="fas fa-cart-plus"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    @else
                                        <button class="out-of-stock-btn" disabled>
                                            <i class="fas fa-ban"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>No Featured Products</h4>
                    <p>Check back later for our featured products.</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- All Products Tab -->
            @if($activeMenu === 'all')
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @if($products->count() > 0)
                <div class="products-grid">
                    @foreach($products as $product)
                    <div class="product-card-modern">
                        <div class="product-image-container">
                            @if($product->featured_image)
                                <img src="{{ Storage::url($product->featured_image) }}" class="product-image" alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            
                            @if($product->discount_percentage > 0)
                                <div class="product-badge sale-badge">
                                    <span>{{ $product->discount_percentage }}% OFF</span>
                                </div>
                            @endif
                            
                            <div class="product-overlay">
                                <div class="quick-actions">
                                    <a href="{{ route('product', $product->slug) }}" class="quick-btn" title="Quick View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" class="quick-btn" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-content">
                            <div class="product-category">
                                <small>{{ $product->category->name }}</small>
                            </div>
                            <h6 class="product-title">
                                <a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
                            </h6>
                            <p class="product-description">{{ Str::limit($product->short_description, 60) }}</p>
                            
                            <div class="product-footer">
                                <div class="price-section">
                                    @if($product->discount_price)
                                        <span class="current-price">₹{{ number_format($product->discount_price, 2) }}</span>
                                        <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                <div class="product-actions-modern">
                                    @if($product->isInStock())
                                        <div class="quantity-controls">
                                            <button class="qty-btn" onclick="decrementQuantity({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="qty-input" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                                            <button class="qty-btn" onclick="incrementQuantity({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button onclick="addToCartWithQuantity({{ $product->id }})" class="add-to-cart-btn">
                                            <i class="fas fa-cart-plus"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    @else
                                        <button class="out-of-stock-btn" disabled>
                                            <i class="fas fa-ban"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Modern Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="modern-pagination-container">
                    {{ $products->appends(['menu' => 'all'])->links() }}
                </div>
                @endif
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h4>No Products Available</h4>
                    <p>Check back later for our products.</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Offer Products Tab -->
            @if($activeMenu === 'offers')
            <div class="tab-pane fade show active" id="offers" role="tabpanel">
                @if($products->count() > 0)
                <div class="products-grid offers-grid">
                    @foreach($products as $product)
                    <div class="product-card-modern offer-card">
                        <div class="product-image-container">
                            @if($product->featured_image)
                                <img src="{{ Storage::url($product->featured_image) }}" class="product-image" alt="{{ $product->name }}">
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            
                            <div class="product-badge hot-deal-badge">
                                <span>{{ $product->discount_percentage }}% OFF</span>
                            </div>
                            
                            <div class="product-badges">
                                <span class="badge-hot-deal">
                                    <i class="fas fa-fire"></i>
                                </span>
                            </div>
                            
                            <div class="savings-badge">
                                <span>Save ₹{{ number_format($product->price - $product->discount_price, 2) }}</span>
                            </div>
                            
                            <div class="product-overlay">
                                <div class="quick-actions">
                                    <a href="{{ route('product', $product->slug) }}" class="quick-btn" title="Quick View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" class="quick-btn" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-content">
                            <div class="product-category">
                                <small>{{ $product->category->name }}</small>
                            </div>
                            <h6 class="product-title">
                                <a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
                            </h6>
                            <p class="product-description">{{ Str::limit($product->short_description, 60) }}</p>
                            
                            <div class="product-footer">
                                <div class="price-section offer-price">
                                    <span class="current-price">₹{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                                    <div class="savings-info">
                                        <small class="text-success">
                                            <i class="fas fa-tag"></i> You Save ₹{{ number_format($product->price - $product->discount_price, 2) }}
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="product-actions-modern">
                                    @if($product->isInStock())
                                        <div class="quantity-controls">
                                            <button class="qty-btn" onclick="decrementQuantity({{ $product->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="qty-input" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                                            <button class="qty-btn" onclick="incrementQuantity({{ $product->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button onclick="addToCartWithQuantity({{ $product->id }})" class="add-to-cart-btn offer-btn">
                                            <i class="fas fa-cart-plus"></i>
                                            <span>Grab Deal</span>
                                        </button>
                                    @else
                                        <button class="out-of-stock-btn" disabled>
                                            <i class="fas fa-ban"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Modern Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="modern-pagination-container">
                    {{ $products->appends(['menu' => 'offers'])->links() }}
                </div>
                @endif
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h4>No Hot Deals Available</h4>
                    <p>Check back later for special offers and deals.</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </section>

    <!-- Enhanced Features Section -->
    <section class="features-section">
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="feature-content">
                    <h5>100% Natural</h5>
                    <p>Pure herbal ingredients</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="feature-content">
                    <h5>Free Delivery</h5>
                    <p>On orders above ₹500</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="feature-content">
                    <h5>Quality Assured</h5>
                    <p>Handmade with care</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="feature-content">
                    <h5>24/7 Support</h5>
                    <p>Call us anytime</p>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
/* Modern Banner Styles */
.hero-section {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
}

.banner-container {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.banner-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 100%);
}

.modern-caption {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 3;
    width: 90%;
}

.caption-content h1 {
    color: white;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    margin-bottom: 1rem;
}

.caption-content .lead {
    color: rgba(255,255,255,0.9);
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.modern-btn {
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 10px 30px rgba(0,123,255,0.3);
    transition: all 0.3s ease;
}

.modern-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,123,255,0.4);
}

.modern-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.9);
    border: none;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: var(--theme-color-primary, #007bff);
}

.modern-control:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.carousel-control-prev {
    left: 30px;
}

.carousel-control-next {
    right: 30px;
}

/* Section Headers */
.section-header {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--theme-color-text, #333);
    margin-bottom: 0.5rem;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(45deg, var(--theme-color-primary, #007bff), var(--theme-color-accent, #28a745));
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.1rem;
    margin-top: 1rem;
}

/* Compact Categories Grid */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.category-card-modern {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.category-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}

.category-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.category-image-container {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--theme-color-primary, #007bff), var(--theme-color-accent, #28a745));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card-modern:hover .category-overlay {
    opacity: 1;
}

.category-overlay i {
    color: white;
    font-size: 1.5rem;
}

.category-content {
    padding: 1rem;
    text-align: center;
}

.category-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--theme-color-text, #333);
}

.category-count {
    font-size: 0.85rem;
}

/* Modern Tabs */
.modern-tabs-container {
    display: flex;
    justify-content: center;
    margin-bottom: 3rem;
}

.modern-tabs {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.modern-tab-item {
    position: relative;
}

.modern-tab-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    text-decoration: none;
    color: var(--theme-color-text, #666);
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.modern-tab-link i {
    font-size: 1.1rem;
}

.modern-tab-link:hover {
    color: var(--theme-color-primary, #007bff);
    background: rgba(0,123,255,0.05);
}

.modern-tab-link.active {
    color: var(--theme-color-primary, #007bff);
    background: rgba(0,123,255,0.1);
}

.tab-indicator {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--theme-color-primary, #007bff);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.modern-tab-link.active .tab-indicator {
    transform: scaleX(1);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.product-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
}

.product-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card-modern:hover .product-image {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 3rem;
}

.product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.sale-badge span {
    background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.hot-deal-badge span {
    background: linear-gradient(45deg, #ff9500, #ff7675);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badges {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}

.badge-featured, .badge-hot-deal {
    background: rgba(255,255,255,0.9);
    color: var(--theme-color-primary, #007bff);
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.badge-hot-deal {
    color: #ff6b6b;
}

.savings-badge {
    position: absolute;
    bottom: 15px;
    left: 15px;
    z-index: 2;
}

.savings-badge span {
    background: rgba(40, 167, 69, 0.9);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 500;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card-modern:hover .product-overlay {
    opacity: 1;
}

.quick-actions {
    display: flex;
    gap: 1rem;
}

.quick-btn {
    background: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--theme-color-primary, #007bff);
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.quick-btn:hover {
    background: var(--theme-color-primary, #007bff);
    color: white;
    transform: scale(1.1);
}

.product-content {
    padding: 1.5rem;
}

.product-category {
    margin-bottom: 0.5rem;
}

.product-category small {
    color: var(--theme-color-primary, #007bff);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
}

.product-title {
    margin-bottom: 0.75rem;
    font-weight: 600;
    line-height: 1.3;
}

.product-title a {
    color: var(--theme-color-text, #333);
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: var(--theme-color-primary, #007bff);
}

.product-description {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.product-footer {
    margin-top: auto;
}

.price-section {
    margin-bottom: 1rem;
}

.current-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--theme-color-primary, #007bff);
}

.original-price {
    font-size: 0.9rem;
    text-decoration: line-through;
    color: #6c757d;
    margin-left: 0.5rem;
}

.savings-info {
    margin-top: 0.25rem;
}

.product-actions-modern {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.qty-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--theme-color-text, #333);
    transition: all 0.3s ease;
}

.qty-btn:hover {
    background: var(--theme-color-primary, #007bff);
    color: white;
    border-color: var(--theme-color-primary, #007bff);
}

.qty-input {
    width: 50px;
    height: 35px;
    text-align: center;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-weight: 500;
}

.add-to-cart-btn {
    background: linear-gradient(45deg, var(--theme-color-primary, #007bff), var(--theme-color-accent, #28a745));
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.3);
}

.offer-btn {
    background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
}

.offer-btn:hover {
    box-shadow: 0 8px 25px rgba(255,107,107,0.3);
}

.out-of-stock-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0.7;
}

/* Offer Cards Special Styling */
.offer-card {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box, 
                linear-gradient(45deg, #ff6b6b, #ee5a6f) border-box;
}

.offer-card:hover {
    box-shadow: 0 20px 50px rgba(255,107,107,0.2);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

.empty-state h4 {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #adb5bd;
}

/* Features Section */
.features-section {
    margin: 4rem 0;
}

.features-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 25px;
}

.feature-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    background: linear-gradient(45deg, var(--theme-color-primary, #007bff), var(--theme-color-accent, #28a745));
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.feature-content h5 {
    margin-bottom: 0.25rem;
    color: var(--theme-color-text, #333);
    font-weight: 600;
}

.feature-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

/* Modern Pagination */
.modern-pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .modern-tabs {
        flex-direction: column;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .banner-container {
        height: 300px;
    }
    
    .features-container {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 2rem 1rem;
    }
    
    .feature-card {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Animation Classes */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-up {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to elements
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);
    
    // Observe product cards and category cards
    document.querySelectorAll('.product-card-modern, .category-card-modern, .feature-card').forEach(card => {
        observer.observe(card);
    });
    
    // Auto-advance carousel
    const carousel = document.querySelector('#heroCarousel');
    if (carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel, {
            interval: 6000,
            pause: 'hover'
        });
    }
});
</script>
@endpush
@endsection