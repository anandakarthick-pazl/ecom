<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $pageTitle = $globalCompany->company_name ?? 'Your Store';
        $pageDescription = 'Discover quality products at ' . ($globalCompany->company_name ?? 'Your Store') . '. Your trusted online shopping destination.';
    @endphp
    
    <title>@yield('title', $pageTitle . ' - E-commerce Store')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', $pageDescription)">
    <meta name="keywords" content="@yield('meta_keywords', 'ecommerce, online shopping, products, ' . strtolower($globalCompany->company_name ?? 'online store'))">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="@yield('og_title', $pageTitle . ' - E-commerce Store')">
    <meta property="og:description" content="@yield('og_description', $pageDescription)">
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
            padding: 0.25rem; /* Reduced from 0.5rem */
            margin-top: 0.25rem; /* Reduced from 0.5rem */
            max-height: 300px; /* Added max height */
            overflow-y: auto; /* Added scroll for many categories */
            min-width: 180px; /* Reduced minimum width */
            max-width: 250px; /* Added maximum width */
        }

        .dropdown-item-modern {
            padding: 0.5rem 0.75rem; /* Reduced from 0.75rem 1rem */
            border-radius: var(--radius);
            transition: all 0.3s ease;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.875rem; /* Reduced font size */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dropdown-item-modern:hover {
            background: rgba(37, 99, 235, 0.05);
            color: var(--primary-color);
            transform: translateX(2px); /* Reduced from 4px */
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

        /* Modern Footer - Enhanced Alignment Design */
        .footer-modern {
            background: linear-gradient(135deg, #1A202C 0%, #2D3748 50%, #1A202C 100%);
            color: #E2E8F0;
            margin-top: 4rem;
            position: relative;
            overflow: hidden;
        }
        
        .footer-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .footer-modern .container {
            position: relative;
            z-index: 2;
        }
        
        /* Footer Sections */
        .footer-section {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .footer-section:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.12);
        }
        
        /* Brand Section */
        .footer-brand-content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .footer-logo {
            height: 45px;
            width: auto;
            max-width: 45px;
            object-fit: contain;
            filter: brightness(1.1) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            transition: all 0.3s ease;
        }
        
        .footer-logo:hover {
            transform: scale(1.05);
        }
        
        .footer-icon-placeholder {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .footer-brand-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #F7FAFC;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .footer-tagline {
            font-size: 0.85rem;
            color: var(--primary-color);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .footer-description {
            color: #CBD5E0;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        /* Social Media */
        .footer-social {
            margin-top: auto;
        }
        
        .social-title {
            font-size: 1rem;
            font-weight: 600;
            color: #F7FAFC;
            margin-bottom: 1rem;
        }
        
        .social-link {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            color: #CBD5E0;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.6s ease;
        }
        
        .social-link:hover::before {
            left: 100%;
        }
        
        .social-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        
        /* Footer Titles */
        .footer-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #F7FAFC;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        /* Footer Links */
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-link {
            color: #CBD5E0;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 400;
            transition: all 0.3s ease;
            display: inline-block;
            position: relative;
            padding: 0.25rem 0;
        }
        
        .footer-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .footer-link:hover {
            color: #F7FAFC;
            transform: translateX(4px);
        }
        
        .footer-link:hover::before {
            width: 100%;
        }
        
        /* Contact Information */
        .contact-details {
            margin-bottom: 2rem;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            border-left: 3px solid var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .contact-item:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(3px);
        }
        
        .contact-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        
        .contact-info {
            flex: 1;
        }
        
        .contact-label {
            display: block;
            font-size: 0.8rem;
            color: var(--primary-color);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        
        .contact-value {
            display: block;
            color: #E2E8F0;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        /* Newsletter */
        .newsletter-signup {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .newsletter-title {
            font-size: 1rem;
            font-weight: 600;
            color: #F7FAFC;
        }
        
        .newsletter-text {
            color: #CBD5E0;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .newsletter-input {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px 0 0 8px;
            color: #E2E8F0;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .newsletter-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            color: #F7FAFC;
            outline: none;
        }
        
        .newsletter-input::placeholder {
            color: #9CA3AF;
        }
        
        .newsletter-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 0 8px 8px 0;
            color: white;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .newsletter-btn:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        /* Footer Bottom */
        .footer-bottom {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px 16px 0 0;
            padding: 1.5rem 0;
            margin-top: 2rem;
        }
        
        .copyright {
            color: #A0AEC0;
            font-size: 0.9rem;
            font-weight: 400;
        }
        
        .footer-links-bottom {
            display: flex;
            align-items: center;
            justify-content: end;
            gap: 0.5rem;
        }
        
        .footer-bottom-link {
            color: #CBD5E0;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }
        
        .footer-bottom-link:hover {
            color: var(--primary-color);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .separator {
            color: #6B7280;
            font-size: 0.8rem;
        }
        
        /* Responsive Design */
        @media (max-width: 991px) {
            .footer-section {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .footer-brand-content {
                text-align: center;
            }
            
            .newsletter-signup {
                text-align: center;
            }
        }
        
        @media (max-width: 767px) {
            .footer-section {
                padding: 1.25rem;
            }
            
            .footer-title {
                font-size: 1.1rem;
                text-align: center;
            }
            
            .footer-links {
                text-align: center;
            }
            
            .contact-item {
                flex-direction: column;
                text-align: center;
            }
            
            .contact-icon {
                margin: 0 auto 0.5rem auto;
            }
            
            .footer-links-bottom {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .social-link {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 575px) {
            .footer-bottom {
                text-align: center;
            }
            
            .footer-links-bottom {
                margin-top: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .separator {
                display: none;
            }
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
            
            /* Mobile dropdown optimizations */
            .dropdown-menu-modern {
                max-height: 250px; /* Reduced for mobile */
                min-width: 160px;
                max-width: 200px;
            }
            
            .dropdown-item-modern {
                padding: 0.4rem 0.6rem; /* Further reduced for mobile */
                font-size: 0.8rem;
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
        
        @keyframes newsletter-glow {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
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
        
        /* ===== FLOATING CART ICON STYLES ===== */
        .floating-cart-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            cursor: pointer;
            text-decoration: none;
        }
        
        .floating-cart-icon:hover {
            background: var(--secondary-color);
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            color: white;
        }
        
        .floating-cart-icon:active {
            transform: scale(0.95);
        }
        
        .floating-cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            min-width: 24px;
            animation: pulse-floating 2s infinite;
        }
        
        @keyframes pulse-floating {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .floating-cart-icon.cart-bounce {
            animation: cartBounce 0.6s ease;
        }
        
        @keyframes cartBounce {
            0% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Hide floating cart on cart page to avoid confusion */
        .cart-page .floating-cart-icon {
            display: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .floating-cart-icon {
                width: 55px;
                height: 55px;
                font-size: 20px;
                bottom: 15px;
                right: 15px;
            }
            
            .floating-cart-count {
                width: 22px;
                height: 22px;
                font-size: 11px;
                top: -6px;
                right: -6px;
            }
        }
        
        @media (max-width: 576px) {
            .floating-cart-icon {
                width: 50px;
                height: 50px;
                font-size: 18px;
                bottom: 80px; /* Adjust to avoid mobile browser UI */
                right: 15px;
            }
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
    
    {{-- Dynamic Animation Styles --}}
    @if(isset($animationsEnabled) && $animationsEnabled)
    <style>
        {!! $animationCSS ?? '' !!}
    </style>
    @endif
</head>
<body class="{{ $animationClasses ?? '' }}">
    <!-- Adaptive Modern Navigation -->
    <x-adaptive-navbar :company="$globalCompany" />

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
    
    <!-- Floating Cart Icon -->
    <a href="{{ route('cart.index') }}" 
       class="floating-cart-icon" 
       id="floating-cart-icon"
       title="View Cart">
        <i class="fas fa-shopping-cart"></i>
        <span class="floating-cart-count" id="floating-cart-count">0</span>
    </a>
    
    {{-- Animation Demo Component --}}
    @include('components.animation-demo')

    <!-- Enhanced Modern Footer -->
    <footer class="footer-modern py-5">
        <div class="container">
            <!-- Main Footer Content -->
            <div class="row g-4 mb-5">
                <!-- Company Brand Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section h-100">
                        <div class="footer-brand-content">
                            <div class="d-flex align-items-center mb-3">
                                @if($globalCompany->company_logo)
                                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                         alt="{{ $globalCompany->company_name }}" 
                                         class="footer-logo me-3">
                                @else
                                    <div class="footer-icon-placeholder me-3">
                                        <i class="fas fa-store"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="footer-brand-name mb-1">{{ $globalCompany->company_name ?? 'Your Store' }}</h5>
                                    <span class="footer-tagline">Quality & Trust</span>
                                </div>
                            </div>
                            <p class="footer-description mb-4">
                                Your trusted online shopping destination. We deliver premium quality products with exceptional service and care.
                            </p>
                            
                            <!-- Social Media Links -->
                            <div class="footer-social">
                                <h6 class="social-title mb-3">Follow Us</h6>
                                <div class="d-flex gap-3">
                                    <a href="#" class="social-link" title="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="social-link" title="Twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="social-link" title="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="social-link" title="LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" class="social-link" title="YouTube">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links Column -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section h-100">
                        <h5 class="footer-title">Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="{{ route('shop') }}" class="footer-link">Home</a></li>
                            <li><a href="{{ route('products') }}" class="footer-link">All Products</a></li>
                            <li><a href="{{ route('offer.products') }}" class="footer-link">Special Offers</a></li>
                            <li><a href="{{ route('track.order') }}" class="footer-link">Track Order</a></li>
                            <li><a href="#" class="footer-link">About Us</a></li>
                            <li><a href="#" class="footer-link">Contact</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Categories Column -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section h-100">
                        <h5 class="footer-title">Categories</h5>
                        <ul class="footer-links">
                            @if(isset($globalCategories) && $globalCategories->count() > 0)
                                @foreach($globalCategories->take(6) as $category)
                                    <li><a href="{{ route('category', $category->slug) }}" class="footer-link">{{ $category->name }}</a></li>
                                @endforeach
                            @else
                                <li><a href="{{ route('products') }}" class="footer-link">Featured Items</a></li>
                                <li><a href="{{ route('products') }}" class="footer-link">New Arrivals</a></li>
                                <li><a href="{{ route('offer.products') }}" class="footer-link">Best Deals</a></li>
                                <li><a href="{{ route('products') }}" class="footer-link">Top Rated</a></li>
                                <li><a href="{{ route('products') }}" class="footer-link">Popular</a></li>
                                <li><a href="{{ route('products') }}" class="footer-link">Trending</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Customer Service Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="footer-section h-100">
                        <h5 class="footer-title">Contact Information</h5>
                        
                        <!-- Contact Details -->
                        <div class="contact-details mb-4">
                            @if($globalCompany->company_phone)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="contact-info">
                                        <span class="contact-label">Phone</span>
                                        <span class="contact-value">{{ $globalCompany->company_phone }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Email</span>
                                    <span class="contact-value">{{ $globalCompany->company_email ?? 'info@example.com' }}</span>
                                </div>
                            </div>
                            
                            @if($globalCompany->company_address)
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="contact-info">
                                        <span class="contact-label">Address</span>
                                        <span class="contact-value">{{ $globalCompany->company_address }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-info">
                                    <span class="contact-label">Business Hours</span>
                                    <span class="contact-value">Mon - Sat: 9:00 AM - 8:00 PM</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Newsletter Signup -->
                        <div class="newsletter-signup">
                            <h6 class="newsletter-title mb-3">Stay Updated</h6>
                            <p class="newsletter-text mb-3">Subscribe for exclusive offers and updates</p>
                            <div class="newsletter-form">
                                <div class="input-group">
                                    <input type="email" class="form-control newsletter-input" placeholder="Enter your email">
                                    <button class="btn newsletter-btn" type="button">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="footer-bottom-left">
                            <p class="copyright mb-0">
                                &copy; {{ date('Y') }} {{ $globalCompany->company_name ?? 'Your Store' }}. All rights reserved.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-right text-md-end">
                            <div class="footer-links-bottom">
                                <a href="#" class="footer-bottom-link">Privacy Policy</a>
                                <span class="separator">|</span>
                                <a href="#" class="footer-bottom-link">Terms of Service</a>
                                <span class="separator">|</span>
                                <a href="https://kasoftware.in" class="footer-bottom-link">KA Software</a>
                            </div>
                        </div>
                    </div>
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
                    const navbarCountElement = $('#cart-count');
                    const floatingCountElement = $('#floating-cart-count');
                    
                    // Update navbar cart count
                    navbarCountElement.text(data.count);
                    
                    // Update floating cart count
                    floatingCountElement.text(data.count);
                    
                    if(data.count == 0) {
                        navbarCountElement.hide();
                        floatingCountElement.hide();
                    } else {
                        navbarCountElement.show();
                        floatingCountElement.show();
                        
                        // Add pulse animation for new items
                        navbarCountElement.addClass('animate__animated animate__pulse');
                        floatingCountElement.addClass('animate__animated animate__pulse');
                        
                        // Add bounce animation to floating cart
                        $('#floating-cart-icon').addClass('cart-bounce');
                        
                        setTimeout(() => {
                            navbarCountElement.removeClass('animate__animated animate__pulse');
                            floatingCountElement.removeClass('animate__animated animate__pulse');
                            $('#floating-cart-icon').removeClass('cart-bounce');
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
    
    {{-- Dynamic Animation Scripts --}}
    @if(isset($animationsEnabled) && $animationsEnabled)
    <script>
        {!! $animationJS ?? '' !!}
        
        // Trigger success animations on cart operations
        if (window.showToast) {
            const originalShowToast = window.showToast;
            window.showToast = function(message, type = 'success') {
                originalShowToast(message, type);
                if (type === 'success' && window.triggerCrackers) {
                    window.triggerCrackers();
                }
            };
        }
    </script>
    @endif
    
    @stack('scripts')
    
    <!-- Flash Offer Popup -->
    @include('components.flash-offer-popup', ['flashOffer' => $flashOffer ?? null])
</body>
</html>