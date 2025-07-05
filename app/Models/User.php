<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\SupportTicket;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'branch_id',
        'role',
        'role_id',
        'employee_id',
        'hire_date',
        'department',
        'designation',
        'salary',
        'permissions',
        'is_super_admin',
        'avatar',
        'phone',
        'status',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'permissions' => 'json'
        ];
    }

    const ROLES = [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'staff' => 'Staff'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }

    public function isCompanyAdmin()
    {
        return $this->role === 'admin' && !$this->is_super_admin;
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super_admin', true);
    }

    public function scopeCompanyUsers($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function assignedRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get all permissions for the user (from role + individual permissions)
     */
    public function getAllPermissions()
    {
        $rolePermissions = $this->assignedRole ? $this->assignedRole->permissions : collect();
        $individualPermissions = $this->permissions ? collect($this->permissions) : collect();
        
        // Merge role permissions with individual permissions
        $allPermissions = $rolePermissions->pluck('name')->merge($individualPermissions)->unique();
        
        return Permission::whereIn('name', $allPermissions)->get();
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permission)
    {
        // Super admins have all permissions
        if ($this->is_super_admin) {
            return true;
        }
        
        if (is_string($permission)) {
            $permissionName = $permission;
        } elseif (is_object($permission)) {
            $permissionName = $permission->name;
        } else {
            return false;
        }
        
        // Check individual permissions first
        if ($this->permissions && in_array($permissionName, $this->permissions)) {
            return true;
        }
        
        // Check role permissions
        if ($this->assignedRole) {
            return $this->assignedRole->hasPermission($permissionName);
        }
        
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Give permission to user (individual permission)
     */
    public function givePermissionTo($permission)
    {
        if (is_object($permission)) {
            $permissionName = $permission->name;
        } else {
            $permissionName = $permission;
        }
        
        $currentPermissions = $this->permissions ?? [];
        if (!in_array($permissionName, $currentPermissions)) {
            $currentPermissions[] = $permissionName;
            $this->permissions = $currentPermissions;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Remove permission from user
     */
    public function revokePermissionTo($permission)
    {
        if (is_object($permission)) {
            $permissionName = $permission->name;
        } else {
            $permissionName = $permission;
        }
        
        $currentPermissions = $this->permissions ?? [];
        $currentPermissions = array_diff($currentPermissions, [$permissionName]);
        $this->permissions = array_values($currentPermissions);
        $this->save();
        
        return $this;
    }

    /**
     * Assign role to user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        if ($role) {
            $this->role_id = $role->id;
            $this->role = $role->name; // Keep for backward compatibility
            $this->save();
        }
        
        return $this;
    }

    /**
     * Check if user has role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->assignedRole && $this->assignedRole->name === $role;
        } elseif (is_object($role)) {
            return $this->assignedRole && $this->assignedRole->id === $role->id;
        }
        
        return false;
    }

    /**
     * Get user's permissions grouped by module
     */
    public function getGroupedPermissions()
    {
        return $this->getAllPermissions()->groupBy('module');
    }

    /**
     * Check if user can access module
     */
    public function canAccessModule($module)
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        return $this->getAllPermissions()->where('module', $module)->count() > 0;
    }

    /**
     * Get employee full information
     */
    public function getEmployeeInfo()
    {
        return [
            'employee_id' => $this->employee_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department,
            'designation' => $this->designation,
            'hire_date' => $this->hire_date,
            'salary' => $this->salary,
            'role' => $this->assignedRole?->display_name ?? $this->role,
            'status' => $this->status,
            'avatar' => $this->avatar
        ];
    }

    /**
     * Scope for employees (non-super-admin users)
     */
    public function scopeEmployees($query)
    {
        return $query->where('is_super_admin', false);
    }

    /**
     * Scope for active employees
     */
    public function scopeActiveEmployees($query)
    {
        return $query->employees()->where('status', 'active');
    }
}
