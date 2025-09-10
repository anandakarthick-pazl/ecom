@extends('layouts.app')

@section('title')
    Home - {{ $globalCompany->company_name ?? 'Your Store' }}
@endsection

@section('meta_description')
    Discover quality products at {{ $globalCompany->company_name ?? 'Your Store' }}. Shop our curated collection with confidence.
@endsection

@section('content')
<!-- Enhanced Announcement Banner -->
@if($globalCompany->announcement_text)
<div class="announcement-banner">
    <div class="marquee">
        <span>{{ $globalCompany->announcement_text }}</span>
    </div>
</div>
@endif

<!-- Enhanced Contact Information Banner -->
@if($globalCompany)
<div class="contact-info-banner">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="contact-header">
                    <h4 class="contact-title">
                        <i class="fas fa-phone-alt"></i>
                        Get in Touch
                    </h4>
                    <p class="contact-subtitle">We're here to help you 24/7</p>
                </div>
                <div class="contact-items-wrapper">
                    @if($globalCompany->whatsapp_number)
                    <div class="contact-item whatsapp-item">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $globalCompany->whatsapp_number) }}?text=Hi, I'm interested in your products" 
                           target="_blank" class="contact-link" title="Chat with us on WhatsApp">
                            <div class="contact-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">WhatsApp</span>
                                <span class="contact-value">{{ $globalCompany->whatsapp_number }}</span>
                                <span class="contact-action">Click to Chat</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-external-link-alt"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    @if($globalCompany->mobile_number)
                    <div class="contact-item mobile-item">
                        <a href="tel:{{ $globalCompany->mobile_number }}" class="contact-link" title="Call us on mobile">
                            <div class="contact-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">Mobile</span>
                                <span class="contact-value">{{ $globalCompany->mobile_number }}</span>
                                <span class="contact-action">Tap to Call</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-phone"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    @if($globalCompany->company_phone)
                    <div class="contact-item phone-item">
                        <a href="tel:{{ $globalCompany->company_phone }}" class="contact-link" title="Call our office">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">Office</span>
                                <span class="contact-value">{{ $globalCompany->company_phone }}</span>
                                <span class="contact-action">Call Now</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-building"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    @if($globalCompany->company_email)
                    <div class="contact-item email-item">
                        <a href="mailto:{{ $globalCompany->company_email }}" class="contact-link" title="Send us an email">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">Email</span>
                                <span class="contact-value">{{ $globalCompany->company_email }}</span>
                                <span class="contact-action">Send Email</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    @if($globalCompany->alternate_phone)
                    <div class="contact-item alternate-item">
                        <a href="tel:{{ $globalCompany->alternate_phone }}" class="contact-link" title="Alternative contact number">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">Alternate</span>
                                <span class="contact-value">{{ $globalCompany->alternate_phone }}</span>
                                <span class="contact-action">Call Now</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-phone-volume"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    @if(isset($globalCompany->gpay_number) && $globalCompany->gpay_number)
                    <div class="contact-item gpay-item">
                        <a href="upi://pay?pa={{ $globalCompany->gpay_number }}@paytm&pn={{ urlencode($globalCompany->company_name ?? 'Store') }}&cu=INR" class="contact-link" title="Pay via Google Pay">
                            <div class="contact-icon">
                                <i class="fab fa-google-pay"></i>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">Google Pay</span>
                                <span class="contact-value">{{ $globalCompany->gpay_number }}</span>
                                <span class="contact-action">Tap to Pay</span>
                            </div>
                            <div class="contact-badge">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($banners->count() > 0)
<section class="hero-section-compact">
    <!-- Debug: Found {{ $banners->count() }} banner(s) -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators">
            @foreach($banners as $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" 
                    class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
       
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <div class="hero-container-compact">
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" 
                             class="hero-image" 
                             alt="{{ $banner->alt_text ?: $banner->title }}" 
                             onerror="console.error('Banner image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';">
                    @else
                        <div class="hero-placeholder">
                            <div class="hero-gradient"></div>
                        </div>
                    @endif
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <div class="carousel-btn-compact">
                <i class="fas fa-chevron-left"></i>
            </div>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <div class="carousel-btn-compact">
                <i class="fas fa-chevron-right"></i>
            </div>
        </button>
        @endif
    </div>
</section>
@else
<!-- No Banners Fallback -->
<section class="hero-section-compact">
    <div class="hero-container-compact">
        <div class="hero-placeholder">
            <div class="hero-gradient"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content text-center">
            <h1 class="text-white">Welcome to Our Store</h1>
            <p class="text-white">Discover amazing products at great prices</p>
            <a href="{{ route('products') }}" class="btn btn-primary">Shop Now</a>
        </div>
    </div>
</section>
@endif

<div class="main-container">
    <!-- Enhanced Categories Section with Images -->
    @if($categories->count() > 0)
    <section class="categories-section-enhanced">
        <div class="section-header-enhanced">
            <h2 class="section-title-enhanced">Shop by Category</h2>
            <p class="section-subtitle-enhanced">Explore our carefully curated collections</p>
        </div>
        <div class="categories-grid-enhanced">
            @foreach($categories->take(6) as $category)
            <div class="category-card-enhanced">
                <a href="{{ route('category', $category->slug) }}" class="category-link-enhanced">
                    <div class="category-image-wrapper-enhanced">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" 
                                 class="category-image-enhanced" 
                                 alt="{{ $category->name }}"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ asset('images/fallback/category-placeholder.png') }}'; this.parentElement.classList.add('fallback-image-enhanced');">
                        @else
                            <div class="category-placeholder-enhanced">
                                <i class="fas fa-th-large category-icon-enhanced"></i>
                            </div>
                        @endif
                        @if($category->products_count > 0)
                            <div class="category-badge-enhanced">
                                <span class="badge bg-primary">{{ $category->products_count }}</span>
                            </div>
                        @endif
                        <div class="category-overlay-enhanced"></div>
                    </div>
                    <div class="category-info-enhanced">
                        <h3 class="category-name-enhanced">{{ $category->name }}</h3>
                        <span class="category-count-enhanced">{{ $category->products_count ?? 0 }} products</span>
                        @if($category->description)
                            <p class="category-desc-enhanced">{{ Str::limit($category->description, 60) }}</p>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @if($categories->count() > 6)
            <div class="text-center mt-4">
                <a href="{{ route('products') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-th-large me-2"></i>View All Categories
                </a>
            </div>
        @endif
    </section>
    @else
    <section class="categories-section-enhanced">
        <div class="empty-categories-enhanced">
            <i class="fas fa-th-large fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">Categories Coming Soon</h3>
            <p class="text-muted">We're organizing our products into categories. Check back soon!</p>
        </div>
    </section>
    @endif

    <!-- Compact Products Section -->
    <section class="products-section-compact">
        <div class="section-header-compact">
            <h2 class="section-title-compact">Our Products</h2>
            <p class="section-subtitle-compact">Handpicked quality products for you</p>
        </div>
        
        <!-- Product Tabs -->
        <div class="product-tabs-compact">
            <nav class="tabs-nav-compact">
                <a href="{{ route('shop', ['menu' => 'all']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'all' ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>All Products</span>
                </a>
                <a href="{{ route('shop', ['menu' => 'featured']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'featured' ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Featured</span>
                </a>
                <a href="{{ route('shop', ['menu' => 'offers']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'offers' ? 'active' : '' }}">
                    <i class="fas fa-fire"></i>
                    <span>Hot Deals</span>
                </a>
            </nav>
        </div>
        
        <!-- View Toggle Controls -->
        <div class="view-controls-section">
            <div class="view-controls">
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid" title="Grid View">
                        <i class="fas fa-th"></i>
                        <span>Grid</span>
                    </button>
                    <button class="view-btn" data-view="list" title="List View">
                        <i class="fas fa-list"></i>
                        <span>List</span>
                    </button>
                </div>
                <div class="sort-controls">
                    <select class="sort-select" id="sortProducts">
                        <option value="default">Sort by: Default</option>
                        <option value="name_asc">Name: A to Z</option>
                        <option value="name_desc">Name: Z to A</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-container">
            @if($activeMenu === 'all')
                @if($products->count() > 0)
                    <div class="products-grid-compact" id="products-grid">
                        @foreach($products as $product)
                            @include('partials.product-card-modern', ['product' => $product])
                        @endforeach
                    </div>
                    
                    <!-- Hidden table format for list view -->
                    <div class="products-list-table-container" id="products-list-table" style="display: none;">
                        @include('partials.products-list-table', ['products' => $products])
                    </div>
                    @if(($frontendPaginationSettings['enabled'] ?? true) && isset($products) && method_exists($products, 'appends'))
                        <div class="pagination-container">
                            {{ $products->appends(['menu' => 'all'])->links() }}
                        </div>
                    @endif
                @else
                    @include('partials.empty-state', ['icon' => 'box', 'title' => 'No Products Available', 'message' => 'New products coming soon'])
                @endif
            @endif
            
            @if($activeMenu === 'featured')
                @if($featuredProducts->count() > 0)
                    <div class="products-grid-compact">
                        @foreach($featuredProducts as $product)
                            @include('partials.product-card-modern', ['product' => $product, 'featured' => true])
                        @endforeach
                    </div>
                @else
                    @include('partials.empty-state', ['icon' => 'star', 'title' => 'No Featured Products', 'message' => 'Check back later for featured items'])
                @endif
            @endif
            
            @if($activeMenu === 'offers')
                @if($products->count() > 0)
                    <div class="products-grid-compact offers">
                        @foreach($products as $product)
                            @include('partials.product-card-modern', ['product' => $product, 'offer' => true])
                        @endforeach
                    </div>
                    @if(($frontendPaginationSettings['enabled'] ?? true) && isset($products) && method_exists($products, 'appends'))
                        <div class="pagination-container">
                            {{ $products->appends(['menu' => 'offers'])->links() }}
                        </div>
                    @endif
                @else
                    @include('partials.empty-state', ['icon' => 'fire', 'title' => 'No Offers Available', 'message' => 'Stay tuned for amazing deals'])
                @endif
            @endif
        </div>
    </section>

    <!-- Compact Features Section -->
    <section class="features-section-compact">
        <div class="features-grid-compact">
            <div class="feature-item-compact">
                <div class="feature-icon-compact">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="feature-content-compact">
                    <h3>Fast Delivery</h3>
                    <p>Quick & reliable shipping</p>
                </div>
            </div>
            <div class="feature-item-compact">
                <div class="feature-icon-compact">
                    <i class="fas fa-award"></i>
                </div>
                <div class="feature-content-compact">
                    <h3>Quality Assured</h3>
                    <p>Handpicked with care</p>
                </div>
            </div>
            <div class="feature-item-compact">
                <div class="feature-icon-compact">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="feature-content-compact">
                    <h3>24/7 Support</h3>
                    <p>Always here to help</p>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
/* Enhanced Announcement Banner */
.announcement-banner {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
    background-size: 300% 300%;
    animation: gradientShift 8s ease infinite;
    padding: 8px 0;
    border-radius: 0 0 12px 12px;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.marquee {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    box-sizing: border-box;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 20px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    margin: 0 20px;
}

.marquee span {
    display: inline-block;
    padding-left: 100%;
    animation: marquee 15s linear infinite;
    font-size: 16px;
    font-weight: 700;
    color: white;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    letter-spacing: 0.5px;
}

@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* Enhanced Contact Information Banner */
.contact-info-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px 20px;
    margin: 20px 0;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
}

.contact-info-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.contact-header {
    text-align: center;
    margin-bottom: 25px;
    position: relative;
    z-index: 2;
}

.contact-title {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.contact-title i {
    font-size: 1.3rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.contact-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    margin: 0;
    font-weight: 400;
}

.contact-items-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    position: relative;
    z-index: 2;
}

.contact-item {
    position: relative;
}

.contact-link {
    display: flex;
    align-items: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid transparent;
    border-radius: 16px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.contact-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.contact-link:hover::before {
    left: 100%;
}

.contact-link:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}

/* Individual contact item hover effects */
.whatsapp-item .contact-link:hover {
    border-color: #25d366;
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #059669;
}

.mobile-item .contact-link:hover {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1d4ed8;
}

.phone-item .contact-link:hover {
    border-color: #10b981;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #047857;
}

.email-item .contact-link:hover {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.alternate-item .contact-link:hover {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
    color: #7c3aed;
}

.gpay-item .contact-link:hover {
    border-color: #ff9800;
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: #f57c00;
}

.contact-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin-right: 16px;
    font-size: 24px;
    flex-shrink: 0;
    transition: all 0.3s ease;
    position: relative;
}

.contact-icon::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    background: inherit;
    filter: blur(10px);
    opacity: 0.3;
    z-index: -1;
}

.whatsapp-item .contact-icon {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
    color: white;
}

.mobile-item .contact-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
}

.phone-item .contact-icon {
    background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    color: white;
}

.email-item .contact-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.alternate-item .contact-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.gpay-item .contact-icon {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    color: white;
}

.contact-details {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    gap: 2px;
}

.contact-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6b7280;
    margin-bottom: 2px;
}

.contact-value {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
    margin-bottom: 2px;
}

.contact-action {
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.contact-link:hover .contact-action {
    opacity: 1;
}

.contact-badge {
    width: 35px;
    height: 35px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #6b7280;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.contact-link:hover .contact-badge {
    background: rgba(0, 0, 0, 0.1);
    transform: scale(1.1);
}

.contact-link:hover .contact-icon {
    transform: scale(1.1) rotate(10deg);
}

/* Compact Design Variables */
:root {
    --primary: {{ $globalCompany->primary_color ?? '#2563eb' }};
    --secondary: {{ $globalCompany->secondary_color ?? '#10b981' }};
    --accent: #f59e0b;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --surface: #ffffff;
    --background: #f9fafb;
    --border: #e5e7eb;
    --radius: 6px;
    --radius-lg: 8px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

/* Compact Hero Section */
.hero-section-compact {
    margin-bottom: 2rem;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.hero-container-compact {
    position: relative;
    height: 300px;
    display: flex;
    align-items: center;
    overflow: hidden;
}

.hero-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
}

.hero-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-gradient {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 100%);
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    width: 100%;
    padding: 0 1rem;
}

.carousel-btn-compact {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    transition: all 0.3s ease;
}

.carousel-control-prev:hover .carousel-btn-compact,
.carousel-control-next:hover .carousel-btn-compact {
    background: white;
    transform: scale(1.1);
    box-shadow: var(--shadow-md);
}

/* Main Container */
.main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Compact Section Headers */
.section-header-compact {
    text-align: center;
    margin-bottom: 2rem;
}

.section-title-compact {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    position: relative;
}

.section-title-compact::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 3px;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    border-radius: var(--radius);
}

.section-subtitle-compact {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-top: 0.75rem;
}

/* Compact Products Section */
.products-section-compact {
    margin-bottom: 3rem;
}

.product-tabs-compact {
    margin-bottom: 1.5rem;
}

.tabs-nav-compact {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    background: var(--surface);
    padding: 0.4rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    max-width: fit-content;
    margin-left: auto;
    margin-right: auto;
}

.tab-link-compact {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.2rem;
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: 500;
    border-radius: var(--radius);
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.875rem;
}

.tab-link-compact:hover {
    color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

.tab-link-compact.active {
    color: var(--primary);
    background: rgba(37, 99, 235, 0.1);
    font-weight: 600;
}

/* Compact Products Grid */
.products-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.products-grid-compact .product-card {
    font-size: 0.7rem;
    border-radius: 8px;
    display: block !important;
    height: auto !important;
    min-height: unset !important;
    flex-direction: unset !important;
}

.products-grid-compact .product-image-container,
.products-grid-compact .product-card-header {
    height: 80px !important;
}

.products-grid-compact .product-content {
    padding: 0.4rem;
    display: block !important;
    flex: unset !important;
}

.products-grid-compact .product-title {
    font-size: 0.7rem;
    line-height: 1.1;
    margin-bottom: 0.2rem;
    height: 2.2rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.products-grid-compact .current-price {
    font-size: 0.8rem;
    font-weight: 700;
}

.products-grid-compact .btn-add-cart {
    padding: 0.3rem 0.5rem !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
    width: 100% !important;
    display: block !important;
    margin-top: 0.3rem !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.products-grid-compact .product-description {
    display: none;
}

/* Compact Features Section */
.features-section-compact {
    margin: 2.5rem 0;
}

.features-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    background: var(--surface);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.feature-item-compact {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius-lg);
    transition: transform 0.3s ease;
}

.feature-item-compact:hover {
    transform: translateY(-2px);
}

.feature-icon-compact {
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.feature-content-compact h3 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
}

.feature-content-compact p {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin: 0;
}

/* Enhanced Categories with Images */
.categories-section-enhanced {
    padding: 1.5rem 0;
    background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(241, 245, 249, 0.8) 100%);
    margin: 1.5rem 0;
    border-radius: 20px;
}

.section-header-enhanced {
    text-align: center;
    margin-bottom: 1.5rem;
}

.section-title-enhanced {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.section-subtitle-enhanced {
    font-size: 1.1rem;
    color: var(--text-secondary);
    margin-bottom: 0;
}

.categories-grid-enhanced {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    padding: 0 1rem;
}

.category-card-enhanced {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.category-card-enhanced:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.category-link-enhanced {
    display: block;
    text-decoration: none;
    color: inherit;
    height: 100%;
}

.category-image-wrapper-enhanced {
    position: relative;
    height: 120px;
    overflow: hidden;
}

.category-image-enhanced {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.category-card-enhanced:hover .category-image-enhanced {
    transform: scale(1.1);
}

.category-placeholder-enhanced {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #e8f4fd 0%, #dbeafe 50%, #bfdbfe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.category-icon-enhanced {
    font-size: 3rem;
    color: #3b82f6;
    opacity: 0.7;
    transition: all 0.3s ease;
}

.category-card-enhanced:hover .category-icon-enhanced {
    transform: scale(1.2);
    opacity: 1;
}

.category-badge-enhanced {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 3;
}

.category-badge-enhanced .badge {
    background: rgba(59, 130, 246, 0.9) !important;
    color: white !important;
    font-size: 0.7rem;
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

.category-info-enhanced {
    padding: 1rem;
    text-align: center;
}

.category-name-enhanced {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.4rem;
    line-height: 1.2;
}

.category-count-enhanced {
    display: inline-block;
    font-size: 0.8rem;
    color: var(--primary);
    background: rgba(59, 130, 246, 0.1);
    padding: 0.2rem 0.6rem;
    border-radius: 15px;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.category-desc-enhanced {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.4;
    margin: 0;
}

.empty-categories-enhanced {
    text-align: center;
    padding: 4rem 2rem;
}

/* Enhanced Responsive Design */
@media (max-width: 768px) {
    .contact-info-banner {
        padding: 20px 15px;
        margin: 15px 0;
    }
    
    .contact-items-wrapper {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .contact-link {
        padding: 16px;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-right: 12px;
    }
    
    .contact-value {
        font-size: 14px;
    }
    
    .contact-title {
        font-size: 1.3rem;
    }
    
    .contact-subtitle {
        font-size: 0.9rem;
    }
    
    .marquee {
        margin: 0 10px;
        padding: 6px 15px;
    }
    
    .marquee span {
        font-size: 14px;
    }
    
    .hero-container-compact {
        height: 250px;
    }
    
    .products-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
        gap: 0.4rem;
    }
    
    .features-grid-compact {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        padding: 1rem;
    }
    
    .categories-grid-enhanced {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        padding: 0 0.5rem;
    }
    
    .section-title-enhanced {
        font-size: 1.5rem;
    }
    
    .section-subtitle-enhanced {
        font-size: 0.95rem;
    }
    
    .category-image-wrapper-enhanced {
        height: 100px;
    }
    
    .category-info-enhanced {
        padding: 0.75rem;
    }
    
    .category-name-enhanced {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .contact-info-banner {
        padding: 15px 10px;
        margin: 10px 0;
        border-radius: 15px;
    }
    
    .contact-header {
        margin-bottom: 20px;
    }
    
    .contact-title {
        font-size: 1.2rem;
        flex-direction: column;
        gap: 8px;
    }
    
    .contact-subtitle {
        font-size: 0.85rem;
    }
    
    .contact-link {
        padding: 12px;
        border-radius: 12px;
    }
    
    .contact-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
        margin-right: 10px;
    }
    
    .contact-value {
        font-size: 13px;
    }
    
    .contact-badge {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .contact-items-wrapper {
        gap: 12px;
    }
    
    .marquee {
        margin: 0 5px;
        padding: 5px 12px;
    }
    
    .marquee span {
        font-size: 13px;
    }
    
    .products-grid-compact {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.3rem;
    }
    
    .hero-container-compact {
        height: 200px;
    }
    
    .categories-grid-enhanced {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .category-image-wrapper-enhanced {
        height: 90px;
    }
    
    .category-badge-enhanced {
        top: 6px;
        right: 6px;
    }
    
    .category-badge-enhanced .badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.5rem;
    }
    
    .category-info-enhanced {
        padding: 0.6rem;
    }
    
    .category-name-enhanced {
        font-size: 0.9rem;
    }
    
    .category-desc-enhanced {
        display: none;
    }
}

@media (max-width: 768px) {
    .view-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .view-toggle {
        justify-content: center;
    }
    
    .sort-controls {
        width: 100%;
    }
    
    .sort-select {
        width: 100%;
        min-width: unset;
    }
    
    .products-list .product-card {
        flex-direction: column;
    }
    
    .products-list .product-image-container {
        width: 100%;
        height: 200px;
    }
    
    .products-list .product-footer {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .products-list .product-actions {
        justify-content: center;
    }
    
    /* Table responsive styles */
    .products-list .products-table {
        font-size: 0.75rem;
    }
    
    .products-list .products-table th,
    .products-list .products-table td {
        padding: 0.5rem 0.25rem;
    }
    
    .products-list .product-info {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .products-list .product-image-small,
    .products-list .product-placeholder-small {
        width: 35px;
        height: 35px;
        margin: 0 auto;
    }
    
    .products-list .btn-add-cart-small {
        padding: 0.25rem 0.5rem;
        font-size: 0.65rem;
    }
    
    .products-list .quantity-controls {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .products-list .qty-btn-small {
        width: 24px;
        height: 24px;
    }
    
    .products-list .qty-input-small {
        width: 35px;
        height: 24px;
    }
}

@media (max-width: 480px) {
    .contact-items-wrapper {
        grid-template-columns: 1fr;
    }
    
    .contact-link {
        padding: 10px;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .contact-value {
        font-size: 12px;
    }
    
    .contact-label {
        font-size: 10px;
    }
    
    .contact-action {
        font-size: 10px;
    }
    
    .products-grid-compact {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.25rem;
    }
    
    /* Mobile table adjustments */
    .products-list .products-table {
        display: block;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .products-list .products-table th:nth-child(3),
    .products-list .products-table td:nth-child(3),
    .products-list .products-table th:nth-child(4),
    .products-list .products-table td:nth-child(4) {
        display: none;
    }
    
    .products-list .product-col {
        width: 50%;
    }
    
    .products-list .offer-col,
    .products-list .qty-col,
    .products-list .amount-col {
        width: 16%;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .marquee span,
    .contact-icon,
    .contact-link::before,
    .announcement-banner {
        animation: none;
    }
    
    .contact-link:hover {
        transform: none;
    }
}

/* Animation */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* View Controls - Same as products page */
.view-controls-section {
    margin-bottom: 2rem;
}

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.view-toggle {
    display: flex;
    background: #f9fafb;
    border-radius: 8px;
    padding: 4px;
    gap: 4px;
}

.view-btn {
    background: transparent;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.view-btn:hover {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.view-btn.active {
    background: var(--primary);
    color: white;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.sort-select {
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 180px;
}

.sort-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* List View Styles - Same as products page */
.products-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.products-list .product-card {
    display: flex;
    flex-direction: row;
    height: auto;
    max-width: none;
}

.products-list .product-image-container {
    width: 200px;
    height: 150px;
    flex-shrink: 0;
}

.products-list .product-content {
    flex: 1;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}

.products-list .product-title {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.products-list .product-description {
    font-size: 1rem;
    margin-bottom: 1rem;
    display: block;
    -webkit-line-clamp: 3;
}

.products-list .product-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: auto;
}

.products-list .price-section {
    margin-bottom: 0;
}

.products-list .current-price {
    font-size: 1.5rem;
}

.products-list .product-actions {
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    margin-top: 0;
}

.products-list .quantity-selector {
    margin-bottom: 0;
}

.products-list .btn-add-cart {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    white-space: nowrap;
    width: auto;
}

.products-list .quick-actions {
    position: static;
    opacity: 1;
    background: none;
    flex-direction: row;
    gap: 0.5rem;
    margin-top: 1rem;
}

.products-list .product-overlay {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize view controls for home page
    initializeHomeViewControls();
    
    // Intersection Observer for animations
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
    
    // Observe elements for animation
    document.querySelectorAll('.product-card, .category-card-enhanced, .feature-item-compact, .contact-item').forEach(el => {
        observer.observe(el);
    });
    
    // Enhanced carousel
    const carousel = document.querySelector('#heroCarousel');
    if (carousel) {
        const bsCarousel = new bootstrap.Carousel(carousel, {
            interval: 7000,
            pause: 'hover',
            ride: 'carousel'
        });
    }
    
    // Contact link analytics (optional)
    document.querySelectorAll('.contact-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const contactType = this.closest('.contact-item').className.split(' ').find(cls => cls.endsWith('-item'));
            console.log(`Contact clicked: ${contactType}`);
            
            // You can add analytics tracking here
            // gtag('event', 'contact_click', { contact_type: contactType });
        });
    });
    
    // Add pulse effect to WhatsApp link
    const whatsappLink = document.querySelector('.whatsapp-item .contact-icon');
    if (whatsappLink) {
        setInterval(() => {
            whatsappLink.style.transform = 'scale(1.1)';
            setTimeout(() => {
                whatsappLink.style.transform = 'scale(1)';
            }, 200);
        }, 3000);
    }
    
    // Home page view controls functionality
    function initializeHomeViewControls() {
        let currentView = localStorage.getItem('product-view') || 'grid';
        
        // View toggle buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                switchHomeView(view);
            });
        });
        
        // Sort functionality (basic client-side for now)
        const sortSelect = document.getElementById('sortProducts');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                sortProductsClient(sortValue);
            });
        }
        
        // Apply saved view
        switchHomeView(currentView);
    }
    
    function switchHomeView(view) {
        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
        
        // Update grid display
        const productsGrid = document.getElementById('products-grid');
        const productsListTable = document.getElementById('products-list-table');
        
        if (productsGrid && productsListTable) {
            if (view === 'list') {
                productsGrid.style.display = 'none';
                productsListTable.style.display = 'block';
                productsListTable.classList.add('products-list');
            } else {
                productsGrid.style.display = 'grid';
                productsListTable.style.display = 'none';
                productsGrid.classList.add('products-grid-compact');
            }
        }
        
        // Save preference
        localStorage.setItem('product-view', view);
        
        // Add animation
        const activeContainer = view === 'list' ? productsListTable : productsGrid;
        if (activeContainer) {
            activeContainer.style.opacity = '0';
            setTimeout(() => {
                activeContainer.style.opacity = '1';
            }, 150);
        }
    }
    
    function sortProductsClient(sortValue) {
        const productsGrid = document.getElementById('products-grid');
        if (!productsGrid) return;
        
        const productCards = Array.from(productsGrid.children);
        
        productCards.sort((a, b) => {
            switch (sortValue) {
                case 'name_asc':
                    const nameA = a.querySelector('.product-title a').textContent.toLowerCase();
                    const nameB = b.querySelector('.product-title a').textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                    
                case 'name_desc':
                    const nameDescA = a.querySelector('.product-title a').textContent.toLowerCase();
                    const nameDescB = b.querySelector('.product-title a').textContent.toLowerCase();
                    return nameDescB.localeCompare(nameDescA);
                    
                case 'price_asc':
                    const priceA = parseFloat(a.querySelector('.current-price').textContent.replace(/[^0-9.]/g, ''));
                    const priceB = parseFloat(b.querySelector('.current-price').textContent.replace(/[^0-9.]/g, ''));
                    return priceA - priceB;
                    
                case 'price_desc':
                    const priceDescA = parseFloat(a.querySelector('.current-price').textContent.replace(/[^0-9.]/g, ''));
                    const priceDescB = parseFloat(b.querySelector('.current-price').textContent.replace(/[^0-9.]/g, ''));
                    return priceDescB - priceDescA;
                    
                default:
                    return 0;
            }
        });
        
        // Clear and re-append sorted cards
        productsGrid.innerHTML = '';
        productCards.forEach(card => {
            productsGrid.appendChild(card);
        });
        
        // Add fade-in animation
        productCards.forEach((card, index) => {
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
                card.classList.add('fade-in');
            }, index * 50);
        });
    }
});
</script>
@endpush
@endsection