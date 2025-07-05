@extends('admin.layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Employee Details</h1>
    
    <div class="row">
        <!-- Employee Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user me-1"></i>
                        {{ $employee->name }}
                        <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'danger' }} ms-2">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteEmployee()">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Employee ID:</strong></td>
                                    <td>{{ $employee->employee_id ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Full Name:</strong></td>
                                    <td>{{ $employee->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $employee->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $employee->phone ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Role:</strong></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $employee->assignedRole?->display_name ?? $employee->role }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $employee->branch?->name ?? 'Not Assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Department:</strong></td>
                                    <td>{{ $employee->department ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Designation:</strong></td>
                                    <td>{{ $employee->designation ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Hire Date:</strong></td>
                                    <td>{{ $employee->hire_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Salary:</strong></td>
                                    <td>{{ $employee->salary ? '₹' . number_format($employee->salary, 2) : 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>{{ $employee->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Joined:</strong></td>
                                    <td>{{ $employee->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permissions -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Permissions & Access
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Role Permissions -->
                        <div class="col-md-6">
                            <h6 class="text-primary">Role Permissions</h6>
                            @if($employee->assignedRole && $employee->assignedRole->permissions->count() > 0)
                                <div class="permissions-grid">
                                    @foreach($employee->assignedRole->permissions->groupBy('module') as $module => $permissions)
                                        <div class="mb-3">
                                            <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $module)) }}</strong>
                                            <div class="mt-1">
                                                @foreach($permissions as $permission)
                                                    <span class="badge bg-light text-dark me-1 mb-1">
                                                        {{ $permission->display_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No role permissions assigned</p>
                            @endif
                        </div>
                        
                        <!-- Individual Permissions -->
                        <div class="col-md-6">
                            <h6 class="text-success">Individual Permissions</h6>
                            @if($employee->permissions && count($employee->permissions) > 0)
                                <div class="permissions-grid">
                                    @php
                                        $individualPermissions = \App\Models\Permission::whereIn('name', $employee->permissions)->get()->groupBy('module');
                                    @endphp
                                    @foreach($individualPermissions as $module => $permissions)
                                        <div class="mb-3">
                                            <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $module)) }}</strong>
                                            <div class="mt-1">
                                                @foreach($permissions as $permission)
                                                    <span class="badge bg-success me-1 mb-1">
                                                        {{ $permission->display_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No individual permissions assigned</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editPermissions()">
                            <i class="fas fa-edit me-1"></i> Edit Permissions
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeRole()">
                            <i class="fas fa-user-tag me-1"></i> Change Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Picture & Quick Actions -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-circle me-1"></i>
                    Profile Picture
                </div>
                <div class="card-body text-center">
                    @if($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" alt="{{ $employee->name }}" 
                             class="img-fluid rounded-circle mb-3" style="max-width: 150px; max-height: 150px;">
                    @else
                        <div class="text-muted mb-3">
                            <i class="fas fa-user-circle fa-5x"></i>
                        </div>
                    @endif
                    <h6>{{ $employee->name }}</h6>
                    <p class="text-muted">{{ $employee->designation ?? $employee->assignedRole?->display_name }}</p>
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
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="resetPassword()">
                            <i class="fas fa-key me-1"></i> Reset Password
                        </button>
                        
                        @if($employee->status === 'active')
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleStatus('inactive')">
                                <i class="fas fa-user-times me-1"></i> Deactivate Employee
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleStatus('active')">
                                <i class="fas fa-user-check me-1"></i> Activate Employee
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="sendWelcomeEmail()">
                            <i class="fas fa-envelope me-1"></i> Send Welcome Email
                        </button>
                        
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Employee
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Company Info -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-building me-1"></i>
                    Company Info
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <strong>Company:</strong> {{ $employee->company?->name ?? 'Not Set' }}<br>
                        <strong>Branch:</strong> {{ $employee->branch?->name ?? 'Not Assigned' }}<br>
                        <strong>Department:</strong> {{ $employee->department ?? 'Not Set' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Employees
        </a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this employee?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEmployee() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function editPermissions() {
    // You can implement an AJAX modal here or redirect to edit page
    window.location.href = "{{ route('admin.employees.edit', $employee) }}#permissions";
}

function changeRole() {
    // You can implement an AJAX modal here or redirect to edit page  
    window.location.href = "{{ route('admin.employees.edit', $employee) }}#role";
}

function toggleStatus(status) {
    if (confirm('Are you sure you want to ' + (status === 'active' ? 'activate' : 'deactivate') + ' this employee?')) {
        // You can implement AJAX here
        fetch("{{ route('admin.employees.update', $employee) }}", {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                _method: 'PUT'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    }
}

function resetPassword() {
    if (confirm('Are you sure you want to reset this employee\'s password?')) {
        alert('Password reset functionality would be implemented here');
        // Implement password reset logic
    }
}

function sendWelcomeEmail() {
    if (confirm('Send welcome email to ' + "{{ $employee->email }}" + '?')) {
        alert('Welcome email functionality would be implemented here');
        // Implement email sending logic
    }
}
</script>
@endsection
