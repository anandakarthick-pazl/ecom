@extends('super-admin.layouts.app')

@section('title', 'Storage Management')
@section('page-title', 'Storage Management')

@section('content')
<div class="container-fluid">
    <!-- Storage Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Current Storage</h5>
                            <h3 class="text-white">{{ $storageConfig['current_storage'] === 'local' ? 'Local' : 'AWS S3' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas {{ $storageConfig['current_storage'] === 'local' ? 'fa-hdd' : 'fa-cloud' }} fa-2x"></i>
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
                            <h5 class="card-title text-white mb-0">Total Files</h5>
                            <h3 class="text-white">{{ number_format($storageStats['total_files'] ?? 0) }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-file fa-2x"></i>
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
                            <h5 class="card-title text-white mb-0">Local Storage</h5>
                            <h3 class="text-white">{{ $storageStats['local']['total_size_formatted'] ?? '0 B' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-hdd fa-2x"></i>
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
                            <h5 class="card-title text-white mb-0">S3 Storage</h5>
                            <h3 class="text-white">{{ $storageStats['s3']['total_size_formatted'] ?? '0 B' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fab fa-aws fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('super-admin.storage.local') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-hdd me-2"></i>Local Storage
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('super-admin.storage.s3') }}" class="btn btn-outline-info w-100">
                                <i class="fab fa-aws me-2"></i>S3 Storage
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-success w-100" onclick="showUploadModal()">
                                <i class="fas fa-upload me-2"></i>Upload Files
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-warning w-100" onclick="showSyncModal()">
                                <i class="fas fa-sync me-2"></i>Sync Files
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Configuration -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-cog me-2"></i>Storage Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('super-admin.storage.config.update') }}" method="POST" id="storageConfigForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="storage_type" class="form-label">Primary Storage Type</label>
                                    <select class="form-select" id="storage_type" name="storage_type" onchange="toggleStorageConfig()">
                                        <option value="local" {{ $storageConfig['current_storage'] === 'local' ? 'selected' : '' }}>Local Storage</option>
                                        <option value="s3" {{ $storageConfig['current_storage'] === 's3' ? 'selected' : '' }}>AWS S3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Storage Status</label>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $storageConfig['local_config']['available'] ? 'success' : 'danger' }} me-2">
                                            Local: {{ $storageConfig['local_config']['available'] ? 'Available' : 'Unavailable' }}
                                        </span>
                                        <span class="badge bg-{{ $storageConfig['s3_config']['available'] ? 'success' : 'danger' }}">
                                            S3: {{ $storageConfig['s3_config']['available'] ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Local Storage Config -->
                        <div id="localConfig" class="storage-config {{ $storageConfig['current_storage'] === 'local' ? '' : 'd-none' }}">
                            <h6 class="text-primary"><i class="fas fa-hdd me-2"></i>Local Storage Configuration</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="local_path" class="form-label">Storage Path</label>
                                        <input type="text" class="form-control" id="local_path" name="local_path" 
                                               value="{{ $storageConfig['local_config']['path'] }}" readonly>
                                        <small class="form-text text-muted">Local storage directory path</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="local_url" class="form-label">Public URL</label>
                                        <input type="text" class="form-control" id="local_url" 
                                               value="{{ $storageConfig['local_config']['url'] }}" readonly>
                                        <small class="form-text text-muted">Base URL for local files</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- S3 Storage Config -->
                        <div id="s3Config" class="storage-config {{ $storageConfig['current_storage'] === 's3' ? '' : 'd-none' }}">
                            <h6 class="text-info"><i class="fab fa-aws me-2"></i>AWS S3 Configuration</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="aws_access_key_id" class="form-label">Access Key ID</label>
                                        <input type="password" class="form-control" id="aws_access_key_id" name="aws_access_key_id" 
                                               placeholder="Enter AWS Access Key ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="aws_secret_access_key" class="form-label">Secret Access Key</label>
                                        <input type="password" class="form-control" id="aws_secret_access_key" name="aws_secret_access_key" 
                                               placeholder="Enter AWS Secret Access Key">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="aws_default_region" class="form-label">Region</label>
                                        <select class="form-select" id="aws_default_region" name="aws_default_region">
                                            <option value="us-east-1">US East (N. Virginia)</option>
                                            <option value="us-west-1">US West (N. California)</option>
                                            <option value="us-west-2">US West (Oregon)</option>
                                            <option value="eu-west-1">Europe (Ireland)</option>
                                            <option value="eu-central-1">Europe (Frankfurt)</option>
                                            <option value="ap-southeast-1">Asia Pacific (Singapore)</option>
                                            <option value="ap-south-1">Asia Pacific (Mumbai)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="aws_bucket" class="form-label">Bucket Name</label>
                                        <input type="text" class="form-control" id="aws_bucket" name="aws_bucket" 
                                               value="{{ $storageConfig['s3_config']['bucket'] }}" 
                                               placeholder="Enter S3 bucket name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_url" class="form-label">Custom URL (Optional)</label>
                                        <input type="url" class="form-control" id="aws_url" name="aws_url" 
                                               value="{{ $storageConfig['s3_config']['url'] }}" 
                                               placeholder="https://yourbucket.s3.amazonaws.com">
                                        <small class="form-text text-muted">Leave empty to use default S3 URLs</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Configuration
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="testConnection()">
                                <i class="fas fa-link me-2"></i>Test Connection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- File Categories Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Files by Category</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(['products' => 'Products', 'banners' => 'Banners', 'categories' => 'Categories', 'general' => 'General'] as $key => $label)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-{{ $key === 'products' ? 'shopping-bag' : ($key === 'banners' ? 'image' : ($key === 'categories' ? 'tags' : 'file')) }} fa-2x text-primary mb-2"></i>
                                    <h5>{{ $label }}</h5>
                                    <h3 class="text-primary">{{ $storageStats['categories'][$key] ?? 0 }}</h3>
                                    <small class="text-muted">files</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-upload me-2"></i>Upload Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="uploadStorageType" class="form-label">Storage Type</label>
                                <select class="form-select" id="uploadStorageType" name="storage_type" required>
                                    <option value="local">Local Storage</option>
                                    <option value="s3">AWS S3</option>
                                </select>
                            </div>
                        </div>
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
                    </div>
                    <div class="mb-3">
                        <label for="uploadDirectory" class="form-label">Directory (Optional)</label>
                        <input type="text" class="form-control" id="uploadDirectory" name="directory" 
                               placeholder="e.g., 2024/january">
                    </div>
                    <div class="mb-3">
                        <label for="uploadFile" class="form-label">Select Files</label>
                        <input type="file" class="form-control" id="uploadFile" name="file" 
                               accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.doc,.docx" multiple required>
                        <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, WebP, SVG, PDF, DOC, DOCX (Max: 10MB)</small>
                    </div>
                    <div class="progress d-none" id="uploadProgress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadFiles()">
                    <i class="fas fa-upload me-2"></i>Upload Files
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
                <h5 class="modal-title" id="syncModalLabel"><i class="fas fa-sync me-2"></i>Sync Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="syncForm">
                    @csrf
                    <div class="mb-3">
                        <label for="syncDirection" class="form-label">Sync Direction</label>
                        <select class="form-select" id="syncDirection" name="sync_direction" required>
                            <option value="local_to_s3">Local to S3</option>
                            <option value="s3_to_local">S3 to Local</option>
                        </select>
                    </div>
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
                        This will copy files from source to destination. Existing files may be overwritten.
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
@endsection

@push('scripts')
<script>
function toggleStorageConfig() {
    const storageType = document.getElementById('storage_type').value;
    const localConfig = document.getElementById('localConfig');
    const s3Config = document.getElementById('s3Config');
    
    if (storageType === 'local') {
        localConfig.classList.remove('d-none');
        s3Config.classList.add('d-none');
    } else {
        localConfig.classList.add('d-none');
        s3Config.classList.remove('d-none');
    }
}

function showUploadModal() {
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

function showSyncModal() {
    new bootstrap.Modal(document.getElementById('syncModal')).show();
}

function testConnection() {
    const storageType = document.getElementById('storage_type').value;
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';
    btn.disabled = true;
    
    fetch('{{ route("super-admin.storage.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ storage_type: storageType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Connection test successful!', 'success');
        } else {
            showAlert('Connection test failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Connection test failed: ' + error.message, 'danger');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
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
            showAlert('Files uploaded successfully!', 'success');
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
            showAlert(`Sync completed! ${data.data.synced_count} files synced.`, 'success');
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
