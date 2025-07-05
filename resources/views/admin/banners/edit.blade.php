@extends('admin.layouts.app')

@section('title', 'Edit Banner')
@section('page_title', 'Edit Banner: ' . $banner->title)

@section('page_actions')
<a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Banners
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Banner Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Banner Image</label>
                        <div class="mb-2">
                            <img src="{{ Storage::url($banner->image) }}" class="img-thumbnail" style="max-height: 150px;" alt="{{ $banner->title }}">
                            <small class="d-block text-muted">Current banner image</small>
                        </div>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                               id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}">
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position *</label>
                            <select class="form-select @error('position') is-invalid @enderror" id="position" name="position" required>
                                <option value="top" {{ old('position', $banner->position) == 'top' ? 'selected' : '' }}>Top</option>
                                <option value="middle" {{ old('position', $banner->position) == 'middle' ? 'selected' : '' }}>Middle</option>
                                <option value="bottom" {{ old('position', $banner->position) == 'bottom' ? 'selected' : '' }}>Bottom</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $banner->start_date?->format('Y-m-d')) }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', $banner->end_date?->format('Y-m-d')) }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Alt Text</label>
                        <input type="text" class="form-control @error('alt_text') is-invalid @enderror" 
                               id="alt_text" name="alt_text" value="{{ old('alt_text', $banner->alt_text) }}">
                        @error('alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Banner
                        </button>
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Banner Info</h6>
                <p><strong>Created:</strong> {{ $banner->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> 
                    @if($banner->isActive())
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </p>
                
                @if($banner->start_date || $banner->end_date)
                <hr>
                <h6>Schedule</h6>
                @if($banner->start_date)
                    <p><strong>Starts:</strong> {{ $banner->start_date->format('M d, Y') }}</p>
                @endif
                @if($banner->end_date)
                    <p><strong>Ends:</strong> {{ $banner->end_date->format('M d, Y') }}</p>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
