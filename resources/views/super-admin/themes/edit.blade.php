@extends('super-admin.layouts.app')

@section('title', 'Edit Theme - ' . $theme->name)
@section('page-title', 'Edit Theme')

@section('content')
<div class="theme-creation">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('super-admin.themes.update', $theme) }}" method="POST" enctype="multipart/form-data" id="themeForm">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Theme Name *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $theme->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Category *</label>
                                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach(App\Models\SuperAdmin\Theme::CATEGORIES as $key => $name)
                                            <option value="{{ $key }}" {{ old('category', $theme->category) == $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" required>{{ old('description', $theme->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Layout Type</label>
                                    <select name="layout_type" class="form-select @error('layout_type') is-invalid @enderror">
                                        <option value="">Select Layout</option>
                                        @foreach(App\Models\SuperAdmin\Theme::LAYOUT_TYPES as $key => $name)
                                            <option value="{{ $key }}" {{ old('layout_type', $theme->layout_type) == $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('layout_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Difficulty Level</label>
                                    <select name="difficulty_level" class="form-select @error('difficulty_level') is-invalid @enderror">
                                        @foreach(App\Models\SuperAdmin\Theme::DIFFICULTY_LEVELS as $key => $name)
                                            <option value="{{ $key }}" {{ old('difficulty_level', $theme->difficulty_level) == $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('difficulty_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" 
                                   value="{{ old('author', $theme->author) }}" placeholder="Author/Company Name">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Preview & Media -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-images me-2"></i>Preview & Media
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Preview Image</label>
                                    <input type="file" name="preview_image" class="form-control @error('preview_image') is-invalid @enderror" 
                                           accept="image/*">
                                    <div class="form-text">Upload a new preview image (JPG, PNG, GIF, WebP - Max 2MB)</div>
                                    @error('preview_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($theme->preview_image)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $theme->preview_image) }}" 
                                                 alt="Current preview" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Demo URL</label>
                                    <input type="url" name="demo_url" class="form-control @error('demo_url') is-invalid @enderror" 
                                           value="{{ old('demo_url', $theme->demo_url) }}" placeholder="https://example.com/demo">
                                    @error('demo_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Screenshots (Multiple)</label>
                            <input type="file" name="screenshots[]" class="form-control @error('screenshots') is-invalid @enderror" 
                                   accept="image/*" multiple>
                            <div class="form-text">Upload new screenshots to replace existing ones</div>
                            @error('screenshots')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($theme->screenshots && count($theme->screenshots) > 0)
                                <div class="mt-2">
                                    <div class="row">
                                        @foreach($theme->screenshots as $screenshot)
                                            <div class="col-md-3 mb-2">
                                                <img src="{{ asset('storage/' . $screenshot) }}" 
                                                     alt="Screenshot" 
                                                     class="img-thumbnail" 
                                                     style="width: 100%; height: 100px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Color Scheme -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-palette me-2"></i>Color Scheme
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[primary]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.primary', $theme->color_scheme['primary'] ?? '#007bff') }}" title="Primary Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.primary', $theme->color_scheme['primary'] ?? '#007bff') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[secondary]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.secondary', $theme->color_scheme['secondary'] ?? '#6c757d') }}" title="Secondary Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.secondary', $theme->color_scheme['secondary'] ?? '#6c757d') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Accent Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[accent]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.accent', $theme->color_scheme['accent'] ?? '#28a745') }}" title="Accent Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.accent', $theme->color_scheme['accent'] ?? '#28a745') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Background Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[background]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.background', $theme->color_scheme['background'] ?? '#ffffff') }}" title="Background Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.background', $theme->color_scheme['background'] ?? '#ffffff') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Features & Tags -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Features & Tags
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Features</label>
                            <div id="featuresContainer">
                                @php
                                    $features = old('features', $theme->features ?: []);
                                    $features = is_array($features) ? $features : [];
                                @endphp
                                @if(count($features) > 0)
                                    @foreach($features as $feature)
                                        <div class="input-group mb-2">
                                            <input type="text" name="features[]" class="form-control" value="{{ $feature }}">
                                            <button type="button" class="btn btn-outline-danger remove-feature">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="features[]" class="form-control" placeholder="Enter feature">
                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addFeature">
                                <i class="fas fa-plus me-1"></i>Add Feature
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <div id="tagsContainer">
                                @php
                                    $tags = old('tags', $theme->tags ?: []);
                                    $tags = is_array($tags) ? $tags : [];
                                @endphp
                                @if(count($tags) > 0)
                                    @foreach($tags as $tag)
                                        <div class="input-group mb-2">
                                            <input type="text" name="tags[]" class="form-control" value="{{ $tag }}">
                                            <button type="button" class="btn btn-outline-danger remove-tag">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="tags[]" class="form-control" placeholder="Enter tag">
                                        <button type="button" class="btn btn-outline-danger remove-tag">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addTag">
                                <i class="fas fa-plus me-1"></i>Add Tag
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Pricing & Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>Pricing & Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_free" class="form-check-input" id="isFree" 
                                               {{ old('is_free', $theme->is_free) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isFree">
                                            Free Theme
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3" id="priceContainer" style="{{ old('is_free', $theme->is_free) ? 'display: none;' : '' }}">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $theme->price) }}" min="0" step="0.01">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', $theme->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $theme->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="responsive" class="form-check-input" id="responsive" 
                                           {{ old('responsive', $theme->responsive) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="responsive">
                                        Responsive Design
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="rtl_support" class="form-check-input" id="rtlSupport" 
                                           {{ old('rtl_support', $theme->rtl_support) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rtlSupport">
                                        RTL Support
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="dark_mode" class="form-check-input" id="darkMode" 
                                           {{ old('dark_mode', $theme->dark_mode) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="darkMode">
                                        Dark Mode Support
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <a href="{{ route('super-admin.themes.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Themes
                                </a>
                                <a href="{{ route('super-admin.themes.show', $theme) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Theme
                                </button>
                                <button type="submit" name="action" value="save_and_continue" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Update & Continue Editing
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Theme Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>Created:</strong>
                        <span>{{ $theme->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Last Updated:</strong>
                        <span>{{ $theme->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Companies Using:</strong>
                        <span>{{ $theme->companies->count() }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Downloads:</strong>
                        <span>{{ number_format($theme->downloads_count) }}</span>
                    </div>
                    @if($theme->rating)
                        <div class="info-item">
                            <strong>Rating:</strong>
                            <span>{{ $theme->rating }}/5</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Edit Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="guideline-item">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Important Notes</h6>
                        <ul class="list-unstyled small">
                            <li>• Changes may affect {{ $theme->companies->count() }} companies</li>
                            <li>• Test thoroughly before saving</li>
                            <li>• Backup existing files if needed</li>
                            <li>• Consider version control</li>
                        </ul>
                    </div>
                    
                    <div class="guideline-item">
                        <h6><i class="fas fa-palette text-primary me-2"></i>Color Updates</h6>
                        <ul class="list-unstyled small">
                            <li>• Color changes apply immediately</li>
                            <li>• Test contrast ratios</li>
                            <li>• Consider accessibility</li>
                            <li>• Preview on different devices</li>
                        </ul>
                    </div>
                    
                    <div class="guideline-item">
                        <h6><i class="fas fa-images text-info me-2"></i>Media Updates</h6>
                        <ul class="list-unstyled small">
                            <li>• New images replace existing ones</li>
                            <li>• Maintain aspect ratios</li>
                            <li>• Optimize for web</li>
                            <li>• Use consistent style</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.theme-creation {
    padding: 20px 0;
}

.form-control-color {
    width: 50px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem 0 0 0.375rem;
}

.guideline-item {
    margin-bottom: 20px;
}

.guideline-item h6 {
    margin-bottom: 10px;
    color: #495057;
}

.guideline-item ul li {
    margin-bottom: 5px;
    color: #6c757d;
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

.sticky-top {
    top: 20px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.input-group .form-control-color + .form-control {
    border-left: 0;
    border-radius: 0 0.375rem 0.375rem 0;
}

#featuresContainer .input-group,
#tagsContainer .input-group {
    margin-bottom: 10px;
}

.remove-feature,
.remove-tag {
    min-width: 45px;
}

.img-thumbnail {
    max-height: 150px;
    object-fit: cover;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle price field based on free theme checkbox
    $('#isFree').change(function() {
        if ($(this).is(':checked')) {
            $('#priceContainer').hide();
            $('input[name="price"]').val(0);
        } else {
            $('#priceContainer').show();
        }
    });
    
    // Color picker synchronization
    $('input[type="color"]').change(function() {
        $(this).next('input[type="text"]').val($(this).val());
    });
    
    $('input[type="text"]').on('input', function() {
        const colorInput = $(this).prev('input[type="color"]');
        if (colorInput.length && /^#[0-9A-F]{6}$/i.test($(this).val())) {
            colorInput.val($(this).val());
        }
    });
    
    // Add feature functionality
    $('#addFeature').click(function() {
        const newFeature = `
            <div class="input-group mb-2">
                <input type="text" name="features[]" class="form-control" placeholder="Enter feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        $('#featuresContainer').append(newFeature);
    });
    
    // Remove feature functionality
    $(document).on('click', '.remove-feature', function() {
        if ($('#featuresContainer .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        }
    });
    
    // Add tag functionality
    $('#addTag').click(function() {
        const newTag = `
            <div class="input-group mb-2">
                <input type="text" name="tags[]" class="form-control" placeholder="Enter tag">
                <button type="button" class="btn btn-outline-danger remove-tag">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        $('#tagsContainer').append(newTag);
    });
    
    // Remove tag functionality
    $(document).on('click', '.remove-tag', function() {
        if ($('#tagsContainer .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        }
    });
    
    // Form validation
    $('#themeForm').submit(function(e) {
        let isValid = true;
        
        // Check required fields
        $('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Check if at least one feature is provided
        const features = $('input[name="features[]"]').map(function() {
            return $(this).val();
        }).get().filter(Boolean);
        
        if (features.length === 0) {
            alert('Please add at least one feature for your theme.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
});
</script>
@endpush
