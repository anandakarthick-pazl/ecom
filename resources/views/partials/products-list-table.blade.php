<!-- Products List Table View - Similar to Price List Format -->
<table class="products-table">
    <thead>
        <tr>
            <th class="sno-col">S.No</th>
            <th class="product-col">Product</th>
            <th class="mrp-col">MRP</th>
            {{-- <th class="unit-col">Unit</th> --}}
            <th class="offer-col">Offer Price</th>
            <th class="qty-col">Qty</th>
            <th class="amount-col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $index => $product)
            @php
                // Get offer details using the new priority system
                $offerDetails = $product->getOfferDetails();
                $hasOffer = $offerDetails !== null;
                $effectivePrice = $hasOffer ? $offerDetails['discounted_price'] : $product->price;
                $discountPercentage = $hasOffer ? $offerDetails['discount_percentage'] : 0;
                $serialNumber = method_exists($products, 'currentPage') ? 
                    (($products->currentPage() - 1) * $products->perPage() + $index + 1) : 
                    ($index + 1);
            @endphp
            <tr class="{{ !$product->isInStock() ? 'out-of-stock' : '' }}">
                <!-- S.No -->
                <td class="sno-col">{{ $serialNumber }}</td>
                
                <!-- Product Info -->
                <td class="product-col">
                    <div class="product-info">
                        @if($product->featured_image)
                            <img src="{{ $product->featured_image_url }}" class="product-image-small" alt="{{ $product->name }}" style="width: 35px; height: 35px;">
                        @else
                            <div class="product-placeholder-small" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        <div class="product-details">
                            <div class="product-name">
                                <a href="{{ route('product', $product->slug) }}" class="text-decoration-none">
                                    {{ $product->name }}
                                </a>
                                @if($hasOffer && $discountPercentage > 0)
                                    <span class="offer-badge">{{ round($discountPercentage) }}% OFF</span>
                                @endif
                            </div>
                            <div class="product-category">{{ $product->category->name }}</div>
                        </div>
                    </div>
                </td>
                
                <!-- MRP -->
                <td class="mrp-col">
                    <span class="price mrp-price">₹{{ number_format($product->price, 2) }}</span>
                </td>
                
                <!-- Unit -->
                {{-- <td class="unit-col">
                    <span class="unit-text">{{ $product->weight_unit ?? 'pcs' }}</span>
                </td> --}}
                
                <!-- Offer Price -->
                <td class="offer-col">
                    @if($hasOffer)
                        <span class="price offer-price">₹{{ number_format($effectivePrice, 2) }}</span>
                    @else
                        <span class="price current-price">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                </td>
                
                <!-- Quantity Controls -->
                <td class="qty-col">
                    @if($product->isInStock())
                        <div class="quantity-controls">
                            <button class="qty-btn-small" onclick="decrementQuantityList({{ $product->id }})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="qty-input-small" id="quantity-list-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                            <button class="qty-btn-small" onclick="incrementQuantityList({{ $product->id }})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    @else
                        <div class="stock-status">
                            <span class="stock-badge">Out of Stock</span>
                        </div>
                    @endif
                </td>
                
                <!-- Action/Amount -->
                <td class="amount-col">
                    @if($product->isInStock())
                        <div class="action-buttons">
                            <button onclick="addToCartFromList({{ $product->id }})" class="btn-add-cart-small {{ $hasOffer ? 'offer' : '' }}">
                                <i class="fas fa-cart-plus"></i>
                                <span>Add to Cart</span>
                            </button>
                            @if($hasOffer)
                                <small class="text-success">
                                    <i class="fas fa-tags"></i> Save ₹{{ number_format($offerDetails['savings'], 2) }}
                                </small>
                            @endif
                        </div>
                    @else
                        <button class="btn-out-stock-small" disabled>
                            <i class="fas fa-ban"></i>
                            <span>Unavailable</span>
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <p class="mb-0">No products found</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<style>
/* Additional styles for list table */
.products-table .out-of-stock {
    opacity: 0.7;
}

.products-table .out-of-stock .product-image-small {
    filter: grayscale(30%);
}

.products-table .product-name a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.products-table .product-name a:hover {
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

/* Enhanced button styles for table */
.products-table .qty-btn-small {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 2px solid #e2e8f0;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    color: #475569;
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.products-table .qty-btn-small:hover {
    background: linear-gradient(135deg, {{ $globalCompany->primary_color ?? '#3b82f6' }} 0%, {{ $globalCompany->secondary_color ?? '#1d4ed8' }} 100%);
    color: white;
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.products-table .qty-input-small {
    width: 45px;
    height: 32px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.products-table .qty-input-small:focus {
    outline: none;
    border-color: {{ $globalCompany->primary_color ?? '#3b82f6' }};
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), inset 0 1px 3px rgba(0, 0, 0, 0.1);
    background: #ffffff;
}

.products-table .btn-add-cart-small {
    background: linear-gradient(135deg, {{ $globalCompany->primary_color ?? '#3b82f6' }} 0%, {{ $globalCompany->secondary_color ?? '#1d4ed8' }} 100%);
    border-radius: 10px;
    padding: 0.6rem 1.2rem;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.products-table .btn-add-cart-small::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.products-table .btn-add-cart-small:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.products-table .btn-add-cart-small:hover::before {
    left: 100%;
}

.products-table .btn-add-cart-small.offer {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
    animation: offerPulse 2s infinite;
}

.products-table .btn-add-cart-small.offer:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5);
    animation: none;
}

@keyframes offerPulse {
    0%, 100% {
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
    }
    50% {
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
    }
}

/* Responsive adjustments for table */
@media (max-width: 768px) {
    .products-table {
        font-size: 0.75rem;
    }
    
    .products-table th,
    .products-table td {
        padding: 0.5rem 0.25rem;
    }
    
    .product-info {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
    
    .product-image-small,
    .product-placeholder-small {
        width: 30px;
        height: 30px;
    }
    
    .product-name {
        font-size: 0.75rem;
    }
    
    .btn-add-cart-small {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 8px;
    }
    
    .qty-btn-small {
        width: 28px;
        height: 28px;
        font-size: 0.75rem;
    }
    
    .qty-input-small {
        width: 35px;
        height: 28px;
        font-size: 0.75rem;
    }
    
    .quantity-controls {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .qty-btn-small {
        width: 24px;
        height: 24px;
    }
    
    .qty-input-small {
        width: 35px;
        height: 24px;
    }
}

@media (max-width: 576px) {
    .products-table {
        display: block;
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .products-table th:nth-child(3),
    .products-table td:nth-child(3),
    .products-table th:nth-child(4),
    .products-table td:nth-child(4) {
        display: none;
    }
    
    .product-col {
        width: 50%;
    }
    
    .offer-col,
    .qty-col,
    .amount-col {
        width: 16%;
    }
}
</style>

<script>
// List view specific functions
function incrementQuantityList(productId) {
    const input = document.getElementById('quantity-list-' + productId);
    const max = parseInt(input.getAttribute('max'));
    let value = parseInt(input.value);
    if (value < max) {
        input.value = value + 1;
    }
}

function decrementQuantityList(productId) {
    const input = document.getElementById('quantity-list-' + productId);
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function addToCartFromList(productId) {
    const quantity = parseInt(document.getElementById('quantity-list-' + productId).value);
    
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