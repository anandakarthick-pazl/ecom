@extends('layouts.app')
<?php //echo "<pre>";print_R($product->getOfferDetails());exit;?>
@section('title', $product->meta_title ?: $product->name . ' - Herbal Bliss')
@section('meta_description', $product->meta_description ?: Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->meta_keywords)

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6">
            <div class="product-images">
                @if($product->featured_image)
                    <img src="{{ $product->featured_image_url }}" class="img-fluid rounded main-image" alt="{{ $product->name }}" id="mainImage">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                        <i class="fas fa-image fa-4x text-muted"></i>
                    </div>
                @endif

                @if($product->images && count($product->images) > 0)
                <div class="row mt-3">
                    @if($product->featured_image)
                        <div class="col-3">
                            <img src="{{ $product->featured_image_url }}" class="img-fluid rounded thumb-image active" alt="{{ $product->name }}" onclick="changeMainImage(this)">
                        </div>
                    @endif
                    @foreach($product->image_urls as $imageUrl)
                        <div class="col-3">
                            <img src="{{ $imageUrl }}" class="img-fluid rounded thumb-image" alt="{{ $product->name }}" onclick="changeMainImage(this)">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="product-details">
                <div class="mb-2">
                    <span class="badge bg-secondary">{{ $product->category->name }}</span>
                    @if($product->is_featured)
                        <span class="badge bg-warning">Featured</span>
                    @endif
                </div>

                <h1 class="h3 mb-3">{{ $product->name }}</h1>

                @if($product->short_description)
                    <p class="lead text-muted mb-3">{{ $product->short_description }}</p>
                @endif

                <!-- Price -->
                <div class="price-section mb-4">
                    @if($product->getOfferDetails()['discounted_price'])
                        <h3 class="text-primary d-inline">₹{{ number_format($product->getOfferDetails()['discounted_price'], 2) }}</h3>
                        <span class="h5 text-muted text-decoration-line-through ms-2">₹{{ number_format($product->getOfferDetails()['original_price'], 2) }}</span>
                        <span class="badge bg-danger ms-2">{{ $product->getOfferDetails()['discount_percentage'] }}% OFF</span>
                    @else
                        <h3 class="text-primary">₹{{ number_format($product->getOfferDetails()['price'], 2) }}</h3>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="product-info mb-4">
                    @if($product->weight)
                        <p><strong>Weight:</strong> {{ $product->weight }} {{ $product->weight_unit }}</p>
                    @endif
                    @if($product->sku)
                        <p><strong>SKU:</strong> {{ $product->sku }}</p>
                    @endif
                    <p><strong>Stock:</strong> 
                        @if($product->stock > 10)
                            <span class="text-success">In Stock ({{ $product->stock }} available)</span>
                        @elseif($product->stock > 0)
                            <span class="text-warning">Limited Stock ({{ $product->stock }} left)</span>
                        @else
                            <span class="text-danger">Out of Stock</span>
                        @endif
                    </p>
                </div>

                <!-- Add to Cart -->
                @if($product->isInStock())
                <div class="add-to-cart mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="{{ $product->stock }}">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <button onclick="addToCartWithQuantity()" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> This product is currently out of stock.
                </div>
                @endif

                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h6>Share this product:</h6>
                    <a href="https://wa.me/?text=Check out this amazing product: {{ $product->name }} - {{ url()->current() }}" class="btn btn-success btn-sm me-2" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" class="btn btn-primary btn-sm me-2" target="_blank">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <button onclick="copyToClipboard()" class="btn btn-secondary btn-sm">
                        <i class="fas fa-link"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Product Description</h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">Related Products</h4>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        @if($relatedProduct->featured_image)
                            <img src="{{ $relatedProduct->featured_image_url }}" class="card-img-top" alt="{{ $relatedProduct->name }}" style="height: 200px; object-fit: cover;">
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    @if($relatedProduct->discount_price)
                                        <span class="h6 text-primary">₹{{ number_format($relatedProduct->discount_price, 2) }}</span>
                                        <small class="text-muted text-decoration-line-through">₹{{ number_format($relatedProduct->price, 2) }}</small>
                                    @else
                                        <span class="h6 text-primary">₹{{ number_format($relatedProduct->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="product-actions">
                                    @if($relatedProduct->isInStock())
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="input-group input-group-sm quantity-selector">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity({{ $relatedProduct->id }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" id="quantity-{{ $relatedProduct->id }}" value="1" min="1" max="{{ $relatedProduct->stock }}" style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity({{ $relatedProduct->id }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('product', $relatedProduct->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                        @if($relatedProduct->isInStock())
                                            <button onclick="addToCartWithQuantity({{ $relatedProduct->id }})" class="btn btn-primary btn-sm flex-grow-1">
                                                <i class="fas fa-cart-plus"></i> Add
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-times"></i> Out of Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.thumb-image {
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.2s;
}

.thumb-image:hover,
.thumb-image.active {
    border-color: var(--primary-color);
}

.main-image {
    max-height: 500px;
    object-fit: cover;
}

.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-3px);
}
</style>

@push('scripts')
<script>
function changeMainImage(img) {
    document.getElementById('mainImage').src = img.src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumb-image').forEach(thumb => {
        thumb.classList.remove('active');
    });
    img.classList.add('active');
}

// For the main product page, we use simple functions without product ID
function incrementQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    
    quantityInput.value = currentValue + 1;
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
        showToast('Link copied to clipboard!', 'success');
    });
}
</script>
@endpush
@endsection
