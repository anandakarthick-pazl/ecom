@extends('super-admin.layouts.app')

@section('title', 'WhatsApp Configuration - ' . $company->name)
@section('page_title', 'WhatsApp Configuration - ' . $company->name)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('super-admin.whatsapp.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to All Companies
    </a>
    
    @if($config && $config->isConfigured())
        <button type="button" class="btn btn-outline-success" 
                data-bs-toggle="modal" data-bs-target="#testWhatsAppModal">
            <i class="fas fa-paper-plane"></i> Test Configuration
        </button>
        
        <button type="button" class="btn btn-outline-info" id="checkAccountInfo">
            <i class="fas fa-info-circle"></i> Account Info
        </button>
        
        <button type="button" class="btn btn-outline-warning" id="getUsageStats">
            <i class="fas fa-chart-bar"></i> Usage Stats
        </button>
        
        <a href="{{ route('super-admin.whatsapp.export', $company->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-download"></i> Export Config
        </a>
    @endif
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">WhatsApp Configuration</h4>
                    <p class="text-muted">Configure Twilio WhatsApp integration for {{ $company->name }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('super-admin.whatsapp.update', $company->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="twilio_account_sid" class="form-label">
                                        Twilio Account SID <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('twilio_account_sid') is-invalid @enderror" 
                                           id="twilio_account_sid" name="twilio_account_sid" 
                                           value="{{ old('twilio_account_sid', $config ? $config->twilio_account_sid : '') }}"
                                           placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                    @error('twilio_account_sid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Found in your Twilio Console</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="twilio_auth_token" class="form-label">
                                        Twilio Auth Token <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control @error('twilio_auth_token') is-invalid @enderror" 
                                           id="twilio_auth_token" name="twilio_auth_token" 
                                           value="{{ old('twilio_auth_token', $config ? $config->twilio_auth_token : '') }}"
                                           placeholder="Enter your Twilio Auth Token">
                                    @error('twilio_auth_token')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        @if($config && $config->twilio_auth_token)
                                            Current: {{ $config->getMaskedAuthToken() }}
                                        @else
                                            Keep this secure - found in your Twilio Console
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="whatsapp_business_number" class="form-label">
                                        WhatsApp Business Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('whatsapp_business_number') is-invalid @enderror" 
                                           id="whatsapp_business_number" name="whatsapp_business_number" 
                                           value="{{ old('whatsapp_business_number', $config ? $config->whatsapp_business_number : '') }}"
                                           placeholder="whatsapp:+1234567890">
                                    @error('whatsapp_business_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Your approved WhatsApp Business number from Twilio</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="test_number" class="form-label">Test Phone Number</label>
                                    <input type="text" class="form-control" 
                                           id="test_number" name="test_number" 
                                           value="{{ old('test_number', $config ? $config->test_number : '') }}"
                                           placeholder="+1234567890">
                                    <small class="text-muted">Phone number for testing (optional)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_enabled" name="is_enabled" value="1"
                                       {{ old('is_enabled', $config ? $config->is_enabled : false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_enabled">
                                    <strong>Enable WhatsApp Integration</strong>
                                </label>
                            </div>
                            <small class="text-muted">When enabled, admins can send bills via WhatsApp</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_message_template" class="form-label">
                                Default Message Template <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('default_message_template') is-invalid @enderror" 
                                      id="default_message_template" name="default_message_template" 
                                      rows="5" placeholder="Enter default message template...">{{ old('default_message_template', $config && $config->default_message_template ? $config->default_message_template : ($config ? $config->getDefaultMessageTemplate() : '')) }}</textarea>
                            @error('default_message_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Available placeholders: @verbatim{{customer_name}}, {{order_number}}, {{total}}, {{company_name}}, {{order_date}}, {{status}}, {{payment_status}}@endverbatim
                            </small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_file_size_mb" class="form-label">
                                        Max File Size (MB) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('max_file_size_mb') is-invalid @enderror" 
                                           id="max_file_size_mb" name="max_file_size_mb" 
                                           value="{{ old('max_file_size_mb', $config ? $config->max_file_size_mb : 5) }}"
                                           min="1" max="20">
                                    @error('max_file_size_mb')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum file size for attachments (1-20 MB)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate_limit_per_minute" class="form-label">
                                        Rate Limit (per minute) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('rate_limit_per_minute') is-invalid @enderror" 
                                           id="rate_limit_per_minute" name="rate_limit_per_minute" 
                                           value="{{ old('rate_limit_per_minute', $config ? $config->rate_limit_per_minute : 10) }}"
                                           min="1" max="100">
                                    @error('rate_limit_per_minute')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Number of messages per minute (1-100)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="allowed_file_types" class="form-label">
                                Allowed File Types <span class="text-danger">*</span>
                            </label>
                            <div class="row">
                                @php
                                    $allowedTypes = old('allowed_file_types', $config ? $config->allowed_file_types : ['pdf']);
                                    $allTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx'];
                                @endphp
                                @foreach($allTypes as $type)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="type_{{ $type }}" name="allowed_file_types[]" 
                                                   value="{{ $type }}"
                                                   {{ in_array($type, $allowedTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_{{ $type }}">
                                                {{ strtoupper($type) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('allowed_file_types')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select file types that can be sent via WhatsApp</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="webhook_url" class="form-label">Webhook URL</label>
                                    <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                                           id="webhook_url" name="webhook_url" 
                                           value="{{ old('webhook_url', $config ? $config->webhook_url : '') }}"
                                           placeholder="https://your-domain.com/webhook">
                                    @error('webhook_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">URL for receiving WhatsApp webhooks (optional)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="webhook_secret" class="form-label">Webhook Secret</label>
                                    <input type="password" class="form-control @error('webhook_secret') is-invalid @enderror" 
                                           id="webhook_secret" name="webhook_secret" 
                                           value="{{ old('webhook_secret', $config ? $config->webhook_secret : '') }}"
                                           placeholder="Enter webhook secret">
                                    @error('webhook_secret')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Secret for webhook verification (optional)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            @if($config && $config->isConfigured())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Configuration Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            @if($config->is_enabled)
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">WhatsApp Active</h5>
                                <p class="text-muted">Ready to send messages</p>
                            @else
                                <i class="fas fa-pause-circle fa-3x text-warning mb-3"></i>
                                <h5 class="text-warning">WhatsApp Disabled</h5>
                                <p class="text-muted">Configuration saved but disabled</p>
                            @endif
                        </div>
                        
                        <hr>
                        
                        <div class="mb-2">
                            <strong>Business Number:</strong><br>
                            <span class="text-monospace">{{ $config->getFormattedPhoneNumber() }}</span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Rate Limit:</strong><br>
                            <span class="badge bg-info">{{ $config->rate_limit_per_minute }}/min</span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Max File Size:</strong><br>
                            <span class="badge bg-secondary">{{ $config->max_file_size_mb }}MB</span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Allowed Types:</strong><br>
                            @foreach($config->allowed_file_types as $type)
                                <span class="badge bg-light text-dark">{{ strtoupper($type) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Setup Instructions</h5>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">
                            <strong>Create Twilio Account:</strong><br>
                            <small>Sign up at <a href="https://twilio.com" target="_blank">twilio.com</a></small>
                        </li>
                        <li class="list-group-item">
                            <strong>Get WhatsApp Business Number:</strong><br>
                            <small>Apply for WhatsApp Business API access through Twilio</small>
                        </li>
                        <li class="list-group-item">
                            <strong>Find Your Credentials:</strong><br>
                            <small>Account SID and Auth Token from Twilio Console</small>
                        </li>
                        <li class="list-group-item">
                            <strong>Configure & Test:</strong><br>
                            <small>Enter credentials, enable, and test the configuration</small>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test WhatsApp Modal -->
<div class="modal fade" id="testWhatsAppModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp text-success"></i> Test WhatsApp Configuration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testWhatsAppForm">
                    <div class="mb-3">
                        <label for="modal_test_number" class="form-label">Test Phone Number</label>
                        <input type="tel" class="form-control" id="modal_test_number" name="test_number" 
                               value="{{ old('test_number', $config ? $config->test_number : '') }}"
                               placeholder="+1234567890" required>
                        <small class="text-muted">Include country code (e.g., +91 for India)</small>
                    </div>
                    <div class="mb-3">
                        <label for="modal_test_message" class="form-label">Test Message (Optional)</label>
                        <textarea class="form-control" id="modal_test_message" name="test_message" rows="3"
                                  placeholder="Leave empty to use default test message..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="sendTestMessage">
                    <i class="fab fa-whatsapp"></i> Send Test Message
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Account Info Modal -->
<div class="modal fade" id="accountInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle text-info"></i> Twilio Account Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="accountInfoContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading account information...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('WhatsApp config page loaded - jQuery version:', $.fn.jquery);
    
    // Initialize CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Send test message functionality
    $('#sendTestMessage').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Send test message button clicked');
        
        const testNumber = $('#modal_test_number').val().trim();
        const testMessage = $('#modal_test_message').val().trim();
        
        console.log('Test number:', testNumber);
        console.log('Test message:', testMessage);
        
        // Validation
        if (!testNumber) {
            showAlert('Please enter a test phone number', 'error');
            return;
        }
        
        // Phone number format validation
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        if (!phoneRegex.test(testNumber.replace(/[\s\-\(\)]/g, ''))) {
            showAlert('Please enter a valid phone number with country code (e.g., +91xxxxxxxxxx)', 'error');
            return;
        }
        
        const btn = $(this);
        const originalHtml = btn.html();
        
        // Disable button and show loading state
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        console.log('Making AJAX request to test endpoint');
        
        // Make AJAX request
        $.ajax({
            url: '{{ route("super-admin.whatsapp.test", $company->id) }}',
            method: 'POST',
            dataType: 'json',
            data: {
                test_number: testNumber,
                test_message: testMessage || null,
                _token: '{{ csrf_token() }}'
            },
            timeout: 30000, // 30 second timeout
            success: function(response) {
                console.log('AJAX success response:', response);
                
                if (response && response.success) {
                    showAlert('✅ Test message sent successfully!', 'success');
                    $('#testWhatsAppModal').modal('hide');
                    $('#testWhatsAppForm')[0].reset();
                } else {
                    const errorMsg = response && response.message ? response.message : 'Failed to send test message';
                    showAlert('❌ ' + errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error - Status:', status, 'Error:', error);
                console.log('XHR response:', xhr.responseText);
                
                let errorMessage = 'Failed to send test message';
                
                if (xhr.status === 422) {
                    // Validation errors
                    try {
                        const errors = JSON.parse(xhr.responseText);
                        if (errors.errors) {
                            errorMessage = Object.values(errors.errors).flat().join(', ');
                        } else if (errors.message) {
                            errorMessage = errors.message;
                        }
                    } catch (e) {
                        errorMessage = 'Validation error';
                    }
                } else if (xhr.status === 400) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage = errorData.message || 'Configuration error';
                    } catch (e) {
                        errorMessage = 'Configuration not found or invalid';
                    }
                } else if (xhr.status === 500) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage = errorData.message || 'Server error';
                    } catch (e) {
                        errorMessage = 'Internal server error';
                    }
                } else if (status === 'timeout') {
                    errorMessage = 'Request timed out. Please try again.';
                } else if (status === 'error') {
                    errorMessage = 'Network error. Please check your connection.';
                }
                
                showAlert('❌ ' + errorMessage, 'error');
            },
            complete: function() {
                console.log('AJAX request completed');
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Check account info functionality
    $('#checkAccountInfo').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Check account info clicked');
        
        $('#accountInfoModal').modal('show');
        $('#accountInfoContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading account information...</p></div>');
        
        $.ajax({
            url: '{{ route("super-admin.whatsapp.account-info", $company->id) }}',
            method: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Account info response:', response);
                
                if (response && response.success) {
                    const content = `
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Account SID:</strong><br>
                                <span class="text-monospace small">${response.account_sid}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Account Name:</strong><br>
                                <span>${response.friendly_name}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong><br>
                                <span class="badge bg-${response.status === 'active' ? 'success' : 'warning'}">${response.status}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Account Type:</strong><br>
                                <span>${response.type}</span>
                            </div>
                            <div class="col-12">
                                <strong>Created:</strong><br>
                                <span>${response.date_created}</span>
                            </div>
                        </div>
                    `;
                    $('#accountInfoContent').html(content);
                } else {
                    const errorMsg = response && response.error ? response.error : 'Failed to load account information';
                    $('#accountInfoContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> 
                            ${errorMsg}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.log('Account info error:', xhr, status, error);
                $('#accountInfoContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Failed to load account information. Please check your configuration.
                    </div>
                `);
            }
        });
    });
    
    // Get usage stats functionality
    $('#getUsageStats').off('click').on('click', function(e) {
        e.preventDefault();
        console.log('Get usage stats clicked');
        
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        $.ajax({
            url: '{{ route("super-admin.whatsapp.usage", $company->id) }}',
            method: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Usage stats response:', response);
                
                if (response && response.success && response.usage && response.usage.length > 0) {
                    let usageHtml = '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Category</th><th>Usage</th><th>Price</th><th>Period</th></tr></thead><tbody>';
                    response.usage.forEach(function(item) {
                        usageHtml += `<tr>
                            <td>${item.description || item.category}</td>
                            <td>${item.usage} ${item.usage_unit}</td>
                            <td>${item.price} ${item.price_unit}</td>
                            <td>${item.start_date} - ${item.end_date}</td>
                        </tr>`;
                    });
                    usageHtml += '</tbody></table></div>';
                    
                    showModal('Usage Statistics', usageHtml);
                } else {
                    showAlert('No usage data available for the selected period', 'info');
                }
            },
            error: function(xhr, status, error) {
                console.log('Usage stats error:', xhr, status, error);
                showAlert('Failed to load usage statistics', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Clear form when modal is hidden
    $('#testWhatsAppModal').on('hidden.bs.modal', function() {
        $('#testWhatsAppForm')[0].reset();
    });
    
    // Auto-fill test number from config when modal is shown
    $('#testWhatsAppModal').on('show.bs.modal', function() {
        const configTestNumber = '{{ $config ? $config->test_number : "" }}';
        if (configTestNumber && !$('#modal_test_number').val()) {
            $('#modal_test_number').val(configTestNumber);
        }
    });
});

// Helper functions
function showAlert(message, type = 'info') {
    console.log('Showing alert:', message, type);
    
    // Remove existing alerts
    $('.custom-alert').remove();
    
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show custom-alert" role="alert" 
             style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
            <i class="${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(alert);
    
    // Auto-remove after 8 seconds
    setTimeout(function() {
        $('.custom-alert').alert('close');
    }, 8000);
}

function showModal(title, content) {
    const modalId = 'dynamicModal' + Date.now();
    const modal = `
        <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $(`#${modalId}`).modal('show');
    
    // Remove modal from DOM after it's hidden
    $(`#${modalId}`).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

// Add some basic error handling for the page
window.addEventListener('error', function(e) {
    console.error('JavaScript error on page:', e.error);
});

console.log('WhatsApp configuration JavaScript loaded successfully');
</script>
@endsection
