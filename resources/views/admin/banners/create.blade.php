@extends('admin.layouts.app')

@section('title', 'Create Banner')
@section('page_title', 'Create Banner')

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
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Banner Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Banner Image *</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Recommended size: 1200x400px for best results</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                               id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://example.com">
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional: Where should the banner link when clicked?</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Position *</label>
                            <select class="form-select @error('position') is-invalid @enderror" id="position" name="position" required>
                                <option value="top" {{ old('position') == 'top' ? 'selected' : '' }}>Top</option>
                                <option value="middle" {{ old('position') == 'middle' ? 'selected' : '' }}>Middle</option>
                                <option value="bottom" {{ old('position') == 'bottom' ? 'selected' : '' }}>Bottom</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: When should this banner start showing?</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: When should this banner stop showing?</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Alt Text</label>
                        <input type="text" class="form-control @error('alt_text') is-invalid @enderror" 
                               id="alt_text" name="alt_text" value="{{ old('alt_text') }}" 
                               placeholder="Describe the banner for accessibility">
                        @error('alt_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Banner
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
                <h6>Banner Guidelines</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Use high-quality images</li>
                    <li><i class="fas fa-check text-success"></i> Recommended: 1200x400px</li>
                    <li><i class="fas fa-check text-success"></i> Keep text overlay minimal</li>
                    <li><i class="fas fa-check text-success"></i> Use contrasting colors</li>
                    <li><i class="fas fa-check text-success"></i> Optimize file size (&lt;2MB)</li>
                </ul>
                
                <hr>
                
                <h6>Position Guide</h6>
                <ul class="list-unstyled small">
                    <li><strong>Top:</strong> Main hero banner</li>
                    <li><strong>Middle:</strong> Category promotions</li>
                    <li><strong>Bottom:</strong> Newsletter/offers</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
