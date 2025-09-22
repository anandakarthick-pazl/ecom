<!-- Modern Product Card -->
@php
    // Get offer details using the new priority system
    $offerDetails = $product->getOfferDetails();
    $hasOffer = $offerDetails !== null;
    $effectivePrice = $hasOffer ? $offerDetails['discounted_price'] : $product->price;
    $discountPercentage = $hasOffer ? $offerDetails['discount_percentage'] : 0;
    $offerSource = $hasOffer ? $offerDetails['source'] : null;
@endphp

<div class="product-card {{ isset($offer) && $offer ? 'offer-card' : '' }} {{ !$product->isInStock() ? 'out-of-stock-card' : '' }}">
    <div class="product-image-container">
        @if($product->featured_image)
            <img src="{{ $product->featured_image_url }}" class="product-image" alt="{{ $product->name }}">
        @else
            <div class="product-placeholder">
                <i class="fas fa-image"></i>
            </div>
        @endif
        
        @if(!$product->isInStock())
            <div class="product-badge stock-badge">
                <span class="badge-out-of-stock">Out of Stock</span>
            </div>
        @elseif($hasOffer && $discountPercentage > 0)
            <div class="product-badge">
                <span class="badge-discount">{{ round($discountPercentage) }}% OFF</span>
            </div>
            {{-- Show offer source indicator --}}
            @if($offerSource === 'offers_page')
                {{-- <div class="product-badge-special">
                    <span class="badge-special-offer">
                        <i class="fas fa-fire"></i> {{ $offerDetails['offer_name'] }}
                    </span>
                </div> --}}
            @elseif($offerSource === 'product_onboarding')
                <div class="product-badge-product">
                    <span class="badge-product-discount">
                        <i class="fas fa-tag"></i> Product Discount
                    </span>
                </div>
            @endif
        @endif
        
        @if(isset($featured) && $featured)
            <div class="badge-featured">
                <i class="fas fa-star"></i>
            </div>
        @endif
        
        <div class="product-overlay">
            <div class="quick-actions">
                <a href="{{ route('product', $product->slug) }}" class="quick-btn" title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                @if($product->isInStock())
                    <button onclick="addToCart({{ $product->id }})" class="quick-btn" title="Add to Cart">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                @endif
            </div>
        </div>
        
        @if(!$product->isInStock())
            <div class="stock-overlay">
                <div class="stock-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Currently Unavailable</span>
                </div>
            </div>
        @endif
    </div>
    
    <div class="product-content">
        {{-- <div class="product-category"><a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a></div> --}}
        <div class="product-category">{{ $product->name }}</div>
        <h3 class="product-title">
            {{ $product->category->name }}
        </h3>
        <p class="product-description">{{ Str::limit($product->short_description ?? '', 60) }}</p>
        
        {{-- Show offer details with priority information --}}
        @if($hasOffer && $offerDetails)
            <div class="offer-info">
                @if($offerSource === 'offers_page')
                    <i class="fas fa-fire text-danger"></i> 
                    <small class="text-success fw-bold">{{ $offerDetails['offer_name'] }}</small>
                    <span class="badge bg-success ms-1">
                        {{ round($offerDetails['discount_percentage']) }}% OFF
                    </span>
                    <br><small class="text-muted">🎯 Special Offer</small>
                @elseif($offerSource === 'product_onboarding')
                    <i class="fas fa-tag text-info"></i> 
                    <small class="text-info fw-bold">Product Discount</small>
                    <span class="badge bg-info ms-1">
                        {{ round($offerDetails['discount_percentage']) }}% OFF
                    </span>
                    <br><small class="text-muted">💰 Regular Discount</small>
                @endif
            </div>
        @endif
        
        <div class="product-footer">
            <div class="price-section">
                @if($hasOffer)
                    <span class="current-price">₹{{ number_format($effectivePrice, 2) }}</span>
                    <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                    <div class="savings-info">
                        <small class="text-success">
                            <i class="fas fa-tags"></i> You save ₹{{ number_format($offerDetails['savings'], 2) }}
                            @if($offerSource === 'offers_page')
                                <span class="badge badge-sm bg-success ms-1">Special Offer</span>
                            @elseif($offerSource === 'product_onboarding')
                                <span class="badge badge-sm bg-info ms-1">Product Discount</span>
                            @endif
                        </small>
                    </div>
                @else
                    <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            <div class="product-actions">
                @if($product->isInStock())
                    <div class="quantity-selector">
                        <button class="qty-btn" onclick="decrementQuantity({{ $product->id }})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="qty-input" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                        <button class="qty-btn" onclick="incrementQuantity({{ $product->id }})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn-add-cart {{ ($hasOffer || (isset($offer) && $offer)) ? 'offer' : '' }}">
                        <i class="fas fa-cart-plus"></i>
                        <span>Add</span>
                    </button>
                @else
                    <div class="out-of-stock-section">
                        <button class="btn-out-stock" disabled>
                            <i class="fas fa-ban"></i>
                            <span>Out of Stock</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Product Card Styles - Ensure these work if not already defined in parent */
.product-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.offer-card {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box, 
                linear-gradient(45deg, rgba(255, 107, 107, 0.2), rgba(239, 68, 68, 0.2)) border-box;
}

.offer-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
}

.product-image-container {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 2rem;
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

.product-badge-special {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
}

.badge-discount {
    background: linear-gradient(45deg, #ef4444, #dc2626);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: pulseOffer 2s ease-in-out infinite;
}

.badge-special-offer {
    background: linear-gradient(45deg, #10b981, #059669);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    animation: pulseOffer 3s ease-in-out infinite;
}

@keyframes pulseOffer {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.badge-featured {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.9);
    color: #f59e0b;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    z-index: 2;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.quick-actions {
    display: flex;
    gap: 0.75rem;
}

.quick-btn {
    background: #ffffff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.quick-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    transform: scale(1.1);
}

.product-content {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 0.75rem;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.product-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.product-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.4;
    margin-bottom: 0.5rem;
    flex: 1;
}

.offer-info {
    padding: 0.5rem;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid rgba(34, 197, 94, 0.2);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.product-footer {
    margin-top: auto;
}

.price-section {
    margin-bottom: 1rem;
}

.current-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.original-price {
    font-size: 0.875rem;
    text-decoration: line-through;
    color: #6b7280;
    margin-left: 0.5rem;
}

.savings-info {
    margin-top: 0.25rem;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.qty-btn {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1f2937;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.qty-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.qty-input {
    width: 50px;
    height: 32px;
    text-align: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 500;
}

.btn-add-cart {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-add-cart:hover {
    background: {{ $globalCompany->secondary_color ?? '#10b981' }};
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.btn-add-cart.offer {
    background: linear-gradient(45deg, #ef4444, #dc2626);
    animation: pulseButton 3s ease-in-out infinite;
}

.btn-add-cart.offer:hover {
    background: linear-gradient(45deg, #dc2626, #b91c1c);
    animation: none;
}

@keyframes pulseButton {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
}

.btn-out-stock {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0.7;
    cursor: not-allowed;
    width: 100%;
}

/* Out of Stock Card Styles */
.out-of-stock-card {
    position: relative;
    opacity: 0.8;
}

.out-of-stock-card .product-image {
    filter: grayscale(30%);
}

.stock-badge {
    top: 12px;
    left: 12px;
    z-index: 3;
}

.badge-out-of-stock {
    background: linear-gradient(45deg, #f59e0b, #d97706);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

.stock-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    color: white;
    padding: 1rem;
    z-index: 2;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.out-of-stock-card:hover .stock-overlay {
    opacity: 1;
}

.stock-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.out-of-stock-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}



@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
</style>

<script>
// Quantity controls
function incrementQuantity(productId) {
    const input = document.getElementById('quantity-' + productId);
    const max = parseInt(input.getAttribute('max'));
    let value = parseInt(input.value);
    if (value < max) {
        input.value = value + 1;
    }
}

function decrementQuantity(productId) {
    const input = document.getElementById('quantity-' + productId);
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function addToCartWithQuantity(productId) {
    const quantity = parseInt(document.getElementById('quantity-' + productId).value);
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to cart!', 'success');
            // Update cart count if function exists
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        } else {
            showToast(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add to cart', 'error');
    });
}

function addToCart(productId) {
    addToCartWithQuantity(productId);
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> 
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}


</script>
