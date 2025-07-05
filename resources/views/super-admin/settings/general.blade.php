@extends('super-admin.layouts.app')

@section('title', 'General Settings')
@section('page-title', 'General Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>General System Settings
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.settings.general.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Site Information -->
                            <div class="mb-4">
                                <h6>Site Information</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name</label>
                                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                                   id="site_name" name="site_name" 
                                                   value="{{ old('site_name', $settings['site_name'] ?? 'Multi-Tenant E-commerce') }}" required>
                                            @error('site_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="site_tagline" class="form-label">Site Tagline</label>
                                            <input type="text" class="form-control @error('site_tagline') is-invalid @enderror" 
                                                   id="site_tagline" name="site_tagline" 
                                                   value="{{ old('site_tagline', $settings['site_tagline'] ?? 'Build Your E-commerce Empire') }}">
                                            @error('site_tagline')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_description" class="form-label">Site Description</label>
                                    <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                              id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description'] ?? 'A powerful multi-tenant e-commerce platform for building and managing online stores.') }}</textarea>
                                    @error('site_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="admin_email" class="form-label">Admin Email</label>
                                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                                   id="admin_email" name="admin_email" 
                                                   value="{{ old('admin_email', $settings['admin_email'] ?? 'admin@example.com') }}" required>
                                            @error('admin_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="support_email" class="form-label">Support Email</label>
                                            <input type="email" class="form-control @error('support_email') is-invalid @enderror" 
                                                   id="support_email" name="support_email" 
                                                   value="{{ old('support_email', $settings['support_email'] ?? 'support@example.com') }}">
                                            @error('support_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System Configuration -->
                            <div class="mb-4">
                                <h6>System Configuration</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Default Timezone</label>
                                            <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                                                <option value="">Select Timezone</option>
                                                @foreach(['UTC', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Tokyo', 'Asia/Shanghai', 'Australia/Sydney'] as $tz)
                                                    <option value="{{ $tz }}" {{ old('timezone', $settings['timezone'] ?? 'UTC') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                                @endforeach
                                            </select>
                                            @error('timezone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_format" class="form-label">Date Format</label>
                                            <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format" required>
                                                <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                                <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                                <option value="d/m/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                                <option value="M j, Y" {{ old('date_format', $settings['date_format'] ?? '') == 'M j, Y' ? 'selected' : '' }}>Mon DD, YYYY</option>
                                            </select>
                                            @error('date_format')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Default Currency</label>
                                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                                <option value="">Select Currency</option>
                                                @foreach(['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CNY', 'INR'] as $currency)
                                                    <option value="{{ $currency }}" {{ old('currency', $settings['currency'] ?? 'USD') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="items_per_page" class="form-label">Items Per Page</label>
                                            <select class="form-select @error('items_per_page') is-invalid @enderror" id="items_per_page" name="items_per_page" required>
                                                @foreach([10, 15, 20, 25, 50] as $count)
                                                    <option value="{{ $count }}" {{ old('items_per_page', $settings['items_per_page'] ?? 15) == $count ? 'selected' : '' }}>{{ $count }}</option>
                                                @endforeach
                                            </select>
                                            @error('items_per_page')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Registration Settings -->
                            <div class="mb-4">
                                <h6>Registration & Trial Settings</h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration" value="1" 
                                           {{ old('allow_registration', $settings['allow_registration'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_registration">
                                        Allow New Company Registration
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_verification_required" name="email_verification_required" value="1" 
                                           {{ old('email_verification_required', $settings['email_verification_required'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verification_required">
                                        Require Email Verification
                                    </label>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default_trial_days" class="form-label">Default Trial Days</label>
                                            <input type="number" class="form-control @error('default_trial_days') is-invalid @enderror" 
                                                   id="default_trial_days" name="default_trial_days" 
                                                   value="{{ old('default_trial_days', $settings['default_trial_days'] ?? 14) }}" min="0" max="365">
                                            @error('default_trial_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_trial_extensions" class="form-label">Max Trial Extensions</label>
                                            <input type="number" class="form-control @error('max_trial_extensions') is-invalid @enderror" 
                                                   id="max_trial_extensions" name="max_trial_extensions" 
                                                   value="{{ old('max_trial_extensions', $settings['max_trial_extensions'] ?? 2) }}" min="0" max="10">
                                            @error('max_trial_extensions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Security Settings -->
                            <div class="mb-4">
                                <h6>Security Settings</h6>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" 
                                           {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Maintenance Mode
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="maintenance_message" class="form-label">Maintenance Message</label>
                                    <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3">{{ old('maintenance_message', $settings['maintenance_message'] ?? 'We are currently performing scheduled maintenance. Please check back soon.') }}</textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="session_lifetime" class="form-label">Session Lifetime (minutes)</label>
                                            <input type="number" class="form-control @error('session_lifetime') is-invalid @enderror" 
                                                   id="session_lifetime" name="session_lifetime" 
                                                   value="{{ old('session_lifetime', $settings['session_lifetime'] ?? 120) }}" min="15" max="1440">
                                            @error('session_lifetime')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                            <input type="number" class="form-control @error('max_login_attempts') is-invalid @enderror" 
                                                   id="max_login_attempts" name="max_login_attempts" 
                                                   value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}" min="3" max="10">
                                            @error('max_login_attempts')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <!-- Logos and Branding -->
                            <div class="mb-4">
                                <h6>Logos and Branding</h6>
                                
                                <!-- Site Logo -->
                                <div class="mb-3">
                                    <label for="site_logo" class="form-label">Site Logo</label>
                                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                                                 class="img-fluid rounded" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('site_logo') is-invalid @enderror" 
                                           id="site_logo" name="site_logo" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 300x100px</small>
                                    @error('site_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Favicon -->
                                <div class="mb-3">
                                    <label for="favicon" class="form-label">Favicon</label>
                                    @if(isset($settings['favicon']) && $settings['favicon'])
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $settings['favicon']) }}" 
                                                 class="img-fluid rounded" style="max-height: 32px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('favicon') is-invalid @enderror" 
                                           id="favicon" name="favicon" accept="image/*">
                                    <small class="form-text text-muted">32x32px ICO or PNG file</small>
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Primary Color -->
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Primary Brand Color</label>
                                    <input type="color" class="form-control form-control-color" 
                                           id="primary_color" name="primary_color" 
                                           value="{{ old('primary_color', $settings['primary_color'] ?? '#667eea') }}">
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="mb-4">
                                <h6>Contact Information</h6>
                                
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" 
                                           value="{{ old('company_name', $settings['company_name'] ?? '') }}">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company_address" class="form-label">Company Address</label>
                                    <textarea class="form-control @error('company_address') is-invalid @enderror" 
                                              id="company_address" name="company_address" rows="3">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                                    @error('company_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="company_phone" class="form-label">Company Phone</label>
                                    <input type="tel" class="form-control @error('company_phone') is-invalid @enderror" 
                                           id="company_phone" name="company_phone" 
                                           value="{{ old('company_phone', $settings['company_phone'] ?? '') }}">
                                    @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Analytics and Tracking -->
                            <div class="mb-4">
                                <h6>Analytics and Tracking</h6>
                                
                                <div class="mb-3">
                                    <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                    <input type="text" class="form-control @error('google_analytics_id') is-invalid @enderror" 
                                           id="google_analytics_id" name="google_analytics_id" 
                                           value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" 
                                           placeholder="GA-XXXXXXXXX-X">
                                    @error('google_analytics_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="facebook_pixel_id" class="form-label">Facebook Pixel ID</label>
                                    <input type="text" class="form-control @error('facebook_pixel_id') is-invalid @enderror" 
                                           id="facebook_pixel_id" name="facebook_pixel_id" 
                                           value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}" 
                                           placeholder="123456789012345">
                                    @error('facebook_pixel_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="enable_cookie_consent" name="enable_cookie_consent" value="1" 
                                           {{ old('enable_cookie_consent', $settings['enable_cookie_consent'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_cookie_consent">
                                        Enable Cookie Consent Banner
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Notification Settings -->
                            <div class="mb-4">
                                <h6>Notification Settings</h6>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="notify_new_registrations" name="notify_new_registrations" value="1" 
                                           {{ old('notify_new_registrations', $settings['notify_new_registrations'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_new_registrations">
                                        Notify on New Registrations
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="notify_new_orders" name="notify_new_orders" value="1" 
                                           {{ old('notify_new_orders', $settings['notify_new_orders'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_new_orders">
                                        Notify on New Orders
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="notify_payment_failures" name="notify_payment_failures" value="1" 
                                           {{ old('notify_payment_failures', $settings['notify_payment_failures'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_payment_failures">
                                        Notify on Payment Failures
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Settings
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
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
    // Maintenance mode warning
    $('#maintenance_mode').on('change', function() {
        if ($(this).is(':checked')) {
            alert('Warning: Enabling maintenance mode will make the site inaccessible to all users except super admins.');
        }
    });
    
    // Phone number formatting
    $('#company_phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{1})(\d{3})(\d{3})(\d{4})/, '+$1 ($2) $3-$4');
            $(this).val(value);
        }
    });
    
    // Color preview
    $('#primary_color').on('change', function() {
        const color = $(this).val();
        $('body').append(`<style id="color-preview">.btn-primary { background-color: ${color} !important; border-color: ${color} !important; }</style>`);
    });
    
    // File size validation
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const maxSize = $(this).attr('id') === 'favicon' ? 0.5 : 2; // MB
            if (file.size > maxSize * 1024 * 1024) {
                alert(`File size must be less than ${maxSize}MB`);
                $(this).val('');
            }
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        const requiredFields = ['site_name', 'admin_email', 'timezone', 'currency'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const $field = $(`#${field}`);
            if (!$field.val()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endpush
