<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Role extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system_role',
        'company_id',
        'is_active'
    ];

    protected $casts = [
        'is_system_role' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the permissions for the role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the company this role belongs to
     */
    public function company()
    {
        return $this->belongsTo(\App\Models\SuperAdmin\Company::class);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }
        
        if (is_object($permission)) {
            return $this->permissions()->where('id', $permission->id)->exists();
        }
        
        return false;
    }

    /**
     * Assign permission to role
     */
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permission->id);
        }
        
        return $this;
    }

    /**
     * Remove permission from role
     */
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
        
        return $this;
    }

    /**
     * Sync permissions for role
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $permissionIds[] = $perm->id;
                }
            } elseif (is_numeric($permission)) {
                $permissionIds[] = $permission;
            } elseif (is_object($permission)) {
                $permissionIds[] = $permission->id;
            }
        }
        
        $this->permissions()->sync($permissionIds);
        return $this;
    }

    /**
     * Get permissions grouped by module
     */
    public function getGroupedPermissions()
    {
        return $this->permissions()
                   ->get()
                   ->groupBy('module')
                   ->map(function ($permissions) {
                       return $permissions->keyBy('action');
                   });
    }

    /**
     * Scope for active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for non-system roles (can be modified/deleted)
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system_role', false);
    }

    /**
     * Scope for system roles (cannot be deleted)
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system_role', true);
    }

    /**
     * Check if role can be deleted
     */
    public function canBeDeleted()
    {
        // System roles cannot be deleted
        if ($this->is_system_role) {
            return false;
        }
        
        // Roles with users cannot be deleted
        if ($this->users()->count() > 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Get role statistics
     */
    public function getStats()
    {
        return [
            'users_count' => $this->users()->count(),
            'permissions_count' => $this->permissions()->count(),
            'active_users_count' => $this->users()->where('status', 'active')->count(),
            'modules_covered' => $this->permissions()->distinct('module')->count('module')
        ];
    }

    /**
     * Create default roles for a company
     */
    public static function createDefaultRoles($companyId)
    {
        $defaultRoles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'is_system_role' => true,
                'company_id' => $companyId
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Company administrator with most permissions',
                'is_system_role' => true,
                'company_id' => $companyId
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Department manager with management permissions',
                'is_system_role' => true,
                'company_id' => $companyId
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Regular staff member with basic permissions',
                'is_system_role' => true,
                'company_id' => $companyId
            ],
            [
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'POS and sales focused permissions',
                'is_system_role' => true,
                'company_id' => $companyId
            ]
        ];

        foreach ($defaultRoles as $roleData) {
            self::firstOrCreate(
                ['name' => $roleData['name'], 'company_id' => $companyId],
                $roleData
            );
        }
    }
}
