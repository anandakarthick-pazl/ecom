@extends('admin.layouts.app')

@section('title', 'Banners')
@section('page_title', 'Banners')

@section('page_actions')
<a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Banner
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($banners->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Banner</th>
                        <th>Position</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $banner)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ Storage::url($banner->image) }}" class="me-2 rounded" style="width: 60px; height: 40px; object-fit: cover;" alt="{{ $banner->title }}">
                                <div>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->link_url)
                                        <br><small class="text-muted">{{ Str::limit($banner->link_url, 40) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($banner->position) }}</span>
                        </td>
                        <td>
                            @if($banner->start_date && $banner->end_date)
                                {{ $banner->start_date->format('M d') }} - {{ $banner->end_date->format('M d, Y') }}
                            @elseif($banner->start_date)
                                From {{ $banner->start_date->format('M d, Y') }}
                            @elseif($banner->end_date)
                                Until {{ $banner->end_date->format('M d, Y') }}
                            @else
                                <span class="text-muted">Always Active</span>
                            @endif
                        </td>
                        <td>
                            @if($banner->isActive())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.banners.show', $banner) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.banners.toggle-status', $banner) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $banner->is_active ? 'warning' : 'success' }}" title="{{ $banner->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $banner->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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
            {{ $banners->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-image fa-3x text-muted mb-3"></i>
            <h5>No banners found</h5>
            <p class="text-muted">Create your first banner to display on the homepage.</p>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Banner
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
