@extends('super-admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>System Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">General Settings</h6>
                                <p class="card-text">Manage application name, logo, and basic settings.</p>
                                <a href="{{ route('super-admin.settings.general') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cog me-2"></i>General Settings
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Email Settings</h6>
                                <p class="card-text">Configure SMTP and email notification settings.</p>
                                <a href="{{ route('super-admin.settings.email') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-envelope me-2"></i>Email Settings
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Cache Management</h6>
                                <p class="card-text">Clear application cache and optimize performance.</p>
                                <a href="{{ route('super-admin.settings.cache') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-sync me-2"></i>Cache Settings
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Backup & Restore</h6>
                                <p class="card-text">Create backups and restore system data.</p>
                                <a href="{{ route('super-admin.settings.backup') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-2"></i>Backup Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Settings Modal -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.settings.email') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mail_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                   value="{{ env('MAIL_HOST') }}" placeholder="smtp.gmail.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                   value="{{ env('MAIL_PORT') }}" placeholder="587">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_username" class="form-label">SMTP Username</label>
                            <input type="email" class="form-control" id="mail_username" name="mail_username" 
                                   value="{{ env('MAIL_USERNAME') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mail_encryption" class="form-label">Encryption</label>
                            <select class="form-select" id="mail_encryption" name="mail_encryption">
                                <option value="tls" {{ env('MAIL_ENCRYPTION') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ env('MAIL_ENCRYPTION') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-info" id="testEmailBtn">
                            <i class="fas fa-paper-plane me-2"></i>Send Test Email
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#testEmailBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("super-admin.settings.test-email") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert('Test email sent successfully!');
            },
            error: function() {
                alert('Failed to send test email. Please check your settings.');
            },
            complete: function() {
                $btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
