@extends('admin.layouts.app')

@section('title', 'Categories')
@section('page_title', 'Categories')

@section('page_actions')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Category
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent Category</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $category->name }}">
                                @endif
                                <div>
                                    <strong>{{ $category->name }}</strong>
                                    <br><small class="text-muted">{{ $category->slug }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($category->parent)
                                <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-muted">Root Category</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $category->products_count ?? $category->products->count() }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $categories->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h5>No categories found</h5>
            <p class="text-muted">Start by creating your first category.</p>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Category
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
