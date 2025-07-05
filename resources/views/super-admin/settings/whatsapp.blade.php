@extends('super-admin.layouts.app')

@section('title', 'WhatsApp Configuration')
@section('page_title', 'WhatsApp Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Company WhatsApp Settings</h4>
                    <p class="text-muted">Configure WhatsApp integration for each company using Twilio API</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>WhatsApp Number</th>
                                    <th>Rate Limit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                    @php
                                        $config = $configs->get($company->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="badge bg-primary">{{ $company->name }}</span>
                                                </div>
                                                <div>
                                                    <small class="text-muted">{{ $company->domain }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($config && $config->isConfigured())
                                                @if($config->is_enabled)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-pause-circle"></i> Disabled
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times-circle"></i> Not Configured
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($config && $config->whatsapp_business_number)
                                                <span class="text-monospace">{{ $config->getFormattedPhoneNumber() }}</span>
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($config)
                                                <span class="badge bg-info">{{ $config->rate_limit_per_minute ?? 10 }}/min</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('super-admin.whatsapp.show', $company->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-cog"></i> Configure
                                                </a>
                                                
                                                @if($config && $config->isConfigured())
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success test-whatsapp-btn"
                                                            data-company-id="{{ $company->id }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#testWhatsAppModal">
                                                        <i class="fas fa-paper-plane"></i> Test
                                                    </button>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-{{ $config->is_enabled ? 'warning' : 'success' }} toggle-status-btn"
                                                            data-company-id="{{ $company->id }}"
                                                            data-current-status="{{ $config->is_enabled ? 'enabled' : 'disabled' }}">
                                                        <i class="fas fa-{{ $config->is_enabled ? 'pause' : 'play' }}"></i>
                                                        {{ $config->is_enabled ? 'Disable' : 'Enable' }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                        <label for="test_number" class="form-label">Test Phone Number</label>
                        <input type="tel" class="form-control" id="test_number" name="test_number" 
                               placeholder="+1234567890" required>
                        <small class="text-muted">Include country code (e.g., +91 for India)</small>
                    </div>
                    <div class="mb-3">
                        <label for="test_message" class="form-label">Test Message (Optional)</label>
                        <textarea class="form-control" id="test_message" name="test_message" rows="3"
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

@endsection

@section('scripts')
<script>
let currentCompanyId = null;

// Test WhatsApp button click
$('.test-whatsapp-btn').on('click', function() {
    currentCompanyId = $(this).data('company-id');
});

// Send test message
$('#sendTestMessage').on('click', function() {
    if (!currentCompanyId) {
        showToast('Company ID not found', 'error');
        return;
    }
    
    const testNumber = $('#test_number').val();
    const testMessage = $('#test_message').val();
    
    if (!testNumber) {
        showToast('Please enter a test phone number', 'error');
        return;
    }
    
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
    
    $.ajax({
        url: `{{ route('super-admin.whatsapp.test', ':id') }}`.replace(':id', currentCompanyId),
        method: 'POST',
        data: {
            test_number: testNumber,
            test_message: testMessage,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showToast('Test message sent successfully!', 'success');
                $('#testWhatsAppModal').modal('hide');
                $('#testWhatsAppForm')[0].reset();
            } else {
                showToast(response.message || 'Failed to send test message', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to send test message';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast(errorMessage, 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Send Test Message');
        }
    });
});

// Toggle status
$('.toggle-status-btn').on('click', function() {
    const companyId = $(this).data('company-id');
    const currentStatus = $(this).data('current-status');
    const btn = $(this);
    
    const action = currentStatus === 'enabled' ? 'disable' : 'enable';
    
    if (!confirm(`Are you sure you want to ${action} WhatsApp for this company?`)) {
        return;
    }
    
    btn.prop('disabled', true);
    
    $.ajax({
        url: `{{ route('super-admin.whatsapp.toggle', ':id') }}`.replace(':id', companyId),
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                location.reload();
            } else {
                showToast(response.message || 'Failed to update status', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to update status';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showToast(errorMessage, 'error');
        },
        complete: function() {
            btn.prop('disabled', false);
        }
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    const toastClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const toast = `
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    const toastElement = $('.toast').last();
    
    setTimeout(function() {
        toastElement.remove();
    }, 5000);
}
</script>
@endsection
