@extends('admin.layouts.app')

@section('title', 'File Upload Test')
@section('page_title', 'File Upload System Test')

@push('styles')
<style>
    .test-section {
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f9f9f9;
    }
    .result-box {
        margin-top: 15px;
        padding: 15px;
        border-radius: 5px;
        background: #fff;
        border: 1px solid #ccc;
    }
    .success-box {
        border-color: #28a745;
        background-color: #d4edda;
        color: #155724;
    }
    .error-box {
        border-color: #dc3545;
        background-color: #f8d7da;
        color: #721c24;
    }
    .diagnostic-item {
        margin: 10px 0;
        padding: 10px;
        background: #fff;
        border-radius: 3px;
        border-left: 4px solid #007bff;
    }
    .diagnostic-success {
        border-left-color: #28a745;
    }
    .diagnostic-warning {
        border-left-color: #ffc107;
    }
    .diagnostic-error {
        border-left-color: #dc3545;
    }
    .file-preview {
        max-width: 200px;
        max-height: 200px;
        margin: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Storage Diagnostics -->
            <div class="test-section">
                <h4>📊 Storage Diagnostics</h4>
                <button type="button" class="btn btn-info" onclick="runDiagnostics()">
                    <i class="fas fa-stethoscope"></i> Run Diagnostics
                </button>
                <button type="button" class="btn btn-warning" onclick="fixStorage()">
                    <i class="fas fa-wrench"></i> Fix Storage Issues
                </button>
                <div id="diagnostics-result" class="result-box" style="display: none;"></div>
            </div>

            <!-- File Upload Test -->
            <div class="test-section">
                <h4>📁 File Upload Test</h4>
                <form id="upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select File</label>
                                <input type="file" name="file" class="form-control" accept="image/*" required>
                                <small class="text-muted">Max 5MB, formats: JPG, PNG, WebP</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Directory</label>
                                <select name="directory" class="form-select" required>
                                    <option value="test">Test</option>
                                    <option value="products">Products</option>
                                    <option value="categories">Categories</option>
                                    <option value="banners">Banners</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="generate_thumbnails" class="form-check-input" value="1" checked>
                                    <label class="form-check-label">Generate Thumbnails</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                </form>
                <div id="upload-result" class="result-box" style="display: none;"></div>
            </div>

            <!-- File URL Test -->
            <div class="test-section">
                <h4>🔗 File URL Test</h4>
                <div class="input-group">
                    <input type="text" id="url-test-path" class="form-control" placeholder="Enter file path (e.g., products/123456_image.jpg)">
                    <button type="button" class="btn btn-info" onclick="testUrl()">
                        <i class="fas fa-link"></i> Test URL
                    </button>
                </div>
                <div id="url-result" class="result-box" style="display: none;"></div>
            </div>

            <!-- File Delete Test -->
            <div class="test-section">
                <h4>🗑️ File Delete Test</h4>
                <div class="input-group">
                    <input type="text" id="delete-test-path" class="form-control" placeholder="Enter file path to delete">
                    <button type="button" class="btn btn-danger" onclick="testDelete()">
                        <i class="fas fa-trash"></i> Delete File
                    </button>
                </div>
                <div id="delete-result" class="result-box" style="display: none;"></div>
            </div>

            <!-- Recent Uploads -->
            <div class="test-section">
                <h4>📋 Recent Test Uploads</h4>
                <div id="recent-uploads">
                    <p class="text-muted">Upload some files to see them here...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let recentUploads = [];

// Run storage diagnostics
function runDiagnostics() {
    const resultDiv = document.getElementById('diagnostics-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Running diagnostics...</div>';
    
    fetch('{{ route("admin.test.file-upload.diagnostics") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<h5>Diagnostics Results:</h5>';
            
            // Storage type
            html += `<div class="diagnostic-item diagnostic-${data.diagnostics.storage_type === 'local' ? 'success' : 'warning'}">
                        <strong>Storage Type:</strong> ${data.diagnostics.storage_type}
                     </div>`;
            
            // Storage link
            const linkStatus = data.diagnostics.storage_link_exists ? 'success' : 'error';
            html += `<div class="diagnostic-item diagnostic-${linkStatus}">
                        <strong>Storage Symlink:</strong> ${data.diagnostics.storage_link_exists ? 'Exists' : 'Missing'}
                        ${data.diagnostics.storage_link_target ? `<br><small>Target: ${data.diagnostics.storage_link_target}</small>` : ''}
                     </div>`;
            
            // Directories
            html += '<h6>Directories:</h6>';
            Object.entries(data.diagnostics.directories).forEach(([dir, info]) => {
                const status = info.exists && info.writable ? 'success' : 'error';
                html += `<div class="diagnostic-item diagnostic-${status}">
                            <strong>${dir}:</strong> 
                            ${info.exists ? '✅ Exists' : '❌ Missing'} | 
                            ${info.writable ? '✅ Writable' : '❌ Not Writable'} | 
                            Files: ${info.file_count}
                            ${info.thumbs_dir_exists ? ' | Thumbs: ✅' : ' | Thumbs: ❌'}
                         </div>`;
            });
            
            // PHP Settings
            html += '<h6>PHP Settings:</h6>';
            Object.entries(data.diagnostics.php_settings).forEach(([setting, value]) => {
                const status = value === false || value === '' ? 'warning' : 'success';
                html += `<div class="diagnostic-item diagnostic-${status}">
                            <strong>${setting}:</strong> ${value === true ? 'Available' : value === false ? 'Not Available' : value}
                         </div>`;
            });
            
            resultDiv.innerHTML = html;
            resultDiv.className = 'result-box success-box';
        } else {
            resultDiv.innerHTML = `<strong>Error:</strong> ${data.message}`;
            resultDiv.className = 'result-box error-box';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
        resultDiv.className = 'result-box error-box';
    });
}

// Fix storage issues
function fixStorage() {
    const resultDiv = document.getElementById('diagnostics-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Fixing storage issues...</div>';
    
    fetch('{{ route("admin.test.file-upload.fix-storage") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<h5>Storage Fix Results:</h5>';
            data.results.forEach(result => {
                html += `<div class="diagnostic-item diagnostic-success">${result}</div>`;
            });
            resultDiv.innerHTML = html;
            resultDiv.className = 'result-box success-box';
        } else {
            resultDiv.innerHTML = `<strong>Error:</strong> ${data.message}`;
            resultDiv.className = 'result-box error-box';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
        resultDiv.className = 'result-box error-box';
    });
}

// Handle file upload
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('upload-result');
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Uploading...</div>';
    
    fetch('{{ route("admin.test.file-upload.test-upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<h5>Upload Successful!</h5>';
            html += `<p><strong>File Path:</strong> ${data.result.file_path}</p>`;
            html += `<p><strong>URL:</strong> <a href="${data.result.url}" target="_blank">${data.result.url}</a></p>`;
            html += `<p><strong>Size:</strong> ${(data.result.size / 1024).toFixed(2)} KB</p>`;
            
            if (data.result.thumbnails && Object.keys(data.result.thumbnails).length > 0) {
                html += '<h6>Thumbnails Generated:</h6>';
                Object.entries(data.result.thumbnails).forEach(([size, thumb]) => {
                    html += `<div class="d-inline-block m-2">
                                <img src="${thumb.url}" class="file-preview" alt="${size}">
                                <div class="text-center small">${size} (${thumb.width}x${thumb.height})</div>
                             </div>`;
                });
            }
            
            // Add to recent uploads
            recentUploads.unshift(data.result);
            updateRecentUploads();
            
            // Populate delete test field
            document.getElementById('delete-test-path').value = data.result.file_path;
            document.getElementById('url-test-path').value = data.result.file_path;
            
            resultDiv.innerHTML = html;
            resultDiv.className = 'result-box success-box';
        } else {
            resultDiv.innerHTML = `<strong>Upload Failed:</strong> ${data.message}`;
            resultDiv.className = 'result-box error-box';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
        resultDiv.className = 'result-box error-box';
    });
});

// Test file URL
function testUrl() {
    const filePath = document.getElementById('url-test-path').value;
    const resultDiv = document.getElementById('url-result');
    
    if (!filePath) {
        alert('Please enter a file path');
        return;
    }
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Testing URL...</div>';
    
    fetch('{{ route("admin.test.file-upload.test-url") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ file_path: filePath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<h5>URL Test Result:</h5>';
            html += `<p><strong>URL:</strong> <a href="${data.url}" target="_blank">${data.url}</a></p>`;
            html += `<p><strong>File Exists:</strong> ${data.file_exists ? '✅ Yes' : '❌ No'}</p>`;
            
            if (data.file_exists && data.url.match(/\.(jpg|jpeg|png|webp)$/i)) {
                html += `<div class="mt-3">
                            <img src="${data.url}" class="file-preview" alt="File preview">
                         </div>`;
            }
            
            resultDiv.innerHTML = html;
            resultDiv.className = 'result-box success-box';
        } else {
            resultDiv.innerHTML = `<strong>Error:</strong> ${data.message}`;
            resultDiv.className = 'result-box error-box';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
        resultDiv.className = 'result-box error-box';
    });
}

// Test file deletion
function testDelete() {
    const filePath = document.getElementById('delete-test-path').value;
    const resultDiv = document.getElementById('delete-result');
    
    if (!filePath) {
        alert('Please enter a file path');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this file?')) {
        return;
    }
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Deleting...</div>';
    
    fetch('{{ route("admin.test.file-upload.test-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ file_path: filePath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<h5>File Deleted Successfully!</h5>';
            resultDiv.className = 'result-box success-box';
            
            // Clear the input
            document.getElementById('delete-test-path').value = '';
        } else {
            resultDiv.innerHTML = `<strong>Delete Failed:</strong> ${data.message}`;
            resultDiv.className = 'result-box error-box';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<strong>Error:</strong> ${error.message}`;
        resultDiv.className = 'result-box error-box';
    });
}

// Update recent uploads display
function updateRecentUploads() {
    const container = document.getElementById('recent-uploads');
    
    if (recentUploads.length === 0) {
        container.innerHTML = '<p class="text-muted">Upload some files to see them here...</p>';
        return;
    }
    
    let html = '<div class="row">';
    recentUploads.slice(0, 6).forEach(upload => {
        html += `<div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="${upload.url}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">${upload.file_path}</h6>
                            <p class="card-text small">
                                Size: ${(upload.size / 1024).toFixed(2)} KB<br>
                                Thumbnails: ${upload.thumbnails ? Object.keys(upload.thumbnails).length : 0}
                            </p>
                            <button class="btn btn-sm btn-danger" onclick="deleteUpload('${upload.file_path}')">
                                Delete
                            </button>
                        </div>
                    </div>
                 </div>`;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

// Delete upload from recent list
function deleteUpload(filePath) {
    document.getElementById('delete-test-path').value = filePath;
    testDelete();
}

// Run initial diagnostics
document.addEventListener('DOMContentLoaded', function() {
    runDiagnostics();
});
</script>
@endpush
