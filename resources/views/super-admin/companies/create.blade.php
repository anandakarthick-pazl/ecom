@extends('super-admin.layouts.app')

@section('title', 'Create New Company')
@section('page-title', 'Create New Company')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Company
                </h5>
                <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Companies
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
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
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="domain" class="form-label">Domain <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                                       id="domain" name="domain" value="{{ old('domain') }}" 
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
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city') }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" value="{{ old('state') }}">
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country') }}">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Admin User Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Admin User Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_name" class="form-label">Admin Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                                   id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                                            @error('admin_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_email" class="form-label">Admin Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                                   id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                                            @error('admin_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_password" class="form-label">Admin Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                                       id="admin_password" name="admin_password" required>
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                                </button>
                                                @error('admin_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">Minimum 8 characters required.</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-grid">
                                                <button type="button" class="btn btn-outline-primary" id="generatePassword">
                                                    <i class="fas fa-random me-2"></i>Generate Password
                                                </button>
                                            </div>
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
                                                <option value="{{ $theme->id }}" {{ old('theme_id') == $theme->id ? 'selected' : '' }}>
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
                                                        {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                                    {{ $package->name }} - ${{ $package->price }}/{{ $package->billing_cycle }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('package_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted" id="trialInfo"></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Company Logo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo Upload</label>
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
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Company
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
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        const passwordField = $('#admin_password');
        const passwordIcon = $('#passwordIcon');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Generate random password
    $('#generatePassword').on('click', function() {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        $('#admin_password').val(password);
    });
    
    // Show trial days info when package is selected
    $('#package_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const trialDays = selectedOption.data('trial-days');
        
        if (trialDays) {
            $('#trialInfo').text(`Includes ${trialDays} days free trial`);
        } else {
            $('#trialInfo').text('');
        }
    });
    
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
    
    // Auto-generate domain from company name
    $('#name').on('input', function() {
        const domainField = $('#domain');
        if (!domainField.val()) {
            const companyName = $(this).val().toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .replace(/^-+|-+$/g, '');
            if (companyName) {
                domainField.val(companyName + '.com');
            }
        }
    });
    
    // Auto-generate admin email from company email
    $('#email').on('input', function() {
        const adminEmailField = $('#admin_email');
        if (!adminEmailField.val()) {
            const companyEmail = $(this).val();
            if (companyEmail && companyEmail.includes('@')) {
                const domain = companyEmail.split('@')[1];
                adminEmailField.val('admin@' + domain);
            }
        }
    });
});
</script>
@endpush
