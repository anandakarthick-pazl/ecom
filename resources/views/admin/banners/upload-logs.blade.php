@extends('admin.layouts.app')

@section('title', 'Upload Logs')
@section('page_title', 'Upload Logs')

@section('page_actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Banners
    </a>
    <div class="btn-group">
        <a href="{{ route('admin.banners.upload-logs') }}" 
           class="btn btn-outline-primary {{ !request('type') ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.banners.upload-logs', ['type' => 'product']) }}" 
           class="btn btn-outline-primary {{ request('type') == 'product' ? 'active' : '' }}">Products</a>
        <a href="{{ route('admin.banners.upload-logs', ['type' => 'category']) }}" 
           class="btn btn-outline-primary {{ request('type') == 'category' ? 'active' : '' }}">Categories</a>
        <a href="{{ route('admin.banners.upload-logs', ['type' => 'banner']) }}" 
           class="btn btn-outline-primary {{ request('type') == 'banner' ? 'active' : '' }}">Banners</a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Upload Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary mb-0">{{ $uploadStats['total_uploads'] ?? 0 }}</h4>
                        <small class="text-muted">Total Uploads</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success mb-0">{{ $uploadStats['product_uploads'] ?? 0 }}</h4>
                        <small class="text-muted">Product Images</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info mb-0">{{ $uploadStats['category_uploads'] ?? 0 }}</h4>
                        <small class="text-muted">Category Images</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning mb-0">{{ $uploadStats['banner_uploads'] ?? 0 }}</h4>
                        <small class="text-muted">Banner Images</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Logs Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-upload"></i> 
                        @if($uploadType)
                            {{ ucfirst($uploadType) }} Upload Logs
                        @else
                            All Upload Logs
                        @endif
                    </h6>
                    <div class="text-muted small">
                        Total: {{ $uploadLogs->total() }} uploads
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($uploadLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Preview</th>
                                <th>File Info</th>
                                <th>Upload Type</th>
                                <th>Source</th>
                                <th>Uploaded By</th>
                                <th>Upload Date</th>
                                <th width="100">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploadLogs as $log)
                            <tr>
                                <td>
                                    <img src="{{ $log->url }}" 
                                         class="rounded cursor-pointer" 
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         onclick="showImagePreview('{{ $log->url }}', '{{ $log->original_name }}', '{{ $log->formatted_size }}', '{{ $log->upload_type }}')"
                                         data-bs-toggle="tooltip"
                                         title="Click to preview">
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $log->original_name }}</div>
                                    <div class="text-muted small">
                                        {{ $log->formatted_size }} • {{ $log->mime_type }}
                                    </div>
                                    @if($log->meta_data && isset($log->meta_data['title']))
                                    <div class="text-info small">
                                        Title: {{ $log->meta_data['title'] }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->upload_type) {
                                            'product' => 'bg-primary',
                                            'category' => 'bg-success',
                                            'banner' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($log->upload_type) }}
                                    </span>
                                    @if($log->storage_type === 's3')
                                    <span class="badge bg-info ms-1">S3</span>
                                    @else
                                    <span class="badge bg-secondary ms-1">Local</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->source_id && $log->source_type)
                                        @php
                                            $sourceClass = class_basename($log->source_type);
                                        @endphp
                                        <div class="small">
                                            <strong>{{ $sourceClass }} #{{ $log->source_id }}</strong>
                                        </div>
                                        @if($log->meta_data && isset($log->meta_data['action']))
                                        <div class="text-muted small">
                                            Action: {{ $log->meta_data['action'] }}
                                        </div>
                                        @endif
                                    @else
                                        <span class="text-muted small">No source</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->uploader)
                                        <div class="fw-bold small">{{ $log->uploader->name }}</div>
                                        <div class="text-muted small">{{ $log->uploader->email }}</div>
                                    @else
                                        <span class="text-muted small">System</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">{{ $log->created_at->format('M j, Y') }}</div>
                                    <div class="text-muted small">{{ $log->created_at->format('g:i A') }}</div>
                                    <div class="text-muted small">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm"
                                                onclick="showImagePreview('{{ $log->url }}', '{{ $log->original_name }}', '{{ $log->formatted_size }}', '{{ $log->upload_type }}')"
                                                data-bs-toggle="tooltip"
                                                title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ $log->url }}" 
                                           target="_blank"
                                           class="btn btn-outline-success btn-sm"
                                           data-bs-toggle="tooltip"
                                           title="Open in new tab">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3">
                    <div class="text-muted small">
                        Showing {{ $uploadLogs->firstItem() ?? 0 }} to {{ $uploadLogs->lastItem() ?? 0 }} 
                        of {{ $uploadLogs->total() }} results
                    </div>
                    {{ $uploadLogs->appends(request()->query())->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-upload fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Upload Logs Found</h5>
                    <p class="text-muted">
                        @if($uploadType)
                            No {{ $uploadType }} uploads have been recorded yet.
                        @else
                            No uploads have been recorded yet.
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid mb-3" style="max-height: 500px;">
                <div id="imageInfo">
                    <p class="mb-1"><strong>Filename:</strong> <span id="imageName"></span></p>
                    <p class="mb-1"><strong>Size:</strong> <span id="imageSize"></span></p>
                    <p class="mb-0"><strong>Type:</strong> <span id="imageType"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadImageBtn" href="" target="_blank" class="btn btn-primary">
                    <i class="fas fa-download"></i> Open Original
                </a>
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
.table td {
    vertical-align: middle;
}
</style>

<script>
function showImagePreview(imageUrl, imageName, imageSize, imageType) {
    // Update modal content
    document.getElementById('previewImage').src = imageUrl;
    document.getElementById('imageName').textContent = imageName;
    document.getElementById('imageSize').textContent = imageSize;
    document.getElementById('imageType').textContent = imageType.charAt(0).toUpperCase() + imageType.slice(1) + ' Image';
    document.getElementById('downloadImageBtn').href = imageUrl;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
