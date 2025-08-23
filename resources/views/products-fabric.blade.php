@extends('layouts.app-fabric')

@section('title', 'Products - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<!-- Products Page with Pagination -->
<section style="padding: 3rem 0; background: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 style="font-size: 2rem; font-weight: 700;">All Products</h2>
            </div>
            <div class="col-md-6 text-end">
                <!-- Pagination Controls -->
                @if($frontendPaginationSettings['enabled'] ?? false)
                    <div class="d-inline-flex align-items-center gap-2">
                        <label style="font-size: 0.9rem;">Show:</label>
                        <select onchange="changePageSize(this.value)" style="padding: 0.25rem 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                            @foreach(($frontendPaginationControls['allowed_values'] ?? null) ?: [10, 20, 50, 100] as $option)
                                <option value="{{ $option }}" {{ request('per_page', $frontendPaginationSettings['per_page'] ?? 50) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <span style="font-size: 0.9rem;">per page</span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Categories Filter -->
        @if($categories->count() > 0)
        <div class="mb-4">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('products') }}" 
                   style="padding: 0.5rem 1rem; background: {{ !request('category') ? '#ff6b35' : '#f8f9fa' }}; color: {{ !request('category') ? 'white' : '#212529' }}; text-decoration: none; border-radius: 20px; font-size: 0.9rem;">
                    All Products
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products', ['category' => $category->slug]) }}" 
                       style="padding: 0.5rem 1rem; background: {{ request('category') == $category->slug ? '#ff6b35' : '#f8f9fa' }}; color: {{ request('category') == $category->slug ? 'white' : '#212529' }}; text-decoration: none; border-radius: 20px; font-size: 0.9rem;">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="row">
            @forelse($products as $product)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <div style="background: white; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.08); height: 100%; position: relative;">
                    <div style="text-align: center;">
                        <a href="{{ route('product', $product->slug) }}" style="text-decoration: none;">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 100%; height: 120px; object-fit: contain; margin-bottom: 0.5rem;">
                        </a>
                        
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
                <div class="text-center" style="padding: 4rem 0;">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                    <h3>No products found</h3>
                    <p>Try adjusting your filters or browse all products.</p>
                    <a href="{{ route('products') }}" class="btn btn-primary">View All Products</a>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination Links -->
        @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
        <div class="mt-4">
            <nav>
                <ul class="pagination justify-content-center">
                    {{-- Previous Page Link --}}
                    @if ($products->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}">Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($products->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
            
            <!-- Showing results text -->
            <div class="text-center mt-2">
                <small class="text-muted">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                </small>
            </div>
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
<script>
// Change page size
function changePageSize(size) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', size);
    url.searchParams.set('page', 1); // Reset to first page
    window.location.href = url.toString();
}

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
