@extends('layouts.app-foodie')

@section('title', 'Premium Crackers & Fireworks - ' . ($globalCompany->company_name ?? 'Your Celebration Partner'))
@section('meta_description', 'Buy quality crackers and fireworks online. Safe, colorful, and exciting products for all celebrations from ' . ($globalCompany->company_name ?? 'Crackers Store') . '.')

@section('content')

<!-- Dynamic Banner Section -->
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
            <div class="carousel-inner" style="border-radius: 16px; overflow: hidden;">
                @foreach($banners as $banner)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    @if($banner->link)
                        <a href="{{ $banner->link }}" target="{{ $banner->link_target ?? '_self' }}">
                    @endif
                    
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" 
                             class="d-block w-100" 
                             alt="{{ $banner->alt_text ?: $banner->title }}" 
                             style="height: 500px; object-fit: cover;">
                    @else
                        <div style="height: 500px; background: linear-gradient(135deg, #ff6b35 0%, #f77b00 100%); display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-white px-4">
                                <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">{{ $banner->title ?? 'Welcome to Our Store' }}</h1>
                                <p style="font-size: 1.25rem;">{{ $banner->description ?? 'Discover amazing products at great prices' }}</p>
                                @if($banner->button_text)
                                <a href="{{ $banner->button_link ?? '#' }}" class="btn-foodie btn-foodie-primary mt-3" style="background: white; color: var(--primary-color);">
                                    {{ $banner->button_text }}
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($banner->link)
                        </a>
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
@else
<!-- Default Hero Section if no banners -->
<section class="hero-section">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 3rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem;">Welcome to {{ $globalCompany->company_name ?? 'Our Store' }}</h1>
            <p style="font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 2rem;">Premium Quality Crackers & Fireworks for Your Celebrations</p>
            <div class="hero-buttons justify-content-center">
                <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                    Browse Menu
                </a>
                <a href="{{ route('offer.products') }}" class="btn-foodie btn-foodie-outline">
                    View Offers
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Featured Crackers Products Section -->
<section style="padding: 60px 0; background: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Spectacular Fireworks</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Light up your celebrations with our premium crackers</p>
        </div>
        
        <div class="position-relative">
            <!-- Carousel Controls -->
            <button class="carousel-control prev" onclick="scrollProducts('left')">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-control next" onclick="scrollProducts('right')">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Products Carousel -->
            <div id="productsCarousel" style="overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                <div style="display: flex; gap: 1.5rem; padding: 0 2rem;">
                    @forelse($featuredProducts->take(8) as $product)
                    <div style="min-width: 280px;">
                        <div class="food-card">
                            <div class="food-card-checkered">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="food-card-image">
                                @else
                                    <div style="width: 150px; height: 150px; background: linear-gradient(135deg, #ffe0b2, #ffcc80); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                                        🎆
                                    </div>
                                @endif
                            </div>
                            <div class="food-card-content">
                                <h3 class="food-card-title">{{ Str::limit($product->name, 20) }}</h3>
                                <p class="food-card-description">{{ Str::limit($product->description ?? 'Premium quality crackers', 50) }}</p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="food-card-price">₹{{ $product->final_price }}</span>
                                        @if($product->discount_price && $product->discount_price < $product->price)
                                            <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 1rem; margin-left: 0.5rem;">₹{{ $product->price }}</span>
                                        @endif
                                    </div>
                                </div>
                                <!-- Quantity and Add to Cart Section -->
                                <div class="d-flex gap-2 align-items-center">
                                    @if($product->stock > 0)
                                        <div style="display: flex; align-items: center; background: #f8f9fa; border-radius: 8px; padding: 0.25rem;">
                                            <button style="width: 28px; height: 28px; border: none; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="decrementQty({{ $product->id }})">
                                                <i class="fas fa-minus" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <input type="number" 
                                                   id="qty-{{ $product->id }}" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $product->stock }}" 
                                                   style="width: 50px; text-align: center; border: 1px solid #e0e0e0; background: white; font-weight: 600; font-size: 0.875rem; border-radius: 4px; padding: 2px;"
                                                   onchange="validateQty({{ $product->id }}, {{ $product->stock }})" 
                                                   onkeyup="validateQty({{ $product->id }}, {{ $product->stock }})">
                                            <button style="width: 28px; height: 28px; border: none; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="incrementQty({{ $product->id }})">
                                                <i class="fas fa-plus" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                        <button style="flex: 1; background: var(--primary-color); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.875rem;" onclick="addToCartWithQty({{ $product->id }})">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    @else
                                        <button style="flex: 1; background: #999; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; cursor: not-allowed; opacity: 0.7; font-size: 0.875rem;" disabled>
                                            <i class="fas fa-times"></i> Out of Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- Default Products if no products available -->
                    @php
                        $defaultProducts = [
                            ['name' => 'Sky Shots', 'price' => '299', 'icon' => '🎆'],
                            ['name' => 'Flower Pots', 'price' => '399', 'icon' => '✨'],
                            ['name' => 'Rockets', 'price' => '449', 'icon' => '🚀'],
                            ['name' => 'Sparklers', 'price' => '349', 'icon' => '✨'],
                        ];
                    @endphp
                    @foreach($defaultProducts as $defaultProduct)
                    <div style="min-width: 280px;">
                        <div class="food-card">
                            <div class="food-card-cart">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="food-card-checkered">
                                <div style="width: 150px; height: 150px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem; box-shadow: var(--shadow-md);">
                                    {{ $defaultProduct['icon'] }}
                                </div>
                            </div>
                            <div class="food-card-content">
                                <h3 class="food-card-title">{{ $defaultProduct['name'] }}</h3>
                                <p class="food-card-description">Premium quality crackers</p>
                                <span class="food-card-price">₹{{ $defaultProduct['price'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Special Offers Section -->
@if($categories->count() > 0)
<section style="padding: 60px 0; background: var(--background);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Explore Categories</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Find your favorite food categories</p>
        </div>
        
        <div class="row g-4">
            @foreach($categories->take(6) as $category)
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('category', $category->slug) }}" style="text-decoration: none;">
                    <div class="food-card">
                        <div style="height: 200px; background: linear-gradient(135deg, {{ $loop->index % 2 == 0 ? '#fff3e0, #ffe0b2' : '#e3f2fd, #bbdefb' }}); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ ['🎆', '✨', '🎇', '💥', '🎉', '🎊'][$loop->index % 6] }}
                            @endif
                        </div>
                        <div class="food-card-content">
                            <h3 class="food-card-title">{{ $category->name }}</h3>
                            <p class="food-card-description">{{ $category->products_count }} items available</p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        
        @if($categories->count() > 6)
        <div class="text-center mt-4">
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                View All Categories
            </a>
        </div>
        @endif
    </div>
</section>
@endif

<!-- Why Choose Us Section -->
<section style="padding: 60px 0; background: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Why Choose Us</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Your trusted partner for safe celebrations</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff6b35, #f77b00); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Safety First</h4>
                    <p style="color: var(--text-secondary);">All products tested for safety</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Certified Quality</h4>
                    <p style="color: var(--text-secondary);">Government approved products</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #007bff, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h4>Best Prices</h4>
                    <p style="color: var(--text-secondary);">Competitive pricing guaranteed</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ffc107, #ff9800); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4>Fast Delivery</h4>
                    <p style="color: var(--text-secondary);">Quick & safe delivery</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- All Products Section -->
@if($products->count() > 0)
<section style="padding: 60px 0; background: var(--background);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">All Products</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Browse our complete menu</p>
        </div>
        
        <!-- Menu Tabs -->
        <div class="d-flex justify-content-center mb-4">
            <div style="background: white; padding: 0.5rem; border-radius: 50px; box-shadow: var(--shadow-sm);">
                <a href="{{ route('shop') }}?menu=all" 
                   class="btn-foodie {{ $activeMenu === 'all' ? 'btn-foodie-primary' : 'btn-foodie-outline' }}" 
                   style="padding: 8px 24px; font-size: 0.95rem;">
                    All Products
                </a>
                <a href="{{ route('shop') }}?menu=offers" 
                   class="btn-foodie {{ $activeMenu === 'offers' ? 'btn-foodie-primary' : 'btn-foodie-outline' }}" 
                   style="padding: 8px 24px; font-size: 0.95rem;">
                    Special Offers
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-md-3 col-sm-6">
                <div class="food-card">
                    <div class="food-card-cart" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div style="height: 200px; background: linear-gradient(135deg, #f5f5f5, #e0e0e0); display: flex; align-items: center; justify-content: center; position: relative;">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="font-size: 4rem;">🎇</div>
                        @endif
                        @if($product->discount_price && $product->discount_price < $product->price)
                            <span style="position: absolute; top: 10px; left: 10px; background: var(--danger-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                            </span>
                        @endif
                    </div>
                    <div class="food-card-content">
                        <h3 class="food-card-title">{{ Str::limit($product->name, 25) }}</h3>
                        <p class="food-card-description">{{ Str::limit($product->description ?? 'Premium quality crackers', 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="food-card-price">₹{{ $product->final_price }}</span>
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.9rem; margin-left: 0.5rem;">₹{{ $product->price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($frontendPaginationSettings['enabled'] && method_exists($products, 'links'))
        <div class="mt-5">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
        
        <div class="text-center mt-4">
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif

<!-- Newsletter Section -->
<section style="padding: 60px 0; background: linear-gradient(135deg, #ff6b35, #f77b00);">
    <div class="container">
        <div class="text-center text-white">
            <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">Stay Updated</h2>
            <p style="font-size: 1.1rem; margin-bottom: 2rem;">Subscribe to get special offers and updates</p>
            <div style="max-width: 500px; margin: 0 auto;">
                <div style="background: white; padding: 0.5rem; border-radius: 50px; display: flex;">
                    <input type="email" placeholder="Enter your email" style="flex: 1; border: none; padding: 0.5rem 1rem; outline: none; background: transparent;">
                    <button class="btn-foodie btn-foodie-primary" style="border-radius: 50px;">Subscribe</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Flash Offer Popup -->
@if($flashOffer)
<div class="modal fade" id="flashOfferModal" tabindex="-1" aria-labelledby="flashOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" 
                        style="top: 1rem; right: 1rem; z-index: 10; background: white; border-radius: 50%; opacity: 1; box-shadow: var(--shadow-md);">
                </button>
                
                @if($flashOffer->image)
                    <img src="{{ asset('storage/' . $flashOffer->image) }}" 
                         alt="{{ $flashOffer->name }}" 
                         class="w-100" 
                         style="max-height: 400px; object-fit: cover;">
                @else
                    <div style="background: linear-gradient(135deg, #ff6b35, #f77b00); padding: 3rem; text-align: center; color: white;">
                        <div style="font-size: 5rem; margin-bottom: 1rem;">🎆</div>
                        <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem;">{{ $flashOffer->name }}</h2>
                    </div>
                @endif
                
                <div style="padding: 2rem;">
                    <h3 style="font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">{{ $flashOffer->name }}</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">{{ $flashOffer->description }}</p>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($flashOffer->discount_type == 'percentage')
                            <span style="background: var(--danger-color); color: white; padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 1.5rem;">
                                {{ $flashOffer->discount_value }}% OFF
                            </span>
                        @elseif($flashOffer->discount_type == 'fixed')
                            <span style="background: var(--danger-color); color: white; padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 1.5rem;">
                                ₹{{ $flashOffer->discount_value }} OFF
                            </span>
                        @endif
                        
                        @if($flashOffer->minimum_order_amount > 0)
                            <span style="color: var(--text-secondary);">
                                Min. order: ₹{{ $flashOffer->minimum_order_amount }}
                            </span>
                        @endif
                    </div>
                    
                    @if($flashOffer->code)
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small style="color: var(--text-secondary);">Use Code:</small>
                                <h4 style="font-weight: 700; color: var(--primary-color); margin: 0;">{{ $flashOffer->code }}</h4>
                            </div>
                            <button onclick="copyCode('{{ $flashOffer->code }}')" class="btn-foodie btn-foodie-outline" style="padding: 8px 20px;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary flex-fill">
                            Shop Now
                        </a>
                        <button type="button" class="btn-foodie btn-foodie-outline" data-bs-dismiss="modal">
                            Maybe Later
                        </button>
                    </div>
                    
                    @if($flashOffer->end_date)
                    <p class="text-center mt-3" style="color: var(--text-secondary); font-size: 0.9rem;">
                        <i class="fas fa-clock"></i> Offer ends: {{ $flashOffer->end_date->format('M d, Y h:i A') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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
        const originalHtml = button.innerHTML;
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
                }
                
                // Re-enable button
                button.disabled = false;
                button.innerHTML = originalHtml;
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Failed to add to cart', 'error');
                }
                
                // Re-enable button
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showToast === 'function') {
                showToast('Something went wrong!', 'error');
            }
            
            // Re-enable button
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
    }

    // Show flash offer modal on page load
    @if($flashOffer && $flashOffer->show_popup)
    document.addEventListener('DOMContentLoaded', function() {
        // Check if user has already seen this offer today
        const offerKey = 'flash_offer_{{ $flashOffer->id }}_seen';
        const lastSeen = localStorage.getItem(offerKey);
        const today = new Date().toDateString();
        
        if (lastSeen !== today) {
            // Show the modal after a short delay
            setTimeout(function() {
                const flashModal = new bootstrap.Modal(document.getElementById('flashOfferModal'));
                flashModal.show();
                
                // Mark as seen for today
                localStorage.setItem(offerKey, today);
            }, 1500); // Show after 1.5 seconds
        }
    });
    @endif
    
    // Copy coupon code function
    function copyCode(code) {
        // Create a temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = code;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Show success message
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.classList.add('btn-foodie-primary');
        button.classList.remove('btn-foodie-outline');
        
        setTimeout(function() {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-foodie-primary');
            button.classList.add('btn-foodie-outline');
        }, 2000);
    }
    
    // Scroll products carousel
    function scrollProducts(direction) {
        const carousel = document.getElementById('productsCarousel');
        const scrollAmount = 300;
        
        if (direction === 'left') {
            carousel.scrollLeft -= scrollAmount;
        } else {
            carousel.scrollLeft += scrollAmount;
        }
    }
    
    // Hide scrollbar for carousel
    document.getElementById('productsCarousel').style.cssText = `
        overflow-x: auto;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
    `;
    
    // Hide webkit scrollbar
    const style = document.createElement('style');
    style.textContent = `
        #productsCarousel::-webkit-scrollbar {
            display: none;
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
