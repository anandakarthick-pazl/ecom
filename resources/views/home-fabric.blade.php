@extends('layouts.app-fabric')

@section('title', 'Best Online Crackers Store - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Get 100+ Quality Crackers Products. Discover premium quality crackers at ' . ($globalCompany->company_name ?? 'Your Store') . '.')

@section('content')

<!-- Banner Section (from existing UI) -->
@if($banners->count() > 0)
<section class="hero-section-compact" style="margin-top: -20px;">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators">
            @foreach($banners as $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" 
                    class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($banners as $banner)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <div class="hero-container-compact">
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" 
                             class="hero-image" 
                             alt="{{ $banner->alt_text ?: $banner->title }}" 
                             style="width: 100%; height: 500px; object-fit: cover;">
                    @else
                        <div class="hero-placeholder" style="height: 500px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="hero-gradient"></div>
                        </div>
                    @endif
                    <div class="hero-overlay" style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.5));"></div>
                    <div class="hero-content" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 10;">
                        @if($banner->title)
                            <h1 style="color: white; font-size: 3rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">{{ $banner->title }}</h1>
                        @endif
                        @if($banner->description)
                            <p style="color: white; font-size: 1.2rem; margin-top: 1rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ $banner->description }}</p>
                        @endif
                        @if($banner->link_url)
                            <a href="{{ $banner->link_url }}" class="btn btn-primary btn-lg mt-3" style="padding: 0.75rem 2rem; font-weight: 600;">
                                {{ $banner->link_text ?? 'Shop Now' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <div class="carousel-btn-compact">
                <i class="fas fa-chevron-left"></i>
            </div>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <div class="carousel-btn-compact">
                <i class="fas fa-chevron-right"></i>
            </div>
        </button>
        @endif
    </div>
</section>
@else
<!-- Default Hero when no banners -->
<section class="hero-section-compact" style="margin-top: -20px;">
    <div class="hero-container-compact">
        <div style="height: 500px; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); display: flex; align-items: center; justify-content: center;">
            <div class="text-center">
                <h1 style="color: white; font-size: 3rem; font-weight: 700;">Welcome to {{ $globalCompany->company_name ?? 'Your Store' }}</h1>
                <p style="color: white; font-size: 1.2rem; margin-top: 1rem;">Discover Premium Quality Crackers Products</p>
                <a href="{{ route('products') }}" class="btn btn-light btn-lg mt-3" style="padding: 0.75rem 2rem; font-weight: 600;">
                    Shop Now
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Flash Offer Floating Widget -->
@if(isset($flashOffer) && $flashOffer && $flashOffer->isFlashOfferActive())
@php
    $timeRemaining = $flashOffer->getTimeRemaining();
@endphp
<div class="flash-offer-widget" id="flashOfferWidget">
    <div class="flash-widget-content">
        <div class="flash-widget-icon">
            <i class="fas fa-bolt"></i>
        </div>
        <div class="flash-widget-text">
            <span class="flash-widget-title">Flash Sale!</span>
            <span class="flash-widget-discount">{{ $flashOffer->discount_value_display }} OFF</span>
        </div>
        @if($flashOffer->show_countdown && !$timeRemaining['expired'])
        <div class="flash-widget-timer" id="widgetCountdown" data-end-time="{{ $flashOffer->end_date->endOfDay()->timestamp }}">
            <span class="timer-value" id="widget-hours">{{ str_pad($timeRemaining['hours'], 2, '0', STR_PAD_LEFT) }}</span>
            <span class="timer-sep">:</span>
            <span class="timer-value" id="widget-minutes">{{ str_pad($timeRemaining['minutes'], 2, '0', STR_PAD_LEFT) }}</span>
            <span class="timer-sep">:</span>
            <span class="timer-value" id="widget-seconds">{{ str_pad($timeRemaining['seconds'], 2, '0', STR_PAD_LEFT) }}</span>
        </div>
        @endif
    </div>
    <button class="flash-widget-close" onclick="closeFlashWidget()">×</button>
</div>

<style>
/* Flash Offer Floating Widget */
.flash-offer-widget {
    position: fixed;
    bottom: 100px;
    right: 20px;
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
    z-index: 999;
    cursor: pointer;
    animation: flashWidgetSlide 0.5s ease-out, flashWidgetPulse 2s infinite;
    transition: all 0.3s ease;
}

@keyframes flashWidgetSlide {
    from {
        transform: translateX(400px);
    }
    to {
        transform: translateX(0);
    }
}

@keyframes flashWidgetPulse {
    0%, 100% {
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
    }
    50% {
        box-shadow: 0 12px 35px rgba(255, 107, 53, 0.6);
    }
}

.flash-offer-widget:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 35px rgba(255, 107, 53, 0.6);
}

.flash-widget-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.flash-widget-icon {
    font-size: 1.5rem;
    color: #ffd93d;
    animation: flashIconBlink 1s infinite;
}

@keyframes flashIconBlink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.flash-widget-text {
    display: flex;
    flex-direction: column;
}

.flash-widget-title {
    font-size: 0.9rem;
    font-weight: 600;
}

.flash-widget-discount {
    font-size: 1.2rem;
    font-weight: 700;
    color: #ffd93d;
}

.flash-widget-timer {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 1rem;
    font-weight: 600;
    background: rgba(0, 0, 0, 0.2);
    padding: 5px 10px;
    border-radius: 20px;
}

.timer-value {
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 6px;
    border-radius: 4px;
}

.timer-sep {
    color: #ffd93d;
}

.flash-widget-close {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    display: none;
}

.flash-offer-widget:hover .flash-widget-close {
    display: block;
}

.flash-widget-close:hover {
    background: #c82333;
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .flash-offer-widget {
        bottom: 80px;
        right: 10px;
        padding: 10px 15px;
        border-radius: 30px;
    }
    
    .flash-widget-icon {
        font-size: 1.2rem;
    }
    
    .flash-widget-title {
        font-size: 0.8rem;
    }
    
    .flash-widget-discount {
        font-size: 1rem;
    }
    
    .flash-widget-timer {
        font-size: 0.9rem;
        padding: 3px 8px;
    }
}
</style>
@endif

<!-- Categories Section -->
@if($categories->count() > 0)
<section style="padding: 3rem 0; background: white;">
    <div class="container">
        <h2 style="text-align: center; font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Shop by Category</h2>
        <div class="row">
            @foreach($categories->take(8) as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('category', $category->slug) }}" style="text-decoration: none;">
                    <div style="background: #f8f9fa; padding: 1.5rem; text-align: center; border-radius: 8px; transition: all 0.3s; hover: transform: translateY(-5px);">
                        <i class="fas fa-box-open" style="font-size: 2rem; color: #ff6b35; margin-bottom: 0.5rem;"></i>
                        <h5 style="color: #212529; margin: 0;">{{ $category->name }}</h5>
                        @if($category->products_count > 0)
                            <small style="color: #6c757d;">({{ $category->products_count }} Products)</small>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Products Section with Pagination -->
<section style="padding: 3rem 0; background: #f8f9fa;">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 style="font-size: 2rem; font-weight: 700;">Newest Products</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('products') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">
                    View All Products <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row">
            @forelse($products as $product)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <div style="background: white; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.08); height: 100%;">
                    <div style="text-align: center;">
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}" 
                             style="width: 100%; height: 120px; object-fit: contain; margin-bottom: 0.5rem;">
                        
                        @if($product->discount_percentage > 0)
                            <span style="position: absolute; top: 10px; right: 10px; background: #ff6b35; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem;">
                                {{ $product->discount_percentage }}% OFF
                            </span>
                        @endif
                        
                        <h6 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem; height: 40px; overflow: hidden;">
                            {{ Str::limit($product->name, 40) }}
                        </h6>
                        
                        <div style="margin-bottom: 0.5rem;">
                            @if($product->sale_price)
                                <span style="font-size: 1rem; font-weight: 700; color: #ff6b35;">₹{{ number_format($product->sale_price, 2) }}</span>
                                <br>
                                <span style="font-size: 0.8rem; color: #999; text-decoration: line-through;">₹{{ number_format($product->price, 2) }}</span>
                            @else
                                <span style="font-size: 1rem; font-weight: 700; color: #ff6b35;">₹{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        <!-- Quantity and Add to Cart -->
                        <div class="product-actions">
                            <div style="display: flex; gap: 0.25rem; margin-bottom: 0.5rem;">
                                <input type="number" 
                                       id="qty-{{ $product->id }}" 
                                       min="1" 
                                       value="1" 
                                       style="width: 60%; padding: 0.25rem; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 0.9rem;">
                                <button onclick="addToCart({{ $product->id }})" 
                                        style="width: 40%; padding: 0.25rem; background: #ff6b35; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: 600;">
                                    ADD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center">No products available at the moment.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</section>

<!-- Floating Cart Button -->
<div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <a href="{{ route('cart.index') }}" 
       style="display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: #ff6b35; color: white; border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.2); text-decoration: none; position: relative;">
        <i class="fas fa-shopping-cart" style="font-size: 1.5rem;"></i>
        <span id="floating-cart-count" 
              style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; font-size: 0.75rem; font-weight: 600; padding: 2px 6px; border-radius: 50%; min-width: 20px; text-align: center; display: none;">
            0
        </span>
    </a>
</div>

@endsection

@section('scripts')
<!-- Include Flash Offer Popup Component -->
@if(isset($flashOffer) && $flashOffer)
    @include('components.flash-offer-popup', ['flashOffer' => $flashOffer])
@endif

<script>
// Flash Offer Widget Functions
function closeFlashWidget() {
    const widget = document.getElementById('flashOfferWidget');
    if (widget) {
        widget.style.display = 'none';
        // Store in session that widget was closed
        sessionStorage.setItem('flash_widget_closed', 'true');
    }
}

// Show popup when clicking on widget
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('flashOfferWidget');
    if (widget) {
        // Check if widget was previously closed in this session
        if (sessionStorage.getItem('flash_widget_closed') === 'true') {
            widget.style.display = 'none';
        }
        
        // Click on widget to show popup
        widget.addEventListener('click', function(e) {
            if (!e.target.classList.contains('flash-widget-close')) {
                const modalElement = document.getElementById('flashOfferModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            }
        });
        
        // Widget countdown timer
        @if(isset($flashOffer) && $flashOffer && $flashOffer->show_countdown)
        const widgetCountdown = document.getElementById('widgetCountdown');
        if (widgetCountdown) {
            const endTime = parseInt(widgetCountdown.dataset.endTime) * 1000;
            
            function updateWidgetCountdown() {
                const now = new Date().getTime();
                const timeLeft = endTime - now;
                
                if (timeLeft > 0) {
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                    
                    const hoursEl = document.getElementById('widget-hours');
                    const minutesEl = document.getElementById('widget-minutes');
                    const secondsEl = document.getElementById('widget-seconds');
                    
                    if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                    if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                    if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
                } else {
                    // Offer expired, hide widget
                    widget.style.display = 'none';
                    clearInterval(widgetCountdownInterval);
                }
            }
            
            // Update countdown immediately and then every second
            updateWidgetCountdown();
            const widgetCountdownInterval = setInterval(updateWidgetCountdown, 1000);
        }
        @endif
    }
});

// Add to cart function
function addToCart(productId) {
    const qtyInput = document.getElementById('qty-' + productId);
    const quantity = parseInt(qtyInput.value) || 1;
    
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
            showNotification('Product added to cart!', 'success');
            // Reset quantity to 1
            qtyInput.value = 1;
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to add product to cart', 'error');
    });
}

// Update cart count
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;
            
            // Update navbar cart count
            const navbarBadge = document.getElementById('cart-count-badge');
            if (navbarBadge) {
                navbarBadge.textContent = count;
                navbarBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
            
            // Update floating cart count
            const floatingBadge = document.getElementById('floating-cart-count');
            if (floatingBadge) {
                floatingBadge.textContent = count;
                floatingBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

// Show notification
function showNotification(message, type) {
    const toast = document.createElement('div');
    toast.className = `toast-notification ${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        border-left: 4px solid ${type === 'success' ? '#4caf50' : '#f44336'};
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

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
</script>
@endsection
