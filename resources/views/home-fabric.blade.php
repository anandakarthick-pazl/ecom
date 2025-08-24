@extends('layouts.app-fabric')

@section('title', 'Best Online Store - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Get Quality Products Online. Discover premium quality products at ' . ($globalCompany->company_name ?? 'Your Store') . '.')

@section('content')

<!-- Hero Banner Section -->
@if($banners->count() > 0)
<section style="background: #f8f9fa; padding: 20px 0;">
    <div class="container">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            @if($banners->count() > 1)
            <div class="carousel-indicators">
                @foreach($banners as $banner)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" 
                        class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
            @endif
            <div class="carousel-inner" style="border-radius: 12px; overflow: hidden;">
                @foreach($banners as $banner)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" 
                             class="d-block w-100" 
                             alt="{{ $banner->alt_text ?: $banner->title }}" 
                             style="height: 400px; object-fit: cover;">
                    @else
                        <div style="height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-white">
                                <h2>{{ $banner->title ?? 'Welcome to Our Store' }}</h2>
                                <p>{{ $banner->description ?? 'Discover amazing products' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Promotional Badges Section -->
<section style="padding: 30px 0; background: white;">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🎯</div>
                    <h4 style="color: #c2185b; margin-bottom: 5px;">Save</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #880e4f;">₹29</div>
                    <p style="font-size: 0.9rem; color: #880e4f; margin-top: 10px;">Enjoy Discount all types of Crackers</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">✨</div>
                    <h4 style="color: #e65100; margin-bottom: 5px;">Discount</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #bf360c;">30%</div>
                    <p style="font-size: 0.9rem; color: #bf360c; margin-top: 10px;">Enjoy Discount all types of Crackers</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🏆</div>
                    <h4 style="color: #1565c0; margin-bottom: 5px;">Up to</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #0d47a1;">50%</div>
                    <p style="font-size: 0.9rem; color: #0d47a1; margin-top: 10px;">Enjoy Discount all types of Crackers</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🚚</div>
                    <h4 style="color: #7b1fa2; margin-bottom: 5px;">Free</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #4a148c;">SHIP</div>
                    <p style="font-size: 0.9rem; color: #4a148c; margin-top: 10px;">Enjoy Discount all types of Crackers</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section style="padding: 40px 0; background: #f8f9fa;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #212529;">Shop by Category</h2>
            <p style="color: #6c757d;">Find what you need from our categories</p>
        </div>
        <div class="row g-3">
            @foreach($categories->take(8) as $category)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('category', $category->slug) }}" style="text-decoration: none;">
                    <div style="background: white; padding: 20px; text-align: center; border-radius: 12px; transition: all 0.3s; border: 1px solid #e0e0e0; height: 100%;">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" 
                                 alt="{{ $category->name }}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; margin-bottom: 15px;">
                        @else
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-box" style="font-size: 2rem; color: white;"></i>
                            </div>
                        @endif
                        <h5 style="color: #212529; font-weight: 600; margin-bottom: 5px;">{{ $category->name }}</h5>
                        <span style="color: #6c757d; font-size: 0.9rem;">{{ $category->products_count ?? 0 }} Products</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
<section style="padding: 40px 0; background: white;">
    <div class="container">
        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="font-size: 2rem; font-weight: 700; color: #212529;">Featured Products</h2>
                <p style="color: #6c757d; margin: 0;">Our best selling products</p>
            </div>
            <a href="{{ route('products') }}" style="margin-left: auto; color: #28a745; text-decoration: none; font-weight: 600;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="row g-3">
            @forelse($featuredProducts->take(12) as $product)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="product-card" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 15px; height: 100%; position: relative; transition: all 0.3s;">
                    @if($product->discount_percentage > 0)
                        <span style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 4px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                    
                    <div style="text-align: center; margin-bottom: 15px;">
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}" 
                             style="width: 100%; height: 120px; object-fit: contain;">
                    </div>
                    
                    <h6 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: #212529; min-height: 40px;">
                        {{ Str::limit($product->name, 40) }}
                    </h6>
                    
                    @if($product->category)
                    <p style="font-size: 0.75rem; color: #6c757d; margin-bottom: 10px;">{{ $product->category->name }}</p>
                    @endif
                    
                    <div style="margin-bottom: 12px;">
                        @php
                            // Check for effective price (from offers) or discount price (manual)
                            $finalPrice = isset($product->effective_price) ? $product->effective_price : ($product->discount_price ?: $product->price);
                            $hasDiscount = $finalPrice < $product->price;
                        @endphp
                        
                        @if($hasDiscount)
                            <span style="font-size: 1.1rem; font-weight: 700; color: #28a745;">₹{{ number_format($finalPrice, 2) }}</span>
                            <span style="font-size: 0.85rem; color: #999; text-decoration: line-through; margin-left: 5px;">₹{{ number_format($product->price, 2) }}</span>
                        @else
                            <span style="font-size: 1.1rem; font-weight: 700; color: #28a745;">₹{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    @if($product->stock > 0)
                    <div style="display: flex; gap: 5px;">
                        <input type="number" 
                               id="qty-{{ $product->id }}" 
                               min="1" 
                               value="1" 
                               style="width: 50px; padding: 6px; border: 1px solid #ddd; border-radius: 6px; text-align: center; font-size: 0.85rem;">
                        <button onclick="addToCart({{ $product->id }})" 
                                style="flex: 1; padding: 6px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#218838'"
                                onmouseout="this.style.background='#28a745'">
                            Add to Cart
                        </button>
                    </div>
                    @else
                    <button disabled style="width: 100%; padding: 8px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 0.85rem; cursor: not-allowed;">
                        Out of Stock
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No featured products available.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- All Products Section -->
<section style="padding: 40px 0; background: #f8f9fa;">
    <div class="container">
        <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="font-size: 2rem; font-weight: 700; color: #212529;">All Products</h2>
                <p style="color: #6c757d; margin: 0;">Browse our complete collection</p>
            </div>
            <div>
                @if($frontendPaginationSettings['enabled'] ?? false)
                    <select onchange="changePageSize(this.value)" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px;">
                        @foreach(($frontendPaginationControls['allowed_values'] ?? null) ?: [12, 24, 48, 96] as $option)
                            <option value="{{ $option }}" {{ request('per_page', $frontendPaginationSettings['per_page'] ?? 12) == $option ? 'selected' : '' }}>
                                Show {{ $option }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        
        <div class="row g-3">
            @forelse($products as $product)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="product-card" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 15px; height: 100%; position: relative; transition: all 0.3s;">
                    @if($product->discount_percentage > 0)
                        <span style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 4px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                            -{{ $product->discount_percentage }}%
                        </span>
                    @endif
                    
                    <!-- Wishlist Button -->
                    <button onclick="addToWishlist({{ $product->id }})" 
                            style="position: absolute; top: 10px; left: 10px; background: white; border: 1px solid #ddd; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s;">
                        <i class="far fa-heart" style="color: #666; font-size: 0.9rem;"></i>
                    </button>
                    
                    <div style="text-align: center; margin-bottom: 15px;">
                        <a href="{{ route('product', $product->slug) }}">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 100%; height: 120px; object-fit: contain;">
                        </a>
                    </div>
                    
                    <h6 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: #212529; min-height: 40px;">
                        <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; color: inherit;">
                            {{ Str::limit($product->name, 40) }}
                        </a>
                    </h6>
                    
                    @if($product->category)
                    <p style="font-size: 0.75rem; color: #6c757d; margin-bottom: 10px;">{{ $product->category->name }}</p>
                    @endif
                    
                    <div style="margin-bottom: 12px;">
                        @if($product->sale_price)
                            <span style="font-size: 1.1rem; font-weight: 700; color: #28a745;">₹{{ number_format($product->sale_price, 2) }}</span>
                            <span style="font-size: 0.85rem; color: #999; text-decoration: line-through; margin-left: 5px;">₹{{ number_format($product->price, 2) }}</span>
                        @else
                            <span style="font-size: 1.1rem; font-weight: 700; color: #28a745;">₹{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    
                    @if($product->stock > 0)
                    <div style="display: flex; gap: 5px;">
                        <input type="number" 
                               id="qty-{{ $product->id }}" 
                               min="1" 
                               value="1" 
                               style="width: 50px; padding: 6px; border: 1px solid #ddd; border-radius: 6px; text-align: center; font-size: 0.85rem;">
                        <button onclick="addToCart({{ $product->id }})" 
                                style="flex: 1; padding: 6px; background: #28a745; color: white; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#218838'"
                                onmouseout="this.style.background='#28a745'">
                            Add to Cart
                        </button>
                    </div>
                    @else
                    <button disabled style="width: 100%; padding: 8px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 0.85rem; cursor: not-allowed;">
                        Out of Stock
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <p>No products available at the moment.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section style="padding: 60px 0; background: white;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck" style="font-size: 2rem; color: #2e7d32;"></i>
                    </div>
                    <h5 style="font-weight: 600; margin-bottom: 10px;">Fast Delivery</h5>
                    <p style="color: #6c757d; font-size: 0.9rem;">Quick delivery to your doorstep</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shield-alt" style="font-size: 2rem; color: #f57c00;"></i>
                    </div>
                    <h5 style="font-weight: 600; margin-bottom: 10px;">Secure Payment</h5>
                    <p style="color: #6c757d; font-size: 0.9rem;">100% secure transactions</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-award" style="font-size: 2rem; color: #1976d2;"></i>
                    </div>
                    <h5 style="font-weight: 600; margin-bottom: 10px;">Best Quality</h5>
                    <p style="color: #6c757d; font-size: 0.9rem;">Premium quality products</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-headset" style="font-size: 2rem; color: #c2185b;"></i>
                    </div>
                    <h5 style="font-weight: 600; margin-bottom: 10px;">24/7 Support</h5>
                    <p style="color: #6c757d; font-size: 0.9rem;">Dedicated customer support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Floating Cart Button -->
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
    <a href="{{ route('cart.index') }}" 
       style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: #28a745; color: white; border-radius: 50%; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3); text-decoration: none; position: relative; transition: all 0.3s;"
       onmouseover="this.style.transform='scale(1.1)'"
       onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-shopping-cart" style="font-size: 1.5rem;"></i>
        <span id="floating-cart-count" 
              style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.75rem; font-weight: 600; padding: 4px 8px; border-radius: 50%; min-width: 20px; text-align: center; display: none;">
            0
        </span>
    </a>
</div>

<!-- Flash Offer Popup Component -->
@if(isset($flashOffer) && $flashOffer)
    @include('components.flash-offer-popup', ['flashOffer' => $flashOffer])
@endif

@endsection

@section('styles')
<style>
/* Product Card Hover Effects */
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.product-card a {
    text-decoration: none;
}

.product-card img {
    transition: transform 0.3s;
}

.product-card:hover img {
    transform: scale(1.05);
}

/* Category Card Hover */
.col-lg-3 > a > div:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Wishlist Button Hover */
button[onclick^="addToWishlist"]:hover {
    background: #f8f9fa !important;
}

button[onclick^="addToWishlist"]:hover i {
    color: #dc3545 !important;
}

/* Pagination Styling */
.pagination {
    justify-content: center;
}

.page-link {
    color: #28a745;
    border-color: #dee2e6;
}

.page-link:hover {
    color: #218838;
    background-color: #e9ecef;
}

.page-item.active .page-link {
    background-color: #28a745;
    border-color: #28a745;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .col-6 {
        padding: 5px;
    }
    
    .product-card {
        padding: 10px !important;
    }
    
    .product-card h6 {
        font-size: 0.8rem !important;
        min-height: 35px !important;
    }
    
    .product-card img {
        height: 100px !important;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Loading Animation */
@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.loading {
    animation: pulse 1.5s infinite;
}
</style>
@endsection

@section('scripts')
<script>
// Add to cart function
function addToCart(productId) {
    const qtyInput = document.getElementById('qty-' + productId);
    const quantity = parseInt(qtyInput.value) || 1;
    
    // Add loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();
            // Show success message
            showNotification('✅ Product added to cart!', 'success');
            // Reset quantity to 1
            qtyInput.value = 1;
            
            // Animate cart icon
            animateCartIcon();
        } else {
            showNotification(data.message || '❌ Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Failed to add product to cart', 'error');
    })
    .finally(() => {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Add to wishlist function
function addToWishlist(productId) {
    const button = event.currentTarget;
    const icon = button.querySelector('i');
    
    // Toggle heart icon
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        icon.style.color = '#dc3545';
        showNotification('❤️ Added to wishlist!', 'success');
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        icon.style.color = '#666';
        showNotification('💔 Removed from wishlist', 'info');
    }
}

// Update cart count
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;
            
            // Update all cart count badges
            document.querySelectorAll('#floating-cart-count, #cart-count-badge').forEach(badge => {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            });
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

// Animate cart icon
function animateCartIcon() {
    const cartButton = document.querySelector('[href*="cart.index"]');
    if (cartButton) {
        cartButton.style.transform = 'scale(1.3)';
        setTimeout(() => {
            cartButton.style.transform = 'scale(1)';
        }, 300);
    }
}

// Show notification
function showNotification(message, type) {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.innerHTML = message;
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 25px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        border-left: 4px solid ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        font-size: 14px;
        font-weight: 500;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Change page size
function changePageSize(size) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', size);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count
    updateCartCount();
    
    // Lazy load images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
    
    // Add smooth scroll to top button
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 45px;
        height: 45px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        z-index: 999;
        transition: all 0.3s;
    `;
    
    scrollTopBtn.onclick = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
    
    document.body.appendChild(scrollTopBtn);
    
    // Show/hide scroll to top button
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });
});
</script>
@endsection
