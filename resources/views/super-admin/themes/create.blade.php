@extends('super-admin.layouts.app')

@section('title', 'Create New Theme')
@section('page-title', 'Create New Theme')

@section('content')
<div class="theme-creation">
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('super-admin.themes.store') }}" method="POST" enctype="multipart/form-data" id="themeForm">
                @csrf
                
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
                                           value="{{ old('name') }}" required>
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
                                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $name }}</option>
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
                                      rows="4" required>{{ old('description') }}</textarea>
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
                                            <option value="{{ $key }}" {{ old('layout_type') == $key ? 'selected' : '' }}>{{ $name }}</option>
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
                                            <option value="{{ $key }}" {{ old('difficulty_level', 'beginner') == $key ? 'selected' : '' }}>{{ $name }}</option>
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
                                   value="{{ old('author') }}" placeholder="Author/Company Name">
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
                                    <label class="form-label">Preview Image *</label>
                                    <input type="file" name="preview_image" class="form-control @error('preview_image') is-invalid @enderror" 
                                           accept="image/*" required>
                                    <div class="form-text">Upload a preview image (JPG, PNG, GIF - Max 2MB)</div>
                                    @error('preview_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Demo URL</label>
                                    <input type="url" name="demo_url" class="form-control @error('demo_url') is-invalid @enderror" 
                                           value="{{ old('demo_url') }}" placeholder="https://example.com/demo">
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
                            <div class="form-text">Upload multiple screenshots to showcase your theme</div>
                            @error('screenshots')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                               value="{{ old('color_scheme.primary', '#007bff') }}" title="Primary Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.primary', '#007bff') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[secondary]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.secondary', '#6c757d') }}" title="Secondary Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.secondary', '#6c757d') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Accent Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[accent]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.accent', '#28a745') }}" title="Accent Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.accent', '#28a745') }}" 
                                               onchange="this.previousElementSibling.value = this.value">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Background Color</label>
                                    <div class="input-group">
                                        <input type="color" name="color_scheme[background]" class="form-control form-control-color" 
                                               value="{{ old('color_scheme.background', '#ffffff') }}" title="Background Color">
                                        <input type="text" class="form-control" value="{{ old('color_scheme.background', '#ffffff') }}" 
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
                                @if(old('features'))
                                    @foreach(old('features') as $feature)
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
                                @if(old('tags'))
                                    @foreach(old('tags') as $tag)
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
                                               {{ old('is_free') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isFree">
                                            Free Theme
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3" id="priceContainer" style="{{ old('is_free') ? 'display: none;' : '' }}">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', 0) }}" min="0" step="0.01">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                           {{ old('responsive', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="responsive">
                                        Responsive Design
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="rtl_support" class="form-check-input" id="rtlSupport" 
                                           {{ old('rtl_support') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rtlSupport">
                                        RTL Support
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="dark_mode" class="form-check-input" id="darkMode" 
                                           {{ old('dark_mode') ? 'checked' : '' }}>
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
                            <a href="{{ route('super-admin.themes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Themes
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save Theme
                                </button>
                                <button type="submit" name="action" value="save_and_continue" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Save & Continue Editing
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
                        <i class="fas fa-lightbulb me-2"></i>Theme Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="guideline-item">
                        <h6><i class="fas fa-check-circle text-success me-2"></i>Best Practices</h6>
                        <ul class="list-unstyled small">
                            <li>• Use high-quality preview images</li>
                            <li>• Provide clear, descriptive names</li>
                            <li>• Add comprehensive feature lists</li>
                            <li>• Include demo URLs when available</li>
                            <li>• Test responsiveness on all devices</li>
                        </ul>
                    </div>
                    
                    <div class="guideline-item">
                        <h6><i class="fas fa-palette text-primary me-2"></i>Color Guidelines</h6>
                        <ul class="list-unstyled small">
                            <li>• Choose colors that work well together</li>
                            <li>• Ensure good contrast for readability</li>
                            <li>• Test colors on different backgrounds</li>
                            <li>• Consider color psychology</li>
                        </ul>
                    </div>
                    
                    <div class="guideline-item">
                        <h6><i class="fas fa-images text-info me-2"></i>Image Requirements</h6>
                        <ul class="list-unstyled small">
                            <li>• Preview: 1200x800px minimum</li>
                            <li>• Screenshots: 1920x1080px preferred</li>
                            <li>• Format: JPG, PNG, WebP</li>
                            <li>• Max file size: 2MB per image</li>
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
