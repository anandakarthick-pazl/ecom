<?php if($products->count() > 0): ?>
    <div class="product-grid-enhanced">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="product-card-compact" data-product-id="<?php echo e($product->id); ?>">
            <div class="product-image-container">
                <?php if($product->featured_image): ?>
                    <img src="<?php echo e(Storage::url($product->featured_image)); ?>" 
                         class="product-image" 
                         alt="<?php echo e($product->name); ?>" 
                         loading="lazy">
                <?php else: ?>
                    <div class="product-image bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-2x text-muted"></i>
                    </div>
                <?php endif; ?>
                
                <div class="product-badges">
                    <div>
                        <?php if($product->discount_percentage > 0): ?>
                            <span class="badge badge-compact bg-danger"><?php echo e($product->discount_percentage); ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if($product->is_featured): ?>
                            <span class="badge badge-compact bg-warning text-dark">
                                <i class="fas fa-star"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="product-content">
                <div class="product-category"><?php echo e($product->category->name); ?></div>
                <h6 class="product-title-compact"><?php echo e($product->name); ?></h6>
                
                <div class="product-price-section">
                    <?php if($product->discount_price): ?>
                        <span class="price-current">₹<?php echo e(number_format($product->discount_price, 2)); ?></span>
                        <span class="price-original">₹<?php echo e(number_format($product->price, 2)); ?></span>
                    <?php else: ?>
                        <span class="price-current">₹<?php echo e(number_format($product->price, 2)); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if($product->stock <= 5 && $product->stock > 0): ?>
                    <div class="stock-indicator stock-low">
                        <i class="fas fa-exclamation-triangle"></i> Only <?php echo e($product->stock); ?> left!
                    </div>
                <?php elseif($product->stock == 0): ?>
                    <div class="stock-indicator stock-out">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </div>
                <?php endif; ?>
                
                <!-- Quantity Selector -->
                <?php if($product->isInStock()): ?>
                    <div class="quantity-section mb-2">
                        <label for="quantity-<?php echo e($product->id); ?>" class="quantity-label">Quantity:</label>
                        <div class="quantity-input-group">
                            <button type="button" class="quantity-btn quantity-decrease" onclick="decreaseQuantity(<?php echo e($product->id); ?>)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   id="quantity-<?php echo e($product->id); ?>" 
                                   class="quantity-input" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo e($product->stock); ?>" 
                                   onchange="validateQuantity(<?php echo e($product->id); ?>, <?php echo e($product->stock); ?>)">
                            <button type="button" class="quantity-btn quantity-increase" onclick="increaseQuantity(<?php echo e($product->id); ?>, <?php echo e($product->stock); ?>)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="product-actions-compact">
                    <a href="<?php echo e(route('product', $product->slug)); ?>" class="btn btn-outline-primary btn-compact">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <?php if($product->isInStock()): ?>
                        <button onclick="addToCartWithAnimation(<?php echo e($product->id); ?>)" class="btn btn-primary btn-compact">
                            <i class="fas fa-cart-plus"></i> Add
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-compact" disabled>
                            <i class="fas fa-times"></i> Sold Out
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted"></i>
        </div>
        <h3 class="text-muted mb-3">No Products Found</h3>
        <p class="text-muted mb-4">Try browsing different categories or check back later for new arrivals.</p>
        <a href="<?php echo e(route('products')); ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Browse All Products
        </a>
    </div>
<?php endif; ?>

<style>
/* Enhanced Add to Cart Animation */
.product-card-compact.adding-to-cart {
    animation: addToCartPulse 0.6s ease-out;
}

@keyframes addToCartPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(0,123,255,0.5); }
    100% { transform: scale(1); }
}

/* Hover effect enhancement */
.product-card-compact:hover .product-title-compact {
    color: var(--primary-color, #007bff);
}

.product-card-compact:hover .price-current {
    font-size: 16px;
}

/* Enhanced hover effects with animation */
.product-card-compact:hover {
    --hover-lift: -12px;
    transform: translateY(var(--hover-lift)) scale(1.03);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2);
}

.product-card-compact:hover .product-image {
    transform: scale(1.15);
}

.product-card-compact:hover .btn-compact {
    transform: scale(1.05);
}

/* Product card animation on load */
.product-card-compact {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for grid items */
.product-grid-enhanced .product-card-compact:nth-child(1) { animation-delay: 0.1s; }
.product-grid-enhanced .product-card-compact:nth-child(2) { animation-delay: 0.2s; }
.product-grid-enhanced .product-card-compact:nth-child(3) { animation-delay: 0.3s; }
.product-grid-enhanced .product-card-compact:nth-child(4) { animation-delay: 0.4s; }
.product-grid-enhanced .product-card-compact:nth-child(5) { animation-delay: 0.5s; }
.product-grid-enhanced .product-card-compact:nth-child(6) { animation-delay: 0.6s; }
.product-grid-enhanced .product-card-compact:nth-child(7) { animation-delay: 0.7s; }
.product-grid-enhanced .product-card-compact:nth-child(8) { animation-delay: 0.8s; }
.product-grid-enhanced .product-card-compact:nth-child(9) { animation-delay: 0.9s; }
.product-grid-enhanced .product-card-compact:nth-child(10) { animation-delay: 1.0s; }
.product-grid-enhanced .product-card-compact:nth-child(11) { animation-delay: 1.1s; }
.product-grid-enhanced .product-card-compact:nth-child(12) { animation-delay: 1.2s; }

/* Loading state for individual products */
.product-card-compact.loading {
    opacity: 0.6;
    pointer-events: none;
    position: relative;
}

.product-card-compact.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary-color, #007bff);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
    z-index: 10;
}

/* Micro-interaction for buttons */
.btn-compact:active {
    transform: scale(0.95);
    transition: transform 0.1s;
}

/* Enhanced badge animations */
.badge-compact {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Disable animations for reduced motion preference */
@media (prefers-reduced-motion: reduce) {
    .product-card-compact,
    .product-image,
    .btn-compact,
    .badge-compact {
        animation: none !important;
        transition: none !important;
    }
    
    .product-card-compact:hover {
        transform: none !important;
    }
}

/* Quantity Selector Styles */
.quantity-section {
    margin: 8px 0;
}

.quantity-label {
    font-size: 10px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
    display: block;
}

.quantity-input-group {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background: white;
    height: 28px;
}

.quantity-btn {
    background: #f8f9fa;
    border: none;
    width: 24px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #666;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}

.quantity-btn:hover {
    background: var(--primary-color, #007bff);
    color: white;
}

.quantity-btn:active {
    transform: scale(0.95);
}

.quantity-btn:disabled {
    background: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
}

.quantity-input {
    flex: 1;
    border: none;
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    padding: 0 4px;
    height: 26px;
    width: 100%;
    background: white;
    color: #333;
}

.quantity-input:focus {
    outline: none;
    background: #f8f9fa;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type=number] {
    -moz-appearance: textfield;
}
</style>

<script>
// Enhanced Add to Cart with Animation
function addToCartWithAnimation(productId) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    
    if (productCard) {
        // Add animation class
        productCard.classList.add('adding-to-cart');
        
        // Remove animation class after animation completes
        setTimeout(() => {
            productCard.classList.remove('adding-to-cart');
        }, 600);
        
        // Trigger fireworks if enabled
        if (typeof window.fireworks !== 'undefined') {
            window.fireworks.triggerOnAction(productCard);
        }
    }
    
    // Get quantity from the specific product's quantity selector if it exists
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    // Add loading state
    if (productCard) {
        productCard.classList.add('loading');
    }
    
    // Make AJAX request to add to cart
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
            // Show success message with quantity
            const quantityText = quantity > 1 ? ` (${quantity} items)` : '';
            showNotification(`Product added to cart${quantityText}!`, 'success');
            
            // Reset quantity to 1 after successful add
            if (quantityInput) {
                quantityInput.value = 1;
                updateQuantityButtons(productId, 1, parseInt(quantityInput.getAttribute('max')));
            }
            
            // Update cart count if function exists
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
            
            // Trigger celebration animations if enabled
            if (typeof fireworks !== 'undefined') {
                fireworks.createCelebrationBurst();
            }
        } else {
            showNotification(data.message || 'Error adding product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding product to cart', 'error');
    })
    .finally(() => {
        // Remove loading state
        if (productCard) {
            productCard.classList.remove('loading');
        }
    });
}

// Notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    const alertClass = type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'danger');
    notification.className = `alert alert-${alertClass} position-fixed`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
        animation: slideInRight 0.3s ease-out;
    `;
    
    const iconClass = type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle');
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${iconClass} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds (or 4 seconds for warnings)
    const duration = type === 'warning' ? 4000 : 3000;
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, duration);
}

// Quantity Management Functions
function increaseQuantity(productId, maxStock) {
    const input = document.getElementById(`quantity-${productId}`);
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.min(currentValue + 1, maxStock);
    
    input.value = newValue;
    
    // Update button states
    updateQuantityButtons(productId, newValue, maxStock);
    
    // Add visual feedback
    input.style.transform = 'scale(1.1)';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
    }, 150);
}

function decreaseQuantity(productId) {
    const input = document.getElementById(`quantity-${productId}`);
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(currentValue - 1, 1);
    
    input.value = newValue;
    
    // Update button states
    updateQuantityButtons(productId, newValue, parseInt(input.getAttribute('max')));
    
    // Add visual feedback
    input.style.transform = 'scale(0.9)';
    setTimeout(() => {
        input.style.transform = 'scale(1)';
    }, 150);
}

function validateQuantity(productId, maxStock) {
    const input = document.getElementById(`quantity-${productId}`);
    let value = parseInt(input.value);
    
    // Validate range
    if (isNaN(value) || value < 1) {
        value = 1;
    } else if (value > maxStock) {
        value = maxStock;
        showNotification(`Only ${maxStock} items available in stock`, 'warning');
    }
    
    input.value = value;
    
    // Update button states
    updateQuantityButtons(productId, value, maxStock);
}

function updateQuantityButtons(productId, currentValue, maxStock) {
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (!productCard) return;
    
    const decreaseBtn = productCard.querySelector('.quantity-decrease');
    const increaseBtn = productCard.querySelector('.quantity-increase');
    
    // Update decrease button
    if (decreaseBtn) {
        decreaseBtn.disabled = currentValue <= 1;
    }
    
    // Update increase button
    if (increaseBtn) {
        increaseBtn.disabled = currentValue >= maxStock;
    }
}

// Add CSS for notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
<?php /**PATH D:\source_code\ecom\resources\views/partials/products-grid-enhanced.blade.php ENDPATH**/ ?>