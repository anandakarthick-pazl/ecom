<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?> - <?php echo e($globalCompany->company_name ?? 'Admin Panel'); ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/pagination.css')); ?>" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: <?php echo e(($globalCompany->primary_color && $globalCompany->primary_color !== '#ffffff' && $globalCompany->primary_color !== '#fff') 
                ? $globalCompany->primary_color 
                : '#2c3e50'); ?>;
            --secondary-color: <?php echo e(($globalCompany->secondary_color && $globalCompany->secondary_color !== '#ffffff' && $globalCompany->secondary_color !== '#fff') 
                ? $globalCompany->secondary_color 
                : '#34495e'); ?>;
            --sidebar-color: <?php echo e(($globalCompany->sidebar_color && $globalCompany->sidebar_color !== '#ffffff' && $globalCompany->sidebar_color !== '#fff') 
                ? $globalCompany->sidebar_color 
                : '#2c3e50'); ?>;
            --sidebar-width: 280px;
        }
        
        .sidebar {
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-color);
            background: linear-gradient(180deg, var(--sidebar-color) 0%, <?php echo e($globalCompany->sidebar_color ?? '#2c3e50'); ?>dd 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            flex-shrink: 0;
        }
        
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }
        
        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255,255,255,0.5);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
        }
        
        /* Ensure text color contrast */
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        /* Override Bootstrap colors for better contrast */
        .bg-primary {
            background-color: var(--primary-color) !important;
            background: linear-gradient(135deg, var(--primary-color) 0%, <?php echo e($globalCompany->secondary_color ?? '#34495e'); ?> 100%) !important;
        }
        
        .bg-success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%) !important;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%) !important;
        }
        
        .bg-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important;
        }
        
        .bg-danger {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%) !important;
        }
        
        /* Stats card specific styling */
        .stats-card {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
            transition: all 0.3s ease !important;
            overflow: hidden;
            position: relative;
        }
        
        .stats-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        
        .stats-card.bg-primary {
            color: white !important;
        }
        
        .stats-card.bg-success {
            color: white !important;
        }
        
        .stats-card.bg-info {
            color: white !important;
        }
        
        .stats-card.bg-warning {
            color: white !important;
        }
        
        .stats-card.bg-danger {
            color: white !important;
        }
        
        /* Ensure all text in stats cards is white and visible */
        .stats-card h1, .stats-card h2, .stats-card h3, .stats-card h4, .stats-card h5, .stats-card h6,
        .stats-card p, .stats-card span, .stats-card div, .stats-card i {
            color: white !important;
        }
        
        /* Add subtle pattern overlay for visual interest */
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
            z-index: 1;
        }
        
        .stats-card .d-flex {
            position: relative;
            z-index: 2;
            padding: 20px;
        }
        
        /* Additional card improvements */
        .stats-card h3 {
            font-weight: 700 !important;
            font-size: 2.2rem !important;
            line-height: 1.2 !important;
        }
        
        .stats-card p {
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            opacity: 0.9 !important;
        }
        
        .stats-card i {
            opacity: 0.3 !important;
        }
        
        /* Responsive stats cards */
        @media (max-width: 768px) {
            .stats-card .d-flex {
                padding: 15px;
            }
            
            .stats-card h3 {
                font-size: 1.8rem !important;
            }
            
            .stats-card i {
                font-size: 1.5rem !important;
            }
        }
        
        /* Dark theme support for cards */
        @media (prefers-color-scheme: dark) {
            .card {
                background-color: #2c3e50;
                color: white;
            }
            
            .card-header {
                background-color: #34495e;
                border-bottom-color: #4a5f7a;
            }
        }
        
        /* Fallback colors if CSS variables fail */
        .sidebar {
            background: #2c3e50 !important;
        }
        
        .sidebar[style*="--sidebar-color"] {
            background: var(--sidebar-color) !important;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 12px 20px;
            border-radius: 0;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1) !important;
            border-left-color: rgba(255,255,255,0.5);
            transform: translateX(2px);
        }
        
        .sidebar .nav-link.active {
            color: white !important;
            background: rgba(255,255,255,0.15) !important;
            border-left-color: #fff;
            font-weight: 500;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar .nav-section {
            padding: 15px 20px 5px;
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-section:first-of-type {
            border-top: none;
            margin-top: 0;
        }
        
        .nav-submenu {
            background: rgba(0,0,0,0.15);
            margin-left: 0;
            border-left: 2px solid rgba(255,255,255,0.1);
        }
        
        .nav-submenu .nav-link {
            padding-left: 40px;
            font-size: 14px;
            color: rgba(255,255,255,0.8) !important;
        }
        
        .nav-submenu .nav-link:hover {
            background: rgba(255,255,255,0.08) !important;
            color: rgba(255,255,255,0.95) !important;
        }
        
        .nav-submenu .nav-link.active {
            background: rgba(255,255,255,0.12) !important;
            color: white !important;
        }
        
        .nav-toggle {
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-toggle:hover {
            background: rgba(255,255,255,0.1) !important;
        }
        
        .nav-toggle .fa-chevron-down {
            transition: transform 0.3s ease;
            font-size: 12px;
        }
        
        .nav-toggle.collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }
        
        .sidebar-header {
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        
        .sidebar-header h5 {
            font-size: 16px;
            font-weight: 600;
        }
        
        .sidebar-header small {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .content-header {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: white !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .btn-primary:focus {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-color), 0.25);
        }
        
        .btn-primary:active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }
        
        /* Ensure all admin button variants are visible */
        .btn {
            border-width: 1px !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
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
        
        .btn-outline-secondary {
            color: #6c757d !important;
            border-color: #6c757d !important;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            color: white !important;
        }
        
        /* Special styling for logout button in sidebar */
        .sidebar .nav-item form .nav-link {
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            color: rgba(255,255,255,0.9) !important;
            padding: 12px 20px;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-item form .nav-link:hover {
            background: rgba(255,69,69,0.15) !important;
            color: #ff6b6b !important;
            border-left-color: #ff6b6b;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                transition: margin-left 0.3s ease;
                z-index: 1050;
            }
            
            .sidebar.show {
                margin-left: 0;
                box-shadow: 5px 0 15px rgba(0,0,0,0.3);
            }
            
            .main-content {
                margin-left: 0;
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
        
        /* Notification Toast Styling */
        #newOrderToast {
            min-width: 350px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            border: none;
        }
        
        #newOrderToast .toast-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-bottom: 2px solid rgba(255,255,255,0.2);
        }
        
        #newOrderToast .toast-body {
            background: #fff;
            border-radius: 0 0 0.375rem 0.375rem;
        }
        
        /* Bell animation */
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            10%, 30%, 50%, 70%, 90% { transform: rotate(10deg); }
            20%, 40%, 60%, 80% { transform: rotate(-10deg); }
        }
        
        .notification-bell.animate__ring {
            animation: bellRing 1s ease-in-out;
        }
        
        /* Enhanced Notification Badge Styling */
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            font-size: 0.65rem;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 10;
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border-radius: 50%;
        }
        
        .notification-badge.bg-danger {
            background-color: #dc3545 !important;
            color: #ffffff !important;
        }
        
        /* Dropdown Menu Positioning */
        .dropdown-menu {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
        }
        
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }
        
        /* Header Actions Container */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .header-actions .btn {
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        
        /* Notification Bell Button */
        .notification-btn {
            position: relative;
            transition: all 0.3s ease;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .notification-btn:hover {
            transform: translateY(-1px);
        }
        
        .notification-btn .fa-bell {
            font-size: 1rem;
        }
        
        /* Profile Button */
        .profile-btn {
            transition: all 0.3s ease;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .profile-btn:hover {
            transform: translateY(-1px);
        }
        
        .profile-btn .fa-user {
            font-size: 0.875rem;
        }
        
        /* Responsive Header Actions */
        @media (max-width: 768px) {
            .header-actions {
                gap: 0.5rem;
            }
            
            .notification-btn,
            .profile-btn {
                padding: 0.375rem 0.5rem;
                font-size: 0.8rem;
            }
            
            .notification-btn .fa-bell {
                font-size: 0.875rem;
            }
            
            .profile-btn .fa-user {
                font-size: 0.8rem;
            }
            
            .notification-badge {
                top: -4px;
                right: -4px;
                min-width: 14px;
                height: 14px;
                font-size: 0.6rem;
            }
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header p-3">
            <div class="d-flex align-items-center">
                <?php if($globalCompany->company_logo): ?>
                    <img src="<?php echo e(asset('storage/' . $globalCompany->company_logo)); ?>" 
                         alt="<?php echo e($globalCompany->company_name ?? 'Logo'); ?>" 
                         class="me-2" 
                         style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                <?php else: ?>
                    <div class="me-2" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🏪
                    </div>
                <?php endif; ?>
                <div>
                    <h5 class="text-white mb-0"><?php echo e($globalCompany->company_name ?? 'Admin Panel'); ?></h5>
                    <small class="text-white-50">Management System</small>
                </div>
            </div>
        </div>
        
        <div class="sidebar-content">
            <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <!-- E-commerce Section -->
            <div class="nav-section">E-commerce</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.categories.index')); ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.products.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.products.index')); ?>">
                    <i class="fas fa-box"></i> Products
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.orders.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.orders.index')); ?>">
                    <i class="fas fa-shopping-cart"></i> Customer Orders
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.customers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.customers.index')); ?>">
                    <i class="fas fa-users"></i> Customers
                </a>
            </li>
            
            <!-- Organization Section -->
            <div class="nav-section">Organization</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.branches.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.branches.index')); ?>">
                    <i class="fas fa-building"></i> Branch Management
                </a>
            </li>
            
            <!-- User Management -->
            <li class="nav-item">
                <a class="nav-link nav-toggle <?php echo e(request()->routeIs('admin.employees.*', 'admin.roles.*', 'admin.permissions.*') ? '' : 'collapsed'); ?>" 
                   data-bs-toggle="collapse" 
                   href="#userManagementSubmenu" 
                   role="button" 
                   aria-expanded="<?php echo e(request()->routeIs('admin.employees.*', 'admin.roles.*', 'admin.permissions.*') ? 'true' : 'false'); ?>">
                    <i class="fas fa-users-cog"></i> User Management
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu <?php echo e(request()->routeIs('admin.employees.*', 'admin.roles.*', 'admin.permissions.*') ? 'show' : ''); ?>" id="userManagementSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.employees.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.employees.index')); ?>">
                                <i class="fas fa-user-tie"></i> Employees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.roles.index')); ?>">
                                <i class="fas fa-user-tag"></i> Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.permissions.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.permissions.index')); ?>">
                                <i class="fas fa-key"></i> Permissions
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Procurement Section -->
            <div class="nav-section">Procurement</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.suppliers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.suppliers.index')); ?>">
                    <i class="fas fa-truck"></i> Suppliers
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.purchase-orders.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.purchase-orders.index')); ?>">
                    <i class="fas fa-file-invoice"></i> Purchase Orders
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.estimates.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.estimates.index')); ?>">
                    <i class="fas fa-calculator"></i> Estimates
                </a>
            </li>
            
            <!-- Inventory Section -->
            <div class="nav-section">Inventory</div>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle <?php echo e(request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? '' : 'collapsed'); ?>" 
                   data-bs-toggle="collapse" 
                   href="#inventorySubmenu" 
                   role="button" 
                   aria-expanded="<?php echo e(request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? 'true' : 'false'); ?>">
                    <i class="fas fa-warehouse"></i> Inventory
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu <?php echo e(request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? 'show' : ''); ?>" id="inventorySubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.inventory.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.inventory.index')); ?>">
                                <i class="fas fa-boxes"></i> Stock Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.grns.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.grns.index')); ?>">
                                <i class="fas fa-clipboard-check"></i> Goods Receipt (GRN)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.stock-adjustments.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.stock-adjustments.index')); ?>">
                                <i class="fas fa-exchange-alt"></i> Stock Adjustments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.inventory.low-stock') ? 'active' : ''); ?>" href="<?php echo e(route('admin.inventory.low-stock')); ?>">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.inventory.valuation') ? 'active' : ''); ?>" href="<?php echo e(route('admin.inventory.valuation')); ?>">
                                <i class="fas fa-calculator"></i> Stock Valuation
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Sales Section -->
            <div class="nav-section">Sales</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.pos.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.pos.index')); ?>">
                    <i class="fas fa-cash-register"></i> Point of Sale (POS)
                </a>
            </li>
            
            <!-- Marketing Section -->
            <div class="nav-section">Marketing</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.banners.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.banners.index')); ?>">
                    <i class="fas fa-image"></i> Banners
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.offers.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.offers.index')); ?>">
                    <i class="fas fa-percent"></i> Offers
                </a>
            </li>
            
            <!-- Reports Section -->
            <div class="nav-section">Reports & Analytics</div>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle <?php echo e(request()->routeIs('admin.reports.*') ? '' : 'collapsed'); ?>" 
                   data-bs-toggle="collapse" 
                   href="#reportsSubmenu" 
                   role="button" 
                   aria-expanded="<?php echo e(request()->routeIs('admin.reports.*') ? 'true' : 'false'); ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu <?php echo e(request()->routeIs('admin.reports.*') ? 'show' : ''); ?>" id="reportsSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.index') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.index')); ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.customers') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.customers')); ?>">
                                <i class="fas fa-users"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.sales') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.sales')); ?>">
                                <i class="fas fa-chart-line"></i> Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.inventory') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.inventory')); ?>">
                                <i class="fas fa-warehouse"></i> Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.purchase-orders') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.purchase-orders')); ?>">
                                <i class="fas fa-file-invoice"></i> Purchase Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.grn') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.grn')); ?>">
                                <i class="fas fa-truck"></i> GRN Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.reports.income') ? 'active' : ''); ?>" href="<?php echo e(route('admin.reports.income')); ?>">
                                <i class="fas fa-money-bill-wave"></i> Financial
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- System Section -->
            <div class="nav-section">System</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.payment-methods.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.payment-methods.index')); ?>">
                    <i class="fas fa-credit-card"></i> Payment Methods
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle <?php echo e(request()->routeIs('admin.settings.*') ? '' : 'collapsed'); ?>" 
                   data-bs-toggle="collapse" 
                   href="#settingsSubmenu" 
                   role="button" 
                   aria-expanded="<?php echo e(request()->routeIs('admin.settings.*') ? 'true' : 'false'); ?>">
                    <i class="fas fa-cog"></i> Settings
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu <?php echo e(request()->routeIs('admin.settings.*') ? 'show' : ''); ?>" id="settingsSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.settings.index') ? 'active' : ''); ?>" href="<?php echo e(route('admin.settings.index')); ?>">
                                <i class="fas fa-cogs"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.settings.profile') ? 'active' : ''); ?>" href="<?php echo e(route('admin.settings.profile')); ?>">
                                <i class="fas fa-user-cog"></i> Profile & Account
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.notifications.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.notifications.index')); ?>">
                    <i class="fas fa-bell"></i> Notifications
                    <span class="badge bg-danger ms-2" id="sidebarNotificationBadge" style="display: none;"></span>
                </a>
            </li>
            
            <li class="nav-item mt-auto">
                <a class="nav-link" href="<?php echo e(route('home')); ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Store
                </a>
            </li>
            
            <li class="nav-item">
                <form action="<?php echo e(route('admin.logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Mobile Menu Toggle -->
        <div class="d-md-none p-3 bg-white border-bottom">
            <button class="btn btn-primary btn-sm" id="sidebarToggle">
                <i class="fas fa-bars me-1"></i> Menu
            </button>
        </div>
        
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><?php echo $__env->yieldContent('page_title', 'Dashboard'); ?></h1>
                    <?php if(isset($breadcrumbs)): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($loop->last): ?>
                                        <li class="breadcrumb-item active"><?php echo e($breadcrumb['title']); ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item"><a href="<?php echo e($breadcrumb['url']); ?>"><?php echo e($breadcrumb['title']); ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </nav>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex align-items-center gap-2 header-actions">
                    <?php echo $__env->yieldContent('page_actions'); ?>
                    
                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle position-relative notification-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell notification-bell"></i>
                            <span class="badge bg-danger rounded-pill notification-badge" id="notificationCount" style="display: none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div id="notificationsList">
                                <div class="text-center p-3">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?php echo e(route('admin.notifications.index')); ?>">
                                <i class="fas fa-eye"></i> View All Notifications
                            </a>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle profile-btn" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            <?php if(auth()->user()->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="Profile" class="rounded-circle" width="24" height="24">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                            <?php echo e(auth()->user()->name); ?>

                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?php echo e(route('admin.settings.index')); ?>">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <a class="dropdown-item" href="<?php echo e(route('admin.settings.profile')); ?>">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="<?php echo e(route('admin.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Sound -->
        <audio id="notificationSound" preload="auto" style="display: none;">
            <source src="<?php echo e(asset('admin/sounds/notification.mp3')); ?>" type="audio/mpeg">
            <source src="<?php echo e(asset('admin/sounds/notification.ogg')); ?>" type="audio/ogg">
            <!-- Fallback beep sound as data URI -->
            <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjGH0fPTgjMGHm7A7+OZURE
" type="audio/wav">
        </audio>

        <!-- Alerts -->
        <div class="px-3">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Main Content -->
        <div class="p-3">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <!-- New Order Popup Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        <div id="newOrderToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-shopping-cart me-2"></i>
                <strong class="me-auto">New Order Received!</strong>
                <small class="text-white-50">Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div id="orderNotificationContent">
                    <!-- Order details will be inserted here -->
                </div>
                <div class="mt-3 d-flex gap-2">
                    <a href="#" id="viewOrderBtn" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Order
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">
                        <i class="fas fa-times"></i> Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        // Global variables for notifications
        let lastNotificationCheck = new Date().toISOString();
        let notificationCheckInterval;
        let soundEnabled = <?php echo e(\App\Models\AppSetting::get('sound_notifications', true) ? 'true' : 'false'); ?>;
        let popupEnabled = <?php echo e(\App\Models\AppSetting::get('popup_notifications', true) ? 'true' : 'false'); ?>;
        
        // Mobile sidebar toggle
        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('show');
            $('.sidebar-overlay').toggleClass('show');
        });
        
        // Close sidebar when clicking overlay
        $(document).on('click', '.sidebar-overlay', function() {
            $('.sidebar').removeClass('show');
            $('.sidebar-overlay').removeClass('show');
        });
        
        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('.sidebar, #sidebarToggle').length) {
                    $('.sidebar').removeClass('show');
                    $('.sidebar-overlay').removeClass('show');
                }
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
        
        // Initialize notifications if we're on an admin page
        if (window.location.pathname.includes('/admin')) {
            loadNotifications();
            startNotificationChecking();
        }
        
        // Load notifications function
        function loadNotifications() {
            $.get('<?php echo e(route("admin.notifications.unread")); ?>', function(data) {
                updateNotificationBadge(data.count);
                renderNotifications(data.notifications);
            }).fail(function() {
                console.log('Failed to load notifications');
            });
        }
        
        // Update notification badge
        function updateNotificationBadge(count) {
            const badge = $('#notificationCount');
            const sidebarBadge = $('#sidebarNotificationBadge');
            
            if (count > 0) {
                badge.text(count).show();
                sidebarBadge.text(count).show();
            } else {
                badge.hide();
                sidebarBadge.hide();
            }
        }
        
        // Render notifications
        function renderNotifications(notifications) {
            const container = $('#notificationsList');
            
            if (notifications.length === 0) {
                container.html('<div class="text-center p-3 text-muted">No new notifications</div>');
                return;
            }
            
            let html = '';
            notifications.forEach(function(notification) {
                html += `
                    <div class="dropdown-item notification-item" onclick="markNotificationAsRead(${notification.id})">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="${notification.icon} text-${notification.color}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${notification.title}</div>
                                <div class="small text-muted">${notification.message}</div>
                                <div class="small text-muted mt-1">${notification.created_at}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
        }
        
        // Mark notification as read
        function markNotificationAsRead(notificationId) {
            $.post(`/admin/notifications/${notificationId}/mark-read`, {
                _token: '<?php echo e(csrf_token()); ?>'
            }, function() {
                loadNotifications();
            });
        }
        
        // Start checking for new notifications
        function startNotificationChecking() {
            notificationCheckInterval = setInterval(function() {
                checkForNewNotifications();
            }, 10000); // Check every 10 seconds
        }
        
        // Check for new notifications
        function checkForNewNotifications() {
            $.get('<?php echo e(route("admin.notifications.check-new")); ?>', {
                last_check: lastNotificationCheck
            }, function(data) {
                if (data.hasNew && data.notifications.length > 0) {
                    updateNotificationBadge(data.count);
                    
                    // Add animation to bell
                    $('.notification-bell').addClass('animate__animated animate__ring');
                    setTimeout(() => {
                        $('.notification-bell').removeClass('animate__animated animate__ring');
                    }, 1000);
                    
                    // Handle notifications
                    data.notifications.forEach(function(notification) {
                        if (notification.type === 'order_placed') {
                            handleNewOrderNotification(notification);
                        }
                    });
                    
                    loadNotifications();
                }
                
                lastNotificationCheck = new Date().toISOString();
            }).fail(function() {
                console.log('Failed to check for new notifications');
            });
        }
        
        // Handle new order notification
        function handleNewOrderNotification(notification) {
            // Play sound if enabled
            if (soundEnabled) {
                playNotificationSound();
            }
            
            // Show browser notification if supported
            if ("Notification" in window && Notification.permission === "granted") {
                new Notification(notification.title, {
                    body: notification.message,
                    icon: '/favicon.ico'
                });
            }
            
            // Show popup notification if enabled
            if (popupEnabled) {
                showOrderPopup(notification);
            }
        }
        
        // Show order popup notification
        function showOrderPopup(notification) {
            const orderData = notification.data || {};
            
            // Format order details
            let orderDetails = `
                <div class="mb-2">
                    <strong>Order #${orderData.order_number || 'N/A'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-user"></i> Customer: <strong>${orderData.customer_name || 'Guest'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-money-bill"></i> Total: <strong class="text-success">₹${orderData.total || '0.00'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-clock"></i> Time: ${notification.created_at || 'Just now'}
                </div>
            `;
            
            // Update popup content
            $('#orderNotificationContent').html(orderDetails);
            
            // Update view order button link
            if (orderData.order_id) {
                $('#viewOrderBtn').attr('href', `/admin/orders/${orderData.order_id}`);
            }
            
            // Show the toast
            const toastEl = document.getElementById('newOrderToast');
            const toast = new bootstrap.Toast(toastEl, {
                autohide: false
            });
            toast.show();
            
            // Add pulse animation to grab attention
            $('#newOrderToast').addClass('animate__animated animate__pulse');
            setTimeout(() => {
                $('#newOrderToast').removeClass('animate__animated animate__pulse');
            }, 1000);
        }
        
        // Play notification sound
        function playNotificationSound() {
            try {
                const audio = document.getElementById('notificationSound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.log('Could not play notification sound:', e));
                }
            } catch (e) {
                console.log('Error playing notification sound:', e);
            }
        }
        
        // Mark all notifications as read
        $('#markAllReadBtn').click(function() {
            $.post('<?php echo e(route("admin.notifications.mark-all-read")); ?>', {
                _token: '<?php echo e(csrf_token()); ?>'
            }, function() {
                loadNotifications();
            });
        });
        
        // Request notification permission
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    </script>
    
    <!-- Session Manager for handling session expiration -->
    <script src="<?php echo e(asset('js/session-manager.js')); ?>"></script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\source_code\ecom\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>