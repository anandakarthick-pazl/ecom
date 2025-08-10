{{-- Enhanced Product Card Component --}}
@props([
    'product',
    'showQuantitySelector' => true,
    'showDescription' => true,
    'cardClass' => '',
    'animationDelay' => 0
])

@php
    // Get offer details using the new priority system
    $offerDetails = $product->getOfferDetails();
    $hasOffer = $offerDetails !== null;
    $effectivePrice = $hasOffer ? $offerDetails['discounted_price'] : $product->price;
    $discountPercentage = $hasOffer ? $offerDetails['discount_percentage'] : 0;
    $offerSource = $hasOffer ? $offerDetails['source'] : null;
@endphp

<div class="product-card-enhanced {{ $cardClass }} animate-fade-in animate-stagger-{{ min($animationDelay, 12) }}" 
     data-product-id="{{ $product->id }}">
     
    <div class="product-image-enhanced">
        @if($product->featured_image)
            <img src="{{ Storage::url($product->featured_image) }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy">
        @else
            <div class="product-image-placeholder bg-light d-flex align-items-center justify-content-center h-100">
                <i class="fas fa-image fa-3x text-muted"></i>
            </div>
        @endif
        
        <div class="product-badges-enhanced">
            <div>
                @if($hasOffer && $discountPercentage > 0)
                    <span class="badge badge-enhanced bg-danger">{{ round($discountPercentage) }}% OFF</span>
                    @if($offerSource === 'offers_page')
                        <span class="badge badge-enhanced bg-success ms-1">
                            <i class="fas fa-fire"></i> Special
                        </span>
                    @elseif($offerSource === 'product_onboarding')
                        <span class="badge badge-enhanced bg-info ms-1">
                            <i class="fas fa-tag"></i> Product
                        </span>
                    @endif
                @endif
                @if($product->is_featured)
                    <span class="badge badge-enhanced bg-warning text-dark ms-1">
                        <i class="fas fa-star"></i> Featured
                    </span>
                @endif
            </div>
            <div>
                @if($product->stock <= 5 && $product->stock > 0)
                    <span class="badge badge-enhanced bg-warning text-dark">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock
                    </span>
                @elseif($product->stock == 0)
                    <span class="badge badge-enhanced bg-secondary">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Quick Action Overlay -->
        <div class="product-overlay-actions">
            <a href="{{ route('product', $product->slug) }}" 
               class="btn btn-outline-enhanced btn-sm mb-2">
                <i class="fas fa-eye"></i> Quick View
            </a>
        </div>
    </div>
    
    <div class="card-body-enhanced">
        <div class="product-category-enhanced">{{ $product->category->name }}</div>
        
        <h6 class="product-title-enhanced">{{ $product->name }}</h6>
        
        @if($showDescription && $product->short_description)
            <p class="product-description-enhanced">{{ Str::limit($product->short_description, 80) }}</p>
        @endif
        
        <div class="price-section-enhanced">
            @if($hasOffer)
                <span class="price-current-enhanced">₹{{ number_format($effectivePrice, 2) }}</span>
                <span class="price-original-enhanced">₹{{ number_format($product->price, 2) }}</span>
                <div class="savings-enhanced">
                    <i class="fas fa-tag"></i> You Save: ₹{{ number_format($offerDetails['savings'], 2) }}
                    @if($offerSource === 'offers_page')
                        <small class="text-success">(Special Offer)</small>
                    @elseif($offerSource === 'product_onboarding')
                        <small class="text-info">(Product Discount)</small>
                    @endif
                </div>
            @else
                <span class="price-current-enhanced">₹{{ number_format($product->price, 2) }}</span>
            @endif
        </div>
        
        @if($product->isInStock() && $showQuantitySelector)
            <div class="quantity-selector-enhanced">
                <button type="button" 
                        class="quantity-btn-enhanced" 
                        onclick="decreaseQuantityEnhanced({{ $product->id }})">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" 
                       id="quantity-{{ $product->id }}" 
                       class="quantity-input-enhanced" 
                       value="1" 
                       min="1" 
                       max="{{ $product->stock }}" 
                       onchange="validateQuantityEnhanced({{ $product->id }}, {{ $product->stock }})">
                <button type="button" 
                        class="quantity-btn-enhanced" 
                        onclick="increaseQuantityEnhanced({{ $product->id }}, {{ $product->stock }})">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        @endif
        
        <div class="product-actions-enhanced d-flex gap-2 mt-3">
            <a href="{{ route('product', $product->slug) }}" 
               class="btn btn-outline-enhanced flex-grow-1">
                <i class="fas fa-eye me-1"></i> View Details
            </a>
            @if($product->isInStock())
                <button onclick="addToCartEnhanced({{ $product->id }})" 
                        class="btn btn-primary-enhanced flex-grow-1">
                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                </button>
            @else
                <button onclick="notifyWhenAvailable({{ $product->id }})" 
                        class="btn btn-warning flex-grow-1">
                    <i class="fas fa-bell me-1"></i> Notify Me
                </button>
            @endif
        </div>
        
        @if($product->stock <= 10 && $product->stock > 0)
            <div class="stock-indicator-enhanced text-center mt-2">
                <small class="text-warning">
                    <i class="fas fa-clock"></i> Only {{ $product->stock }} left in stock!
                </small>
            </div>
        @endif
    </div>
</div>

<style>
    /* Product Overlay Actions */
    .product-overlay-actions {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: all 0.4s ease;
        z-index: 3;
    }
    
    .product-card-enhanced:hover .product-overlay-actions {
        opacity: 1;
    }
    
    .product-overlay-actions .btn {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.9);
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .product-overlay-actions .btn:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.05);
    }
    
    /* Product Actions Enhanced */
    .product-actions-enhanced .btn {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-size: 13px;
    }
    
    /* Stock Indicator */
    .stock-indicator-enhanced {
        padding: 8px;
        background: rgba(255, 193, 7, 0.1);
        border-radius: 8px;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    /* Product Image Placeholder */
    .product-image-placeholder {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    /* Loading State */
    .product-card-enhanced.loading {
        opacity: 0.7;
        pointer-events: none;
        position: relative;
    }
    
    .product-card-enhanced.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 30px;
        height: 30px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
        z-index: 10;
    }
    
    /* Adding to Cart Animation */
    .product-card-enhanced.adding-to-cart {
        animation: addToCartPulseEnhanced 0.8s ease-out;
    }
    
    @keyframes addToCartPulseEnhanced {
        0% { 
            transform: scale(1); 
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        50% { 
            transform: scale(1.05); 
            box-shadow: 0 15px 50px rgba(var(--primary-color), 0.4);
        }
        100% { 
            transform: scale(1); 
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .product-card-enhanced .product-image-enhanced {
            height: 220px;
        }
        
        .product-card-enhanced .card-body-enhanced {
            padding: 20px;
        }
        
        .product-actions-enhanced .btn {
            font-size: 12px;
            padding: 8px 12px;
        }
        
        .quantity-selector-enhanced {
            margin: 10px 0;
        }
        
        .quantity-btn-enhanced {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
        
        .quantity-input-enhanced {
            font-size: 14px;
            padding: 8px;
        }
    }
    
    @media (max-width: 576px) {
        .product-card-enhanced .product-image-enhanced {
            height: 180px;
        }
        
        .product-card-enhanced .card-body-enhanced {
            padding: 15px;
        }
        
        .product-title-enhanced {
            font-size: 16px;
        }
        
        .price-current-enhanced {
            font-size: 20px;
        }
    }
</style>

<script>
// Enhanced Product Card Functions
function increaseQuantityEnhanced(productId, maxStock) {
    const input = document.getElementById(`quantity-${productId}`);
    if (!input) return;
    
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.min(currentValue + 1, maxStock);
    
    input.value = newValue;
    updateQuantityButtonsEnhanced(productId, newValue, maxStock);
    
    // Add visual feedback
    input.style.transform = 'scale(1.1)';
    input.style.background = '#e3f2fd';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
        input.style.background = 'white';
    }, 200);
    
    // Show notification if at max
    if (newValue === maxStock) {
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification(`Maximum quantity (${maxStock}) reached`, 'warning', 2000);
        }
    }
}

function decreaseQuantityEnhanced(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    if (!input) return;
    
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(currentValue - 1, 1);
    
    input.value = newValue;
    updateQuantityButtonsEnhanced(productId, newValue, parseInt(input.getAttribute('max')));
    
    // Add visual feedback
    input.style.transform = 'scale(0.9)';
    input.style.background = '#ffebee';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
        input.style.background = 'white';
    }, 200);
}

function validateQuantityEnhanced(productId, maxStock) {
    const input = document.getElementById(`quantity-${productId}`);
    if (!input) return;
    
    let value = parseInt(input.value);
    
    if (isNaN(value) || value < 1) {
        value = 1;
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Minimum quantity is 1', 'warning', 2000);
        }
    } else if (value > maxStock) {
        value = maxStock;
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification(`Only ${maxStock} items available in stock`, 'warning', 3000);
        }
    }
    
    input.value = value;
    updateQuantityButtonsEnhanced(productId, value, maxStock);
}

function updateQuantityButtonsEnhanced(productId, currentValue, maxStock) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (!productCard) return;
    
    const decreaseBtn = productCard.querySelector('.quantity-btn-enhanced:first-child');
    const increaseBtn = productCard.querySelector('.quantity-btn-enhanced:last-child');
    
    if (decreaseBtn) {
        decreaseBtn.disabled = currentValue <= 1;
        decreaseBtn.style.opacity = currentValue <= 1 ? '0.5' : '1';
    }
    
    if (increaseBtn) {
        increaseBtn.disabled = currentValue >= maxStock;
        increaseBtn.style.opacity = currentValue >= maxStock ? '0.5' : '1';
    }
}

function addToCartEnhanced(productId) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    // Add loading state
    if (productCard) {
        productCard.classList.add('loading');
        productCard.classList.add('adding-to-cart');
    }
    
    // Trigger fireworks animation
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.triggerOnAction(productCard);
    }
    
    // Make AJAX request
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const quantityText = quantity > 1 ? ` (${quantity} items)` : '';
            
            // Show success notification
            if (typeof window.showEnhancedNotification === 'function') {
                window.showEnhancedNotification(
                    `${data.message || 'Product added to cart'}${quantityText}!`, 
                    'success', 
                    3000
                );
            }
            
            // Reset quantity to 1
            if (quantityInput) {
                quantityInput.value = 1;
                updateQuantityButtonsEnhanced(productId, 1, parseInt(quantityInput.getAttribute('max')));
            }
            
            // Update cart count if function exists
            if (typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }
            
            // Trigger celebration for higher quantities
            if (quantity > 1 && typeof window.enhancedFireworks !== 'undefined') {
                setTimeout(() => {
                    window.enhancedFireworks.createCelebrationBurst();
                }, 500);
            }
        } else {
            if (typeof window.showEnhancedNotification === 'function') {
                window.showEnhancedNotification(
                    data.message || 'Error adding product to cart', 
                    'error', 
                    4000
                );
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Network error. Please try again.', 'error', 4000);
        }
    })
    .finally(() => {
        // Remove loading state
        if (productCard) {
            productCard.classList.remove('loading');
            setTimeout(() => {
                productCard.classList.remove('adding-to-cart');
            }, 800);
        }
    });
}

// Initialize quantity buttons on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-product-id]').forEach(card => {
        const productId = card.getAttribute('data-product-id');
        const quantityInput = document.getElementById(`quantity-${productId}`);
        
        if (quantityInput) {
            const currentValue = parseInt(quantityInput.value);
            const maxStock = parseInt(quantityInput.getAttribute('max'));
            updateQuantityButtonsEnhanced(productId, currentValue, maxStock);
        }
    });
});

// Notification function for out-of-stock products (Enhanced version)
if (typeof window.notifyWhenAvailable === 'undefined') {
    window.notifyWhenAvailable = function(productId) {
        const productCard = document.querySelector(`[data-product-id="${productId}"]`);
        const productName = productCard ? productCard.querySelector('.product-title-enhanced')?.textContent || 'this product' : 'this product';
        
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification(`✅ We'll notify you when "${productName}" is back in stock!`, 'success', 4000);
        } else if (typeof window.showToast === 'function') {
            window.showToast(`✅ We'll notify you when "${productName}" is back in stock!`, 'success');
        } else {
            alert(`✅ We'll notify you when "${productName}" is back in stock!`);
        }
        
        // Trigger celebration effect for enhanced cards
        if (typeof window.enhancedFireworks !== 'undefined') {
            setTimeout(() => {
                window.enhancedFireworks.createCelebrationBurst();
            }, 300);
        }
    };
}
</script>
