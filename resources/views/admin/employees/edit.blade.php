@extends('admin.layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Employee</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Edit Employee: {{ $employee->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <div class="form-text">Leave empty to keep current password</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}">
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Role and Branch -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role_id') is-invalid @enderror" 
                                        id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" 
                                        id="branch_id" name="branch_id">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Job Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                       id="department" name="department" value="{{ old('department', $employee->department) }}">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="designation" class="form-label">Designation</label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                       id="designation" name="designation" value="{{ old('designation', $employee->designation) }}">
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="salary" class="form-label">Salary</label>
                                <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" 
                                       id="salary" name="salary" value="{{ old('salary', $employee->salary) }}">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Avatar Upload -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                   id="avatar" name="avatar" accept="image/*" onchange="previewImage(this)">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Max file size: 2MB. Formats: JPG, PNG, GIF</div>
                        </div>
                        
                        <div class="text-center">
                            @if($employee->avatar)
                                <img id="avatar-preview" src="{{ asset('storage/' . $employee->avatar) }}" 
                                     alt="Current Avatar" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <div class="mt-2">
                                    <small class="text-muted">Current profile picture</small>
                                </div>
                            @else
                                <img id="avatar-preview" src="#" alt="Preview" class="img-thumbnail d-none" style="max-width: 200px; max-height: 200px;">
                                <div class="text-muted">
                                    <i class="fas fa-user-circle fa-5x"></i>
                                    <div class="mt-2">No profile picture</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Individual Permissions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Individual Permissions</h5>
                        <p class="text-muted">Grant additional permissions to this employee beyond their role permissions.</p>
                        
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false">
                                            <i class="fas fa-{{ $module === 'dashboard' ? 'tachometer-alt' : ($module === 'users' ? 'users' : ($module === 'products' ? 'box' : ($module === 'orders' ? 'shopping-cart' : 'cog'))) }} me-2"></i>
                                            {{ ucfirst(str_replace('_', ' ', $module)) }} Permissions
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse" 
                                         data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="individual_permissions[]" 
                                                                   value="{{ $permission->name }}" 
                                                                   id="perm_{{ $permission->id }}"
                                                                   {{ in_array($permission->name, old('individual_permissions', $employee->permissions ?? [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                {{ $permission->display_name }}
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
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Employee
                    </button>
                    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> View Employee
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Role change handler to load role permissions
document.getElementById('role_id').addEventListener('change', function() {
    const roleId = this.value;
    if (roleId) {
        // Here you could add AJAX to load role permissions and show them
        console.log('Selected role:', roleId);
    }
});
</script>
@endsection
