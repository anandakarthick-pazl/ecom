@extends('layouts.app')

@section('title')
    Home - {{ $globalCompany->company_name ?? 'Your Store' }}
@endsection

@section('meta_description')
    Discover quality products at {{ $globalCompany->company_name ?? 'Your Store' }}. Shop our curated collection with confidence.
@endsection

@section('content')
<!-- Compact Hero Section -->
@if($banners->count() > 0)
<section class="hero-section-compact">
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
                        <img src="{{ Storage::url($banner->image) }}" class="hero-image" alt="{{ $banner->alt_text ?: $banner->title }}">
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
@endif

<div class="main-container">
    <!-- Compact Categories Section -->
    @if($categories->count() > 0)
    <section class="categories-section-compact">
        <div class="section-header-compact">
            <h2 class="section-title-compact">Shop by Category</h2>
            <p class="section-subtitle-compact">Explore our carefully curated collections</p>
        </div>
        <div class="categories-grid-compact">
            @foreach($categories->take(6) as $category)
            <div class="category-card-compact">
                <a href="{{ route('category', $category->slug) }}" class="category-link">
                    <div class="category-image-compact">
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}">
                        @else
                            <div class="category-placeholder-compact">
                                <i class="fas fa-leaf"></i>
                            </div>
                        @endif
                        <div class="category-overlay-compact">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                    <div class="category-info-compact">
                        <h3 class="category-name-compact">{{ $category->name }}</h3>
                        <span class="category-count-compact">{{ $category->products_count ?? 0 }} items</span>
                    </div>
                </a>
            </div>
            @endforeach
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
                <a href="{{ route('shop', ['menu' => 'featured']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'featured' ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Featured</span>
                </a>
                <a href="{{ route('shop', ['menu' => 'all']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'all' ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>All Products</span>
                </a>
                <a href="{{ route('shop', ['menu' => 'offers']) }}" 
                   class="tab-link-compact {{ $activeMenu === 'offers' ? 'active' : '' }}">
                    <i class="fas fa-fire"></i>
                    <span>Hot Deals</span>
                </a>
            </nav>
        </div>
        
        <!-- Products Grid -->
        <div class="products-container">
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
            
            @if($activeMenu === 'all')
                @if($products->count() > 0)
                    <div class="products-grid-compact">
                        @foreach($products as $product)
                            @include('partials.product-card-modern', ['product' => $product])
                        @endforeach
                    </div>
                    @if($enablePagination && isset($products) && method_exists($products, 'appends'))
                        <div class="pagination-container">
                            {{ $products->appends(['menu' => 'all'])->links() }}
                        </div>
                    @endif
                @else
                    @include('partials.empty-state', ['icon' => 'box', 'title' => 'No Products Available', 'message' => 'New products coming soon'])
                @endif
            @endif
            
            @if($activeMenu === 'offers')
                @if($products->count() > 0)
                    <div class="products-grid-compact offers">
                        @foreach($products as $product)
                            @include('partials.product-card-modern', ['product' => $product, 'offer' => true])
                        @endforeach
                    </div>
                    @if($enablePagination && isset($products) && method_exists($products, 'appends'))
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
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="feature-content-compact">
                    <h3>100% Natural</h3>
                    <p>Premium quality ingredients</p>
                </div>
            </div>
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
    height: 300px; /* Reduced from 600px */
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

.hero-text {
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
}

.hero-title-compact {
    font-size: 2rem; /* Reduced from 3.5rem */
    font-weight: 700;
    color: white;
    margin-bottom: 0.75rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    line-height: 1.1;
}

.hero-subtitle-compact {
    font-size: 1rem; /* Reduced from 1.25rem */
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 1.5rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.hero-actions-compact {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-hero-compact {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem; /* Reduced padding */
    font-weight: 600;
    text-decoration: none;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem; /* Reduced font size */
}

.btn-hero-compact.primary {
    background: var(--primary);
    color: white;
    box-shadow: var(--shadow-md);
}

.btn-hero-compact.primary:hover {
    background: var(--secondary);
    transform: translateY(-2px);
    color: white;
}

.btn-hero-compact.secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-hero-compact.secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
}

.carousel-btn-compact {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 40px; /* Reduced from 50px */
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
    max-width: 1200px; /* Reduced from 1400px */
    margin: 0 auto;
    padding: 0 1rem;
}

/* Compact Section Headers */
.section-header-compact {
    text-align: center;
    margin-bottom: 2rem; /* Reduced from 3rem */
}

.section-title-compact {
    font-size: 1.8rem; /* Reduced from 2.5rem */
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
    width: 40px; /* Reduced from 60px */
    height: 3px;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    border-radius: var(--radius);
}

.section-subtitle-compact {
    font-size: 1rem;
    color: var(--text-secondary);
    margin-top: 0.75rem;
}

/* Compact Categories Grid */
.categories-section-compact {
    margin-bottom: 2.5rem;
}

.categories-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); /* Reduced from 200px */
    gap: 1rem; /* Reduced from 1.5rem */
}

.category-card-compact {
    background: var(--surface);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.category-card-compact:hover {
    transform: translateY(-3px); /* Reduced from -4px */
    box-shadow: var(--shadow-lg);
}

.category-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.category-image-compact {
    position: relative;
    height: 80px; /* Reduced from 120px */
    overflow: hidden;
}

.category-image-compact img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.category-placeholder-compact {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem; /* Reduced from 2rem */
}

.category-overlay-compact {
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

.category-card-compact:hover .category-overlay-compact {
    opacity: 1;
}

.category-card-compact:hover .category-image-compact img {
    transform: scale(1.05);
}

.category-overlay-compact i {
    color: white;
    font-size: 1.2rem;
}

.category-info-compact {
    padding: 0.75rem; /* Reduced from 1rem */
    text-align: center;
}

.category-name-compact {
    font-size: 0.875rem; /* Reduced from 1rem */
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
}

.category-count-compact {
    font-size: 0.75rem; /* Reduced from 0.875rem */
    color: var(--text-secondary);
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
    padding: 0.4rem; /* Reduced from 0.5rem */
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
    padding: 0.6rem 1.2rem; /* Reduced from 0.75rem 1.5rem */
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: 500;
    border-radius: var(--radius);
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.875rem; /* Reduced font size */
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
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Reduced from 260px */
    gap: 1rem; /* Reduced from 1.5rem */
    margin-bottom: 1.5rem;
}

/* Compact Features Section */
.features-section-compact {
    margin: 2.5rem 0; /* Reduced from 4rem */
}

.features-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Reduced from 250px */
    gap: 1rem; /* Reduced from 2rem */
    background: var(--surface);
    padding: 1.5rem; /* Reduced from 3rem 2rem */
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.feature-item-compact {
    display: flex;
    align-items: center;
    gap: 0.75rem; /* Reduced from 1rem */
    padding: 0.75rem; /* Reduced from 1rem */
    border-radius: var(--radius-lg);
    transition: transform 0.3s ease;
}

.feature-item-compact:hover {
    transform: translateY(-2px);
}

.feature-icon-compact {
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    color: white;
    width: 40px; /* Reduced from 50px */
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem; /* Reduced from 1.25rem */
    flex-shrink: 0;
}

.feature-content-compact h3 {
    font-size: 0.875rem; /* Reduced from 1rem */
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
}

.feature-content-compact p {
    font-size: 0.75rem; /* Reduced from 0.875rem */
    color: var(--text-secondary);
    margin: 0;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-container-compact {
        height: 250px; /* Further reduced for mobile */
    }
    
    .hero-title-compact {
        font-size: 1.5rem;
    }
    
    .hero-actions-compact {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-hero-compact {
        width: 180px;
        justify-content: center;
    }
    
    .products-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.75rem;
    }
    
    .categories-grid-compact {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.75rem;
    }
    
    .tabs-nav-compact {
        flex-direction: column;
        max-width: 150px;
    }
    
    .section-title-compact {
        font-size: 1.5rem;
    }
    
    .main-container {
        padding: 0 0.5rem;
    }
    
    .features-grid-compact {
        grid-template-columns: 1fr;
        gap: 0.75rem;
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .products-grid-compact {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .hero-container-compact {
        height: 200px;
    }
    
    .hero-title-compact {
        font-size: 1.25rem;
    }
    
    .categories-grid-compact {
        grid-template-columns: repeat(3, 1fr);
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    document.querySelectorAll('.product-card-compact, .category-card-compact, .feature-item-compact').forEach(el => {
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
});
</script>
@endpush
@endsection