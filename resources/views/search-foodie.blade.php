@extends('layouts.app-foodie')

@section('title', 'Search Results for "' . $query . '" - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', 'Search results for crackers and fireworks.')

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Search Results</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">
                @if($products->total() > 0)
                    Found {{ $products->total() }} results for "{{ $query }}"
                @else
                    No results found for "{{ $query }}"
                @endif
            </p>
        </div>
    </div>
</section>

<!-- Search Results -->
<section style="padding: 60px 0; background: var(--background);">
    <div class="container">
        @if($products->count() > 0)
        <div class="row g-4" id="products-grid">
            @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="food-card">
                    <div class="food-card-cart" onclick="addToCart({{ $product->id }})">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; background: linear-gradient(135deg, #f5f5f5, #e0e0e0); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
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
                            @elseif($product->discount_price && $product->discount_price < $product->price)
                                <span style="position: absolute; top: 10px; left: 10px; background: var(--danger-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                                </span>
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
            {{ $products->appends(['q' => $query])->links() }}
        </div>
        @endif
        
        @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <div style="font-size: 5rem; color: var(--text-secondary); margin-bottom: 1rem;">🔍</div>
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">No products found</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Try searching with different keywords</p>
            
            <!-- Search Suggestions -->
            <div style="max-width: 600px; margin: 0 auto 2rem;">
                <h5 style="color: var(--text-primary); margin-bottom: 1rem;">Search suggestions:</h5>
                <ul style="text-align: left; color: var(--text-secondary);">
                    <li>Check your spelling</li>
                    <li>Try more general keywords</li>
                    <li>Browse our categories below</li>
                </ul>
            </div>
            
            <a href="{{ route('products') }}" class="btn-foodie btn-foodie-primary">
                Browse All Products
            </a>
        </div>
        @endif
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
