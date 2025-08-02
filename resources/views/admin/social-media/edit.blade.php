@extends('admin.layouts.app')

@section('title', 'Edit Social Media Link')
@section('page_title', 'Edit Social Media Link')

@push('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Social Media Link</h5>
                <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.social-media.update', $social_medium) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Platform Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $social_medium->name) }}" 
                                       placeholder="e.g., Facebook, Twitter, Instagram" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon_class" class="form-label">Icon Class <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('icon_class') is-invalid @enderror" 
                                           id="icon_class" name="icon_class" value="{{ old('icon_class', $social_medium->icon_class) }}" 
                                           placeholder="e.g., fab fa-facebook-f" required>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                @error('icon_class')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Font Awesome icon class (e.g., fab fa-facebook-f)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="url" class="form-label">Profile URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control @error('url') is-invalid @enderror" 
                               id="url" name="url" value="{{ old('url', $social_medium->url) }}" 
                               placeholder="https://facebook.com/yourpage" required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Enter the complete URL to your social media profile or page</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Brand Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', $social_medium->color ?: '#6c757d') }}" title="Choose brand color">
                                    <input type="text" class="form-control" id="color_text" 
                                           placeholder="#6c757d" value="{{ old('color', $social_medium->color ?: '#6c757d') }}">
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Brand color for the social media icon</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $social_medium->sort_order) }}" 
                                       min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Lower numbers appear first</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $social_medium->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Display on website)
                            </label>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="mb-4">
                        <h6>Preview</h6>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon me-3" id="preview-icon" style="background-color: {{ $social_medium->brand_color }};">
                                    <i class="{{ $social_medium->icon_class }} text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" id="preview-name">{{ $social_medium->name }}</div>
                                    <small class="text-muted" id="preview-icon-class">{{ $social_medium->icon_class }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Social Media Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Icon Selection Modal -->
<div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iconModalLabel">Select Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Icon Category Tabs -->
                <ul class="nav nav-tabs mb-3" id="iconCategoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="social-tab" data-bs-toggle="tab" data-bs-target="#social-icons" type="button" role="tab">
                            <i class="fab fa-facebook me-1"></i>Social Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#location-icons" type="button" role="tab">
                            <i class="fas fa-map-marker-alt me-1"></i>Location Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-icons" type="button" role="tab">
                            <i class="fas fa-icons me-1"></i>General Icons
                        </button>
                    </li>
                </ul>

                <!-- Search -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Search icons...">
                </div>

                <!-- Icon Categories -->
                <div class="tab-content" id="iconCategoryContent">
                    <!-- Social Icons -->
                    <div class="tab-pane fade show active" id="social-icons" role="tabpanel">
                        <div class="row g-2 icon-grid">
                            @foreach(\App\Helpers\IconClass::getSocialMediaIcons() as $iconClass => $iconName)
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 icon-select-btn" 
                                        data-icon="{{ $iconClass }}">
                                    <i class="{{ $iconClass }} me-2"></i>{{ $iconName }}
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Location Icons -->
                    <div class="tab-pane fade" id="location-icons" role="tabpanel">
                        <div class="row g-2 icon-grid">
                            @foreach(\App\Helpers\IconClass::getLocationIcons() as $iconClass => $iconName)
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 icon-select-btn" 
                                        data-icon="{{ $iconClass }}">
                                    <i class="{{ $iconClass }} me-2"></i>{{ $iconName }}
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- General Icons -->
                    <div class="tab-pane fade" id="general-icons" role="tabpanel">
                        <div class="row g-2 icon-grid">
                            @foreach(\App\Helpers\IconClass::getGeneralIcons() as $iconClass => $iconName)
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 icon-select-btn" 
                                        data-icon="{{ $iconClass }}">
                                    <i class="{{ $iconClass }} me-2"></i>{{ $iconName }}
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.icon-select-btn {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    text-align: left;
}

.icon-select-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    transform: translateY(-1px);
}

.icon-select-btn:active {
    transform: translateY(0);
}

.icon-grid .col-md-4 {
    margin-bottom: 0.5rem;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.nav-tabs .nav-link.active {
    color: var(--bs-primary);
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

#iconSearch {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Fix for modal backdrop issue */
.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    // Color picker synchronization
    $('#color').on('input', function() {
        $('#color_text').val($(this).val());
        updatePreview();
    });
    
    $('#color_text').on('input', function() {
        const color = $(this).val();
        if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
            $('#color').val(color);
            updatePreview();
        }
    });

    // Update preview on input changes
    $('#name, #icon_class').on('input', updatePreview);

    // Icon selection
    $(document).on('click', '.icon-select-btn', function() {
        const iconClass = $(this).data('icon');
        $('#icon_class').val(iconClass);
        
        // Close modal
        $('#iconModal').modal('hide');
        
        updatePreview();
    });

    // Icon search functionality
    $('#iconSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.icon-select-btn').each(function() {
            const text = $(this).text().toLowerCase();
            const iconClass = $(this).data('icon').toLowerCase();
            
            if (text.includes(searchTerm) || iconClass.includes(searchTerm)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });

    // Handle modal show event
    $('#iconModal').on('show.bs.modal', function () {
        // Clear search when modal opens
        $('#iconSearch').val('');
        $('.icon-select-btn').parent().show();
    });

    // Modal trigger click handler with debugging
    $('.btn[data-bs-toggle="modal"]').on('click', function(e) {
        console.log('Modal trigger clicked');
        
        // Check if Bootstrap is loaded
        if (typeof bootstrap === 'undefined' && typeof $.fn.modal === 'undefined') {
            console.error('Neither Bootstrap 5 nor jQuery modal is available');
            
            // Fallback: show modal manually
            $('#iconModal').show();
            $('body').addClass('modal-open');
            
            // Add backdrop
            if (!$('.modal-backdrop').length) {
                $('body').append('<div class="modal-backdrop fade show"></div>');
            }
            
            e.preventDefault();
            return false;
        }
        
        // Check if modal element exists
        const modalId = $(this).data('bs-target');
        if (!$(modalId).length) {
            console.error('Modal element not found:', modalId);
            e.preventDefault();
            return false;
        }
        
        console.log('Modal should open:', modalId);
    });
    
    // Manual modal close for fallback
    $(document).on('click', '.btn-close, [data-bs-dismiss="modal"]', function() {
        $('#iconModal').hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
    
    // Close modal when clicking outside
    $(document).on('click', '.modal-backdrop', function() {
        $('#iconModal').hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });

    function updatePreview() {
        const name = $('#name').val() || '{{ $social_medium->name }}';
        const iconClass = $('#icon_class').val() || '{{ $social_medium->icon_class }}';
        const color = $('#color').val() || '{{ $social_medium->brand_color }}';
        
        $('#preview-name').text(name);
        $('#preview-icon-class').text(iconClass);
        $('#preview-icon').css('background-color', color);
        $('#preview-icon i').attr('class', iconClass + ' text-white');
    }
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
@endsection
