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
                : '#2563eb' 
            }};
            --secondary-color: {{ 
                ($globalCompany->secondary_color && $globalCompany->secondary_color !== '#ffffff' && $globalCompany->secondary_color !== '#fff') 
                ? $globalCompany->secondary_color 
                : '#10b981' 
            }};
            --accent-color: #f59e0b;
            --surface: #ffffff;
            --background: #f9fafb;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
        }

        /* Base Styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            padding-top: 80px;
        }

        /* Modern Navbar */
        .navbar-modern {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            z-index: 1050;
        }

        .navbar-modern.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: var(--shadow-md);
        }

        .navbar-brand-modern {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .navbar-brand-modern:hover {
            color: var(--primary-color) !important;
            transform: scale(1.02);
        }

        .navbar-brand-modern img {
            height: 40px;
            width: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .navbar-nav-modern .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            border-radius: var(--radius);
            transition: all 0.3s ease;
            position: relative;
            margin: 0 0.25rem;
        }

        .navbar-nav-modern .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(37, 99, 235, 0.05);
            transform: translateY(-1px);
        }

        .navbar-nav-modern .nav-link.active {
            color: var(--primary-color) !important;
            background: rgba(37, 99, 235, 0.1);
            font-weight: 600;
        }

        .navbar-nav-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        /* Search Form */
        .search-form {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            border: 2px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 0.75rem 3rem 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: var(--surface);
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .search-btn {
            position: absolute;
            right: 4px;
            top: 4px;
            bottom: 4px;
            background: var(--primary-color);
            border: none;
            border-radius: var(--radius);
            color: white;
            padding: 0 0.75rem;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        /* Cart Button */
        .cart-btn {
            background: var(--primary-color) !important;
            border: 2px solid var(--primary-color) !important;
            color: white !important;
            border-radius: var(--radius-lg) !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease !important;
            position: relative;
            font-weight: 600;
        }

        .cart-btn:hover {
            background: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444 !important;
            color: white !important;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Dropdown Menu */
        .dropdown-menu-modern {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item-modern {
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            transition: all 0.3s ease;
            color: var(--text-primary);
            font-weight: 500;
        }

        .dropdown-item-modern:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateX(4px);
        }

        /* Mobile Navbar */
        .navbar-toggler-modern {
            border: none;
            padding: 0.5rem;
            border-radius: var(--radius);
            background: rgba(37, 99, 235, 0.1);
        }

        .navbar-toggler-modern:focus {
            box-shadow: none;
        }

        .navbar-toggler-modern .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2837, 99, 235, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Alert Styles */
        .alert-modern {
            border: none;
            border-radius: var(--radius-lg);
            padding: 1rem 1.5rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left: 4px solid var(--secondary-color);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Footer */
        .footer-modern {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            margin-top: 4rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-modern h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-modern .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            padding: 0.25rem 0;
        }

        .footer-modern .footer-link:hover {
            color: white;
            transform: translateX(4px);
        }

        .footer-modern .social-link {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.25rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footer-modern .social-link:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Button Improvements */
        .btn {
            border-radius: var(--radius-lg) !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            border-width: 2px !important;
        }

        .btn-primary {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .btn-primary:hover {
            background: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background: transparent !important;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            transform: translateY(-2px);
        }

        /* Toast Container */
        .toast-container {
            z-index: 1055;
        }

        .toast {
            border-radius: var(--radius-lg);
            border: none;
            box-shadow: var(--shadow-lg);
        }

        /* Utility Classes */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: transform 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
        }

        /* Responsive Adjustments */
        @media (max-width: 991px) {
            body {
                padding-top: 70px;
            }

            .navbar-nav-modern {
                padding: 1rem 0;
            }

            .navbar-nav-modern .nav-link {
                margin: 0.25rem 0;
                text-align: center;
            }

            .search-form {
                margin: 1rem 0;
                max-width: 100%;
            }

            .cart-btn {
                margin-top: 1rem;
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand-modern {
                font-size: 1.25rem;
            }

            .search-input {
                font-size: 1rem;
            }
        }

        /* Loading Animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== COMPACT LAYOUT STYLES ===== */
        /* Banner and Product Size Reductions */
        
        /* Hero Banners - Reduced from 400px to 250px */
        .hero-section .carousel-item img,
        .banner-image,
        .hero-banner {
            height: 250px !important;
            object-fit: cover;
        }
        
        /* Category Images - Reduced from 200px to 150px */
        .category-image,
        .categories-section .card-img-top {
            height: 150px !important;
            object-fit: cover;
        }
        
        /* Product Images - Reduced from 200px to 150px */
        .product-image-container,
        .product-card-header,
        .product-image-wrapper {
            height: 150px !important;
        }
        
        /* Product Grid Layout - More compact spacing */
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
            gap: 1.25rem !important;
        }
        
        /* Cart Product Images - Reduced from 80px to 60px */
        .cart-item img,
        .cart-product-image {
            max-height: 60px !important;
        }
        
        .cart-item .product-placeholder {
            height: 60px !important;
        }
        
        /* Admin Product Grid - More compact */
        .admin .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
        }
        
        .admin .product-card-header {
            height: 100px !important;
        }
        
        /* Responsive adjustments for compact layout */
        @media (max-width: 768px) {
            .hero-section .carousel-item img {
                height: 200px !important;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
                gap: 1rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .hero-section .carousel-item img {
                height: 180px !important;
            }
            
            .products-grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }
            
            .product-image-container {
                height: 120px !important;
            }
            
            .category-image,
            .categories-section .card-img-top {
                height: 120px !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-modern">
        <div class="container">
            <a class="navbar-brand-modern" href="{{ route('shop') }}">
                @if($globalCompany->company_logo)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                         alt="{{ $globalCompany->company_name }}">
                @else
                    <i class="fas fa-store"></i>
                @endif
                <span>{{ $globalCompany->company_name ?? 'Your Store' }}</span>
            </a>
            
            <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav navbar-nav-modern me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('category') ? 'active' : '' }}" 
                           href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large me-1"></i>Categories
                        </a>
                        <ul class="dropdown-menu dropdown-menu-modern">
                            @php
                                $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                            @endphp
                            @foreach($categories as $category)
                                <li>
                                    <a class="dropdown-item dropdown-item-modern" href="{{ route('category', $category->slug) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
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
                            <i class="fas fa-fire me-1"></i>Offers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}" href="{{ route('track.order') }}">
                            <i class="fas fa-search me-1"></i>Track Order
                        </a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="search-form me-3" action="{{ route('search') }}" method="GET">
                    <input class="form-control search-input" type="search" name="q" 
                           placeholder="Search products..." value="{{ request('q') }}">
                    <button class="search-btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Cart Button -->
                <a href="{{ route('cart.index') }}" class="btn cart-btn position-relative">
                    <i class="fas fa-shopping-cart me-2"></i>
                    <span class="d-none d-md-inline">Cart</span>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show m-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Modern Footer -->
    <footer class="footer-modern py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        @if($globalCompany->company_logo)
                            <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                 alt="{{ $globalCompany->company_name }}" 
                                 style="height: 40px; width: auto; object-fit: contain;" class="me-3">
                        @else
                            <i class="fas fa-store fs-2 me-3"></i>
                        @endif
                        <h5 class="mb-0">{{ $globalCompany->company_name ?? 'Your Store' }}</h5>
                    </div>
                    <p class="text-light opacity-75">
                        Your trusted online shopping destination. Quality products delivered with care to your doorstep.
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop') }}" class="footer-link">Home</a></li>
                        <li><a href="{{ route('products') }}" class="footer-link">Products</a></li>
                        <li><a href="{{ route('offer.products') }}" class="footer-link">Offers</a></li>
                        <li><a href="{{ route('track.order') }}" class="footer-link">Track Order</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5>Categories</h5>
                    <ul class="list-unstyled">
                        @foreach($categories->take(4) as $category)
                            <li><a href="{{ route('category', $category->slug) }}" class="footer-link">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h5>Contact Info</h5>
                    @if($globalCompany->company_phone)
                        <p class="mb-2 text-light opacity-75">
                            <i class="fas fa-phone me-2"></i>{{ $globalCompany->company_phone }}
                        </p>
                    @endif
                    <p class="mb-2 text-light opacity-75">
                        <i class="fas fa-envelope me-2"></i>{{ $globalCompany->company_email ?? 'info@example.com' }}
                    </p>
                    @if($globalCompany->company_address)
                        <p class="mb-2 text-light opacity-75">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $globalCompany->company_address }}
                        </p>
                    @endif
                </div>
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light opacity-75">
                        &copy; {{ date('Y') }} {{ $globalCompany->company_name ?? 'Your Store' }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-light opacity-75">
                        <i class="fas fa-heart text-danger"></i> Made with care for you
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" 
            integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" 
            crossorigin="anonymous"></script>
    <script>
        // Fallback jQuery loading
        if (typeof jQuery === 'undefined') {
            document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"><\/script>');
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Enhanced Layout Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar scroll effect
            const navbar = document.querySelector('.navbar-modern');
            let lastScrollTop = 0;
            
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                lastScrollTop = scrollTop;
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // jQuery-dependent functions
        function waitForJQuery(callback, maxRetries = 50, currentRetry = 0) {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                callback();
            } else if (currentRetry < maxRetries) {
                setTimeout(() => {
                    waitForJQuery(callback, maxRetries, currentRetry + 1);
                }, 100);
            } else {
                console.error('jQuery failed to load');
            }
        }
        
        waitForJQuery(function() {
            // Update cart count
            window.updateCartCount = function() {
                $.get('{{ route("cart.count") }}', function(data) {
                    const countElement = $('#cart-count');
                    countElement.text(data.count);
                    
                    if(data.count == 0) {
                        countElement.hide();
                    } else {
                        countElement.show();
                        // Add pulse animation for new items
                        countElement.addClass('animate__animated animate__pulse');
                        setTimeout(() => {
                            countElement.removeClass('animate__animated animate__pulse');
                        }, 1000);
                    }
                }).fail(function() {
                    console.error('Failed to update cart count');
                });
            };
            
            // Add to cart function with loading state
            window.addToCart = function(productId, quantity = 1) {
                const button = $(`button[onclick*="${productId}"]`);
                const originalText = button.html();
                
                // Show loading state
                button.prop('disabled', true).html('<span class="spinner me-2"></span>Adding...');
                
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
                            // Reset quantity to 1
                            $(`#quantity-${productId}`).val(1);
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToast('Something went wrong!', 'error');
                    },
                    complete: function() {
                        // Restore button state
                        button.prop('disabled', false).html(originalText);
                    }
                });
            };
            
            // Add to cart with quantity
            window.addToCartWithQuantity = function(productId) {
                const quantity = $(`#quantity-${productId}`).val() || 1;
                addToCart(productId, quantity);
            };
            
            // Quantity controls
            window.incrementQuantity = function(productId) {
                const input = $(`#quantity-${productId}`);
                const currentValue = parseInt(input.val());
                const maxValue = parseInt(input.attr('max'));
                
                if (currentValue < maxValue) {
                    input.val(currentValue + 1);
                }
            };
            
            window.decrementQuantity = function(productId) {
                const input = $(`#quantity-${productId}`);
                const currentValue = parseInt(input.val());
                const minValue = parseInt(input.attr('min')) || 1;
                
                if (currentValue > minValue) {
                    input.val(currentValue - 1);
                }
            };
            
            // Enhanced toast function
            window.showToast = function(message, type = 'success') {
                const toastId = 'toast-' + Date.now();
                const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
                
                const toast = $(`
                    <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${iconClass} me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `);
                
                $('.toast-container').append(toast);
                const bsToast = new bootstrap.Toast(toast[0], { delay: 4000 });
                bsToast.show();
                
                // Remove from DOM after hiding
                toast[0].addEventListener('hidden.bs.toast', function() {
                    $(this).remove();
                });
            };
            
            // Initialize cart count
            updateCartCount();
            
            // Search form enhancement
            $('.search-form').on('submit', function() {
                const btn = $(this).find('.search-btn');
                btn.html('<span class="spinner"></span>');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>