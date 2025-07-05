<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting User Management System setup...');
        
        // First, create all permissions
        $this->createPermissions();
        
        // Then create roles for each company
        $this->createRolesForCompanies();
        
        // Create sample employees for each company
        $this->createSampleEmployees();
        
        $this->command->info('✅ User Management System setup completed successfully!');
    }
    
    private function createPermissions()
    {
        $this->command->info('📋 Creating permissions...');
        
        // Create default permissions
        Permission::createDefaultPermissions();
        
        $this->command->info('✅ Permissions created successfully!');
    }
    
    private function createRolesForCompanies()
    {
        $this->command->info('👥 Creating roles for companies...');
        
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->warn('⚠️  No companies found. Creating a default company first...');
            $this->createDefaultCompany();
            $companies = Company::all();
        }
        
        foreach ($companies as $company) {
            $this->createRolesForCompany($company);
        }
        
        $this->command->info('✅ Roles created for all companies!');
    }
    
    private function createDefaultCompany()
    {
        Company::create([
            'company_name' => 'Demo Herbal Store',
            'slug' => 'demo-herbal-store',
            'domain' => 'demo.herbalstore.local',
            'email' => 'admin@demo.herbalstore.local',
            'phone' => '+1234567890',
            'address' => '123 Demo Street',
            'city' => 'Demo City',
            'state' => 'Demo State',
            'postal_code' => '12345',
            'country' => 'Demo Country',
            'status' => 'active',
            'plan' => 'premium',
            'max_users' => 50,
            'max_products' => 1000,
            'max_orders' => 10000,
        ]);
    }
    
    private function createRolesForCompany($company)
    {
        $this->command->info("  📝 Creating roles for: {$company->company_name}");
        
        // Create default roles
        Role::createDefaultRoles($company->id);
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles($company->id);
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
        
        // Admin Role - Most permissions
        $adminRole = Role::where('name', 'admin')
                        ->where('company_id', $companyId)
                        ->first();
        if ($adminRole) {
            $adminPermissions = $permissions->filter(function ($permission) {
                // Exclude some super sensitive permissions
                $excludePatterns = ['permissions.delete'];
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
                $allowedModules = ['dashboard', 'products', 'orders', 'customers', 'inventory', 'reports', 'users'];
                $restrictedActions = ['delete'];
                
                return in_array($permission->module, $allowedModules) && 
                       !in_array($permission->action, $restrictedActions);
            });
            $managerRole->permissions()->sync($managerPermissions->pluck('id'));
        }
        
        // Staff Role - Basic permissions
        $staffRole = Role::where('name', 'staff')
                        ->where('company_id', $companyId)
                        ->first();
        if ($staffRole) {
            $staffPermissions = $permissions->filter(function ($permission) {
                $allowedPermissions = [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'customers.view',
                    'inventory.view'
                ];
                
                return in_array($permission->name, $allowedPermissions);
            });
            $staffRole->permissions()->sync($staffPermissions->pluck('id'));
        }
        
        // Cashier Role - POS focused permissions
        $cashierRole = Role::where('name', 'cashier')
                          ->where('company_id', $companyId)
                          ->first();
        if ($cashierRole) {
            $cashierPermissions = $permissions->filter(function ($permission) {
                $allowedPermissions = [
                    'dashboard.view',
                    'pos.access',
                    'pos.view_sales',
                    'pos.create_sales',
                    'pos.refund_sales',
                    'products.view',
                    'customers.view',
                    'customers.create'
                ];
                
                return in_array($permission->name, $allowedPermissions);
            });
            $cashierRole->permissions()->sync($cashierPermissions->pluck('id'));
        }
    }
    
    private function createSampleEmployees()
    {
        $this->command->info('👤 Creating sample employees...');
        
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createEmployeesForCompany($company);
        }
        
        $this->command->info('✅ Sample employees created for all companies!');
    }
    
    private function createEmployeesForCompany($company)
    {
        $this->command->info("  👥 Creating employees for: {$company->company_name}");
        
        $roles = Role::where('company_id', $company->id)->get()->keyBy('name');
        
        $employees = [
            [
                'name' => 'John Admin',
                'email' => 'admin@' . str_replace('.local', '.com', $company->domain),
                'employee_id' => 'EMP001',
                'role' => 'admin',
                'department' => 'Administration',
                'designation' => 'System Administrator',
                'phone' => '+1234567801',
                'salary' => 75000.00,
                'hire_date' => now()->subMonths(12),
                'status' => 'active'
            ],
            [
                'name' => 'Sarah Manager',
                'email' => 'manager@' . str_replace('.local', '.com', $company->domain),
                'employee_id' => 'EMP002',
                'role' => 'manager',
                'department' => 'Sales',
                'designation' => 'Sales Manager',
                'phone' => '+1234567802',
                'salary' => 65000.00,
                'hire_date' => now()->subMonths(8),
                'status' => 'active'
            ],
            [
                'name' => 'Mike Cashier',
                'email' => 'cashier@' . str_replace('.local', '.com', $company->domain),
                'employee_id' => 'EMP003',
                'role' => 'cashier',
                'department' => 'Sales',
                'designation' => 'Head Cashier',
                'phone' => '+1234567803',
                'salary' => 45000.00,
                'hire_date' => now()->subMonths(6),
                'status' => 'active'
            ],
            [
                'name' => 'Emma Staff',
                'email' => 'staff@' . str_replace('.local', '.com', $company->domain),
                'employee_id' => 'EMP004',
                'role' => 'staff',
                'department' => 'Customer Service',
                'designation' => 'Customer Service Representative',
                'phone' => '+1234567804',
                'salary' => 35000.00,
                'hire_date' => now()->subMonths(3),
                'status' => 'active'
            ],
            [
                'name' => 'David Inventory',
                'email' => 'inventory@' . str_replace('.local', '.com', $company->domain),
                'employee_id' => 'EMP005',
                'role' => 'staff',
                'department' => 'Inventory',
                'designation' => 'Inventory Specialist',
                'phone' => '+1234567805',
                'salary' => 40000.00,
                'hire_date' => now()->subMonths(4),
                'status' => 'active'
            ]
        ];
        
        foreach ($employees as $employeeData) {
            // Check if user already exists
            $existingUser = User::where('email', $employeeData['email'])->first();
            if ($existingUser) {
                continue;
            }
            
            $role = $roles->get($employeeData['role']);
            
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'employee_id' => $employeeData['employee_id'],
                'company_id' => $company->id,
                'role' => $employeeData['role'],
                'role_id' => $role?->id,
                'department' => $employeeData['department'],
                'designation' => $employeeData['designation'],
                'phone' => $employeeData['phone'],
                'salary' => $employeeData['salary'],
                'hire_date' => $employeeData['hire_date'],
                'status' => $employeeData['status'],
                'is_super_admin' => false,
                'email_verified_at' => now(),
                'permissions' => []
            ]);
            
            $this->command->info("    ✅ Created employee: {$user->name} ({$user->email})");
        }
    }
}
