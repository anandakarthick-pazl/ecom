<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SuperAdmin\Company;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default permissions first
        $this->createDefaultPermissions();
        
        // Get all active companies
        $companies = Company::where('status', 'active')->get();
        
        foreach ($companies as $company) {
            $this->createDefaultRolesForCompany($company->id);
        }
        
        $this->command->info('Default roles and permissions created successfully!');
    }
    
    private function createDefaultPermissions()
    {
        Permission::createDefaultPermissions();
        $this->command->info('Default permissions created');
    }
    
    private function createDefaultRolesForCompany($companyId)
    {
        // Create default roles
        Role::createDefaultRoles($companyId);
        
        // Get permissions for assignment
        $allPermissions = Permission::all();
        $permissionGroups = $allPermissions->groupBy('module');
        
        // Define role permission mappings
        $rolePermissions = [
            'super_admin' => $allPermissions->pluck('id')->toArray(), // All permissions
            'admin' => $this->getAdminPermissions($permissionGroups),
            'manager' => $this->getManagerPermissions($permissionGroups),
            'staff' => $this->getStaffPermissions($permissionGroups),
            'cashier' => $this->getCashierPermissions($permissionGroups)
        ];
        
        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissionIds) {
            $role = Role::where('name', $roleName)
                       ->where('company_id', $companyId)
                       ->first();
                       
            if ($role && !empty($permissionIds)) {
                $role->permissions()->sync($permissionIds);
            }
        }
        
        $this->command->info("Default roles created for company ID: {$companyId}");
    }
    
    private function getAdminPermissions($permissionGroups)
    {
        $permissions = [];
        
        // Admin gets most permissions except some sensitive system ones
        $allowedModules = [
            'dashboard', 'users', 'roles', 'products', 'orders', 'customers',
            'pos', 'inventory', 'reports', 'settings', 'branches'
        ];
        
        foreach ($allowedModules as $module) {
            if (isset($permissionGroups[$module])) {
                $permissions = array_merge($permissions, $permissionGroups[$module]->pluck('id')->toArray());
            }
        }
        
        return $permissions;
    }
    
    private function getManagerPermissions($permissionGroups)
    {
        $permissions = [];
        
        // Manager permissions - can manage most operations but limited admin functions
        $modulePermissions = [
            'dashboard' => ['view'],
            'users' => ['view', 'create', 'update'],
            'products' => ['view', 'create', 'update', 'manage_categories', 'manage_inventory'],
            'orders' => ['view', 'create', 'update', 'manage_status', 'view_reports'],
            'customers' => ['view', 'create', 'update'],
            'pos' => ['access', 'view_sales', 'create_sales', 'view_reports'],
            'inventory' => ['view', 'update', 'stock_adjustments', 'purchase_orders', 'suppliers'],
            'reports' => ['sales', 'inventory', 'customer'],
            'settings' => ['view']
        ];
        
        foreach ($modulePermissions as $module => $actions) {
            if (isset($permissionGroups[$module])) {
                foreach ($actions as $action) {
                    $permission = $permissionGroups[$module]->where('action', $action)->first();
                    if ($permission) {
                        $permissions[] = $permission->id;
                    }
                }
            }
        }
        
        return $permissions;
    }
    
    private function getStaffPermissions($permissionGroups)
    {
        $permissions = [];
        
        // Staff permissions - basic operations
        $modulePermissions = [
            'dashboard' => ['view'],
            'products' => ['view', 'update'],
            'orders' => ['view', 'create', 'update'],
            'customers' => ['view', 'create', 'update'],
            'pos' => ['access', 'view_sales', 'create_sales'],
            'inventory' => ['view'],
            'reports' => ['sales']
        ];
        
        foreach ($modulePermissions as $module => $actions) {
            if (isset($permissionGroups[$module])) {
                foreach ($actions as $action) {
                    $permission = $permissionGroups[$module]->where('action', $action)->first();
                    if ($permission) {
                        $permissions[] = $permission->id;
                    }
                }
            }
        }
        
        return $permissions;
    }
    
    private function getCashierPermissions($permissionGroups)
    {
        $permissions = [];
        
        // Cashier permissions - POS focused
        $modulePermissions = [
            'dashboard' => ['view'],
            'products' => ['view'],
            'orders' => ['view'],
            'customers' => ['view', 'create'],
            'pos' => ['access', 'view_sales', 'create_sales', 'refund_sales'],
            'inventory' => ['view']
        ];
        
        foreach ($modulePermissions as $module => $actions) {
            if (isset($permissionGroups[$module])) {
                foreach ($actions as $action) {
                    $permission = $permissionGroups[$module]->where('action', $action)->first();
                    if ($permission) {
                        $permissions[] = $permission->id;
                    }
                }
            }
        }
        
        return $permissions;
    }
}
