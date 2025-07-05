@extends('admin.layouts.app')

@section('title', 'Manage Role Permissions')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Role Permissions</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-key me-1"></i>
                Permissions for: {{ $role->display_name }}
                @if($role->is_system_role)
                    <span class="badge bg-secondary ms-2">System Role</span>
                @endif
            </div>
            <div>
                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i> View Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Roles
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Current Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 id="totalPermissions">{{ count($rolePermissions) }}</h4>
                            <small>Total Permissions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 id="modulesCovered">{{ $role->permissions->groupBy('module')->count() }}</h4>
                            <small>Modules Covered</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ $role->users->count() }}</h4>
                            <small>Assigned Users</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body text-center">
                            <h4>{{ $permissions->sum(fn($group) => $group->count()) }}</h4>
                            <small>Available Permissions</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Global Actions -->
            <div class="mb-4">
                <div class="btn-toolbar justify-content-between" role="toolbar">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deselectAllPermissions()">
                            <i class="fas fa-times me-1"></i> Deselect All
                        </button>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" onclick="savePermissions()">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewChanges()">
                            <i class="fas fa-eye me-1"></i> Preview Changes
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Permissions by Module -->
            <form id="permissionsForm">
                @csrf
                <div class="accordion" id="permissionsAccordion">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    <div class="d-flex align-items-center w-100">
                                        <i class="fas fa-{{ $module === 'dashboard' ? 'tachometer-alt' : ($module === 'users' ? 'users' : ($module === 'products' ? 'box' : ($module === 'orders' ? 'shopping-cart' : 'cog'))) }} me-2"></i>
                                        <span class="me-3">{{ ucfirst(str_replace('_', ' ', $module)) }} Permissions</span>
                                        <span class="badge bg-secondary">{{ $modulePermissions->count() }} total</span>
                                        @php
                                            $moduleAssigned = $modulePermissions->whereIn('id', $rolePermissions)->count();
                                        @endphp
                                        @if($moduleAssigned > 0)
                                            <span class="badge bg-success ms-2 module-assigned-{{ $module }}">{{ $moduleAssigned }} assigned</span>
                                        @else
                                            <span class="badge bg-light text-dark ms-2 module-assigned-{{ $module }}">0 assigned</span>
                                        @endif
                                        <div class="ms-auto me-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="selectAllInModule('{{ $module }}')">
                                                All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="deselectAllInModule('{{ $module }}')">
                                                None
                                            </button>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 data-bs-parent="#permissionsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach($modulePermissions as $permission)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card permission-card h-100">
                                                    <div class="card-body p-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-check module-{{ $module }}" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission->id }}" 
                                                                   id="perm_{{ $permission->id }}"
                                                                   data-module="{{ $module }}"
                                                                   {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                                   onchange="updatePermissionCount()">
                                                            <label class="form-check-label w-100" for="perm_{{ $permission->id }}">
                                                                <strong>{{ $permission->display_name }}</strong>
                                                                @if($permission->description)
                                                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                                                @endif
                                                                <br><code class="small">{{ $permission->name }}</code>
                                                            </label>
                                                        </div>
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
            </form>
            
            <!-- Action Buttons -->
            <div class="mt-4 text-center">
                <button type="button" class="btn btn-primary btn-lg" onclick="savePermissions()">
                    <i class="fas fa-save me-1"></i> Save Permissions
                </button>
                <button type="button" class="btn btn-secondary btn-lg" onclick="resetPermissions()">
                    <i class="fas fa-undo me-1"></i> Reset Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Changes Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Permission Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="savePermissions(); bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.permission-card {
    transition: all 0.2s ease;
    border: 1px solid #dee2e6;
}

.permission-card:has(.form-check-input:checked) {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.permission-card:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<script>
// Store original permissions for reset functionality
const originalPermissions = @json($rolePermissions);

// Update permission count and module badges
function updatePermissionCount() {
    const checkedPermissions = document.querySelectorAll('.permission-check:checked');
    document.getElementById('totalPermissions').textContent = checkedPermissions.length;
    
    // Update module counts
    @foreach($permissions as $module => $modulePermissions)
        const {{ $module }}Count = document.querySelectorAll('.module-{{ $module }}:checked').length;
        const {{ $module }}Badge = document.querySelector('.module-assigned-{{ $module }}');
        if ({{ $module }}Badge) {
            {{ $module }}Badge.textContent = {{ $module }}Count + ' assigned';
            {{ $module }}Badge.className = {{ $module }}Count > 0 ? 'badge bg-success ms-2 module-assigned-{{ $module }}' : 'badge bg-light text-dark ms-2 module-assigned-{{ $module }}';
        }
    @endforeach
    
    // Update modules covered count
    const modulesCovered = new Set();
    checkedPermissions.forEach(checkbox => {
        modulesCovered.add(checkbox.dataset.module);
    });
    document.getElementById('modulesCovered').textContent = modulesCovered.size;
}

// Select/Deselect all permissions in a module
function selectAllInModule(module) {
    document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionCount();
}

function deselectAllInModule(module) {
    document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionCount();
}

// Select/Deselect all permissions
function selectAllPermissions() {
    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.checked = true;
    });
    updatePermissionCount();
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionCount();
}

// Reset to original permissions
function resetPermissions() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.querySelectorAll('.permission-check').forEach(checkbox => {
            checkbox.checked = originalPermissions.includes(parseInt(checkbox.value));
        });
        updatePermissionCount();
    }
}

// Preview changes
function previewChanges() {
    const currentPermissions = Array.from(document.querySelectorAll('.permission-check:checked')).map(cb => parseInt(cb.value));
    
    const added = currentPermissions.filter(id => !originalPermissions.includes(id));
    const removed = originalPermissions.filter(id => !currentPermissions.includes(id));
    
    let html = '<div class="row">';
    
    if (added.length > 0) {
        html += '<div class="col-md-6"><h6 class="text-success">Added Permissions (' + added.length + ')</h6><ul class="list-group">';
        added.forEach(id => {
            const checkbox = document.querySelector(`input[value="${id}"]`);
            const label = document.querySelector(`label[for="${checkbox.id}"]`);
            html += '<li class="list-group-item list-group-item-success">' + label.querySelector('strong').textContent + '</li>';
        });
        html += '</ul></div>';
    }
    
    if (removed.length > 0) {
        html += '<div class="col-md-6"><h6 class="text-danger">Removed Permissions (' + removed.length + ')</h6><ul class="list-group">';
        removed.forEach(id => {
            // Find permission name from original data
            const permissionName = @json($permissions->flatten()->keyBy('id'))[id]?.display_name || 'Unknown Permission';
            html += '<li class="list-group-item list-group-item-danger">' + permissionName + '</li>';
        });
        html += '</ul></div>';
    }
    
    if (added.length === 0 && removed.length === 0) {
        html += '<div class="col-12"><div class="alert alert-info">No changes made to permissions.</div></div>';
    }
    
    html += '</div>';
    
    document.getElementById('previewContent').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

// Save permissions
function savePermissions() {
    const selectedPermissions = Array.from(document.querySelectorAll('.permission-check:checked')).map(cb => cb.value);
    
    // Show loading state
    const saveBtn = document.querySelector('button[onclick="savePermissions()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
    saveBtn.disabled = true;
    
    fetch("{{ route('admin.roles.update-permissions', $role) }}", {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            permissions: selectedPermissions
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);
            
            // Update original permissions
            originalPermissions.length = 0;
            originalPermissions.push(...selectedPermissions.map(id => parseInt(id)));
            
            // Scroll to top
            window.scrollTo(0, 0);
        } else {
            alert('Error saving permissions: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving permissions');
    })
    .finally(() => {
        // Restore button
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updatePermissionCount();
});
</script>
@endsection
