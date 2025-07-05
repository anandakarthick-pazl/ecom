@extends('super-admin.layouts.app')

@section('title', 'Companies Management')
@section('page-title', 'Companies Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>All Companies
                </h5>
                <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Company
                </a>
            </div>
            <div class="card-body">
                @if($companies->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Company Name</th>
                                    <th>Domain</th>
                                    <th>Email</th>
                                    <th>Theme</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Trial Ends</th>
                                    <th>Created</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>
                                        @if($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" 
                                                 class="rounded" width="40" height="40" 
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ strtoupper(substr($company->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        @if($company->phone)
                                            <br><small class="text-muted">{{ $company->phone }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="http://{{ $company->domain }}" target="_blank" class="text-decoration-none">
                                            {{ $company->domain }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </td>
                                    <td>{{ $company->email }}</td>
                                    <td>
                                        @if($company->theme)
                                            <span class="badge bg-info">{{ $company->theme->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Theme</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->package)
                                            <span class="badge bg-success">{{ $company->package->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Package</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-company-id="{{ $company->id }}" 
                                                style="width: auto; min-width: 100px;">
                                            <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                            <option value="suspended" {{ $company->status === 'suspended' ? 'selected' : '' }}>
                                                Suspended
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        @if($company->trial_ends_at)
                                            <span class="badge {{ $company->trial_ends_at->isPast() ? 'bg-danger' : 'bg-warning' }}">
                                                {{ $company->trial_ends_at->format('M d, Y') }}
                                            </span>
                                            @if(!$company->trial_ends_at->isPast())
                                                <br><small class="text-muted">
                                                    {{ $company->trial_ends_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No Trial</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $company->created_at->format('M d, Y') }}
                                            <br>{{ $company->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td class="actions-column">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.companies.show', $company) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.companies.edit', $company) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($company->trial_ends_at && !$company->trial_ends_at->isPast())
                                                <button type="button" class="btn btn-sm btn-outline-warning extend-trial-btn" 
                                                        data-company-id="{{ $company->id }}" title="Extend Trial">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-company-id="{{ $company->id }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $companies->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Companies Found</h5>
                        <p class="text-muted">Start by adding your first company to the system.</p>
                        <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add First Company
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Extend Trial Modal -->
<div class="modal fade" id="extendTrialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Trial Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extendTrialForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="extend_days" class="form-label">Extend trial by (days):</label>
                        <input type="number" class="form-control" id="extend_days" name="days" 
                               min="1" max="365" value="30" required>
                        <small class="form-text text-muted">Enter number of days to extend the trial period.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Extend Trial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this company? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will also delete all associated users, data, and configurations.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Company</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Status change handler
    $('.status-select').on('change', function() {
        const companyId = $(this).data('company-id');
        const status = $(this).val();
        const selectElement = $(this);
        
        $.ajax({
            url: `/super-admin/companies/${companyId}/status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>Company status updated successfully!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    $('.content-wrapper').prepend(alert);
                    
                    // Auto-hide after 3 seconds
                    setTimeout(() => alert.fadeOut(), 3000);
                }
            },
            error: function() {
                alert('Error updating company status. Please try again.');
                // Reset to previous value
                selectElement.val(selectElement.data('original-value'));
            }
        });
    });
    
    // Store original values for rollback
    $('.status-select').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    // Extend trial handler
    let currentCompanyId = null;
    $('.extend-trial-btn').on('click', function() {
        currentCompanyId = $(this).data('company-id');
        $('#extendTrialModal').modal('show');
    });
    
    $('#extendTrialForm').on('submit', function(e) {
        e.preventDefault();
        const days = $('#extend_days').val();
        
        $.ajax({
            url: `/super-admin/companies/${currentCompanyId}/extend-trial`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                days: days
            },
            success: function(response) {
                if (response.success) {
                    $('#extendTrialModal').modal('hide');
                    location.reload(); // Refresh to show updated trial date
                }
            },
            error: function() {
                alert('Error extending trial period. Please try again.');
            }
        });
    });
    
    // Delete handler
    $('.delete-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        $('#deleteForm').attr('action', `/super-admin/companies/${companyId}`);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
