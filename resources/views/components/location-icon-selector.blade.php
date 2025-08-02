{{-- Location Icon Selector Component --}}
<div class="location-icon-selector">
    <div class="mb-3">
        <label for="{{ $fieldName ?? 'location_icon' }}" class="form-label">
            {{ $label ?? 'Location Icon' }} 
            @if($required ?? false)<span class="text-danger">*</span>@endif
        </label>
        <div class="input-group">
            <input type="text" 
                   class="form-control @error($fieldName ?? 'location_icon') is-invalid @enderror" 
                   id="{{ $fieldName ?? 'location_icon' }}" 
                   name="{{ $fieldName ?? 'location_icon' }}" 
                   value="{{ old($fieldName ?? 'location_icon', $value ?? '') }}" 
                   placeholder="{{ $placeholder ?? 'e.g., fas fa-map-marker-alt' }}"
                   {{ ($required ?? false) ? 'required' : '' }}>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#locationIconModal">
                <i class="fas fa-search"></i>
            </button>
        </div>
        @error($fieldName ?? 'location_icon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">{{ $helpText ?? 'Select a Font Awesome icon for the location' }}</div>
    </div>

    <!-- Location Icon Preview -->
    <div class="mb-3">
        <h6>Preview</h6>
        <div class="p-3 bg-light rounded">
            <div class="d-flex align-items-center">
                <div class="location-icon-preview me-3" style="background-color: {{ $previewColor ?? '#DC3545' }};">
                    <i class="{{ $value ?? 'fas fa-map-marker-alt' }} text-white"></i>
                </div>
                <div>
                    <div class="fw-semibold location-preview-name">{{ $previewName ?? 'Location' }}</div>
                    <small class="text-muted location-preview-class">{{ $value ?? 'fas fa-map-marker-alt' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Icon Selection Modal -->
<div class="modal fade" id="locationIconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Location Icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Icon Category Tabs -->
                <ul class="nav nav-tabs mb-3" id="iconCategoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="location-tab" data-bs-toggle="tab" data-bs-target="#location-icons" type="button" role="tab">
                            <i class="fas fa-map-marker-alt me-1"></i>Location Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-icons" type="button" role="tab">
                            <i class="fas fa-icons me-1"></i>General Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social-icons" type="button" role="tab">
                            <i class="fab fa-facebook me-1"></i>Social Icons
                        </button>
                    </li>
                </ul>

                <!-- Search -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="locationIconSearch" placeholder="Search icons...">
                </div>

                <!-- Icon Categories -->
                <div class="tab-content" id="iconCategoryContent">
                    <!-- Location Icons -->
                    <div class="tab-pane fade show active" id="location-icons" role="tabpanel">
                        <div class="row g-2 icon-grid">
                            @foreach(\App\Helpers\IconClass::getLocationIcons() as $iconClass => $iconName)
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 location-icon-btn" 
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
                                <button type="button" class="btn btn-outline-secondary w-100 location-icon-btn" 
                                        data-icon="{{ $iconClass }}">
                                    <i class="{{ $iconClass }} me-2"></i>{{ $iconName }}
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Social Icons -->
                    <div class="tab-pane fade" id="social-icons" role="tabpanel">
                        <div class="row g-2 icon-grid">
                            @foreach(\App\Helpers\IconClass::getSocialMediaIcons() as $iconClass => $iconName)
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100 location-icon-btn" 
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
    </div>
</div>

<style>
.location-icon-preview {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.location-icon-btn {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    text-align: left;
}

.location-icon-btn:hover {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
    transform: translateY(-1px);
}

.location-icon-btn:active {
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

#locationIconSearch {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    const fieldName = '{{ $fieldName ?? "location_icon" }}';
    const fieldSelector = '#' + fieldName;
    
    // Icon selection
    $('.location-icon-btn').click(function() {
        const iconClass = $(this).data('icon');
        $(fieldSelector).val(iconClass);
        $('#locationIconModal').modal('hide');
        updateLocationPreview();
    });

    // Icon search
    $('#locationIconSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.location-icon-btn').each(function() {
            const text = $(this).text().toLowerCase();
            const iconClass = $(this).data('icon').toLowerCase();
            
            if (text.includes(searchTerm) || iconClass.includes(searchTerm)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });

    // Update preview on input change
    $(fieldSelector).on('input', updateLocationPreview);

    function updateLocationPreview() {
        const iconClass = $(fieldSelector).val() || 'fas fa-map-marker-alt';
        const previewColor = '{{ $previewColor ?? "#DC3545" }}';
        
        $('.location-preview-class').text(iconClass);
        $('.location-icon-preview').css('background-color', previewColor);
        $('.location-icon-preview i').attr('class', iconClass + ' text-white');
    }
    
    // Initial preview update
    updateLocationPreview();
});
</script>
@endpush
