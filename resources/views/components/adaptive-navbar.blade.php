@props(['company' => null, 'categories' => null, 'forceSize' => null])

@php
    use App\Services\AdaptiveNavbarService;
    
    $company = $company ?? ($globalCompany ?? null);
    $categories = $categories ?? ($globalCategories ?? collect());
    
    // Fallback to database query if no categories provided
    if ($categories->isEmpty()) {
        try {
            $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
        } catch (\Exception $e) {
            $categories = collect();
        }
    }
    
    // Determine navbar configuration
    $navbarAnalysis = $company ? AdaptiveNavbarService::determineNavbarSize($company) : ['size_class' => 'normal'];
    $sizeClass = $forceSize ?? $navbarAnalysis['size_class'];
    $jsConfig = $company ? AdaptiveNavbarService::getJSConfig($company) : ['sizeClass' => 'normal'];
@endphp

{{-- Adaptive Navbar Component --}}
<nav class="navbar navbar-expand-lg fixed-top navbar-modern {{ $sizeClass }}" 
     id="adaptive-navbar" 
     data-config="{{ json_encode($jsConfig) }}"
     {{ $attributes }}>
    <div class="container">
        {{-- Brand Section with Adaptive Logo --}}
        <a class="navbar-brand-modern" href="{{ route('shop') }}" id="navbar-brand">
            @if($company?->company_logo)
                <img src="{{ asset('storage/' . $company->company_logo) }}" 
                     alt="{{ $company->company_name }}"
                     id="navbar-logo"
                     class="navbar-logo">
            @else
                <i class="fas fa-store navbar-icon" id="navbar-icon"></i>
            @endif
            <span class="navbar-text" id="navbar-text">{{ $company?->company_name ?? 'Your Store' }}</span>
        </a>
        
        {{-- Mobile Toggle Button --}}
        <button class="navbar-toggler navbar-toggler-modern" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Navigation Links --}}
            <ul class="navbar-nav navbar-nav-modern me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" 
                       href="{{ route('shop') }}">
                        <i class="fas fa-home me-1"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                
                @if($categories->count() > 0)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('category') ? 'active' : '' }}" 
                       href="#" 
                       id="categoriesDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="fas fa-th-large me-1"></i>
                        <span class="nav-text">Categories</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-modern" id="categories-dropdown">
                        @foreach($categories as $category)
                            <li>
                                <a class="dropdown-item dropdown-item-modern" 
                                   href="{{ route('category', $category->slug) }}">
                                    <i class="fas fa-tag me-2"></i>
                                    <span class="category-name">{{ $category->name }}</span>
                                    @if(($category->products_count ?? 0) > 0)
                                        <small class="text-muted ms-auto category-count">({{ $category->products_count }})</small>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                @endif
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}" 
                       href="{{ route('products') }}">
                        <i class="fas fa-box me-1"></i>
                        <span class="nav-text">Products</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('offer.products') ? 'active' : '' }}" 
                       href="{{ route('offer.products') }}">
                        <i class="fas fa-fire me-1"></i>
                        <span class="nav-text">Offers</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}" 
                       href="{{ route('track.order') }}">
                        <i class="fas fa-search me-1"></i>
                        <span class="nav-text">Track Estimate</span>
                    </a>
                </li>
            </ul>
            
            {{-- Search Form --}}
            <form class="search-form me-3" 
                  action="{{ route('search') }}" 
                  method="GET" 
                  id="search-form">
                <input class="form-control search-input" 
                       type="search" 
                       name="q" 
                       placeholder="Search..." 
                       value="{{ request('q') }}" 
                       id="search-input">
                <button class="search-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            {{-- Notification Icon (Admin only) --}}
            @auth
                @if(auth()->user()->is_admin)
                    <div class="notification-wrapper me-3" id="notification-wrapper">
                        <button class="btn btn-outline-primary position-relative notification-btn" 
                                id="notification-btn" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false"
                                title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-count d-none" id="notification-count">0</span>
                        </button>
                        
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" 
                             id="notification-dropdown"
                             style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary me-1" 
                                            id="mark-all-read-btn" 
                                            title="Mark all as read">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    <a href="{{ route('admin.notifications.index') }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="View all">
                                        <i class="fas fa-list"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <div id="notification-list">
                                <div class="dropdown-item-text text-center py-3">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
            
            {{-- Cart Button --}}
            <a href="{{ route('cart.index') }}" 
               class="btn cart-btn position-relative" 
               id="cart-button">
                <i class="fas fa-shopping-cart me-2"></i>
                <span class="d-none d-md-inline cart-text">Cart</span>
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </div>
</nav>

{{-- Development Debug Info (only in debug mode) --}}
@if(config('app.debug') && request()->has('debug-navbar'))
<div class="navbar-debug-info" style="position: fixed; top: 90px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 1060;">
    <strong>Navbar Debug Info:</strong><br>
    Size Class: {{ $sizeClass }}<br>
    Company Name: {{ $company?->company_name ?? 'N/A' }}<br>
    Name Length: {{ strlen($company?->company_name ?? '') }}<br>
    Has Logo: {{ $company?->company_logo ? 'Yes' : 'No' }}<br>
    Categories: {{ $categories->count() }}<br>
    @if(isset($navbarAnalysis['reasons']))
        Reasons: {{ implode(', ', $navbarAnalysis['reasons']) }}<br>
    @endif
</div>
@endif

{{-- Component Styles --}}
@once
@push('styles')
<style>
/* Adaptive Navbar Component Styles */
.navbar-modern {
    --navbar-height: 80px;
    --navbar-padding: 1rem 0;
    --logo-size: 40px;
    --font-size-brand: 1.5rem;
    --font-size-nav: 1rem;
    --search-width: 300px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Size variations */
.navbar-modern.compact {
    --navbar-height: 60px;
    --navbar-padding: 0.5rem 0;
    --logo-size: 30px;
    --font-size-brand: 1.2rem;
    --font-size-nav: 0.9rem;
    --search-width: 250px;
}

.navbar-modern.extra-compact {
    --navbar-height: 50px;
    --navbar-padding: 0.25rem 0;
    --logo-size: 25px;
    --font-size-brand: 1rem;
    --font-size-nav: 0.85rem;
    --search-width: 200px;
}

/* Apply variables */
.navbar-modern {
    min-height: var(--navbar-height);
    padding: var(--navbar-padding);
}

.navbar-logo, .navbar-icon {
    height: var(--logo-size);
    width: auto;
    max-width: var(--logo-size);
    font-size: var(--logo-size);
    transition: all 0.4s ease;
}

.navbar-brand-modern {
    font-size: var(--font-size-brand);
    transition: all 0.4s ease;
}

.navbar-nav-modern .nav-link {
    font-size: var(--font-size-nav);
    transition: all 0.4s ease;
}

.search-form {
    max-width: var(--search-width);
    transition: all 0.4s ease;
}

/* Text overflow handling */
.navbar-text {
    display: inline-block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
    transition: all 0.4s ease;
}

.navbar-modern.compact .navbar-text {
    max-width: 200px;
}

.navbar-modern.extra-compact .navbar-text {
    max-width: 150px;
}

/* Body padding adjustment */
body {
    padding-top: var(--navbar-height, 80px);
    transition: padding-top 0.4s ease;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .navbar-modern {
        --navbar-height: 70px;
    }
    
    .navbar-modern.compact {
        --navbar-height: 60px;
    }
    
    .navbar-modern.extra-compact {
        --navbar-height: 55px;
    }
}

@media (max-width: 576px) {
    .navbar-text {
        max-width: 150px !important;
    }
    
    .navbar-modern.compact .navbar-text,
    .navbar-modern.extra-compact .navbar-text {
        max-width: 120px !important;
    }
    
    .notification-dropdown {
        width: 300px !important;
        margin-left: -250px;
    }
    
    .notification-wrapper {
        order: -1;
        margin-right: 0.5rem !important;
    }
}

/* Notification Styles */
.notification-btn {
    border-radius: var(--radius-lg) !important;
    border: 2px solid var(--primary-color) !important;
    color: var(--primary-color) !important;
    background: transparent !important;
    transition: all 0.3s ease !important;
    position: relative;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-btn:hover {
    background: var(--primary-color) !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.notification-btn:focus {
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
}

.notification-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444 !important;
    color: white !important;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

.notification-count.d-none {
    display: none !important;
}

.notification-dropdown {
    border: none !important;
    box-shadow: var(--shadow-xl) !important;
    border-radius: var(--radius-lg) !important;
    padding: 0 !important;
    margin-top: 0.5rem !important;
}

.notification-dropdown .dropdown-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1rem;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    border: none;
}

.notification-dropdown .dropdown-header h6 {
    color: white;
    font-weight: 600;
}

.notification-dropdown .dropdown-header .btn {
    color: white;
    border-color: rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.notification-dropdown .dropdown-header .btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
}

.notification-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    display: block;
}

.notification-item:hover {
    background: rgba(37, 99, 235, 0.05);
    color: inherit;
    text-decoration: none;
}

.notification-item.unread {
    background: rgba(37, 99, 235, 0.1);
    border-left: 4px solid var(--primary-color);
}

.notification-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.notification-item-content {
    flex: 1;
    min-width: 0;
}

.notification-item-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    line-height: 1.4;
}

.notification-item-message {
    color: var(--text-secondary);
    font-size: 0.8rem;
    line-height: 1.3;
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-item-time {
    color: var(--text-secondary);
    font-size: 0.7rem;
    font-weight: 500;
}

.notification-empty {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--text-secondary);
}

.notification-empty i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}
</style>
@endpush
@endonce

{{-- Component Scripts --}}
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('adaptive-navbar');
    if (!navbar) return;
    
    // Get configuration from data attribute or window
    const config = JSON.parse(navbar.dataset.config || '{}');
    
    // Initialize adaptive behavior
    function initAdaptiveNavbar() {
        // Apply initial size class if not already applied
        if (config.sizeClass && config.sizeClass !== 'normal') {
            navbar.classList.add(config.sizeClass);
        }
        
        // Monitor for changes
        observeChanges();
        
        // Handle scroll events
        handleScrollEvents();
        
        // Update body padding
        updateBodyPadding();
        
        console.log('Adaptive navbar initialized with config:', config);
    }
    
    function observeChanges() {
        const textElement = document.getElementById('navbar-text');
        const logoElement = document.getElementById('navbar-logo');
        
        if (textElement) {
            const textObserver = new MutationObserver(function() {
                setTimeout(recalculateSize, 100);
            });
            
            textObserver.observe(textElement, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
        
        if (logoElement) {
            const logoObserver = new MutationObserver(function() {
                setTimeout(recalculateSize, 100);
            });
            
            logoObserver.observe(logoElement, {
                attributes: true,
                attributeFilter: ['src']
            });
        }
    }
    
    function recalculateSize() {
        const textElement = document.getElementById('navbar-text');
        if (!textElement) return;
        
        const textContent = textElement.textContent || '';
        const textLength = textContent.length;
        
        // Remove existing size classes
        navbar.classList.remove('compact', 'extra-compact');
        
        // Apply appropriate size class
        if (textLength > 25) {
            navbar.classList.add('extra-compact');
        } else if (textLength > 15) {
            navbar.classList.add('compact');
        }
        
        updateBodyPadding();
    }
    
    function handleScrollEvents() {
        let scrollTimeout;
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            if (scrollTimeout) clearTimeout(scrollTimeout);
            
            scrollTimeout = setTimeout(function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (scrollTop > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                lastScrollTop = scrollTop;
                updateBodyPadding();
            }, 10);
        }, { passive: true });
    }
    
    function updateBodyPadding() {
        setTimeout(function() {
            const navbarHeight = navbar.offsetHeight;
            document.body.style.paddingTop = navbarHeight + 'px';
        }, 50);
    }
    
    // Initialize everything
    initAdaptiveNavbar();
    
    // Expose methods globally
    window.adaptiveNavbar = {
        recalculate: recalculateSize,
        config: config
    };
});

// Notification System
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notification-btn');
    const notificationCount = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    
    if (!notificationBtn) return; // Not an admin or not logged in
    
    let isDropdownOpen = false;
    let refreshInterval;
    
    // Initialize notification system
    function initNotifications() {
        loadNotifications();
        setupEventListeners();
        startAutoRefresh();
    }
    
    function setupEventListeners() {
        // Show dropdown and load notifications
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isDropdownOpen) {
                loadNotifications();
            }
        });
        
        // Handle dropdown show/hide events
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.addEventListener('show.bs.dropdown', function() {
            isDropdownOpen = true;
            loadNotifications();
        });
        
        dropdown.addEventListener('hide.bs.dropdown', function() {
            isDropdownOpen = false;
        });
        
        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                markAllAsRead();
            });
        }
    }
    
    function loadNotifications() {
        fetch('/admin/notifications/unread', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateNotificationCount(data.totalCount || 0);
            renderNotifications(data.notifications || []);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = `
                <div class="dropdown-item-text text-center py-3 text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load notifications
                </div>
            `;
        });
    }
    
    function updateNotificationCount(count) {
        if (count > 0) {
            notificationCount.textContent = count > 99 ? '99+' : count;
            notificationCount.classList.remove('d-none');
        } else {
            notificationCount.classList.add('d-none');
        }
    }
    
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <div class="mt-2">No new notifications</div>
                </div>
            `;
            return;
        }
        
        notificationList.innerHTML = notifications.map(notification => `
            <a href="#" class="notification-item ${!notification.is_read ? 'unread' : ''}" 
               data-id="${notification.id}" 
               onclick="handleNotificationClick(${notification.id}, event)">
                <div class="d-flex align-items-start">
                    <div class="notification-item-icon bg-${notification.color} text-white me-3">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-item-content">
                        <div class="notification-item-title">${notification.title}</div>
                        <div class="notification-item-message">${notification.message}</div>
                        <div class="notification-item-time">${notification.created_at}</div>
                    </div>
                </div>
            </a>
        `).join('');
    }
    
    function markAllAsRead() {
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationCount(0);
                loadNotifications();
                showToastMessage('All notifications marked as read', 'success');
            }
        })
        .catch(error => {
            console.error('Error marking notifications as read:', error);
            showToastMessage('Failed to mark notifications as read', 'error');
        });
    }
    
    function markAsRead(notificationId) {
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
    
    function startAutoRefresh() {
        // Refresh notifications every 30 seconds
        refreshInterval = setInterval(() => {
            if (!isDropdownOpen) {
                // Only update count when dropdown is closed
                fetch('/admin/notifications/count', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateNotificationCount(data.count || 0);
                })
                .catch(error => {
                    console.error('Error getting notification count:', error);
                });
            }
        }, 30000);
    }
    
    function showToastMessage(message, type = 'success') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            // Fallback alert
            alert(message);
        }
    }
    
    // Global function for handling notification clicks
    window.handleNotificationClick = function(notificationId, event) {
        event.preventDefault();
        
        // Mark as read
        markAsRead(notificationId);
        
        // Close dropdown
        const dropdown = bootstrap.Dropdown.getInstance(notificationBtn);
        if (dropdown) {
            dropdown.hide();
        }
        
        // Redirect to notifications page
        setTimeout(() => {
            window.location.href = '/admin/notifications';
        }, 300);
    };
    
    // Initialize the notification system
    initNotifications();
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
});
</script>
@endpush
@endonce
