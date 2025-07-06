<?php $__env->startSection('title', 'Offer Products - ' . ($globalCompany->company_name ?? 'Your Store')); ?>
<?php $__env->startSection('meta_description', 'Great deals and offers on quality products. Save money with our special discounts and promotional offers.'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 mb-3">
                <i class="fas fa-tags text-danger me-2"></i>
                Offer Products
            </h1>
            <p class="lead text-muted">Amazing deals and discounts on quality products</p>
            <div class="mt-3">
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-fire me-1"></i>Limited Time Offers
                </span>
            </div>
        </div>
    </div>

    <!-- Offer Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white text-center py-4">
                    <h3 class="mb-2">
                        <i class="fas fa-gift me-2"></i>Special Offers Just For You!
                    </h3>
                    <p class="mb-3">Save big on your favorite products with our exclusive deals</p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <i class="fas fa-percent fa-2x"></i>
                            </div>
                            <h6>Up to 50% OFF</h6>
                            <small>On selected items</small>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <i class="fas fa-shipping-fast fa-2x"></i>
                            </div>
                            <h6>Free Shipping</h6>
                            <small>On orders above ₹500</small>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-2">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <h6>Limited Time</h6>
                            <small>Don't miss out!</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <?php if($categories->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Offers by Category
                    </h6>
                    <div class="d-flex flex-wrap gap-2" id="category-filters">
                        <button class="btn btn-outline-danger btn-sm category-filter <?php echo e(request('category', 'all') === 'all' ? 'active' : ''); ?>" data-category="all">
                            <i class="fas fa-tags me-1"></i>All Offers
                        </button>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button class="btn btn-outline-secondary btn-sm category-filter <?php echo e(request('category') === $category->slug ? 'active' : ''); ?>" data-category="<?php echo e($category->slug); ?>">
                            <?php echo e($category->name); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Offer Products Grid -->
    <!-- Loading Spinner -->
    <div class="text-center my-4" id="loading-spinner" style="display: none;">
        <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading offers...</p>
    </div>

    <!-- Products Grid -->
    <div id="products-container">
        <?php echo $__env->make('partials.offer-products-grid', ['products' => $products], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- Pagination -->
    <?php if($enablePagination && isset($products) && method_exists($products, 'appends')): ?>
    <div class="d-flex justify-content-center mt-4" id="pagination-container">
        <?php echo e($products->appends(request()->query())->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Offer Stats Section -->
<?php if($products->count() > 0): ?>
<div class="bg-light py-4 mt-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="mb-3">
                    <i class="fas fa-tags fa-2x text-danger"></i>
                </div>
                <h5><?php echo e($enablePagination && method_exists($products, 'total') ? $products->total() : $products->count()); ?> Offers</h5>
                <p class="text-muted">Products on sale</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <i class="fas fa-percent fa-2x text-success"></i>
                </div>
                <h5>Up to 50% OFF</h5>
                <p class="text-muted">Maximum discount</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h5>Limited Time</h5>
                <p class="text-muted">Don't miss out</p>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <i class="fas fa-truck fa-2x text-info"></i>
                </div>
                <h5>Free Shipping</h5>
                <p class="text-muted">On orders above ₹500</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Newsletter Signup -->
<div class="bg-primary text-white py-4 mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-2">
                    <i class="fas fa-envelope me-2"></i>Never Miss an Offer!
                </h4>
                <p class="mb-0">Subscribe to our newsletter and be the first to know about new deals and exclusive offers.</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-light btn-lg">
                    <i class="fas fa-bell me-2"></i>Subscribe Now
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.offer-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 2px solid #ffc107;
    position: relative;
    overflow: hidden;
}

.offer-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #dc3545, #ffc107, #28a745);
    z-index: 1;
}

.offer-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(220, 53, 69, 0.3);
}

.quantity-selector {
    max-width: 150px;
}

.quantity-selector .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.quantity-selector input {
    text-align: center;
    font-weight: 500;
}

.price-section {
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.card-title {
    font-weight: 600;
    color: #333;
}

.badge {
    font-size: 0.75rem;
}

/* Pulsing animation for offer badges */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.offer-card .badge {
    animation: pulse 2s infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .product-card .card-body {
        padding: 0.75rem;
    }
    
    .price-section .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .price-section .text-end {
        text-align: start !important;
    }
}

/* Gradient background for offer banner */
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

/* Loading spinner */
#loading-spinner {
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Category filters */
.category-filter {
    transition: all 0.3s ease;
}

.category-filter.active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.category-filter:hover {
    transform: translateY(-2px);
}
</style>

<script>
// Ensure jQuery is loaded before running our code
(function() {
    function initializeOfferFilters() {
        // Check if jQuery is available
        if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
            console.error('jQuery is not loaded. Offer category filters cannot be initialized.');
            return;
        }
        
        console.log('Initializing offer category filters...');
        
        // Initialize current state from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        let currentCategory = urlParams.get('category') || 'all';
        let currentPage = parseInt(urlParams.get('page')) || 1;
        let isLoading = false;
        let enablePagination = <?php echo e($enablePagination ? 'true' : 'false'); ?>;
        
        // Set initial active filter based on URL
        $('.category-filter').removeClass('active');
        $('.category-filter[data-category="' + currentCategory + '"]').addClass('active');
        
        // Category filter click
        $('.category-filter').on('click', function(e) {
            e.preventDefault();
            
            if (isLoading) return;
            
            const category = $(this).data('category');
            if (category === currentCategory) return; // No change needed
            
            currentCategory = category;
            currentPage = 1;
            
            // Update active state
            $('.category-filter').removeClass('active');
            $(this).addClass('active');
            
            loadProducts(category, 1);
        });
        
        // Pagination click
        $(document).on('click', '#pagination-container .pagination a', function(e) {
            e.preventDefault();
            
            if (isLoading || !enablePagination) return;
            
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page');
            
            if (page && page !== currentPage.toString()) {
                currentPage = parseInt(page);
                loadProducts(currentCategory, page);
            }
        });
        
        function loadProducts(category, page) {
            if (isLoading) return;
            
            isLoading = true;
            
            console.log('Loading offer products:', { category, page }); // Debug log
            
            // Show loading spinner
            $('#loading-spinner').show();
            $('#products-container').hide();
            if (enablePagination) {
                $('#pagination-container').hide();
            }
            
            let requestData = {};
            if (category && category !== 'all') {
                requestData.category = category;
            }
            if (enablePagination && page && page > 1) {
                requestData.page = page;
            }
            
            console.log('Request data:', requestData); // Debug log
            
            $.ajax({
                url: '<?php echo e(route("offer.products")); ?>',
                method: 'GET',
                data: requestData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Response received:', response); // Debug log
                    
                    if (response.html) {
                        // Update products grid
                        $('#products-container').html(response.html).show();
                        
                        // Update pagination if enabled
                        if (enablePagination && response.pagination) {
                            $('#pagination-container').html(response.pagination).show();
                        } else if (enablePagination) {
                            $('#pagination-container').hide();
                        }
                        
                        // Automatic scroll removed per user request
                        // Users can manually scroll if needed
                        // $('html, body').animate({
                        //     scrollTop: $('#products-container').offset().top - 100
                        // }, 500);
                        
                        // Update page title
                        let title = 'Offer Products';
                        if (category !== 'all') {
                            const categoryName = $('.category-filter[data-category="' + category + '"]').text().trim();
                            title = categoryName + ' Offers';
                        }
                        document.title = title + ' - <?php echo e($globalCompany->company_name ?? "Your Store"); ?>';
                        
                        // Update URL without page reload
                        let newUrl = '<?php echo e(route("offer.products")); ?>';
                        let params = [];
                        if (category && category !== 'all') {
                            params.push('category=' + encodeURIComponent(category));
                        }
                        if (enablePagination && page && page > 1) {
                            params.push('page=' + page);
                        }
                        if (params.length > 0) {
                            newUrl += '?' + params.join('&');
                        }
                        
                        window.history.pushState({ category, page }, '', newUrl);
                    } else {
                        console.error('Invalid response format:', response);
                        $('#products-container').html(
                            '<div class="alert alert-danger">Invalid response format. Please try again.</div>'
                        ).show();
                    }
                    
                    isLoading = false;
                },
                error: function(xhr) {
                    console.error('Error loading offers:', xhr);
                    console.error('Response text:', xhr.responseText);
                    
                    // Show error message
                    $('#products-container').html(
                        '<div class="alert alert-danger">Error loading offers. Please try again.</div>'
                    ).show();
                    
                    isLoading = false;
                },
                complete: function() {
                    // Hide loading spinner
                    $('#loading-spinner').hide();
                }
            });
        }
        
        // Handle browser back/forward
        window.addEventListener('popstate', function(e) {
            const state = e.state || {};
            const urlParams = new URLSearchParams(window.location.search);
            const category = state.category || urlParams.get('category') || 'all';
            const page = state.page || parseInt(urlParams.get('page')) || 1;
            
            currentCategory = category;
            currentPage = page;
            
            // Update active filter
            $('.category-filter').removeClass('active');
            $('.category-filter[data-category="' + category + '"]').addClass('active');
            
            loadProducts(category, page);
        });
        
        console.log('Offer category filters initialized successfully!');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Add a small delay to ensure jQuery is fully loaded
            setTimeout(initializeOfferFilters, 100);
        });
    } else {
        // DOM is already loaded
        setTimeout(initializeOfferFilters, 100);
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/offer-products.blade.php ENDPATH**/ ?>