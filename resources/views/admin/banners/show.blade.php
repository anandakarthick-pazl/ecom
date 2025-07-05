@extends('admin.layouts.app')

@section('title', 'Banner: ' . $banner->title)
@section('page_title', 'Banner: ' . $banner->title)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4>{{ $banner->title }}</h4>
                
                <div class="mb-4">
                    <img src="{{ Storage::url($banner->image) }}" class="img-fluid rounded" alt="{{ $banner->title }}" style="max-height: 300px;">
                </div>
                
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <strong>Position:</strong> {{ ucfirst($banner->position) }}
                    </div>
                    <div class="col-sm-6">
                        <strong>Sort Order:</strong> {{ $banner->sort_order }}
                    </div>
                </div>
                
                @if($banner->link_url)
                <div class="mb-3">
                    <strong>Link URL:</strong> 
                    <a href="{{ $banner->link_url }}" target="_blank">{{ $banner->link_url }}</a>
                </div>
                @endif
                
                @if($banner->alt_text)
                <div class="mb-3">
                    <strong>Alt Text:</strong> {{ $banner->alt_text }}
                </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <strong>Status:</strong>
                        @if($banner->isActive())
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <strong>Created:</strong> {{ $banner->created_at->format('M d, Y') }}
                    </div>
                </div>
                
                @if($banner->start_date || $banner->end_date)
                <div class="row mb-3">
                    @if($banner->start_date)
                    <div class="col-sm-6">
                        <strong>Start Date:</strong> {{ $banner->start_date->format('M d, Y') }}
                    </div>
                    @endif
                    @if($banner->end_date)
                    <div class="col-sm-6">
                        <strong>End Date:</strong> {{ $banner->end_date->format('M d, Y') }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Banner
                    </a>
                    
                    <form action="{{ route('admin.banners.toggle-status', $banner) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $banner->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $banner->is_active ? 'eye-slash' : 'eye' }}"></i> 
                            {{ $banner->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash"></i> Delete Banner
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6>Banner Status</h6>
                @if($banner->start_date && $banner->start_date > today())
                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i> Scheduled to start on {{ $banner->start_date->format('M d, Y') }}
                    </div>
                @elseif($banner->end_date && $banner->end_date < today())
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Expired on {{ $banner->end_date->format('M d, Y') }}
                    </div>
                @elseif($banner->isActive())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Currently Active
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Inactive
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6>Image Details</h6>
                @php
                    $imagePath = storage_path('app/public/' . $banner->image);
                    if (file_exists($imagePath)) {
                        $imageSize = getimagesize($imagePath);
                        $fileSize = filesize($imagePath);
                    }
                @endphp
                
                @if(isset($imageSize))
                    <p><strong>Dimensions:</strong> {{ $imageSize[0] }} × {{ $imageSize[1] }}px</p>
                    <p><strong>File Size:</strong> {{ round($fileSize / 1024, 2) }} KB</p>
                @endif
                <p><strong>Format:</strong> {{ strtoupper(pathinfo($banner->image, PATHINFO_EXTENSION)) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
