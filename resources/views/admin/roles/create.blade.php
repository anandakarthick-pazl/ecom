@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create Role</h1>
    
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-tag me-1"></i>
            Create New Role
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g., store_manager" pattern="[a-z_]+"
                                   title="Only lowercase letters and underscores allowed">
                            <div class="form-text">Use lowercase letters and underscores only (e.g., store_manager)</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" value="{{ old('display_name') }}" required
                                   placeholder="e.g., Store Manager">
                            <div class="form-text">Human-readable name for this role</div>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Describe the role and its responsibilities">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="is_active" value="1" id="is_active" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active Role
                        </label>
                        <div class="form-text">Inactive roles cannot be assigned to users</div>
                    </div>
                </div>
                
                <!-- Permissions Section -->
                <div class="mb-4">
                    <h5>Assign Permissions</h5>
                    <p class="text-muted">Select the permissions that users with this role should have.</p>
                    
                    <div class="accordion" id="permissionsAccordion">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-{{ $module === 'dashboard' ? 'tachometer-alt' : ($module === 'users' ? 'users' : ($module === 'products' ? 'box' : ($module === 'orders' ? 'shopping-cart' : 'cog'))) }} me-2"></i>
                                            <span class="me-3">{{ ucfirst(str_replace('_', ' ', $module)) }} Permissions</span>
                                            <span class="badge bg-secondary">{{ $modulePermissions->count() }}</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" 
                                     data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-2" 
                                                        onclick="selectAllInModule('{{ $module }}')">
                                                    Select All
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        onclick="deselectAllInModule('{{ $module }}')">
                                                    Deselect All
                                                </button>
                                            </div>
                                            @foreach($modulePermissions as $permission)
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-check module-{{ $module }}" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->id }}" 
                                                               id="perm_{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                            {{ $permission->display_name }}
                                                            @if($permission->description)
                                                                <small class="text-muted d-block">{{ $permission->description }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate role name from display name
document.getElementById('display_name').addEventListener('input', function() {
    const displayName = this.value;
    const roleName = displayName.toLowerCase()
                               .replace(/[^a-z0-9\s]/g, '')
                               .replace(/\s+/g, '_')
                               .trim();
    document.getElementById('name').value = roleName;
});

// Select/Deselect all permissions in a module
function selectAllInModule(module) {
    document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllInModule(module) {
    document.querySelectorAll(`.module-${module}`).forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Select/Deselect all permissions
function selectAllPermissions() {
    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-check').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Add global select/deselect buttons
document.addEventListener('DOMContentLoaded', function() {
    const permissionsSection = document.querySelector('#permissionsAccordion');
    if (permissionsSection) {
        const globalControls = document.createElement('div');
        globalControls.className = 'mb-3 text-end';
        globalControls.innerHTML = `
            <button type="button" class="btn btn-sm btn-success me-2" onclick="selectAllPermissions()">
                <i class="fas fa-check-double me-1"></i> Select All Permissions
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deselectAllPermissions()">
                <i class="fas fa-times me-1"></i> Deselect All
            </button>
        `;
        permissionsSection.parentNode.insertBefore(globalControls, permissionsSection);
    }
});
</script>
@endsection
