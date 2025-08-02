{{-- Adaptive Navbar Component --}}
<nav class="navbar navbar-expand-lg fixed-top navbar-modern" id="adaptive-navbar">
    <div class="container">
        {{-- Brand Section with Adaptive Logo --}}
        <a class="navbar-brand-modern" href="{{ route('shop') }}" id="navbar-brand">
            @if($globalCompany->company_logo)
                <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                     alt="{{ $globalCompany->company_name }}"
                     id="navbar-logo"
                     class="navbar-logo">
            @else
                <i class="fas fa-store navbar-icon"></i>
            @endif
            <span class="navbar-text" id="navbar-text">{{ $globalCompany->company_name ?? 'Your Store' }}</span>
        </a>
        
        {{-- Mobile Toggle Button --}}
        <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Navigation Links --}}
            <ul class="navbar-nav navbar-nav-modern me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">
                        <i class="fas fa-home me-1"></i>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('category') ? 'active' : '' }}" 
                       href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-th-large me-1"></i>
                        <span class="nav-text">Categories</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-modern" id="categories-dropdown">
                        @php
                            $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                        @endphp
                        @if($categories->count() > 0)
                            @foreach($categories as $category)
                                <li>
                                    <a class="dropdown-item dropdown-item-modern" href="{{ route('category', $category->slug) }}">
                                        <i class="fas fa-tag me-2"></i>
                                        <span class="category-name">{{ $category->name }}</span>
                                        @if($category->products_count ?? 0 > 0)
                                            <small class="text-muted ms-auto category-count">({{ $category->products_count }})</small>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <span class="dropdown-item-modern text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No categories available
                                </span>
                            </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}" href="{{ route('products') }}">
                        <i class="fas fa-box me-1"></i>
                        <span class="nav-text">Products</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('offer.products') ? 'active' : '' }}" href="{{ route('offer.products') }}">
                        <i class="fas fa-fire me-1"></i>
                        <span class="nav-text">Offers</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}" href="{{ route('track.order') }}">
                        <i class="fas fa-search me-1"></i>
                        <span class="nav-text">Track Order</span>
                    </a>
                </li>
            </ul>
            
            {{-- Search Form --}}
            <form class="search-form me-3" action="{{ route('search') }}" method="GET" id="search-form">
                <input class="form-control search-input" type="search" name="q" 
                       placeholder="Search..." value="{{ request('q') }}" id="search-input">
                <button class="search-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            {{-- Cart Button --}}
            <a href="{{ route('cart.index') }}" class="btn cart-btn position-relative" id="cart-button">
                <i class="fas fa-shopping-cart me-2"></i>
                <span class="d-none d-md-inline cart-text">Cart</span>
                <span class="cart-count" id="cart-count">0</span>
            </a>
        </div>
    </div>
</nav>

{{-- Adaptive Navbar Styles --}}
<style>
/* ===== ADAPTIVE NAVBAR SYSTEM ===== */

/* Base navbar states */
.navbar-modern {
    --navbar-height: 80px;
    --navbar-padding: 1rem 0;
    --logo-size: 40px;
    --font-size-brand: 1.5rem;
    --font-size-nav: 1rem;
    --search-width: 300px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Compact state - automatically applied based on content */
.navbar-modern.compact {
    --navbar-height: 60px;
    --navbar-padding: 0.5rem 0;
    --logo-size: 30px;
    --font-size-brand: 1.2rem;
    --font-size-nav: 0.9rem;
    --search-width: 250px;
}

/* Extra compact state - for very long company names */
.navbar-modern.extra-compact {
    --navbar-height: 50px;
    --navbar-padding: 0.25rem 0;
    --logo-size: 25px;
    --font-size-brand: 1rem;
    --font-size-nav: 0.85rem;
    --search-width: 200px;
}

/* Apply the variables */
.navbar-modern {
    min-height: var(--navbar-height);
    padding: var(--navbar-padding);
}

.navbar-logo {
    height: var(--logo-size);
    width: auto;
    max-width: var(--logo-size);
    object-fit: contain;
    transition: all 0.4s ease;
}

.navbar-icon {
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

/* Content-based adaptations */
.navbar-brand-modern .navbar-text {
    display: inline-block;
    transition: all 0.4s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}

/* When company name is too long */
.navbar-modern.text-overflow .navbar-text {
    max-width: 200px;
}

.navbar-modern.extra-compact .navbar-text {
    max-width: 150px;
}

/* Scroll-based adaptations */
.navbar-modern.scrolled {
    --navbar-height: 60px;
    --navbar-padding: 0.5rem 0;
    --logo-size: 32px;
    --font-size-brand: 1.3rem;
    box-shadow: var(--shadow-lg);
}

.navbar-modern.scrolled.compact {
    --navbar-height: 50px;
    --logo-size: 28px;
    --font-size-brand: 1.1rem;
}

/* Dropdown adaptations */
.dropdown-menu-modern {
    max-height: 350px;
    overflow-y: auto;
    transition: all 0.3s ease;
}

.navbar-modern.compact .dropdown-menu-modern {
    max-height: 280px;
}

.navbar-modern.extra-compact .dropdown-menu-modern {
    max-height: 250px;
}

.dropdown-item-modern {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.navbar-modern.compact .dropdown-item-modern {
    padding: 0.5rem 0.8rem;
    font-size: 0.85rem;
}

.navbar-modern.extra-compact .dropdown-item-modern {
    padding: 0.4rem 0.7rem;
    font-size: 0.8rem;
}

.category-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}

.navbar-modern.compact .category-name {
    max-width: 150px;
}

.navbar-modern.extra-compact .category-name {
    max-width: 120px;
}

/* Search adaptations */
.search-input {
    padding: 0.75rem 3rem 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.4s ease;
}

.navbar-modern.compact .search-input {
    padding: 0.6rem 2.5rem 0.6rem 0.8rem;
    font-size: 0.85rem;
}

.navbar-modern.extra-compact .search-input {
    padding: 0.5rem 2.2rem 0.5rem 0.7rem;
    font-size: 0.8rem;
}

/* Cart button adaptations */
.cart-btn {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.4s ease;
}

.navbar-modern.compact .cart-btn {
    padding: 0.6rem 0.8rem;
    font-size: 0.85rem;
}

.navbar-modern.extra-compact .cart-btn {
    padding: 0.5rem 0.7rem;
    font-size: 0.8rem;
}

.cart-count {
    width: 22px;
    height: 22px;
    font-size: 0.75rem;
    transition: all 0.4s ease;
}

.navbar-modern.compact .cart-count {
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
}

.navbar-modern.extra-compact .cart-count {
    width: 18px;
    height: 18px;
    font-size: 0.65rem;
}

/* Mobile responsive adaptations */
@media (max-width: 991px) {
    .navbar-modern {
        --navbar-height: 70px;
        --navbar-padding: 0.75rem 0;
    }
    
    .navbar-modern.compact {
        --navbar-height: 60px;
        --navbar-padding: 0.5rem 0;
    }
    
    .navbar-modern.extra-compact {
        --navbar-height: 55px;
        --navbar-padding: 0.4rem 0;
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
    
    .navbar-text {
        max-width: 200px !important;
    }
    
    .navbar-modern.compact .navbar-text,
    .navbar-modern.extra-compact .navbar-text {
        max-width: 150px !important;
    }
}

@media (max-width: 576px) {
    .navbar-brand-modern {
        font-size: 1.2rem;
    }
    
    .navbar-modern.compact .navbar-brand-modern {
        font-size: 1.1rem;
    }
    
    .navbar-modern.extra-compact .navbar-brand-modern {
        font-size: 1rem;
    }
    
    .navbar-text {
        max-width: 150px !important;
    }
    
    /* Hide text on very small screens if needed */
    .navbar-modern.extra-compact .nav-text {
        display: none;
    }
    
    .navbar-modern.extra-compact .cart-text {
        display: none !important;
    }
}

/* Animation keyframes */
@keyframes navbar-resize {
    from { transform: scale(1.02); }
    to { transform: scale(1); }
}

.navbar-modern.resizing {
    animation: navbar-resize 0.4s ease-out;
}

/* Loading states */
.navbar-modern.loading {
    opacity: 0.9;
}

.navbar-modern.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    animation: loading-bar 2s ease-in-out infinite;
}

@keyframes loading-bar {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Body padding adjustments */
body {
    padding-top: var(--navbar-height, 80px);
    transition: padding-top 0.4s ease;
}

/* Smooth transitions for all elements */
* {
    transition-property: height, width, padding, margin, font-size;
    transition-duration: 0.4s;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

{{-- Adaptive Navbar JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('adaptive-navbar');
    const brand = document.getElementById('navbar-brand');
    const logo = document.getElementById('navbar-logo');
    const text = document.getElementById('navbar-text');
    const searchInput = document.getElementById('search-input');
    const categoriesDropdown = document.getElementById('categories-dropdown');
    
    let resizeTimeout;
    let scrollTimeout;
    let lastScrollTop = 0;
    
    // Adaptive sizing based on content
    function adaptNavbarSize() {
        if (!navbar || !text) return;
        
        navbar.classList.add('loading');
        
        // Reset classes
        navbar.classList.remove('compact', 'extra-compact', 'text-overflow');
        
        // Measure text width
        const textWidth = text.scrollWidth;
        const availableWidth = window.innerWidth < 992 ? 200 : 300;
        
        // Company name length analysis
        const companyName = text.textContent;
        const nameLength = companyName.length;
        
        // Determine appropriate size
        let sizeClass = '';
        if (nameLength > 25 || textWidth > availableWidth * 1.2) {
            sizeClass = 'extra-compact';
            text.style.maxWidth = window.innerWidth < 576 ? '120px' : '150px';
        } else if (nameLength > 15 || textWidth > availableWidth) {
            sizeClass = 'compact';
            text.style.maxWidth = window.innerWidth < 576 ? '150px' : '200px';
        }
        
        if (textWidth > parseInt(text.style.maxWidth || '300')) {
            navbar.classList.add('text-overflow');
        }
        
        if (sizeClass) {
            navbar.classList.add(sizeClass);
        }
        
        // Adapt dropdown based on categories count
        if (categoriesDropdown) {
            const categoryItems = categoriesDropdown.querySelectorAll('.dropdown-item-modern');
            if (categoryItems.length > 8) {
                navbar.classList.add('compact');
            }
        }
        
        // Update body padding
        setTimeout(() => {
            const navbarHeight = navbar.offsetHeight;
            document.body.style.paddingTop = navbarHeight + 'px';
        }, 100);
        
        // Add resize animation
        navbar.classList.add('resizing');
        setTimeout(() => {
            navbar.classList.remove('resizing', 'loading');
        }, 400);
    }
    
    // Scroll-based adaptations
    function handleScroll() {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        
        scrollTimeout = setTimeout(() => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            // Auto-hide on scroll down (mobile)
            if (window.innerWidth < 992) {
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    navbar.style.transform = 'translateY(0)';
                }
            }
            
            lastScrollTop = scrollTop;
        }, 10);
    }
    
    // Window resize handler
    function handleResize() {
        if (resizeTimeout) clearTimeout(resizeTimeout);
        
        resizeTimeout = setTimeout(() => {
            adaptNavbarSize();
            
            // Adjust search placeholder
            if (searchInput) {
                const isCompact = navbar.classList.contains('compact') || navbar.classList.contains('extra-compact');
                searchInput.placeholder = window.innerWidth < 576 ? 'Search...' : 
                                        isCompact ? 'Search products...' : 'Search products...';
            }
        }, 150);
    }
    
    // Content change observer
    function observeContentChanges() {
        if (!text) return;
        
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    adaptNavbarSize();
                }
            });
        });
        
        observer.observe(text, {
            childList: true,
            characterData: true,
            subtree: true
        });
        
        // Also observe company logo changes
        if (logo) {
            const logoObserver = new MutationObserver(() => {
                adaptNavbarSize();
            });
            
            logoObserver.observe(logo, {
                attributes: true,
                attributeFilter: ['src']
            });
        }
    }
    
    // Smart dropdown positioning
    function adjustDropdownPosition() {
        const dropdowns = document.querySelectorAll('.dropdown-menu-modern');
        dropdowns.forEach(dropdown => {
            const rect = dropdown.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            
            if (rect.bottom > viewportHeight - 50) {
                dropdown.style.maxHeight = (viewportHeight - rect.top - 100) + 'px';
            }
        });
    }
    
    // Touch-friendly adjustments for mobile
    function mobileOptimizations() {
        if ('ontouchstart' in window) {
            navbar.classList.add('touch-device');
            
            // Larger touch targets
            const navLinks = navbar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.style.minHeight = '44px';
                link.style.display = 'flex';
                link.style.alignItems = 'center';
            });
        }
    }
    
    // Performance monitoring
    function monitorPerformance() {
        let frameCount = 0;
        let lastTime = performance.now();
        
        function checkFPS() {
            frameCount++;
            const currentTime = performance.now();
            
            if (currentTime - lastTime >= 1000) {
                const fps = Math.round((frameCount * 1000) / (currentTime - lastTime));
                
                // Reduce animations if FPS is low
                if (fps < 30) {
                    navbar.classList.add('low-performance');
                } else {
                    navbar.classList.remove('low-performance');
                }
                
                frameCount = 0;
                lastTime = currentTime;
            }
            
            requestAnimationFrame(checkFPS);
        }
        
        requestAnimationFrame(checkFPS);
    }
    
    // Initialize everything
    function init() {
        adaptNavbarSize();
        observeContentChanges();
        mobileOptimizations();
        monitorPerformance();
        
        // Event listeners
        window.addEventListener('scroll', handleScroll, { passive: true });
        window.addEventListener('resize', handleResize, { passive: true });
        
        // Bootstrap dropdown events
        navbar.addEventListener('show.bs.dropdown', adjustDropdownPosition);
        
        // Focus management for accessibility
        navbar.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openDropdowns = navbar.querySelectorAll('.dropdown-menu.show');
                openDropdowns.forEach(dropdown => {
                    bootstrap.Dropdown.getOrCreateInstance(dropdown.previousElementSibling).hide();
                });
            }
        });
        
        console.log('Adaptive navbar initialized successfully');
    }
    
    // Run initialization
    init();
    
    // Expose functions globally for external use
    window.adaptiveNavbar = {
        resize: adaptNavbarSize,
        getCurrentSize: () => {
            if (navbar.classList.contains('extra-compact')) return 'extra-compact';
            if (navbar.classList.contains('compact')) return 'compact';
            return 'normal';
        }
    };
});

// Additional low-performance optimizations
document.head.insertAdjacentHTML('beforeend', `
<style>
.navbar-modern.low-performance * {
    transition: none !important;
    animation: none !important;
}

.navbar-modern.low-performance .cart-count {
    animation: none !important;
}
</style>
`);
</script>
