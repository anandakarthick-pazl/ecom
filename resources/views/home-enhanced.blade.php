@extends('layouts.app')

@section('title', 'Home - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Discover pure, natural herbal products made with love. Shop organic teas, skincare, and wellness products at ' . ($globalCompany->company_name ?? 'Your Store') . '.')

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Hero Banners Section -->
@if($banners->count() > 0)
<section class="hero-section-enhanced mb-5">
    <div id="heroCarousel" class="carousel slide carousel-enhanced" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <div class="carousel-image-container">
                    <img src="{{ Storage::url($banner->image) }}" 
                         class="d-block w-100 carousel-image" 
                         alt="{{ $banner->alt_text ?: $banner->title }}"
                         loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    <div class="carousel-overlay"></div>
                </div>
                <div class="carousel-caption-enhanced">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 text-center">
                                <h1 class="carousel-title animate-fade-in">{{ $banner->title }}</h1>
                                @if($banner->description)
                                    <p class="carousel-description animate-slide-up">{{ $banner->description }}</p>
                                @endif
                                @if($banner->link_url)
                                    <a href="{{ $banner->link_url }}" 
                                       class="btn btn-primary-enhanced btn-lg animate-bounce-in"
                                       onclick="triggerFireworks(this)">
                                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev carousel-control-enhanced" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon-enhanced">
                <i class="fas fa-chevron-left"></i>
            </span>
        </button>
        <button class="carousel-control-next carousel-control-enhanced" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon-enhanced">
                <i class="fas fa-chevron-right"></i>
            </span>
        </button>
        <div class="carousel-indicators-enhanced">
            @foreach($banners as $banner)
                <button type="button" 
                        data-bs-target="#heroCarousel" 
                        data-bs-slide-to="{{ $loop->index }}" 
                        class="{{ $loop->first ? 'active' : '' }}"
                        aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

<div class="container">
    <!-- Enhanced Featured Categories Section -->
    @if($categories->count() > 0)
    <section class="section-enhanced categories-section">
        <div class="section-header">
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle">Discover our carefully curated collection of premium herbal products</p>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-lg-4 col-md-6">
                <div class="card-enhanced category-card animate-scale-in animate-stagger-{{ $loop->iteration }}">
                    <div class="category-image-container">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" 
                                 class="category-image" 
                                 alt="{{ $category->name }}"
                                 loading="lazy">
                        @else
                            <div class="category-image-placeholder bg-light-green d-flex align-items-center justify-content-center">
                                <i class="fas fa-leaf fa-4x text-success"></i>
                            </div>
                        @endif
                        <div class="category-overlay">
                            <div class="category-overlay-content">
                                <i class="fas fa-arrow-right category-arrow"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="category-title">{{ $category->name }}</h5>
                        <p class="category-description">{{ Str::limit($category->description, 100) }}</p>
                        <a href="{{ route('category', $category->slug) }}" 
                           class="btn btn-primary-enhanced"
                           onclick="triggerFireworks(this)">
                            <i class="fas fa-shopping-bag me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Enhanced Product Menu Section -->
    <section class="section-enhanced product-menu-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-sparkles me-3"></i>Our Products
            </h2>
            <p class="section-subtitle">Explore our premium collection of natural and organic products</p>
        </div>
        
        <div class="product-menu-tabs-enhanced mb-5">
            <ul class="nav nav-pills-enhanced justify-content-center" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link-enhanced {{ $activeMenu === 'featured' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'featured']) }}" 
                       role="tab"
                       onclick="triggerFireworks(this)">
                        <i class="fas fa-star me-2"></i>
                        Featured Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link-enhanced {{ $activeMenu === 'all' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'all']) }}" 
                       role="tab"
                       onclick="triggerFireworks(this)">
                        <i class="fas fa-th-large me-2"></i>
                        All Products
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link-enhanced {{ $activeMenu === 'offers' ? 'active' : '' }}" 
                       href="{{ route('shop', ['menu' => 'offers']) }}" 
                       role="tab"
                       onclick="triggerFireworks(this)">
                        <i class="fas fa-tags me-2"></i>
                        Offer Products
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="tab-content-enhanced" id="productTabsContent">
            <!-- Featured Products Tab -->
            @if($activeMenu === 'featured')
            <div class="tab-pane fade show active" id="featured" role="tabpanel">
                @if($featuredProducts->count() > 0)
                <div class="row g-4">
                    @foreach($featuredProducts as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        @include('enhanced-components.product-card', [
                            'product' => $product,
                            'showQuantitySelector' => true,
                            'showDescription' => true,
                            'animationDelay' => $loop->iteration
                        ])
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state-enhanced">
                    <div class="empty-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="empty-title">No Featured Products Available</h3>
                    <p class="empty-description">Check back later for our specially curated featured products.</p>
                    <a href="{{ route('products') }}" class="btn btn-primary-enhanced">
                        <i class="fas fa-box me-2"></i>Browse All Products
                    </a>
                </div>
                @endif
            </div>
            @endif
            
            <!-- All Products Tab -->
            @if($activeMenu === 'all')
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        @include('enhanced-components.product-card', [
                            'product' => $product,
                            'showQuantitySelector' => true,
                            'showDescription' => true,
                            'animationDelay' => $loop->iteration
                        ])
                    </div>
                    @endforeach
                </div>
                
                <!-- Enhanced Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="pagination-enhanced d-flex justify-content-center mt-5">
                    {{ $products->appends(['menu' => 'all'])->links() }}
                </div>
                @endif
                @else
                <div class="empty-state-enhanced">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-title">No Products Available</h3>
                    <p class="empty-description">We're working hard to bring you amazing products. Check back soon!</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary-enhanced">
                        <i class="fas fa-refresh me-2"></i>Refresh Page
                    </a>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Offer Products Tab -->
            @if($activeMenu === 'offers')
            <div class="tab-pane fade show active" id="offers" role="tabpanel">
                @if($products->count() > 0)
                <div class="row g-4">
                    @foreach($products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        @include('enhanced-components.product-card', [
                            'product' => $product,
                            'showQuantitySelector' => true,
                            'showDescription' => true,
                            'cardClass' => 'offer-product-card',
                            'animationDelay' => $loop->iteration
                        ])
                    </div>
                    @endforeach
                </div>
                
                <!-- Enhanced Pagination -->
                @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                <div class="pagination-enhanced d-flex justify-content-center mt-5">
                    {{ $products->appends(['menu' => 'offers'])->links() }}
                </div>
                @endif
                @else
                <div class="empty-state-enhanced">
                    <div class="empty-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="empty-title">No Special Offers Available</h3>
                    <p class="empty-description">Stay tuned for exciting deals and special offers on our premium products!</p>
                    <a href="{{ route('products') }}" class="btn btn-primary-enhanced">
                        <i class="fas fa-box me-2"></i>Browse All Products
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </section>

    <!-- Enhanced Features Section -->
    <section class="section-enhanced features-section bg-light">
        <div class="section-header">
            <h2 class="section-title">Why Choose Us</h2>
            <p class="section-subtitle">We're committed to providing you with the finest herbal products and exceptional service</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-enhanced animate-scale-in animate-stagger-1">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h5 class="feature-title">100% Natural</h5>
                    <p class="feature-description">Pure herbal ingredients sourced from the finest locations around the world</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-enhanced animate-scale-in animate-stagger-2">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="feature-title">Free Delivery</h5>
                    <p class="feature-description">Complimentary shipping on orders above ₹500 to your doorstep</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-enhanced animate-scale-in animate-stagger-3">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h5 class="feature-title">Quality Assured</h5>
                    <p class="feature-description">Every product is carefully crafted and tested to meet the highest standards</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-enhanced animate-scale-in animate-stagger-4">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="feature-title">24/7 Support</h5>
                    <p class="feature-description">Our dedicated customer service team is always here to help you</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Newsletter Section -->
    <section class="section-enhanced newsletter-section">
        <div class="newsletter-container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="newsletter-content animate-bounce-in">
                        <h3 class="newsletter-title">
                            <i class="fas fa-envelope-open me-3"></i>
                            Stay Updated with Our Latest Offers
                        </h3>
                        <p class="newsletter-description">
                            Subscribe to our newsletter and be the first to know about new products, special discounts, and health tips.
                        </p>
                        <form class="newsletter-form" action="#" method="POST">
                            @csrf
                            <div class="input-group newsletter-input-group">
                                <input type="email" 
                                       class="form-control newsletter-input" 
                                       placeholder="Enter your email address" 
                                       required>
                                <button class="btn btn-primary-enhanced newsletter-btn" 
                                        type="submit"
                                        onclick="triggerFireworks(this)">
                                    <i class="fas fa-paper-plane me-2"></i>Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Enhanced Hero Section */
    .hero-section-enhanced {
        margin-bottom: 80px;
    }
    
    .carousel-enhanced {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.2);
    }
    
    .carousel-image-container {
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    
    .carousel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 8s ease-in-out;
    }
    
    .carousel-item.active .carousel-image {
        transform: scale(1.05);
    }
    
    .carousel-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.4), rgba(0,0,0,0.1));
    }
    
    .carousel-caption-enhanced {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 5;
    }
    
    .carousel-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        line-height: 1.2;
    }
    
    .carousel-description {
        font-size: 1.5rem;
        margin-bottom: 30px;
        text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        opacity: 0.95;
    }
    
    .carousel-control-enhanced {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
    }
    
    .carousel-control-enhanced:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-control-prev-icon-enhanced,
    .carousel-control-next-icon-enhanced {
        font-size: 20px;
        color: white;
    }
    
    .carousel-indicators-enhanced {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 5;
    }
    
    .carousel-indicators-enhanced button {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        background: transparent;
        transition: all 0.3s ease;
    }
    
    .carousel-indicators-enhanced button.active {
        background: white;
        transform: scale(1.2);
    }
    
    /* Enhanced Category Cards */
    .category-card {
        height: 100%;
        overflow: hidden;
    }
    
    .category-image-container {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .category-card:hover .category-image {
        transform: scale(1.1);
    }
    
    .category-image-placeholder {
        height: 100%;
        background: linear-gradient(135deg, var(--light-green) 0%, #e8f5e8 100%);
    }
    
    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.3), transparent);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.4s ease;
    }
    
    .category-card:hover .category-overlay {
        opacity: 1;
    }
    
    .category-overlay-content {
        background: rgba(255,255,255,0.9);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(0);
        transition: transform 0.4s ease;
    }
    
    .category-card:hover .category-overlay-content {
        transform: scale(1);
    }
    
    .category-arrow {
        color: var(--primary-color);
        font-size: 20px;
    }
    
    .category-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .category-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    /* Enhanced Product Menu Tabs */
    .product-menu-tabs-enhanced {
        display: flex;
        justify-content: center;
    }
    
    .nav-pills-enhanced {
        background: white;
        padding: 8px;
        border-radius: 50px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        display: flex;
        gap: 5px;
    }
    
    .nav-link-enhanced {
        background: transparent;
        border: none;
        color: #666;
        padding: 15px 25px;
        border-radius: 25px;
        transition: all 0.4s ease;
        font-weight: 600;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }
    
    .nav-link-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        transition: left 0.4s ease;
        z-index: -1;
        border-radius: 25px;
    }
    
    .nav-link-enhanced:hover {
        color: white;
        transform: translateY(-2px);
    }
    
    .nav-link-enhanced:hover::before {
        left: 0;
    }
    
    .nav-link-enhanced.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
    }
    
    .nav-link-enhanced.active::before {
        left: 0;
    }
    
    /* Tab Content Animation */
    .tab-content-enhanced .tab-pane {
        animation: tabFadeIn 0.6s ease-out;
    }
    
    @keyframes tabFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Offer Product Card Enhancement */
    .offer-product-card {
        border: 2px solid #ffc107;
        box-shadow: 0 8px 30px rgba(255, 193, 7, 0.2);
        position: relative;
    }
    
    .offer-product-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #ffc107, #ff9800, #ffc107);
        border-radius: 22px;
        z-index: -1;
        animation: offerGlow 2s ease-in-out infinite alternate;
    }
    
    @keyframes offerGlow {
        0% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    .offer-product-card:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 25px 70px rgba(255, 193, 7, 0.3);
    }
    
    /* Enhanced Newsletter Section */
    .newsletter-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border-radius: 25px;
        margin: 80px 0;
        position: relative;
        overflow: hidden;
    }
    
    .newsletter-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.3;0.1" dur="4s" repeatCount="indefinite"/></circle><circle cx="80" cy="30" r="1.5" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.4;0.1" dur="3s" repeatCount="indefinite"/></circle><circle cx="60" cy="70" r="1" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.2;0.1" dur="5s" repeatCount="indefinite"/></circle></svg>');
        animation: float 25s linear infinite;
    }
    
    .newsletter-container {
        position: relative;
        z-index: 1;
        padding: 60px 40px;
    }
    
    .newsletter-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .newsletter-description {
        font-size: 1.25rem;
        margin-bottom: 40px;
        opacity: 0.9;
        line-height: 1.6;
    }
    
    .newsletter-input-group {
        max-width: 500px;
        margin: 0 auto;
    }
    
    .newsletter-input {
        border: none;
        padding: 15px 20px;
        font-size: 16px;
        border-radius: 25px 0 0 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .newsletter-input:focus {
        box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        border-color: transparent;
    }
    
    .newsletter-btn {
        border-radius: 0 25px 25px 0;
        padding: 15px 25px;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Enhanced Pagination */
    .pagination-enhanced .pagination {
        border-radius: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        background: white;
        padding: 10px;
        border: 1px solid #f0f0f0;
    }
    
    .pagination-enhanced .page-link {
        border: none;
        color: #666;
        font-weight: 600;
        margin: 0 2px;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .pagination-enhanced .page-link:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }
    
    .pagination-enhanced .page-item.active .page-link {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .carousel-image-container {
            height: 300px;
        }
        
        .carousel-title {
            font-size: 2.5rem;
        }
        
        .carousel-description {
            font-size: 1.2rem;
        }
        
        .section-enhanced {
            padding: 60px 0;
        }
        
        .section-title {
            font-size: 2.5rem;
        }
        
        .category-image-container {
            height: 200px;
        }
        
        .nav-pills-enhanced {
            flex-direction: column;
            gap: 5px;
        }
        
        .nav-link-enhanced {
            text-align: center;
            padding: 12px 20px;
        }
        
        .newsletter-title {
            font-size: 2rem;
        }
        
        .newsletter-input-group {
            flex-direction: column;
            gap: 10px;
        }
        
        .newsletter-input,
        .newsletter-btn {
            border-radius: 25px;
        }
    }
    
    @media (max-width: 576px) {
        .carousel-title {
            font-size: 2rem;
        }
        
        .carousel-description {
            font-size: 1rem;
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .category-image-container {
            height: 180px;
        }
        
        .newsletter-container {
            padding: 40px 20px;
        }
        
        .newsletter-title {
            font-size: 1.75rem;
        }
        
        .newsletter-description {
            font-size: 1rem;
        }
    }
</style>

<script>
// Enhanced Home Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Trigger welcome fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        setTimeout(() => {
            window.enhancedFireworks.triggerWelcomeFireworks();
        }, 1000);
    }
    
    // Enhanced carousel auto-play with pause on hover
    const carousel = document.getElementById('heroCarousel');
    if (carousel) {
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            pause: 'hover'
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                carouselInstance.prev();
            } else if (e.key === 'ArrowRight') {
                carouselInstance.next();
            }
        });
    }
    
    // Newsletter form enhancement
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('.newsletter-input').value;
            const btn = this.querySelector('.newsletter-btn');
            
            // Add loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...';
            btn.disabled = true;
            
            // Simulate subscription (replace with actual implementation)
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed!';
                
                if (typeof window.showEnhancedNotification === 'function') {
                    window.showEnhancedNotification(
                        'Thank you for subscribing to our newsletter!', 
                        'success', 
                        4000
                    );
                }
                
                // Trigger celebration
                if (typeof window.enhancedFireworks !== 'undefined') {
                    window.enhancedFireworks.createCelebrationBurst();
                }
                
                // Reset form after delay
                setTimeout(() => {
                    this.reset();
                    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Subscribe';
                    btn.disabled = false;
                }, 2000);
            }, 1500);
        });
    }
    
    // Add scroll-triggered animations for better performance
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                
                // Add animation classes when element comes into view
                if (element.classList.contains('feature-enhanced')) {
                    element.style.animationDelay = '0s';
                    element.classList.add('animate-bounce-in');
                }
                
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    // Observe feature cards for scroll animations
    document.querySelectorAll('.feature-enhanced').forEach(feature => {
        observer.observe(feature);
    });
    
    console.log('🏠 Enhanced Home Page initialized successfully!');
});

// Global trigger fireworks function for onclick events
function triggerFireworks(element) {
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(element);
    }
}
</script>
@endsection
