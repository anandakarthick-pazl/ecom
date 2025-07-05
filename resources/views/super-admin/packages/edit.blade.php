@extends('super-admin.layouts.app')

@section('title', 'Edit Package')
@section('page-title', 'Edit Package')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Package: {{ $package->name }}
                </h5>
                <div>
                    <a href="{{ route('super-admin.packages.show', $package) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('super-admin.packages.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Packages
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.packages.update', $package) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Package Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Package Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Package Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $package->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="active" {{ old('status', $package->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $package->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4" required>{{ old('description', $package->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Provide a detailed description of what this package includes.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pricing & Billing -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Pricing & Billing</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" name="price" value="{{ old('price', $package->price) }}" 
                                                       min="0" step="0.01" required>
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Set to 0 for free packages.</small>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_cycle" class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                                            <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                                                    id="billing_cycle" name="billing_cycle" required>
                                                <option value="">Select Billing Cycle</option>
                                                @foreach(App\Models\SuperAdmin\Package::BILLING_CYCLES as $key => $name)
                                                    <option value="{{ $key }}" {{ old('billing_cycle', $package->billing_cycle) == $key ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('billing_cycle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="trial_days" class="form-label">Trial Days <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('trial_days') is-invalid @enderror" 
                                                   id="trial_days" name="trial_days" value="{{ old('trial_days', $package->trial_days) }}" 
                                                   min="0" max="365" required>
                                            @error('trial_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Number of free trial days (0 for no trial).</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Features -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Package Features</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Features</label>
                                        <div id="features-container">
                                            @php
                                                $features = old('features', $package->features ?? []);
                                            @endphp
                                            @if(count($features) > 0)
                                                @foreach($features as $index => $feature)
                                                    <div class="input-group mb-2 feature-item">
                                                        <input type="text" class="form-control" name="features[]" 
                                                               value="{{ $feature }}" placeholder="Enter feature">
                                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="input-group mb-2 feature-item">
                                                    <input type="text" class="form-control" name="features[]" 
                                                           placeholder="Enter feature">
                                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature">
                                            <i class="fas fa-plus me-2"></i>Add Feature
                                        </button>
                                        <small class="form-text text-muted d-block mt-2">
                                            Add features that are included in this package (e.g., Unlimited Products, Email Support, etc.)
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Limits -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Package Limits</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Limits</label>
                                        <div id="limits-container">
                                            @php
                                                $limits = old('limits', $package->limits ?? []);
                                            @endphp
                                            @if(count($limits) > 0)
                                                @foreach($limits as $key => $value)
                                                    <div class="row mb-2 limit-item">
                                                        <div class="col-6">
                                                            <input type="text" class="form-control limit-key" 
                                                                   placeholder="Limit name (e.g., products)" value="{{ $key }}">
                                                        </div>
                                                        <div class="col-5">
                                                            <input type="text" class="form-control limit-value" 
                                                                   placeholder="Limit value (e.g., 100)" value="{{ $value }}">
                                                        </div>
                                                        <div class="col-1">
                                                            <button type="button" class="btn btn-outline-danger remove-limit">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row mb-2 limit-item">
                                                    <div class="col-6">
                                                        <input type="text" class="form-control limit-key" 
                                                               placeholder="Limit name (e.g., products)">
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="text" class="form-control limit-value" 
                                                               placeholder="Limit value (e.g., 100)">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="button" class="btn btn-outline-danger remove-limit">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-limit">
                                            <i class="fas fa-plus me-2"></i>Add Limit
                                        </button>
                                        <small class="form-text text-muted d-block mt-2">
                                            Set limits for this package (e.g., products: 100, storage: 5GB, etc.)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Package Configuration & Statistics -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Package Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" 
                                                   value="1" {{ old('is_popular', $package->is_popular) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_popular">
                                                <strong>Popular Package</strong>
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Mark this package as popular to highlight it.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" 
                                               min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Lower numbers appear first.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Package Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Package Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="mb-1 text-primary">{{ $package->companies->count() }}</h4>
                                                <small class="text-muted">Companies</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="mb-1 text-success">{{ $package->companies->where('status', 'active')->count() }}</h4>
                                            <small class="text-muted">Active</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Created: {{ $package->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Live Preview -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Live Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="package-preview-card" id="package-preview">
                                        <div class="text-center mb-3">
                                            <div class="popular-badge" {{ $package->is_popular ? '' : 'style=display:none;' }}>
                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="fas fa-star me-1"></i>POPULAR
                                                </span>
                                            </div>
                                            <h5 class="preview-title">{{ $package->name }}</h5>
                                            <div class="mb-2">
                                                <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }} preview-status">{{ ucfirst($package->status) }}</span>
                                                <span class="badge bg-info preview-billing">{{ $package->billing_cycle_name }}</span>
                                            </div>
                                            <h2 class="text-primary preview-price">${{ number_format($package->price, 2) }}</h2>
                                            <small class="text-success preview-trial" {{ $package->trial_days > 0 ? '' : 'style=display:none;' }}>
                                                <i class="fas fa-gift me-1"></i>{{ $package->trial_days }}-day free trial
                                            </small>
                                        </div>
                                        
                                        <div class="preview-description text-muted mb-3">
                                            {{ Str::limit($package->description, 100) }}
                                        </div>
                                        
                                        <div class="preview-features">
                                            <h6 class="text-muted mb-2">Features:</h6>
                                            <ul class="list-unstyled" id="preview-features-list">
                                                @if($package->features && count($package->features) > 0)
                                                    @foreach($package->features as $feature)
                                                        <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                                    @endforeach
                                                @else
                                                    <li class="text-muted">No features added yet</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super-admin.packages.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Package
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.package-preview-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    background: #f8f9fa;
}

.feature-item, .limit-item {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.popular-badge {
    position: relative;
    top: -10px;
}

#preview-features-list li {
    margin-bottom: 5px;
    font-size: 0.9rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Add feature functionality
    $('#add-feature').on('click', function() {
        const featureHtml = `
            <div class="input-group mb-2 feature-item">
                <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        $('#features-container').append(featureHtml);
        updatePreview();
    });
    
    // Remove feature functionality
    $(document).on('click', '.remove-feature', function() {
        if ($('.feature-item').length > 1) {
            $(this).closest('.feature-item').fadeOut(300, function() {
                $(this).remove();
                updatePreview();
            });
        }
    });
    
    // Add limit functionality
    $('#add-limit').on('click', function() {
        const limitHtml = `
            <div class="row mb-2 limit-item">
                <div class="col-6">
                    <input type="text" class="form-control limit-key" placeholder="Limit name (e.g., products)">
                </div>
                <div class="col-5">
                    <input type="text" class="form-control limit-value" placeholder="Limit value (e.g., 100)">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-outline-danger remove-limit">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#limits-container').append(limitHtml);
    });
    
    // Remove limit functionality
    $(document).on('click', '.remove-limit', function() {
        if ($('.limit-item').length > 1) {
            $(this).closest('.limit-item').fadeOut(300, function() {
                $(this).remove();
            });
        }
    });
    
    // Live preview updates
    function updatePreview() {
        const name = $('#name').val() || '{{ $package->name }}';
        const description = $('#description').val() || '{{ $package->description }}';
        const price = $('#price').val() || '{{ $package->price }}';
        const billingCycle = $('#billing_cycle option:selected').text() || '{{ $package->billing_cycle_name }}';
        const status = $('#status').val() || '{{ $package->status }}';
        const isPopular = $('#is_popular').is(':checked');
        const trialDays = $('#trial_days').val() || '{{ $package->trial_days }}';
        
        $('.preview-title').text(name);
        $('.preview-description').text(description.substring(0, 100) + (description.length > 100 ? '...' : ''));
        $('.preview-price').text('$' + parseFloat(price).toFixed(2));
        $('.preview-billing').text(billingCycle);
        $('.preview-status').removeClass('bg-success bg-secondary')
                           .addClass(status === 'active' ? 'bg-success' : 'bg-secondary')
                           .text(status.charAt(0).toUpperCase() + status.slice(1));
        
        // Popular badge
        if (isPopular) {
            $('.popular-badge').show();
        } else {
            $('.popular-badge').hide();
        }
        
        // Trial info
        if (parseInt(trialDays) > 0) {
            $('.preview-trial').text(`${trialDays}-day free trial`).show();
        } else {
            $('.preview-trial').hide();
        }
        
        // Features
        const features = $('input[name="features[]"]').map(function() {
            return $(this).val().trim();
        }).get().filter(Boolean);
        
        const $featuresList = $('#preview-features-list');
        $featuresList.empty();
        
        if (features.length > 0) {
            features.forEach(function(feature) {
                $featuresList.append(`<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`);
            });
        } else {
            $featuresList.append('<li class="text-muted">No features added yet</li>');
        }
    }
    
    // Update preview on input changes
    $('#name, #description, #price, #billing_cycle, #status, #trial_days').on('input change', updatePreview);
    $('#is_popular').on('change', updatePreview);
    $(document).on('input', 'input[name="features[]"]', updatePreview);
    
    // Form submission - convert limits to proper format
    $('form').on('submit', function(e) {
        const limits = {};
        $('.limit-item').each(function() {
            const key = $(this).find('.limit-key').val().trim();
            const value = $(this).find('.limit-value').val().trim();
            if (key && value) {
                limits[key] = value;
            }
        });
        
        // Remove existing limit inputs and add hidden field
        $('input[name^="limits"]').remove();
        
        // Add limits as hidden fields
        Object.keys(limits).forEach(function(key) {
            $('<input>').attr({
                type: 'hidden',
                name: `limits[${key}]`,
                value: limits[key]
            }).appendTo('form');
        });
    });
});
</script>
@endpush
