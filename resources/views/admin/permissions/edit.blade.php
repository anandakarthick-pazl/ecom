@extends('admin.layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Permission</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-key me-1"></i>
                Edit Permission: {{ $permission->display_name }}
                @if($permission->is_system_permission)
                    <span class="badge bg-warning ms-2">System Permission</span>
                @endif
            </div>
            <div>
                <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i> View Permission
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($permission->is_system_permission)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>System Permission:</strong> This is a system permission and cannot be modified. System permissions are essential for the application to function properly.
                </div>
                
                <!-- Display-only form for system permissions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Permission Name</label>
                            <input type="text" class="form-control" value="{{ $permission->name }}" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Display Name</label>
                            <input type="text" class="form-control" value="{{ $permission->display_name }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Module</label>
                            <input type="text" class="form-control" value="{{ $permission->module }}" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <input type="text" class="form-control" value="{{ $permission->action }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" readonly>{{ $permission->description }}</textarea>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> View Permission Details
                    </a>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Permissions
                    </a>
                </div>
                
            @else
                <!-- Editable form for custom permissions -->
                <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $permission->name) }}" required
                                       placeholder="e.g., products.view, users.create"
                                       pattern="[a-z_.]+" title="Use lowercase letters, underscores, and dots only">
                                <div class="form-text">Format: module.action (e.g., products.view, orders.create)</div>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" name="display_name" value="{{ old('display_name', $permission->display_name) }}" required
                                       placeholder="e.g., View Products, Create Users">
                                <div class="form-text">Human-readable name for this permission</div>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module" class="form-label">Module <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('module') is-invalid @enderror" 
                                       id="module" name="module" value="{{ old('module', $permission->module) }}" required
                                       placeholder="e.g., products, users, orders"
                                       list="modulesList">
                                <datalist id="modulesList">
                                    @foreach($modules as $module)
                                        <option value="{{ $module }}">
                                    @endforeach
                                </datalist>
                                <div class="form-text">The module or section this permission belongs to</div>
                                @error('module')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="action" class="form-label">Action <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('action') is-invalid @enderror" 
                                       id="action" name="action" value="{{ old('action', $permission->action) }}" required
                                       placeholder="e.g., view, create, update, delete"
                                       list="actionsList">
                                <datalist id="actionsList">
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}">
                                    @endforeach
                                    <option value="view">
                                    <option value="create">
                                    <option value="update">
                                    <option value="delete">
                                    <option value="manage">
                                    <option value="export">
                                    <option value="import">
                                    <option value="approve">
                                    <option value="publish">
                                    <option value="archive">
                                </datalist>
                                <div class="form-text">The specific action this permission allows</div>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Describe what this permission allows users to do">{{ old('description', $permission->description) }}</textarea>
                        <div class="form-text">Optional: Provide a detailed description of this permission</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Permission Preview -->
                    <div class="card bg-light mb-3">
                        <div class="card-header">
                            <i class="fas fa-eye me-1"></i>
                            Permission Preview
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Permission Name:</strong> <code id="previewName">{{ $permission->name }}</code>
                                </div>
                                <div class="col-md-6">
                                    <strong>Display Name:</strong> <span id="previewDisplayName">{{ $permission->display_name }}</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Module:</strong> <span class="badge bg-primary" id="previewModule">{{ $permission->module }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Action:</strong> <span class="badge bg-secondary" id="previewAction">{{ $permission->action }}</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <strong>Description:</strong> <span id="previewDescription">{{ $permission->description ?: 'No description provided' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Usage Info -->
                    <div class="card bg-info text-white mb-3">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-1"></i>
                            Current Usage
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Assigned to Roles:</strong> {{ $permission->roles->count() }} roles
                                </div>
                                <div class="col-md-6">
                                    <strong>Total Users Affected:</strong> {{ $permission->roles->sum(fn($role) => $role->users->count()) }} users
                                </div>
                            </div>
                            @if($permission->roles->count() > 0)
                                <div class="mt-2">
                                    <strong>Roles:</strong>
                                    @foreach($permission->roles as $role)
                                        <span class="badge bg-light text-dark me-1">{{ $role->display_name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Permission
                        </button>
                        <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i> View Permission
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Permissions
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@if(!$permission->is_system_permission)
<script>
// Auto-generate permission name and display name
function updatePreview() {
    const module = document.getElementById('module').value;
    const action = document.getElementById('action').value;
    const displayName = document.getElementById('display_name').value;
    const description = document.getElementById('description').value;
    
    const permissionName = module && action ? `${module}.${action}` : '-';
    
    document.getElementById('previewName').textContent = permissionName;
    document.getElementById('previewDisplayName').textContent = displayName || '-';
    document.getElementById('previewModule').textContent = module || '-';
    document.getElementById('previewAction').textContent = action || '-';
    document.getElementById('previewDescription').textContent = description || 'No description provided';
}

// Add event listeners
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('display_name').addEventListener('input', updatePreview);
document.getElementById('module').addEventListener('input', updatePreview);
document.getElementById('action').addEventListener('input', updatePreview);
document.getElementById('description').addEventListener('input', updatePreview);

// Auto-generate name when module/action changes
document.getElementById('module').addEventListener('input', function() {
    const action = document.getElementById('action').value;
    if (this.value && action) {
        document.getElementById('name').value = `${this.value}.${action}`;
    }
    updatePreview();
});

document.getElementById('action').addEventListener('input', function() {
    const module = document.getElementById('module').value;
    if (this.value && module) {
        document.getElementById('name').value = `${module}.${this.value}`;
    }
    updatePreview();
});
</script>
@endif
@endsection
