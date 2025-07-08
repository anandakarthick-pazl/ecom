@extends('admin.layouts.app')

@section('title', 'Product: ' . $product->name)
@section('page_title', 'Product: ' . $product->name)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-secondary" target="_blank">
        <i class="fas fa-external-link-alt"></i> View on Site
    </a>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $product->name }}</h4>
                        <p class="text-muted mb-2">{{ $product->slug }}</p>
                        <span class="badge bg-secondary">{{ $product->category->name }}</span>
                        @if($product->is_featured)
                            <span class="badge bg-warning">Featured</span>
                        @endif
                        @if($product->sku)
                            <span class="badge bg-info">SKU: {{ $product->sku }}</span>
                        @endif
                        
                        @if($product->short_description)
                            <div class="mt-3 mb-3">
                                <strong>Short Description:</strong>
                                <p>{{ $product->short_description }}</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p>{{ $product->description }}</p>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Price:</strong>
                                @if($product->discount_price)
                                    <span class="h5 text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                    <span class="text-muted text-decoration-line-through ms-2">₹{{ number_format($product->price, 2) }}</span>
                                    <span class="badge bg-danger ms-2">{{ $product->discount_percentage }}% OFF</span>
                                @else
                                    <span class="h5 text-primary">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <strong>Stock:</strong>
                                <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $product->stock }} units
                                </span>
                            </div>
                        </div>
                        
                        @if($product->weight)
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Weight:</strong> {{ $product->weight }} {{ $product->weight_unit }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Created:</strong> {{ $product->created_at->format('M d, Y') }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Sort Order:</strong> {{ $product->sort_order }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        @if($product->featured_image)
                            <img src="{{ $product->featured_image_url }}" class="img-fluid rounded" alt="{{ $product->name }}">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 250px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($product->images && count($product->images) > 0)
                <hr>
                <h6>Additional Images</h6>
                <div class="row">
                    @foreach($product->image_urls as $imageUrl)
                        <div class="col-md-3 mb-3">
                            <img src="{{ $imageUrl }}" class="img-fluid rounded" alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div>
                @endif
                
                @if($product->meta_title || $product->meta_description || $product->meta_keywords)
                <hr>
                <h6>SEO Information</h6>
                @if($product->meta_title)
                    <p><strong>Meta Title:</strong> {{ $product->meta_title }}</p>
                @endif
                @if($product->meta_description)
                    <p><strong>Meta Description:</strong> {{ $product->meta_description }}</p>
                @endif
                @if($product->meta_keywords)
                    <p><strong>Meta Keywords:</strong> {{ $product->meta_keywords }}</p>
                @endif
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                    <a href="{{ route('product', $product->slug) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View on Website
                    </a>
                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i> 
                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $product->is_featured ? 'outline-warning' : 'warning' }} w-100">
                            <i class="fas fa-star"></i> 
                            {{ $product->is_featured ? 'Remove Featured' : 'Mark Featured' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6>Product Statistics</h6>
                @if($product->orderItems()->count() > 0)
                    <p><strong>Total Sales:</strong> {{ $product->orderItems()->sum('quantity') }} units</p>
                    <p><strong>Revenue:</strong> ₹{{ number_format($product->orderItems()->sum('total'), 2) }}</p>
                    <p><strong>Orders:</strong> {{ $product->orderItems()->distinct('order_id')->count() }}</p>
                @else
                    <p class="text-muted">No sales yet</p>
                @endif
                
                <hr>
                
                <h6>Stock Alert</h6>
                @if($product->stock == 0)
                    <div class="alert alert-danger py-2">
                        <i class="fas fa-exclamation-triangle"></i> Out of Stock
                    </div>
                @elseif($product->stock <= 5)
                    <div class="alert alert-warning py-2">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock ({{ $product->stock }} left)
                    </div>
                @else
                    <div class="alert alert-success py-2">
                        <i class="fas fa-check-circle"></i> In Stock ({{ $product->stock }} units)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
