@extends('super-admin.layouts.app')

@section('title', 'Data Import')
@section('page-title', 'Data Import Management')

@section('content')
<div class="data-import-management">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="import-icon me-3">
                        <i class="fas fa-file-import fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Data Import Center</h2>
                        <p class="text-muted mb-0">Import categories and products from external sources</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-info" id="viewHistory">
                        <i class="fas fa-history me-2"></i>Import History
                    </button>
                    <button class="btn btn-outline-success" id="bulkImport">
                        <i class="fas fa-file-upload me-2"></i>Bulk Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Import Options</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="import-option">
                                <div class="import-option-header">
                                    <i class="fas fa-code fa-2x text-success"></i>
                                    <h6>HTML Content Import</h6>
                                </div>
                                <p class="text-muted">Paste HTML content directly to import categories and products</p>
                                <button class="btn btn-success" id="htmlImportBtn">
                                    <i class="fas fa-code me-2"></i>Import from HTML
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="import-option">
                                <div class="import-option-header">
                                    <i class="fas fa-file-upload fa-2x text-primary"></i>
                                    <h6>File Upload Import</h6>
                                </div>
                                <p class="text-muted">Upload HTML file to import categories and products</p>
                                <button class="btn btn-primary" id="fileImportBtn">
                                    <i class="fas fa-file-upload me-2"></i>Upload File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Company Selection</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Select Company (Optional)</label>
                            <select class="form-select" id="companySelect">
                                <option value="">Global (No Company)</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select a company to import data specifically for that company</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Import Settings</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="downloadImages" checked>
                                <label class="form-check-label" for="downloadImages">
                                    Download Product Images
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="createCategories" checked>
                                <label class="form-check-label" for="createCategories">
                                    Create Categories
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="updateExisting">
                                <label class="form-check-label" for="updateExisting">
                                    Update Existing Products
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Progress -->
    <div class="row mb-4" id="importProgress" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Import Progress</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%">
                            <span class="progress-text">0%</span>
                        </div>
                    </div>
                    <div class="import-status">
                        <div class="status-message">Ready to import...</div>
                        <div class="status-details"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Results -->
    <div class="row mb-4" id="importResults" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Import Results</h5>
                </div>
                <div class="card-body">
                    <div class="results-summary">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="result-stat">
                                    <div class="stat-number" id="categoriesFound">0</div>
                                    <div class="stat-label">Categories Found</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="result-stat">
                                    <div class="stat-number" id="categoriesCreated">0</div>
                                    <div class="stat-label">Categories Created</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="result-stat">
                                    <div class="stat-number" id="productsFound">0</div>
                                    <div class="stat-label">Products Found</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="result-stat">
                                    <div class="stat-number" id="productsCreated">0</div>
                                    <div class="stat-label">Products Created</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="results-details mt-4">
                        <div class="results-tabs">
                            <ul class="nav nav-tabs" id="resultsTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab">Categories</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">Products</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="resultsTabContent">
                                <div class="tab-pane fade show active" id="categories" role="tabpanel">
                                    <div class="categories-list mt-3">
                                        <!-- Categories will be populated here -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="products" role="tabpanel">
                                    <div class="products-list mt-3">
                                        <!-- Products will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HTML Import Modal -->
<div class="modal fade" id="htmlImportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import from HTML Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">HTML Content</label>
                    <textarea class="form-control" id="htmlContent" rows="20" placeholder="Paste your HTML content here..."></textarea>
                    <small class="text-muted">Paste the HTML content containing product and category information</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="previewBtn">
                        <i class="fas fa-eye me-2"></i>Preview Data
                    </button>
                    <button class="btn btn-success" id="importBtn">
                        <i class="fas fa-download me-2"></i>Import Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Import Modal -->
<div class="modal fade" id="fileImportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload HTML File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="fileImportForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Select HTML File</label>
                        <input type="file" class="form-control" id="htmlFile" accept=".html,.htm,.txt" required>
                        <small class="text-muted">Select an HTML file containing product and category information</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="previewFileBtn">
                            <i class="fas fa-eye me-2"></i>Preview File
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="preview-stats mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-number" id="previewCategoriesCount">0</div>
                                <div class="stat-label">Categories</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-number" id="previewProductsCount">0</div>
                                <div class="stat-label">Products</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="preview-details">
                    <div class="preview-tabs">
                        <ul class="nav nav-tabs" id="previewTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="preview-categories-tab" data-bs-toggle="tab" data-bs-target="#preview-categories" type="button" role="tab">Categories</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="preview-products-tab" data-bs-toggle="tab" data-bs-target="#preview-products" type="button" role="tab">Products</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="previewTabContent">
                            <div class="tab-pane fade show active" id="preview-categories" role="tabpanel">
                                <div class="preview-categories-list mt-3">
                                    <!-- Categories preview will be populated here -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="preview-products" role="tabpanel">
                                <div class="preview-products-list mt-3">
                                    <!-- Products preview will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmImport">
                    <i class="fas fa-check me-2"></i>Confirm Import
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.data-import-management {
    padding: 20px 0;
}

.import-option {
    padding: 30px;
    border: 2px dashed #dee2e6;
    border-radius: 15px;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
}

.import-option:hover {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.import-option-header {
    margin-bottom: 15px;
}

.import-option-header i {
    margin-bottom: 10px;
}

.import-option h6 {
    margin-bottom: 10px;
    color: #333;
}

.result-stat {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    text-transform: uppercase;
}

.stat-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.progress-bar {
    position: relative;
    overflow: visible;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: bold;
    color: white;
}

.status-message {
    font-weight: bold;
    margin-bottom: 10px;
}

.status-details {
    color: #666;
    font-size: 0.9rem;
}

.results-summary {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.categories-list, .products-list {
    max-height: 400px;
    overflow-y: auto;
}

.preview-categories-list, .preview-products-list {
    max-height: 300px;
    overflow-y: auto;
}

.category-item, .product-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-item:last-child, .product-item:last-child {
    border-bottom: none;
}

.category-name, .product-name {
    font-weight: bold;
}

.category-description, .product-description {
    color: #666;
    font-size: 0.9rem;
}

.product-price {
    font-weight: bold;
    color: #28a745;
}

.product-original-price {
    text-decoration: line-through;
    color: #999;
    margin-right: 10px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    text-align: center;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let currentData = null;

    // HTML Import Button
    $('#htmlImportBtn').on('click', function() {
        $('#htmlImportModal').modal('show');
    });

    // File Import Button
    $('#fileImportBtn').on('click', function() {
        $('#fileImportModal').modal('show');
    });

    // Preview Button
    $('#previewBtn').on('click', function() {
        const htmlContent = $('#htmlContent').val();
        if (!htmlContent.trim()) {
            showNotification('Please enter HTML content', 'error');
            return;
        }
        previewData(htmlContent);
    });

    // Import Button
    $('#importBtn').on('click', function() {
        const htmlContent = $('#htmlContent').val();
        if (!htmlContent.trim()) {
            showNotification('Please enter HTML content', 'error');
            return;
        }
        importData(htmlContent);
    });

    // File Import Form
    $('#fileImportForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const companyId = $('#companySelect').val();
        
        if (companyId) {
            formData.append('company_id', companyId);
        }

        uploadAndImport(formData);
    });

    // Confirm Import Button
    $('#confirmImport').on('click', function() {
        if (currentData) {
            $('#previewModal').modal('hide');
            importData(currentData);
        }
    });

    // Preview Data Function
    function previewData(htmlContent) {
        showLoading();
        
        $.ajax({
            url: '/super-admin/data-import/preview',
            method: 'POST',
            data: {
                html_content: htmlContent,
                company_id: $('#companySelect').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    currentData = htmlContent;
                    displayPreview(response.stats);
                    $('#previewModal').modal('show');
                } else {
                    showNotification('Error previewing data: ' + response.error, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error previewing data: ' + xhr.responseText, 'error');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    // Import Data Function
    function importData(htmlContent) {
        showImportProgress();
        
        $.ajax({
            url: '/super-admin/data-import/import',
            method: 'POST',
            data: {
                html_content: htmlContent,
                company_id: $('#companySelect').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Data imported successfully!', 'success');
                    displayResults(response.result);
                    $('#htmlImportModal').modal('hide');
                } else {
                    showNotification('Error importing data: ' + response.error, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error importing data: ' + xhr.responseText, 'error');
            },
            complete: function() {
                hideImportProgress();
            }
        });
    }

    // Upload and Import Function
    function uploadAndImport(formData) {
        showLoading();
        
        $.ajax({
            url: '/super-admin/data-import/upload-and-import',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('Data imported successfully from file!', 'success');
                    displayResults(response.result);
                    $('#fileImportModal').modal('hide');
                } else {
                    showNotification('Error importing data: ' + response.error, 'error');
                }
            },
            error: function(xhr) {
                showNotification('Error importing data: ' + xhr.responseText, 'error');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    // Display Preview Function
    function displayPreview(stats) {
        $('#previewCategoriesCount').text(stats.total_categories);
        $('#previewProductsCount').text(stats.total_products);

        // Display categories
        const $categoriesList = $('.preview-categories-list');
        $categoriesList.empty();
        
        if (stats.categories && stats.categories.length > 0) {
            stats.categories.forEach(function(category) {
                $categoriesList.append(`
                    <div class="category-item">
                        <div>
                            <div class="category-name">${category.name}</div>
                            <div class="category-description">${category.description}</div>
                        </div>
                    </div>
                `);
            });
        } else {
            $categoriesList.append('<div class="text-muted">No categories found</div>');
        }

        // Display products
        const $productsList = $('.preview-products-list');
        $productsList.empty();
        
        if (stats.products && stats.products.length > 0) {
            stats.products.forEach(function(product) {
                $productsList.append(`
                    <div class="product-item">
                        <div>
                            <div class="product-name">${product.name}</div>
                            <div class="product-description">SKU: ${product.sku}</div>
                        </div>
                        <div class="product-price">
                            <span class="product-original-price">₹${product.price}</span>
                            <span>₹${product.sale_price}</span>
                        </div>
                    </div>
                `);
            });
        } else {
            $productsList.append('<div class="text-muted">No products found</div>');
        }
    }

    // Display Results Function
    function displayResults(result) {
        $('#importResults').show();
        
        $('#categoriesFound').text(result.total_categories);
        $('#categoriesCreated').text(result.categories_created);
        $('#productsFound').text(result.total_products);
        $('#productsCreated').text(result.products_created);
    }

    // Show Import Progress
    function showImportProgress() {
        $('#importProgress').show();
        $('.progress-bar').css('width', '0%');
        $('.progress-text').text('0%');
        $('.status-message').text('Starting import...');
        
        // Simulate progress
        let progress = 0;
        const interval = setInterval(function() {
            progress += 10;
            $('.progress-bar').css('width', progress + '%');
            $('.progress-text').text(progress + '%');
            
            if (progress >= 100) {
                clearInterval(interval);
            }
        }, 500);
    }

    // Hide Import Progress
    function hideImportProgress() {
        $('.progress-bar').css('width', '100%');
        $('.progress-text').text('100%');
        $('.status-message').text('Import completed');
    }

    // Show Loading
    function showLoading() {
        $('body').append(`
            <div class="loading-overlay">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <div class="mt-3">Processing...</div>
                </div>
            </div>
        `);
    }

    // Hide Loading
    function hideLoading() {
        $('.loading-overlay').remove();
    }

    // Show Notification
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
});
</script>
@endpush
