@extends('super-admin.layouts.app')

@section('title', 'AWS S3 Storage')
@section('page-title', 'AWS S3 Storage Management')

@section('content')
<div class="container-fluid">
    <!-- S3 Configuration Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-{{ $s3Config['available'] ? 'success' : 'warning' }}">
                <div class="d-flex align-items-center">
                    <i class="fab fa-aws fa-2x me-3"></i>
                    <div>
                        @if($s3Config['available'])
                        <h5 class="alert-heading mb-1">S3 Configuration Active</h5>
                        <p class="mb-0">Bucket: <strong>{{ $s3Config['bucket'] }}</strong> | Region: <strong>{{ $s3Config['region'] }}</strong></p>
                        @else
                        <h5 class="alert-heading mb-1">S3 Configuration Required</h5>
                        <p class="mb-0">Please configure your AWS S3 credentials in the storage settings to use S3 storage.</p>
                        @endif
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('super-admin.storage.index') }}" class="btn btn-{{ $s3Config['available'] ? 'outline-success' : 'warning' }}">
                            <i class="fas fa-cog me-2"></i>Configure S3
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($s3Config['available'])
    <!-- Storage Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Total Files</h5>
                            <h3 class="text-white">{{ number_format($s3Stats['file_count'] ?? 0) }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-file fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Used Space</h5>
                            <h3 class="text-white">{{ $s3Stats['total_size_formatted'] ?? '0 B' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fab fa-aws fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Bucket</h5>
                            <h3 class="text-white">{{ $s3Config['bucket'] }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-bucket fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Region</h5>
                            <h3 class="text-white">{{ $s3Config['region'] }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-globe fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- S3 Buckets -->
    @if(count($buckets) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-bucket me-2"></i>Available S3 Buckets</h5>
                    <button class="btn btn-primary btn-sm" onclick="refreshBuckets()">
                        <i class="fas fa-refresh me-2"></i>Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bucket Name</th>
                                    <th>Creation Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($buckets as $bucket)
                                <tr>
                                    <td>
                                        <i class="fas fa-bucket text-primary me-2"></i>
                                        {{ $bucket['name'] }}
                                        @if($bucket['name'] === $s3Config['bucket'])
                                        <span class="badge bg-success ms-2">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $bucket['creation_date']->format('M j, Y g:i A') }}</td>
                                    <td>
                                        <span class="badge bg-success">Available</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="browseBucket('{{ $bucket['name'] }}')">
                                            <i class="fas fa-eye"></i> Browse
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- File Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-cloud me-2"></i>S3 Files</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" onclick="showUploadModal()">
                            <i class="fas fa-upload me-2"></i>Upload Files
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="refreshFileList()">
                            <i class="fas fa-refresh me-2"></i>Refresh
                        </button>
                        <button class="btn btn-info btn-sm" onclick="showSyncModal()">
                            <i class="fas fa-sync me-2"></i>Sync from Local
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- File Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter" onchange="filterFiles()">
                                <option value="">All Categories</option>
                                <option value="products">Products</option>
                                <option value="banners">Banners</option>
                                <option value="categories">Categories</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter" onchange="filterFiles()">
                                <option value="">All Types</option>
                                <option value="jpg,jpeg">JPEG</option>
                                <option value="png">PNG</option>
                                <option value="gif">GIF</option>
                                <option value="webp">WebP</option>
                                <option value="svg">SVG</option>
                                <option value="pdf">PDF</option>
                                <option value="doc,docx">Documents</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search files..." onkeyup="filterFiles()">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- Files Grid -->
                    <div class="row" id="filesGrid">
                        @forelse($s3Files as $file)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 file-item" 
                             data-category="{{ pathinfo($file['path'], PATHINFO_DIRNAME) }}" 
                             data-type="{{ $file['type'] }}" 
                             data-name="{{ strtolower($file['name']) }}">
                            <div class="card h-100">
                                <div class="card-header p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fab fa-aws me-1"></i>{{ $file['type'] ?? 'file' }}
                                        </small>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ $file['url'] }}" target="_blank">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ $file['url'] }}" download>
                                                    <i class="fas fa-download me-2"></i>Download
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="copyUrl('{{ $file['url'] }}')">
                                                    <i class="fas fa-copy me-2"></i>Copy URL
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteFile('{{ $file['path'] }}', 's3')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body p-2 text-center">
                                    @if(in_array($file['type'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                                    <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" 
                                         class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover;">
                                    @else
                                    <div class="text-center mb-2">
                                        <i class="fas fa-file-{{ $file['type'] === 'pdf' ? 'pdf' : 'alt' }} fa-4x text-info"></i>
                                    </div>
                                    @endif
                                    
                                    <h6 class="card-title small" title="{{ $file['name'] }}">
                                        {{ Str::limit($file['name'], 20) }}
                                    </h6>
                                    <p class="card-text small text-muted">
                                        {{ $file['size_formatted'] }}<br>
                                        <small>{{ $file['modified']->format('M j, Y') }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <i class="fab fa-aws fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No files found in S3</h5>
                                <p class="text-muted">Upload some files to your S3 bucket to get started</p>
                                <button class="btn btn-primary" onclick="showUploadModal()">
                                    <i class="fas fa-upload me-2"></i>Upload Files
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- S3 Not Configured -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fab fa-aws fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">AWS S3 Storage Not Configured</h3>
                    <p class="text-muted mb-4">Configure your AWS S3 credentials to enable cloud storage functionality.</p>
                    <a href="{{ route('super-admin.storage.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-cog me-2"></i>Configure S3 Storage
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($s3Config['available'])
<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-upload me-2"></i>Upload to S3 Storage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="storage_type" value="s3">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="uploadCategory" class="form-label">Category</label>
                                <select class="form-select" id="uploadCategory" name="category" required>
                                    <option value="products">Products</option>
                                    <option value="banners">Banners</option>
                                    <option value="categories">Categories</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="uploadDirectory" class="form-label">Directory (Optional)</label>
                                <input type="text" class="form-control" id="uploadDirectory" name="directory" 
                                       placeholder="e.g., 2024/january">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="uploadFile" class="form-label">Select Files</label>
                        <input type="file" class="form-control" id="uploadFile" name="file" 
                               accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx" multiple required>
                        <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, WebP, SVG, PDF, DOC, DOCX (Max: 10MB each)</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Files will be uploaded to: <strong>{{ $s3Config['bucket'] }}</strong>
                    </div>
                    
                    <div class="progress d-none" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadFiles()">
                    <i class="fas fa-upload me-2"></i>Upload to S3
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel"><i class="fas fa-sync me-2"></i>Sync Local Files to S3</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="syncForm">
                    @csrf
                    <input type="hidden" name="sync_direction" value="local_to_s3">
                    
                    <div class="mb-3">
                        <label for="syncCategory" class="form-label">Category (Optional)</label>
                        <select class="form-select" id="syncCategory" name="category">
                            <option value="">All Categories</option>
                            <option value="products">Products</option>
                            <option value="banners">Banners</option>
                            <option value="categories">Categories</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will copy files from your local storage to S3. Existing files in S3 may be overwritten.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="syncFiles()">
                    <i class="fas fa-sync me-2"></i>Start Sync
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function showUploadModal() {
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

function showSyncModal() {
    new bootstrap.Modal(document.getElementById('syncModal')).show();
}

function uploadFiles() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);
    const progressBar = document.querySelector('#uploadProgress .progress-bar');
    const progressContainer = document.getElementById('uploadProgress');
    
    progressContainer.classList.remove('d-none');
    progressBar.style.width = '0%';
    
    fetch('{{ route("super-admin.storage.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Files uploaded to S3 successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            location.reload();
        } else {
            showAlert('Upload failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Upload failed: ' + error.message, 'danger');
    })
    .finally(() => {
        progressContainer.classList.add('d-none');
    });
}

function syncFiles() {
    const form = document.getElementById('syncForm');
    const formData = new FormData(form);
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Syncing...';
    btn.disabled = true;
    
    fetch('{{ route("super-admin.storage.sync") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Sync completed! ${data.data.synced_count} files synced to S3.`, 'success');
            bootstrap.Modal.getInstance(document.getElementById('syncModal')).hide();
            location.reload();
        } else {
            showAlert('Sync failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Sync failed: ' + error.message, 'danger');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function deleteFile(filePath, storageType) {
    if (!confirm('Are you sure you want to delete this file from S3? This action cannot be undone.')) {
        return;
    }
    
    fetch('{{ route("super-admin.storage.delete") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            file_path: filePath,
            storage_type: storageType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('File deleted from S3 successfully!', 'success');
            location.reload();
        } else {
            showAlert('File deletion failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('File deletion failed: ' + error.message, 'danger');
    });
}

function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showAlert('URL copied to clipboard!', 'success');
    }).catch(() => {
        showAlert('Failed to copy URL', 'danger');
    });
}

function filterFiles() {
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    const fileItems = document.querySelectorAll('.file-item');
    
    fileItems.forEach(item => {
        const category = item.dataset.category.toLowerCase();
        const type = item.dataset.type.toLowerCase();
        const name = item.dataset.name.toLowerCase();
        
        let showItem = true;
        
        if (categoryFilter && !category.includes(categoryFilter)) {
            showItem = false;
        }
        
        if (typeFilter && !typeFilter.split(',').some(t => type.includes(t.trim()))) {
            showItem = false;
        }
        
        if (searchFilter && !name.includes(searchFilter)) {
            showItem = false;
        }
        
        item.style.display = showItem ? 'block' : 'none';
    });
}

function clearFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('searchFilter').value = '';
    filterFiles();
}

function refreshFileList() {
    location.reload();
}

function refreshBuckets() {
    location.reload();
}

function browseBucket(bucketName) {
    // Implement bucket browsing logic
    showAlert(`Browsing bucket: ${bucketName}`, 'info');
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endpush
