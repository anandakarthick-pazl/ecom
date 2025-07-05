<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = Role::with(['permissions', 'company'])
                    ->withCount('users')
                    ->currentTenant();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('is_system_role')) {
            $query->where('is_system_role', $request->boolean('is_system_role'));
        }

        $roles = $query->latest()->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::getGroupedPermissions();
        
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            // Check if role name is unique for this company
            $existingRole = Role::where('name', $request->name)
                              ->where('company_id', $this->getCurrentCompanyId())
                              ->first();

            if ($existingRole) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Role name already exists in your company.');
            }

            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_system_role' => false, // Custom roles are never system roles
                'is_active' => $request->boolean('is_active', true),
                'company_id' => $this->getCurrentCompanyId()
            ]);

            // Sync permissions
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Role created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $permissionGroups = $role->getGroupedPermissions();
        $stats = $role->getStats();

        return view('admin.roles.show', compact('role', 'permissionGroups', 'stats'));
    }

    public function edit(Role $role)
    {
        // Ensure we can edit this role
        if ($role->is_system_role) {
            return redirect()->back()
                           ->with('warning', 'System roles cannot be edited. You can only modify permissions.');
        }

        $permissions = Permission::getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            // Check if role name is unique for this company (excluding current role)
            $existingRole = Role::where('name', $request->name)
                              ->where('company_id', $this->getCurrentCompanyId())
                              ->where('id', '!=', $role->id)
                              ->first();

            if ($existingRole) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Role name already exists in your company.');
            }

            $updateData = [
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ];

            // Only allow name change for non-system roles
            if (!$role->is_system_role) {
                $updateData['name'] = $request->name;
            }

            $role->update($updateData);

            // Sync permissions
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->sync([]);
            }

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Role updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            if (!$role->canBeDeleted()) {
                $reason = $role->is_system_role ? 'System roles cannot be deleted.' : 
                         'Role cannot be deleted because it has assigned users.';
                
                return redirect()->back()
                               ->with('error', $reason);
            }

            $role->delete();

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Role deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    public function permissions(Role $role)
    {
        $permissions = Permission::getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->sync([]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully!',
                'permissions_count' => count($request->permissions ?? [])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating permissions: ' . $e->getMessage()
            ], 422);
        }
    }

    public function duplicate(Role $role)
    {
        try {
            $newRoleName = $role->name . '_copy_' . time();
            
            $newRole = Role::create([
                'name' => $newRoleName,
                'display_name' => $role->display_name . ' (Copy)',
                'description' => $role->description ? $role->description . ' (Copy)' : null,
                'is_system_role' => false, // Duplicated roles are always custom
                'is_active' => true,
                'company_id' => $role->company_id
            ]);

            // Copy all permissions
            $permissionIds = $role->permissions->pluck('id')->toArray();
            $newRole->permissions()->sync($permissionIds);

            return redirect()->route('admin.roles.edit', $newRole)
                           ->with('success', 'Role duplicated successfully! Please review and modify as needed.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error duplicating role: ' . $e->getMessage());
        }
    }

    public function assignUsers(Request $request, Role $role)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $users = User::whereIn('id', $request->user_ids)
                        ->employees()
                        ->currentTenant()
                        ->get();

            foreach ($users as $user) {
                $user->assignRole($role);
            }

            return response()->json([
                'success' => true,
                'message' => 'Users assigned to role successfully!',
                'assigned_count' => $users->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning users: ' . $e->getMessage()
            ], 422);
        }
    }

    public function removeUser(Request $request, Role $role, User $user)
    {
        try {
            $user->update(['role_id' => null, 'role' => null]);

            return response()->json([
                'success' => true,
                'message' => 'User removed from role successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing user: ' . $e->getMessage()
            ], 422);
        }
    }

    public function toggleStatus(Role $role)
    {
        try {
            $role->update(['is_active' => !$role->is_active]);

            $status = $role->is_active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Role {$status} successfully!",
                'is_active' => $role->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role status: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getUsers(Role $role)
    {
        $users = $role->users()
                     ->with(['branch'])
                     ->select(['id', 'name', 'email', 'employee_id', 'department', 'status', 'branch_id'])
                     ->get();

        return response()->json([
            'users' => $users,
            'total' => $users->count()
        ]);
    }

}
