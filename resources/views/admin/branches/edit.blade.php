@extends('admin.layouts.app')

@section('title', 'Edit Branch')
@section('page_title', 'Edit Branch - ' . $branch->name)

@section('page_actions')
    <a href="{{ route('admin.branches.show', $branch) }}" class="btn btn-info">
        <i class="fas fa-eye"></i> View Details
    </a>
    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Branches
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('admin.branches.update', $branch) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Branch Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $branch->name) }}" 
                                           placeholder="Enter branch name"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Branch Code <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $branch->code) }}" 
                                           placeholder="e.g., BR001"
                                           required>
                                    <div class="form-text">Unique code to identify this branch</div>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Branch Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $branch->email) }}" 
                                           placeholder="branch@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Branch Phone</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $branch->phone) }}" 
                                           placeholder="+1234567890">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description about this branch">{{ old('description', $branch->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map-marker-alt"></i> Address Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      placeholder="Enter complete address">{{ old('address', $branch->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           id="city" 
                                           name="city" 
                                           value="{{ old('city', $branch->city) }}" 
                                           placeholder="Enter city">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" 
                                           class="form-control @error('state') is-invalid @enderror" 
                                           id="state" 
                                           name="state" 
                                           value="{{ old('state', $branch->state) }}" 
                                           placeholder="Enter state">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" 
                                           class="form-control @error('country') is-invalid @enderror" 
                                           id="country" 
                                           name="country" 
                                           value="{{ old('country', $branch->country) }}" 
                                           placeholder="Enter country">
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" 
                                           class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" 
                                           name="postal_code" 
                                           value="{{ old('postal_code', $branch->postal_code) }}" 
                                           placeholder="Enter postal code">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coordinates (Optional) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude', $branch->latitude) }}" 
                                           step="any"
                                           min="-90"
                                           max="90"
                                           placeholder="e.g., 40.7128">
                                    <div class="form-text">For location mapping (optional)</div>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude', $branch->longitude) }}" 
                                           step="any"
                                           min="-180"
                                           max="180"
                                           placeholder="e.g., -74.0060">
                                    <div class="form-text">For location mapping (optional)</div>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie"></i> Manager Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="manager_name" class="form-label">Manager Name</label>
                                    <input type="text" 
                                           class="form-control @error('manager_name') is-invalid @enderror" 
                                           id="manager_name" 
                                           name="manager_name" 
                                           value="{{ old('manager_name', $branch->manager_name) }}" 
                                           placeholder="Enter manager name">
                                    @error('manager_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="manager_email" class="form-label">Manager Email</label>
                                    <input type="email" 
                                           class="form-control @error('manager_email') is-invalid @enderror" 
                                           id="manager_email" 
                                           name="manager_email" 
                                           value="{{ old('manager_email', $branch->manager_email) }}" 
                                           placeholder="manager@example.com">
                                    @error('manager_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="manager_phone" class="form-label">Manager Phone</label>
                                    <input type="text" 
                                           class="form-control @error('manager_phone') is-invalid @enderror" 
                                           id="manager_phone" 
                                           name="manager_phone" 
                                           value="{{ old('manager_phone', $branch->manager_phone) }}" 
                                           placeholder="+1234567890">
                                    @error('manager_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.branches.show', $branch) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['name', 'code', 'status'];
        requiredFields.forEach(function(field) {
            const input = $(`[name="${field}"]`);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });
        
        // Validate coordinates if provided
        const latitude = parseFloat($('#latitude').val());
        const longitude = parseFloat($('#longitude').val());
        
        if ($('#latitude').val() && (latitude < -90 || latitude > 90)) {
            $('#latitude').addClass('is-invalid');
            isValid = false;
        }
        
        if ($('#longitude').val() && (longitude < -180 || longitude > 180)) {
            $('#longitude').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
    
    // Remove error styling on input
    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush
