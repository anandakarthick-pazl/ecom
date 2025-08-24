@extends('layouts.app-foodie')

@section('title', 'Special Offers - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', 'Amazing deals and discounts on crackers and fireworks. Limited time offers available.')

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Special Offers</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Limited time deals on premium crackers</p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section style="padding: 30px 0; background: white; border-bottom: 1px solid var(--border);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('offer.products') }}" 
                       class="btn-foodie {{ !request()->has('category') || request()->category == 'all' ? 'btn-foodie-primary' : 'btn-foodie-outline' }}"
                       style="padding: 8px 20px; font-size: 0.9rem;">
                        All Offers
                    </a>
                    @foreach($categories as $category)
                    <a href="{{ route('offer.products') }}?category={{ $category->slug }}" 
                       class="btn-foodie {{ request()->category == $category->slug ? 'btn-foodie-primary' : 'btn-foodie-outline' }}"
                       style="padding: 8px 20px; font-size: 0.9rem;">
                        {{ $category->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                    <div style="color: var(--text-secondary);">
                        <i class="fas fa-fire" style="color: var(--danger-color);"></i>
                        <span>{{ $products->total() ?? count($products) }} offers available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Offers Grid -->
<section style="padding: 60px 0; background: var(--background);">
    <div class="container">
        @if($products->count() > 0)
        <div class="row g-4" id="products-grid">
            @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="food-card">
                    @if($product->discount_price && $product->discount_price < $product->price)
                        <div style="position: absolute; top: 10px; left: 10px; background: var(--danger-color); color: white; padding: 6px 16px; border-radius: 50px; font-weight: 700; z-index: 5; font-size: 0.9rem;">
                            {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                        </div>
                    @endif
                    
                    <div class="food-card-cart" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    
                    <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; background: linear-gradient(135deg, #fff3e0, #ffe0b2); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="font-size: 4rem;">🎆</div>
                            @endif
                            
                            @if($product->stock <= 0)
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center;">
                                    <span style="background: var(--danger-color); color: white; padding: 8px 20px; border-radius: 20px; font-weight: 600;">Out of Stock</span>
                                </div>
                            @endif
                        </div>
                        <div class="food-card-content">
                            <h3 class="food-card-title">{{ Str::limit($product->name, 25) }}</h3>
                            @if($product->category)
                                <p style="color: var(--primary-color); font-size: 0.85rem; margin-bottom: 0.5rem;">{{ $product->category->name }}</p>
                            @endif
                            <p class="food-card-description">{{ Str::limit($product->description ?? 'Premium quality crackers', 50) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="food-card-price">₹{{ $product->final_price }}</span>
                                    @if($product->discount_price && $product->discount_price < $product->price)
                                        <span style="text-decoration: line-through; color: var(--text-secondary); font-size: 0.9rem; margin-left: 0.5rem;">₹{{ $product->price }}</span>
                                    @endif
                                </div>
                                <span style="background: #ffebee; color: var(--danger-color); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                    Save ₹{{ $product->price - $product->final_price }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($frontendPaginationSettings['enabled'] && method_exists($products, 'links'))
        <div class="mt-5 d-flex justify-content-center" id="pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
        @endif
        
        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;">🎁</div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">No offers available</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Check back soon for amazing deals!</p>
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                Browse All Products
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Offer Banner -->
<section style="padding: 60px 0; background: linear-gradient(135deg, #ff6b35, #f77b00);">
    <div class="container">
        <div class="text-center text-white">
            <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">Festival Season Special</h2>
            <p style="font-size: 1.25rem; margin-bottom: 2rem;">Get extra discounts on bulk orders</p>
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary" style="background: white; color: var(--primary-color);">
                Shop Now
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // AJAX pagination
    @if($frontendPaginationSettings['enabled'])
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        $.get(url, function(data) {
            $('#products-grid').html($(data).find('#products-grid').html());
            $('#pagination-container').html($(data).find('#pagination-container').html());
            
            // Scroll to top of products
            $('html, body').animate({
                scrollTop: $('#products-grid').offset().top - 100
            }, 500);
        });
    });
    @endif
</script>
@endpush
