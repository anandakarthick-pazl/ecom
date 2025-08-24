<div class="row g-4">
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
                    
                    @if($product->is_featured)
                        <span style="position: absolute; top: 10px; right: 10px; background: var(--warning-color); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-star"></i> Featured
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
