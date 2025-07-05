@extends('admin.layouts.app')

@section('title', 'Profile Settings')
@section('page_title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user"></i> Profile Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Profile Picture Section -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <div class="profile-picture-container">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" 
                                             alt="Profile Picture" 
                                             class="rounded-circle profile-picture"
                                             style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #e3e6f0;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                             style="width: 120px; height: 120px; border: 4px solid #e3e6f0; margin: 0 auto;">
                                            <i class="fas fa-user fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-camera"></i> Change Picture
                                    </label>
                                    <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*">
                                    <small class="form-text text-muted d-block mt-2">
                                        Recommended: Square image, max 2MB
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user"></i> Full Name *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email Address *
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="role" class="form-label">
                                        <i class="fas fa-user-tag"></i> Role
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ ucfirst($user->role ?? 'Administrator') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Address
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Account Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Member Since
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->created_at->format('M d, Y') }}" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-clock"></i> Last Updated
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $user->updated_at->format('M d, Y H:i A') }}" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Settings
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Card -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lock"></i> Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.password') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="current_password" class="form-label">
                                Current Password *
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">
                                        New Password *
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    <small class="text-muted">Minimum 8 characters</small>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">
                                        Confirm New Password *
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Profile picture preview
    $('#avatar').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.profile-picture').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Password strength indicator (optional enhancement)
    $('#password').on('input', function() {
        const password = $(this).val();
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        // You can add a strength indicator here if needed
    });
});
</script>
@endpush
