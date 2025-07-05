@if($products->count() > 0)
    <div class="row">
        @foreach($products as $product)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card">
                @if($product->featured_image)
                    <img src="{{ Storage::url($product->featured_image) }}" 
                         class="card-img-top" 
                         alt="{{ $product->name }}" 
                         style="height: 250px; object-fit: cover;">
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
                
                @if($product->is_featured)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-warning">
                            <i class="fas fa-star"></i> Featured
                        </span>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <small class="text-muted">{{ $product->category->name }}</small>
                    </div>
                    <h6 class="card-title">{{ $product->name }}</h6>
                    <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                    
                    <div class="mt-auto">
                        <div class="price-section mb-2">
                            @if($product->discount_price)
                                <span class="h6 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($product->price, 2) }}</small>
                            @else
                                <span class="h6 text-primary">₹{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($product->stock <= 5)
                            <div class="mb-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Only {{ $product->stock }} left in stock
                                </small>
                            </div>
                        @endif
                        
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
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if($product->isInStock())
                                    <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                        <i class="fas fa-times me-1"></i>Out of Stock
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
@else
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h3 class="text-muted mb-3">No Products Found</h3>
                <p class="text-muted mb-4">Try adjusting your filters or check back later.</p>
            </div>
        </div>
    </div>
@endif
