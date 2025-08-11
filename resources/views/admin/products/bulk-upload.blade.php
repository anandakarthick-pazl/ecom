@extends('admin.layouts.app')

@section('title', 'Bulk Upload Products')
@section('page_title', 'Bulk Upload Products')

@section('page_actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
    <a href="{{ route('admin.products.download-template') }}" class="btn btn-info">
        <i class="fas fa-download"></i> Download Template
    </a>
    <a href="{{ route('admin.products.upload-history') }}" class="btn btn-outline-primary">
        <i class="fas fa-history"></i> Upload History
    </a>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Upload Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-upload"></i> Upload Products File
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('upload_errors'))
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Upload completed with errors:</h6>
                            <div class="error-details" style="max-height: 300px; overflow-y: auto;">
                                @foreach(session('upload_errors') as $error)
                                    <div class="text-sm">• {{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.process-bulk-upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="file" class="form-label">
                                        <i class="fas fa-file-csv"></i> Select CSV or Excel File
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".csv,.xlsx,.xls,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        Supported formats: CSV (.csv), Excel (.xlsx, .xls). Maximum file size: 10MB<br>
                                        <small class="text-muted">Note: CSV files may show as different types (XLS Worksheet, Plain Text, etc.) - this is normal.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" value="1">
                                        <label class="form-check-label" for="update_existing">
                                            <strong>Update Existing Products</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        If checked, existing products (matched by SKU or name) will be updated. 
                                        If unchecked, duplicate products will be skipped.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="upload-btn">
                                <i class="fas fa-upload"></i> Upload Products
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                                <i class="fas fa-redo"></i> Reset Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Instructions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Upload Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">📋 Step-by-Step Guide:</h6>
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <strong>Download Template</strong>
                                <div class="text-sm text-muted">Get the CSV template with all required columns</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <strong>Fill Product Data</strong>
                                <div class="text-sm text-muted">Add your products following the template format</div>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <strong>Upload & Process</strong>
                                <div class="text-sm text-muted">Upload your file and wait for processing</div>
                            </div>
                        </li>
                    </ol>

                    <hr>
                    
                    <h6 class="text-primary">📄 Required Fields:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> <strong>name</strong> - Product name</li>
                        <li><i class="fas fa-check text-success"></i> <strong>description</strong> - Full description</li>
                        <li><i class="fas fa-check text-success"></i> <strong>price</strong> - Product price</li>
                        <li><i class="fas fa-check text-success"></i> <strong>stock</strong> - Available quantity</li>
                        <li><i class="fas fa-check text-success"></i> <strong>category_name</strong> - Category name</li>
                        <li><i class="fas fa-check text-success"></i> <strong>tax_percentage</strong> - Tax rate</li>
                    </ul>

                    <hr>

                    <h6 class="text-primary">🔧 Optional Fields:</h6>
                    <ul class="list-unstyled text-sm">
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> short_description</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> discount_price</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> cost_price</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> sku</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> barcode</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> weight & weight_unit</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> low_stock_threshold</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> is_active (1 or 0)</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> is_featured (1 or 0)</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> meta_title</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> meta_description</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> featured_image_url</li>
                        <li><i class="fas fa-circle text-muted" style="font-size: 4px;"></i> additional_images</li>
                    </ul>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-lightbulb"></i> Pro Tips
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info p-2 mb-2">
                        <small>
                            <i class="fas fa-tag"></i> 
                            Use unique SKUs to prevent duplicates and enable updates
                        </small>
                    </div>
                    <div class="alert alert-success p-2 mb-2">
                        <small>
                            <i class="fas fa-images"></i> 
                            Add image URLs to automatically download and attach product images
                        </small>
                    </div>
                    <div class="alert alert-warning p-2 mb-2">
                        <small>
                            <i class="fas fa-list"></i> 
                            Make sure category names exactly match existing categories
                        </small>
                    </div>
                    <div class="alert alert-primary p-2 mb-0">
                        <small>
                            <i class="fas fa-chart-line"></i> 
                            Large files may take several minutes to process
                        </small>
                    </div>
                </div>
            </div>

            <!-- Available Categories -->
            @if($categories->count() > 0)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-list"></i> Available Categories
                    </h6>
                </div>
                <div class="card-body">
                    <div style="max-height: 200px; overflow-y: auto;">
                        @foreach($categories as $category)
                            <span class="badge bg-light text-dark me-1 mb-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle"></i> 
                        Use these exact category names in your CSV file
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('upload-form');
    const uploadBtn = document.getElementById('upload-btn');
    const fileInput = document.getElementById('file');
    
    form.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload.');
            return;
        }
        
        // Disable button and show loading state
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        // Show progress message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info mt-3';
        alertDiv.innerHTML = '<i class="fas fa-info-circle"></i> Processing your file... This may take a few minutes for large files.';
        form.appendChild(alertDiv);
    });
    
    // File validation
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = [
                'text/csv', 
                'text/plain',
                'application/csv',
                'application/vnd.ms-excel', 
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/excel',
                'application/vnd.msexcel'
            ];
            
            if (file.size > maxSize) {
                alert('File size must be less than 10MB');
                this.value = '';
                return;
            }
            
            // Check by extension if MIME type validation fails
            const isValidExtension = file.name.match(/\.(csv|xlsx|xls)$/i);
            const isValidMimeType = allowedTypes.includes(file.type);
            
            if (!isValidExtension && !isValidMimeType) {
                alert('Only CSV (.csv) and Excel (.xlsx, .xls) files are allowed');
                this.value = '';
                return;
            }
            
            // Show file info with type detection
            let fileTypeDisplay = 'Unknown';
            if (file.name.match(/\.csv$/i) || file.type.includes('csv') || file.type === 'text/plain') {
                fileTypeDisplay = 'CSV';
            } else if (file.name.match(/\.xlsx$/i) || file.type.includes('openxmlformats')) {
                fileTypeDisplay = 'Excel (XLSX)';
            } else if (file.name.match(/\.xls$/i) || file.type.includes('ms-excel')) {
                fileTypeDisplay = 'Excel (XLS)';
            }
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'mt-2 text-success';
            fileInfo.innerHTML = `<i class="fas fa-check"></i> Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB) - Detected as: ${fileTypeDisplay}`;
            
            // Remove previous file info
            const existingInfo = this.parentNode.querySelector('.file-info');
            if (existingInfo) {
                existingInfo.remove();
            }
            
            fileInfo.className += ' file-info';
            this.parentNode.appendChild(fileInfo);
        }
    });
});
</script>
@endpush
@endsection