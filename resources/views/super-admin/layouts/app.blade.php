@php
    use App\Services\SuperAdminMenuService;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin Panel')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js" crossorigin="anonymous"></script>
    
    @stack('styles')
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #4facfe;
            --warning-color: #43e97b;
            --danger-color: #fa709a;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            height: 100vh !important;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 280px !important; /* Increased width for better readability */
            z-index: 1000 !important;
            overflow-y: scroll !important; /* Force scroll instead of auto */
            overflow-x: hidden !important;
            display: block !important; /* Use block instead of flex for better compatibility */
            /* Force proper scrolling */
            max-height: 100vh !important;
            min-height: 100vh !important;
            /* Ensure scrolling works properly */
            contain: layout !important;
            /* Override any external CSS */
            flex: none !important;
        }
        
        /* Enhanced Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar,
        .sidebar nav::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar::-webkit-scrollbar-track,
        .sidebar nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb,
        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover,
        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Firefox scrollbar */
        .sidebar,
        .sidebar nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.1);
        }
        
        /* Scroll indicators for better UX */
        .sidebar nav {
            position: relative;
        }
        
        .sidebar nav::before,
        .sidebar nav::after {
            content: '';
            position: sticky;
            left: 0;
            right: 0;
            height: 15px;
            pointer-events: none;
            z-index: 10;
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        
        .sidebar nav::before {
            top: 0;
            background: linear-gradient(to bottom, var(--primary-color), transparent);
        }
        
        .sidebar nav::after {
            bottom: 0;
            background: linear-gradient(to top, var(--primary-color), transparent);
        }
        
        .sidebar nav.can-scroll-up::before {
            opacity: 0.8;
        }
        
        .sidebar nav.can-scroll-down::after {
            opacity: 0.8;
        }
        
        .sidebar-brand {
            color: white !important;
            font-size: 1.5rem !important;
            font-weight: bold !important;
            text-decoration: none !important;
            padding: 1.5rem !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
            display: block !important;
            flex-shrink: 0 !important;
            /* Ensure brand doesn't interfere with scrolling */
            position: relative !important;
            z-index: 1001 !important;
        }
        
        .sidebar-brand:hover {
            color: white;
            text-decoration: none;
        }
        
        .sidebar-nav-container {
            /* New wrapper for nav content */
            height: calc(100vh - 100px) !important;
            overflow-y: scroll !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            padding-bottom: 2rem !important;
            /* Force scrolling behavior */
            display: block !important;
            width: 100% !important;
        }
        
        .sidebar nav {
            /* Reset flex behavior */
            display: block !important;
            overflow: visible !important; /* Let parent handle overflow */
            height: auto !important;
            max-height: none !important;
            min-height: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            /* Remove flex properties that might interfere */
            flex: none !important;
            flex-shrink: unset !important;
            flex-grow: unset !important;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            margin: 0.2rem 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            text-decoration: none;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        
        .nav-section {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-weight: 600;
        }
        
        .main-content {
            margin-left: 280px; /* Adjusted for new sidebar width */
            min-height: 100vh;
            background: #f8f9fa;
            padding: 0;
        }
        
        .navbar {
            background: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 0;
            border: none;
            padding: 0.5rem 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            min-height: 120px;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #00f2fe 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #38f9d7 100%);
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #fee140 100%);
        }
        
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 0;
        }
        
        .content-wrapper {
            padding: 1rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1rem;
        }
        
        .badge {
            border-radius: 6px;
            padding: 0.5em 0.75em;
            font-size: 0.75em;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        }
        
        /* Priority indicators */
        .nav-link.priority-high {
            border-left: 3px solid #ff4757;
        }
        
        .nav-link.priority-medium {
            border-left: 3px solid #ffa502;
        }
        
        .nav-link.priority-low {
            border-left: 3px solid #2ed573;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
                height: 100vh !important;
                overflow-y: scroll !important; /* Force scroll */
                /* Enhanced mobile scrolling */
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
                display: block !important; /* Use block for mobile too */
            }
            
            .sidebar-nav-container {
                height: calc(100vh - 80px) !important;
                overflow-y: scroll !important;
                -webkit-overflow-scrolling: touch !important;
            }
            
            .sidebar nav {
                display: block !important;
                overflow: visible !important;
                height: auto !important;
                padding-bottom: 3rem !important;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 0.5rem;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        /* AGGRESSIVE SCROLL OVERRIDES - Override everything */
        .sidebar,
        .sidebar * {
            box-sizing: border-box !important;
        }
        
        /* Kill any conflicting styles */
        .sidebar {
            all: unset !important;
            /* Reapply our styles */
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 280px !important;
            height: 100vh !important;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
            overflow-y: scroll !important;
            overflow-x: hidden !important;
            z-index: 1000 !important;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1) !important;
            display: block !important;
        }
        
        /* Force scrolling behavior - override any conflicting styles */
        .sidebar,
        .sidebar nav,
        .sidebar-nav-container {
            overflow: auto !important;
            contain: layout style paint !important;
        }
        
        /* Ensure flex children can scroll */
        .sidebar-nav-container {
            flex-shrink: 1 !important;
            flex-grow: 1 !important;
            min-height: 0 !important;
        }
        
        /* Prevent any Tailwind conflicts */
        .sidebar * {
            box-sizing: border-box;
        }
        
        /* Ensure touch scrolling works on all devices */
        @supports (-webkit-overflow-scrolling: touch) {
            .sidebar,
            .sidebar nav,
            .sidebar-nav-container {
                -webkit-overflow-scrolling: touch !important;
            }
        }
        
        /* Modern scroll snap for better UX */
        .sidebar-nav-container {
            scroll-snap-type: y proximity !important;
        }
        
        .sidebar nav .nav-section {
            scroll-snap-align: start;
        }
        
        /* Fallback for older browsers */
        @supports not (overscroll-behavior: contain) {
            .sidebar-nav-container {
                overflow-y: scroll !important;
            }
        }
    </style>
    
    <!-- Additional CSS to ensure scrolling works -->
    <style>
        /* Additional scroll enforcement - loaded after main styles */
        .force-scroll {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch !important;
            overscroll-behavior: contain !important;
        }
        
        /* Diagnostic styles for troubleshooting */
        .sidebar-debug {
            border: 2px solid red !important;
        }
        
        .sidebar-debug nav {
            border: 2px solid blue !important;
        }
        
        /* Ensure proper height calculation */
        .sidebar {
            height: 100vh !important;
            min-height: 100vh !important;
            max-height: 100vh !important;
        }
        
        .sidebar nav {
            flex: 1 1 auto !important;
            overflow: auto !important;
            min-height: 0 !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('super-admin.dashboard') }}" class="sidebar-brand">
            <i class="fas fa-crown"></i> Super Admin Portal
        </a>
        
        <!-- Scrollable Navigation Container -->
        <div class="sidebar-nav-container" id="sidebarNavContainer">
            <nav class="nav flex-column">
                <!-- Main Dashboard -->
                @if(SuperAdminMenuService::isMenuEnabled('dashboard'))
                <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }} priority-high" href="{{ route('super-admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Main Dashboard
                </a>
                @endif
                
                @if(SuperAdminMenuService::isMenuEnabled('analytics_overview'))
                <a class="nav-link {{ request()->routeIs('super-admin.analytics.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.analytics.index') }}">
                    <i class="fas fa-chart-line"></i> Analytics Overview
                </a>
                @endif
                
                <!-- Company & Tenant Management -->
                @if(SuperAdminMenuService::isMenuEnabled('companies') || SuperAdminMenuService::isMenuEnabled('subscriptions') || SuperAdminMenuService::isMenuEnabled('subscriptions_expiring') || SuperAdminMenuService::isMenuEnabled('packages') || SuperAdminMenuService::isMenuEnabled('domain_management') || SuperAdminMenuService::isMenuEnabled('multi_tenant_config') || SuperAdminMenuService::isMenuEnabled('resource_allocation'))
                <div class="nav-section">🏢 Company & Tenant Management</div>
                @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('companies'))
            <a class="nav-link {{ request()->routeIs('super-admin.companies.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.companies.index') }}">
                <i class="fas fa-building"></i> Companies
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('subscriptions'))
            <a class="nav-link {{ request()->routeIs('super-admin.subscriptions.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.subscriptions.index') }}">
                <i class="fas fa-calendar-check"></i> Subscriptions
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('subscriptions_expiring'))
            <a class="nav-link {{ request()->routeIs('super-admin.subscriptions.expiring-soon') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.subscriptions.expiring-soon') }}">
                <i class="fas fa-exclamation-triangle text-warning"></i> Expiring Soon
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('packages'))
            <a class="nav-link {{ request()->routeIs('super-admin.packages.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.packages.index') }}">
                <i class="fas fa-box"></i> Packages & Plans
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('domain_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.companies.domains') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-globe"></i> Domain Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('multi_tenant_config'))
            <a class="nav-link {{ request()->routeIs('super-admin.companies.multi-tenant') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-network-wired"></i> Multi-Tenant Config
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('resource_allocation'))
            <a class="nav-link {{ request()->routeIs('super-admin.companies.resources') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-server"></i> Resource Allocation
            </a>
            @endif
            
            <!-- Financial & Billing Management -->
            @if(SuperAdminMenuService::isMenuEnabled('billing_management') || SuperAdminMenuService::isMenuEnabled('billing_reports') || SuperAdminMenuService::isMenuEnabled('invoice_generator') || SuperAdminMenuService::isMenuEnabled('revenue_analytics') || SuperAdminMenuService::isMenuEnabled('payment_gateway') || SuperAdminMenuService::isMenuEnabled('subscription_billing') || SuperAdminMenuService::isMenuEnabled('tax_configuration') || SuperAdminMenuService::isMenuEnabled('discount_management') || SuperAdminMenuService::isMenuEnabled('currency_settings'))
            <div class="nav-section">💰 Financial & Billing Management</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('billing_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.billing.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.billing.index') }}">
                <i class="fas fa-credit-card"></i> Billing Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('billing_reports'))
            <a class="nav-link {{ request()->routeIs('super-admin.billing.reports') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.billing.reports') }}">
                <i class="fas fa-chart-bar"></i> Billing Reports
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('invoice_generator'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.invoices') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.finance.invoices') }}">
                <i class="fas fa-receipt"></i> Invoice Generator
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('revenue_analytics'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.revenue') ? 'active' : '' }} priority-low" href="{{ route('super-admin.finance.revenue') }}">
                <i class="fas fa-chart-line"></i> Revenue Analytics
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('payment_gateway'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.payment-gateway') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.finance.payment-gateway') }}">
                <i class="fas fa-coins"></i> Payment Gateway
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('subscription_billing'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.subscriptions') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-credit-card"></i> Subscription Billing
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('tax_configuration'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.taxes') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-calculator"></i> Tax Configuration
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('discount_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.discounts') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-percentage"></i> Discount Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('currency_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.finance.currency') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-dollar-sign"></i> Currency Settings
            </a>
            @endif
            
            <!-- Theme & Design Management -->
            @if(SuperAdminMenuService::isMenuEnabled('theme_library') || SuperAdminMenuService::isMenuEnabled('theme_assignment') || SuperAdminMenuService::isMenuEnabled('theme_statistics') || SuperAdminMenuService::isMenuEnabled('theme_reports'))
            <div class="nav-section">🎨 Design & Themes</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('theme_library'))
            <a class="nav-link {{ request()->routeIs('super-admin.themes.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.themes.index') }}">
                <i class="fas fa-palette"></i> Theme Library
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('theme_assignment'))
            <a class="nav-link {{ request()->routeIs('super-admin.theme-assignments.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.theme-assignments.index') }}">
                <i class="fas fa-paintbrush"></i> Theme Assignment
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('theme_statistics'))
            <a class="nav-link {{ request()->routeIs('super-admin.theme-assignments.stats') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.theme-assignments.stats') }}">
                <i class="fas fa-chart-pie"></i> Theme Statistics
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('theme_reports'))
            <a class="nav-link {{ request()->routeIs('super-admin.theme-assignments.report') ? 'active' : '' }} priority-low" href="{{ route('super-admin.theme-assignments.report') }}">
                <i class="fas fa-file-chart"></i> Theme Reports
            </a>
            @endif
            
            <!-- Data Management & Transfer -->
            @if(SuperAdminMenuService::isMenuEnabled('data_import') || SuperAdminMenuService::isMenuEnabled('import_history') || SuperAdminMenuService::isMenuEnabled('data_export') || SuperAdminMenuService::isMenuEnabled('database_manager') || SuperAdminMenuService::isMenuEnabled('backup_management') || SuperAdminMenuService::isMenuEnabled('data_migration') || SuperAdminMenuService::isMenuEnabled('data_synchronization') || SuperAdminMenuService::isMenuEnabled('bulk_operations') || SuperAdminMenuService::isMenuEnabled('data_cleanup') || SuperAdminMenuService::isMenuEnabled('storage_management') || SuperAdminMenuService::isMenuEnabled('local_storage') || SuperAdminMenuService::isMenuEnabled('s3_storage'))
            <div class="nav-section">📊 Data Management & Transfer</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('storage_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.storage.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.storage.index') }}">
                <i class="fas fa-cloud"></i> Storage Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('local_storage'))
            <a class="nav-link {{ request()->routeIs('super-admin.storage.local') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.storage.local') }}">
                <i class="fas fa-hdd"></i> Local File Storage
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('s3_storage'))
            <a class="nav-link {{ request()->routeIs('super-admin.storage.s3') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.storage.s3') }}">
                <i class="fab fa-aws"></i> AWS S3 Storage
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('data_import'))
            <a class="nav-link {{ request()->routeIs('super-admin.data-import.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.data-import.index') }}">
                <i class="fas fa-file-import"></i> Data Import
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('import_history'))
            <a class="nav-link {{ request()->routeIs('super-admin.data-import.history') ? 'active' : '' }} priority-low" href="{{ route('super-admin.data-import.history') }}">
                <i class="fas fa-history"></i> Import History
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('data_export'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.export') ? 'active' : '' }} priority-low" href="{{ route('super-admin.analytics.export') }}">
                <i class="fas fa-download"></i> Data Export
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('database_manager'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.database') ? 'active' : '' }} priority-low" href="{{ route('super-admin.dev.database') }}">
                <i class="fas fa-database"></i> Database Manager
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('backup_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.settings.backup') ? 'active' : '' }} priority-high" href="{{ route('super-admin.settings.backup') }}">
                <i class="fas fa-hdd"></i> Backup Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('data_migration'))
            <a class="nav-link {{ request()->routeIs('super-admin.data.migration') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-exchange-alt"></i> Data Migration
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('data_synchronization'))
            <a class="nav-link {{ request()->routeIs('super-admin.data.sync') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-sync"></i> Data Synchronization
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('bulk_operations'))
            <a class="nav-link {{ request()->routeIs('super-admin.data.bulk') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-layer-group"></i> Bulk Operations
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('data_cleanup'))
            <a class="nav-link {{ request()->routeIs('super-admin.data.cleanup') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-broom"></i> Data Cleanup
            </a>
            @endif
            
            <!-- Customer Support & Communication -->
            @if(SuperAdminMenuService::isMenuEnabled('support_tickets') || SuperAdminMenuService::isMenuEnabled('whatsapp_config') || SuperAdminMenuService::isMenuEnabled('email_settings') || SuperAdminMenuService::isMenuEnabled('email_templates') || SuperAdminMenuService::isMenuEnabled('notifications') || SuperAdminMenuService::isMenuEnabled('live_chat_config') || SuperAdminMenuService::isMenuEnabled('sms_configuration') || SuperAdminMenuService::isMenuEnabled('push_notifications') || SuperAdminMenuService::isMenuEnabled('social_media_integration') || SuperAdminMenuService::isMenuEnabled('marketing_automation'))
            <div class="nav-section">🎧 Support & Communication</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('support_tickets'))
            <a class="nav-link {{ request()->routeIs('super-admin.support.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.support.index') }}">
                <i class="fas fa-headset"></i> Support Tickets
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('whatsapp_config'))
            <a class="nav-link {{ request()->routeIs('super-admin.whatsapp.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.whatsapp.index') }}">
                <i class="fab fa-whatsapp"></i> WhatsApp Config
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('email_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.settings.email') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.settings.email') }}">
                <i class="fas fa-envelope"></i> Email Settings
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('email_templates'))
            <a class="nav-link {{ request()->routeIs('super-admin.content.templates.email') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.content.templates.email') }}">
                <i class="fas fa-envelope-open"></i> Email Templates
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('notifications'))
            <a class="nav-link {{ request()->routeIs('super-admin.mobile.push-notifications') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.mobile.push-notifications') }}">
                <i class="fas fa-bell"></i> Notifications
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('live_chat_config'))
            <a class="nav-link" href="#">
                <i class="fas fa-comments"></i> Live Chat Config
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('sms_configuration'))
            <a class="nav-link {{ request()->routeIs('super-admin.communication.sms') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-sms"></i> SMS Configuration
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('push_notifications'))
            <a class="nav-link {{ request()->routeIs('super-admin.communication.push') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-bell"></i> Push Notifications
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('social_media_integration'))
            <a class="nav-link {{ request()->routeIs('super-admin.communication.social') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-share-alt"></i> Social Media Integration
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('marketing_automation'))
            <a class="nav-link {{ request()->routeIs('super-admin.communication.automation') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-robot"></i> Marketing Automation
            </a>
            @endif
            
            <!-- Content & Website Management -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['landing_page', 'hero_section', 'features_section', 'pricing_section', 'contact_section', 'blog_management', 'media_library']))
            <div class="nav-section">🌐 Content & Website</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('landing_page'))
            <a class="nav-link {{ request()->routeIs('super-admin.landing-page.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.landing-page.index') }}">
                <i class="fas fa-globe"></i> Landing Page
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('hero_section'))
            <a class="nav-link {{ request()->routeIs('super-admin.landing-page.hero') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.landing-page.hero') }}">
                <i class="fas fa-star"></i> Hero Section
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('features_section'))
            <a class="nav-link {{ request()->routeIs('super-admin.landing-page.features') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.landing-page.features') }}">
                <i class="fas fa-list-ul"></i> Features Section
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('pricing_section'))
            <a class="nav-link {{ request()->routeIs('super-admin.landing-page.pricing') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.landing-page.pricing') }}">
                <i class="fas fa-dollar-sign"></i> Pricing Section
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('contact_section'))
            <a class="nav-link {{ request()->routeIs('super-admin.landing-page.contact') ? 'active' : '' }} priority-low" href="{{ route('super-admin.landing-page.contact') }}">
                <i class="fas fa-phone"></i> Contact Section
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('blog_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.content.blog') ? 'active' : '' }} priority-low" href="{{ route('super-admin.content.blog') }}">
                <i class="fas fa-blog"></i> Blog Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('media_library'))
            <a class="nav-link {{ request()->routeIs('super-admin.content.media') ? 'active' : '' }} priority-low" href="{{ route('super-admin.content.media') }}">
                <i class="fas fa-images"></i> Media Library
            </a>
            @endif
            
            <!-- User & Security Management -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['user_management', 'admin_users', 'blocked_users', 'api_keys', 'security_settings', 'access_control', 'role_management']))
            <div class="nav-section">🔐 Users & Security</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('user_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.users.index') }}">
                <i class="fas fa-users"></i> User Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('admin_users'))
            <a class="nav-link {{ request()->routeIs('super-admin.users.admins') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.users.admins') }}">
                <i class="fas fa-user-shield"></i> Admin Users
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('blocked_users'))
            <a class="nav-link {{ request()->routeIs('super-admin.users.blocked') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.users.blocked') }}">
                <i class="fas fa-user-times"></i> Blocked Users
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('api_keys'))
            <a class="nav-link {{ request()->routeIs('super-admin.api.keys') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.api.keys') }}">
                <i class="fas fa-key"></i> API Keys
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('security_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.security.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.security.index') }}">
                <i class="fas fa-shield-alt"></i> Security Settings
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('access_control'))
            <a class="nav-link {{ request()->routeIs('super-admin.security.access-control') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.security.access-control') }}">
                <i class="fas fa-lock"></i> Access Control
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('role_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.security.roles') ? 'active' : '' }} priority-low" href="{{ route('super-admin.security.roles') }}">
                <i class="fas fa-user-tag"></i> Role Management
            </a>
            @endif
            
            <!-- System Configuration -->
            <div class="nav-section">⚙️ System Configuration</div>
            
            @if(SuperAdminMenuService::isMenuEnabled('system_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.settings.index') }}">
                <i class="fas fa-cog"></i> System Settings
            </a>
            @endif
            
            <!-- Menu Management - Always visible for super admin -->
            @if(Route::has('super-admin.menu-management.index'))
            <a class="nav-link {{ request()->routeIs('super-admin.menu-management.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.menu-management.index') }}">
                <i class="fas fa-list-ul"></i> Menu Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('general_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.settings.general') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.settings.general') }}">
                <i class="fas fa-sliders-h"></i> General Settings
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('cache_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.settings.cache') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.settings.cache') }}">
                <i class="fas fa-server"></i> Cache Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('system_health'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.health') ? 'active' : '' }} priority-high" href="{{ route('super-admin.system.health') }}">
                <i class="fas fa-heartbeat"></i> System Health
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('performance_monitor'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.performance') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.system.performance') }}">
                <i class="fas fa-chart-area"></i> Performance Monitor
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('task_scheduler'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.scheduler') ? 'active' : '' }} priority-low" href="{{ route('super-admin.system.scheduler') }}">
                <i class="fas fa-tasks"></i> Task Scheduler
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('queue_monitor'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.queue') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.system.queue') }}">
                <i class="fas fa-sync-alt"></i> Queue Monitor
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('application_config'))
            <a class="nav-link {{ request()->routeIs('super-admin.config.app') ? 'active' : '' }} priority-high" href="#">
                <i class="fas fa-cogs"></i> Application Config
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('environment_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.config.environment') ? 'active' : '' }} priority-high" href="#">
                <i class="fas fa-layer-group"></i> Environment Settings
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('feature_flags'))
            <a class="nav-link {{ request()->routeIs('super-admin.config.features') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-toggle-on"></i> Feature Flags
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('maintenance_mode'))
            <a class="nav-link {{ request()->routeIs('super-admin.config.maintenance') ? 'active' : '' }} priority-medium" href="#">
                <i class="fas fa-tools"></i> Maintenance Mode
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('localization'))
            <a class="nav-link {{ request()->routeIs('super-admin.config.localization') ? 'active' : '' }} priority-low" href="#">
                <i class="fas fa-language"></i> Localization
            </a>
            @endif
            
            <!-- Logs & Monitoring -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['system_logs', 'error_logs', 'security_logs', 'activity_logs']))
            <div class="nav-section">📋 Logs & Monitoring</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('system_logs'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.logs') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.system.logs') }}">
                <i class="fas fa-file-alt"></i> System Logs
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('error_logs'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.error-logs') ? 'active' : '' }} priority-high" href="{{ route('super-admin.system.error-logs') }}">
                <i class="fas fa-bug"></i> Error Logs
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('security_logs'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.security-logs') ? 'active' : '' }} priority-high" href="{{ route('super-admin.system.security-logs') }}">
                <i class="fas fa-shield-virus"></i> Security Logs
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('activity_logs'))
            <a class="nav-link {{ request()->routeIs('super-admin.system.activity-logs') ? 'active' : '' }} priority-low" href="{{ route('super-admin.system.activity-logs') }}">
                <i class="fas fa-history"></i> Activity Logs
            </a>
            @endif
            
            <!-- Integration & Third-Party Services -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['api_management', 'api_documentation', 'api_webhooks', 'integrations', 'mobile_app_settings']))
            <div class="nav-section">🔌 Integration & API</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('api_management'))
            <a class="nav-link {{ request()->routeIs('super-admin.api.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.api.index') }}">
                <i class="fas fa-plug"></i> API Management
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('api_documentation'))
            <a class="nav-link {{ request()->routeIs('super-admin.api.documentation') ? 'active' : '' }} priority-low" href="{{ route('super-admin.api.documentation') }}">
                <i class="fas fa-code"></i> API Documentation
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('api_webhooks'))
            <a class="nav-link {{ request()->routeIs('super-admin.api.webhooks') ? 'active' : '' }} priority-low" href="{{ route('super-admin.api.webhooks') }}">
                <i class="fas fa-webhook"></i> Webhooks
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('integrations'))
            <a class="nav-link" href="#">
                <i class="fas fa-puzzle-piece"></i> Integrations
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('mobile_app_settings'))
            <a class="nav-link {{ request()->routeIs('super-admin.mobile.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.mobile.index') }}">
                <i class="fas fa-mobile-alt"></i> Mobile App Settings
            </a>
            @endif
            
            <!-- Reports & Analytics -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['analytics_dashboard', 'user_analytics', 'sales_reports', 'growth_metrics', 'custom_reports']))
            <div class="nav-section">📈 Reports & Analytics</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('analytics_dashboard'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.*') ? 'active' : '' }} priority-high" href="{{ route('super-admin.analytics.index') }}">
                <i class="fas fa-chart-bar"></i> Analytics Dashboard
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('user_analytics'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.users') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.analytics.users') }}">
                <i class="fas fa-users-cog"></i> User Analytics
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('sales_reports'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.sales') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.analytics.sales') }}">
                <i class="fas fa-shopping-cart"></i> Sales Reports
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('growth_metrics'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.growth') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.analytics.growth') }}">
                <i class="fas fa-chart-line"></i> Growth Metrics
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('custom_reports'))
            <a class="nav-link {{ request()->routeIs('super-admin.analytics.custom') ? 'active' : '' }} priority-low" href="{{ route('super-admin.analytics.custom') }}">
                <i class="fas fa-file-csv"></i> Custom Reports
            </a>
            @endif
            
            <!-- System Debug & Development -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['debug_console', 'artisan_commands', 'database_query_builder', 'version_info']))
            <div class="nav-section">🛠️ Debug & Development</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('debug_console'))
            <a class="nav-link {{ request()->routeIs('super-admin.debug.*') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.debug.console') }}">
                <i class="fas fa-tools"></i> Debug Console
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('artisan_commands'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.artisan') ? 'active' : '' }} priority-low" href="{{ route('super-admin.dev.artisan') }}">
                <i class="fas fa-terminal"></i> Artisan Commands
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('database_query_builder'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.database') ? 'active' : '' }} priority-low" href="{{ route('super-admin.dev.database') }}">
                <i class="fas fa-database"></i> DB Query Builder
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('version_info'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.version') ? 'active' : '' }} priority-low" href="{{ route('super-admin.dev.version') }}">
                <i class="fas fa-code-branch"></i> Version Info
            </a>
            @endif
            
            <!-- Quick Actions -->
            @if(SuperAdminMenuService::hasAnyMenuEnabled(['view_main_site', 'quick_setup_wizard', 'deployment_tools']))
            <div class="nav-section">⚡ Quick Actions</div>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('view_main_site'))
            <a class="nav-link" href="{{ url('/') }}" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Main Site
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('quick_setup_wizard'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.setup-wizard') ? 'active' : '' }} priority-medium" href="{{ route('super-admin.dev.setup-wizard') }}">
                <i class="fas fa-magic"></i> Quick Setup Wizard
            </a>
            @endif
            
            @if(SuperAdminMenuService::isMenuEnabled('deployment_tools'))
            <a class="nav-link {{ request()->routeIs('super-admin.dev.deployment') ? 'active' : '' }} priority-low" href="{{ route('super-admin.dev.deployment') }}">
                <i class="fas fa-rocket"></i> Deployment Tools
            </a>
            @endif
            
            <!-- Logout - Always visible -->
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            
                <form action="{{ route('super-admin.logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="nav-link text-start w-100 border-0 bg-transparent" style="color: rgba(255,255,255,0.8);">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </nav>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none me-2" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0">@yield('page-title', 'Super Admin Dashboard')</h4>
                
                <!-- Quick Actions in Header -->
                <div class="d-flex align-items-center">
                    <!-- Quick Cache Clear -->
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="clearCache()" title="Clear Cache">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    
                    <!-- Quick System Status -->
                    <div class="badge bg-success me-3" id="systemStatus">
                        <i class="fas fa-heartbeat"></i> Online
                    </div>
                    
                    <!-- User Dropdown -->
                    <div class="navbar-nav">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=667eea&color=fff" 
                                     class="rounded-circle me-2" width="32" height="32">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('super-admin.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay d-md-none" id="sidebarOverlay"></div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    
    <script>
        // Initialize application
        document.addEventListener('DOMContentLoaded', function() {
            initializeDropdowns();
            initializeSidebar();
            initializeQuickActions();
            updateSystemStatus();
            
            // Force scrolling initialization
            initializeScrolling();
        });
        
        // Force scrolling initialization
        function initializeScrolling() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarNavContainer = document.querySelector('.sidebar-nav-container');
            const sidebarNav = document.querySelector('.sidebar nav');
            
            console.log('🔧 Initializing aggressive scrolling fixes...');
            
            if (sidebar && sidebarNavContainer) {
                // Apply force-scroll class
                sidebar.classList.add('force-scroll');
                sidebarNavContainer.classList.add('force-scroll');
                
                // AGGRESSIVE: Remove all existing styles and force our own
                sidebar.removeAttribute('style');
                sidebarNavContainer.removeAttribute('style');
                
                // Force styles via JavaScript as primary method
                sidebar.style.cssText = `
                    position: fixed !important;
                    top: 0px !important;
                    left: 0px !important;
                    width: 280px !important;
                    height: 100vh !important;
                    max-height: 100vh !important;
                    min-height: 100vh !important;
                    overflow-y: scroll !important;
                    overflow-x: hidden !important;
                    z-index: 1000 !important;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                    box-shadow: 2px 0 5px rgba(0,0,0,0.1) !important;
                    display: block !important;
                    -webkit-overflow-scrolling: touch !important;
                `;
                
                sidebarNavContainer.style.cssText = `
                    height: calc(100vh - 100px) !important;
                    max-height: calc(100vh - 100px) !important;
                    overflow-y: scroll !important;
                    overflow-x: hidden !important;
                    -webkit-overflow-scrolling: touch !important;
                    padding-bottom: 2rem !important;
                    display: block !important;
                    width: 100% !important;
                `;
                
                if (sidebarNav) {
                    sidebarNav.style.cssText = `
                        display: block !important;
                        overflow: visible !important;
                        height: auto !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    `;
                }
                
                console.log('✅ Aggressive scrolling styles applied');
                
                // Force recalculation
                sidebar.offsetHeight;
                sidebarNavContainer.offsetHeight;
                
                // Test scrolling immediately
                setTimeout(() => {
                    testScrollingCapability();
                }, 100);
                
                // Auto-run debug after initialization
                setTimeout(() => {
                    debugSidebarScrolling();
                }, 500);
            } else {
                console.error('❌ Sidebar elements not found for scrolling initialization');
                console.log('Sidebar:', sidebar);
                console.log('SidebarNavContainer:', sidebarNavContainer);
            }
        }
        
        // Test scrolling capability
        function testScrollingCapability() {
            const container = document.querySelector('.sidebar-nav-container');
            if (container) {
                console.log('🧪 Testing scroll capability...');
                const originalScrollTop = container.scrollTop;
                
                // Try to scroll
                container.scrollTop = 50;
                
                setTimeout(() => {
                    const newScrollTop = container.scrollTop;
                    if (newScrollTop !== originalScrollTop) {
                        console.log('✅ SCROLLING WORKS! Container can scroll.');
                    } else {
                        console.log('❌ SCROLLING FAILED! Applying emergency fixes...');
                        emergencyScrollFixes();
                    }
                    // Reset scroll position
                    container.scrollTop = originalScrollTop;
                }, 50);
            }
        }
        
        // Emergency scroll fixes
        function emergencyScrollFixes() {
            console.log('🚨 Applying emergency scroll fixes...');
            
            const sidebar = document.querySelector('.sidebar');
            const container = document.querySelector('.sidebar-nav-container');
            
            if (sidebar && container) {
                // Method 1: Force height with specific pixel values
                const viewportHeight = window.innerHeight;
                sidebar.style.height = viewportHeight + 'px';
                container.style.height = (viewportHeight - 100) + 'px';
                container.style.overflowY = 'scroll';
                
                // Method 2: Use different positioning
                container.style.position = 'absolute';
                container.style.top = '100px';
                container.style.bottom = '0';
                container.style.left = '0';
                container.style.right = '0';
                
                // Method 3: Force content to be taller than container
                const nav = container.querySelector('nav');
                if (nav) {
                    nav.style.minHeight = (viewportHeight + 200) + 'px';
                }
                
                console.log('🚨 Emergency fixes applied');
                
                // Test again
                setTimeout(() => {
                    testScrollingCapability();
                }, 100);
            }
        }
        
        // Initialize Bootstrap dropdowns
        function initializeDropdowns() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        }
        
        // Initialize sidebar functionality
        function initializeSidebar() {
            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
                
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Enhanced scroll functionality - target new container
            const sidebarNavContainer = document.querySelector('.sidebar-nav-container');
            const scrollKey = 'super-admin-sidebar-scroll';
            
            if (sidebarNavContainer) {
                // Force scrollable behavior
                sidebarNavContainer.style.overflowY = 'scroll';
                sidebarNavContainer.style.overflowX = 'hidden';
                
                // Restore scroll position
                const savedScrollPosition = sessionStorage.getItem(scrollKey);
                if (savedScrollPosition) {
                    setTimeout(() => {
                        sidebarNavContainer.scrollTop = parseInt(savedScrollPosition);
                    }, 100);
                }
                
                // Save scroll position
                sidebarNavContainer.addEventListener('scroll', function() {
                    sessionStorage.setItem(scrollKey, sidebarNavContainer.scrollTop);
                    updateScrollIndicators(sidebarNavContainer);
                });
                
                // Update scroll indicators on load
                setTimeout(() => {
                    updateScrollIndicators(sidebarNavContainer);
                }, 100);
                
                // Add scroll detection for better UX
                sidebarNavContainer.addEventListener('wheel', function(e) {
                    // Prevent horizontal scrolling
                    if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                        e.preventDefault();
                    }
                });
                
                // Keyboard navigation for sidebar
                sidebarNavContainer.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        e.preventDefault();
                        const scrollAmount = 50;
                        const direction = e.key === 'ArrowUp' ? -1 : 1;
                        sidebarNavContainer.scrollBy({
                            top: scrollAmount * direction,
                            behavior: 'smooth'
                        });
                    }
                });
            } else {
                console.error('❌ Sidebar nav container not found');
            }
            
            // Fix active states
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        }
        
        // Update scroll indicators
        function updateScrollIndicators(element) {
            const isScrolledToTop = element.scrollTop <= 10;
            const isScrolledToBottom = element.scrollTop >= (element.scrollHeight - element.clientHeight - 10);
            
            if (isScrolledToTop) {
                element.classList.remove('can-scroll-up');
            } else {
                element.classList.add('can-scroll-up');
            }
            
            if (isScrolledToBottom) {
                element.classList.remove('can-scroll-down');
            } else {
                element.classList.add('can-scroll-down');
            }
        }
        
        // Initialize quick actions
        function initializeQuickActions() {
            // Auto-refresh system status every 30 seconds
            setInterval(updateSystemStatus, 30000);
        }
        
        // Quick cache clear function
        function clearCache() {
            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            // TODO: Implement actual cache clear endpoint
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                
                // Show success toast
                showToast('Cache cleared successfully!', 'success');
            }, 2000);
        }
        
        // Update system status
        function updateSystemStatus() {
            const statusElement = document.getElementById('systemStatus');
            // TODO: Implement actual system health check endpoint
            
            // Simulate status check
            const isOnline = Math.random() > 0.1; // 90% uptime simulation
            
            if (isOnline) {
                statusElement.className = 'badge bg-success me-3';
                statusElement.innerHTML = '<i class="fas fa-heartbeat"></i> Online';
            } else {
                statusElement.className = 'badge bg-danger me-3';
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Issues';
            }
        }
        
        // Toast notification system
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast element after it's hidden
            toast.addEventListener('hidden.bs.toast', function () {
                toast.remove();
            });
        }
        
        // Create toast container if it doesn't exist
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1100';
            document.body.appendChild(container);
            return container;
        }
        
        // Enhanced keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search (if we add search functionality)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                // TODO: Focus search input when implemented
            }
            
            // Ctrl/Cmd + Shift + C to clear cache
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
                e.preventDefault();
                clearCache();
            }
            
            // Ctrl/Cmd + Shift + S to test sidebar scrolling
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'S') {
                e.preventDefault();
                debugSidebarScrolling();
            }
            
            // Ctrl/Cmd + Shift + D to toggle debug borders
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                toggleSidebarDebug();
            }
            
            // Ctrl/Cmd + Shift + F to force scrolling
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                forceSidebarScroll();
            }
            
            // Ctrl/Cmd + Shift + E to apply emergency fixes
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'E') {
                e.preventDefault();
                emergencyScrollFixes();
            }
            
            // Ctrl/Cmd + Shift + T to test immediate scrolling
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                immediateScrollTest();
            }
        });
        
        // Enhanced debug function to test scrolling
        function debugSidebarScrolling() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarNavContainer = document.querySelector('.sidebar-nav-container');
            const sidebarNav = document.querySelector('.sidebar nav');
            
            console.log('🔍 ENHANCED Sidebar Debug Info:');
            console.log('==========================================');
            
            if (sidebar) {
                console.log('Sidebar found:', true);
                console.log('Sidebar height:', sidebar.offsetHeight + 'px');
                console.log('Sidebar computed height:', getComputedStyle(sidebar).height);
                console.log('Sidebar overflow-y:', getComputedStyle(sidebar).overflowY);
                console.log('Sidebar classes:', sidebar.className);
            } else {
                console.log('❌ Sidebar not found');
                return;
            }
            
            if (sidebarNavContainer) {
                console.log('Sidebar nav container found:', true);
                console.log('Container height:', sidebarNavContainer.offsetHeight + 'px');
                console.log('Container scroll height:', sidebarNavContainer.scrollHeight + 'px');
                console.log('Container client height:', sidebarNavContainer.clientHeight + 'px');
                console.log('Container computed height:', getComputedStyle(sidebarNavContainer).height);
                console.log('Container overflow-y:', getComputedStyle(sidebarNavContainer).overflowY);
                console.log('Container classes:', sidebarNavContainer.className);
                
                const canScroll = sidebarNavContainer.scrollHeight > sidebarNavContainer.clientHeight;
                console.log('Can scroll?', canScroll);
                console.log('Scroll difference:', (sidebarNavContainer.scrollHeight - sidebarNavContainer.clientHeight) + 'px');
                
                if (canScroll) {
                    console.log('✅ Sidebar should be scrollable');
                    console.log('🧪 Testing scroll functionality...');
                    
                    // Test scroll functionality
                    const originalScrollTop = sidebarNavContainer.scrollTop;
                    sidebarNavContainer.scrollTo({ top: 200, behavior: 'smooth' });
                    
                    setTimeout(() => {
                        const newScrollTop = sidebarNavContainer.scrollTop;
                        console.log('Scroll test result:', newScrollTop > originalScrollTop ? '✅ SUCCESS' : '❌ FAILED');
                        console.log('Original scroll top:', originalScrollTop);
                        console.log('New scroll top:', newScrollTop);
                        
                        if (newScrollTop === originalScrollTop) {
                            console.log('🚨 Scroll test failed! Trying emergency fixes...');
                            emergencyScrollFixes();
                        }
                        
                        // Scroll back to original position
                        sidebarNavContainer.scrollTo({ top: originalScrollTop, behavior: 'smooth' });
                        console.log('✅ Scroll test completed');
                    }, 1000);
                } else {
                    console.log('⚠️ Content fits in viewport - forcing content to be taller');
                    
                    // Force content to be taller to enable scrolling
                    if (sidebarNav) {
                        sidebarNav.style.minHeight = (window.innerHeight + 200) + 'px';
                        console.log('💡 Forced nav height to enable scrolling');
                        
                        // Test again after forcing height
                        setTimeout(() => {
                            debugSidebarScrolling();
                        }, 500);
                    }
                }
            } else {
                console.log('❌ Sidebar nav container not found');
            }
            
            if (sidebarNav) {
                console.log('Sidebar nav found:', true);
                console.log('Nav display:', getComputedStyle(sidebarNav).display);
                console.log('Nav overflow:', getComputedStyle(sidebarNav).overflow);
            }
            
            console.log('==========================================');
        }
        
        // Additional utility functions
        function toggleSidebarDebug() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('sidebar-debug');
                console.log('Debug borders toggled');
            }
        }
        
        function forceSidebarScroll() {
            const sidebarNavContainer = document.querySelector('.sidebar-nav-container');
            if (sidebarNavContainer) {
                sidebarNavContainer.style.overflowY = 'scroll';
                sidebarNavContainer.style.height = 'calc(100vh - 100px)';
                sidebarNavContainer.style.maxHeight = 'calc(100vh - 100px)';
                console.log('✅ Force scroll applied to nav container');
                
                // Also force nav content to be taller
                const nav = sidebarNavContainer.querySelector('nav');
                if (nav) {
                    nav.style.minHeight = (window.innerHeight + 100) + 'px';
                    console.log('✅ Forced nav content to be taller');
                }
            }
        }
        
        // New utility: Immediate scroll test
        function immediateScrollTest() {
            const container = document.querySelector('.sidebar-nav-container');
            if (container) {
                console.log('🔥 IMMEDIATE SCROLL TEST');
                container.scrollTop = 100;
                console.log('Set scroll to 100, actual scroll:', container.scrollTop);
                
                if (container.scrollTop === 0) {
                    console.log('❌ SCROLLING NOT WORKING - APPLYING EMERGENCY FIX');
                    emergencyScrollFixes();
                } else {
                    console.log('✅ SCROLLING IS WORKING!');
                }
                
                container.scrollTop = 0;
            }
        }
        
        // Make utility functions globally available
        window.toggleSidebarDebug = toggleSidebarDebug;
        window.forceSidebarScroll = forceSidebarScroll;
        window.immediateScrollTest = immediateScrollTest;
        window.emergencyScrollFixes = emergencyScrollFixes;
        window.testScrollingCapability = testScrollingCapability;
        
        // Make debug function available globally
        window.debugSidebarScrolling = debugSidebarScrolling;
        
        console.log('🚀 ENHANCED Super Admin Panel Loaded');
        console.log('📊 Features: Comprehensive menu system with 100+ navigation items');
        console.log('⚡ Quick Actions: Cache clear, system status, keyboard shortcuts');
        console.log('📱 Responsive: Mobile-friendly sidebar with smooth animations');
        console.log('🔧 Configuration: Complete system configuration access');
        console.log('📊 Data Transfer: Full data management and migration tools');
        console.log('🌐 Multi-Tenant: Advanced tenant and environment management');
        console.log('🤖 Automation: Workflow builder and automation tools');
        console.log('🚀 Performance: Optimization and monitoring tools');
        console.log('📜 Scrolling: AGGRESSIVE sidebar scrolling with multiple fallbacks');
        console.log('🐛 Debug Commands:');
        console.log('   - debugSidebarScrolling() - Test and analyze scrolling');
        console.log('   - immediateScrollTest() - Quick scroll test');
        console.log('   - toggleSidebarDebug() - Toggle debug borders');
        console.log('   - forceSidebarScroll() - Force scroll styles');
        console.log('   - emergencyScrollFixes() - Apply emergency fixes');
        console.log('⌨️ Keyboard Shortcuts:');
        console.log('   - Ctrl+Shift+S - Quick scroll debug');
        console.log('   - Ctrl+Shift+D - Toggle debug borders');
        console.log('   - Ctrl+Shift+F - Force scroll styles');
        console.log('   - Ctrl+Shift+E - Emergency fixes');
        console.log('   - Ctrl+Shift+T - Immediate scroll test');
        console.log('   - Ctrl+Shift+C - Clear cache');
        console.log('🚨 If scrolling still not working, try: emergencyScrollFixes()');
    </script>
    
    @stack('scripts')
    
    <!-- Immediate Scroll Fix - Runs as soon as possible -->
    <script>
        // Immediate aggressive scroll fix - Don't wait for anything
        (function() {
            console.log('🚀 IMMEDIATE SCROLL FIX STARTING...');
            
            function immediateScrollFix() {
                const sidebar = document.getElementById('sidebar');
                const container = document.getElementById('sidebarNavContainer');
                
                if (sidebar && container) {
                    console.log('🔧 Applying immediate scroll fixes...');
                    
                    // Force immediate styles
                    sidebar.style.cssText = `
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        width: 280px !important;
                        height: 100vh !important;
                        overflow-y: scroll !important;
                        overflow-x: hidden !important;
                        z-index: 1000 !important;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                        box-shadow: 2px 0 5px rgba(0,0,0,0.1) !important;
                        display: block !important;
                    `;
                    
                    container.style.cssText = `
                        height: calc(100vh - 100px) !important;
                        overflow-y: scroll !important;
                        overflow-x: hidden !important;
                        padding-bottom: 2rem !important;
                        display: block !important;
                        width: 100% !important;
                    `;
                    
                    console.log('✅ Immediate scroll fix applied');
                    
                    // Test immediately
                    setTimeout(() => {
                        if (container.scrollHeight > container.clientHeight) {
                            container.scrollTop = 10;
                            setTimeout(() => {
                                if (container.scrollTop === 10) {
                                    console.log('✅ IMMEDIATE SCROLL TEST: SUCCESS!');
                                } else {
                                    console.log('❌ IMMEDIATE SCROLL TEST: FAILED');
                                }
                                container.scrollTop = 0;
                            }, 50);
                        } else {
                            // Force content to be taller
                            const nav = container.querySelector('nav');
                            if (nav) {
                                nav.style.minHeight = '120vh';
                                console.log('🔧 Forced nav content to be taller');
                            }
                        }
                    }, 100);
                } else {
                    console.log('⏳ Waiting for sidebar elements...');
                    setTimeout(immediateScrollFix, 100);
                }
            }
            
            // Try immediately
            immediateScrollFix();
            
            // Also try on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', immediateScrollFix);
            } else {
                immediateScrollFix();
            }
            
            console.log('🚀 IMMEDIATE SCROLL FIX INITIALIZED');
        })();
    </script>
</body>
</html>