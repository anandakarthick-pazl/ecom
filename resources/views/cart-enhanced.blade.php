@extends('layouts.app')

@section('title', 'Shopping Cart - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Page Header -->
<div class="page-header-enhanced">
    <div class="container">
        <div class="header-content text-center">
            <h1 class="display-4 mb-3 fw-bold animate-fade-in">
                <i class="fas fa-shopping-cart me-3"></i>
                Shopping Cart
            </h1>
            <p class="lead animate-slide-up">Review your items and proceed to checkout</p>
            
            @if($cartItems->count() > 0)
            <div class="cart-stats animate-bounce-in">
                <span class="stat-badge">
                    <i class="fas fa-box me-2"></i>{{ $cartItems->sum('quantity') }} Items
                </span>
                <span class="stat-badge ms-3">
                    <i class="fas fa-rupee-sign me-2"></i>₹{{ number_format($subtotal, 2) }} Subtotal
                </span>
                @if($subtotal >= 500)
                    <span class="stat-badge ms-3 success-badge">
                        <i class="fas fa-shipping-fast me-2"></i>Free Shipping!
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<div class="container cart-page-container">
    @if($cartItems->count() > 0)
    <div class="row g-4">
        <!-- Enhanced Cart Items -->
        <div class="col-lg-8">
            <div class="cart-items-section animate-slide-up">
                <div class="section-header">
                    <h5 class="section-title">
                        <i class="fas fa-list me-2"></i>Cart Items ({{ $cartItems->count() }})
                    </h5>
                    <div class="section-actions">
                        <button class="btn btn-outline-secondary btn-sm" onclick="selectAllItems()">
                            <i class="fas fa-check-square me-1"></i>Select All
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="clearCartEnhanced()">
                            <i class="fas fa-trash me-1"></i>Clear Cart
                        </button>
                    </div>
                </div>
                
                <div class="cart-items-container">
                    @foreach($cartItems as $item)
                    <div class="cart-item-enhanced animate-fade-in animate-stagger-{{ $loop->iteration }}" 
                         data-product-id="{{ $item->product_id }}">
                        <div class="item-selector">
                            <input type="checkbox" class="form-check-input item-checkbox" 
                                   id="item-{{ $item->product_id }}" 
                                   data-product-id="{{ $item->product_id }}" 
                                   checked>
                        </div>
                        
                        <div class="item-image">
                            @if($item->product->featured_image)
                                <img src="{{ Storage::url($item->product->featured_image) }}" 
                                     alt="{{ $item->product->name }}"
                                     onclick="openProductModal({{ $item->product->id }})">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            
                            @if($item->product->discount_percentage > 0)
                                <div class="item-badge discount-badge">
                                    {{ $item->product->discount_percentage }}% OFF
                                </div>
                            @endif
                        </div>
                        
                        <div class="item-details">
                            <div class="item-header">
                                <h6 class="item-name">{{ $item->product->name }}</h6>
                                <button class="btn btn-outline-danger btn-sm remove-btn" 
                                        onclick="removeFromCartEnhanced({{ $item->product_id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <div class="item-meta">
                                <span class="item-category">
                                    <i class="fas fa-tag me-1"></i>{{ $item->product->category->name }}
                                </span>
                                @if($item->product->weight)
                                    <span class="item-weight">
                                        <i class="fas fa-weight-hanging me-1"></i>{{ $item->product->weight }} {{ $item->product->weight_unit }}
                                    </span>
                                @endif
                                @if($item->product->sku)
                                    <span class="item-sku">
                                        <i class="fas fa-barcode me-1"></i>{{ $item->product->sku }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="item-price-section">
                                <div class="price-display">
                                    <span class="current-price">₹{{ number_format($item->price, 2) }}</span>
                                    @if($item->product->price > $item->price)
                                        <span class="original-price">₹{{ number_format($item->product->price, 2) }}</span>
                                    @endif
                                </div>
                                
                                @if($item->product->tax_percentage > 0)
                                    <div class="tax-info">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Tax: {{ $item->product->tax_percentage }}% included
                                            (CGST: ₹{{ number_format($item->product->getCgstAmount($item->price) * $item->quantity, 2) }} + 
                                            SGST: ₹{{ number_format($item->product->getSgstAmount($item->price) * $item->quantity, 2) }})
                                        </small>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="item-actions">
                                <div class="quantity-section">
                                    <label class="quantity-label">Qty:</label>
                                    <div class="quantity-selector-enhanced">
                                        <button type="button" 
                                                class="quantity-btn-enhanced" 
                                                onclick="updateQuantityEnhanced({{ $item->product_id }}, {{ $item->quantity - 1 }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" 
                                               class="quantity-input-enhanced" 
                                               value="{{ $item->quantity }}" 
                                               min="1" 
                                               max="{{ $item->product->stock }}"
                                               data-product-id="{{ $item->product_id }}"
                                               onchange="updateQuantityEnhanced({{ $item->product_id }}, this.value)">
                                        <button type="button" 
                                                class="quantity-btn-enhanced" 
                                                onclick="updateQuantityEnhanced({{ $item->product_id }}, {{ $item->quantity + 1 }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <small class="stock-info">
                                        @if($item->product->stock <= 5)
                                            <span class="text-warning">Only {{ $item->product->stock }} left!</span>
                                        @else
                                            <span class="text-muted">{{ $item->product->stock }} available</span>
                                        @endif
                                    </small>
                                </div>
                                
                                <div class="item-total-display">
                                    <span class="total-label">Total:</span>
                                    <span class="total-amount">₹<span class="item-total">{{ number_format($item->total, 2) }}</span></span>
                                </div>
                            </div>
                            
                            <div class="item-footer">
                                <div class="item-features">
                                    @if($item->product->is_featured)
                                        <span class="feature-tag">
                                            <i class="fas fa-star me-1"></i>Featured
                                        </span>
                                    @endif
                                    <span class="delivery-tag">
                                        <i class="fas fa-truck me-1"></i>2-5 days delivery
                                    </span>
                                </div>
                                
                                <div class="item-quick-actions">
                                    <button class="btn btn-link btn-sm" onclick="saveForLater({{ $item->product_id }})">
                                        <i class="fas fa-heart me-1"></i>Save for Later
                                    </button>
                                    <a href="{{ route('product', $item->product->slug) }}" 
                                       class="btn btn-link btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Cart Actions Bar -->
                <div class="cart-actions-bar">
                    <div class="selected-items-info">
                        <span id="selected-count">{{ $cartItems->count() }}</span> items selected
                    </div>
                    <div class="cart-navigation">
                        <a href="{{ route('products') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                        <button class="btn btn-success" onclick="proceedToCheckout()">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Order Summary -->
        <div class="col-lg-4">
            <div class="order-summary-enhanced animate-scale-in">
                <div class="summary-header">
                    <h5 class="summary-title">
                        <i class="fas fa-calculator me-2"></i>Order Summary
                    </h5>
                    <div class="summary-actions">
                        <button class="btn btn-link btn-sm" onclick="toggleSummaryDetails()">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
                
                <div class="summary-content">
                    <!-- Summary Items -->
                    <div class="summary-section">
                        <div class="summary-row">
                            <span class="summary-label">
                                <i class="fas fa-box me-2"></i>Subtotal:
                            </span>
                            <span class="summary-value" id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @php
                            // Calculate tax amounts
                            $totalTax = 0;
                            $cgstAmount = 0;
                            $sgstAmount = 0;
                            
                            foreach($cartItems as $item) {
                                $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                                $totalTax += $itemTax;
                                $cgstAmount += ($itemTax / 2);
                                $sgstAmount += ($itemTax / 2);
                            }
                            
                            $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                            $grandTotal = $subtotal + $totalTax + $deliveryCharge;
                        @endphp
                        
                        <div class="summary-row tax-row">
                            <span class="summary-label">
                                <i class="fas fa-receipt me-2"></i>CGST:
                            </span>
                            <span class="summary-value" id="cgst-amount">₹{{ number_format($cgstAmount, 2) }}</span>
                        </div>
                        
                        <div class="summary-row tax-row">
                            <span class="summary-label">
                                <i class="fas fa-receipt me-2"></i>SGST:
                            </span>
                            <span class="summary-value" id="sgst-amount">₹{{ number_format($sgstAmount, 2) }}</span>
                        </div>
                        
                        <div class="summary-row">
                            <span class="summary-label">
                                <i class="fas fa-percentage me-2"></i>Total Tax:
                            </span>
                            <span class="summary-value" id="total-tax">₹{{ number_format($totalTax, 2) }}</span>
                        </div>
                        
                        <div class="summary-row delivery-row">
                            <span class="summary-label">
                                <i class="fas fa-shipping-fast me-2"></i>Delivery:
                            </span>
                            <span class="summary-value" id="delivery-charge">
                                @if($subtotal >= 500)
                                    <span class="text-success fw-bold">FREE</span>
                                @else
                                    ₹50.00
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <!-- Free Shipping Progress -->
                    @if($subtotal < 500)
                    <div class="free-shipping-progress">
                        <div class="progress-header">
                            <small class="text-muted">
                                <i class="fas fa-truck me-1"></i>
                                Add ₹{{ number_format(500 - $subtotal, 2) }} more for FREE delivery!
                            </small>
                        </div>
                        <div class="progress progress-enhanced">
                            <div class="progress-bar" 
                                 style="width: {{ ($subtotal / 500) * 100 }}%"
                                 role="progressbar">
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Total Section -->
                    <div class="summary-total">
                        <div class="total-row">
                            <span class="total-label">Grand Total:</span>
                            <span class="total-value" id="cart-total">₹{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <div class="savings-info">
                            @php
                                $totalSavings = 0;
                                foreach($cartItems as $item) {
                                    if($item->product->price > $item->price) {
                                        $totalSavings += ($item->product->price - $item->price) * $item->quantity;
                                    }
                                }
                            @endphp
                            @if($totalSavings > 0)
                                <small class="text-success">
                                    <i class="fas fa-tag me-1"></i>
                                    You're saving ₹{{ number_format($totalSavings, 2) }}!
                                </small>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <div class="checkout-section">
                        <button class="btn-checkout-enhanced" onclick="proceedToCheckoutEnhanced()">
                            <i class="fas fa-lock me-2"></i>
                            <span>Secure Checkout</span>
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        
                        <div class="checkout-security">
                            <div class="security-badges">
                                <span class="security-badge">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <small>Secure</small>
                                </span>
                                <span class="security-badge">
                                    <i class="fas fa-lock text-primary"></i>
                                    <small>Encrypted</small>
                                </span>
                                <span class="security-badge">
                                    <i class="fas fa-award text-warning"></i>
                                    <small>Trusted</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <h6 class="payment-title">We Accept:</h6>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-paypal"></i>
                            <i class="fas fa-university"></i>
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recommended Products -->
            <div class="recommended-section animate-bounce-in">
                <h6 class="recommended-title">
                    <i class="fas fa-lightbulb me-2"></i>You might also like
                </h6>
                <div class="recommended-items">
                    <!-- This would be populated with recommended products -->
                    <div class="recommended-placeholder">
                        <i class="fas fa-star fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Personalized recommendations coming soon!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Enhanced Empty Cart -->
    <div class="empty-cart-enhanced animate-bounce-in">
        <div class="empty-cart-content">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="empty-cart-title">Your cart is empty</h3>
            <p class="empty-cart-description">
                Looks like you haven't added any items to your cart yet. 
                Start shopping to fill it up with amazing products!
            </p>
            <div class="empty-cart-actions">
                <a href="{{ route('products') }}" 
                   class="btn btn-primary-enhanced btn-lg"
                   onclick="triggerFireworks(this)">
                    <i class="fas fa-leaf me-2"></i>Start Shopping
                </a>
                <a href="{{ route('offer.products') }}" 
                   class="btn btn-outline-enhanced btn-lg">
                    <i class="fas fa-tags me-2"></i>View Offers
                </a>
            </div>
            
            <div class="empty-cart-suggestions">
                <h6>Popular Categories:</h6>
                <div class="suggestion-tags">
                    @php
                        $popularCategories = \App\Models\Category::active()->parent()->limit(4)->get();
                    @endphp
                    @foreach($popularCategories as $category)
                        <a href="{{ route('category', $category->slug) }}" class="suggestion-tag">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    /* Cart Stats */
    .cart-stats {
        margin-top: 30px;
    }
    
    .stat-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 10px 18px;
        border-radius: 25px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: inline-block;
        margin: 5px;
    }
    
    .stat-badge.success-badge {
        background: rgba(40, 167, 69, 0.9);
        animation: pulse 2s infinite;
    }
    
    /* Cart Page Container */
    .cart-page-container {
        background: white;
        border-radius: 25px 25px 0 0;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        padding: 60px 15px 40px 15px;
        position: relative;
        z-index: 2;
        margin-top: -40px;
    }
    
    /* Cart Items Section */
    .cart-items-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }
    
    .section-header {
        background: #f8f9fa;
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .section-title {
        margin: 0;
        font-weight: 700;
        color: #333;
    }
    
    .section-actions {
        display: flex;
        gap: 10px;
    }
    
    .cart-items-container {
        padding: 0;
    }
    
    /* Enhanced Cart Item */
    .cart-item-enhanced {
        display: flex;
        align-items: flex-start;
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .cart-item-enhanced:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    
    .cart-item-enhanced:last-child {
        border-bottom: none;
    }
    
    .item-selector {
        margin-right: 15px;
        margin-top: 5px;
    }
    
    .item-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid #ddd;
    }
    
    .item-checkbox:checked {
        background: var(--primary-gradient);
        border-color: var(--primary-color);
    }
    
    .item-image {
        width: 120px;
        height: 120px;
        margin-right: 20px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
        cursor: pointer;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .item-image:hover img {
        transform: scale(1.1);
    }
    
    .image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 2rem;
    }
    
    .item-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        padding: 4px 8px;
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .item-badge.discount-badge {
        background: rgba(220, 53, 69, 0.9);
        color: white;
    }
    
    .item-details {
        flex-grow: 1;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    
    .item-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin: 0;
        line-height: 1.3;
    }
    
    .remove-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 2px solid #dc3545;
        color: #dc3545;
        background: white;
        transition: all 0.3s ease;
    }
    
    .remove-btn:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.1);
    }
    
    .item-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .item-meta span {
        font-size: 12px;
        color: #666;
        font-weight: 500;
    }
    
    .item-price-section {
        margin-bottom: 15px;
    }
    
    .price-display {
        margin-bottom: 5px;
    }
    
    .current-price {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-right: 10px;
    }
    
    .original-price {
        font-size: 1rem;
        color: #999;
        text-decoration: line-through;
    }
    
    .tax-info {
        margin-top: 5px;
    }
    
    .item-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .quantity-section {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quantity-label {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stock-info {
        margin-top: 2px;
        display: block;
    }
    
    .item-total-display {
        text-align: right;
    }
    
    .total-label {
        font-size: 12px;
        color: #666;
        display: block;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .total-amount {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    
    .item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .item-features {
        display: flex;
        gap: 10px;
    }
    
    .feature-tag, .delivery-tag {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .feature-tag {
        background: rgba(255, 193, 7, 0.2);
        color: #856404;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    .delivery-tag {
        background: rgba(40, 167, 69, 0.2);
        color: #155724;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }
    
    .item-quick-actions {
        display: flex;
        gap: 10px;
    }
    
    .item-quick-actions .btn-link {
        font-size: 12px;
        color: #666;
        text-decoration: none;
        padding: 2px 0;
    }
    
    .item-quick-actions .btn-link:hover {
        color: var(--primary-color);
    }
    
    /* Cart Actions Bar */
    .cart-actions-bar {
        background: #f8f9fa;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f0f0f0;
    }
    
    .selected-items-info {
        font-weight: 600;
        color: #333;
    }
    
    .cart-navigation {
        display: flex;
        gap: 15px;
    }
    
    /* Enhanced Order Summary */
    .order-summary-enhanced {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        overflow: hidden;
        position: sticky;
        top: 100px;
    }
    
    .summary-header {
        background: var(--primary-gradient);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .summary-title {
        margin: 0;
        font-weight: 700;
    }
    
    .summary-content {
        padding: 25px;
    }
    
    .summary-section {
        margin-bottom: 20px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .summary-row:last-child {
        border-bottom: none;
    }
    
    .summary-label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }
    
    .summary-value {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    
    .tax-row .summary-label,
    .tax-row .summary-value {
        font-size: 12px;
        color: #888;
    }
    
    .delivery-row {
        background: #f8f9fa;
        margin: 0 -10px;
        padding: 12px 10px 8px 10px;
        border-radius: 8px;
    }
    
    /* Free Shipping Progress */
    .free-shipping-progress {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-radius: 15px;
        padding: 15px;
        margin: 20px 0;
        border: 1px solid #ffeaa7;
    }
    
    .progress-enhanced {
        height: 8px;
        border-radius: 4px;
        background: rgba(255,255,255,0.5);
        margin-top: 8px;
    }
    
    .progress-enhanced .progress-bar {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 4px;
    }
    
    /* Summary Total */
    .summary-total {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 20px;
        margin: 20px 0;
        border: 2px solid #f0f0f0;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .total-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
    }
    
    .total-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
    }
    
    .savings-info {
        text-align: center;
    }
    
    /* Checkout Section */
    .checkout-section {
        text-align: center;
    }
    
    .btn-checkout-enhanced {
        width: 100%;
        padding: 15px 20px;
        border-radius: 15px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
        margin-bottom: 15px;
    }
    
    .btn-checkout-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--primary-color), 0.4);
    }
    
    .checkout-security {
        margin-bottom: 20px;
    }
    
    .security-badges {
        display: flex;
        justify-content: space-around;
    }
    
    .security-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 10px;
        color: #666;
    }
    
    .security-badge i {
        font-size: 1.2rem;
        margin-bottom: 4px;
    }
    
    /* Payment Methods */
    .payment-methods {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
    
    .payment-title {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-icons {
        display: flex;
        justify-content: space-around;
        font-size: 1.5rem;
        color: #666;
    }
    
    /* Recommended Section */
    .recommended-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #f0f0f0;
    }
    
    .recommended-title {
        color: #333;
        font-weight: 700;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .recommended-placeholder {
        text-align: center;
        padding: 20px;
        color: #666;
    }
    
    /* Enhanced Empty Cart */
    .empty-cart-enhanced {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 25px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        margin: 40px 0;
        border: 1px solid #f0f0f0;
    }
    
    .empty-cart-icon {
        font-size: 6rem;
        color: #ddd;
        margin-bottom: 30px;
    }
    
    .empty-cart-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }
    
    .empty-cart-description {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    
    .empty-cart-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .empty-cart-suggestions {
        margin-top: 40px;
    }
    
    .empty-cart-suggestions h6 {
        color: #666;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .suggestion-tags {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .suggestion-tag {
        padding: 8px 16px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .suggestion-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
        color: white;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .cart-page-container {
            padding: 40px 15px 20px 15px;
        }
        
        .cart-item-enhanced {
            flex-direction: column;
            padding: 20px;
        }
        
        .item-image {
            width: 100%;
            height: 200px;
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .item-details {
            min-height: auto;
        }
        
        .item-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .item-actions {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .item-total-display {
            text-align: left;
        }
        
        .item-footer {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .cart-actions-bar {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .cart-navigation {
            flex-direction: column;
            width: 100%;
        }
        
        .order-summary-enhanced {
            position: static;
            margin-top: 30px;
        }
        
        .empty-cart-title {
            font-size: 2rem;
        }
        
        .empty-cart-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .suggestion-tags {
            justify-content: center;
        }
        
        .stat-badge {
            display: block;
            margin: 5px auto;
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .section-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .section-actions {
            flex-direction: column;
        }
        
        .item-meta {
            flex-direction: column;
            gap: 5px;
        }
        
        .payment-icons {
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .empty-cart-title {
            font-size: 1.75rem;
        }
        
        .empty-cart-description {
            font-size: 1rem;
        }
    }
</style>

<script>
// Enhanced Cart Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Trigger welcome fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        setTimeout(() => {
            window.enhancedFireworks.triggerWelcomeFireworks();
        }, 1000);
    }
    
    // Initialize cart functionality
    initializeCartEnhanced();
    
    console.log('🛒 Enhanced Cart Page initialized successfully!');
});

// Enhanced quantity update function
function updateQuantityEnhanced(productId, quantity) {
    if (quantity < 1) {
        removeFromCartEnhanced(productId);
        return;
    }
    
    // Show loading state
    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.6';
        cartItem.style.pointerEvents = 'none';
    }
    
    // Make AJAX request (assuming jQuery is available)
    if (typeof $ !== 'undefined') {
        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'PUT',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    // Trigger fireworks for successful update
                    if (typeof window.enhancedFireworks !== 'undefined') {
                        window.enhancedFireworks.triggerOnAction(cartItem);
                    }
                    
                    // Show success notification
                    if (typeof window.showEnhancedNotification === 'function') {
                        window.showEnhancedNotification('Cart updated successfully!', 'success', 2000);
                    }
                    
                    // Reload page to update totals
                    location.reload();
                } else {
                    if (typeof window.showEnhancedNotification === 'function') {
                        window.showEnhancedNotification(response.message, 'error', 3000);
                    }
                    
                    // Restore state
                    if (cartItem) {
                        cartItem.style.opacity = '1';
                        cartItem.style.pointerEvents = 'auto';
                    }
                }
            },
            error: function() {
                if (typeof window.showEnhancedNotification === 'function') {
                    window.showEnhancedNotification('Something went wrong!', 'error', 3000);
                }
                
                // Restore state
                if (cartItem) {
                    cartItem.style.opacity = '1';
                    cartItem.style.pointerEvents = 'auto';
                }
            }
        });
    } else {
        // Fallback without jQuery
        console.error('jQuery not available for cart update');
        location.reload();
    }
}

// Enhanced remove from cart function
function removeFromCartEnhanced(productId) {
    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    const productName = cartItem ? cartItem.querySelector('.item-name').textContent : 'item';
    
    // Show confirmation dialog
    if (!confirm(`Are you sure you want to remove "${productName}" from your cart?`)) {
        return;
    }
    
    // Add removal animation
    if (cartItem) {
        cartItem.style.transform = 'translateX(-100%)';
        cartItem.style.opacity = '0';
    }
    
    // Make AJAX request
    if (typeof $ !== 'undefined') {
        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    // Trigger fireworks
                    if (typeof window.enhancedFireworks !== 'undefined') {
                        window.enhancedFireworks.triggerOnAction(cartItem);
                    }
                    
                    // Show success notification
                    if (typeof window.showEnhancedNotification === 'function') {
                        window.showEnhancedNotification(response.message, 'success', 3000);
                    }
                    
                    // Update cart count
                    if (typeof window.updateCartCount === 'function') {
                        window.updateCartCount();
                    }
                    
                    // Reload page
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    if (typeof window.showEnhancedNotification === 'function') {
                        window.showEnhancedNotification(response.message, 'error', 3000);
                    }
                    
                    // Restore item
                    if (cartItem) {
                        cartItem.style.transform = 'translateX(0)';
                        cartItem.style.opacity = '1';
                    }
                }
            },
            error: function() {
                if (typeof window.showEnhancedNotification === 'function') {
                    window.showEnhancedNotification('Something went wrong!', 'error', 3000);
                }
                
                // Restore item
                if (cartItem) {
                    cartItem.style.transform = 'translateX(0)';
                    cartItem.style.opacity = '1';
                }
            }
        });
    } else {
        // Fallback without jQuery
        console.error('jQuery not available for cart removal');
        location.reload();
    }
}

// Enhanced clear cart function
function clearCartEnhanced() {
    if (!confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
        return;
    }
    
    // Add clearing animation
    const cartItems = document.querySelectorAll('.cart-item-enhanced');
    cartItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.transform = 'translateX(-100%)';
            item.style.opacity = '0';
        }, index * 100);
    });
    
    // Make AJAX request
    if (typeof $ !== 'undefined') {
        $.ajax({
            url: '{{ route("cart.clear") }}',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    // Trigger celebration
                    if (typeof window.enhancedFireworks !== 'undefined') {
                        window.enhancedFireworks.createCelebrationBurst();
                    }
                    
                    if (typeof window.showEnhancedNotification === 'function') {
                        window.showEnhancedNotification(response.message, 'success', 3000);
                    }
                    
                    // Update cart count
                    if (typeof window.updateCartCount === 'function') {
                        window.updateCartCount();
                    }
                    
                    // Reload page
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            },
            error: function() {
                if (typeof window.showEnhancedNotification === 'function') {
                    window.showEnhancedNotification('Something went wrong!', 'error', 3000);
                }
                location.reload();
            }
        });
    } else {
        location.reload();
    }
}

// Select all items function
function selectAllItems() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    
    updateSelectedCount();
    
    // Trigger fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.startRandomFireworks();
    }
}

// Update selected count
function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    const selectedCount = selectedCheckboxes.length;
    
    const countElement = document.getElementById('selected-count');
    if (countElement) {
        countElement.textContent = selectedCount;
    }
}

// Proceed to checkout function
function proceedToCheckoutEnhanced() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    
    if (selectedItems.length === 0) {
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Please select at least one item to checkout', 'warning', 3000);
        }
        return;
    }
    
    // Trigger celebration
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.createCelebrationBurst();
    }
    
    // Show loading notification
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Proceeding to secure checkout...', 'info', 2000);
    }
    
    // Redirect to checkout
    setTimeout(() => {
        window.location.href = '{{ route("checkout") }}';
    }, 1000);
}

// Alias for backward compatibility
function proceedToCheckout() {
    proceedToCheckoutEnhanced();
}

// Save for later function
function saveForLater(productId) {
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Save for later feature coming soon!', 'info', 3000);
    }
    
    // Here you would implement the save for later functionality
}

// Open product modal function
function openProductModal(productId) {
    // This would open a quick view modal of the product
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Quick view coming soon!', 'info', 2000);
    }
}

// Toggle summary details
function toggleSummaryDetails() {
    const taxRows = document.querySelectorAll('.tax-row');
    taxRows.forEach(row => {
        row.style.display = row.style.display === 'none' ? 'flex' : 'none';
    });
}

// Initialize cart functionality
function initializeCartEnhanced() {
    // Add change event listeners to quantity inputs
    document.querySelectorAll('.quantity-input-enhanced').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const quantity = parseInt(this.value);
            updateQuantityEnhanced(productId, quantity);
        });
    });
    
    // Add change event listeners to checkboxes
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Initialize selected count
    updateSelectedCount();
}

// Global trigger fireworks function
function triggerFireworks(element) {
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(element);
    }
}
</script>
@endsection
