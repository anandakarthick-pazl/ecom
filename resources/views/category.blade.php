@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name . ' - Herbal Bliss')
@section('meta_description', $category->meta_description ?: $category->description)
@section('meta_keywords', $category->meta_keywords)

@push('styles')
<style>
/* Import compact grid styles */
.products-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

/* Ultra Small Product Cards for Category Page */
.products-grid-compact .product-card {
    font-size: 0.7rem;
    border-radius: 8px;
}

.products-grid-compact .product-image-container {
    height: 80px !important;
}

.products-grid-compact .product-content {
    padding: 0.4rem;
}

.products-grid-compact .product-title {
    font-size: 0.7rem;
    line-height: 1.1;
    margin-bottom: 0.2rem;
    height: 2.2rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.products-grid-compact .product-category {
    font-size: 0.6rem;
    margin-bottom: 0.2rem;
}

.products-grid-compact .product-description {
    display: none;
}

.products-grid-compact .current-price {
    font-size: 0.8rem;
    font-weight: 700;
}

.products-grid-compact .original-price {
    font-size: 0.65rem;
}

.products-grid-compact .btn-add-cart {
    padding: 0.3rem 0.5rem;
    font-size: 0.65rem;
    border-radius: 4px;
    white-space: nowrap !important; /* Prevent text wrapping */
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.products-grid-compact .quantity-selector {
    margin-bottom: 0.2rem;
    gap: 0.25rem;
}

.products-grid-compact .qty-btn {
    width: 20px;
    height: 20px;
    font-size: 0.6rem;
    border-radius: 4px;
}

.products-grid-compact .qty-input {
    width: 30px;
    height: 20px;
    font-size: 0.65rem;
    border-radius: 4px;
}

.products-grid-compact .badge-discount {
    font-size: 0.6rem;
    padding: 0.15rem 0.3rem;
    border-radius: 4px;
}

.products-grid-compact .offer-info {
    display: none !important; /* Completely hide offer info in compact grid */
}

.products-grid-compact .product-footer {
    margin-top: 0.3rem;
}

.products-grid-compact .price-section {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .products-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
        gap: 0.4rem;
    }
    
    .products-grid-compact .product-image-container {
        height: 70px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.65rem;
        height: 2rem;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.75rem;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.25rem 0.4rem;
        font-size: 0.6rem;
    }
}

@media (max-width: 576px) {
    .products-grid-compact {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.3rem;
    }
    
    .products-grid-compact .product-image-container {
        height: 60px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.6rem;
        height: 1.8rem;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.7rem;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.2rem 0.3rem;
        font-size: 0.55rem;
    }
}

@media (max-width: 480px) {
    .products-grid-compact {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.25rem;
    }
    
    .products-grid-compact .product-image-container {
        height: 55px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.55rem;
        height: 1.6rem;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.65rem;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.15rem 0.25rem;
        font-size: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="category-header mb-4">
                <h1 class="h2">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="lead text-muted">{{ $category->description }}</p>
                @endif
                
                {{-- Show active category offers --}}
                @if(isset($categoryOffers) && $categoryOffers->count() > 0)
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading"><i class="fas fa-fire"></i> Special Offers on {{ $category->name }}!</h5>
                        @foreach($categoryOffers as $offer)
                            <div class="mb-2">
                                <span class="badge bg-danger me-2">{{ $offer->name }}</span>
                                <span class="fw-bold">
                                    @if($offer->discount_type === 'percentage')
                                        Get {{ $offer->value }}% OFF
                                    @else
                                        Get ₹{{ number_format($offer->value, 2) }} OFF
                                    @endif
                                </span>
                                @if($offer->minimum_amount)
                                    <small class="text-muted">(on orders above ₹{{ number_format($offer->minimum_amount, 2) }})</small>
                                @endif
                                @if($offer->code)
                                    <span class="badge bg-info ms-2">Code: {{ $offer->code }}</span>
                                @endif
                        @include('partials.product-card-modern', ['product' => $product])
                        @endforeach
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Valid till {{ $categoryOffers->first()->end_date->format('d M Y') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>

        @if($products->count() > 0)
        <div class="products-grid-compact">
            @foreach($products as $product)
                @include('partials.product-card-modern', ['product' => $product])
            @endforeach
        </div>

    <!-- Pagination -->
    @if(($frontendPaginationSettings['enabled'] ?? true) && isset($products) && method_exists($products, 'appends'))
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
        <h4>No products found</h4>
        <p class="text-muted mb-4">We don't have any products in this category yet.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="fas fa-home"></i> Go Home
        </a>
    </div>
    @endif
</div>

<style>
.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.offer-product {
    border: 2px solid #28a745 !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8fff9 100%);
}

.offer-product .card-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-color: #2196f3;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545, #e57373) !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.badge.bg-success {
    background: linear-gradient(135deg, #28a745, #4caf50) !important;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}
</style>

@push('scripts')
<script>
// Quantity selector functions
function incrementQuantity(productId) {
    const input = document.getElementById('quantity-' + productId);
    const max = parseInt(input.getAttribute('max'));
    let value = parseInt(input.value);
    if (value < max) {
        input.value = value + 1;
    }
}

function decrementQuantity(productId) {
    const input = document.getElementById('quantity-' + productId);
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function addToCartWithQuantity(productId) {
    const quantity = parseInt(document.getElementById('quantity-' + productId).value);
    
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
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
@endpush
@endsection
