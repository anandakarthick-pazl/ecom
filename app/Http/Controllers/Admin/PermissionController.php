<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends BaseAdminController
{
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('is_system_permission')) {
            $query->where('is_system_permission', $request->boolean('is_system_permission'));
        }

        $permissions = $query->orderBy('module')->orderBy('action')->paginate(20);
        
        // Get filter options
        $modules = Permission::distinct()->pluck('module')->sort();
        $actions = Permission::distinct()->pluck('action')->sort();
        
        // Group permissions for display
        $groupedPermissions = Permission::getGroupedPermissions();

        return view('admin.permissions.index', compact(
            'permissions', 'modules', 'actions', 'groupedPermissions'
        ));
    }

    public function create()
    {
        $modules = Permission::distinct()->pluck('module')->sort();
        $actions = Permission::distinct()->pluck('action')->sort();
        
        return view('admin.permissions.create', compact('modules', 'actions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100'
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => $request->module,
                'action' => $request->action,
                'is_system_permission' => false // Custom permissions are never system permissions
            ]);

            return redirect()->route('admin.permissions.index')
                           ->with('success', 'Permission created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating permission: ' . $e->getMessage());
        }
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');
        $stats = $permission->getStats();

        return view('admin.permissions.show', compact('permission', 'stats'));
    }

    public function edit(Permission $permission)
    {
        // System permissions cannot be edited
        if ($permission->is_system_permission) {
            return redirect()->back()
                           ->with('warning', 'System permissions cannot be edited.');
        }

        $modules = Permission::distinct()->pluck('module')->sort();
        $actions = Permission::distinct()->pluck('action')->sort();
        
        return view('admin.permissions.edit', compact('permission', 'modules', 'actions'));
    }

    public function update(Request $request, Permission $permission)
    {
        // System permissions cannot be updated
        if ($permission->is_system_permission) {
            return redirect()->back()
                           ->with('error', 'System permissions cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100'
        ]);

        try {
            $permission->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => $request->module,
                'action' => $request->action
            ]);

            return redirect()->route('admin.permissions.index')
                           ->with('success', 'Permission updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating permission: ' . $e->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            if (!$permission->canBeDeleted()) {
                $reason = $permission->is_system_permission ? 'System permissions cannot be deleted.' : 
                         'Permission cannot be deleted because it is assigned to roles.';
                
                return redirect()->back()
                               ->with('error', $reason);
            }

            $permission->delete();

            return redirect()->route('admin.permissions.index')
                           ->with('success', 'Permission deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::findOrFail($request->role_id);
            $currentPermissions = $role->permissions->pluck('id')->toArray();
            $newPermissions = array_unique(array_merge($currentPermissions, $request->permission_ids));
            
            $role->permissions()->sync($newPermissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned to role successfully!',
                'assigned_count' => count($request->permission_ids)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning permissions: ' . $e->getMessage()
            ], 422);
        }
    }

    public function bulkRemove(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::findOrFail($request->role_id);
            $role->permissions()->detach($request->permission_ids);

            return response()->json([
                'success' => true,
                'message' => 'Permissions removed from role successfully!',
                'removed_count' => count($request->permission_ids)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing permissions: ' . $e->getMessage()
            ], 422);
        }
    }

    public function getRoles(Permission $permission)
    {
        $roles = $permission->roles()
                           ->where('company_id', $this->getCurrentCompanyId())
                           ->withCount('users')
                           ->get();

        return response()->json([
            'roles' => $roles,
            'total' => $roles->count()
        ]);
    }

    public function modulePermissions($module)
    {
        $permissions = Permission::getModulePermissions($module);

        return response()->json([
            'module' => $module,
            'permissions' => $permissions
        ]);
    }

    public function generatePermissions(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:100',
            'actions' => 'required|array',
            'actions.*' => 'required|string|max:100'
        ]);

        try {
            $createdPermissions = [];
            
            foreach ($request->actions as $action) {
                $name = "{$request->module}.{$action}";
                $displayName = ucfirst($action) . ' ' . ucfirst($request->module);
                
                // Check if permission already exists
                if (Permission::where('name', $name)->exists()) {
                    continue;
                }
                
                $permission = Permission::create([
                    'name' => $name,
                    'display_name' => $displayName,
                    'description' => "Permission to {$action} {$request->module}",
                    'module' => $request->module,
                    'action' => $action,
                    'is_system_permission' => false
                ]);
                
                $createdPermissions[] = $permission;
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions generated successfully!',
                'created_count' => count($createdPermissions),
                'permissions' => $createdPermissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating permissions: ' . $e->getMessage()
            ], 422);
        }
    }

    public function export(Request $request)
    {
        $permissions = Permission::orderBy('module')->orderBy('action')->get();
        
        $filename = 'permissions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($permissions) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'Display Name', 'Module', 'Action', 'Description', 'System Permission', 'Roles Count']);
            
            // Add data
            foreach ($permissions as $permission) {
                fputcsv($file, [
                    $permission->name,
                    $permission->display_name,
                    $permission->module,
                    $permission->action,
                    $permission->description,
                    $permission->is_system_permission ? 'Yes' : 'No',
                    $permission->roles_count ?? 0
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
