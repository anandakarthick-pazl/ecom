@extends('admin.layouts.app')

@section('title', 'Permission Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Permission Details</h1>
    
    <div class="row">
        <!-- Permission Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-key me-1"></i>
                        {{ $permission->display_name }}
                        @if($permission->is_system_permission)
                            <span class="badge bg-warning ms-2">System Permission</span>
                        @else
                            <span class="badge bg-info ms-2">Custom Permission</span>
                        @endif
                    </div>
                    <div class="btn-group">
                        @if(!$permission->is_system_permission)
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endif
                        @if($permission->canBeDeleted())
                            <button type="button" class="btn btn-danger btn-sm" onclick="deletePermission()">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Permission Name:</strong></td>
                                    <td><code>{{ $permission->name }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Display Name:</strong></td>
                                    <td>{{ $permission->display_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Module:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $permission->module }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Action:</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permission->action }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($permission->is_system_permission)
                                            <span class="badge bg-warning">System Permission</span>
                                        @else
                                            <span class="badge bg-info">Custom Permission</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Roles Count:</strong></td>
                                    <td>{{ $stats['roles_count'] }} roles</td>
                                </tr>
                                <tr>
                                    <td><strong>Users Count:</strong></td>
                                    <td>{{ $stats['users_count'] }} users</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $permission->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($permission->description)
                        <div class="mt-3">
                            <strong>Description:</strong>
                            <p class="mt-2 text-muted">{{ $permission->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Assigned Roles -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-tag me-1"></i>
                    Assigned Roles ({{ $permission->roles->count() }})
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        <div class="row">
                            @foreach($permission->roles as $role)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title">{{ $role->display_name }}</h6>
                                                    <p class="card-text">
                                                        <small class="text-muted">{{ $role->description ?: 'No description' }}</small>
                                                    </p>
                                                    <div>
                                                        @if($role->is_system_role)
                                                            <span class="badge bg-secondary">System</span>
                                                        @else
                                                            <span class="badge bg-info">Custom</span>
                                                        @endif
                                                        <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                                                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="h5 text-primary">{{ $role->users->count() }}</div>
                                                    <small class="text-muted">users</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View Role
                                            </a>
                                            @if(!$permission->is_system_permission && !$role->is_system_role)
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="removeFromRole({{ $role->id }})">
                                                    <i class="fas fa-times me-1"></i> Remove
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                            <p class="text-muted">This permission is not assigned to any roles</p>
                            <button type="button" class="btn btn-primary" onclick="assignToRole()">
                                <i class="fas fa-plus me-1"></i> Assign to Role
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Statistics & Quick Actions -->
        <div class="col-md-4">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Assigned Roles</div>
                                    <div class="h4">{{ $stats['roles_count'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-tag fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Total Users</div>
                                    <div class="h4">{{ $stats['users_count'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Active Roles</div>
                                    <div class="h4">{{ $stats['active_roles_count'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-sm" onclick="assignToRole()">
                            <i class="fas fa-plus me-1"></i> Assign to Role
                        </button>
                        
                        @if(!$permission->is_system_permission)
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Permission
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-info btn-sm" onclick="viewUsers()">
                            <i class="fas fa-users me-1"></i> View All Users
                        </button>
                        
                        <a href="{{ route('admin.permissions.module', $permission->module) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sitemap me-1"></i> View Module Permissions
                        </a>
                        
                        @if($permission->canBeDeleted())
                            <button type="button" class="btn btn-danger btn-sm" onclick="deletePermission()">
                                <i class="fas fa-trash me-1"></i> Delete Permission
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Module Information -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-sitemap me-1"></i>
                    Module: {{ ucfirst(str_replace('_', ' ', $permission->module)) }}
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Other permissions in this module:</p>
                    @php
                        $modulePermissions = \App\Models\Permission::where('module', $permission->module)
                                                                 ->where('id', '!=', $permission->id)
                                                                 ->limit(5)
                                                                 ->get();
                    @endphp
                    @if($modulePermissions->count() > 0)
                        @foreach($modulePermissions as $relatedPermission)
                            <div class="mb-2">
                                <a href="{{ route('admin.permissions.show', $relatedPermission) }}" 
                                   class="text-decoration-none">
                                    <small class="badge bg-secondary">{{ $relatedPermission->action }}</small>
                                    <small class="text-muted ms-1">{{ $relatedPermission->display_name }}</small>
                                </a>
                            </div>
                        @endforeach
                        
                        @php
                            $totalModulePermissions = \App\Models\Permission::where('module', $permission->module)->count();
                        @endphp
                        @if($totalModulePermissions > 6)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.permissions.module', $permission->module) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    View All {{ $totalModulePermissions }} Permissions
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">No other permissions in this module</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Permissions
        </a>
    </div>
</div>

<!-- Assign to Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Permission to Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignRoleForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roleSelect" class="form-label">Select Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="roleSelect" name="role_id" required>
                            <option value="">Choose a role...</option>
                            @foreach(\App\Models\Role::active()->currentTenant()->get() as $role)
                                @if(!$permission->roles->contains($role))
                                    <option value="{{ $role->id }}">
                                        {{ $role->display_name }}
                                        ({{ $role->users->count() }} users)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will grant the "{{ $permission->display_name }}" permission to all users with the selected role.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Assign Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Modal -->
<div class="modal fade" id="usersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Users with {{ $permission->display_name }} Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="usersContent">
                    Loading users...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the permission <strong>{{ $permission->display_name }}</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will remove this permission from all roles.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function assignToRole() {
    const modal = new bootstrap.Modal(document.getElementById('assignRoleModal'));
    modal.show();
}

function deletePermission() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function removeFromRole(roleId) {
    if (confirm('Are you sure you want to remove this permission from the role?')) {
        fetch(`/admin/permissions/bulk-remove`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                role_id: roleId,
                permission_ids: [{{ $permission->id }}]
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error removing permission from role');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing permission from role');
        });
    }
}

function viewUsers() {
    const modal = new bootstrap.Modal(document.getElementById('usersModal'));
    const content = document.getElementById('usersContent');
    
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading users...</div>';
    modal.show();
    
    // Get all users from roles that have this permission
    const roleIds = @json($permission->roles->pluck('id'));
    
    if (roleIds.length === 0) {
        content.innerHTML = '<p class="text-center text-muted">No users have this permission.</p>';
        return;
    }
    
    // For demo purposes, show a simple list
    // In a real app, you'd make an API call to get users
    let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>User</th><th>Role</th><th>Status</th></tr></thead><tbody>';
    
    @foreach($permission->roles as $role)
        @foreach($role->users as $user)
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <i class="fas fa-user-circle fa-lg text-muted"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $role->display_name }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                </tr>
            `;
        @endforeach
    @endforeach
    
    html += '</tbody></table></div>';
    content.innerHTML = html;
}

// Assign role form handler
document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const roleId = document.getElementById('roleSelect').value;
    if (!roleId) {
        alert('Please select a role');
        return;
    }
    
    fetch('/admin/permissions/bulk-assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            role_id: roleId,
            permission_ids: [{{ $permission->id }}]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error assigning permission to role: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error assigning permission to role');
    });
});
</script>
@endsection
