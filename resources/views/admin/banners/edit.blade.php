@extends('admin.layouts.app')

@section('title', 'Edit Banner')
@section('page_title', 'Edit Banner')

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
                    
                    <!-- Current Image Display -->
                    @if($banner->image)
                    <div class="mb-3">
                        <label class="form-label">Current Banner Image</label>
                        <div class="border rounded p-2">
                            <img src="{{ asset('storage/' . $banner->image) }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 200px;">
                            <div class="mt-2">
                                <small class="text-muted">{{ basename($banner->image) }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Replace Banner Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty to keep current image. Recommended size: 1200x400px</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                               id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="https://example.com">
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional: Where should the banner link when clicked?</small>
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
                                   id="start_date" name="start_date" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d') : '') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: When should this banner start showing?</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d') : '') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: When should this banner stop showing?</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alt_text" class="form-label">Alt Text</label>
                        <input type="text" class="form-control @error('alt_text') is-invalid @enderror" 
                               id="alt_text" name="alt_text" value="{{ old('alt_text', $banner->alt_text) }}" 
                               placeholder="Describe the banner for accessibility">
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
        <!-- Upload Statistics -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Upload Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary mb-0">{{ $uploadStats['product_uploads'] ?? 0 }}</h5>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success mb-0">{{ $uploadStats['category_uploads'] ?? 0 }}</h5>
                        <small class="text-muted">Categories</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h6 class="text-info mb-0">{{ $uploadStats['today_uploads'] ?? 0 }}</h6>
                            <small class="text-muted">Today</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-warning mb-0">{{ $uploadStats['this_week_uploads'] ?? 0 }}</h6>
                        <small class="text-muted">This Week</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner Upload History -->
        @if($bannerUploadLogs && $bannerUploadLogs->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history"></i> Upload History</h6>
            </div>
            <div class="card-body p-2">
                @foreach($bannerUploadLogs as $log)
                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                    <div class="flex-shrink-0">
                        <img src="{{ $log->url }}" 
                             class="rounded" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <div class="fw-bold small">{{ $log->original_name }}</div>
                        <div class="text-muted small">
                            {{ $log->formatted_size }} • {{ $log->created_at->diffForHumans() }}
                        </div>
                        @if($log->uploader)
                        <div class="text-muted small">
                            by {{ $log->uploader->name }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Product Uploads -->
        @if($recentProductUploads && $recentProductUploads->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-box"></i> Recent Product Images</h6>
                <small class="text-muted">Click to use</small>
            </div>
            <div class="card-body p-2">
                <div class="row g-2">
                    @foreach($recentProductUploads->take(6) as $upload)
                    <div class="col-4">
                        <div class="position-relative">
                            <img src="{{ $upload->url }}" 
                                 class="img-fluid rounded cursor-pointer upload-image-option" 
                                 style="height: 60px; width: 100%; object-fit: cover;"
                                 data-upload-id="{{ $upload->id }}"
                                 data-image-url="{{ $upload->url }}"
                                 data-upload-type="product"
                                 data-bs-toggle="tooltip"
                                 title="{{ $upload->original_name }} ({{ $upload->formatted_size }})">
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-primary rounded-pill" style="font-size: 0.6rem;">P</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($recentProductUploads->count() > 6)
                <div class="text-center mt-2">
                    <small class="text-muted">+{{ $recentProductUploads->count() - 6 }} more product images</small>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Recent Category Uploads -->
        @if($recentCategoryUploads && $recentCategoryUploads->count() > 0)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-tags"></i> Recent Category Images</h6>
                <small class="text-muted">Click to use</small>
            </div>
            <div class="card-body p-2">
                <div class="row g-2">
                    @foreach($recentCategoryUploads->take(6) as $upload)
                    <div class="col-4">
                        <div class="position-relative">
                            <img src="{{ $upload->url }}" 
                                 class="img-fluid rounded cursor-pointer upload-image-option" 
                                 style="height: 60px; width: 100%; object-fit: cover;"
                                 data-upload-id="{{ $upload->id }}"
                                 data-image-url="{{ $upload->url }}"
                                 data-upload-type="category"
                                 data-bs-toggle="tooltip"
                                 title="{{ $upload->original_name }} ({{ $upload->formatted_size }})">
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-success rounded-pill" style="font-size: 0.6rem;">C</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($recentCategoryUploads->count() > 6)
                <div class="text-center mt-2">
                    <small class="text-muted">+{{ $recentCategoryUploads->count() - 6 }} more category images</small>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Banner Guidelines -->
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

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Use Existing Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid mb-3" style="max-height: 400px;">
                <div id="imageInfo" class="mb-3">
                    <p class="mb-1"><strong>Filename:</strong> <span id="imageName"></span></p>
                    <p class="mb-1"><strong>Size:</strong> <span id="imageSize"></span></p>
                    <p class="mb-0"><strong>Type:</strong> <span id="imageType"></span></p>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> This will replace the current banner image with the selected image. The selected image will be copied for use as your banner.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="useImageBtn">Use This Image</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<style>
.cursor-pointer {
    cursor: pointer;
}
.upload-image-option:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedUploadId = null;
    
    // Handle clicking on upload images
    document.querySelectorAll('.upload-image-option').forEach(img => {
        img.addEventListener('click', function() {
            const uploadId = this.dataset.uploadId;
            const imageUrl = this.dataset.imageUrl;
            const uploadType = this.dataset.uploadType;
            const imageName = this.title.split(' (')[0];
            const imageSize = this.title.match(/\(([^)]+)\)/)[1];
            
            // Update modal content
            document.getElementById('previewImage').src = imageUrl;
            document.getElementById('imageName').textContent = imageName;
            document.getElementById('imageSize').textContent = imageSize;
            document.getElementById('imageType').textContent = uploadType.charAt(0).toUpperCase() + uploadType.slice(1) + ' Image';
            
            selectedUploadId = uploadId;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        });
    });
    
    // Handle use image button
    document.getElementById('useImageBtn').addEventListener('click', function() {
        if (!selectedUploadId) {
            alert('No image selected');
            return;
        }
        
        // Create a form to submit the request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.banners.use-existing-upload") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add upload log ID
        const uploadLogInput = document.createElement('input');
        uploadLogInput.type = 'hidden';
        uploadLogInput.name = 'upload_log_id';
        uploadLogInput.value = selectedUploadId;
        form.appendChild(uploadLogInput);
        
        // Add banner ID
        const bannerIdInput = document.createElement('input');
        bannerIdInput.type = 'hidden';
        bannerIdInput.name = 'banner_id';
        bannerIdInput.value = '{{ $banner->id }}';
        form.appendChild(bannerIdInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
