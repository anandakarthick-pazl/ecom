@extends('layouts.app')

@section('title', 'All Products - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Browse all our products. Find what you need from our complete product catalog.')

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Page Header -->
<div class="page-header-enhanced">
    <div class="container">
        <div class="header-content text-center">
            <h1 class="display-4 mb-3 fw-bold animate-fade-in">
                <i class="fas fa-sparkles me-3"></i>
                Discover Amazing Products
            </h1>
            <p class="lead animate-slide-up">Find exactly what you're looking for in our curated collection</p>
            
            <div class="header-stats animate-bounce-in">
                <span class="stat-badge">
                    <i class="fas fa-box me-2"></i>
                    {{ $enablePagination && method_exists($products, 'total') ? $products->total() : $products->count() }} Products
                </span>
                <span class="stat-badge ms-3">
                    <i class="fas fa-tags me-2"></i>{{ $categories->count() }} Categories
                </span>
                <span class="stat-badge ms-3">
                    <i class="fas fa-shipping-fast me-2"></i>Free Shipping ₹500+
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-0">
    <div class="container">
        <!-- Enhanced Categories Filter -->
        @if($categories->count() > 0)
        <div class="filter-section-enhanced">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h6 class="mb-3 fw-bold text-dark">
                        <i class="fas fa-filter me-2"></i>Filter by Category
                    </h6>
                    <div class="d-flex flex-wrap" id="category-filters">
                        <button class="btn category-filter-enhanced {{ request('category', 'all') === 'all' ? 'active' : '' }}" 
                                data-category="all"
                                onclick="filterByCategory('all', this)">
                            <i class="fas fa-th-large me-1"></i>All Products
                        </button>
                        @foreach($categories as $category)
                        <button class="btn category-filter-enhanced {{ request('category') === $category->slug ? 'active' : '' }}" 
                                data-category="{{ $category->slug }}"
                                onclick="filterByCategory('{{ $category->slug }}', this)">
                            <i class="fas fa-tag me-1"></i>{{ $category->name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="view-controls">
                        <label for="viewType" class="form-label fw-bold text-dark">
                            <i class="fas fa-eye me-2"></i>View:
                        </label>
                        <div class="btn-group view-toggle" role="group">
                            <input type="radio" class="btn-check" name="viewType" id="gridView" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="gridView" onclick="switchView('grid')">
                                <i class="fas fa-th"></i> Grid
                            </label>
                            
                            <input type="radio" class="btn-check" name="viewType" id="listView" autocomplete="off">
                            <label class="btn btn-outline-primary" for="listView" onclick="switchView('list')">
                                <i class="fas fa-list"></i> List
                            </label>
                        </div>
                        
                        <select class="form-select sort-select mt-2" onchange="sortProducts(this.value)">
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
        </div>
        @endif

        <!-- Quick Actions Bar -->
        <div class="quick-actions-bar">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="quick-filters">
                        <button class="quick-filter-btn" data-filter="featured" onclick="quickFilter('featured', this)">
                            <i class="fas fa-star me-1"></i>Featured
                        </button>
                        <button class="quick-filter-btn" data-filter="offers" onclick="quickFilter('offers', this)">
                            <i class="fas fa-tags me-1"></i>On Sale
                        </button>
                        <button class="quick-filter-btn" data-filter="new" onclick="quickFilter('new', this)">
                            <i class="fas fa-plus me-1"></i>New Arrivals
                        </button>
                        <button class="quick-filter-btn" data-filter="popular" onclick="quickFilter('popular', this)">
                            <i class="fas fa-fire me-1"></i>Popular
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="results-info">
                        <span id="results-count" class="text-muted">
                            Showing <strong>{{ $products->count() }}</strong> products
                        </span>
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="clearAllFilters()">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-enhanced" id="loading-spinner" style="display: none;">
            <div class="spinner-enhanced"></div>
            <p class="loading-text">Loading amazing products...</p>
        </div>

        <!-- Products Grid Container -->
        <div class="products-container" id="products-container">
            <div id="products-grid" class="products-grid-view">
                @if($products->count() > 0)
                    <div class="row g-4">
                        @foreach($products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 product-item" 
                             data-product-id="{{ $product->id }}"
                             data-name="{{ strtolower($product->name) }}"
                             data-price="{{ $product->discount_price ?: $product->price }}"
                             data-featured="{{ $product->is_featured ? 'yes' : 'no' }}"
                             data-offer="{{ $product->discount_percentage > 0 ? 'yes' : 'no' }}"
                             data-stock="{{ $product->isInStock() ? 'yes' : 'no' }}"
                             data-category="{{ $product->category->slug }}"
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
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="empty-title">No Products Found</h3>
                        <p class="empty-description">Try browsing different categories or check back later for new arrivals.</p>
                        <a href="{{ route('products') }}" class="btn btn-primary-enhanced">
                            <i class="fas fa-arrow-left me-1"></i> Browse All Products
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Enhanced Pagination -->
        @if($enablePagination && isset($products) && method_exists($products, 'appends'))
        <div class="pagination-enhanced d-flex justify-content-center mt-5" id="pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Enhanced Product Stats Section -->
@if($products->count() > 0)
<div class="stats-section-enhanced">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card-enhanced animate-scale-in animate-stagger-1">
                    <div class="stats-icon-enhanced">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stats-number-enhanced">{{ $enablePagination && method_exists($products, 'total') ? $products->total() : $products->count() }}</div>
                    <div class="stats-label-enhanced">Amazing Products</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card-enhanced animate-scale-in animate-stagger-2">
                    <div class="stats-icon-enhanced">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stats-number-enhanced">{{ $categories->count() }}</div>
                    <div class="stats-label-enhanced">Categories</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card-enhanced animate-scale-in animate-stagger-3">
                    <div class="stats-icon-enhanced">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="stats-number-enhanced">Free</div>
                    <div class="stats-label-enhanced">Shipping on ₹500+</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card-enhanced animate-scale-in animate-stagger-4">
                    <div class="stats-icon-enhanced">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stats-number-enhanced">100%</div>
                    <div class="stats-label-enhanced">Quality Assured</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    /* Enhanced Header Stats */
    .header-stats {
        margin-top: 30px;
    }
    
    .stat-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: inline-block;
        margin: 5px;
    }
    
    /* Quick Actions Bar */
    .quick-actions-bar {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
    }
    
    .quick-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .quick-filter-btn {
        padding: 8px 16px;
        border: 2px solid #e9ecef;
        border-radius: 20px;
        background: white;
        color: #666;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .quick-filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        transition: left 0.3s ease;
        z-index: -1;
    }
    
    .quick-filter-btn:hover {
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }
    
    .quick-filter-btn:hover::before {
        left: 0;
    }
    
    .quick-filter-btn.active {
        background: var(--primary-gradient);
        border-color: var(--primary-color);
        color: white;
        box-shadow: 0 2px 8px rgba(var(--primary-color), 0.3);
    }
    
    .quick-filter-btn.active::before {
        left: 0;
    }
    
    /* View Controls */
    .view-controls {
        text-align: right;
    }
    
    .view-toggle .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 16px;
    }
    
    .view-toggle .btn-check:checked + .btn {
        background: var(--primary-gradient);
        border-color: var(--primary-color);
        color: white;
    }
    
    /* Enhanced Products Grid */
    .products-grid-view .row {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 1.5rem;
    }
    
    .products-list-view {
        display: none;
    }
    
    .products-list-view.active {
        display: block;
    }
    
    .products-grid-view.active {
        display: block;
    }
    
    .products-grid-view.inactive {
        display: none;
    }
    
    /* List View Styles */
    .product-list-item {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .product-list-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .product-list-image {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        overflow: hidden;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .product-list-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-list-content {
        flex-grow: 1;
    }
    
    .product-list-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }
    
    .product-list-description {
        color: #666;
        margin-bottom: 10px;
        line-height: 1.5;
    }
    
    .product-list-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }
    
    .product-list-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    /* Results Info */
    .results-info {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    
    /* Enhanced Stats Section */
    .stats-section-enhanced {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 80px 0;
        margin-top: 80px;
        position: relative;
        overflow: hidden;
        border-radius: 25px 25px 0 0;
    }
    
    .stats-section-enhanced::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.3;0.1" dur="3s" repeatCount="indefinite"/></circle><circle cx="75" cy="75" r="1.5" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.4;0.1" dur="2s" repeatCount="indefinite"/></circle><circle cx="85" cy="25" r="1" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.2;0.1" dur="4s" repeatCount="indefinite"/></circle></svg>');
        animation: float 20s linear infinite;
    }
    
    /* Filter States */
    .product-item.filtered-out {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
        height: 0;
        overflow: hidden;
        margin: 0;
        padding: 0;
        transition: all 0.4s ease;
    }
    
    .product-item.filtered-in {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
        transition: all 0.4s ease;
    }
    
    /* No Results Message */
    .no-results-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    .no-results-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }
    
    .no-results-modal {
        background: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        max-width: 400px;
        margin: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        transform: scale(0.8);
        transition: transform 0.3s ease;
    }
    
    .no-results-overlay.show .no-results-modal {
        transform: scale(1);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .quick-actions-bar {
            padding: 15px;
        }
        
        .quick-filters {
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .quick-filter-btn {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .view-controls {
            text-align: left;
        }
        
        .results-info {
            justify-content: flex-start;
            margin-top: 10px;
        }
        
        .stat-badge {
            padding: 8px 12px;
            font-size: 12px;
            margin: 3px;
        }
        
        .product-list-item {
            padding: 15px;
        }
        
        .product-list-image {
            width: 80px;
            height: 80px;
            margin-right: 15px;
        }
        
        .product-list-title {
            font-size: 1.1rem;
        }
        
        .product-list-price {
            font-size: 1.25rem;
        }
    }
    
    @media (max-width: 576px) {
        .quick-filters {
            flex-direction: column;
            align-items: center;
        }
        
        .quick-filter-btn {
            width: 200px;
            text-align: center;
        }
        
        .view-toggle {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .view-toggle .btn {
            flex: 1;
        }
        
        .product-list-item {
            padding: 10px;
        }
        
        .product-list-image {
            width: 60px;
            height: 60px;
            margin-right: 10px;
        }
        
        .product-list-actions {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>

<script>
// Enhanced Products Page JavaScript
(function() {
    // Get animation settings
    const animationsEnabled = {{ \App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true' ? 'true' : 'false' }};
    const animationIntensity = {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') }};
    const fireworksEnabled = {{ \App\Models\AppSetting::get('frontend_fireworks_enabled', 'true') === 'true' ? 'true' : 'false' }};
    const respectReducedMotion = {{ \App\Models\AppSetting::get('reduce_motion_respect', 'true') === 'true' ? 'true' : 'false' }};
    
    const prefersReducedMotion = respectReducedMotion && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const effectiveAnimationsEnabled = animationsEnabled && !prefersReducedMotion;
    
    // Apply animation settings
    if (!effectiveAnimationsEnabled) {
        document.body.classList.add('animations-disabled');
    }
    
    let currentView = 'grid';
    let currentFilters = {
        category: '{{ request("category", "all") }}',
        quick: null,
        sort: 'name_asc'
    };
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        initializeProductFiltering();
        
        // Trigger welcome fireworks
        if (effectiveAnimationsEnabled && fireworksEnabled) {
            setTimeout(() => {
                if (typeof window.enhancedFireworks !== 'undefined') {
                    window.enhancedFireworks.triggerWelcomeFireworks();
                }
            }, 1000);
        }
        
        console.log('🛍️ Enhanced Products Page initialized successfully!');
    });
    
    // Category Filtering
    window.filterByCategory = function(category, buttonElement) {
        // Update active button
        document.querySelectorAll('.category-filter-enhanced').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.triggerOnAction(buttonElement);
        }
        
        currentFilters.category = category;
        applyAllFilters();
        
        // Update URL
        updateURL();
        
        // Show notification
        if (typeof window.showEnhancedNotification === 'function') {
            const categoryName = category === 'all' ? 'All Products' : buttonElement.textContent.trim();
            window.showEnhancedNotification(`Showing ${categoryName}`, 'info', 2000);
        }
    };
    
    // Quick Filtering
    window.quickFilter = function(filterType, buttonElement) {
        // Toggle active state
        const isActive = buttonElement.classList.contains('active');
        
        // Clear other quick filters
        document.querySelectorAll('.quick-filter-btn').forEach(btn => btn.classList.remove('active'));
        
        if (!isActive) {
            buttonElement.classList.add('active');
            currentFilters.quick = filterType;
            
            // Trigger fireworks
            if (typeof window.enhancedFireworks !== 'undefined') {
                window.enhancedFireworks.triggerOnAction(buttonElement);
            }
        } else {
            currentFilters.quick = null;
        }
        
        applyAllFilters();
        updateURL();
        
        // Show notification
        if (typeof window.showEnhancedNotification === 'function') {
            const filterNames = {
                'featured': 'Featured Products',
                'offers': 'Products on Sale',
                'new': 'New Arrivals',
                'popular': 'Popular Products'
            };
            
            const message = currentFilters.quick 
                ? `Showing ${filterNames[filterType]}`
                : 'Filter cleared';
            
            window.showEnhancedNotification(message, 'info', 2000);
        }
    };
    
    // Apply All Filters
    function applyAllFilters() {
        const productItems = document.querySelectorAll('.product-item');
        let visibleCount = 0;
        
        productItems.forEach((item, index) => {
            let shouldShow = true;
            
            // Category filter
            if (currentFilters.category !== 'all') {
                shouldShow = shouldShow && item.dataset.category === currentFilters.category;
            }
            
            // Quick filter
            if (currentFilters.quick) {
                switch(currentFilters.quick) {
                    case 'featured':
                        shouldShow = shouldShow && item.dataset.featured === 'yes';
                        break;
                    case 'offers':
                        shouldShow = shouldShow && item.dataset.offer === 'yes';
                        break;
                    case 'new':
                        const createdDate = new Date(item.dataset.created);
                        const thirtyDaysAgo = new Date();
                        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                        shouldShow = shouldShow && createdDate >= thirtyDaysAgo;
                        break;
                    case 'popular':
                        shouldShow = shouldShow && (item.dataset.featured === 'yes' || item.dataset.offer === 'yes');
                        break;
                }
            }
            
            if (shouldShow) {
                item.classList.remove('filtered-out');
                item.classList.add('filtered-in');
                visibleCount++;
                
                // Add stagger animation
                if (effectiveAnimationsEnabled) {
                    setTimeout(() => {
                        item.style.animationDelay = (visibleCount * 0.05) + 's';
                        item.classList.add('animate-scale-in');
                    }, index * 20);
                }
            } else {
                item.classList.remove('filtered-in');
                item.classList.add('filtered-out');
            }
        });
        
        // Update results count
        updateResultsCount(visibleCount);
        
        // Show no results if needed
        if (visibleCount === 0) {
            showNoResultsMessage();
        } else {
            hideNoResultsMessage();
        }
    }
    
    // Sort Products
    window.sortProducts = function(sortType) {
        const container = document.getElementById('products-grid').querySelector('.row');
        const items = Array.from(container.querySelectorAll('.product-item'));
        
        // Show loading
        showLoading();
        
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
            if (effectiveAnimationsEnabled) {
                items.forEach((item, index) => {
                    item.style.animationDelay = (index * 0.02) + 's';
                    item.classList.remove('animate-scale-in');
                    void item.offsetWidth; // Trigger reflow
                    item.classList.add('animate-scale-in');
                });
            }
            
            hideLoading();
            
            currentFilters.sort = sortType;
            updateURL();
            
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
                
                window.showEnhancedNotification(`Sorted by ${sortNames[sortType]}`, 'info', 2000);
            }
            
            // Trigger fireworks
            if (typeof window.enhancedFireworks !== 'undefined') {
                window.enhancedFireworks.startRandomFireworks();
            }
        }, 300);
    };
    
    // Switch View
    window.switchView = function(viewType) {
        currentView = viewType;
        const gridView = document.getElementById('products-grid');
        
        if (viewType === 'list') {
            generateListView();
            gridView.style.display = 'none';
            document.getElementById('products-list').style.display = 'block';
        } else {
            gridView.style.display = 'block';
            const listView = document.getElementById('products-list');
            if (listView) {
                listView.style.display = 'none';
            }
        }
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            setTimeout(() => {
                window.enhancedFireworks.startRandomFireworks();
            }, 200);
        }
    };
    
    // Generate List View
    function generateListView() {
        let listContainer = document.getElementById('products-list');
        
        if (!listContainer) {
            listContainer = document.createElement('div');
            listContainer.id = 'products-list';
            listContainer.className = 'products-list-view';
            document.getElementById('products-container').appendChild(listContainer);
        }
        
        const products = document.querySelectorAll('.product-item');
        let listHTML = '';
        
        products.forEach(product => {
            const productCard = product.querySelector('.product-card-enhanced');
            const productId = product.dataset.productId;
            const name = productCard.querySelector('.product-title-enhanced').textContent;
            const image = productCard.querySelector('img');
            const price = productCard.querySelector('.price-current-enhanced').textContent;
            const description = productCard.querySelector('.product-description-enhanced')?.textContent || '';
            const isInStock = product.dataset.stock === 'yes';
            
            listHTML += `
                <div class="product-list-item" data-product-id="${productId}">
                    <div class="d-flex align-items-center">
                        <div class="product-list-image">
                            ${image ? `<img src="${image.src}" alt="${name}">` : '<div class="bg-light d-flex align-items-center justify-content-center h-100"><i class="fas fa-image fa-2x text-muted"></i></div>'}
                        </div>
                        <div class="product-list-content">
                            <h6 class="product-list-title">${name}</h6>
                            <p class="product-list-description">${description}</p>
                            <div class="product-list-price">${price}</div>
                            <div class="product-list-actions">
                                <div class="quantity-selector-enhanced">
                                    <button type="button" class="quantity-btn-enhanced" onclick="decreaseQuantityEnhanced(${productId})">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity-${productId}" class="quantity-input-enhanced" value="1" min="1">
                                    <button type="button" class="quantity-btn-enhanced" onclick="increaseQuantityEnhanced(${productId}, 99)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                ${isInStock 
                                    ? `<button onclick="addToCartEnhanced(${productId})" class="btn btn-primary-enhanced">
                                         <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                       </button>`
                                    : `<button class="btn btn-secondary" disabled>
                                         <i class="fas fa-times me-1"></i>Out of Stock
                                       </button>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        listContainer.innerHTML = listHTML;
    }
    
    // Clear All Filters
    window.clearAllFilters = function() {
        currentFilters = {
            category: 'all',
            quick: null,
            sort: 'name_asc'
        };
        
        // Reset UI
        document.querySelectorAll('.category-filter-enhanced').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.category-filter-enhanced[data-category="all"]').classList.add('active');
        
        document.querySelectorAll('.quick-filter-btn').forEach(btn => btn.classList.remove('active'));
        
        document.querySelector('.sort-select').value = 'name_asc';
        
        applyAllFilters();
        updateURL();
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.createCelebrationBurst();
        }
        
        // Show notification
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('All filters cleared', 'success', 2000);
        }
    };
    
    // Helper Functions
    function showLoading() {
        document.getElementById('loading-spinner').style.display = 'flex';
        document.getElementById('products-container').style.opacity = '0.5';
    }
    
    function hideLoading() {
        document.getElementById('loading-spinner').style.display = 'none';
        document.getElementById('products-container').style.opacity = '1';
    }
    
    function updateResultsCount(count) {
        const resultsElement = document.getElementById('results-count');
        if (resultsElement) {
            resultsElement.innerHTML = `Showing <strong>${count}</strong> products`;
        }
    }
    
    function showNoResultsMessage() {
        let overlay = document.getElementById('no-results-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'no-results-overlay';
            overlay.className = 'no-results-overlay';
            overlay.innerHTML = `
                <div class="no-results-modal">
                    <div class="no-results-icon">
                        <i class="fas fa-search fa-4x text-muted"></i>
                    </div>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your filters or browse all products.</p>
                    <button class="btn btn-primary-enhanced" onclick="clearAllFilters(); hideNoResultsMessage();">
                        <i class="fas fa-refresh me-2"></i>Show All Products
                    </button>
                </div>
            `;
            document.body.appendChild(overlay);
        }
        
        setTimeout(() => {
            overlay.classList.add('show');
        }, 100);
    }
    
    window.hideNoResultsMessage = function() {
        const overlay = document.getElementById('no-results-overlay');
        if (overlay) {
            overlay.classList.remove('show');
        }
    };
    
    function updateURL() {
        const url = new URL(window.location);
        
        if (currentFilters.category !== 'all') {
            url.searchParams.set('category', currentFilters.category);
        } else {
            url.searchParams.delete('category');
        }
        
        if (currentFilters.quick) {
            url.searchParams.set('filter', currentFilters.quick);
        } else {
            url.searchParams.delete('filter');
        }
        
        if (currentFilters.sort !== 'name_asc') {
            url.searchParams.set('sort', currentFilters.sort);
        } else {
            url.searchParams.delete('sort');
        }
        
        window.history.pushState({}, '', url);
    }
    
    function initializeProductFiltering() {
        // Initialize from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category') || 'all';
        const filter = urlParams.get('filter');
        const sort = urlParams.get('sort') || 'name_asc';
        
        currentFilters.category = category;
        currentFilters.quick = filter;
        currentFilters.sort = sort;
        
        // Set UI state
        if (category !== 'all') {
            document.querySelector(`.category-filter-enhanced[data-category="${category}"]`)?.classList.add('active');
        }
        
        if (filter) {
            document.querySelector(`.quick-filter-btn[data-filter="${filter}"]`)?.classList.add('active');
        }
        
        document.querySelector('.sort-select').value = sort;
        
        // Apply filters
        applyAllFilters();
    }
})();
</script>
@endsection
