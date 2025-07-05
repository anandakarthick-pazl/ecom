<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SuperAdmin\Company;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Please create companies first.');
            return;
        }
        
        foreach ($companies as $company) {
            $this->createRolesForCompany($company->id);
        }
        
        $this->command->info('Default roles created successfully!');
    }
    
    private function createRolesForCompany($companyId)
    {
        // Create default roles
        Role::createDefaultRoles($companyId);
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles($companyId);
    }
    
    private function assignPermissionsToRoles($companyId)
    {
        // Get all permissions
        $permissions = Permission::all()->keyBy('name');
        
        // Super Admin Role - All permissions
        $superAdminRole = Role::where('name', 'super_admin')
                             ->where('company_id', $companyId)
                             ->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($permissions->pluck('id'));
        }
        
        // Admin Role - Most permissions except some super admin specific ones
        $adminRole = Role::where('name', 'admin')
                        ->where('company_id', $companyId)
                        ->first();
        if ($adminRole) {
            $adminPermissions = $permissions->filter(function ($permission) {
                // Exclude some super admin specific permissions
                $excludePatterns = ['permissions.delete', 'roles.delete'];
                foreach ($excludePatterns as $pattern) {
                    if (str_contains($permission->name, $pattern)) {
                        return false;
                    }
                }
                return true;
            });
            $adminRole->permissions()->sync($adminPermissions->pluck('id'));
        }
        
        // Manager Role - Management permissions
        $managerRole = Role::where('name', 'manager')
                          ->where('company_id', $companyId)
                          ->first();
        if ($managerRole) {
            $managerPermissions = $permissions->filter(function ($permission) {
                $allowedModules = ['dashboard', 'products', 'orders', 'customers', 'inventory', 'reports'];
                $allowedActions = ['view', 'create', 'update', 'manage_status'];
                
                return in_array($permission->module, $allowedModules) && 
                       in_array($permission->action, $allowedActions);
            });
            $managerRole->permissions()->sync($managerPermissions->pluck('id'));
        }
        
        // Staff Role - Basic permissions
        $staffRole = Role::where('name', 'staff')
                        ->where('company_id', $companyId)
                        ->first();
        if ($staffRole) {
            $staffPermissions = $permissions->filter(function ($permission) {
                $allowedModules = ['dashboard', 'products', 'orders', 'customers'];
                $allowedActions = ['view'];
                
                return in_array($permission->module, $allowedModules) && 
                       in_array($permission->action, $allowedActions);
            });
            $staffRole->permissions()->sync($staffPermissions->pluck('id'));
        }
        
        // Cashier Role - POS and sales focused permissions
        $cashierRole = Role::where('name', 'cashier')
                          ->where('company_id', $companyId)
                          ->first();
        if ($cashierRole) {
            $cashierPermissions = $permissions->filter(function ($permission) {
                $allowedModules = ['dashboard', 'pos', 'products', 'customers'];
                $posActions = ['access', 'view_sales', 'create_sales', 'refund_sales'];
                
                if ($permission->module === 'pos') {
                    return in_array($permission->action, $posActions);
                }
                
                return in_array($permission->module, $allowedModules) && 
                       $permission->action === 'view';
            });
            $cashierRole->permissions()->sync($cashierPermissions->pluck('id'));
        }
    }
}
