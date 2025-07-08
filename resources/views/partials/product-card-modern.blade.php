<!-- Modern Product Card -->
<div class="product-card {{ isset($offer) && $offer ? 'offer-card' : '' }}">
    <div class="product-image-container">
        @if($product->featured_image)
            <img src="{{ $product->featured_image_url }}" class="product-image" alt="{{ $product->name }}">
        @else
            <div class="product-placeholder">
                <i class="fas fa-image"></i>
            </div>
        @endif
        
        @if($product->discount_percentage > 0)
            <div class="product-badge">
                <span class="badge-discount">{{ $product->discount_percentage }}% OFF</span>
            </div>
        @endif
        
        @if(isset($featured) && $featured)
            <div class="badge-featured">
                <i class="fas fa-star"></i>
            </div>
        @endif
        
        <div class="product-overlay">
            <div class="quick-actions">
                <a href="{{ route('product', $product->slug) }}" class="quick-btn" title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                <button onclick="addToCart({{ $product->id }})" class="quick-btn" title="Add to Cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="product-content">
        <div class="product-category">{{ $product->category->name }}</div>
        <h3 class="product-title">
            <a href="{{ route('product', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <p class="product-description">{{ Str::limit($product->short_description ?? '', 60) }}</p>
        
        <div class="product-footer">
            <div class="price-section">
                @if($product->discount_price)
                    <span class="current-price">₹{{ number_format($product->discount_price, 2) }}</span>
                    <span class="original-price">₹{{ number_format($product->price, 2) }}</span>
                @else
                    <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            <div class="product-actions">
                @if($product->isInStock())
                    <div class="quantity-selector">
                        <button class="qty-btn" onclick="decrementQuantity({{ $product->id }})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="qty-input" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}">
                        <button class="qty-btn" onclick="incrementQuantity({{ $product->id }})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn-add-cart {{ isset($offer) && $offer ? 'offer' : '' }}">
                        <i class="fas fa-cart-plus"></i>
                        <span>{{ isset($offer) && $offer ? 'Grab Deal' : 'Add to Cart' }}</span>
                    </button>
                @else
                    <button class="btn-out-stock" disabled>
                        <i class="fas fa-ban"></i>
                        <span>Out of Stock</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Product Card Styles - Ensure these work if not already defined in parent */
.product-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.offer-card {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box, 
                linear-gradient(45deg, rgba(255, 107, 107, 0.2), rgba(239, 68, 68, 0.2)) border-box;
}

.offer-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(255, 107, 107, 0.2);
}

.product-image-container {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 2rem;
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

.badge-discount {
    background: linear-gradient(45deg, #ef4444, #dc2626);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-featured {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.9);
    color: #f59e0b;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    z-index: 2;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 3;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.quick-actions {
    display: flex;
    gap: 0.75rem;
}

.quick-btn {
    background: #ffffff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.quick-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    transform: scale(1.1);
}

.product-content {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 0.75rem;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.product-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.product-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.4;
    margin-bottom: 1rem;
    flex: 1;
}

.product-footer {
    margin-top: auto;
}

.price-section {
    margin-bottom: 1rem;
}

.current-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.original-price {
    font-size: 0.875rem;
    text-decoration: line-through;
    color: #6b7280;
    margin-left: 0.5rem;
}

.product-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.qty-btn {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1f2937;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.qty-btn:hover {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border-color: {{ $globalCompany->primary_color ?? '#2563eb' }};
}

.qty-input {
    width: 50px;
    height: 32px;
    text-align: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 500;
}

.btn-add-cart {
    background: {{ $globalCompany->primary_color ?? '#2563eb' }};
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-add-cart:hover {
    background: {{ $globalCompany->secondary_color ?? '#10b981' }};
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.btn-add-cart.offer {
    background: linear-gradient(45deg, #ef4444, #dc2626);
}

.btn-add-cart.offer:hover {
    background: linear-gradient(45deg, #dc2626, #b91c1c);
}

.btn-out-stock {
    background: #6b7280;
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0.7;
    cursor: not-allowed;
}
</style>