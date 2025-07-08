@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name . ' - Herbal Bliss')
@section('meta_description', $category->meta_description ?: $category->description)
@section('meta_keywords', $category->meta_keywords)

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
            </div>
        </div>
    </div>

    @if($products->count() > 0)
    <div class="row">
        @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card">
                @if($product->featured_image)
                    <img src="{{ $product->featured_image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-image fa-2x text-muted"></i>
                    </div>
                @endif
                
                @if($product->discount_percentage > 0)
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-danger">{{ $product->discount_percentage }}% OFF</span>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">{{ $product->name }}</h6>
                    <p class="card-text text-muted small">{{ Str::limit($product->short_description, 80) }}</p>
                    
                    <div class="mt-auto">
                        <div class="price-section mb-2">
                            @if($product->discount_price)
                                <span class="h6 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                            @else
                                <span class="h6 text-primary">₹{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        <div class="product-actions">
                            @if($product->isInStock())
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="input-group input-group-sm quantity-selector">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQuantity({{ $product->id }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" style="max-width: 60px;">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQuantity({{ $product->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">View</a>
                                @if($product->isInStock())
                                    <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-sm flex-grow-1">
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

    <!-- Pagination -->
    @if($enablePagination && isset($products) && method_exists($products, 'appends'))
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
</style>
@endsection
