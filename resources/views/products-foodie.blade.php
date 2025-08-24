@extends('layouts.app-foodie')

@section('title', 'All Products - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Browse all our products. Find what you need from our complete product catalog.')

@push('styles')
<style>
/* Product Grid Styles */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.product-card-foodie {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
}

.product-card-foodie:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.product-image-wrapper {
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff6b35;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.product-info {
    padding: 1rem;
}

.product-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.4;
    height: 2.8rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-category {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.product-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.75rem;
}

.product-price .original-price {
    font-size: 1rem;
    color: #999;
    text-decoration: line-through;
    margin-left: 0.5rem;
}

/* Quantity and Add to Cart Section */
.product-actions {
    padding: 0 1rem 1rem;
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.quantity-wrapper {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.25rem;
}

.qty-btn {
    width: 28px;
    height: 28px;
    border: none;
    background: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.qty-btn:hover {
    background: var(--primary-color);
    color: white;
}

.qty-input {
    width: 60px;
    height: 28px;
    text-align: center;
    border: 1px solid #e0e0e0;
    background: white;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.qty-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.1);
}

/* Show number input arrows */
.qty-input::-webkit-inner-spin-button,
.qty-input::-webkit-outer-spin-button {
    opacity: 1;
    cursor: pointer;
    height: 28px;
}

.btn-add-to-cart {
    flex: 1;
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-add-to-cart:hover {
    background: #ff5722;
    transform: scale(1.02);
}

.btn-add-to-cart:active {
    transform: scale(0.98);
}

.btn-out-of-stock {
    flex: 1;
    background: #999;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: not-allowed;
    opacity: 0.7;
    font-size: 0.875rem;
}

/* Filter Section */
.filter-section {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.filter-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.5rem 1.25rem;
    background: #f8f9fa;
    border: 2px solid transparent;
    border-radius: 25px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    background: #fff5f3;
    border-color: var(--primary-color);
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .product-image-wrapper {
        height: 150px;
    }
    
    .product-name {
        font-size: 0.875rem;
    }
    
    .product-price {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
}
</style>
@endpush

@section('content')
<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">All Products</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Explore our complete collection</p>
        </div>
    </div>
</section>

<section style="padding: 40px 0;">
    <div class="container">
        <!-- Category Filter -->
        @if($categories->count() > 0)
        <div class="filter-section">
            <h5 style="margin-bottom: 1rem; font-weight: 600;">Filter by Category</h5>
            <div class="filter-buttons">
                <button class="filter-btn {{ request('category', 'all') === 'all' ? 'active' : '' }}" onclick="filterByCategory('all')">
                    All Products
                </button>
                @foreach($categories as $category)
                <button class="filter-btn {{ request('category') === $category->slug ? 'active' : '' }}" onclick="filterByCategory('{{ $category->slug }}')">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Products Grid -->
        @if($products->count() > 0)
        <div class="products-grid">
            @foreach($products as $product)
            <div class="product-card-foodie">
                <!-- Product Image -->
                <div class="product-image-wrapper">
                    @if($product->featured_image)
                        <img src="{{ $product->featured_image_url }}" alt="{{ $product->name }}">
                    @else
                        <div style="font-size: 4rem; color: #ddd;">🎆</div>
                    @endif
                    
                    @php
                        $offerDetails = $product->getOfferDetails();
                        $hasOffer = $offerDetails !== null;
                    @endphp
                    
                    @if($hasOffer)
                        <div class="product-badge">
                            {{ round($offerDetails['discount_percentage']) }}% OFF
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="product-category">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <div class="product-price">
                        @if($hasOffer)
                            ₹{{ number_format($offerDetails['discounted_price'], 2) }}
                            <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                        @else
                            ₹{{ number_format($product->price, 2) }}
                        @endif
                    </div>
                </div>

                <!-- Quantity and Add to Cart -->
                <div class="product-actions">
                    @if($product->isInStock())
                        <div class="quantity-wrapper">
                            <button class="qty-btn" onclick="decrementQty({{ $product->id }})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   class="qty-input" 
                                   id="qty-{{ $product->id }}" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $product->stock }}"
                                   onchange="validateQty({{ $product->id }}, {{ $product->stock }})" 
                                   onkeyup="validateQty({{ $product->id }}, {{ $product->stock }})">
                            <button class="qty-btn" onclick="incrementQty({{ $product->id }})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="btn-add-to-cart" onclick="addToCartWithQty({{ $product->id }})">
                            <i class="fas fa-cart-plus"></i>
                            <span>Add</span>
                        </button>
                    @else
                        <button class="btn-out-of-stock" disabled>
                            <i class="fas fa-times"></i> Out of Stock
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(method_exists($products, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div style="font-size: 6rem; color: var(--text-secondary); margin-bottom: 1rem;">📦</div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">No products found</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Try adjusting your filters or check back later</p>
            <a href="{{ route('shop') }}" class="btn-foodie btn-foodie-primary">
                Go to Home
            </a>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
// Validate quantity input
function validateQty(productId, maxStock) {
    const input = document.getElementById('qty-' + productId);
    let value = parseInt(input.value);
    
    // If not a valid number, set to 1
    if (isNaN(value) || value < 1) {
        input.value = 1;
        return;
    }
    
    // If exceeds stock, set to max stock
    if (value > maxStock) {
        input.value = maxStock;
        showToast('Maximum available quantity is ' + maxStock, 'error');
        return;
    }
    
    // Valid value, keep it
    input.value = value;
}

// Quantity controls
function incrementQty(productId) {
    const input = document.getElementById('qty-' + productId);
    const currentValue = parseInt(input.value) || 0;
    const maxValue = parseInt(input.getAttribute('max'));
    
    if (currentValue < maxValue) {
        input.value = currentValue + 1;
    } else {
        showToast('Maximum available quantity is ' + maxValue, 'error');
    }
}

function decrementQty(productId) {
    const input = document.getElementById('qty-' + productId);
    const currentValue = parseInt(input.value) || 2;
    
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

// Add to cart with quantity
function addToCartWithQty(productId) {
    const quantity = document.getElementById('qty-' + productId).value;
    
    // Disable button to prevent double clicks
    const button = event.currentTarget;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count immediately
            updateCartCount();
            
            // Reset quantity to 1
            document.getElementById('qty-' + productId).value = 1;
            
            // Show success message
            if (typeof showToast === 'function') {
                showToast(data.message || 'Product added to cart!', 'success');
            } else {
                alert('Product added to cart!');
            }
            
            // Re-enable button
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-cart-plus"></i> <span>Add</span>';
        } else {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Failed to add to cart', 'error');
            } else {
                alert(data.message || 'Failed to add to cart');
            }
            
            // Re-enable button
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-cart-plus"></i> <span>Add</span>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showToast === 'function') {
            showToast('Something went wrong!', 'error');
        } else {
            alert('Something went wrong!');
        }
        
        // Re-enable button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-cart-plus"></i> <span>Add</span>';
    });
}

// Filter by category
function filterByCategory(category) {
    let url = '{{ route("products") }}';
    if (category !== 'all') {
        url += '?category=' + category;
    }
    window.location.href = url;
}

// Make sure updateCartCount is available
if (typeof updateCartCount === 'undefined') {
    window.updateCartCount = function() {
        fetch('{{ route("cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                
                // Update all cart badges
                document.querySelectorAll('#cart-count, #mobile-cart-count, #floating-cart-count').forEach(badge => {
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'flex' : 'none';
                    }
                });
            })
            .catch(error => console.error('Error fetching cart count:', error));
    };
}
</script>
@endpush
