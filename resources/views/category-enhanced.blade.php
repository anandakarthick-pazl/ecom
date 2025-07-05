@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name . ' - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', $category->meta_description ?: $category->description)
@section('meta_keywords', $category->meta_keywords)

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Page Header -->
<div class="page-header-enhanced">
    <div class="container">
        <div class="header-content text-center">
            <!-- Enhanced Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb breadcrumb-enhanced justify-content-center">
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop') }}" class="text-white-50">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('products') }}" class="text-white-50">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">
                        {{ $category->name }}
                    </li>
                </ol>
            </nav>
            
            <h1 class="display-4 mb-3 fw-bold animate-fade-in">
                <i class="fas fa-tag me-3"></i>
                {{ $category->name }}
            </h1>
            
            @if($category->description)
                <p class="lead animate-slide-up">{{ $category->description }}</p>
            @endif
            
            <div class="category-stats animate-bounce-in">
                <span class="stat-badge">
                    <i class="fas fa-box me-2"></i>{{ $products->count() }} Products
                </span>
                @if($products->where('discount_percentage', '>', 0)->count() > 0)
                    <span class="stat-badge ms-3">
                        <i class="fas fa-tags me-2"></i>{{ $products->where('discount_percentage', '>', 0)->count() }} Offers
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Enhanced Category Banner -->
    @if($category->image)
    <section class="category-banner-enhanced mb-5">
        <div class="category-banner-container">
            <img src="{{ Storage::url($category->image) }}" 
                 alt="{{ $category->name }}" 
                 class="category-banner-image">
            <div class="category-banner-overlay">
                <div class="category-banner-content">
                    <h2 class="category-banner-title">Explore {{ $category->name }}</h2>
                    <p class="category-banner-description">
                        Discover our premium collection of {{ strtolower($category->name) }} products
                    </p>
                </div>
            </div>
        </div>
    </section>
    @endif
    
    <!-- Enhanced Filter and Sort Section -->
    <section class="filter-section-enhanced mb-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="mb-3 fw-bold text-dark">
                    <i class="fas fa-filter me-2"></i>Filter & Sort
                </h6>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all" onclick="filterProducts('all', this)">
                        <i class="fas fa-th-large me-1"></i>All Products
                    </button>
                    <button class="filter-btn" data-filter="featured" onclick="filterProducts('featured', this)">
                        <i class="fas fa-star me-1"></i>Featured
                    </button>
                    <button class="filter-btn" data-filter="offers" onclick="filterProducts('offers', this)">
                        <i class="fas fa-tags me-1"></i>On Sale
                    </button>
                    <button class="filter-btn" data-filter="stock" onclick="filterProducts('stock', this)">
                        <i class="fas fa-check-circle me-1"></i>In Stock
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sort-section">
                    <label for="sortSelect" class="form-label fw-bold text-dark">
                        <i class="fas fa-sort me-2"></i>Sort By:
                    </label>
                    <select class="form-select sort-select" id="sortSelect" onchange="sortProducts(this.value)">
                        <option value="name_asc">Name (A to Z)</option>
                        <option value="name_desc">Name (Z to A)</option>
                        <option value="price_asc">Price (Low to High)</option>
                        <option value="price_desc">Price (High to Low)</option>
                        <option value="newest">Newest First</option>
                        <option value="featured">Featured First</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading State -->
    <div class="loading-enhanced" id="loading-products" style="display: none;">
        <div class="spinner-enhanced"></div>
        <p class="loading-text">Loading amazing products...</p>
    </div>

    <!-- Products Grid -->
    <section class="products-section" id="products-container">
        @if($products->count() > 0)
        <div class="row g-4" id="products-grid">
            @foreach($products as $product)
            <div class="col-xl-3 col-lg-4 col-md-6 product-item" 
                 data-name="{{ strtolower($product->name) }}"
                 data-price="{{ $product->discount_price ?: $product->price }}"
                 data-featured="{{ $product->is_featured ? 'yes' : 'no' }}"
                 data-offer="{{ $product->discount_percentage > 0 ? 'yes' : 'no' }}"
                 data-stock="{{ $product->isInStock() ? 'yes' : 'no' }}"
                 data-created="{{ $product->created_at->format('Y-m-d') }}">
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
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="empty-title">No Products Found</h3>
            <p class="empty-description">
                We don't have any products in the {{ $category->name }} category yet. 
                Check back later for new arrivals!
            </p>
            <div class="empty-actions">
                <a href="{{ route('products') }}" class="btn btn-primary-enhanced me-3">
                    <i class="fas fa-box me-2"></i>Browse All Products
                </a>
                <a href="{{ route('shop') }}" class="btn btn-outline-enhanced">
                    <i class="fas fa-home me-2"></i>Go Home
                </a>
            </div>
        </div>
        @endif
    </section>

    <!-- Enhanced Pagination -->
    @if($enablePagination && isset($products) && method_exists($products, 'appends'))
    <div class="pagination-enhanced d-flex justify-content-center mt-5" id="pagination-container">
        {{ $products->links() }}
    </div>
    @endif

    <!-- Related Categories Section -->
    @php
        $relatedCategories = \App\Models\Category::active()
            ->parent()
            ->where('id', '!=', $category->id)
            ->limit(4)
            ->get();
    @endphp
    
    @if($relatedCategories->count() > 0)
    <section class="section-enhanced related-categories-section bg-light">
        <div class="section-header">
            <h2 class="section-title">You Might Also Like</h2>
            <p class="section-subtitle">Explore other categories that complement your interests</p>
        </div>
        
        <div class="row g-4">
            @foreach($relatedCategories as $relatedCategory)
            <div class="col-lg-3 col-md-6">
                <div class="related-category-card animate-scale-in animate-stagger-{{ $loop->iteration }}">
                    <div class="related-category-image">
                        @if($relatedCategory->image)
                            <img src="{{ Storage::url($relatedCategory->image) }}" 
                                 alt="{{ $relatedCategory->name }}"
                                 loading="lazy">
                        @else
                            <div class="related-category-placeholder">
                                <i class="fas fa-leaf fa-3x text-success"></i>
                            </div>
                        @endif
                    </div>
                    <div class="related-category-content">
                        <h6 class="related-category-title">{{ $relatedCategory->name }}</h6>
                        <p class="related-category-description">
                            {{ Str::limit($relatedCategory->description, 60) }}
                        </p>
                        <a href="{{ route('category', $relatedCategory->slug) }}" 
                           class="btn btn-sm btn-outline-enhanced"
                           onclick="triggerFireworks(this)">
                            <i class="fas fa-arrow-right me-1"></i>Explore
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<style>
    /* Enhanced Breadcrumb */
    .breadcrumb-enhanced {
        background: transparent;
        padding: 0;
        margin: 0;
    }
    
    .breadcrumb-enhanced .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255,255,255,0.5);
        content: ">";
        font-weight: bold;
    }
    
    .breadcrumb-enhanced a {
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .breadcrumb-enhanced a:hover {
        color: white !important;
        text-shadow: 0 0 8px rgba(255,255,255,0.5);
    }
    
    /* Category Stats */
    .category-stats {
        margin-top: 30px;
    }
    
    .stat-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Enhanced Category Banner */
    .category-banner-enhanced {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
    }
    
    .category-banner-container {
        position: relative;
        height: 300px;
        overflow: hidden;
    }
    
    .category-banner-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .category-banner-enhanced:hover .category-banner-image {
        transform: scale(1.05);
    }
    
    .category-banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.6), rgba(0,0,0,0.3));
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .category-banner-content {
        text-align: center;
        color: white;
        z-index: 2;
    }
    
    .category-banner-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    
    .category-banner-description {
        font-size: 1.25rem;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    
    /* Enhanced Filter Section */
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .filter-btn {
        padding: 10px 20px;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        background: white;
        color: #666;
        font-weight: 600;
        transition: all 0.4s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        transition: left 0.4s ease;
        z-index: -1;
    }
    
    .filter-btn:hover {
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .filter-btn:hover::before {
        left: 0;
    }
    
    .filter-btn.active {
        background: var(--primary-gradient);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
    }
    
    .filter-btn.active::before {
        left: 0;
    }
    
    /* Sort Section */
    .sort-section {
        text-align: right;
    }
    
    .sort-select {
        border-radius: 15px;
        border: 2px solid #e9ecef;
        padding: 12px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .sort-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.2);
    }
    
    /* Product Grid Animations */
    .product-item {
        transition: all 0.4s ease;
    }
    
    .product-item.filtered-out {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
        height: 0;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    
    .product-item.filtered-in {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }
    
    /* Related Categories */
    .related-category-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transition: all 0.4s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    
    .related-category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .related-category-image {
        height: 180px;
        overflow: hidden;
        position: relative;
    }
    
    .related-category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .related-category-card:hover .related-category-image img {
        transform: scale(1.1);
    }
    
    .related-category-placeholder {
        height: 100%;
        background: linear-gradient(135deg, var(--light-green) 0%, #e8f5e8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .related-category-content {
        padding: 20px;
    }
    
    .related-category-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .related-category-description {
        color: #666;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 15px;
    }
    
    /* No Results State */
    .no-results-message {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        margin: 40px 0;
        display: none;
    }
    
    .no-results-message.show {
        display: block;
        animation: fadeIn 0.5s ease-out;
    }
    
    .no-results-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .no-results-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #666;
        margin-bottom: 10px;
    }
    
    .no-results-description {
        color: #999;
        margin-bottom: 20px;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .category-banner-container {
            height: 200px;
        }
        
        .category-banner-title {
            font-size: 2rem;
        }
        
        .category-banner-description {
            font-size: 1rem;
        }
        
        .filter-buttons {
            justify-content: center;
        }
        
        .filter-btn {
            padding: 8px 16px;
            font-size: 14px;
        }
        
        .sort-section {
            text-align: left;
            margin-top: 20px;
        }
        
        .stat-badge {
            padding: 8px 16px;
            font-size: 14px;
            margin: 5px;
        }
        
        .related-category-image {
            height: 150px;
        }
    }
    
    @media (max-width: 576px) {
        .page-header-enhanced h1 {
            font-size: 2rem;
        }
        
        .category-banner-title {
            font-size: 1.75rem;
        }
        
        .filter-btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .related-category-content {
            padding: 15px;
        }
    }
</style>

<script>
// Enhanced Category Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Trigger welcome fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        setTimeout(() => {
            window.enhancedFireworks.startRandomFireworks();
        }, 1000);
    }
    
    console.log('📂 Enhanced Category Page initialized successfully!');
});

// Product Filtering
function filterProducts(filterType, buttonElement) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    buttonElement.classList.add('active');
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(buttonElement);
    }
    
    const productItems = document.querySelectorAll('.product-item');
    let visibleCount = 0;
    
    productItems.forEach((item, index) => {
        let shouldShow = false;
        
        switch(filterType) {
            case 'all':
                shouldShow = true;
                break;
            case 'featured':
                shouldShow = item.dataset.featured === 'yes';
                break;
            case 'offers':
                shouldShow = item.dataset.offer === 'yes';
                break;
            case 'stock':
                shouldShow = item.dataset.stock === 'yes';
                break;
        }
        
        if (shouldShow) {
            item.classList.remove('filtered-out');
            item.classList.add('filtered-in');
            visibleCount++;
            
            // Add stagger animation delay
            setTimeout(() => {
                item.style.animationDelay = (visibleCount * 0.1) + 's';
                item.classList.add('animate-scale-in');
            }, index * 50);
        } else {
            item.classList.remove('filtered-in');
            item.classList.add('filtered-out');
        }
    });
    
    // Show/hide no results message
    toggleNoResultsMessage(visibleCount === 0, filterType);
    
    // Update URL without page reload
    const url = new URL(window.location);
    if (filterType !== 'all') {
        url.searchParams.set('filter', filterType);
    } else {
        url.searchParams.delete('filter');
    }
    window.history.pushState({}, '', url);
    
    // Show notification
    if (typeof window.showEnhancedNotification === 'function') {
        const filterNames = {
            'all': 'All Products',
            'featured': 'Featured Products',
            'offers': 'Products on Sale',
            'stock': 'In Stock Products'
        };
        
        window.showEnhancedNotification(
            `Showing ${visibleCount} ${filterNames[filterType]}`, 
            'info', 
            2000
        );
    }
}

// Product Sorting
function sortProducts(sortType) {
    const container = document.getElementById('products-grid');
    const items = Array.from(container.querySelectorAll('.product-item'));
    
    // Show loading
    document.getElementById('loading-products').style.display = 'flex';
    container.style.opacity = '0.5';
    
    setTimeout(() => {
        items.sort((a, b) => {
            switch(sortType) {
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                case 'price_asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price_desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'newest':
                    return new Date(b.dataset.created) - new Date(a.dataset.created);
                case 'featured':
                    const aFeatured = a.dataset.featured === 'yes' ? 1 : 0;
                    const bFeatured = b.dataset.featured === 'yes' ? 1 : 0;
                    return bFeatured - aFeatured;
                default:
                    return 0;
            }
        });
        
        // Reorder DOM elements
        items.forEach(item => container.appendChild(item));
        
        // Add animation to reordered items
        items.forEach((item, index) => {
            item.style.animationDelay = (index * 0.05) + 's';
            item.classList.remove('animate-scale-in');
            void item.offsetWidth; // Trigger reflow
            item.classList.add('animate-scale-in');
        });
        
        // Hide loading
        document.getElementById('loading-products').style.display = 'none';
        container.style.opacity = '1';
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('sort', sortType);
        window.history.pushState({}, '', url);
        
        // Show notification
        if (typeof window.showEnhancedNotification === 'function') {
            const sortNames = {
                'name_asc': 'Name (A to Z)',
                'name_desc': 'Name (Z to A)',
                'price_asc': 'Price (Low to High)',
                'price_desc': 'Price (High to Low)',
                'newest': 'Newest First',
                'featured': 'Featured First'
            };
            
            window.showEnhancedNotification(
                `Sorted by ${sortNames[sortType]}`, 
                'info', 
                2000
            );
        }
        
        // Trigger fireworks for sorting
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.startRandomFireworks();
        }
    }, 500);
}

// Toggle No Results Message
function toggleNoResultsMessage(show, filterType) {
    let noResultsDiv = document.getElementById('no-results-message');
    
    if (show && !noResultsDiv) {
        // Create no results message
        noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'no-results-message';
        noResultsDiv.className = 'no-results-message';
        noResultsDiv.innerHTML = `
            <div class="no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="no-results-title">No Products Found</h3>
            <p class="no-results-description">
                No products match your current filter. Try a different filter or browse all products.
            </p>
            <button class="btn btn-primary-enhanced" onclick="filterProducts('all', document.querySelector('.filter-btn[data-filter=all]'))">
                <i class="fas fa-refresh me-2"></i>Show All Products
            </button>
        `;
        
        document.getElementById('products-container').appendChild(noResultsDiv);
    }
    
    if (noResultsDiv) {
        if (show) {
            noResultsDiv.classList.add('show');
        } else {
            noResultsDiv.classList.remove('show');
        }
    }
}

// Initialize filters from URL parameters
function initializeFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const filter = urlParams.get('filter');
    const sort = urlParams.get('sort');
    
    if (filter) {
        const filterBtn = document.querySelector(`.filter-btn[data-filter="${filter}"]`);
        if (filterBtn) {
            filterProducts(filter, filterBtn);
        }
    }
    
    if (sort) {
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.value = sort;
            sortProducts(sort);
        }
    }
}

// Global trigger fireworks function
function triggerFireworks(element) {
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(element);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFromURL();
});
</script>
@endsection
