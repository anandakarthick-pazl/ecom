@extends('super-admin.layouts.app')

@section('title', 'Local File Storage')
@section('page-title', 'Local File Storage Management')

@section('content')
<div class="container-fluid">
    <!-- Storage Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-0">Total Files</h5>
                            <h3 class="text-white">{{ number_format($storageStats['file_count'] ?? 0) }}</h3>
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
                            <h3 class="text-white">{{ $storageStats['total_size_formatted'] ?? '0 B' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-hdd fa-2x"></i>
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
                            <h5 class="card-title text-white mb-0">Available</h5>
                            <h3 class="text-white">{{ $storageStats['available_space_formatted'] ?? 'Unknown' }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h5 class="card-title text-white mb-0">Directories</h5>
                            <h3 class="text-white">{{ count($directories) }}</h3>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-folder fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Directory Management -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-folder me-2"></i>Directory Management</h5>
                    <button class="btn btn-primary btn-sm" onclick="showCreateDirectoryModal()">
                        <i class="fas fa-plus me-2"></i>Create Directory
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Directory Name</th>
                                    <th>Path</th>
                                    <th>Files Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($directories as $directory)
                                <tr>
                                    <td>
                                        <i class="fas fa-folder text-warning me-2"></i>
                                        {{ $directory['name'] }}
                                    </td>
                                    <td><code>{{ $directory['path'] }}</code></td>
                                    <td>
                                        <span class="badge bg-info">{{ $directory['file_count'] }} files</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="browseDirectory('{{ $directory['path'] }}')">
                                            <i class="fas fa-eye"></i> Browse
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No directories found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-file me-2"></i>Local Files</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" onclick="showUploadModal()">
                            <i class="fas fa-upload me-2"></i>Upload Files
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="refreshFileList()">
                            <i class="fas fa-refresh me-2"></i>Refresh
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
                        @forelse($localFiles as $file)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 file-item" 
                             data-category="{{ pathinfo($file['path'], PATHINFO_DIRNAME) }}" 
                             data-type="{{ $file['type'] }}" 
                             data-name="{{ strtolower($file['name']) }}">
                            <div class="card h-100">
                                <div class="card-header p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $file['type'] ?? 'file' }}</small>
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
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteFile('{{ $file['path'] }}', 'local')">
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
                                        <i class="fas fa-file-{{ $file['type'] === 'pdf' ? 'pdf' : 'alt' }} fa-4x text-primary"></i>
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
                                <i class="fas fa-file fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No files found</h5>
                                <p class="text-muted">Upload some files to get started</p>
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
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-upload me-2"></i>Upload to Local Storage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="storage_type" value="local">
                    
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

<!-- Create Directory Modal -->
<div class="modal fade" id="createDirectoryModal" tabindex="-1" aria-labelledby="createDirectoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createDirectoryModalLabel"><i class="fas fa-folder-plus me-2"></i>Create Directory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createDirectoryForm">
                    @csrf
                    <input type="hidden" name="storage_type" value="local">
                    
                    <div class="mb-3">
                        <label for="directoryName" class="form-label">Directory Name</label>
                        <input type="text" class="form-control" id="directoryName" name="directory_name" 
                               placeholder="e.g., products/2024" required>
                        <small class="form-text text-muted">Use forward slashes (/) to create nested directories</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="parentDirectory" class="form-label">Parent Directory (Optional)</label>
                        <select class="form-select" id="parentDirectory" name="parent_directory">
                            <option value="">Root Directory</option>
                            @foreach($directories as $directory)
                            <option value="{{ $directory['path'] }}">{{ $directory['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createDirectory()">
                    <i class="fas fa-folder-plus me-2"></i>Create Directory
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showUploadModal() {
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

function showCreateDirectoryModal() {
    new bootstrap.Modal(document.getElementById('createDirectoryModal')).show();
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

function createDirectory() {
    const form = document.getElementById('createDirectoryForm');
    const formData = new FormData(form);
    
    fetch('{{ route("super-admin.storage.directory.create") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Directory created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createDirectoryModal')).hide();
            location.reload();
        } else {
            showAlert('Directory creation failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Directory creation failed: ' + error.message, 'danger');
    });
}

function deleteFile(filePath, storageType) {
    if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
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
            showAlert('File deleted successfully!', 'success');
            location.reload();
        } else {
            showAlert('File deletion failed: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('File deletion failed: ' + error.message, 'danger');
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

function browseDirectory(path) {
    // Implement directory browsing logic
    window.location.href = `{{ route('super-admin.storage.local') }}?directory=${encodeURIComponent(path)}`;
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
