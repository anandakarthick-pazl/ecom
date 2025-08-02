@extends('layouts.app')

@section('title', 'Special Offers - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Discover amazing deals and special offers on quality products. Limited time discounts available.')

@push('styles')
<style>
/* Ultra Compact Grid Styles for Offers - Match shop page */
.products-grid-compact {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)) !important;
    gap: 0.4rem !important;
    margin-bottom: 1.5rem !important;
    align-items: start !important; /* Prevent stretching */
}

/* Remove flex stretching and minimize white space */
.products-grid-compact .product-card {
    font-size: 0.7rem !important;
    border-radius: 8px !important;
    display: block !important; /* Remove flex */
    height: auto !important; /* Allow natural height */
    min-height: unset !important; /* Remove min-height */
}

.products-grid-compact .product-image-container {
    height: 80px !important;
    margin-bottom: 0.3rem !important;
}

.products-grid-compact .product-content {
    padding: 0.4rem !important;
    display: block !important; /* Remove flex */
}

.products-grid-compact .product-title {
    font-size: 0.7rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0.2rem !important;
    height: 2.2rem !important;
    overflow: hidden !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
}

.products-grid-compact .product-category {
    font-size: 0.6rem !important;
    margin-bottom: 0.2rem !important;
}

.products-grid-compact .product-description {
    display: none !important;
}

.products-grid-compact .current-price {
    font-size: 0.8rem !important;
    font-weight: 700 !important;
}

.products-grid-compact .original-price {
    font-size: 0.65rem !important;
}

/* Remove flex from product footer to prevent stretching */
.products-grid-compact .product-footer {
    margin-top: 0.3rem !important;
    display: block !important; /* Remove flex */
}

.products-grid-compact .price-section {
    margin-bottom: 0.4rem !important;
    display: block !important;
}

.products-grid-compact .product-actions {
    display: block !important; /* Remove flex */
}

.products-grid-compact .btn-add-cart {
    padding: 0.3rem 0.5rem !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
    width: 100% !important;
    display: block !important;
    margin-top: 0.3rem !important;
    white-space: nowrap !important; /* Prevent text wrapping */
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.products-grid-compact .quantity-selector {
    margin-bottom: 0.2rem !important;
    gap: 0.25rem !important;
    display: flex !important;
    justify-content: center !important;
}

.products-grid-compact .qty-btn {
    width: 20px !important;
    height: 20px !important;
    font-size: 0.6rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .qty-input {
    width: 30px !important;
    height: 20px !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .badge-discount {
    font-size: 0.6rem !important;
    padding: 0.15rem 0.3rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .offer-info {
    display: none !important; /* Completely hide offer info in compact grid */
}

/* Remove savings info to save space */
.products-grid-compact .savings-info {
    display: none !important;
}

/* Out of stock styles - compact */
.products-grid-compact .out-of-stock-section {
    display: block !important;
}

.products-grid-compact .btn-out-stock {
    padding: 0.3rem !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
    width: 100% !important;
    margin-bottom: 0.2rem !important;
}

/* Enhanced offer card styles for compact grid */
.products-grid-compact.offers .product-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.8);
    position: relative;
}

.products-grid-compact.offers .product-card::before {
    content: '🔥';
    position: absolute;
    top: 5px;
    right: 5px;
    background: linear-gradient(45deg, #ff6b6b, #feca57);
    color: white;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    font-size: 0.6rem;
    font-weight: bold;
    z-index: 5;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-3px); }
    60% { transform: translateY(-2px); }
}

.products-grid-compact.offers .product-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .products-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr)) !important;
        gap: 0.3rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 70px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.65rem !important;
        height: 2rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.75rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.25rem 0.4rem !important;
        font-size: 0.6rem !important;
    }
}

@media (max-width: 576px) {
    .products-grid-compact {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 0.25rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 60px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.6rem !important;
        height: 1.8rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.7rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.2rem 0.3rem !important;
        font-size: 0.55rem !important;
    }
    
    .products-grid-compact .qty-btn {
        width: 18px !important;
        height: 18px !important;
        font-size: 0.55rem !important;
    }
    
    .products-grid-compact .qty-input {
        width: 25px !important;
        height: 18px !important;
        font-size: 0.6rem !important;
    }
}

@media (max-width: 480px) {
    .products-grid-compact {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.2rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 55px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.55rem !important;
        height: 1.6rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.65rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.15rem 0.25rem !important;
        font-size: 0.5rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="products-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-fire me-3"></i>
                    Special Offers
                </h1>
                <p class="page-subtitle">Discover amazing deals and discounts on quality products</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Category Filters -->
        @if($categories->count() > 0)
        <div class="filters-section">
            <h6 class="filters-title">
                <i class="fas fa-filter me-2"></i>Filter Offers by Category
            </h6>
            <div class="filters-container" id="category-filters">
                <button class="filter-btn {{ request('category', 'all') === 'all' ? 'active' : '' }}" data-category="all">
                    <i class="fas fa-fire me-1"></i>All Offers
                </button>
                @foreach($categories as $category)
                <button class="filter-btn {{ request('category') === $category->slug ? 'active' : '' }}" data-category="{{ $category->slug }}">
                    <i class="fas fa-tag me-1"></i>{{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Loading Spinner -->
        <div class="loading-container" id="loading-spinner" style="display: none;">
            <div class="loading-spinner"></div>
            <p class="loading-text">Loading hot deals...</p>
        </div>

        <!-- Products Grid -->
        <div class="products-container" id="products-container">
            @if($products->count() > 0)
                <div class="products-grid-compact offers">
                    @foreach($products as $product)
                        @include('partials.product-card-modern', ['product' => $product, 'offer' => true])
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if(($frontendPaginationSettings['enabled'] ?? true) && method_exists($products, 'appends'))
                <div class="pagination-container" id="pagination-container">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @endif
            @else
                @include('partials.empty-state', [
                    'icon' => 'fire',
                    'title' => 'No Offers Available',
                    'message' => 'Stay tuned for amazing deals and special offers coming soon!',
                    'action' => 'Browse All Products',
                    'actionUrl' => route('products')
                ])
            @endif
        </div>

        <!-- Stats Section -->
        @if($products->count() > 0)
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ ($frontendPaginationSettings['enabled'] ?? true) && method_exists($products, 'total') ? $products->total() : $products->count() }}</div>
                        <div class="stat-label">Hot Deals Available</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Up to 50%</div>
                        <div class="stat-label">Discount</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Limited</div>
                        <div class="stat-label">Time Offers</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">Free</div>
                        <div class="stat-label">Shipping on ₹500+</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Modern Offers Page Styles - Match products page */
.products-page {
    min-height: 100vh;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, {{ $globalCompany->primary_color ?? '#2563eb' }} 0%, {{ $globalCompany->secondary_color ?? '#10b981' }} 100%);
    color: white;
    padding: 2rem 0 1.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"/><circle cx="80" cy="80" r="1.5" fill="white" opacity="0.15"/><circle cx="90" cy="20" r="1" fill="white" opacity="0.1"/></svg>');
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translate(0, 0); }
    100% { transform: translate(-20px, -20px); }
}

.header-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.page-subtitle {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: 0;
}

/* Filters Section */
.filters-section {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filters-title {
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 1rem;
}

.filters-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-btn {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    background: rgba(37, 99, 235, 0.05);
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.filter-btn.active {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

/* Loading */
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #e5e7eb;
    border-top: 3px solid {{ $globalCompany->primary_color ?? '#2563eb' }};
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #6b7280;
    font-weight: 500;
}

/* Products Grid */
.products-container {
    margin-bottom: 3rem;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Stats Section */
.stats-section {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    padding: 2rem;
    margin: 3rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    background: #f9fafb;
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
}

.stat-icon {
    background: linear-gradient(45deg, {{ $globalCompany->primary_color ?? '#2563eb' }}, {{ $globalCompany->secondary_color ?? '#10b981' }});
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .filters-container {
        justify-content: center;
    }
    
    .filter-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-item {
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .page-header {
        padding: 1.5rem 0 1rem;
    }
    
    .filters-section {
        padding: 1rem;
    }
    
    .filter-btn {
        flex: 1;
        min-width: 120px;
        justify-content: center;
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
    // Initialize offer product filtering
    let isLoading = false;
    const enablePagination = {{ ($frontendPaginationSettings['enabled'] ?? true) ? 'true' : 'false' }};
    
    // Category filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (isLoading) return;
            
            const category = this.dataset.category;
            const currentCategory = document.querySelector('.filter-btn.active')?.dataset.category;
            
            if (category === currentCategory) return;
            
            // Update active state
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            loadOfferProducts(category);
        });
    });
    
    // Pagination click handling
    document.addEventListener('click', function(e) {
        if (e.target.closest('#pagination-container .pagination a')) {
            e.preventDefault();
            
            if (isLoading || !enablePagination) return;
            
            const url = e.target.closest('a').getAttribute('href');
            const urlParams = new URLSearchParams(new URL(url).search);
            const page = urlParams.get('page');
            const category = document.querySelector('.filter-btn.active')?.dataset.category || 'all';
            
            if (page) {
                loadOfferProducts(category, page);
            }
        }
    });
    
    function loadOfferProducts(category, page = 1) {
        if (isLoading) return;
        
        isLoading = true;
        
        // Show loading
        document.getElementById('loading-spinner').style.display = 'flex';
        document.getElementById('products-container').style.display = 'none';
        
        // Prepare request data
        const requestData = new URLSearchParams();
        if (category && category !== 'all') {
            requestData.append('category', category);
        }
        if (enablePagination && page && page > 1) {
            requestData.append('page', page);
        }
        
        // Make AJAX request
        fetch('{{ route("offer.products") }}?' + requestData.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                document.getElementById('products-container').innerHTML = data.html;
                document.getElementById('products-container').style.display = 'block';
                
                if (enablePagination && data.pagination) {
                    const paginationContainer = document.getElementById('pagination-container');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
                
                // Update page title
                let title = 'Special Offers';
                if (category !== 'all') {
                    const categoryBtn = document.querySelector(`[data-category="${category}"]`);
                    if (categoryBtn) {
                        const categoryName = categoryBtn.textContent.trim().replace(/.*\s/, '');
                        title = categoryName + ' Offers';
                    }
                }
                document.title = title + ' - {{ $globalCompany->company_name ?? "Your Store" }}';
                
                // Update URL
                let newUrl = '{{ route("offer.products") }}';
                if (requestData.toString()) {
                    newUrl += '?' + requestData.toString();
                }
                window.history.pushState({ category, page }, '', newUrl);
                
                // Add animations to new content
                document.querySelectorAll('#products-container .product-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('fade-in');
                    }, index * 100);
                });
            }
        })
        .catch(error => {
            console.error('Error loading offer products:', error);
            document.getElementById('products-container').innerHTML = 
                '<div class="alert alert-danger text-center">Error loading offers. Please try again.</div>';
            document.getElementById('products-container').style.display = 'block';
        })
        .finally(() => {
            document.getElementById('loading-spinner').style.display = 'none';
            isLoading = false;
        });
    }
    
    // Handle browser back/forward
    window.addEventListener('popstate', function(e) {
        const state = e.state || {};
        const urlParams = new URLSearchParams(window.location.search);
        const category = state.category || urlParams.get('category') || 'all';
        const page = state.page || urlParams.get('page') || 1;
        
        // Update active filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });
        
        loadOfferProducts(category, page);
    });
    
    // Add fade-in animation to existing products
    document.querySelectorAll('.product-card').forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });
});
</script>
@endpush
@endsection