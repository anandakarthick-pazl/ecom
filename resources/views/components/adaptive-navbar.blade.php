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
                    <a class="nav-link {{ request()->routeIs('flash.offers') ? 'active' : '' }}" 
                       href="{{ route('flash.offers') }}">
                        <i class="fas fa-bolt me-1"></i>
                        <span class="nav-text">⚡ Flash Offers</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}" 
                       href="{{ route('track.order') }}">
                        <i class="fas fa-search me-1"></i>
                        <span class="nav-text">Track Order</span>
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
</script>
@endpush
@endonce
