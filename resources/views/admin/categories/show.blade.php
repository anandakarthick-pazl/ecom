@extends('admin.layouts.app')

@section('title', 'Category: ' . $category->name)
@section('page_title', 'Category: ' . $category->name)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
                        <h4>{{ $category->name }}</h4>
                        <p class="text-muted mb-2">{{ $category->slug }}</p>
                        
                        @if($category->parent)
                            <p><strong>Parent Category:</strong> {{ $category->parent->name }}</p>
                        @endif
                        
                        @if($category->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p>{{ $category->description }}</p>
                            </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Status:</strong>
                                @if($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <strong>Sort Order:</strong> {{ $category->sort_order }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Products:</strong> {{ $category->products->count() }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Created:</strong> {{ $category->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" class="img-fluid rounded" alt="{{ $category->name }}">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($category->meta_title || $category->meta_description || $category->meta_keywords)
                <hr>
                <h6>SEO Information</h6>
                <div class="row">
                    @if($category->meta_title)
                    <div class="col-12 mb-2">
                        <strong>Meta Title:</strong> {{ $category->meta_title }}
                    </div>
                    @endif
                    
                    @if($category->meta_description)
                    <div class="col-12 mb-2">
                        <strong>Meta Description:</strong> {{ $category->meta_description }}
                    </div>
                    @endif
                    
                    @if($category->meta_keywords)
                    <div class="col-12 mb-2">
                        <strong>Meta Keywords:</strong> {{ $category->meta_keywords }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Products in this category -->
        @if($category->products->count() > 0)
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Products in this Category</h6>
                <span class="badge bg-primary">{{ $category->products->count() }} products</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->featured_image)
                                            <img src="{{ $product->featured_image_url }}" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->is_featured)
                                                <span class="badge bg-warning ms-1">Featured</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->discount_price)
                                        <span class="text-primary">₹{{ number_format($product->discount_price, 2) }}</span>
                                        <br><small class="text-muted text-decoration-line-through">₹{{ number_format($product->price, 2) }}</small>
                                    @else
                                        ₹{{ number_format($product->price, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Category
                    </a>
                    <a href="{{ route('admin.products.create') }}?category={{ $category->id }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    <a href="{{ route('category', $category->slug) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View on Site
                    </a>
                </div>
            </div>
        </div>
        
        @if($category->children->count() > 0)
        <div class="card mt-3">
            <div class="card-body">
                <h6>Subcategories</h6>
                @foreach($category->children as $child)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $child->name }}</span>
                        <div>
                            <span class="badge bg-info">{{ $child->products->count() }}</span>
                            <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
