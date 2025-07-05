<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ $globalCompany->company_name ?? 'Herbal Bliss' }}</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: {{ $globalCompany->primary_color ?? '#2d5016' }};
            --secondary-color: {{ $globalCompany->secondary_color ?? '#6b8e23' }};
            --sidebar-color: {{ $globalCompany->sidebar_color ?? '#2d5016' }};
            --sidebar-width: 280px;
        }
        
        .sidebar {
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-color);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
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
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 0;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar .nav-section {
            padding: 15px 20px 5px;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-submenu {
            background: rgba(0,0,0,0.1);
            margin-left: 0;
        }
        
        .nav-submenu .nav-link {
            padding-left: 40px;
            font-size: 14px;
        }
        
        .nav-toggle {
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .nav-toggle .fa-chevron-down {
            transition: transform 0.3s;
        }
        
        .nav-toggle.collapsed .fa-chevron-down {
            transform: rotate(-90deg);
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
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
                transition: margin-left 0.3s;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header p-3">
            <div class="d-flex align-items-center">
                @if($globalCompany->company_logo ?? null)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                         alt="{{ $globalCompany->company_name ?? 'Logo' }}" 
                         class="me-2" 
                         style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                @else
                    <div class="me-2" style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🌿
                    </div>
                @endif
                <div>
                    <h5 class="text-white mb-0">{{ $globalCompany->company_name ?? 'Herbal ERP' }}</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
            </div>
        </div>
        
        <div class="sidebar-content">
            <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            
            <!-- E-commerce Section -->
            <div class="nav-section">E-commerce</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="fas fa-tags"></i> Categories
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <i class="fas fa-box"></i> Products
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-shopping-cart"></i> Customer Orders
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-users"></i> Customers
                </a>
            </li>
            
            <!-- Procurement Section -->
            <div class="nav-section">Procurement</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
                    <i class="fas fa-truck"></i> Suppliers
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.purchase-orders.*') ? 'active' : '' }}" href="{{ route('admin.purchase-orders.index') }}">
                    <i class="fas fa-file-invoice"></i> Purchase Orders
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.estimates.*') ? 'active' : '' }}" href="{{ route('admin.estimates.index') }}">
                    <i class="fas fa-calculator"></i> Estimates
                </a>
            </li>
            
            <!-- Inventory Section -->
            <div class="nav-section">Inventory</div>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle {{ request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? '' : 'collapsed' }}" 
                   data-bs-toggle="collapse" 
                   href="#inventorySubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? 'true' : 'false' }}">
                    <i class="fas fa-warehouse"></i> Inventory
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu {{ request()->routeIs('admin.inventory.*', 'admin.grns.*', 'admin.stock-adjustments.*') ? 'show' : '' }}" id="inventorySubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                                <i class="fas fa-boxes"></i> Stock Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.grns.*') ? 'active' : '' }}" href="{{ route('admin.grns.index') }}">
                                <i class="fas fa-clipboard-check"></i> Goods Receipt (GRN)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.stock-adjustments.*') ? 'active' : '' }}" href="{{ route('admin.stock-adjustments.index') }}">
                                <i class="fas fa-exchange-alt"></i> Stock Adjustments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory.low-stock') ? 'active' : '' }}" href="{{ route('admin.inventory.low-stock') }}">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory.valuation') ? 'active' : '' }}" href="{{ route('admin.inventory.valuation') }}">
                                <i class="fas fa-calculator"></i> Stock Valuation
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Sales Section -->
            <div class="nav-section">Sales</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}" href="{{ route('admin.pos.index') }}">
                    <i class="fas fa-cash-register"></i> Point of Sale (POS)
                </a>
            </li>
            
            <!-- Marketing Section -->
            <div class="nav-section">Marketing</div>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                    <i class="fas fa-image"></i> Banners
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.offers.*') ? 'active' : '' }}" href="{{ route('admin.offers.index') }}">
                    <i class="fas fa-percent"></i> Offers
                </a>
            </li>
            
            <!-- Reports Section -->
            <div class="nav-section">Reports & Analytics</div>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle {{ request()->routeIs('admin.reports.*') ? '' : 'collapsed' }}" 
                   data-bs-toggle="collapse" 
                   href="#reportsSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="reportsSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}" href="{{ route('admin.reports.customers') }}">
                                <i class="fas fa-users"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}" href="{{ route('admin.reports.sales') }}">
                                <i class="fas fa-chart-line"></i> Sales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}" href="{{ route('admin.reports.inventory') }}">
                                <i class="fas fa-warehouse"></i> Inventory
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.purchase-orders') ? 'active' : '' }}" href="{{ route('admin.reports.purchase-orders') }}">
                                <i class="fas fa-file-invoice"></i> Purchase Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.grn') ? 'active' : '' }}" href="{{ route('admin.reports.grn') }}">
                                <i class="fas fa-truck"></i> GRN Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.income') ? 'active' : '' }}" href="{{ route('admin.reports.income') }}">
                                <i class="fas fa-money-bill-wave"></i> Financial
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- System Section -->
            <div class="nav-section">System</div>
            
            <li class="nav-item">
                <a class="nav-link nav-toggle {{ request()->routeIs('admin.settings.*') ? '' : 'collapsed' }}" 
                   data-bs-toggle="collapse" 
                   href="#settingsSubmenu" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}">
                    <i class="fas fa-cog"></i> Settings
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse nav-submenu {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}" id="settingsSubmenu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cogs"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.profile') ? 'active' : '' }}" href="{{ route('admin.settings.profile') }}">
                                <i class="fas fa-user-cog"></i> Profile & Account
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                    <i class="fas fa-bell"></i> Notifications
                    <span class="badge bg-danger ms-2" id="sidebarNotificationBadge" style="display: none;"></span>
                </a>
            </li>
            
            <li class="nav-item mt-auto">
                <a class="nav-link" href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Store
                </a>
            </li>
            
            <li class="nav-item">
                <!-- FIXED LOGOUT FORM - Using direct URL instead of route helper -->
                <form action="/admin/logout" method="POST">
                    @csrf
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
        <div class="d-md-none p-3">
            <button class="btn btn-primary" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">@yield('page_title', 'Dashboard')</h1>
                    @if(isset($breadcrumbs))
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                @foreach($breadcrumbs as $breadcrumb)
                                    @if($loop->last)
                                        <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                                    @else
                                        <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown mr-3">
                        <button class="btn btn-outline-primary dropdown-toggle position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-danger badge-pill position-absolute" id="notificationCount" style="top: -5px; right: -5px; display: none;"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div id="notificationsList">
                                <div class="text-center p-3">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="{{ route('admin.notifications.index') }}">
                                <i class="fas fa-eye"></i> View All Notifications
                            </a>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile" class="rounded-circle" width="24" height="24">
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                            {{ auth()->user()->name }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.settings.profile') }}">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <!-- FIXED LOGOUT FORM - Using direct URL instead of route helper -->
                            <form action="/admin/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                @yield('page_actions')
            </div>
        </div>

        <!-- Notification Sound -->
        <audio id="notificationSound" preload="auto" style="display: none;">
            <source src="{{ asset('admin/sounds/notification.mp3') }}" type="audio/mpeg">
            <source src="{{ asset('admin/sounds/notification.ogg') }}" type="audio/ogg">
        </audio>

        <!-- Alerts -->
        <div class="px-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
        
        <!-- Main Content -->
        <div class="p-3">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        // Global variables for notifications
        let lastNotificationCheck = new Date().toISOString();
        let notificationCheckInterval;
        let soundEnabled = {{ \App\Models\AppSetting::get('sound_notifications', true) ? 'true' : 'false' }};
        let popupEnabled = {{ \App\Models\AppSetting::get('popup_notifications', true) ? 'true' : 'false' }};
        
        // Mobile sidebar toggle
        $('#sidebarToggle').click(function() {
            $('.sidebar').toggleClass('show');
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
            $.get('{{ route("admin.notifications.unread") }}', function(data) {
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
                _token: '{{ csrf_token() }}'
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
            $.get('{{ route("admin.notifications.check-new") }}', {
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
            $.post('{{ route("admin.notifications.mark-all-read") }}', {
                _token: '{{ csrf_token() }}'
            }, function() {
                loadNotifications();
            });
        });
        
        // Request notification permission
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    </script>
    
    @stack('scripts')
</body>
</html>
