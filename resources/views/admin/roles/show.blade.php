@extends('admin.layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Role Details</h1>
    
    <div class="row">
        <!-- Role Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-tag me-1"></i>
                        {{ $role->display_name }}
                        @if($role->is_system_role)
                            <span class="badge bg-secondary ms-2">System Role</span>
                        @else
                            <span class="badge bg-info ms-2">Custom Role</span>
                        @endif
                        <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }} ms-2">
                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="btn-group">
                        @if(!$role->is_system_role)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-key me-1"></i> Manage Permissions
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="duplicateRole()">
                            <i class="fas fa-copy me-1"></i> Duplicate
                        </button>
                        @if($role->canBeDeleted())
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole()">
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
                                    <td><strong>Role Name:</strong></td>
                                    <td><code>{{ $role->name }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Display Name:</strong></td>
                                    <td>{{ $role->display_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $role->description ?? 'No description provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>
                                        @if($role->is_system_role)
                                            <span class="badge bg-secondary">System Role</span>
                                        @else
                                            <span class="badge bg-info">Custom Role</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Users Count:</strong></td>
                                    <td>{{ $stats['users_count'] }} users</td>
                                </tr>
                                <tr>
                                    <td><strong>Permissions:</strong></td>
                                    <td>{{ $stats['permissions_count'] }} permissions</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $role->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permissions by Module -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Assigned Permissions
                </div>
                <div class="card-body">
                    @if($permissionGroups->count() > 0)
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($permissionGroups as $module => $permissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $module === 'dashboard' ? 'tachometer-alt' : ($module === 'users' ? 'users' : ($module === 'products' ? 'box' : ($module === 'orders' ? 'shopping-cart' : 'cog'))) }} me-2"></i>
                                                <span class="me-3">{{ ucfirst(str_replace('_', ' ', $module)) }}</span>
                                                <span class="badge bg-primary">{{ $permissions->count() }} permissions</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                         data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    <div class="col-md-6 col-lg-4 mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-check-circle text-success me-2"></i>
                                                            <div>
                                                                <strong>{{ $permission->display_name }}</strong>
                                                                @if($permission->description)
                                                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No permissions assigned to this role</p>
                            <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-primary">
                                <i class="fas fa-key me-1"></i> Assign Permissions
                            </a>
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
                
                <div class="col-12 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Active Users</div>
                                    <div class="h4">{{ $stats['active_users_count'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Permissions</div>
                                    <div class="h4">{{ $stats['permissions_count'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-key fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="small">Modules Covered</div>
                                    <div class="h4">{{ $stats['modules_covered'] }}</div>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-puzzle-piece fa-2x"></i>
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
                        <a href="{{ route('admin.roles.permissions', $role) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-key me-1"></i> Manage Permissions
                        </a>
                        
                        @if(!$role->is_system_role)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Role
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-info btn-sm" onclick="viewUsers()">
                            <i class="fas fa-users me-1"></i> View Users
                        </button>
                        
                        <button type="button" class="btn btn-secondary btn-sm" onclick="duplicateRole()">
                            <i class="fas fa-copy me-1"></i> Duplicate Role
                        </button>
                        
                        <button type="button" class="btn btn-outline-{{ $role->is_active ? 'warning' : 'success' }} btn-sm" 
                                onclick="toggleStatus()">
                            <i class="fas fa-{{ $role->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $role->is_active ? 'Deactivate' : 'Activate' }} Role
                        </button>
                        
                        @if($role->canBeDeleted())
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole()">
                                <i class="fas fa-trash me-1"></i> Delete Role
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Assigned Users -->
            @if($role->users->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users me-1"></i>
                        Assigned Users ({{ $role->users->count() }})
                    </div>
                    <div class="card-body">
                        @foreach($role->users->take(5) as $user)
                            <div class="d-flex align-items-center mb-2">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                @else
                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                        @endforeach
                        
                        @if($role->users->count() > 5)
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewUsers()">
                                    View All {{ $role->users->count() }} Users
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Roles
        </a>
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
                <p>Are you sure you want to delete the role <strong>{{ $role->display_name }}</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Users Modal -->
<div class="modal fade" id="usersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Users with {{ $role->display_name }} Role</h5>
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

<script>
function deleteRole() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function duplicateRole() {
    if (confirm('Are you sure you want to duplicate this role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.roles.duplicate', $role) }}";
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleStatus() {
    const currentStatus = {{ $role->is_active ? 'true' : 'false' }};
    const newStatus = !currentStatus;
    const action = newStatus ? 'activate' : 'deactivate';
    
    if (confirm(`Are you sure you want to ${action} this role?`)) {
        fetch("{{ route('admin.roles.toggle-status', $role) }}", {
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
}

function viewUsers() {
    const modal = new bootstrap.Modal(document.getElementById('usersModal'));
    const content = document.getElementById('usersContent');
    
    // Show loading
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading users...</div>';
    modal.show();
    
    // Fetch users
    fetch("{{ route('admin.roles.get-users', $role) }}")
        .then(response => response.json())
        .then(data => {
            if (data.users && data.users.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Name</th><th>Email</th><th>Department</th><th>Status</th></tr></thead><tbody>';
                data.users.forEach(user => {
                    html += `
                        <tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.department || 'Not Set'}</td>
                            <td><span class="badge bg-${user.status === 'active' ? 'success' : 'danger'}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span></td>
                        </tr>
                    `;
                });
                html += '</tbody></table></div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<p class="text-center text-muted">No users assigned to this role.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<p class="text-center text-danger">Error loading users.</p>';
        });
}
</script>
@endsection
