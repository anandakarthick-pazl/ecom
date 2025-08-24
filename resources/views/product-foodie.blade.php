@extends('layouts.app-foodie')

@section('title', $product->meta_title ?: $product->name . ' - ' . ($globalCompany->company_name ?? 'Food Delivery'))
@section('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->meta_keywords)

@section('content')

<!-- Breadcrumb -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 20px 0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent; margin: 0; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('shop') }}" style="color: var(--primary-color); text-decoration: none;">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category', $product->category->slug) }}" style="color: var(--primary-color); text-decoration: none;">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Details -->
<section style="padding: 40px 0;">
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                    @if($product->featured_image)
                        <img src="{{ $product->featured_image_url }}" class="img-fluid rounded" alt="{{ $product->name }}" id="mainImage" style="width: 100%; height: 400px; object-fit: cover; border-radius: 12px;">
                    @else
                        <div style="height: 400px; background: linear-gradient(135deg, #f5f5f5, #e0e0e0); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 4rem; color: #9e9e9e;"></i>
                        </div>
                    @endif

                    @if($product->images && count($product->images) > 0)
                    <div class="row mt-3">
                        @if($product->featured_image)
                            <div class="col-3">
                                <img src="{{ $product->featured_image_url }}" class="img-fluid rounded thumb-image active" alt="{{ $product->name }}" onclick="changeMainImage(this)" style="cursor: pointer; border: 2px solid var(--primary-color); border-radius: 8px;">
                            </div>
                        @endif
                        @foreach($product->image_urls as $imageUrl)
                            <div class="col-3">
                                <img src="{{ $imageUrl }}" class="img-fluid rounded thumb-image" alt="{{ $product->name }}" onclick="changeMainImage(this)" style="cursor: pointer; border: 2px solid transparent; border-radius: 8px;">
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm);">
                    <!-- Category & Badges -->
                    <div class="mb-3">
                        <span style="background: var(--primary-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            {{ $product->category->name }}
                        </span>
                        @if($product->is_featured)
                            <span style="background: #ffc107; color: #333; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-left: 8px;">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        @endif
                    </div>

                    <h1 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">{{ $product->name }}</h1>

                    @if($product->short_description)
                        <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 1.5rem;">{{ $product->short_description }}</p>
                    @endif

                    <!-- Price Section -->
                    <div style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        @php
                            $offerDetails = $product->getOfferDetails();
                            $hasOffer = $offerDetails !== null;
                            $effectivePrice = $hasOffer ? $offerDetails['discounted_price'] : $product->price;
                        @endphp
                        
                        @if($hasOffer)
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">₹{{ number_format($effectivePrice, 2) }}</span>
                                <span style="font-size: 1.25rem; text-decoration: line-through; color: var(--text-secondary);">₹{{ number_format($product->price, 2) }}</span>
                                <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 8px; font-weight: 600;">
                                    {{ round($offerDetails['discount_percentage']) }}% OFF
                                </span>
                            </div>
                            <p style="color: #28a745; font-size: 0.9rem; margin: 0.5rem 0 0 0;">
                                <i class="fas fa-tag"></i> You save ₹{{ number_format($offerDetails['savings'], 2) }}
                            </p>
                        @else
                            <span style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">₹{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div style="margin-bottom: 1.5rem;">
                        @if($product->weight)
                            <p style="margin-bottom: 0.5rem;"><strong>Weight:</strong> {{ $product->weight }} {{ $product->weight_unit }}</p>
                        @endif
                        @if($product->sku)
                            <p style="margin-bottom: 0.5rem;"><strong>SKU:</strong> {{ $product->sku }}</p>
                        @endif
                        <p style="margin-bottom: 0.5rem;">
                            <strong>Availability:</strong> 
                            @if($product->stock > 10)
                                <span style="color: #28a745; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> In Stock ({{ $product->stock }} available)
                                </span>
                            @elseif($product->stock > 0)
                                <span style="color: #ffc107; font-weight: 600;">
                                    <i class="fas fa-exclamation-circle"></i> Limited Stock ({{ $product->stock }} left)
                                </span>
                            @else
                                <span style="color: #dc3545; font-weight: 600;">
                                    <i class="fas fa-times-circle"></i> Out of Stock
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Add to Cart Section -->
                    @if($product->isInStock())
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Quantity:</label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button onclick="decrementQuantity()" style="width: 40px; height: 40px; border: 2px solid var(--primary-color); background: white; border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--primary-color);">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}" style="width: 80px; height: 40px; text-align: center; border: 2px solid #e0e0e0; border-radius: 8px; font-weight: 600; font-size: 1.1rem;">
                                    <button onclick="incrementQuantity()" style="width: 40px; height: 40px; border: 2px solid var(--primary-color); background: white; border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--primary-color);">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <button onclick="addToCartWithQuantity()" class="btn-foodie btn-foodie-primary w-100" style="padding: 12px; font-size: 1.1rem;">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    @else
                    <div style="background: #fff3cd; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <p style="color: #856404; margin: 0;">
                            <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock.
                        </p>
                    </div>
                    @endif

                    <!-- Share Buttons -->
                    <div style="border-top: 1px solid #e0e0e0; padding-top: 1.5rem;">
                        <h6 style="font-weight: 600; margin-bottom: 1rem;">Share this product:</h6>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="https://wa.me/?text=Check out this amazing product: {{ $product->name }} - {{ url()->current() }}" target="_blank" style="background: #25d366; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" style="background: #1877f2; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <button onclick="copyToClipboard()" style="background: #6c757d; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-link"></i> Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="row mt-4">
            <div class="col-12">
                <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-weight: 600; margin-bottom: 1.5rem; color: var(--text-primary);">
                        <i class="fas fa-info-circle" style="color: var(--primary-color);"></i> Product Description
                    </h3>
                    <div style="color: var(--text-secondary); line-height: 1.8;">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 style="font-weight: 600; margin-bottom: 2rem; color: var(--text-primary);">
                    <i class="fas fa-utensils" style="color: var(--primary-color);"></i> Related Products
                </h3>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        @include('partials.product-card-foodie', ['product' => $relatedProduct])
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
function changeMainImage(img) {
    document.getElementById('mainImage').src = img.src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumb-image').forEach(thumb => {
        thumb.style.border = '2px solid transparent';
    });
    img.style.border = '2px solid var(--primary-color)';
}

function incrementQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const maxValue = parseInt(quantityInput.getAttribute('max'));
    
    if (currentValue < maxValue) {
        quantityInput.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const minValue = parseInt(quantityInput.min);
    
    if (currentValue > minValue) {
        quantityInput.value = currentValue - 1;
    }
}

function addToCartWithQuantity() {
    const quantity = document.getElementById('quantity').value;
    addToCart({{ $product->id }}, quantity);
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        if (typeof showToast === 'function') {
            showToast('Link copied to clipboard!', 'success');
        } else {
            alert('Link copied to clipboard!');
        }
    });
}

function addToCart(productId, quantity = 1) {
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
            // Update cart count
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
            // Show success message
            if (typeof showToast === 'function') {
                showToast(data.message || 'Product added to cart!', 'success');
            } else {
                alert('Product added to cart!');
            }
            // Reset quantity for main product
            if (productId == {{ $product->id }}) {
                document.getElementById('quantity').value = 1;
            }
        } else {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Failed to add to cart', 'error');
            } else {
                alert(data.message || 'Failed to add to cart');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showToast === 'function') {
            showToast('Something went wrong!', 'error');
        } else {
            alert('Something went wrong!');
        }
    });
}
</script>
@endpush
