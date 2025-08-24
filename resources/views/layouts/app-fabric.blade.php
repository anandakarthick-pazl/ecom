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
            --primary-color: #28a745;
            --secondary-color: #20c997;
            --accent-color: #28a745;
            --success-color: #28a745;
            --fabric-green: #28a745;
            --fabric-green-light: #5cb85c;
            --fabric-green-dark: #1e7e34;
            --fabric-teal: #20c997;
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
            padding-top: 72px; /* Space for modern navbar */
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
            color: var(--fabric-green);
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
            color: var(--fabric-green);
        }

        .nav-link-fabric.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--fabric-green);
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
            color: var(--fabric-green);
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
            border-color: var(--fabric-green);
        }

        .search-submit-fabric {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--fabric-green);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-submit-fabric:hover {
            background: var(--fabric-green-dark);
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
            color: var(--fabric-green);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--fabric-green);
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
            color: var(--fabric-green);
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
            color: var(--fabric-green);
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
            background: var(--fabric-green);
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
            background: var(--fabric-green-dark);
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
            background: var(--fabric-green);
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
            color: var(--fabric-green);
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
            color: var(--fabric-green);
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
                padding-top: 64px; /* Adjusted for modern navbar on mobile */
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
    <!-- Modern Premium Navigation -->
    @include('partials.modern-navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Modern Premium Footer -->
    @include('partials.modern-footer')

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
                    // Update both the old badge and modern navbar badge
                    const badge = document.getElementById('cart-count-badge');
                    const modernBadge = document.getElementById('cart-count-modern');
                    
                    if (badge) {
                        badge.textContent = data.count || 0;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                    
                    if (modernBadge) {
                        modernBadge.textContent = data.count || 0;
                        modernBadge.style.display = data.count > 0 ? 'flex' : 'none';
                    }
                    
                    // Also call the modern navbar's update function if it exists
                    if (typeof updateModernCartCount === 'function') {
                        updateModernCartCount();
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));
        }

        // The modern navbar has its own JavaScript functions included
    </script>
    
    @yield('scripts')
</body>
</html>
