<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = User::with(['assignedRole', 'branch', 'company'])
                    ->employees()
                    ->where('company_id', $this->getCurrentCompanyId());

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Enhanced filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('hire_date_from')) {
            $query->whereDate('hire_date', '>=', $request->hire_date_from);
        }

        if ($request->filled('hire_date_to')) {
            $query->whereDate('hire_date', '<=', $request->hire_date_to);
        }

        // Sorting options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $employees = $query->paginate($request->get('per_page', 15));
        
        // Get filter options
        $roles = Role::active()->currentTenant()->get();
        $departments = User::employees()->currentTenant()
                          ->whereNotNull('department')
                          ->distinct()
                          ->pluck('department');
        $branches = Branch::currentTenant()->get();

        // Enhanced statistics
        $stats = $this->getEmployeeStatistics();

        return view('admin.employees.index', compact(
            'employees', 'roles', 'departments', 'branches', 'stats'
        ));
    }

    public function create()
    {
        $roles = Role::active()->currentTenant()->get();
        $branches = Branch::currentTenant()->get();
        $permissions = Permission::getGroupedPermissions();
        
        return view('admin.employees.create', compact('roles', 'branches', 'permissions'));
    }

    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Generate employee ID if not provided
            $employeeId = $request->employee_id ?: $this->generateEmployeeId();

            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            // Get role for backward compatibility
            $role = Role::findOrFail($request->role_id);

            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $employeeId,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'role' => $role->name, // Backward compatibility
                'branch_id' => $request->branch_id,
                'department' => $request->department,
                'designation' => $request->designation,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'avatar' => $avatarPath,
                'status' => $request->status ?: 'active',
                'company_id' => $this->getCurrentCompanyId(),
                'permissions' => $request->individual_permissions ?? [],
                'is_super_admin' => false
            ]);

            return redirect()->route('admin.employees.index')
                           ->with('success', 'Employee created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating employee: ' . $e->getMessage());
        }
    }

    public function show(User $employee)
    {
        $employee->load(['assignedRole', 'branch', 'company']);
        
        // Ensure we're viewing an employee, not a super admin
        if ($employee->is_super_admin) {
            abort(403, 'Cannot view super admin details.');
        }

        return view('admin.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        // Ensure we're editing an employee, not a super admin
        if ($employee->is_super_admin) {
            abort(403, 'Cannot edit super admin.');
        }

        $roles = Role::active()->currentTenant()->get();
        $branches = Branch::currentTenant()->get();
        $permissions = Permission::getGroupedPermissions();
        
        return view('admin.employees.edit', compact('employee', 'roles', 'branches', 'permissions'));
    }

    public function update(Request $request, User $employee)
    {
        // Ensure we're updating an employee, not a super admin
        if ($employee->is_super_admin) {
            abort(403, 'Cannot update super admin.');
        }

        // Basic validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id ?: $employee->employee_id,
                'phone' => $request->phone,
                'role_id' => $request->role_id,
                'branch_id' => $request->branch_id,
                'department' => $request->department,
                'designation' => $request->designation,
                'hire_date' => $request->hire_date,
                'salary' => $request->salary,
                'status' => $request->status ?: $employee->status,
                'permissions' => $request->individual_permissions ?? []
            ];

            // Update role for backward compatibility
            $role = Role::findOrFail($request->role_id);
            $updateData['role'] = $role->name;

            // Handle password update
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($employee->avatar) {
                    Storage::disk('public')->delete($employee->avatar);
                }
                $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $employee->update($updateData);

            return redirect()->route('admin.employees.index')
                           ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    public function destroy(User $employee)
    {
        // Ensure we're deleting an employee, not a super admin
        if ($employee->is_super_admin) {
            abort(403, 'Cannot delete super admin.');
        }

        try {
            // Delete avatar if exists
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }

            $employee->delete();

            return redirect()->route('admin.employees.index')
                           ->with('success', 'Employee deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }

    public function updatePermissions(Request $request, User $employee)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        try {
            $employee->update([
                'permissions' => $request->permissions ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions: ' . $e->getMessage()
            ], 422);
        }
    }

    public function updateRole(Request $request, User $employee)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        try {
            $role = Role::findOrFail($request->role_id);
            
            $employee->update([
                'role_id' => $role->id,
                'role' => $role->name // Backward compatibility
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!',
                'role' => $role->display_name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getPermissions(User $employee)
    {
        $rolePermissions = $employee->assignedRole ? 
                          $employee->assignedRole->permissions->pluck('name')->toArray() : [];
        $individualPermissions = $employee->permissions ?? [];

        return response()->json([
            'role_permissions' => $rolePermissions,
            'individual_permissions' => $individualPermissions,
            'all_permissions' => $employee->getAllPermissions()->pluck('name')->toArray()
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id'
        ]);

        try {
            $employees = User::whereIn('id', $request->employee_ids)
                           ->employees()
                           ->currentTenant()
                           ->get();

            switch ($request->action) {
                case 'activate':
                    $employees->each->update(['status' => 'active']);
                    $message = 'Employees activated successfully!';
                    break;
                    
                case 'deactivate':
                    $employees->each->update(['status' => 'inactive']);
                    $message = 'Employees deactivated successfully!';
                    break;
                    
                case 'delete':
                    foreach ($employees as $employee) {
                        if ($employee->avatar) {
                            Storage::disk('public')->delete($employee->avatar);
                        }
                        $employee->delete();
                    }
                    $message = 'Employees deleted successfully!';
                    break;
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    // ENHANCED METHODS

    protected function getEmployeeStatistics()
    {
        $companyId = $this->getCurrentCompanyId();
        
        return [
            'total' => User::employees()->where('company_id', $companyId)->count(),
            'active' => User::employees()->where('company_id', $companyId)->where('status', 'active')->count(),
            'inactive' => User::employees()->where('company_id', $companyId)->where('status', 'inactive')->count(),
            'new_this_month' => User::employees()
                ->where('company_id', $companyId)
                ->whereMonth('hire_date', Carbon::now()->month)
                ->whereYear('hire_date', Carbon::now()->year)
                ->count(),
            'departments' => User::employees()
                ->where('company_id', $companyId)
                ->whereNotNull('department')
                ->distinct('department')
                ->count('department'),
            'roles' => Role::where('company_id', $companyId)->active()->count(),
            'average_salary' => User::employees()
                ->where('company_id', $companyId)
                ->whereNotNull('salary')
                ->avg('salary') ?: 0,
            'pending_reviews' => User::employees()
                ->where('company_id', $companyId)
                ->where(function($q) {
                    $q->where('last_review_date', '<', Carbon::now()->subYear())
                      ->orWhereNull('last_review_date');
                })
                ->count()
        ];
    }

    protected function generateEmployeeId()
    {
        $prefix = 'EMP';
        $lastEmployee = User::where('employee_id', 'like', $prefix . '%')
                           ->orderBy('employee_id', 'desc')
                           ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
