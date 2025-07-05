@extends('super-admin.layouts.app')

@section('title', 'Theme Details - ' . $theme->name)
@section('page-title', 'Theme Details')

@section('content')
<div class="theme-details">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('super-admin.themes.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="mb-0">{{ $theme->name }}</h2>
                        <p class="text-muted mb-0">{{ $theme->category_name }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($theme->demo_url)
                        <a href="{{ $theme->demo_url }}" target="_blank" class="btn btn-info">
                            <i class="fas fa-external-link-alt me-2"></i>Live Demo
                        </a>
                    @endif
                    <a href="{{ route('super-admin.themes.edit', $theme) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Theme
                    </a>
                    <button class="btn btn-{{ $theme->status === 'active' ? 'warning' : 'success' }} toggle-status-btn" 
                            data-theme-id="{{ $theme->id }}"
                            data-current-status="{{ $theme->status }}">
                        <i class="fas fa-{{ $theme->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                        {{ $theme->status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Theme Preview -->
        <div class="col-lg-8">
            <!-- Preview Image -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="theme-preview-container">
                        @if($theme->preview_image)
                            <img src="{{ asset('storage/' . $theme->preview_image) }}" 
                                 alt="{{ $theme->name }}" 
                                 class="theme-preview-image">
                        @else
                            <div class="theme-preview-placeholder">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No preview image available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Screenshots Gallery -->
            @if($theme->screenshots && count($theme->screenshots) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-images me-2"></i>Screenshots
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($theme->screenshots as $screenshot)
                                <div class="col-md-6 mb-3">
                                    <div class="screenshot-item">
                                        <img src="{{ asset('storage/' . $screenshot) }}" 
                                             alt="Screenshot" 
                                             class="screenshot-image"
                                             data-bs-toggle="modal"
                                             data-bs-target="#screenshotModal"
                                             data-screenshot="{{ asset('storage/' . $screenshot) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Description
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $theme->description }}</p>
                </div>
            </div>

            <!-- Features -->
            @if($theme->features && count($theme->features) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($theme->features as $feature)
                                <div class="col-md-6 mb-2">
                                    <div class="feature-item">
                                        <i class="fas fa-check-circle text-success me-2"></i>{{ $feature }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Color Scheme -->
            @if($theme->color_scheme)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-palette me-2"></i>Color Scheme
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($theme->color_scheme as $colorName => $colorValue)
                                <div class="col-md-3 mb-3">
                                    <div class="color-sample">
                                        <div class="color-swatch" style="background-color: {{ $colorValue }}"></div>
                                        <div class="color-info">
                                            <strong>{{ ucfirst($colorName) }}</strong>
                                            <div class="color-value">{{ $colorValue }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Usage Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Usage Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ $theme->companies->count() }}</div>
                                <div class="stat-label">Companies Using</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-number">{{ number_format($theme->downloads_count) }}</div>
                                <div class="stat-label">Total Downloads</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-item">
                                <div class="stat-number">
                                    @if($theme->rating)
                                        {{ $theme->rating }}/5
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="stat-label">Average Rating</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Theme Info -->
        <div class="col-lg-4">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $theme->status === 'active' ? 'success' : 'secondary' }} ms-2">
                            {{ ucfirst($theme->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>Category:</strong>
                        <span class="ms-2">{{ $theme->category_name }}</span>
                    </div>
                    @if($theme->layout_type)
                        <div class="info-item">
                            <strong>Layout Type:</strong>
                            <span class="ms-2">{{ $theme->layout_type_name }}</span>
                        </div>
                    @endif
                    <div class="info-item">
                        <strong>Difficulty:</strong>
                        <span class="ms-2">{{ $theme->difficulty_level_name }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Price:</strong>
                        <span class="ms-2">
                            @if($theme->is_free)
                                <span class="badge bg-primary">FREE</span>
                            @else
                                <span class="fw-bold">${{ number_format($theme->price, 2) }}</span>
                            @endif
                        </span>
                    </div>
                    @if($theme->author)
                        <div class="info-item">
                            <strong>Author:</strong>
                            <span class="ms-2">{{ $theme->author }}</span>
                        </div>
                    @endif
                    <div class="info-item">
                        <strong>Created:</strong>
                        <span class="ms-2">{{ $theme->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Updated:</strong>
                        <span class="ms-2">{{ $theme->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Capabilities -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Capabilities
                    </h5>
                </div>
                <div class="card-body">
                    <div class="capability-item">
                        <i class="fas fa-{{ $theme->responsive ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                        Responsive Design
                    </div>
                    <div class="capability-item">
                        <i class="fas fa-{{ $theme->rtl_support ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                        RTL Support
                    </div>
                    <div class="capability-item">
                        <i class="fas fa-{{ $theme->dark_mode ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                        Dark Mode
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($theme->tags && count($theme->tags) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Tags
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($theme->tags as $tag)
                                <span class="badge bg-light text-dark">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rating -->
            @if($theme->rating)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Rating
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="rating-display">
                            <div class="rating-stars">
                                @foreach($theme->rating_stars as $star)
                                    <i class="{{ $star }}"></i>
                                @endforeach
                            </div>
                            <div class="rating-value">{{ $theme->rating }} out of 5</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('super-admin.themes.edit', $theme) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Theme
                        </a>
                        <button class="btn btn-outline-secondary" onclick="duplicateTheme({{ $theme->id }})">
                            <i class="fas fa-copy me-2"></i>Duplicate Theme
                        </button>
                        <button class="btn btn-outline-info" onclick="exportTheme({{ $theme->id }})">
                            <i class="fas fa-download me-2"></i>Export Theme
                        </button>
                        <button class="btn btn-outline-danger delete-btn" 
                                data-theme-id="{{ $theme->id }}"
                                data-theme-name="{{ $theme->name }}"
                                data-companies-count="{{ $theme->companies->count() }}">
                            <i class="fas fa-trash me-2"></i>Delete Theme
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Screenshot Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Screenshot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalScreenshot" src="" alt="Screenshot" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the theme <strong id="themeName"></strong>?</p>
                <div class="alert alert-warning" id="companiesWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This theme is currently being used by <span id="companiesCount"></span> company(ies). 
                    Deleting it may affect those companies.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Theme</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.theme-details {
    padding: 20px 0;
}

.theme-preview-container {
    position: relative;
    height: 400px;
    border-radius: 10px;
    overflow: hidden;
}

.theme-preview-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.theme-preview-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.screenshot-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.screenshot-item:hover {
    transform: scale(1.05);
}

.screenshot-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 5px 0;
}

.capability-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
}

.color-sample {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.color-swatch {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-right: 10px;
}

.color-info {
    flex: 1;
}

.color-value {
    font-size: 0.9rem;
    color: #666;
    font-family: monospace;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.rating-display {
    text-align: center;
}

.rating-stars {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.rating-value {
    font-size: 1.1rem;
    font-weight: 500;
    color: #333;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    border-radius: 10px 10px 0 0;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-radius: 5px 0 0 5px;
}

.btn-group .btn:last-child {
    border-radius: 0 5px 5px 0;
}

@media (max-width: 768px) {
    .theme-preview-container {
        height: 250px;
    }
    
    .stat-item {
        margin-bottom: 15px;
    }
    
    .color-sample {
        margin-bottom: 10px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Screenshot modal
    $('.screenshot-image').on('click', function() {
        const screenshot = $(this).data('screenshot');
        $('#modalScreenshot').attr('src', screenshot);
    });
    
    // Toggle status
    $('.toggle-status-btn').on('click', function() {
        const themeId = $(this).data('theme-id');
        const currentStatus = $(this).data('current-status');
        const $btn = $(this);
        
        $.ajax({
            url: `/super-admin/themes/${themeId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error updating theme status. Please try again.');
            }
        });
    });
    
    // Delete theme
    $('.delete-btn').on('click', function() {
        const themeId = $(this).data('theme-id');
        const themeName = $(this).data('theme-name');
        const companiesCount = $(this).data('companies-count');
        
        $('#themeName').text(themeName);
        $('#deleteForm').attr('action', `/super-admin/themes/${themeId}`);
        
        if (companiesCount > 0) {
            $('#companiesCount').text(companiesCount);
            $('#companiesWarning').show();
        } else {
            $('#companiesWarning').hide();
        }
        
        $('#deleteModal').modal('show');
    });
});

function duplicateTheme(themeId) {
    if (confirm('Are you sure you want to duplicate this theme?')) {
        $.ajax({
            url: `/super-admin/themes/${themeId}/duplicate`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = `/super-admin/themes/${response.theme.id}`;
                }
            },
            error: function() {
                alert('Error duplicating theme. Please try again.');
            }
        });
    }
}

function exportTheme(themeId) {
    window.location.href = `/super-admin/themes/${themeId}/export`;
}
</script>
@endpush
