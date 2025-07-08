@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name . ' - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->meta_keywords)

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Product Hero Section -->
<div class="product-hero-enhanced">
    <div class="container">
        <!-- Enhanced Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-enhanced">
                <li class="breadcrumb-item">
                    <a href="{{ route('shop') }}" class="text-white-50">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('category', $product->category->slug) }}" class="text-white-50">
                        <i class="fas fa-tag me-1"></i>{{ $product->category->name }}
                    </a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">
                    {{ Str::limit($product->name, 30) }}
                </li>
            </ol>
        </nav>
        
        <div class="hero-badges">
            <span class="hero-badge animate-bounce-in">
                <i class="fas fa-star me-1"></i>Premium Quality
            </span>
            @if($product->discount_percentage > 0)
                <span class="hero-badge sale-badge animate-bounce-in">
                    <i class="fas fa-tags me-1"></i>{{ $product->discount_percentage }}% OFF
                </span>
            @endif
            @if($product->is_featured)
                <span class="hero-badge featured-badge animate-bounce-in">
                    <i class="fas fa-crown me-1"></i>Featured Product
                </span>
            @endif
        </div>
    </div>
</div>

<div class="container product-page-container">
    <div class="row g-5">
        <!-- Enhanced Product Images -->
        <div class="col-lg-6">
            <div class="product-images-enhanced animate-fade-in">
                <div class="main-image-container">
                    @if($product->featured_image)
                        <img src="{{ $product->featured_image_url }}" 
                             class="main-image-enhanced" 
                             alt="{{ $product->name }}" 
                             id="mainImage">
                    @else
                        <div class="image-placeholder-enhanced">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="mt-3 text-muted">No Image Available</p>
                        </div>
                    @endif
                    
                    <!-- Image Zoom Overlay -->
                    <div class="image-zoom-overlay" onclick="openImageModal()">
                        <i class="fas fa-search-plus"></i>
                        <span>Click to Zoom</span>
                    </div>
                    
                    <!-- Image Badges -->
                    <div class="image-badges">
                        @if($product->discount_percentage > 0)
                            <span class="image-badge discount-badge">
                                {{ $product->discount_percentage }}% OFF
                            </span>
                        @endif
                        @if($product->is_featured)
                            <span class="image-badge featured-badge">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        @endif
                        @if(!$product->isInStock())
                            <span class="image-badge stock-badge">
                                Out of Stock
                            </span>
                        @endif
                    </div>
                </div>

                @if($product->images && count($product->images) > 0 || $product->featured_image)
                <div class="thumbnail-gallery">
                    @if($product->featured_image)
                        <div class="thumbnail-item">
                            <img src="{{ $product->featured_image_url }}" 
                                 class="thumbnail-image active" 
                                 alt="{{ $product->name }}" 
                                 onclick="changeMainImage(this)">
                        </div>
                    @endif
                    @foreach($product->image_urls ?? [] as $imageUrl)
                        <div class="thumbnail-item">
                            <img src="{{ $imageUrl }}" 
                                 class="thumbnail-image" 
                                 alt="{{ $product->name }}" 
                                 onclick="changeMainImage(this)">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Enhanced Product Details -->
        <div class="col-lg-6">
            <div class="product-details-enhanced animate-slide-up">
                <!-- Product Category & Badges -->
                <div class="product-meta">
                    <span class="category-tag">
                        <i class="fas fa-tag me-1"></i>{{ $product->category->name }}
                    </span>
                    @if($product->is_featured)
                        <span class="feature-tag">
                            <i class="fas fa-star me-1"></i>Featured
                        </span>
                    @endif
                    @if($product->stock <= 5 && $product->stock > 0)
                        <span class="stock-tag warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Limited Stock
                        </span>
                    @endif
                </div>

                <!-- Product Title -->
                <h1 class="product-title-enhanced">{{ $product->name }}</h1>

                <!-- Product Short Description -->
                @if($product->short_description)
                    <p class="product-subtitle">{{ $product->short_description }}</p>
                @endif

                <!-- Enhanced Price Section -->
                <div class="price-section-enhanced">
                    @if($product->discount_price)
                        <div class="price-display">
                            <span class="current-price">₹{{ number_format($product->discount_price, 2) }}</span>
                            <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                        </div>
                        <div class="savings-display">
                            <i class="fas fa-tag me-2"></i>
                            You Save: <strong>₹{{ number_format($product->price - $product->discount_price, 2) }}</strong>
                            ({{ $product->discount_percentage }}% off)
                        </div>
                    @else
                        <div class="price-display">
                            <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Product Info Cards -->
                <div class="product-info-cards">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-weight-hanging"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Weight</span>
                            <span class="info-value">{{ $product->weight ? $product->weight . ' ' . $product->weight_unit : 'N/A' }}</span>
                        </div>
                    </div>
                    
                    @if($product->sku)
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">SKU</span>
                            <span class="info-value">{{ $product->sku }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="info-card">
                        <div class="info-icon stock-icon {{ $product->isInStock() ? 'in-stock' : 'out-stock' }}">
                            <i class="fas fa-{{ $product->isInStock() ? 'check-circle' : 'times-circle' }}"></i>
                        </div>
                        <div class="info-content">
                            <span class="info-label">Availability</span>
                            <span class="info-value">
                                @if($product->stock > 10)
                                    <span class="text-success">In Stock ({{ $product->stock }} available)</span>
                                @elseif($product->stock > 0)
                                    <span class="text-warning">Limited Stock ({{ $product->stock }} left)</span>
                                @else
                                    <span class="text-danger">Out of Stock</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Add to Cart Section -->
                @if($product->isInStock())
                <div class="add-to-cart-enhanced">
                    <div class="quantity-section">
                        <label class="quantity-label">Quantity:</label>
                        <div class="quantity-selector-enhanced">
                            <button type="button" class="quantity-btn-enhanced" onclick="decrementQuantityMain()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   id="quantity" 
                                   class="quantity-input-enhanced" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $product->stock }}">
                            <button type="button" class="quantity-btn-enhanced" onclick="incrementQuantityMain()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="cart-actions">
                        <button onclick="addToCartEnhancedMain()" class="btn-cart-enhanced">
                            <i class="fas fa-cart-plus me-2"></i>
                            Add to Cart
                        </button>
                        <button onclick="buyNowEnhanced()" class="btn-buy-enhanced">
                            <i class="fas fa-bolt me-2"></i>
                            Buy Now
                        </button>
                    </div>
                    
                    <div class="cart-info">
                        <div class="shipping-info">
                            <i class="fas fa-shipping-fast me-2"></i>
                            Free shipping on orders above ₹500
                        </div>
                        <div class="delivery-info">
                            <i class="fas fa-clock me-2"></i>
                            Estimated delivery: 2-5 business days
                        </div>
                    </div>
                </div>
                @else
                <div class="out-of-stock-section">
                    <div class="alert alert-warning-enhanced">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        <div>
                            <h6 class="mb-1">Out of Stock</h6>
                            <p class="mb-0">This product is currently unavailable. Check back soon!</p>
                        </div>
                    </div>
                    <div class="notify-buttons">
                        <button class="btn btn-outline-enhanced" onclick="notifyWhenAvailable()">
                            <i class="fas fa-bell me-2"></i>Notify When Available
                        </button>
                        <a href="{{ route('products') }}" class="btn btn-primary-enhanced">
                            <i class="fas fa-search me-2"></i>Browse Similar Products
                        </a>
                    </div>
                </div>
                @endif

                <!-- Enhanced Share Section -->
                <div class="share-section-enhanced">
                    <h6 class="share-title">
                        <i class="fas fa-share-alt me-2"></i>Share this product:
                    </h6>
                    <div class="share-buttons">
                        <button onclick="shareWhatsApp()" class="share-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </button>
                        <button onclick="shareFacebook()" class="share-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </button>
                        <button onclick="shareTwitter()" class="share-btn twitter">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </button>
                        <button onclick="copyProductLink()" class="share-btn copy">
                            <i class="fas fa-link"></i>
                            <span>Copy Link</span>
                        </button>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="fas fa-shield-alt text-success"></i>
                        <span>100% Secure</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-undo text-primary"></i>
                        <span>Easy Returns</span>
                    </div>
                    <div class="trust-badge">
                        <i class="fas fa-award text-warning"></i>
                        <span>Quality Assured</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Product Description Tabs -->
    <div class="product-tabs-enhanced">
        <div class="tab-nav-enhanced">
            <button class="tab-btn-enhanced active" onclick="switchTab('description', this)">
                <i class="fas fa-file-alt me-2"></i>Description
            </button>
            <button class="tab-btn-enhanced" onclick="switchTab('specifications', this)">
                <i class="fas fa-list me-2"></i>Specifications
            </button>
            <button class="tab-btn-enhanced" onclick="switchTab('reviews', this)">
                <i class="fas fa-star me-2"></i>Reviews
            </button>
            <button class="tab-btn-enhanced" onclick="switchTab('shipping', this)">
                <i class="fas fa-truck me-2"></i>Shipping Info
            </button>
        </div>
        
        <div class="tab-content-enhanced">
            <div id="description" class="tab-pane-enhanced active">
                <div class="description-content">
                    <h5 class="mb-3">Product Description</h5>
                    <div class="description-text">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
            
            <div id="specifications" class="tab-pane-enhanced">
                <div class="specifications-content">
                    <h5 class="mb-3">Product Specifications</h5>
                    <div class="spec-grid">
                        @if($product->weight)
                        <div class="spec-item">
                            <span class="spec-label">Weight:</span>
                            <span class="spec-value">{{ $product->weight }} {{ $product->weight_unit }}</span>
                        </div>
                        @endif
                        @if($product->sku)
                        <div class="spec-item">
                            <span class="spec-label">SKU:</span>
                            <span class="spec-value">{{ $product->sku }}</span>
                        </div>
                        @endif
                        <div class="spec-item">
                            <span class="spec-label">Category:</span>
                            <span class="spec-value">{{ $product->category->name }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Availability:</span>
                            <span class="spec-value">{{ $product->isInStock() ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="reviews" class="tab-pane-enhanced">
                <div class="reviews-content">
                    <h5 class="mb-3">Customer Reviews</h5>
                    <div class="reviews-placeholder">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h6>No reviews yet</h6>
                        <p class="text-muted">Be the first to review this product!</p>
                        <button class="btn btn-primary-enhanced" onclick="writeReview()">
                            <i class="fas fa-edit me-2"></i>Write a Review
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="shipping" class="tab-pane-enhanced">
                <div class="shipping-content">
                    <h5 class="mb-3">Shipping Information</h5>
                    <div class="shipping-details">
                        <div class="shipping-item">
                            <i class="fas fa-shipping-fast text-success"></i>
                            <div>
                                <h6>Free Shipping</h6>
                                <p>On orders above ₹500</p>
                            </div>
                        </div>
                        <div class="shipping-item">
                            <i class="fas fa-clock text-primary"></i>
                            <div>
                                <h6>Delivery Time</h6>
                                <p>2-5 business days</p>
                            </div>
                        </div>
                        <div class="shipping-item">
                            <i class="fas fa-undo text-warning"></i>
                            <div>
                                <h6>Easy Returns</h6>
                                <p>7-day return policy</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Related Products -->
    @if($relatedProducts->count() > 0)
    <section class="section-enhanced related-products-section">
        <div class="section-header">
            <h2 class="section-title">You May Also Like</h2>
            <p class="section-subtitle">Discover more amazing products from our collection</p>
        </div>
        
        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-xl-3 col-lg-4 col-md-6">
                @include('enhanced-components.product-card', [
                    'product' => $relatedProduct,
                    'showQuantitySelector' => true,
                    'showDescription' => false,
                    'animationDelay' => $loop->iteration
                ])
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('category', $product->category->slug) }}" 
               class="btn btn-outline-enhanced btn-lg"
               onclick="triggerFireworks(this)">
                <i class="fas fa-eye me-2"></i>View All {{ $product->category->name }}
            </a>
        </div>
    </section>
    @endif
</div>

<!-- Enhanced Image Modal -->
<div class="image-modal" id="imageModal" onclick="closeImageModal()">
    <div class="image-modal-content">
        <button class="image-modal-close" onclick="closeImageModal()">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Product Image">
    </div>
</div>

<style>
    /* Enhanced Product Hero */
    .product-hero-enhanced {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 40px 0 60px 0;
        margin-bottom: -40px;
        position: relative;
        overflow: hidden;
    }
    
    .product-hero-enhanced::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.3;0.1" dur="3s" repeatCount="indefinite"/></circle></svg>');
        animation: float 20s linear infinite;
    }
    
    .hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
        position: relative;
        z-index: 1;
    }
    
    .hero-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .hero-badge.sale-badge {
        background: rgba(220, 53, 69, 0.9);
        animation: pulse 2s infinite;
    }
    
    .hero-badge.featured-badge {
        background: rgba(255, 193, 7, 0.9);
        color: #333;
    }
    
    /* Product Page Container */
    .product-page-container {
        background: white;
        border-radius: 25px 25px 0 0;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        padding: 60px 15px 40px 15px;
        position: relative;
        z-index: 2;
    }
    
    /* Enhanced Product Images */
    .product-images-enhanced {
        position: sticky;
        top: 100px;
    }
    
    .main-image-container {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        background: #f8f9fa;
    }
    
    .main-image-enhanced {
        width: 100%;
        height: 500px;
        object-fit: cover;
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }
    
    .main-image-enhanced:hover {
        transform: scale(1.05);
    }
    
    .image-placeholder-enhanced {
        height: 500px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .image-zoom-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
    }
    
    .main-image-container:hover .image-zoom-overlay {
        opacity: 1;
    }
    
    .image-zoom-overlay i {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .image-badges {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 5;
    }
    
    .image-badge {
        display: block;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
    }
    
    .image-badge.discount-badge {
        background: rgba(220, 53, 69, 0.9);
        color: white;
    }
    
    .image-badge.featured-badge {
        background: rgba(255, 193, 7, 0.9);
        color: #333;
    }
    
    .image-badge.stock-badge {
        background: rgba(108, 117, 125, 0.9);
        color: white;
    }
    
    /* Thumbnail Gallery */
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
    }
    
    .thumbnail-item {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .thumbnail-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        border: 3px solid transparent;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .thumbnail-image:hover,
    .thumbnail-image.active {
        border-color: var(--primary-color);
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
    }
    
    /* Enhanced Product Details */
    .product-details-enhanced {
        padding-left: 20px;
    }
    
    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .category-tag, .feature-tag, .stock-tag {
        padding: 6px 14px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .category-tag {
        background: var(--primary-gradient);
        color: white;
    }
    
    .feature-tag {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #333;
    }
    
    .stock-tag.warning {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        color: white;
    }
    
    .product-title-enhanced {
        font-size: 2.5rem;
        font-weight: 800;
        color: #333;
        margin-bottom: 15px;
        line-height: 1.2;
    }
    
    .product-subtitle {
        font-size: 1.25rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.5;
    }
    
    /* Enhanced Price Section */
    .price-section-enhanced {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        border: 2px solid #f0f0f0;
    }
    
    .price-display {
        margin-bottom: 10px;
    }
    
    .current-price {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-right: 15px;
    }
    
    .original-price {
        font-size: 1.5rem;
        color: #999;
        text-decoration: line-through;
    }
    
    .savings-display {
        color: #28a745;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    /* Product Info Cards */
    .product-info-cards {
        display: grid;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .info-card {
        display: flex;
        align-items: center;
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .info-icon.stock-icon.in-stock {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .info-icon.stock-icon.out-stock {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    
    .info-content {
        flex-grow: 1;
    }
    
    .info-label {
        display: block;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    /* Enhanced Add to Cart Section */
    .add-to-cart-enhanced {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 2px solid #f0f0f0;
        margin-bottom: 30px;
    }
    
    .quantity-section {
        margin-bottom: 25px;
    }
    
    .quantity-label {
        display: block;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .cart-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .btn-cart-enhanced, .btn-buy-enhanced {
        padding: 15px 20px;
        border-radius: 15px;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .btn-cart-enhanced {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
    }
    
    .btn-buy-enhanced {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .btn-cart-enhanced:hover, .btn-buy-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .cart-info {
        border-top: 1px solid #f0f0f0;
        padding-top: 20px;
    }
    
    .shipping-info, .delivery-info {
        color: #666;
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    /* Out of Stock Section */
    .out-of-stock-section {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 2px solid #f0f0f0;
        margin-bottom: 30px;
    }
    
    .alert-warning-enhanced {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffeaa7;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .notify-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    /* Enhanced Share Section */
    .share-section-enhanced {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 2px solid #f0f0f0;
        margin-bottom: 30px;
    }
    
    .share-title {
        color: #333;
        margin-bottom: 15px;
        font-weight: 700;
    }
    
    .share-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
    }
    
    .share-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px 10px;
        border-radius: 15px;
        border: 2px solid #f0f0f0;
        background: white;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        font-weight: 600;
    }
    
    .share-btn i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    
    .share-btn span {
        font-size: 12px;
    }
    
    .share-btn.whatsapp:hover {
        background: #25D366;
        color: white;
        border-color: #25D366;
        transform: translateY(-2px);
    }
    
    .share-btn.facebook:hover {
        background: #4267B2;
        color: white;
        border-color: #4267B2;
        transform: translateY(-2px);
    }
    
    .share-btn.twitter:hover {
        background: #1DA1F2;
        color: white;
        border-color: #1DA1F2;
        transform: translateY(-2px);
    }
    
    .share-btn.copy:hover {
        background: #666;
        color: white;
        border-color: #666;
        transform: translateY(-2px);
    }
    
    /* Trust Badges */
    .trust-badges {
        display: flex;
        justify-content: space-around;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #f0f0f0;
    }
    
    .trust-badge {
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        color: #666;
    }
    
    .trust-badge i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    
    /* Enhanced Product Tabs */
    .product-tabs-enhanced {
        background: white;
        border-radius: 25px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        margin: 60px 0;
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }
    
    .tab-nav-enhanced {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .tab-btn-enhanced {
        flex: 1;
        padding: 20px;
        border: none;
        background: transparent;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .tab-btn-enhanced::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .tab-btn-enhanced:hover {
        background: rgba(var(--primary-color), 0.1);
        color: var(--primary-color);
    }
    
    .tab-btn-enhanced.active {
        background: white;
        color: var(--primary-color);
    }
    
    .tab-btn-enhanced.active::after {
        transform: scaleX(1);
    }
    
    .tab-content-enhanced {
        padding: 40px;
    }
    
    .tab-pane-enhanced {
        display: none;
        animation: fadeIn 0.5s ease-out;
    }
    
    .tab-pane-enhanced.active {
        display: block;
    }
    
    .description-text {
        line-height: 1.8;
        color: #666;
        font-size: 16px;
    }
    
    .spec-grid {
        display: grid;
        gap: 15px;
    }
    
    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .spec-label {
        font-weight: 600;
        color: #333;
    }
    
    .spec-value {
        color: #666;
    }
    
    .reviews-placeholder {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    
    .shipping-details {
        display: grid;
        gap: 20px;
    }
    
    .shipping-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .shipping-item i {
        font-size: 2rem;
    }
    
    .shipping-item h6 {
        margin: 0;
        color: #333;
        font-weight: 700;
    }
    
    .shipping-item p {
        margin: 0;
        color: #666;
    }
    
    /* Enhanced Image Modal */
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        cursor: zoom-out;
    }
    
    .image-modal.show {
        display: flex;
    }
    
    .image-modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    
    .image-modal-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        backdrop-filter: blur(10px);
    }
    
    .image-modal img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .product-page-container {
            padding: 40px 15px 20px 15px;
        }
        
        .product-details-enhanced {
            padding-left: 0;
            margin-top: 30px;
        }
        
        .product-title-enhanced {
            font-size: 2rem;
        }
        
        .product-subtitle {
            font-size: 1.1rem;
        }
        
        .current-price {
            font-size: 2rem;
        }
        
        .cart-actions {
            grid-template-columns: 1fr;
        }
        
        .share-buttons {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .trust-badges {
            flex-direction: column;
            gap: 15px;
        }
        
        .tab-nav-enhanced {
            flex-direction: column;
        }
        
        .tab-content-enhanced {
            padding: 20px;
        }
        
        .spec-item {
            flex-direction: column;
            gap: 5px;
        }
        
        .shipping-item {
            flex-direction: column;
            text-align: center;
        }
        
        .hero-badges {
            justify-content: center;
        }
        
        .main-image-enhanced {
            height: 300px;
        }
        
        .product-images-enhanced {
            position: static;
        }
    }
    
    @media (max-width: 576px) {
        .product-title-enhanced {
            font-size: 1.75rem;
        }
        
        .current-price {
            font-size: 1.75rem;
        }
        
        .add-to-cart-enhanced,
        .share-section-enhanced,
        .out-of-stock-section {
            padding: 20px;
        }
        
        .share-buttons {
            grid-template-columns: 1fr;
        }
        
        .hero-badge {
            font-size: 12px;
            padding: 6px 12px;
        }
        
        .info-card {
            padding: 15px;
        }
        
        .main-image-enhanced {
            height: 250px;
        }
        
        .thumbnail-item {
            width: 60px;
            height: 60px;
        }
    }
</style>

<script>
// Enhanced Product Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Trigger welcome fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        setTimeout(() => {
            window.enhancedFireworks.triggerWelcomeFireworks();
        }, 1000);
    }
    
    console.log('🛍️ Enhanced Product Page initialized successfully!');
});

// Enhanced Image Functions
function changeMainImage(img) {
    const mainImage = document.getElementById('mainImage');
    const modalImage = document.getElementById('modalImage');
    
    if (mainImage && modalImage) {
        mainImage.src = img.src;
        modalImage.src = img.src;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail-image').forEach(thumb => {
            thumb.classList.remove('active');
        });
        img.classList.add('active');
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.triggerOnAction(img);
        }
    }
}

function openImageModal() {
    const modal = document.getElementById('imageModal');
    const mainImage = document.getElementById('mainImage');
    const modalImage = document.getElementById('modalImage');
    
    if (modal && mainImage && modalImage) {
        modalImage.src = mainImage.src;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Enhanced Quantity Functions
function incrementQuantityMain() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        const currentValue = parseInt(quantityInput.value) || 1;
        const maxValue = parseInt(quantityInput.getAttribute('max')) || 999;
        const newValue = Math.min(currentValue + 1, maxValue);
        
        quantityInput.value = newValue;
        
        // Visual feedback
        quantityInput.style.transform = 'scale(1.1)';
        quantityInput.style.background = '#e3f2fd';
        setTimeout(() => {
            quantityInput.style.transform = 'scale(1)';
            quantityInput.style.background = 'white';
        }, 200);
        
        // Show notification if at max
        if (newValue === maxValue && typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification(`Maximum quantity (${maxValue}) reached`, 'warning', 2000);
        }
    }
}

function decrementQuantityMain() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        const currentValue = parseInt(quantityInput.value) || 1;
        const minValue = parseInt(quantityInput.getAttribute('min')) || 1;
        const newValue = Math.max(currentValue - 1, minValue);
        
        quantityInput.value = newValue;
        
        // Visual feedback
        quantityInput.style.transform = 'scale(0.9)';
        quantityInput.style.background = '#ffebee';
        setTimeout(() => {
            quantityInput.style.transform = 'scale(1)';
            quantityInput.style.background = 'white';
        }, 200);
        
        // Show notification if at min
        if (newValue === minValue && typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification(`Minimum quantity is ${minValue}`, 'warning', 2000);
        }
    }
}

// Enhanced Add to Cart
function addToCartEnhancedMain() {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    const productId = {{ $product->id }};
    
    // Add loading state to button
    const cartBtn = document.querySelector('.btn-cart-enhanced');
    if (cartBtn) {
        const originalText = cartBtn.innerHTML;
        cartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
        cartBtn.disabled = true;
    }
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(cartBtn);
    }
    
    // Make AJAX request
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const quantityText = quantity > 1 ? ` (${quantity} items)` : '';
            
            if (typeof window.showEnhancedNotification === 'function') {
                window.showEnhancedNotification(
                    `${data.message || 'Product added to cart'}${quantityText}!`, 
                    'success', 
                    3000
                );
            }
            
            // Reset quantity to 1
            if (quantityInput) {
                quantityInput.value = 1;
            }
            
            // Update cart count
            if (typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }
            
            // Trigger celebration for higher quantities
            if (quantity > 1 && typeof window.enhancedFireworks !== 'undefined') {
                setTimeout(() => {
                    window.enhancedFireworks.createCelebrationBurst();
                }, 500);
            }
        } else {
            if (typeof window.showEnhancedNotification === 'function') {
                window.showEnhancedNotification(
                    data.message || 'Error adding product to cart', 
                    'error', 
                    4000
                );
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Network error. Please try again.', 'error', 4000);
        }
    })
    .finally(() => {
        // Restore button
        if (cartBtn) {
            cartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
            cartBtn.disabled = false;
        }
    });
}

// Buy Now Function
function buyNowEnhanced() {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    // Add to cart first, then redirect to checkout
    addToCartEnhancedMain();
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.createCelebrationBurst();
    }
    
    // Redirect to checkout after a short delay
    setTimeout(() => {
        window.location.href = '/checkout';
    }, 1500);
}

// Tab Switching
function switchTab(tabId, buttonElement) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane-enhanced').forEach(pane => {
        pane.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn-enhanced').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab pane
    const selectedPane = document.getElementById(tabId);
    if (selectedPane) {
        selectedPane.classList.add('active');
    }
    
    // Add active class to clicked button
    buttonElement.classList.add('active');
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(buttonElement);
    }
}

// Share Functions
function shareWhatsApp() {
    const text = encodeURIComponent(`Check out this amazing product: {{ $product->name }} - ${window.location.href}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
    
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Sharing on WhatsApp...', 'info', 2000);
    }
}

function shareFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Sharing on Facebook...', 'info', 2000);
    }
}

function shareTwitter() {
    const text = encodeURIComponent(`Check out: {{ $product->name }}`);
    const url = encodeURIComponent(window.location.href);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Sharing on Twitter...', 'info', 2000);
    }
}

function copyProductLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Link copied to clipboard!', 'success', 2000);
        }
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.createCelebrationBurst();
        }
    }).catch(function(err) {
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Failed to copy link', 'error', 2000);
        }
    });
}

// Notify When Available
function notifyWhenAvailable() {
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Notification request submitted!', 'success', 3000);
    }
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(event.target);
    }
    
    // Here you would typically make an AJAX request to save the notification request
}

// Write Review
function writeReview() {
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Review form coming soon!', 'info', 2000);
    }
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(event.target);
    }
}

// Global trigger fireworks function
function triggerFireworks(element) {
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(element);
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Initialize quantity input validation
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 999;
            
            if (isNaN(value) || value < min) {
                value = min;
            } else if (value > max) {
                value = max;
                if (typeof window.showEnhancedNotification === 'function') {
                    window.showEnhancedNotification(`Only ${max} items available in stock`, 'warning', 3000);
                }
            }
            
            this.value = value;
        });
    }
});
</script>
@endsection
