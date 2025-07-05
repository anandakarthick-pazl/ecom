<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'is_system_permission'
    ];

    protected $casts = [
        'is_system_permission' => 'boolean'
    ];

    /**
     * Get the roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get users who have this permission through their roles
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, Role::class, 'id', 'role_id', 'id', 'id')
                   ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                   ->where('role_permissions.permission_id', $this->id);
    }

    /**
     * Scope for specific module
     */
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for specific action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for system permissions
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system_permission', true);
    }

    /**
     * Scope for custom permissions
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system_permission', false);
    }

    /**
     * Get all permissions grouped by module
     */
    public static function getGroupedPermissions()
    {
        return self::orderBy('module')->orderBy('action')->get()->groupBy('module');
    }

    /**
     * Get permissions for a specific module with actions
     */
    public static function getModulePermissions($module)
    {
        return self::where('module', $module)->orderBy('action')->get()->keyBy('action');
    }

    /**
     * Create default permissions for the system
     */
    public static function createDefaultPermissions()
    {
        $modules = [
            'dashboard' => [
                'view' => 'View Dashboard'
            ],
            'users' => [
                'view' => 'View Users',
                'create' => 'Create Users',
                'update' => 'Update Users',
                'delete' => 'Delete Users',
                'manage_roles' => 'Manage User Roles',
                'manage_permissions' => 'Manage User Permissions'
            ],
            'roles' => [
                'view' => 'View Roles',
                'create' => 'Create Roles',
                'update' => 'Update Roles',
                'delete' => 'Delete Roles',
                'assign_permissions' => 'Assign Permissions to Roles'
            ],
            'permissions' => [
                'view' => 'View Permissions',
                'create' => 'Create Permissions',
                'update' => 'Update Permissions',
                'delete' => 'Delete Permissions'
            ],
            'products' => [
                'view' => 'View Products',
                'create' => 'Create Products',
                'update' => 'Update Products',
                'delete' => 'Delete Products',
                'manage_categories' => 'Manage Product Categories',
                'manage_inventory' => 'Manage Inventory'
            ],
            'orders' => [
                'view' => 'View Orders',
                'create' => 'Create Orders',
                'update' => 'Update Orders',
                'delete' => 'Delete Orders',
                'manage_status' => 'Manage Order Status',
                'view_reports' => 'View Order Reports'
            ],
            'customers' => [
                'view' => 'View Customers',
                'create' => 'Create Customers',
                'update' => 'Update Customers',
                'delete' => 'Delete Customers'
            ],
            'pos' => [
                'access' => 'Access POS System',
                'view_sales' => 'View POS Sales',
                'create_sales' => 'Create POS Sales',
                'refund_sales' => 'Process Refunds',
                'view_reports' => 'View POS Reports'
            ],
            'inventory' => [
                'view' => 'View Inventory',
                'update' => 'Update Inventory',
                'stock_adjustments' => 'Make Stock Adjustments',
                'purchase_orders' => 'Manage Purchase Orders',
                'suppliers' => 'Manage Suppliers'
            ],
            'reports' => [
                'sales' => 'View Sales Reports',
                'inventory' => 'View Inventory Reports',
                'financial' => 'View Financial Reports',
                'customer' => 'View Customer Reports',
                'export' => 'Export Reports'
            ],
            'settings' => [
                'view' => 'View Settings',
                'update' => 'Update Settings',
                'company' => 'Manage Company Settings',
                'system' => 'Manage System Settings'
            ],
            'branches' => [
                'view' => 'View Branches',
                'create' => 'Create Branches',
                'update' => 'Update Branches',
                'delete' => 'Delete Branches'
            ]
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action => $displayName) {
                $name = "{$module}.{$action}";
                
                self::firstOrCreate(
                    ['name' => $name],
                    [
                        'display_name' => $displayName,
                        'description' => "Permission to {$displayName}",
                        'module' => $module,
                        'action' => $action,
                        'is_system_permission' => true
                    ]
                );
            }
        }
    }

    /**
     * Get permission by name
     */
    public static function getByName($name)
    {
        return self::where('name', $name)->first();
    }

    /**
     * Check if permission can be deleted
     */
    public function canBeDeleted()
    {
        // System permissions cannot be deleted
        if ($this->is_system_permission) {
            return false;
        }
        
        // Permissions assigned to roles cannot be deleted
        if ($this->roles()->count() > 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Get permission statistics
     */
    public function getStats()
    {
        return [
            'roles_count' => $this->roles()->count(),
            'users_count' => $this->users()->count(),
            'active_roles_count' => $this->roles()->where('is_active', true)->count()
        ];
    }
}
