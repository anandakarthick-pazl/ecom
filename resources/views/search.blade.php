@extends('layouts.app')

@section('title', 'Search Results - Herbal Bliss')

@push('styles')
<style>
/* Import compact grid styles for search page */
.products-grid-compact {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)) !important;
    gap: 0.5rem !important;
    margin-bottom: 1.5rem !important;
}

.products-grid-compact .product-card {
    font-size: 0.7rem !important;
    border-radius: 8px !important;
}

.products-grid-compact .product-image-container {
    height: 80px !important;
}

.products-grid-compact .product-content {
    padding: 0.4rem !important;
}

.products-grid-compact .product-title {
    font-size: 0.7rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0.2rem !important;
    height: 2.2rem !important;
    overflow: hidden !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
}

.products-grid-compact .product-category {
    font-size: 0.6rem !important;
    margin-bottom: 0.2rem !important;
}

.products-grid-compact .product-description {
    display: none !important;
}

.products-grid-compact .current-price {
    font-size: 0.8rem !important;
    font-weight: 700 !important;
}

.products-grid-compact .original-price {
    font-size: 0.65rem !important;
}

.products-grid-compact .btn-add-cart {
    padding: 0.3rem 0.5rem !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
    white-space: nowrap !important; /* Prevent text wrapping */
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.products-grid-compact .quantity-selector {
    margin-bottom: 0.2rem !important;
    gap: 0.25rem !important;
}

.products-grid-compact .qty-btn {
    width: 20px !important;
    height: 20px !important;
    font-size: 0.6rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .qty-input {
    width: 30px !important;
    height: 20px !important;
    font-size: 0.65rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .badge-discount {
    font-size: 0.6rem !important;
    padding: 0.15rem 0.3rem !important;
    border-radius: 4px !important;
}

.products-grid-compact .offer-info {
    display: none !important; /* Completely hide offer info in compact grid */
}

.products-grid-compact .product-footer {
    margin-top: 0.3rem !important;
}

.products-grid-compact .price-section {
    margin-bottom: 0.5rem !important;
}

@media (max-width: 768px) {
    .products-grid-compact {
        grid-template-columns: repeat(auto-fill, minmax(95px, 1fr)) !important;
        gap: 0.4rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 70px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.65rem !important;
        height: 2rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.75rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.25rem 0.4rem !important;
        font-size: 0.6rem !important;
    }
}

@media (max-width: 576px) {
    .products-grid-compact {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 0.3rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 60px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.6rem !important;
        height: 1.8rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.7rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.2rem 0.3rem !important;
        font-size: 0.55rem !important;
    }
    
    .products-grid-compact .qty-btn {
        width: 18px !important;
        height: 18px !important;
        font-size: 0.55rem !important;
    }
    
    .products-grid-compact .qty-input {
        width: 25px !important;
        height: 18px !important;
        font-size: 0.6rem !important;
    }
}

@media (max-width: 480px) {
    .products-grid-compact {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.25rem !important;
    }
    
    .products-grid-compact .product-image-container {
        height: 55px !important;
    }
    
    .products-grid-compact .product-title {
        font-size: 0.55rem !important;
        height: 1.6rem !important;
    }
    
    .products-grid-compact .current-price {
        font-size: 0.65rem !important;
    }
    
    .products-grid-compact .btn-add-cart {
        padding: 0.15rem 0.25rem !important;
        font-size: 0.5rem !important;
    }
}
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2>Search Results for "{{ $query }}"</h2>
            <p class="text-muted">{{ ($frontendPaginationSettings['enabled'] ?? true) && method_exists($products, 'total') ? $products->total() : $products->count() }} product(s) found</p>
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
        {{ $products->appends(['q' => $query])->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4"></i>
        <h4>No products found</h4>
        <p class="text-muted mb-4">We couldn't find any products matching "{{ $query }}".</p>
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
</style>
@endsection
