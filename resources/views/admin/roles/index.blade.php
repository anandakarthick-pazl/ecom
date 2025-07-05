@extends('admin.layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Roles Management</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-tag me-1"></i>
                Manage Roles & Permissions
            </div>
            <div>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create Role
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search roles..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="is_active">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="is_system_role">
                        <option value="">All Types</option>
                        <option value="1" {{ request('is_system_role') === '1' ? 'selected' : '' }}>System</option>
                        <option value="0" {{ request('is_system_role') === '0' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Roles Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->is_system_role)
                                        <i class="fas fa-lock text-muted ms-1" title="System Role"></i>
                                    @endif
                                </td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ Str::limit($role->description, 50) }}</td>
                                <td>
                                    @if($role->is_system_role)
                                        <span class="badge bg-secondary">System</span>
                                    @else
                                        <span class="badge bg-info">Custom</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-{{ $role->is_active ? 'success' : 'danger' }}"
                                            onclick="toggleStatus({{ $role->id }}, {{ $role->is_active ? 'false' : 'true' }})">
                                        <i class="fas fa-{{ $role->is_active ? 'check' : 'times' }}"></i>
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $role->users_count }} users</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $role->permissions->count() }} permissions</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.roles.show', $role) }}" 
                                           class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.roles.permissions', $role) }}" 
                                           class="btn btn-outline-warning" title="Manage Permissions">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        
                                        @if(!$role->is_system_role)
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="duplicateRole({{ $role->id }})" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        
                                        @if($role->canBeDeleted())
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteRole({{ $role->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No roles found</p>
                                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Create First Role
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($roles->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this role?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(roleId, newStatus) {
    fetch(`/admin/roles/${roleId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            is_active: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating role status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating role status');
    });
}

function duplicateRole(roleId) {
    if (confirm('Are you sure you want to duplicate this role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/roles/${roleId}/duplicate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteRole(roleId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/roles/${roleId}`;
    modal.show();
}
</script>
@endsection
