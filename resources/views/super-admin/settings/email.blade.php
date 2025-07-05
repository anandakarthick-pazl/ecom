@extends('super-admin.layouts.app')

@section('title', 'Email Settings')
@section('page-title', 'Email Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope me-2"></i>Email Configuration
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('super-admin.settings.email.update') }}" method="POST" id="emailSettingsForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Basic Email Settings -->
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>Basic Email Settings
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_from_name" class="form-label">
                                                Mail From Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror" 
                                                   id="mail_from_name" name="mail_from_name" 
                                                   value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('mail.from.name', 'Multi-Tenant E-commerce')) }}" 
                                                   required placeholder="e.g., Herbal E-commerce">
                                            @error('mail_from_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_from_email" class="form-label">
                                                Mail From Email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control @error('mail_from_email') is-invalid @enderror" 
                                                   id="mail_from_email" name="mail_from_email" 
                                                   value="{{ old('mail_from_email', $settings['mail_from_email'] ?? config('mail.from.address', 'noreply@example.com')) }}" 
                                                   required placeholder="noreply@yourdomain.com">
                                            @error('mail_from_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Enable Email Queue</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="enable_queue" name="enable_queue" value="1"
                                                       {{ old('enable_queue', $settings['enable_queue'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_queue">
                                                    <span class="badge bg-success" id="queueStatusYes" style="display: {{ old('enable_queue', $settings['enable_queue'] ?? false) ? 'inline' : 'none' }}">Yes</span>
                                                    <span class="badge bg-secondary" id="queueStatusNo" style="display: {{ old('enable_queue', $settings['enable_queue'] ?? false) ? 'none' : 'inline' }}">No</span>
                                                </label>
                                            </div>
                                            <small class="text-muted">Queue emails for better performance</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_driver" class="form-label">
                                                Mail Driver <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('mail_driver') is-invalid @enderror" 
                                                    id="mail_driver" name="mail_driver" required>
                                                <option value="smtp" {{ old('mail_driver', $settings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                <option value="mail" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'mail' ? 'selected' : '' }}>Mail</option>
                                                <option value="sendmail" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                                <option value="log" {{ old('mail_driver', $settings['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                                            </select>
                                            @error('mail_driver')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SMTP Configuration -->
                            <div class="mb-4" id="smtpSettings">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-server me-2"></i>SMTP Configuration
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="mail_host" class="form-label">
                                                Mail Host <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('mail_host') is-invalid @enderror" 
                                                   id="mail_host" name="mail_host" 
                                                   value="{{ old('mail_host', $settings['mail_host'] ?? config('mail.mailers.smtp.host')) }}" 
                                                   required placeholder="smtp.gmail.com">
                                            @error('mail_host')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="mail_port" class="form-label">
                                                Mail Port <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control @error('mail_port') is-invalid @enderror" 
                                                   id="mail_port" name="mail_port" 
                                                   value="{{ old('mail_port', $settings['mail_port'] ?? config('mail.mailers.smtp.port', 587)) }}" 
                                                   required placeholder="587" min="1" max="65535">
                                            @error('mail_port')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_encryption" class="form-label">Mail Encryption</label>
                                            <select class="form-select @error('mail_encryption') is-invalid @enderror" 
                                                    id="mail_encryption" name="mail_encryption">
                                                <option value="tls" {{ old('mail_encryption', $settings['mail_encryption'] ?? config('mail.mailers.smtp.encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                                <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                <option value="starttls" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                                <option value="" {{ old('mail_encryption', $settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                            </select>
                                            @error('mail_encryption')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email Verified</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified" value="1"
                                                       {{ old('email_verified', $settings['email_verified'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_verified">
                                                    <span class="badge bg-success" id="verifiedStatusYes" style="display: {{ old('email_verified', $settings['email_verified'] ?? false) ? 'inline' : 'none' }}">Yes</span>
                                                    <span class="badge bg-warning" id="verifiedStatusNo" style="display: {{ old('email_verified', $settings['email_verified'] ?? false) ? 'none' : 'inline' }}">No</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_username" class="form-label">
                                                Mail Username <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('mail_username') is-invalid @enderror" 
                                                   id="mail_username" name="mail_username" 
                                                   value="{{ old('mail_username', $settings['mail_username'] ?? config('mail.mailers.smtp.username')) }}" 
                                                   required placeholder="your-email@gmail.com" autocomplete="username">
                                            @error('mail_username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mail_password" class="form-label">Mail Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('mail_password') is-invalid @enderror" 
                                                       id="mail_password" name="mail_password" 
                                                       value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" 
                                                       placeholder="••••••••••••" autocomplete="current-password">
                                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Leave blank to keep current password</small>
                                            @error('mail_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Email Section -->
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Test Email
                                    </h6>
                                    <p class="text-muted small">Send a test email to verify your configuration</p>
                                    
                                    <div class="mb-3">
                                        <label for="test_email" class="form-label">Test Email Address</label>
                                        <input type="email" class="form-control" id="test_email" name="test_email" 
                                               value="{{ auth()->user()->email }}" placeholder="test@example.com">
                                    </div>
                                    
                                    <button type="button" class="btn btn-info btn-sm w-100" id="sendTestEmail">
                                        <i class="fas fa-paper-plane me-2"></i>Send Test Email
                                    </button>
                                    
                                    <div id="testEmailResult" class="mt-3" style="display: none;"></div>
                                </div>
                            </div>

                            <!-- SMTP Presets -->
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-magic me-2"></i>Quick Setup
                                    </h6>
                                    <p class="text-muted small">Use preset configurations for popular providers</p>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm preset-btn" data-provider="gmail">
                                            <i class="fab fa-google me-2"></i>Gmail
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm preset-btn" data-provider="outlook">
                                            <i class="fab fa-microsoft me-2"></i>Outlook
                                        </button>
                                        <button type="button" class="btn btn-outline-dark btn-sm preset-btn" data-provider="yahoo">
                                            <i class="fab fa-yahoo me-2"></i>Yahoo
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm preset-btn" data-provider="mailgun">
                                            <i class="fas fa-envelope me-2"></i>Mailgun
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Connection Status -->
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="fas fa-wifi me-2"></i>Connection Status
                                    </h6>
                                    <div id="connectionStatus">
                                        <span class="badge bg-secondary">Not tested</span>
                                    </div>
                                    <button type="button" class="btn btn-outline-info btn-sm mt-2 w-100" id="testConnection">
                                        <i class="fas fa-plug me-2"></i>Test Connection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('super-admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Settings
                        </a>
                        <div>
                            <button type="button" class="btn btn-warning me-2" id="resetForm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Email Settings
                            </button>
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
    // SMTP presets
    const presets = {
        gmail: {
            host: 'smtp.gmail.com',
            port: 587,
            encryption: 'tls'
        },
        outlook: {
            host: 'smtp-mail.outlook.com',
            port: 587,
            encryption: 'starttls'
        },
        yahoo: {
            host: 'smtp.mail.yahoo.com',
            port: 587,
            encryption: 'tls'
        },
        mailgun: {
            host: 'smtp.mailgun.org',
            port: 587,
            encryption: 'tls'
        }
    };

    // Apply preset configurations
    $('.preset-btn').on('click', function() {
        const provider = $(this).data('provider');
        const config = presets[provider];
        
        $('#mail_host').val(config.host);
        $('#mail_port').val(config.port);
        $('#mail_encryption').val(config.encryption);
        
        $(this).closest('.card').find('.btn').removeClass('btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        // Show success message
        showToast('success', `${provider.charAt(0).toUpperCase() + provider.slice(1)} configuration applied`);
    });

    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        const passwordField = $('#mail_password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Toggle switches
    $('#enable_queue').on('change', function() {
        if ($(this).is(':checked')) {
            $('#queueStatusYes').show();
            $('#queueStatusNo').hide();
        } else {
            $('#queueStatusYes').hide();
            $('#queueStatusNo').show();
        }
    });

    $('#email_verified').on('change', function() {
        if ($(this).is(':checked')) {
            $('#verifiedStatusYes').show();
            $('#verifiedStatusNo').hide();
        } else {
            $('#verifiedStatusYes').hide();
            $('#verifiedStatusNo').show();
        }
    });

    // Show/hide SMTP settings based on driver
    $('#mail_driver').on('change', function() {
        if ($(this).val() === 'smtp') {
            $('#smtpSettings').show();
        } else {
            $('#smtpSettings').hide();
        }
    });

    // Test connection
    $('#testConnection').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Testing...').prop('disabled', true);
        $('#connectionStatus').html('<span class="badge bg-warning">Testing...</span>');
        
        // Simulate connection test
        setTimeout(function() {
            const isSuccess = Math.random() > 0.3; // 70% success rate for demo
            
            if (isSuccess) {
                $('#connectionStatus').html('<span class="badge bg-success"><i class="fas fa-check me-1"></i>Connected</span>');
                showToast('success', 'SMTP connection successful');
            } else {
                $('#connectionStatus').html('<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Failed</span>');
                showToast('error', 'SMTP connection failed. Please check your settings.');
            }
            
            $btn.html(originalText).prop('disabled', false);
        }, 2000);
    });

    // Send test email
    $('#sendTestEmail').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const testEmail = $('#test_email').val();
        
        if (!testEmail) {
            showToast('error', 'Please enter a test email address');
            return;
        }
        
        if (!isValidEmail(testEmail)) {
            showToast('error', 'Please enter a valid email address');
            return;
        }
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...').prop('disabled', true);
        $('#testEmailResult').hide();
        
        $.ajax({
            url: '{{ route("super-admin.settings.test-email") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                test_email: testEmail,
                // Include current form data for testing
                mail_host: $('#mail_host').val(),
                mail_port: $('#mail_port').val(),
                mail_username: $('#mail_username').val(),
                mail_encryption: $('#mail_encryption').val(),
                mail_from_name: $('#mail_from_name').val(),
                mail_from_email: $('#mail_from_email').val()
            },
            success: function(response) {
                $('#testEmailResult').html(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>Test email sent successfully to ${testEmail}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `).show();
                showToast('success', 'Test email sent successfully');
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to send test email';
                $('#testEmailResult').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>${errorMsg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `).show();
                showToast('error', errorMsg);
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Reset form
    $('#resetForm').on('click', function() {
        if (confirm('Are you sure you want to reset all email settings to default values?')) {
            $('#emailSettingsForm')[0].reset();
            $('.preset-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#connectionStatus').html('<span class="badge bg-secondary">Not tested</span>');
            $('#testEmailResult').hide();
            showToast('info', 'Form reset to default values');
        }
    });

    // Form validation
    $('#emailSettingsForm').on('submit', function(e) {
        const requiredFields = ['mail_from_name', 'mail_from_email', 'mail_driver'];
        let isValid = true;
        
        // Check if SMTP is selected and validate SMTP fields
        if ($('#mail_driver').val() === 'smtp') {
            requiredFields.push('mail_host', 'mail_port', 'mail_username');
        }
        
        requiredFields.forEach(field => {
            const $field = $(`#${field}`);
            if (!$field.val()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        // Validate email format
        if (!isValidEmail($('#mail_from_email').val())) {
            $('#mail_from_email').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            showToast('error', 'Please fill in all required fields correctly');
        }
    });

    // Auto-populate username field when from email changes
    $('#mail_from_email').on('blur', function() {
        const fromEmail = $(this).val();
        if (fromEmail && !$('#mail_username').val()) {
            $('#mail_username').val(fromEmail);
        }
    });

    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showToast(type, message) {
        const bgClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const toast = $(`
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        const toastElement = new bootstrap.Toast(toast[0]);
        toastElement.show();
        
        // Remove from DOM after hiding
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Initialize form state
    $('#mail_driver').trigger('change');
});
</script>
@endpush
