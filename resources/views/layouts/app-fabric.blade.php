<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pageTitle = $globalCompany->company_name ?? 'JEOMSU';
        $pageDescription = 'Best Online Fabric Store for Mens. Discover premium quality fabrics at ' . ($globalCompany->company_name ?? 'JEOMSU') . '. Your trusted fabric shopping destination.';
    @endphp
    
    <title>@yield('title', $pageTitle . ' - Online Fabric Store')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', $pageDescription)">
    <meta name="keywords" content="@yield('meta_keywords', 'fabric store, mens fabric, viscose, cashmere, wool, synthetic, linen, silk, polymide, online fabric shopping')">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="@yield('og_title', $pageTitle . ' - Best Online Fabric Store')">
    <meta property="og:description" content="@yield('og_description', $pageDescription)">
    <meta property="og:image" content="@yield('og_image', asset('images/fabric-og.jpg'))">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #ff6b35;
            --accent-color: #ffd93d;
            --success-color: #4caf50;
            --fabric-orange: #ff6b35;
            --fabric-yellow: #ffd93d;
            --fabric-teal: #6bcf7f;
            --fabric-pink: #ff6b9d;
            --surface: #ffffff;
            --background: #f8f9fa;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-light: #999999;
            --border: #e0e0e0;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.16);
            --shadow-xl: 0 12px 36px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            padding-top: 70px; /* Space for fixed navbar */
        }

        /* Modern Navbar for Fabric Store */
        .navbar-fabric {
            background: var(--surface);
            padding: 1rem 0;
            box-shadow: var(--shadow-sm);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-fabric.scrolled {
            padding: 0.5rem 0;
            box-shadow: var(--shadow-md);
        }

        .navbar-brand-fabric {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-logo-fabric {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .brand-text {
            font-weight: 800;
            color: var(--primary-color);
        }

        .navbar-brand-fabric:hover {
            color: var(--fabric-orange);
            text-decoration: none;
        }

        .navbar-nav-fabric {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-link-fabric {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .nav-link-fabric:hover {
            color: var(--fabric-orange);
        }

        .nav-link-fabric.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--fabric-orange);
        }

        /* Dropdown Styles */
        .nav-dropdown {
            position: relative;
        }

        .fabric-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 200px;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            z-index: 1001;
        }

        .fabric-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-content {
            padding: 0.5rem 0;
        }

        .dropdown-item-fabric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.25rem;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .dropdown-item-fabric:hover {
            background: var(--background);
            color: var(--fabric-orange);
            padding-left: 1.5rem;
        }

        .dropdown-item-fabric .count {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        /* Search Bar */
        .search-bar-fabric {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0;
        }

        .search-bar-fabric.show {
            max-height: 80px;
            padding: 1rem 0;
        }

        .search-form-fabric {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }

        .search-input-fabric {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 50px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-input-fabric:focus {
            border-color: var(--fabric-orange);
        }

        .search-submit-fabric {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--fabric-orange);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-submit-fabric:hover {
            background: #ff5722;
        }

        /* Navbar Actions */
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-icon-btn {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.2rem;
            cursor: pointer;
            position: relative;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .nav-icon-btn:hover {
            color: var(--fabric-orange);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--fabric-orange);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        /* Mobile Menu */
        .mobile-menu-fabric {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            box-shadow: var(--shadow-lg);
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .mobile-menu-fabric.show {
            max-height: 500px;
            overflow-y: auto;
        }

        .mobile-nav-fabric {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
        }

        .mobile-link-fabric {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--text-primary);
            text-decoration: none;
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .mobile-link-fabric:hover {
            background: var(--background);
            color: var(--fabric-orange);
            padding-left: 2rem;
        }

        .mobile-dropdown-fabric {
            display: none;
            background: var(--background);
            padding: 0.5rem 0;
        }

        .mobile-dropdown-fabric.show {
            display: block;
        }

        .mobile-dropdown-item {
            display: block;
            padding: 0.5rem 2.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .mobile-dropdown-item:hover {
            color: var(--fabric-orange);
            padding-left: 3rem;
        }

        /* Hero Section Styles */
        .fabric-hero-section {
            padding: 100px 0 50px;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-content {
            padding: 2rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
            color: var(--primary-color);
        }

        .btn-explore {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--fabric-orange);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-bottom: 3rem;
        }

        .btn-explore:hover {
            background: #ff5722;
            transform: translateX(5px);
            color: white;
        }

        .hero-features {
            margin-top: 2rem;
        }

        .hero-features h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: var(--fabric-orange);
            color: white;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* Fabric Categories */
        .fabric-categories {
            padding: 2rem 0;
            background: white;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .categories-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0 2rem;
        }

        .category-item {
            padding: 0.75rem 1.5rem;
            background: var(--background);
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .category-item:hover {
            background: var(--fabric-orange);
            color: white;
            transform: translateY(-2px);
        }

        .category-item.active {
            background: var(--primary-color);
            color: white;
        }

        /* Footer */
        .footer-fabric {
            background: var(--primary-color);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--fabric-orange);
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--fabric-orange);
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.success {
            border-left: 4px solid var(--success-color);
        }

        .toast-notification.error {
            border-left: 4px solid #f44336;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            body {
                padding-top: 60px;
            }

            .navbar-fabric {
                padding: 0.75rem 0;
            }

            .navbar-nav-fabric {
                display: none;
            }

            .mobile-menu-fabric {
                display: block;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .categories-wrapper {
                justify-content: flex-start;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 0.5rem;
            }

            .category-item {
                flex-shrink: 0;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .navbar-brand-fabric {
                font-size: 1.25rem;
            }

            .navbar-logo-fabric {
                height: 30px;
            }

            .fabric-hero-section {
                padding: 80px 0 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Fabric Store Navbar -->
    <nav class="navbar-fabric" id="navbar">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center w-100">
                <!-- Brand -->
                <a href="{{ route('shop') }}" class="navbar-brand-fabric">
                    @if($globalCompany->company_logo)
                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="navbar-logo-fabric">
                    @endif
                    {{-- <span class="brand-text">{{ $globalCompany->company_name ?? 'JEOMSU' }}</span> --}}
                </a>
                
                <!-- Navigation Links -->
                <ul class="navbar-nav-fabric d-none d-lg-flex">
                    <li><a href="{{ route('shop') }}" class="nav-link-fabric {{ request()->routeIs('shop') ? 'active' : '' }}">Home</a></li>
                    
                    <!-- Categories Dropdown -->
                    <li class="nav-dropdown">
                        <a href="#" class="nav-link-fabric" onclick="toggleDropdown(event, 'categoriesDropdown')">
                            Categories <i class="fas fa-chevron-down ms-1"></i>
                        </a>
                        <div class="fabric-dropdown" id="categoriesDropdown">
                            <div class="dropdown-content">
                                @php
                                    $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                                @endphp
                                @forelse($categories as $category)
                                    <a href="{{ route('category', $category->slug) }}" class="dropdown-item-fabric">
                                        <span>{{ $category->name }}</span>
                                        @if($category->activeProducts()->count() > 0)
                                            <small class="count">({{ $category->activeProducts()->count() }})</small>
                                        @endif
                                    </a>
                                @empty
                                    <span class="dropdown-item-fabric">No categories available</span>
                                @endforelse
                            </div>
                        </div>
                    </li>
                    
                    <li><a href="{{ route('products') }}" class="nav-link-fabric {{ request()->routeIs('products') ? 'active' : '' }}">Products</a></li>
                    <li><a href="{{ route('offer.products') }}" class="nav-link-fabric {{ request()->routeIs('offer.products') ? 'active' : '' }}">Offers</a></li>
                    <li><a href="{{ route('track.order') }}" class="nav-link-fabric {{ request()->routeIs('track.order') ? 'active' : '' }}">Track Order</a></li>
                </ul>
                
                <!-- Actions -->
                <div class="navbar-actions">
                    <!-- Search Toggle -->
                    <button class="nav-icon-btn" onclick="toggleSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="nav-icon-btn position-relative">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count" id="cart-count-badge">0</span>
                    </a>
                    
                    <!-- User/Account -->
                    <button class="nav-icon-btn">
                        <i class="fas fa-user"></i>
                    </button>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="nav-icon-btn d-lg-none" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar (Hidden by default) -->
            <div class="search-bar-fabric" id="searchBar">
                <form action="{{ route('search') }}" method="GET" class="search-form-fabric">
                    <input type="search" name="q" placeholder="Search for products..." class="search-input-fabric" value="{{ request('q') }}">
                    <button type="submit" class="search-submit-fabric">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu-fabric" id="mobileMenu">
            <ul class="mobile-nav-fabric">
                <li><a href="{{ route('shop') }}" class="mobile-link-fabric">Home</a></li>
                <li>
                    <a href="#" class="mobile-link-fabric" onclick="toggleMobileDropdown(event)">
                        Categories <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="mobile-dropdown-fabric">
                        @forelse($categories ?? [] as $category)
                            <a href="{{ route('category', $category->slug) }}" class="mobile-dropdown-item">
                                {{ $category->name }}
                                @if($category->activeProducts()->count() > 0)
                                    <span>({{ $category->activeProducts()->count() }})</span>
                                @endif
                            </a>
                        @empty
                            <span class="mobile-dropdown-item">No categories</span>
                        @endforelse
                    </div>
                </li>
                <li><a href="{{ route('products') }}" class="mobile-link-fabric">Products</a></li>
                <li><a href="{{ route('offer.products') }}" class="mobile-link-fabric">Offers</a></li>
                <li><a href="{{ route('track.order') }}" class="mobile-link-fabric">Track Order</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-fabric" style="background: #1a1a1a; color: white; padding: 3rem 0 1rem; margin-top: 0;">
        <div class="container">
            <div class="row">
                <!-- Company Information -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-brand" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        @if($globalCompany->company_logo)
                            <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                 alt="{{ $globalCompany->company_name }}" 
                                 style="height: 50px; width: auto; object-fit: contain;">
                        @endif
                        <h4 style="margin: 0; color: white; font-weight: 600;">{{ $globalCompany->company_name ?? 'Your Store' }}</h4>
                    </div>
                    
                    <p style="color: rgba(255,255,255,0.7); margin-bottom: 1.5rem;">
                        {{ $globalCompany->company_description ?? 'Your trusted online shopping destination for quality products.' }}
                    </p>
                    
                    <!-- Social Media Icons -->
                    <div class="social-links" style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        @php
                            $socialLinks = \App\Models\SocialMediaLink::where('is_active', true)
                                ->orderBy('sort_order')
                                ->get();
                        @endphp
                        
                        @forelse($socialLinks as $social)
                            <a href="{{ $social->url }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; color: white; text-decoration: none; transition: all 0.3s;">
                                <i class="{{ $social->icon_class }}" style="font-size: 1.2rem;"></i>
                            </a>
                        @empty
                            <!-- Default social links if none in database -->
                            @if($globalCompany->facebook ?? null)
                                <a href="{{ $globalCompany->facebook }}" target="_blank" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; color: white; text-decoration: none;">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if($globalCompany->instagram ?? null)
                                <a href="{{ $globalCompany->instagram }}" target="_blank" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; color: white; text-decoration: none;">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($globalCompany->twitter ?? null)
                                <a href="{{ $globalCompany->twitter }}" target="_blank" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.1); border-radius: 50%; color: white; text-decoration: none;">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                        @endforelse
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 style="color: white; margin-bottom: 1.5rem; font-weight: 600;">Contact Us</h5>
                    
                    @if($globalCompany->company_address ?? null)
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; color: rgba(255,255,255,0.8);">
                        <i class="fas fa-map-marker-alt" style="margin-top: 0.25rem; color: #ff6b35;"></i>
                        <span>{{ $globalCompany->company_address }}</span>
                    </div>
                    @endif
                    
                    @if($globalCompany->company_phone ?? null)
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; color: rgba(255,255,255,0.8);">
                        <i class="fas fa-phone" style="color: #ff6b35;"></i>
                        <a href="tel:{{ $globalCompany->company_phone }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                            {{ $globalCompany->company_phone }}
                        </a>
                    </div>
                    @endif
                    
                    @if($globalCompany->company_email ?? null)
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; color: rgba(255,255,255,0.8);">
                        <i class="fas fa-envelope" style="color: #ff6b35;"></i>
                        <a href="mailto:{{ $globalCompany->company_email }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                            {{ $globalCompany->company_email }}
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 style="color: white; margin-bottom: 1.5rem; font-weight: 600;">Quick Links</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.75rem;">
                            <a href="{{ route('shop') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a>
                        </li>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="{{ route('products') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">All Products</a>
                        </li>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="{{ route('offer.products') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Offers</a>
                        </li>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="{{ route('track.order') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Track Order</a>
                        </li>
                        <li style="margin-bottom: 0.75rem;">
                            <a href="{{ route('cart.index') }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">Shopping Cart</a>
                        </li>
                    </ul>
                </div>
                
                <!-- Categories -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 style="color: white; margin-bottom: 1.5rem; font-weight: 600;">Categories</h5>
                    <ul style="list-style: none; padding: 0;">
                        @php
                            $footerCategories = \App\Models\Category::active()->parent()->orderBy('sort_order')->limit(5)->get();
                        @endphp
                        @foreach($footerCategories as $category)
                            <li style="margin-bottom: 0.75rem;">
                                <a href="{{ route('category', $category->slug) }}" style="color: rgba(255,255,255,0.8); text-decoration: none;">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0 1rem;">
            
            <div class="text-center">
                <p style="color: rgba(255,255,255,0.6); margin: 0;">
                    &copy; {{ date('Y') }} {{ $globalCompany->company_name ?? 'Your Store' }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        // Update cart count from server
        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.textContent = data.count || 0;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Toggle dropdown
        function toggleDropdown(event, dropdownId) {
            event.preventDefault();
            event.stopPropagation();
            const dropdown = document.getElementById(dropdownId);
            
            // Close other dropdowns
            document.querySelectorAll('.fabric-dropdown').forEach(d => {
                if (d.id !== dropdownId) {
                    d.classList.remove('show');
                }
            });
            
            dropdown.classList.toggle('show');
        }

        // Toggle search bar
        function toggleSearch() {
            const searchBar = document.getElementById('searchBar');
            searchBar.classList.toggle('show');
            if (searchBar.classList.contains('show')) {
                searchBar.querySelector('input').focus();
            }
        }

        // Toggle mobile menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('show');
        }

        // Toggle mobile dropdown
        function toggleMobileDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.nextElementSibling;
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.nav-dropdown')) {
                document.querySelectorAll('.fabric-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });

        // Close search when clicking outside
        document.addEventListener('click', function(event) {
            const searchBar = document.getElementById('searchBar');
            const searchBtn = event.target.closest('.nav-icon-btn');
            if (!event.target.closest('.search-bar-fabric') && !searchBtn) {
                searchBar.classList.remove('show');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
