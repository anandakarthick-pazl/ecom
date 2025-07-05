@extends('admin.layouts.app')

@section('title', 'Branch Management')
@section('page_title', 'Branch Management')

@section('page_actions')
    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Branch
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <p class="mb-0">Total Branches</p>
                    </div>
                    <div>
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        <p class="mb-0">Active Branches</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card bg-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['inactive'] }}</h3>
                        <p class="mb-0">Inactive Branches</p>
                    </div>
                    <div>
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.branches.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by name, code, city, or manager...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="code" {{ request('sort') === 'code' ? 'selected' : '' }}>Code</option>
                        <option value="city" {{ request('sort') === 'city' ? 'selected' : '' }}>City</option>
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="direction" class="form-label">Order</label>
                    <select class="form-select" id="direction" name="direction">
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Branches Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-building"></i> Branches
                <span class="badge bg-primary ms-2">{{ $branches->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($branches->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Branch Name</th>
                                <th>Manager</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Users</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $branch)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $branch->code }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $branch->name }}</strong>
                                            @if($branch->description)
                                                <br><small class="text-muted">{{ Str::limit($branch->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($branch->manager_name)
                                            <div>
                                                <strong>{{ $branch->manager_name }}</strong>
                                                @if($branch->manager_phone)
                                                    <br><small class="text-muted">{{ $branch->manager_phone }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($branch->city || $branch->state)
                                            <div>
                                                @if($branch->city)
                                                    <i class="fas fa-map-marker-alt text-muted"></i> {{ $branch->city }}
                                                @endif
                                                @if($branch->state)
                                                    <br><small class="text-muted">{{ $branch->state }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($branch->phone || $branch->email)
                                            <div>
                                                @if($branch->phone)
                                                    <i class="fas fa-phone text-muted"></i> {{ $branch->phone }}<br>
                                                @endif
                                                @if($branch->email)
                                                    <i class="fas fa-envelope text-muted"></i> {{ $branch->email }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $branch->users->count() }} Users</span>
                                    </td>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" 
                                                   data-branch-id="{{ $branch->id }}"
                                                   class="status-toggle"
                                                   {{ $branch->status === 'active' ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Branch actions">
                                            <a href="{{ route('admin.branches.show', $branch) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.branches.edit', $branch) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-branch" 
                                                    data-branch-id="{{ $branch->id }}"
                                                    data-branch-name="{{ $branch->name }}"
                                                    title="Delete">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <p class="text-muted mb-0">
                            Showing {{ $branches->firstItem() }} to {{ $branches->lastItem() }} of {{ $branches->total() }} results
                        </p>
                    </div>
                    <div>
                        {{ $branches->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Branches Found</h5>
                    <p class="text-muted">You haven't created any branches yet. Start by adding your first branch.</p>
                    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First Branch
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-labelledby="deleteBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBranchModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the branch <strong id="branchNameToDelete"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteBranchForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Branch</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Toggle Switch Styles */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #28a745;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 24px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Status toggle functionality
    $('.status-toggle').change(function() {
        const branchId = $(this).data('branch-id');
        const isChecked = $(this).is(':checked');
        const toggle = $(this);
        
        $.ajax({
            url: `/admin/branches/${branchId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);
                } else {
                    // Revert toggle state
                    toggle.prop('checked', !isChecked);
                    showAlert('error', 'Failed to update branch status');
                }
            },
            error: function() {
                // Revert toggle state
                toggle.prop('checked', !isChecked);
                showAlert('error', 'Failed to update branch status');
            }
        });
    });
    
    // Delete branch functionality
    $('.delete-branch').click(function() {
        const branchId = $(this).data('branch-id');
        const branchName = $(this).data('branch-name');
        
        $('#branchNameToDelete').text(branchName);
        $('#deleteBranchForm').attr('action', `/admin/branches/${branchId}`);
        $('#deleteBranchModal').modal('show');
    });
    
    // Function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of content
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush
