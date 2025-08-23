@extends('layouts.app-fabric')

@section('title', 'Search Results for "' . $query . '" - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<!-- Search Header Section -->
<section style="padding: 2rem 0; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="background: transparent; padding: 0; margin-bottom: 0.5rem;">
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}" style="color: white; text-decoration: none;">Home</a></li>
                        <li class="breadcrumb-item active" style="color: #ffd93d;">Search Results</li>
                    </ol>
                </nav>
                <h1 style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">
                    Search Results for "{{ $query }}"
                </h1>
                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1rem;">
                    <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 20px; color: white;">
                        <i class="fas fa-search"></i> {{ $products->total() ?? count($products) }} Results Found
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Search Form -->
                <form action="{{ route('search') }}" method="GET" style="margin-top: 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}" 
                               placeholder="Search products..." 
                               style="flex: 1; padding: 0.75rem; border: none; border-radius: 25px; font-size: 1rem;"
                               required>
                        <button type="submit" 
                                style="padding: 0.75rem 1.5rem; background: white; color: #ff6b35; border: none; border-radius: 25px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Search Suggestions -->
@if($products->count() == 0)
<section style="padding: 2rem 0; background: #fff3e0;">
    <div class="container">
        <div style="background: white; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #ff6b35;">
            <h4 style="color: #ff6b35; margin-bottom: 1rem;">
                <i class="fas fa-lightbulb"></i> Search Tips
            </h4>
            <ul style="margin: 0; padding-left: 1.5rem; color: #666;">
                <li>Check your spelling</li>
                <li>Try using more general terms</li>
                <li>Use fewer keywords</li>
                <li>Browse our categories for similar products</li>
            </ul>
        </div>
    </div>
</section>
@endif

<!-- Products Section -->
<section style="padding: 3rem 0; background: #f8f9fa; min-height: 60vh;">
    <div class="container">
        @if($products->count() > 0)
        <!-- Sort and Filter Controls -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 style="font-size: 1.8rem; font-weight: 700;">Search Results</h2>
                <p style="color: #6c757d;">Showing results for "{{ $query }}"</p>
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
        
        <!-- Products Grid -->
        <div class="row">
            @foreach($products as $product)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                <div style="background: white; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.08); height: 100%; position: relative; transition: all 0.3s;">
                    <!-- Out of Stock Overlay -->
                    @if($product->stock <= 0)
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 10; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <span style="background: #dc3545; color: white; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600;">Out of Stock</span>
                    </div>
                    @endif
                    
                    <div style="text-align: center;">
                        <a href="{{ route('product', $product->slug) }}" style="text-decoration: none;">
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 100%; height: 120px; object-fit: contain; margin-bottom: 0.5rem;">
                        </a>
                        
                        @if($product->discount_percentage > 0)
                            <span style="position: absolute; top: 10px; right: 10px; background: #ff6b35; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; z-index: 5;">
                                {{ $product->discount_percentage }}% OFF
                            </span>
                        @endif
                        
                        <!-- Highlight search term in product name -->
                        <h6 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem; height: 40px; overflow: hidden; color: #333;">
                            @php
                                $highlightedName = str_ireplace($query, '<mark style="background: #ffd93d; padding: 0 2px;">' . $query . '</mark>', $product->name);
                            @endphp
                            {!! Str::limit($highlightedName, 40) !!}
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
                            @if($product->stock > 0)
                            <div style="display: flex; gap: 0.25rem; margin-bottom: 0.5rem;">
                                <input type="number" 
                                       id="qty-{{ $product->id }}" 
                                       min="1" 
                                       max="{{ $product->stock }}"
                                       value="1" 
                                       style="width: 60%; padding: 0.25rem; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 0.9rem;">
                                <button onclick="addToCart({{ $product->id }})" 
                                        style="width: 40%; padding: 0.25rem; background: #ff6b35; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8rem; font-weight: 600;">
                                    ADD
                                </button>
                            </div>
                            @else
                            <button disabled
                                    style="width: 100%; padding: 0.5rem; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: not-allowed; font-size: 0.9rem; font-weight: 600;">
                                Out of Stock
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
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
                        <li class="page-item"><a class="page-link" href="{{ $products->appends(['q' => $query])->previousPageUrl() }}">Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <li class="page-item active"><span class="page-link" style="background-color: #ff6b35; border-color: #ff6b35;">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}&q={{ $query }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($products->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $products->appends(['q' => $query])->nextPageUrl() }}">Next</a></li>
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
        @else
        <!-- No Results Found -->
        <div class="text-center" style="padding: 4rem 0;">
            <i class="fas fa-search" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
            <h3 style="color: #6c757d;">No products found</h3>
            <p style="color: #6c757d; margin-bottom: 2rem;">
                We couldn't find any products matching "{{ $query }}"
            </p>
            
            <!-- Quick Actions -->
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('products') }}" 
                   style="padding: 0.75rem 2rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    Browse All Products
                </a>
                <a href="{{ route('shop') }}" 
                   style="padding: 0.75rem 2rem; background: transparent; color: #ff6b35; text-decoration: none; border: 2px solid #ff6b35; border-radius: 8px; font-weight: 600;">
                    Go to Home
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Popular Categories -->
@php
    $popularCategories = \App\Models\Category::active()
        ->whereHas('products')
        ->limit(8)
        ->get();
@endphp

@if($popularCategories->count() > 0)
<section style="padding: 3rem 0; background: white;">
    <div class="container">
        <h3 style="text-align: center; font-size: 1.8rem; font-weight: 700; margin-bottom: 2rem;">Browse Categories</h3>
        <div class="row">
            @foreach($popularCategories as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('category', $category->slug) }}" style="text-decoration: none;">
                    <div style="background: #f8f9fa; padding: 1.5rem; text-align: center; border-radius: 8px; transition: all 0.3s;">
                        <i class="fas fa-box-open" style="font-size: 2rem; color: #ff6b35; margin-bottom: 0.5rem;"></i>
                        <h5 style="color: #212529; margin: 0;">{{ $category->name }}</h5>
                        <small style="color: #6c757d;">{{ $category->products()->active()->count() }} Products</small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Recent Searches Widget (Optional) -->
<section style="padding: 2rem 0; background: #f8f9fa;">
    <div class="container">
        <div style="text-align: center;">
            <h4 style="font-size: 1.2rem; color: #6c757d; margin-bottom: 1rem;">Try searching for:</h4>
            <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                @php
                    $suggestions = ['Crackers', 'Sparklers', 'Rockets', 'Ground Chakkars', 'Flower Pots', 'Bijili'];
                @endphp
                @foreach($suggestions as $suggestion)
                <a href="{{ route('search', ['q' => $suggestion]) }}" 
                   style="padding: 0.5rem 1rem; background: white; color: #ff6b35; text-decoration: none; border: 1px solid #ff6b35; border-radius: 20px; font-size: 0.9rem; transition: all 0.3s;">
                    {{ $suggestion }}
                </a>
                @endforeach
            </div>
        </div>
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
    
    // Add hover effect to product cards
    const productCards = document.querySelectorAll('.col-lg-2 > div');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.15)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 4px rgba(0,0,0,0.08)';
        });
    });
    
    // Add hover effect to suggestion links
    const suggestionLinks = document.querySelectorAll('a[href*="search?q="]');
    suggestionLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.background = '#ff6b35';
            this.style.color = 'white';
        });
        link.addEventListener('mouseleave', function() {
            this.style.background = 'white';
            this.style.color = '#ff6b35';
        });
    });
});
</script>
@endsection
