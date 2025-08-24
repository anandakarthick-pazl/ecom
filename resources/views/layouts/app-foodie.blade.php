<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
    $pageTitle = $globalCompany->company_name ?? 'Crackers Store';
    $pageDescription = 'Premium Quality Crackers & Fireworks for All Celebrations. Shop from ' . ($globalCompany->company_name ?? 'Crackers Store') . ' - Your trusted celebration partner.';
    @endphp
    
    <title>@yield('title', $pageTitle . ' - Premium Crackers & Fireworks')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', $pageDescription)">
    <meta name="keywords" content="@yield('meta_keywords', 'crackers, fireworks, diwali crackers, celebration, sparklers, rockets, online crackers')">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="@yield('og_title', $pageTitle . ' - Premium Crackers & Fireworks')">
    <meta property="og:description" content="@yield('og_description', $pageDescription)">
    <meta property="og:image" content="@yield('og_image', asset('images/crackers-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #ff6b35;
            --secondary-color: #f77b00;
            --accent-color: #28a745;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --surface: #ffffff;
            --background: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-light: #999999;
            --border: #e0e0e0;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.16);
            --shadow-xl: 0 16px 48px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Modern Navbar */
        .navbar-foodie {
            background: var(--surface);
            padding: 1rem 0;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand-foodie {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand-foodie .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .navbar-nav-foodie {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-link-foodie {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link-foodie:hover {
            color: var(--primary-color);
        }

        .nav-link-foodie.active {
            color: var(--primary-color);
        }

        .nav-link-foodie.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        /* Hero Section */
        .hero-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-foodie {
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-foodie-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-foodie-primary:hover {
            background: #ff5722;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-foodie-outline {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border);
        }

        .btn-foodie-outline:hover {
            background: var(--text-primary);
            color: white;
            border-color: var(--text-primary);
        }

        /* Modal Styles */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.7);
        }
        
        .modal-content {
            animation: slideInUp 0.3s ease;
        }
        
        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Food Cards */
        .food-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            height: 100%;
            cursor: pointer;
        }

        .food-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .food-card-checkered {
            background-image: 
                linear-gradient(45deg, #e8f5e9 25%, transparent 25%), 
                linear-gradient(-45deg, #e8f5e9 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #e8f5e9 75%), 
                linear-gradient(-45deg, transparent 75%, #e8f5e9 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            padding: 1rem;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .food-card-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: var(--shadow-md);
        }

        .food-card-content {
            padding: 1.5rem;
        }

        .food-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .food-card-description {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .food-card-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .food-card-cart {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .food-card-cart:hover {
            background: #ff5722;
            transform: scale(1.1);
        }

        /* Review Stars */
        .review-stars {
            display: flex;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }

        .star {
            color: #ffc107;
            font-size: 1rem;
        }

        /* Cart Icon Badge - Fixed Alignment */
        .cart-icon-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cart-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0 4px;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Dropdown Styles */
        .dropdown-menu {
            margin-top: 0.5rem;
            animation: fadeInDown 0.3s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item:hover {
            background: var(--background);
            color: var(--primary-color);
        }

        .navbar-nav-foodie .dropdown:hover .dropdown-menu {
            display: block;
        }

        /* Search Bar Focus */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        /* Carousel Controls */
        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            border: none;
        }

        .carousel-control:hover {
            background: var(--primary-color);
            color: white;
        }

        .carousel-control.prev {
            left: -24px;
        }

        .carousel-control.next {
            right: -24px;
        }

        /* Floating Cart Button */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(255, 107, 53, 0.4);
            z-index: 999;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
        }
        
        .floating-cart:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(255, 107, 53, 0.5);
            color: white;
        }
        
        .floating-cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            border: 2px solid white;
        }
        
        /* Add to Cart Section in Product Card */
        .add-to-cart-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .qty-input {
            width: 60px;
            padding: 6px;
            border: 1px solid var(--border);
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        
        .btn-add-cart {
            flex: 1;
            padding: 8px 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-add-cart:hover {
            background: #ff5722;
            transform: translateY(-2px);
        }
        
        .btn-add-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Footer */
        .footer-foodie {
            background: var(--text-primary);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-section {
                padding: 40px 0;
            }

            .navbar-nav-foodie {
                gap: 1rem;
            }

            .carousel-control {
                width: 36px;
                height: 36px;
            }

            .carousel-control.prev {
                left: 10px;
            }

            .carousel-control.next {
                right: 10px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar-foodie">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('shop') }}" class="navbar-brand-foodie">
                    <span class="brand-icon">🎆</span>
                    <span>{{ $globalCompany->company_name ?? 'Crackers' }}</span>
                </a>
                
                <!-- Desktop Navigation -->
                <ul class="navbar-nav-foodie d-none d-lg-flex">
                    <li><a href="{{ route('shop') }}" class="nav-link-foodie {{ request()->routeIs('shop') ? 'active' : '' }}">Home</a></li>
                    
                    <!-- CRACKERS Menu Item -->
                    <li><a href="{{ route('products') }}" class="nav-link-foodie {{ request()->routeIs('products') ? 'active' : '' }}">Crackers</a></li>
                    
                    <!-- Categories Dropdown -->
                    <li class="dropdown" style="position: relative;">
                        <a href="#" class="nav-link-foodie dropdown-toggle" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu" style="background: white; border: none; box-shadow: var(--shadow-md); border-radius: 12px; padding: 0.5rem; min-width: 200px;">
                            @php
                                $navCategories = \App\Models\Category::active()->parent()->orderBy('sort_order')->limit(10)->get();
                            @endphp
                            @foreach($navCategories as $category)
                            <li>
                                <a class="dropdown-item" href="{{ route('category', $category->slug) }}" 
                                   style="padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.3s;">
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                            @if($navCategories->count() >= 10)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('products') }}" 
                                   style="padding: 0.5rem 1rem; border-radius: 8px; color: var(--primary-color); font-weight: 600;">
                                    View All Categories →
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    
                    <li><a href="{{ route('offer.products') }}" class="nav-link-foodie {{ request()->routeIs('offer.products') ? 'active' : '' }}">Offers</a></li>
                    <li><a href="{{ route('track.order') }}" class="nav-link-foodie {{ request()->routeIs('track.order') ? 'active' : '' }}">Track Order</a></li>
                </ul>
                
                <!-- Search Bar -->
                {{-- <div class="d-none d-md-flex align-items-center" style="flex: 1; max-width: 400px; margin: 0 2rem;">
                    <form action="{{ route('search') }}" method="GET" class="w-100">
                        <div class="input-group" style="background: #f8f9fa; border-radius: 50px; overflow: hidden;">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request()->get('q') }}" 
                                   placeholder="Search for crackers..." 
                                   style="border: none; background: transparent; padding: 10px 20px; outline: none; flex: 1;">
                            <button type="submit" 
                                    style="background: var(--primary-color); border: none; padding: 10px 20px; color: white; cursor: pointer;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div> --}}
                
                <!-- Right Side Actions -->
                <div class="d-flex align-items-center gap-3">
                    <!-- User Account -->
                    {{-- <div class="dropdown">
                        <a href="#" class="nav-link-foodie d-none d-md-block dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="background: white; border: none; box-shadow: var(--shadow-md); border-radius: 12px; padding: 0.5rem;">
                            <li><a class="dropdown-item" href="/login" style="padding: 0.5rem 1rem; border-radius: 8px;">Login</a></li>
                            <li><a class="dropdown-item" href="/register" style="padding: 0.5rem 1rem; border-radius: 8px;">Register</a></li>
                        </ul>
                    </div> --}}
                    
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="cart-icon-wrapper position-relative">
                        <i class="fas fa-shopping-cart" style="font-size: 1.25rem;"></i>
                        <span class="cart-badge" id="cart-count" style="display: none;">0</span>
                    </a>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="d-lg-none" style="background: none; border: none; font-size: 1.5rem;" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: white; z-index: 9999; padding: 2rem; overflow-y: auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="navbar-brand-foodie">
                <span class="brand-icon">🎆</span>
                <span>{{ $globalCompany->company_name ?? 'Crackers' }}</span>
            </span>
            <button onclick="toggleMobileMenu()" style="background: none; border: none; font-size: 1.5rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Mobile Search -->
        <form action="{{ route('search') }}" method="GET" class="mb-4">
            <div class="input-group" style="background: #f8f9fa; border-radius: 50px; overflow: hidden;">
                <input type="text" 
                       name="q" 
                       placeholder="Search..." 
                       style="border: none; background: transparent; padding: 10px 20px; outline: none; flex: 1;">
                <button type="submit" 
                        style="background: var(--primary-color); border: none; padding: 10px 20px; color: white;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        
        <ul style="list-style: none; padding: 0;">
            <li class="mb-3"><a href="{{ route('shop') }}" class="nav-link-foodie">Home</a></li>
            <li class="mb-3"><a href="{{ route('products') }}" class="nav-link-foodie">Crackers</a></li>
            
            <!-- Categories -->
            <li class="mb-3">
                <a href="#" class="nav-link-foodie d-flex justify-content-between align-items-center" onclick="toggleMobileCategories(event)">
                    Categories
                    <i class="fas fa-chevron-down" id="categoriesChevron"></i>
                </a>
                <ul id="mobileCategories" style="display: none; list-style: none; padding-left: 1.5rem; margin-top: 0.5rem;">
                    @php
                        $mobileCategories = \App\Models\Category::active()->parent()->orderBy('sort_order')->limit(10)->get();
                    @endphp
                    @foreach($mobileCategories as $category)
                    <li class="mb-2">
                        <a href="{{ route('category', $category->slug) }}" style="color: var(--text-secondary); text-decoration: none;">
                            {{ $category->name }}
                        </a>
                    </li>
                    @endforeach
                    @if($mobileCategories->count() >= 10)
                    <li class="mt-2">
                        <a href="{{ route('products') }}" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                            View All Categories →
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            
            <li class="mb-3"><a href="{{ route('offer.products') }}" class="nav-link-foodie">Offers</a></li>
            <li class="mb-3"><a href="{{ route('track.order') }}" class="nav-link-foodie">Track Order</a></li>
            <li class="mb-3"><a href="{{ route('cart.index') }}" class="nav-link-foodie">Cart <span class="badge bg-primary ms-2" id="mobile-cart-count">0</span></a></li>
            <li><hr style="margin: 1rem 0;"></li>
            <li class="mb-3"><a href="/login" class="nav-link-foodie">Login</a></li>
            <li><a href="/register" class="btn-foodie btn-foodie-primary w-100">SIGN UP</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    @yield('content')
    
    <!-- Floating Cart Button -->
    <a href="{{ route('cart.index') }}" class="floating-cart">
        <i class="fas fa-shopping-cart" style="font-size: 1.5rem;"></i>
        <span class="floating-cart-count" id="floating-cart-count" style="display: none;">0</span>
    </a>

    <!-- Footer -->
    <footer class="footer-foodie">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">About Us</h4>
                    <p>Premium quality crackers and fireworks for all your celebrations. Safe, certified, and spectacular!</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">Quick Links</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li class="mb-2"><a href="{{ route('shop') }}" style="color: white; text-decoration: none;">Home</a></li>
                        <li class="mb-2"><a href="{{ route('products') }}" style="color: white; text-decoration: none;">Products</a></li>
                        <li class="mb-2"><a href="{{ route('track.order') }}" style="color: white; text-decoration: none;">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3">Contact Us</h4>
                    <p><i class="fas fa-phone me-2"></i> {{ $globalCompany->contact_number ?? '+1234567890' }}</p>
                    <p><i class="fas fa-envelope me-2"></i> {{ $globalCompany->support_email ?? 'info@crackers.com' }}</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i> {{ $globalCompany->address ?? 'Your Location' }}</p>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 {{ $globalCompany->company_name ?? 'Crackers Store' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
        
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Update cart count function - Fixed to update all cart badges
        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const count = data.count || 0;
                    
                    // Update navbar cart count
                    const badge = document.getElementById('cart-count');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'flex' : 'none';
                    }
                    
                    // Update mobile cart count
                    const mobileBadge = document.getElementById('mobile-cart-count');
                    if (mobileBadge) {
                        mobileBadge.textContent = count;
                        mobileBadge.style.display = count > 0 ? 'inline-block' : 'none';
                    }
                    
                    // Update floating cart count
                    const floatingBadge = document.getElementById('floating-cart-count');
                    if (floatingBadge) {
                        floatingBadge.textContent = count;
                        floatingBadge.style.display = count > 0 ? 'flex' : 'none';
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));
        }
        
        // Toggle mobile categories
        function toggleMobileCategories(event) {
            event.preventDefault();
            const categoriesList = document.getElementById('mobileCategories');
            const chevron = document.getElementById('categoriesChevron');
            
            if (categoriesList.style.display === 'none') {
                categoriesList.style.display = 'block';
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            } else {
                categoriesList.style.display = 'none';
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        }

        // Add to cart function with immediate count update
        function addToCart(productId, quantity = 1) {
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Immediately update cart count
                    updateCartCount();
                    // Show success message
                    showToast(data.message || 'Item added to cart!', 'success');
                } else {
                    showToast(data.message || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Something went wrong!', 'error');
            });
        }
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            `;
            document.body.appendChild(container);
            
            // Add toast styles
            const style = document.createElement('style');
            style.textContent = `
                .toast-notification {
                    background: white;
                    padding: 12px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    min-width: 250px;
                    animation: slideIn 0.3s ease;
                }
                .toast-notification.success {
                    border-left: 4px solid #28a745;
                    color: #28a745;
                }
                .toast-notification.error {
                    border-left: 4px solid #dc3545;
                    color: #dc3545;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
            
            return container;
        }
    </script>
    
    @stack('scripts')
</body>
</html>
