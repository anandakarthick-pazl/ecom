{{-- Modern Premium Navbar --}}
<nav class="modern-navbar" id="modernNavbar">
    <div class="modern-container">
        {{-- Left Section: Brand --}}
        <div class="nav-brand-section">
            <a href="{{ route('shop') }}" class="brand-wrapper">
                @if($globalCompany->company_logo)
                    <div class="brand-logo-container">
                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                             alt="{{ $globalCompany->company_name }}" 
                             class="brand-logo">
                    </div>
                @else
                    <div class="brand-icon-container">
                        <i class="fas fa-store"></i>
                    </div>
                @endif
                <div class="brand-info">
                    <h1 class="brand-name">{{ $globalCompany->company_name ?? 'Your Store' }}</h1>
                    {{-- <span class="brand-tagline">Premium Shopping Experience</span> --}}
                </div>
            </a>
        </div>
        
        {{-- Center Section: Navigation --}}
        <div class="nav-center-section">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span class="nav-text">Home</span>
                    </a>
                </li>
                
                <li class="nav-item has-dropdown">
                    <a href="#" class="nav-link {{ request()->routeIs('category') ? 'active' : '' }}" onclick="toggleModernDropdown(event, 'categoriesMenu')">
                        <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                        <span class="nav-text">Categories</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <div class="mega-dropdown" id="categoriesMenu">
                        <div class="dropdown-header">
                            <h3>Shop by Category</h3>
                            <p>Explore our wide range of products</p>
                        </div>
                        <div class="dropdown-grid">
                            @php
                                $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                            @endphp
                            @foreach($categories as $category)
                                <a href="{{ route('category', $category->slug) }}" class="category-card">
                                    <div class="category-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="category-details">
                                        <h4>{{ $category->name }}</h4>
                                        @if($category->products_count > 0)
                                            <span class="product-count">{{ $category->products_count }} items</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="dropdown-footer">
                            <a href="{{ route('products') }}" class="view-all-link">
                                View All Products <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item has-dropdown">
                    <a href="#" class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}" onclick="toggleModernDropdown(event, 'productsMenu')">
                        <span class="nav-icon"><i class="fas fa-box"></i></span>
                        <span class="nav-text">Products</span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                    <div class="mega-dropdown products-dropdown" id="productsMenu">
                        <div class="dropdown-header">
                            <h3>Quick Shop</h3>
                            <p>Browse and add products to cart</p>
                        </div>
                        <div class="products-quick-grid">
                            @php
                                $featuredProducts = \App\Models\Product::active()
                                    ->inStock()
                                    ->take(6)
                                    ->get();
                            @endphp
                            @foreach($featuredProducts as $product)
                                <div class="quick-product-card">
                                    <div class="quick-product-image">
                                        @if($product->featured_image)
                                            <img src="{{ $product->featured_image_url }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="product-placeholder-mini">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="quick-product-info">
                                        <h5>{{ Str::limit($product->name, 20) }}</h5>
                                        <p class="quick-product-price">₹{{ number_format($product->price, 2) }}</p>
                                        <div class="quick-cart-controls">
                                            <div class="quick-qty-selector">
                                                <button class="quick-qty-btn" onclick="quickDecrementQty({{ $product->id }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="quick-qty-input" id="quick-qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                                                <button class="quick-qty-btn" onclick="quickIncrementQty({{ $product->id }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <button class="quick-add-btn" onclick="quickAddToCart({{ $product->id }})">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="dropdown-footer">
                            <a href="{{ route('products') }}" class="view-all-link">
                                View All Products <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="{{ route('cart.index') }}" class="view-cart-link">
                                <i class="fas fa-shopping-cart"></i> View Cart
                                <span class="cart-items-count" id="dropdown-cart-count">0</span>
                            </a>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('offer.products') }}" class="nav-link {{ request()->routeIs('offer.products') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-fire"></i></span>
                        <span class="nav-text">Offers</span>
                        <span class="offer-badge">Hot</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('track.order') }}" class="nav-link {{ request()->routeIs('track.order') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas fa-shipping-fast"></i></span>
                        <span class="nav-text">Track</span>
                    </a>
                </li>
            </ul>
        </div>
        
        {{-- Right Section: Actions --}}
        <div class="nav-actions-section">
            {{-- Search --}}
            <div class="search-container">
                <button class="action-btn search-trigger" onclick="toggleModernSearch()">
                    <i class="fas fa-search"></i>
                </button>
                <div class="search-popup" id="searchPopup">
                    <form action="{{ route('search') }}" method="GET" class="modern-search-form">
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" 
                                   name="q" 
                                   class="modern-search-input" 
                                   placeholder="Search products, categories..."
                                   value="{{ request('q') }}"
                                   autocomplete="off">
                            <button type="submit" class="search-submit">
                                Search
                            </button>
                        </div>
                        <div class="search-suggestions">
                            <span class="suggestion-tag">Trending:</span>
                            <a href="{{ route('search', ['q' => 'new arrivals']) }}" class="suggestion">New Arrivals</a>
                            <a href="{{ route('search', ['q' => 'best sellers']) }}" class="suggestion">Best Sellers</a>
                            <a href="{{ route('offer.products') }}" class="suggestion">Special Offers</a>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Cart --}}
            <a href="{{ route('cart.index') }}" class="action-btn cart-btn">
                <i class="fas fa-shopping-bag"></i>
                <span class="action-badge" id="cart-count-modern">{{ session('cart') ? count(session('cart')) : 0 }}</span>
                <div class="action-tooltip">Cart</div>
            </a>
            
            {{-- Track Order Button --}}
            <a href="{{ route('track.order') }}" class="action-btn track-btn">
                <i class="fas fa-truck"></i>
                <div class="action-tooltip">Track Order</div>
            </a>
            
            {{-- Mobile Menu Toggle --}}
            <button class="mobile-menu-toggle" onclick="toggleMobileModernMenu()">
                <span class="menu-line"></span>
                <span class="menu-line"></span>
                <span class="menu-line"></span>
            </button>
        </div>
    </div>
    
    {{-- Mobile Search Bar --}}
    <div class="mobile-search-bar" id="mobileSearchBar">
        <form action="{{ route('search') }}" method="GET" class="mobile-search-form">
            <input type="text" name="q" placeholder="Search..." value="{{ request('q') }}" class="mobile-search-input">
            <button type="submit" class="mobile-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</nav>

{{-- Mobile Menu Overlay --}}
<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <div class="mobile-menu-panel">
        <div class="mobile-menu-header">
            <div class="mobile-brand">
                @if($globalCompany->company_logo)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}">
                @endif
                <h2>{{ $globalCompany->company_name ?? 'Menu' }}</h2>
            </div>
            <button class="mobile-menu-close" onclick="toggleMobileModernMenu()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-menu-body">
            <div class="mobile-menu-section">
                <a href="{{ route('shop') }}" class="mobile-menu-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                
                <div class="mobile-accordion">
                    <button class="mobile-accordion-trigger" onclick="toggleMobileAccordion(event)">
                        <i class="fas fa-th-large"></i>
                        <span>Categories</span>
                        <i class="fas fa-chevron-down accordion-arrow"></i>
                    </button>
                    <div class="mobile-accordion-content">
                        @php
                            $categories = \App\Models\Category::active()->parent()->orderBy('sort_order')->get();
                        @endphp
                        @foreach($categories as $category)
                            <a href="{{ route('category', $category->slug) }}" class="mobile-category-link">
                                {{ $category->name }}
                                @if($category->products_count > 0)
                                    <span class="mobile-count">{{ $category->products_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <a href="{{ route('products') }}" class="mobile-menu-link">
                    <i class="fas fa-box"></i>
                    <span>All Products</span>
                </a>
                
                <a href="{{ route('offer.products') }}" class="mobile-menu-link">
                    <i class="fas fa-fire"></i>
                    <span>Special Offers</span>
                    <span class="mobile-badge">Hot</span>
                </a>
                
                <a href="{{ route('track.order') }}" class="mobile-menu-link">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Track Order</span>
                </a>
            </div>
            
            <div class="mobile-menu-section">
                <h3 class="mobile-section-title">My Account</h3>
                <a href="#" class="mobile-menu-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="#" class="mobile-menu-link">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Orders</span>
                </a>
                <a href="#" class="mobile-menu-link">
                    <i class="fas fa-heart"></i>
                    <span>Wishlist</span>
                </a>
                <a href="#" class="mobile-menu-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
        </div>
        
        <div class="mobile-menu-footer">
            <button class="mobile-logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
    </div>
</div>

{{-- Modern Navbar Styles --}}
<style>
/* Root Variables */
:root {
    --nav-height: 72px;
    --nav-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --nav-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    --text-primary: #2d3748;
    --text-secondary: #718096;
    --accent-color: #f56565;
    --hover-bg: rgba(255, 255, 255, 0.1);
    --border-color: rgba(255, 255, 255, 0.2);
    --dropdown-bg: #ffffff;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Modern Navbar Container */
.modern-navbar {
    background: var(--nav-bg);
    height: var(--nav-height);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: var(--nav-shadow);
    transition: var(--transition);
}

.modern-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
}

/* Brand Section */
.nav-brand-section {
    flex-shrink: 0;
}

.brand-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    transition: var(--transition);
}

.brand-wrapper:hover {
    transform: translateY(-2px);
}

.brand-logo-container,
.brand-icon-container {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.brand-logo {
    max-width: 36px;
    max-height: 36px;
    object-fit: contain;
}

.brand-icon-container i {
    font-size: 24px;
    background: var(--nav-bg);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.brand-info {
    display: flex;
    flex-direction: column;
}

.brand-name {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: white;
    letter-spacing: -0.5px;
}

.brand-tagline {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
}

/* Navigation Menu */
.nav-center-section {
    flex: 1;
    display: flex;
    justify-content: center;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-item {
    position: relative;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: white;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    border-radius: 10px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0);
    transition: var(--transition);
}

.nav-link:hover::before {
    background: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    background: rgba(255, 255, 255, 0.2);
}

.nav-icon {
    font-size: 16px;
    opacity: 0.9;
}

.nav-text {
    font-weight: 500;
}

.dropdown-arrow {
    font-size: 10px;
    margin-left: 4px;
    transition: var(--transition);
}

.nav-item.has-dropdown:hover .dropdown-arrow {
    transform: rotate(180deg);
}

.offer-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 6px;
    text-transform: uppercase;
    margin-left: 6px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Products Dropdown with Cart */
.products-dropdown {
    min-width: 700px !important;
    max-width: 900px !important;
}

.products-quick-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem;
    max-height: 450px;
    overflow-y: auto;
}

.quick-product-card {
    background: #f7fafc;
    border-radius: 12px;
    padding: 1rem;
    transition: var(--transition);
    border: 1px solid #e2e8f0;
}

.quick-product-card:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.quick-product-image {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.75rem;
    background: white;
}

.quick-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-placeholder-mini {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    color: #9ca3af;
    font-size: 2rem;
}

.quick-product-info h5 {
    margin: 0 0 0.5rem 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.3;
}

.quick-product-price {
    font-size: 16px;
    font-weight: 700;
    color: #667eea;
    margin: 0 0 0.75rem 0;
}

.quick-cart-controls {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.quick-qty-selector {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex: 1;
    background: white;
    border-radius: 8px;
    padding: 0.25rem;
    border: 1px solid #e2e8f0;
}

.quick-qty-btn {
    width: 24px;
    height: 24px;
    border: none;
    background: #f3f4f6;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 10px;
    color: #6b7280;
}

.quick-qty-btn:hover {
    background: #667eea;
    color: white;
}

.quick-qty-input {
    width: 35px;
    height: 24px;
    text-align: center;
    border: none;
    background: transparent;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
}

.quick-qty-input:focus {
    outline: none;
}

.quick-add-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.quick-add-btn:hover {
    background: linear-gradient(135deg, #764ba2, #667eea);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.quick-add-btn:active {
    transform: scale(0.95);
}

.view-cart-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
    margin-left: auto;
}

.view-cart-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.cart-items-count {
    background: white;
    color: #10b981;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
}

/* Mega Dropdown */
.mega-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    left: 50%;
    transform: translateX(-50%);
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    min-width: 600px;
    max-width: 800px;
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
    overflow: hidden;
}

.mega-dropdown.show {
    opacity: 1;
    visibility: visible;
}

.dropdown-header {
    padding: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.dropdown-header h3 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 600;
}

.dropdown-header p {
    margin: 0;
    font-size: 13px;
    opacity: 0.9;
}

.dropdown-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 4px;
    padding: 8px;
    max-height: 400px;
    overflow-y: auto;
}

.category-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    text-decoration: none;
    border-radius: 8px;
    transition: var(--transition);
}

.category-card:hover {
    background: #f7fafc;
    transform: translateX(4px);
}

.category-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.category-details h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

.product-count {
    font-size: 12px;
    color: var(--text-secondary);
}

.dropdown-footer {
    padding: 16px 24px;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
}

.view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
}

.view-all-link:hover {
    gap: 12px;
}

/* Action Buttons */
.nav-actions-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.action-btn {
    position: relative;
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.action-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-size: 10px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}

.action-tooltip {
    position: absolute;
    bottom: -32px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: var(--transition);
}

.action-btn:hover .action-tooltip {
    opacity: 1;
}

/* Search Popup */
.search-container {
    position: relative;
}

.search-popup {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    padding: 24px;
    min-width: 400px;
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
}

.search-popup.show {
    opacity: 1;
    visibility: visible;
}

.search-input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 16px;
    color: var(--text-secondary);
}

.modern-search-input {
    width: 100%;
    padding: 12px 100px 12px 44px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    transition: var(--transition);
}

.modern-search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-submit {
    position: absolute;
    right: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.search-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.search-suggestions {
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.suggestion-tag {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 600;
}

.suggestion {
    padding: 4px 12px;
    background: #f7fafc;
    border-radius: 16px;
    font-size: 12px;
    color: var(--text-primary);
    text-decoration: none;
    transition: var(--transition);
}

.suggestion:hover {
    background: #e2e8f0;
}

/* User Dropdown */
.user-menu-container {
    position: relative;
}

.user-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    min-width: 280px;
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
    overflow: hidden;
}

.user-dropdown.show {
    opacity: 1;
    visibility: visible;
}

.user-dropdown-header {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    font-size: 32px;
}

.user-dropdown-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.user-dropdown-header p {
    margin: 0;
    font-size: 12px;
    opacity: 0.9;
}

.user-dropdown-body {
    padding: 8px;
}

.user-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 8px;
    transition: var(--transition);
    font-size: 14px;
}

.user-menu-item:hover {
    background: #f7fafc;
}

.user-menu-item i {
    width: 20px;
    color: var(--text-secondary);
}

.user-dropdown-footer {
    padding: 8px;
    border-top: 1px solid #e2e8f0;
}

.logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px;
    background: #fff5f5;
    color: #e53e3e;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    transition: var(--transition);
}

.logout-btn:hover {
    background: #fed7d7;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 4px;
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    cursor: pointer;
    transition: var(--transition);
}

.menu-line {
    width: 20px;
    height: 2px;
    background: white;
    border-radius: 2px;
    transition: var(--transition);
    margin: 0 auto;
}

.mobile-menu-toggle.active .menu-line:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active .menu-line:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active .menu-line:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
}

/* Mobile Search Bar */
.mobile-search-bar {
    display: none;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.1);
    border-top: 1px solid var(--border-color);
}

.mobile-search-form {
    display: flex;
    gap: 8px;
}

.mobile-search-input {
    flex: 1;
    padding: 10px 16px;
    background: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
}

.mobile-search-btn {
    padding: 10px 20px;
    background: white;
    border: none;
    border-radius: 10px;
    color: #667eea;
    cursor: pointer;
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1001;
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
}

.mobile-menu-overlay.show {
    opacity: 1;
    visibility: visible;
}

.mobile-menu-panel {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 320px;
    background: white;
    transform: translateX(-100%);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.mobile-menu-overlay.show .mobile-menu-panel {
    transform: translateX(0);
}

.mobile-menu-header {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mobile-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.mobile-brand img {
    width: 36px;
    height: 36px;
    border-radius: 8px;
}

.mobile-brand h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.mobile-menu-close {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 18px;
    cursor: pointer;
}

.mobile-menu-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

.mobile-menu-section {
    margin-bottom: 24px;
}

.mobile-section-title {
    margin: 0 0 12px 0;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 1px;
}

.mobile-menu-link {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px;
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 10px;
    transition: var(--transition);
    margin-bottom: 4px;
}

.mobile-menu-link:hover {
    background: #f7fafc;
}

.mobile-menu-link i {
    width: 20px;
    color: var(--text-secondary);
}

.mobile-badge {
    margin-left: auto;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 6px;
}

.mobile-accordion {
    margin-bottom: 4px;
}

.mobile-accordion-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px;
    background: transparent;
    border: none;
    color: var(--text-primary);
    text-align: left;
    border-radius: 10px;
    cursor: pointer;
    transition: var(--transition);
}

.mobile-accordion-trigger:hover {
    background: #f7fafc;
}

.accordion-arrow {
    margin-left: auto;
    transition: var(--transition);
}

.mobile-accordion-trigger.active .accordion-arrow {
    transform: rotate(180deg);
}

.mobile-accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.mobile-accordion-content.show {
    max-height: 500px;
}

.mobile-category-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px 10px 48px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition);
}

.mobile-category-link:hover {
    color: var(--text-primary);
    background: #f7fafc;
}

.mobile-count {
    background: #e2e8f0;
    color: var(--text-secondary);
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 10px;
}

.mobile-menu-footer {
    padding: 16px;
    border-top: 1px solid #e2e8f0;
}

.mobile-logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: #fff5f5;
    border: none;
    border-radius: 10px;
    color: #e53e3e;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

/* Body Padding */
body {
    padding-top: var(--nav-height);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .nav-center-section {
        display: none;
    }
    
    .mobile-menu-toggle {
        display: flex;
    }
    
    .modern-container {
        gap: 16px;
    }
    
    .brand-tagline {
        display: none;
    }
}

@media (max-width: 768px) {
    .products-quick-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .products-dropdown {
        min-width: 90vw !important;
    }
    
    .modern-navbar {
        height: 64px;
    }
    
    .modern-container {
        padding: 0 16px;
    }
    
    .brand-logo-container,
    .brand-icon-container {
        width: 40px;
        height: 40px;
    }
    
    .brand-name {
        font-size: 18px;
    }
    
    .action-btn {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }
    
    .search-container {
        display: none;
    }
    
    .mobile-search-bar {
        display: block;
    }
    
    body {
        padding-top: 64px;
    }
}

@media (max-width: 480px) {
    .brand-name {
        font-size: 16px;
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .mobile-menu-panel {
        width: 280px;
    }
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Scrolled State */
.modern-navbar.scrolled {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.98) 0%, rgba(118, 75, 162, 0.98) 100%);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

/* Loading States */
.action-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.action-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    border: 2px solid white;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

{{-- Modern Navbar JavaScript --}}
<script>
// Toggle Mega Dropdown
function toggleModernDropdown(event, dropdownId) {
    event.preventDefault();
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('.mega-dropdown');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId) {
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
    
    // Close on outside click
    document.addEventListener('click', function closeDropdown(e) {
        if (!e.target.closest('.has-dropdown')) {
            dropdown.classList.remove('show');
            document.removeEventListener('click', closeDropdown);
        }
    });
}

// Toggle Search Popup
function toggleModernSearch() {
    const searchPopup = document.getElementById('searchPopup');
    const searchInput = searchPopup.querySelector('.modern-search-input');
    
    searchPopup.classList.toggle('show');
    
    if (searchPopup.classList.contains('show')) {
        setTimeout(() => searchInput.focus(), 100);
    }
    
    // Close on outside click
    document.addEventListener('click', function closeSearch(e) {
        if (!e.target.closest('.search-container')) {
            searchPopup.classList.remove('show');
            document.removeEventListener('click', closeSearch);
        }
    });
}

// Quick Add to Cart Functions
function quickIncrementQty(productId) {
    const input = document.getElementById('quick-qty-' + productId);
    const currentValue = parseInt(input.value);
    const maxValue = parseInt(input.getAttribute('max'));
    
    if (currentValue < maxValue) {
        input.value = currentValue + 1;
    }
}

function quickDecrementQty(productId) {
    const input = document.getElementById('quick-qty-' + productId);
    const currentValue = parseInt(input.value);
    const minValue = parseInt(input.getAttribute('min')) || 1;
    
    if (currentValue > minValue) {
        input.value = currentValue - 1;
    }
}

function quickAddToCart(productId) {
    const quantity = document.getElementById('quick-qty-' + productId).value;
    const button = event.currentTarget;
    
    // Add loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counts
            updateModernCartCount();
            updateDropdownCartCount();
            
            // Show success animation
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            
            // Reset quantity
            document.getElementById('quick-qty-' + productId).value = 1;
            
            // Show toast if function exists
            if (typeof showToast === 'function') {
                showToast(data.message || 'Product added to cart!', 'success');
            }
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-cart-plus"></i>';
                button.style.background = '';
                button.disabled = false;
            }, 1500);
        } else {
            button.innerHTML = '<i class="fas fa-exclamation"></i>';
            button.style.background = '#ef4444';
            
            if (typeof showToast === 'function') {
                showToast(data.message || 'Failed to add to cart', 'error');
            }
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-cart-plus"></i>';
                button.style.background = '';
                button.disabled = false;
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = '<i class="fas fa-cart-plus"></i>';
        button.disabled = false;
        
        if (typeof showToast === 'function') {
            showToast('Something went wrong!', 'error');
        }
    });
}

function updateDropdownCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('dropdown-cart-count');
            if (badge) {
                badge.textContent = data.count;
            }
        })
        .catch(error => console.error('Error updating dropdown cart count:', error));
}

// Toggle Mobile Menu
function toggleMobileModernMenu() {
    const overlay = document.getElementById('mobileMenuOverlay');
    const toggle = document.querySelector('.mobile-menu-toggle');
    const body = document.body;
    
    overlay.classList.toggle('show');
    toggle.classList.toggle('active');
    
    if (overlay.classList.contains('show')) {
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = '';
    }
}

// Toggle Mobile Accordion
function toggleMobileAccordion(event) {
    const trigger = event.currentTarget;
    const content = trigger.nextElementSibling;
    
    trigger.classList.toggle('active');
    content.classList.toggle('show');
}

// Update Cart Count
function updateModernCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('cart-count-modern');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'flex' : 'none';
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

// Navbar Scroll Effect
document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('modernNavbar');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Hide/show on scroll (optional)
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Update cart count on load
    updateModernCartCount();
    updateDropdownCartCount();
    
    // Update cart count every 30 seconds
    setInterval(() => {
        updateModernCartCount();
        updateDropdownCartCount();
    }, 30000);
    
    // Close dropdowns on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.show').forEach(el => {
                el.classList.remove('show');
            });
            document.querySelector('.mobile-menu-toggle')?.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Search suggestions
    const searchInput = document.querySelector('.modern-search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !this.value.trim()) {
                e.preventDefault();
            }
        });
    }
});

// Add to cart with animation
window.addToCartModern = function(productId, quantity = 1) {
    const cartBtn = document.querySelector('.cart-btn');
    cartBtn.classList.add('loading');
    
    // Your existing add to cart logic here
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateModernCartCount();
            // Add success animation
            cartBtn.classList.add('success');
            setTimeout(() => {
                cartBtn.classList.remove('success');
            }, 1000);
        }
    })
    .finally(() => {
        cartBtn.classList.remove('loading');
    });
};
</script>
