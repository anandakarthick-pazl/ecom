@extends('admin.layouts.app')

@section('title', 'Add Social Media Link')
@section('page_title', 'Add Social Media Link')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Social Media Link</h5>
                <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.social-media.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Platform Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
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
                                           id="icon_class" name="icon_class" value="{{ old('icon_class') }}" 
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
                               id="url" name="url" value="{{ old('url') }}" 
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
                                           id="color" name="color" value="{{ old('color', '#6c757d') }}" title="Choose brand color">
                                    <input type="text" class="form-control" id="color_text" 
                                           placeholder="#6c757d" value="{{ old('color', '#6c757d') }}">
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
                                       id="sort_order" name="sort_order" value="{{ old('sort_order') }}" 
                                       placeholder="Leave blank for auto" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Lower numbers appear first (leave blank for automatic ordering)</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Display on website)
                            </label>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-4">
                        <h6>Preview</h6>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="social-icon me-3" id="preview-icon" style="background-color: #6c757d;">
                                    <i class="fas fa-share-alt text-white"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" id="preview-name">Platform Name</div>
                                    <small class="text-muted" id="preview-icon-class">Icon Class</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.social-media.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Add Social Media Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Select from Predefined Platforms -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Select from Popular Platforms</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach(\App\Helpers\IconClass::getPredefinedPlatforms() as $key => $platform)
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary platform-btn"
                                    data-name="{{ $platform['name'] }}"
                                    data-icon="{{ $platform['icon_class'] }}"
                                    data-color="{{ $platform['color'] }}"
                                    data-placeholder="{{ $platform['placeholder'] }}">
                                <div class="d-flex align-items-center">
                                    <div class="social-icon me-2" style="background-color: {{ $platform['color'] }};">
                                        <i class="{{ $platform['icon_class'] }} text-white"></i>
                                    </div>
                                    {{ $platform['name'] }}
                                </div>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Icon Selection Modal -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="iconSearch" placeholder="Search icons...">
                </div>
                <div class="row g-2" id="iconGrid">
                    @php
                    $allIcons = \App\Helpers\IconClass::getAllIcons();
                    @endphp
                    
                    @foreach($allIcons as $iconClass => $iconName)
                    <div class="col-md-3">
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

.platform-btn {
    text-align: left;
    padding: 0.75rem;
}

.platform-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.platform-btn:hover .social-icon {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

.icon-select-btn {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.icon-select-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
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

    // Platform button clicks
    $('.platform-btn').click(function() {
        const name = $(this).data('name');
        const icon = $(this).data('icon');
        const color = $(this).data('color');
        const placeholder = $(this).data('placeholder');
        
        $('#name').val(name);
        $('#icon_class').val(icon);
        $('#color').val(color);
        $('#color_text').val(color);
        $('#url').attr('placeholder', placeholder);
        
        updatePreview();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#name').offset().top - 100
        }, 500);
    });

    // Icon selection
    $('.icon-select-btn').click(function() {
        const iconClass = $(this).data('icon');
        $('#icon_class').val(iconClass);
        $('#iconModal').modal('hide');
        updatePreview();
    });

    // Icon search
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

    function updatePreview() {
        const name = $('#name').val() || 'Platform Name';
        const iconClass = $('#icon_class').val() || 'fas fa-share-alt';
        const color = $('#color').val() || '#6c757d';
        
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
