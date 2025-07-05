@extends('admin.layouts.app')

@section('title', 'Permissions Management')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Permissions Management</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-key me-1"></i>
                Manage System Permissions
            </div>
            <div>
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create Permission
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="fas fa-magic me-1"></i> Generate Permissions
                </button>
                <a href="{{ route('admin.permissions.export') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-download me-1"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search permissions..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="module">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $module)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="action">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="is_system_permission">
                        <option value="">All Types</option>
                        <option value="1" {{ request('is_system_permission') === '1' ? 'selected' : '' }}>System</option>
                        <option value="0" {{ request('is_system_permission') === '0' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>

            <!-- Permissions Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Permission Name</th>
                            <th>Display Name</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Type</th>
                            <th>Roles</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input permission-checkbox" 
                                           value="{{ $permission->id }}">
                                </td>
                                <td>
                                    <code>{{ $permission->name }}</code>
                                    @if($permission->is_system_permission)
                                        <i class="fas fa-lock text-muted ms-1" title="System Permission"></i>
                                    @endif
                                </td>
                                <td>{{ $permission->display_name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $permission->module }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $permission->action }}</span>
                                </td>
                                <td>
                                    @if($permission->is_system_permission)
                                        <span class="badge bg-warning">System</span>
                                    @else
                                        <span class="badge bg-info">Custom</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $permission->roles_count }} roles</span>
                                </td>
                                <td>{{ Str::limit($permission->description, 50) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.permissions.show', $permission) }}" 
                                           class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$permission->is_system_permission)
                                            <a href="{{ route('admin.permissions.edit', $permission) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        <button type="button" class="btn btn-outline-warning" 
                                                onclick="showRoles({{ $permission->id }})" title="View Roles">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        
                                        @if($permission->canBeDeleted())
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deletePermission({{ $permission->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-key fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No permissions found</p>
                                    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Create First Permission
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            @if($permissions->count() > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select id="bulkAction" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <option value="">Bulk Actions</option>
                            <option value="assign">Assign to Role</option>
                            <option value="remove">Remove from Role</option>
                        </select>
                        <select id="targetRole" class="form-select form-select-sm ms-2" style="width: auto; display: inline-block;">
                            <option value="">Select Role</option>
                            @foreach(\App\Models\Role::active()->currentTenant()->get() as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm btn-primary ms-2" onclick="executeBulkAction()">
                            Apply
                        </button>
                    </div>
                    
                    <!-- Pagination -->
                    @if($permissions->hasPages())
                        {{ $permissions->links() }}
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <!-- Permissions by Module -->
    @if($groupedPermissions->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-sitemap me-1"></i>
                Permissions by Module
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($groupedPermissions as $module => $modulePermissions)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-{{ $module === 'dashboard' ? 'tachometer-alt' : ($module === 'users' ? 'users' : ($module === 'products' ? 'box' : ($module === 'orders' ? 'shopping-cart' : 'cog'))) }} me-2"></i>
                                    {{ ucfirst(str_replace('_', ' ', $module)) }}
                                    <span class="badge bg-light text-dark float-end">{{ $modulePermissions->count() }}</span>
                                </div>
                                <div class="card-body p-2">
                                    @foreach($modulePermissions->take(5) as $permission)
                                        <div class="mb-1">
                                            <small class="text-muted">{{ $permission->display_name }}</small>
                                        </div>
                                    @endforeach
                                    @if($modulePermissions->count() > 5)
                                        <div class="text-center mt-2">
                                            <small class="text-muted">
                                                +{{ $modulePermissions->count() - 5 }} more permissions
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer p-2">
                                    <a href="{{ route('admin.permissions.module', $module) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Generate Permissions Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="module" class="form-label">Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="module" name="module" required
                               placeholder="e.g., inventory, reports, etc.">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Actions <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="view" id="action_view">
                                    <label class="form-check-label" for="action_view">View</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="create" id="action_create">
                                    <label class="form-check-label" for="action_create">Create</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="update" id="action_update">
                                    <label class="form-check-label" for="action_update">Update</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="delete" id="action_delete">
                                    <label class="form-check-label" for="action_delete">Delete</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="export" id="action_export">
                                    <label class="form-check-label" for="action_export">Export</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="import" id="action_import">
                                    <label class="form-check-label" for="action_import">Import</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="manage" id="action_manage">
                                    <label class="form-check-label" for="action_manage">Manage</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="approve" id="action_approve">
                                    <label class="form-check-label" for="action_approve">Approve</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCommonActions()">
                            Select Common (View, Create, Update, Delete)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllActions()">
                            Select All
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-magic me-1"></i> Generate Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Roles Modal -->
<div class="modal fade" id="rolesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Roles with Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="rolesContent">
                    Loading...
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
                <p>Are you sure you want to delete this permission?</p>
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
// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Generate permissions form
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        module: formData.get('module'),
        actions: formData.getAll('actions[]')
    };
    
    if (!data.module || data.actions.length === 0) {
        alert('Please provide module name and select at least one action');
        return;
    }
    
    fetch("{{ route('admin.permissions.generate') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + ' Created ' + data.created_count + ' permissions.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating permissions');
    });
});

function selectCommonActions() {
    ['view', 'create', 'update', 'delete'].forEach(action => {
        const checkbox = document.getElementById('action_' + action);
        if (checkbox) checkbox.checked = true;
    });
}

function selectAllActions() {
    document.querySelectorAll('input[name="actions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function showRoles(permissionId) {
    const modal = new bootstrap.Modal(document.getElementById('rolesModal'));
    const content = document.getElementById('rolesContent');
    
    content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading roles...</div>';
    modal.show();
    
    fetch(`/admin/permissions/${permissionId}/roles`)
        .then(response => response.json())
        .then(data => {
            if (data.roles && data.roles.length > 0) {
                let html = '<div class="list-group">';
                data.roles.forEach(role => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${role.display_name}</strong>
                                <br><small class="text-muted">${role.description || 'No description'}</small>
                            </div>
                            <div>
                                <span class="badge bg-primary">${role.users_count} users</span>
                                ${role.is_active ? '<span class="badge bg-success ms-1">Active</span>' : '<span class="badge bg-danger ms-1">Inactive</span>'}
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<p class="text-center text-muted">No roles have this permission.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = '<p class="text-center text-danger">Error loading roles.</p>';
        });
}

function deletePermission(permissionId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/permissions/${permissionId}`;
    modal.show();
}

function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const roleId = document.getElementById('targetRole').value;
    const selectedPermissions = Array.from(document.querySelectorAll('.permission-checkbox:checked')).map(cb => cb.value);
    
    if (!action || !roleId || selectedPermissions.length === 0) {
        alert('Please select an action, target role, and at least one permission');
        return;
    }
    
    const endpoint = action === 'assign' ? '/admin/permissions/bulk-assign' : '/admin/permissions/bulk-remove';
    
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            role_id: roleId,
            permission_ids: selectedPermissions
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error executing bulk action');
    });
}
</script>
@endsection
