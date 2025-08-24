@extends('layouts.app-foodie')

@section('title', $category->name . ' - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', $category->description ?? 'Browse our collection of ' . $category->name . ' crackers and fireworks.')

@push('styles')
<style>
/* Product card styles */
.food-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.food-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Quantity controls */
.quantity-section {
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
</style>
@endpush

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">{{ $category->name }}</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">{{ $category->description ?? 'Premium quality crackers for your celebrations' }}</p>
        </div>
    </div>
</section>

<!-- Category Offers -->
@if($categoryOffers && $categoryOffers->count() > 0)
<section style="padding: 30px 0; background: white; border-bottom: 1px solid var(--border);">
    <div class="container">
        <div class="row g-3">
            @foreach($categoryOffers as $offer)
            <div class="col-md-6">
                <div style="background: linear-gradient(135deg, #ff6b35, #f77b00); padding: 1.5rem; border-radius: 12px; color: white;">
                    <h5>{{ $offer->name }}</h5>
                    <p class="mb-2">{{ $offer->description }}</p>
                    <div style="font-size: 1.5rem; font-weight: 700;">
                        {{ $offer->discount_type == 'percentage' ? $offer->discount_value . '%' : '₹' . $offer->discount_value }} OFF
                    </div>
                    @if($offer->code)
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.9rem;">
                        Code: {{ $offer->code }}
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Products Grid -->
<section style="padding: 60px 0; background: var(--background);">
    <div class="container">
        @if($products->count() > 0)
        <div class="row g-4" id="products-grid">
            @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="food-card">
                    <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; background: linear-gradient(135deg, #f5f5f5, #e0e0e0); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="font-size: 4rem;">🎇</div>
                            @endif
                            
                            @if($product->stock <= 0)
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center;">
                                    <span style="background: var(--danger-color); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 600;">Out of Stock</span>
                                </div>
                            @elseif($product->discount_price && $product->discount_price < $product->price)
                                <span style="position: absolute; top: 10px; left: 10px; background: var(--danger-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                                </span>
                            @endif
                            
                            @if($product->is_featured)
                                <span style="position: absolute; top: 10px; right: 10px; background: var(--warning-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                            @endif
                        </div>
                    </a>
                    
                    <div class="food-card-content">
                        <h3 class="food-card-title">{{ Str::limit($product->name, 25) }}</h3>
                        <p class="food-card-description">{{ Str::limit($product->description ?? 'Premium quality crackers', 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="food-card-price">₹{{ $product->final_price }}</span>
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.9rem; margin-left: 0.5rem;">₹{{ $product->price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity and Add to Cart Section -->
                    <div class="quantity-section">
                        @if($product->stock > 0)
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
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($frontendPaginationSettings['enabled'] && method_exists($products, 'links'))
        <div class="mt-5 d-flex justify-content-center" id="pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
        
        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;">📦</div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">No products in this category</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Check out our other categories for more options</p>
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                Browse All Products
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

    // AJAX pagination
    @if($frontendPaginationSettings['enabled'])
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        $.get(url, function(data) {
            $('#products-grid').html($(data).find('#products-grid').html());
            $('#pagination-container').html($(data).find('#pagination-container').html());
            
            // Scroll to top of products
            $('html, body').animate({
                scrollTop: $('#products-grid').offset().top - 100
            }, 500);
        });
    });
    @endif
</script>
@endpush
