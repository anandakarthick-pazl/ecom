<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '{{ $globalCompany->company_name ?? "Your Store" }} - E-commerce Store')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Discover quality products at {{ $globalCompany->company_name ?? "Your Store" }}. Your trusted online shopping destination.')">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, online shopping, products, {{ strtolower($globalCompany->company_name ?? "online store") }}')">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="@yield('og_title', '{{ $globalCompany->company_name ?? "Your Store" }} - E-commerce Store')">
    <meta property="og:description" content="@yield('og_description', 'Discover quality products at {{ $globalCompany->company_name ?? "Your Store" }}. Your trusted online shopping destination.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: {{ 
                ($globalCompany->primary_color && $globalCompany->primary_color !== '#ffffff' && $globalCompany->primary_color !== '#fff') 
                ? $globalCompany->primary_color 
                : '#2c3e50' 
            }};
            --secondary-color: {{ 
                ($globalCompany->secondary_color && $globalCompany->secondary_color !== '#ffffff' && $globalCompany->secondary_color !== '#fff') 
                ? $globalCompany->secondary_color 
                : '#34495e' 
            }};
            --accent-color: #8fbc8f;
            --light-green: #f0f8e8;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-color), 0.25);
        }
        
        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .btn-outline-success {
            color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
        }
        
        .btn-outline-success:hover {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-light-green {
            background-color: var(--light-green);
        }
        
        /* Additional button safety styles */
        .btn {
            border-width: 1px !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        /* Ensure all button variants are visible */
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }
        
        .btn-info {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: white !important;
        }
        
        .btn-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .btn-danger {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }
        
        .btn-light {
            background-color: #f8f9fa !important;
            border-color: #f8f9fa !important;
            color: #212529 !important;
        }
        
        .btn-dark {
            background-color: #343a40 !important;
            border-color: #343a40 !important;
            color: white !important;
        }
        
        /* Button hover states */
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Ensure cart button is always visible */
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545 !important;
            color: white !important;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Quantity selector styles */
        .quantity-selector {
            max-width: 150px;
            margin: 0 auto;
        }
        
        .quantity-selector .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            min-width: 30px;
        }
        
        .quantity-selector input {
            text-align: center;
            font-weight: 500;
            -moz-appearance: textfield;
        }
        
        .quantity-selector input::-webkit-outer-spin-button,
        .quantity-selector input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        /* Product card improvements */
        .product-card .card-body {
            padding: 1rem;
        }
        
        .product-card .product-actions {
            margin-top: auto;
        }
        
        /* Fixed navbar styles */
        .navbar.fixed-top {
            z-index: 1030;
            background-color: white !important;
        }
        
        /* Navigation active state */
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
            position: relative;
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 2px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            transition: color 0.3s ease;
        }
        
        /* Add padding to body to prevent content from hiding behind fixed navbar */
        body {
            padding-top: 76px; /* Adjust based on your navbar height */
        }
        
        @media (max-width: 991px) {
            body {
                padding-top: 66px; /* Slightly less padding on mobile */
            }
            
            .navbar-nav .nav-link.active::after {
                display: none; /* Hide underline on mobile */
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('shop') }}">
                @if($globalCompany->company_logo)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                         alt="{{ $globalCompany->company_name }}" 
                         style="height: 40px; width: auto; object-fit: contain;">
                @else
                    🏪 {{ $globalCompany->company_name ?? 'Your Store' }}
                @endif
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('category') ? 'active' : '' }}" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            @php
                                $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                            @endphp
                            @foreach($categories as $category)
                                <li><a class="dropdown-item" href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}" href="{{ route('products') }}">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('offer.products') ? 'active' : '' }}" href="{{ route('offer.products') }}">
                            <i class="fas fa-tags me-1"></i>Offer Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}" href="{{ route('track.order') }}">Track Order</a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" action="{{ route('search') }}" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search products..." value="{{ request('q') }}">
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>
                        @if($globalCompany->company_logo)
                            <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                 alt="{{ $globalCompany->company_name }}" 
                                 style="height: 30px; width: auto; object-fit: contain; margin-right: 10px;">
                        @else
                            🏪
                        @endif
                        {{ $globalCompany->company_name ?? 'Your Store' }}
                    </h5>
                    <p>Your trusted online shopping destination. Quality products delivered with care to your doorstep.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop') }}" class="text-white-50">Home</a></li>
                        <li><a href="{{ route('track.order') }}" class="text-white-50">Track Order</a></li>
                        <li><a href="#" class="text-white-50">About Us</a></li>
                        <li><a href="#" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    @if($globalCompany->company_phone)
                        <p class="mb-1"><i class="fas fa-phone"></i> {{ $globalCompany->company_phone }}</p>
                    @endif
                    <p class="mb-1"><i class="fas fa-envelope"></i> {{ $globalCompany->company_email ?: 'info@example.com' }}</p>
                    @if($globalCompany->company_address)
                        <p class="mb-1"><i class="fas fa-map-marker-alt"></i> {{ $globalCompany->company_address }}</p>
                    @endif
                    <div class="mt-3">
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $globalCompany->company_name ?? 'Your Store' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- jQuery with multiple CDN fallbacks and integrity check -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" 
            integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" 
            crossorigin="anonymous"></script>
    <script>
        // Fallback jQuery loading if CDN fails
        if (typeof jQuery === 'undefined') {
            console.warn('Primary jQuery CDN failed, loading fallback...');
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"><\/script>');
        }
    </script>
    <script>
        // Final fallback check
        if (typeof jQuery === 'undefined') {
            console.error('All jQuery CDNs failed! Loading local fallback...');
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"><\/script>');
        }
    </script>
    
    <!-- Bootstrap (loads after jQuery is confirmed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ROBUST jQuery Loading Check with Retry Mechanism
        console.log('=== JQUERY LOADING STATUS ===');
        console.log('Initial jQuery check:', typeof jQuery !== 'undefined');
        console.log('Initial $ check:', typeof $ !== 'undefined');
        
        // Function to wait for jQuery to be available
        function waitForJQuery(callback, maxRetries = 50, currentRetry = 0) {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                console.log('✅ jQuery loaded successfully on attempt:', currentRetry + 1);
                console.log('jQuery version:', $.fn.jquery);
                callback();
            } else if (currentRetry < maxRetries) {
                console.log('⏳ Waiting for jQuery... attempt:', currentRetry + 1);
                setTimeout(() => {
                    waitForJQuery(callback, maxRetries, currentRetry + 1);
                }, 100);
            } else {
                console.error('❌ FAILED: jQuery could not load after', maxRetries, 'attempts');
                console.error('This is a critical error that needs immediate attention.');
                alert('Critical Error: Page functionality is limited. Please refresh the page.');
            }
        }
        
        // Initialize layout functionality only when jQuery is ready
        waitForJQuery(function() {
            console.log('🚀 Initializing layout functionality...');
            
            // Global functions that can be called from anywhere (with defensive checks)
            window.updateCartCount = function() {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for updateCartCount');
                    return;
                }
                $.get('{{ route("cart.count") }}', function(data) {
                    $('#cart-count').text(data.count);
                    if(data.count == 0) {
                        $('#cart-count').hide();
                    } else {
                        $('#cart-count').show();
                    }
                }).fail(function() {
                    console.error('Failed to update cart count');
                });
            };
            
            // Add to cart function
            window.addToCart = function(productId, quantity = 1) {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for addToCart');
                    alert('Please refresh the page and try again.');
                    return;
                }
                $.ajax({
                    url: '{{ route("cart.add") }}',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            updateCartCount();
                            showToast(response.message, 'success');
                            // Reset quantity to 1 after successful add
                            $('#quantity-' + productId).val(1);
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToast('Something went wrong!', 'error');
                    }
                });
            };
            
            // Add to cart with quantity from input
            window.addToCartWithQuantity = function(productId) {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for addToCartWithQuantity');
                    return;
                }
                const quantity = $('#quantity-' + productId).val() || 1;
                addToCart(productId, quantity);
            };
            
            // Increment quantity
            window.incrementQuantity = function(productId) {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for incrementQuantity');
                    return;
                }
                const quantityInput = $('#quantity-' + productId);
                const currentValue = parseInt(quantityInput.val());
                quantityInput.val(currentValue + 1);
            };
            
            // Decrement quantity
            window.decrementQuantity = function(productId) {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for decrementQuantity');
                    return;
                }
                const quantityInput = $('#quantity-' + productId);
                const currentValue = parseInt(quantityInput.val());
                const minValue = parseInt(quantityInput.attr('min'));
                
                if (currentValue > minValue) {
                    quantityInput.val(currentValue - 1);
                }
            };
            
            window.showToast = function(message, type) {
                if (typeof $ === 'undefined') {
                    console.error('jQuery not available for showToast');
                    alert(message); // Fallback to basic alert
                    return;
                }
                const toast = $(`
                    <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `);
                
                if(!$('.toast-container').length) {
                    $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
                }
                
                $('.toast-container').append(toast);
                new bootstrap.Toast(toast[0]).show();
            };
            
            // Initialize cart count when jQuery is ready
            $(document).ready(function() {
                console.log('📊 Updating cart count...');
                updateCartCount();
            });
            
            console.log('✅ Layout functionality initialized successfully!');
            console.log('==================================');
        });
    </script>
    
    @stack('scripts')
</body>
</html>
