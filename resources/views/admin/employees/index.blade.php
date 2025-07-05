@extends('admin.layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Employee Management</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3>{{ $employees->total() }}</h3>
                        <p>Total Employees</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-success text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3>{{ $employees->where('status', 'active')->count() }}</h3>
                        <p>Active Employees</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-info text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3>{{ $roles->count() }}</h3>
                        <p>Active Roles</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-warning text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3>{{ $departments->count() }}</h3>
                        <p>Departments</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-tie me-1"></i>
                Manage Employees
            </div>
            <div>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Employee
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="Search employees..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="role_id">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="department">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>

            <!-- Employee Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Employee</th>
                            <th>Employee ID</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Hired Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input employee-checkbox" 
                                           value="{{ $employee->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($employee->avatar)
                                            <img src="{{ asset('storage/' . $employee->avatar) }}" 
                                                 alt="{{ $employee->name }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $employee->name }}</div>
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $employee->employee_id ?? 'Not Set' }}</code>
                                </td>
                                <td>
                                    @if($employee->assignedRole)
                                        <span class="badge bg-primary">{{ $employee->assignedRole->display_name }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($employee->role ?? 'No Role') }}</span>
                                    @endif
                                </td>
                                <td>{{ $employee->department ?? 'Not Set' }}</td>
                                <td>{{ $employee->branch?->name ?? 'Not Assigned' }}</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-{{ $employee->status === 'active' ? 'success' : 'danger' }}"
                                            onclick="toggleStatus({{ $employee->id }}, '{{ $employee->status === 'active' ? 'inactive' : 'active' }}')">
                                        <i class="fas fa-{{ $employee->status === 'active' ? 'check' : 'times' }}"></i>
                                        {{ ucfirst($employee->status) }}
                                    </button>
                                </td>
                                <td>{{ $employee->hire_date?->format('M d, Y') ?? 'Not Set' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.employees.show', $employee) }}" 
                                           class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteEmployee({{ $employee->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No employees found</p>
                                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add First Employee
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions -->
            @if($employees->count() > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <select id="bulkAction" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <option value="">Bulk Actions</option>
                            <option value="activate">Activate Selected</option>
                            <option value="deactivate">Deactivate Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-primary ms-2" onclick="executeBulkAction()">
                            Apply
                        </button>
                    </div>
                    
                    <!-- Pagination -->
                    @if($employees->hasPages())
                        {{ $employees->links() }}
                    @endif
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
                <h5 class="modal-title">Delete Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this employee?</p>
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
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function toggleStatus(employeeId, newStatus) {
    if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this employee?`)) {
        fetch(`/admin/employees/${employeeId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus,
                _method: 'PUT'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating employee status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating employee status');
        });
    }
}

function deleteEmployee(employeeId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/admin/employees/${employeeId}`;
    modal.show();
}

function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedEmployees = Array.from(document.querySelectorAll('.employee-checkbox:checked')).map(cb => cb.value);
    
    if (!action || selectedEmployees.length === 0) {
        alert('Please select an action and at least one employee');
        return;
    }
    
    if (confirm(`Are you sure you want to ${action} ${selectedEmployees.length} employee(s)?`)) {
        fetch('/admin/employees/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                employee_ids: selectedEmployees
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error performing bulk action: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error performing bulk action');
        });
    }
}
</script>
@endsection
