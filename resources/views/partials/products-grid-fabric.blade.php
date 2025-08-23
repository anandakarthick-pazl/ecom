<!-- Products Grid for Fabric Theme -->
<div class="row">
    @forelse($products as $product)
    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
        <div style="background: white; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.08); height: 100%; position: relative;">
            <!-- Out of Stock Overlay -->
            @if($product->stock <= 0)
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 1; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <span style="background: #dc3545; color: white; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600;">Out of Stock</span>
            </div>
            @endif
            
            <div style="text-align: center;">
                <img src="{{ $product->image_url }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100%; height: 120px; object-fit: contain; margin-bottom: 0.5rem;">
                
                @if($product->discount_percentage > 0)
                    <span style="position: absolute; top: 10px; right: 10px; background: #ff6b35; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; z-index: 2;">
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
    @empty
    <div class="col-12">
        <div style="text-align: center; padding: 3rem;">
            <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
            <h4 style="color: #6c757d;">No products available</h4>
            <p style="color: #6c757d;">Please check back later for new products.</p>
            <a href="{{ route('shop') }}" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 2rem; background: #ff6b35; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                Back to Home
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
<div class="mt-4 d-flex justify-content-center">
    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>
@endif
