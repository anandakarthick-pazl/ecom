@extends('admin.layouts.app')

@section('title', 'Create Permission')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create Permission</h1>
    
    <div class="card">
        <div class="card-header">
            <i class="fas fa-key me-1"></i>
            Create New Permission
        </div>
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
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
                                   id="display_name" name="display_name" value="{{ old('display_name') }}" required
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
                                   id="module" name="module" value="{{ old('module') }}" required
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
                                   id="action" name="action" value="{{ old('action') }}" required
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
                              placeholder="Describe what this permission allows users to do">{{ old('description') }}</textarea>
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
                                <strong>Permission Name:</strong> <code id="previewName">-</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Display Name:</strong> <span id="previewDisplayName">-</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Module:</strong> <span class="badge bg-primary" id="previewModule">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Action:</strong> <span class="badge bg-secondary" id="previewAction">-</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>Description:</strong> <span id="previewDescription">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Generate Section -->
                <div class="card bg-info text-white mb-3">
                    <div class="card-header">
                        <i class="fas fa-magic me-1"></i>
                        Quick Generate
                    </div>
                    <div class="card-body">
                        <p>Generate permission name automatically based on module and action:</p>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="quickModule" placeholder="Module name">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="quickAction" placeholder="Action name">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-light" onclick="generatePermission()">
                                    <i class="fas fa-magic me-1"></i> Generate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Permission
                    </button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate permission name and display name
function updatePreview() {
    const module = document.getElementById('module').value || document.getElementById('quickModule').value;
    const action = document.getElementById('action').value || document.getElementById('quickAction').value;
    const displayName = document.getElementById('display_name').value;
    const description = document.getElementById('description').value;
    
    const permissionName = module && action ? `${module}.${action}` : '-';
    const autoDisplayName = module && action ? `${action.charAt(0).toUpperCase() + action.slice(1)} ${module.charAt(0).toUpperCase() + module.slice(1)}` : '-';
    
    document.getElementById('previewName').textContent = permissionName;
    document.getElementById('previewDisplayName').textContent = displayName || autoDisplayName;
    document.getElementById('previewModule').textContent = module || '-';
    document.getElementById('previewAction').textContent = action || '-';
    document.getElementById('previewDescription').textContent = description || 'No description provided';
}

// Generate permission from quick inputs
function generatePermission() {
    const module = document.getElementById('quickModule').value.trim().toLowerCase();
    const action = document.getElementById('quickAction').value.trim().toLowerCase();
    
    if (!module || !action) {
        alert('Please provide both module and action names');
        return;
    }
    
    // Set form fields
    document.getElementById('name').value = `${module}.${action}`;
    document.getElementById('module').value = module;
    document.getElementById('action').value = action;
    document.getElementById('display_name').value = `${action.charAt(0).toUpperCase() + action.slice(1)} ${module.charAt(0).toUpperCase() + module.slice(1)}`;
    
    // Generate description
    const descriptions = {
        'view': `Permission to view ${module}`,
        'create': `Permission to create new ${module}`,
        'update': `Permission to update existing ${module}`,
        'delete': `Permission to delete ${module}`,
        'manage': `Permission to manage ${module}`,
        'export': `Permission to export ${module} data`,
        'import': `Permission to import ${module} data`,
        'approve': `Permission to approve ${module}`,
        'publish': `Permission to publish ${module}`,
        'archive': `Permission to archive ${module}`
    };
    
    document.getElementById('description').value = descriptions[action] || `Permission to ${action} ${module}`;
    
    // Clear quick inputs
    document.getElementById('quickModule').value = '';
    document.getElementById('quickAction').value = '';
    
    updatePreview();
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
        if (!document.getElementById('display_name').value) {
            document.getElementById('display_name').value = `${action.charAt(0).toUpperCase() + action.slice(1)} ${this.value.charAt(0).toUpperCase() + this.value.slice(1)}`;
        }
    }
    updatePreview();
});

document.getElementById('action').addEventListener('input', function() {
    const module = document.getElementById('module').value;
    if (this.value && module) {
        document.getElementById('name').value = `${module}.${this.value}`;
        if (!document.getElementById('display_name').value) {
            document.getElementById('display_name').value = `${this.value.charAt(0).toUpperCase() + this.value.slice(1)} ${module.charAt(0).toUpperCase() + module.slice(1)}`;
        }
    }
    updatePreview();
});

// Initialize preview
updatePreview();
</script>
@endsection
