@extends('super-admin.layouts.app')

@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Company: {{ $company->name }}
                </h5>
                <div>
                    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Companies
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Company Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Company Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                                       id="domain" name="domain" value="{{ old('domain', $company->domain) }}" 
                                                       placeholder="example.com" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-globe"></i>
                                                </span>
                                                @error('domain')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Enter domain without http:// or www.</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $company->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city', $company->city) }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" value="{{ old('state', $company->state) }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country', $company->country) }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                   id="postal_code" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                                <option value="suspended" {{ old('status', $company->status) === 'suspended' ? 'selected' : '' }}>
                                                    Suspended
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Configuration & Logo -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Configuration</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="theme_id" class="form-label">Theme <span class="text-danger">*</span></label>
                                        <select class="form-select @error('theme_id') is-invalid @enderror" 
                                                id="theme_id" name="theme_id" required>
                                            <option value="">Select Theme</option>
                                            @foreach($themes as $theme)
                                                <option value="{{ $theme->id }}" 
                                                        {{ old('theme_id', $company->theme_id) == $theme->id ? 'selected' : '' }}>
                                                    {{ $theme->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('theme_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="package_id" class="form-label">Package <span class="text-danger">*</span></label>
                                        <select class="form-select @error('package_id') is-invalid @enderror" 
                                                id="package_id" name="package_id" required>
                                            <option value="">Select Package</option>
                                            @foreach($packages as $package)
                                                <option value="{{ $package->id }}" 
                                                        data-trial-days="{{ $package->trial_days }}"
                                                        {{ old('package_id', $company->package_id) == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - ${{ $package->price }}/{{ $package->billing_cycle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('package_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    @if($company->trial_ends_at)
                                        <div class="alert alert-info">
                                            <small>
                                                <strong>Trial Status:</strong><br>
                                                Ends: {{ $company->trial_ends_at->format('M d, Y g:i A') }}<br>
                                                <span class="badge {{ $company->trial_ends_at->isPast() ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $company->trial_ends_at->isPast() ? 'Expired' : $company->trial_ends_at->diffForHumans() }}
                                                </span>
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Company Logo</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Current Logo -->
                                    @if($company->logo)
                                        <div class="mb-3">
                                            <label class="form-label">Current Logo</label>
                                            <div class="text-center">
                                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                                     class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">
                                            {{ $company->logo ? 'Change Logo' : 'Upload Logo' }}
                                        </label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                               id="logo" name="logo" accept="image/*">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB.
                                        </small>
                                    </div>
                                    
                                    <div id="logoPreview" class="text-center d-none">
                                        <img id="previewImage" src="" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeLogo">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Company Stats -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Company Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h5 class="mb-1">{{ $company->users()->count() }}</h5>
                                                <small class="text-muted">Users</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="mb-1">{{ $company->created_at->diffInDays(now()) }}</h5>
                                            <small class="text-muted">Days Old</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            Created: {{ $company->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Company
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

@push('scripts')
<script>
$(document).ready(function() {
    // Logo preview
    $('#logo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#logoPreview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove logo preview
    $('#removeLogo').on('click', function() {
        $('#logo').val('');
        $('#logoPreview').addClass('d-none');
    });
});
</script>
@endpush
