@extends('layouts.app')

@section('title', 'Special Offers - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Discover amazing deals and special offers on quality products. Limited time discounts available.')

@section('content')
<div class="offers-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-fire me-3"></i>
                    Special Offers
                </h1>
                <p class="page-subtitle">Amazing deals and discounts on quality products</p>
                <div class="offers-badge">
                    <i class="fas fa-bolt me-2"></i>
                    Limited Time Deals
                </div>
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
                <button class="filter-btn offer-filter {{ request('category', 'all') === 'all' ? 'active' : '' }}" data-category="all">
                    <i class="fas fa-fire me-1"></i>All Offers
                </button>
                @foreach($categories as $category)
                <button class="filter-btn offer-filter {{ request('category') === $category->slug ? 'active' : '' }}" data-category="{{ $category->slug }}">
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
                <div class="products-grid offers">
                    @foreach($products as $product)
                        @include('partials.product-card-modern', ['product' => $product, 'offer' => true])
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($enablePagination && method_exists($products, 'appends'))
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

        <!-- Offer Features -->
        <div class="offer-features">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Up to 50% Off</h3>
                        <p>Massive savings on selected items</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Limited Time</h3>
                        <p>Don't miss out on these deals</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Free Shipping</h3>
                        <p>On all discounted items</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Quality Guaranteed</h3>
                        <p>Same great quality, better price</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Offers Page Styles */
.offers-page {
    min-height: 100vh;
}

/* Page Header with Fire Theme */
.page-header {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 50%, #dc2626 100%);
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.15"><animate attributeName="opacity" values="0.1;0.3;0.1" dur="2s" repeatCount="indefinite"/></circle><circle cx="80" cy="80" r="1.5" fill="white" opacity="0.2"><animate attributeName="opacity" values="0.1;0.4;0.1" dur="1.5s" repeatCount="indefinite"/></circle><circle cx="90" cy="20" r="1" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.25;0.1" dur="3s" repeatCount="indefinite"/></circle></svg>');
    animation: float 15s linear infinite;
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
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.page-subtitle {
    font-size: 1.125rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.offers-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Enhanced Filters for Offers */
.filters-section {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 2px solid rgba(255, 107, 107, 0.1);
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

.offer-filter {
    background: linear-gradient(45deg, rgba(255, 107, 107, 0.05), rgba(239, 68, 68, 0.05));
    border: 2px solid rgba(255, 107, 107, 0.2);
    color: #dc2626;
}

.offer-filter:hover {
    background: linear-gradient(45deg, rgba(255, 107, 107, 0.1), rgba(239, 68, 68, 0.1));
    border-color: #ff6b6b;
    color: #dc2626;
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.offer-filter.active {
    background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
    border-color: #ff6b6b;
    color: white;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    transform: translateY(-2px);
    animation: offerPulse 2s ease-in-out infinite;
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
    border-top: 3px solid #ff6b6b;
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

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.products-grid.offers {
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
}

/* Product Card Styles - Enhanced for Offers */
.product-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}

.offer-card {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box, 
                linear-gradient(45deg, rgba(255, 107, 107, 0.2), rgba(239, 68, 68, 0.2)) border-box;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.offer-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
}

.product-image-container {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 2rem;
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

.badge-discount {
    background: linear-gradient(45deg, #ef4444, #dc2626);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: offerPulse 3s ease-in-out infinite;
}

.badge-featured {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.9);
    color: #f59e0b;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    z-index: 2;
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
    z-index: 3;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.quick-actions {
    display: flex;
    gap: 0.75rem;
}

.quick-btn {
    background: #ffffff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.quick-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    transform: scale(1.1);
}

.product-content {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 0.75rem;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.product-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.product-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.4;
    margin-bottom: 1rem;
    flex: 1;
}

.product-footer {
    margin-top: auto;
}

.price-section {
    margin-bottom: 1rem;
}

.current-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.original-price {
    font-size: 0.875rem;
    text-decoration: line-through;
    color: #6b7280;
    margin-left: 0.5rem;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.qty-btn {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1f2937;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.qty-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.qty-input {
    width: 50px;
    height: 32px;
    text-align: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 500;
}

.btn-add-cart {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-add-cart:hover {
    background: {{ $globalCompany->secondary_color ?? '#10b981' }};
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.btn-add-cart.offer {
    background: linear-gradient(45deg, #ef4444, #dc2626);
}

.btn-add-cart.offer:hover {
    background: linear-gradient(45deg, #dc2626, #b91c1c);
}

.btn-out-stock {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0.7;
    cursor: not-allowed;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

/* Offer Features */
.offer-features {
    background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 100%);
    border-radius: 16px;
    padding: 2rem;
    margin: 3rem 0;
    border: 1px solid rgba(255, 107, 107, 0.1);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 107, 107, 0.1);
}

.feature-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    border-color: rgba(255, 107, 107, 0.2);
}

.feature-icon {
    background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.feature-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.feature-content p {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
}

/* Special Animation for Offers */
@keyframes offerPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .products-grid.offers {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
    }
    
    .filters-container {
        justify-content: center;
    }
    
    .filter-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .feature-item {
        padding: 1rem;
    }
    
    .feature-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
}

@media (max-width: 576px) {
    .products-grid.offers {
        grid-template-columns: 1fr;
    }
    
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
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .feature-item {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem 1rem;
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
    // Initialize offer filtering
    let isLoading = false;
    const enablePagination = {{ $enablePagination ? 'true' : 'false' }};
    
    // Category filter functionality
    document.querySelectorAll('.offer-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            if (isLoading) return;
            
            const category = this.dataset.category;
            const currentCategory = document.querySelector('.offer-filter.active')?.dataset.category;
            
            if (category === currentCategory) return;
            
            // Update active state
            document.querySelectorAll('.offer-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            loadOffers(category);
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
            const category = document.querySelector('.offer-filter.active')?.dataset.category || 'all';
            
            if (page) {
                loadOffers(category, page);
            }
        }
    });
    
    function loadOffers(category, page = 1) {
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
            console.error('Error loading offers:', error);
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
        document.querySelectorAll('.offer-filter').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });
        
        loadOffers(category, page);
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