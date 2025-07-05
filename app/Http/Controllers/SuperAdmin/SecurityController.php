<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SecurityController extends Controller
{
    /**
     * Security dashboard
     */
    public function index()
    {
        $securityMetrics = $this->getSecurityMetrics();
        $recentThreats = $this->getRecentThreats();
        $securityAlerts = $this->getSecurityAlerts();
        $systemSecurity = $this->getSystemSecurityStatus();

        return view('super-admin.security.index', compact(
            'securityMetrics',
            'recentThreats', 
            'securityAlerts',
            'systemSecurity'
        ));
    }

    /**
     * Access control management
     */
    public function accessControl(Request $request)
    {
        $ipWhitelist = $this->getIpWhitelist();
        $ipBlacklist = $this->getIpBlacklist();
        $accessRules = $this->getAccessRules();
        $recentAccess = $this->getRecentAccessAttempts();

        return view('super-admin.security.access-control', compact(
            'ipWhitelist',
            'ipBlacklist', 
            'accessRules',
            'recentAccess'
        ));
    }

    /**
     * Role management
     */
    public function roles(Request $request)
    {
        $roles = DB::table('roles')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $permissions = $this->getAvailablePermissions();
        $roleStats = $this->getRoleStats();

        return view('super-admin.security.roles', compact('roles', 'permissions', 'roleStats'));
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach permissions to role
            foreach ($request->permissions as $permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_name' => $permission,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'role_name' => $request->name,
                    'permissions' => $request->permissions,
                ])
                ->log('Security role created');

            return back()->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create role', [
                'error' => $e->getMessage(),
                'role_name' => $request->name,
            ]);
            return back()->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /**
     * Update existing role
     */
    public function updateRole(Request $request, $roleId)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = DB::table('roles')->where('id', $roleId)->first();

        if (!$role) {
            return back()->with('error', 'Role not found.');
        }

        // Prevent modifying system roles
        if (in_array($role->name, ['super_admin', 'admin'])) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        DB::beginTransaction();
        try {
            // Update role
            DB::table('roles')
                ->where('id', $roleId)
                ->update([
                    'name' => $request->name,
                    'display_name' => $request->display_name,
                    'description' => $request->description,
                    'updated_at' => now(),
                ]);

            // Update permissions
            DB::table('role_permissions')->where('role_id', $roleId)->delete();
            
            foreach ($request->permissions as $permission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_name' => $permission,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'role_id' => $roleId,
                    'role_name' => $request->name,
                    'permissions' => $request->permissions,
                ])
                ->log('Security role updated');

            return back()->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to update role', [
                'error' => $e->getMessage(),
                'role_id' => $roleId,
            ]);
            return back()->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function deleteRole($roleId)
    {
        $role = DB::table('roles')->where('id', $roleId)->first();

        if (!$role) {
            return back()->with('error', 'Role not found.');
        }

        // Prevent deleting system roles
        if (in_array($role->name, ['super_admin', 'admin', 'user'])) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        // Check if role is assigned to any users
        $userCount = DB::table('users')->where('role', $role->name)->count();
        
        if ($userCount > 0) {
            return back()->with('error', "Cannot delete role. It is assigned to {$userCount} user(s).");
        }

        DB::beginTransaction();
        try {
            // Delete role permissions
            DB::table('role_permissions')->where('role_id', $roleId)->delete();
            
            // Delete role
            DB::table('roles')->where('id', $roleId)->delete();

            DB::commit();

            // Log the action
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'role_id' => $roleId,
                    'role_name' => $role->name,
                ])
                ->log('Security role deleted');

            return back()->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete role', [
                'error' => $e->getMessage(),
                'role_id' => $roleId,
            ]);
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Add IP to whitelist
     */
    public function addToWhitelist(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'description' => 'nullable|string|max:255',
        ]);

        // Check if IP already exists
        $exists = DB::table('ip_whitelist')->where('ip_address', $request->ip_address)->exists();
        
        if ($exists) {
            return back()->with('error', 'IP address already exists in whitelist.');
        }

        DB::table('ip_whitelist')->insert([
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip_address,
                'description' => $request->description,
            ])
            ->log('IP address added to whitelist');

        return back()->with('success', 'IP address added to whitelist successfully.');
    }

    /**
     * Add IP to blacklist
     */
    public function addToBlacklist(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Check if IP already exists
        $exists = DB::table('ip_blacklist')->where('ip_address', $request->ip_address)->exists();
        
        if ($exists) {
            return back()->with('error', 'IP address already exists in blacklist.');
        }

        DB::table('ip_blacklist')->insert([
            'ip_address' => $request->ip_address,
            'reason' => $request->reason,
            'expires_at' => $request->expires_at,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $request->ip_address,
                'reason' => $request->reason,
                'expires_at' => $request->expires_at,
            ])
            ->log('IP address added to blacklist');

        return back()->with('success', 'IP address added to blacklist successfully.');
    }

    /**
     * Remove IP from whitelist
     */
    public function removeFromWhitelist($id)
    {
        $entry = DB::table('ip_whitelist')->where('id', $id)->first();
        
        if (!$entry) {
            return back()->with('error', 'Whitelist entry not found.');
        }

        DB::table('ip_whitelist')->where('id', $id)->delete();

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $entry->ip_address,
                'entry_id' => $id,
            ])
            ->log('IP address removed from whitelist');

        return back()->with('success', 'IP address removed from whitelist successfully.');
    }

    /**
     * Remove IP from blacklist
     */
    public function removeFromBlacklist($id)
    {
        $entry = DB::table('ip_blacklist')->where('id', $id)->first();
        
        if (!$entry) {
            return back()->with('error', 'Blacklist entry not found.');
        }

        DB::table('ip_blacklist')->where('id', $id)->delete();

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'ip_address' => $entry->ip_address,
                'entry_id' => $id,
            ])
            ->log('IP address removed from blacklist');

        return back()->with('success', 'IP address removed from blacklist successfully.');
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(Request $request)
    {
        $request->validate([
            'login_attempts_limit' => 'required|integer|min:3|max:10',
            'lockout_duration' => 'required|integer|min:5|max:1440', // minutes
            'password_min_length' => 'required|integer|min:6|max:50',
            'require_special_chars' => 'boolean',
            'require_numbers' => 'boolean',
            'require_uppercase' => 'boolean',
            'session_timeout' => 'required|integer|min:15|max:1440', // minutes
            'two_factor_required' => 'boolean',
            'ip_restriction_enabled' => 'boolean',
        ]);

        $settings = [
            'login_attempts_limit' => $request->login_attempts_limit,
            'lockout_duration' => $request->lockout_duration,
            'password_min_length' => $request->password_min_length,
            'require_special_chars' => $request->boolean('require_special_chars'),
            'require_numbers' => $request->boolean('require_numbers'),
            'require_uppercase' => $request->boolean('require_uppercase'),
            'session_timeout' => $request->session_timeout,
            'two_factor_required' => $request->boolean('two_factor_required'),
            'ip_restriction_enabled' => $request->boolean('ip_restriction_enabled'),
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ];

        foreach ($settings as $key => $value) {
            DB::table('security_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        // Log the action
        activity()
            ->causedBy(auth()->user())
            ->withProperties($settings)
            ->log('Security settings updated');

        return back()->with('success', 'Security settings updated successfully.');
    }

    // Private Helper Methods

    private function getSecurityMetrics()
    {
        return [
            'failed_logins_today' => $this->getFailedLoginsCount(today()),
            'blocked_ips' => DB::table('ip_blacklist')->count(),
            'active_sessions' => $this->getActiveSessionsCount(),
            'security_incidents' => $this->getSecurityIncidentsCount(),
            'password_strength_score' => $this->getPasswordStrengthScore(),
            'two_factor_enabled' => $this->getTwoFactorEnabledCount(),
            'suspicious_activities' => $this->getSuspiciousActivitiesCount(),
            'vulnerability_score' => $this->getVulnerabilityScore(),
        ];
    }

    private function getRecentThreats()
    {
        return [
            [
                'type' => 'Brute Force Attack',
                'ip' => '192.168.1.100',
                'attempts' => 25,
                'target' => 'Admin Login',
                'detected_at' => Carbon::now()->subHours(2),
                'status' => 'Blocked',
                'severity' => 'High',
            ],
            [
                'type' => 'SQL Injection Attempt',
                'ip' => '10.0.0.5',
                'attempts' => 3,
                'target' => 'Product Search',
                'detected_at' => Carbon::now()->subHours(5),
                'status' => 'Blocked',
                'severity' => 'Critical',
            ],
            [
                'type' => 'Suspicious API Usage',
                'ip' => '172.16.0.10',
                'attempts' => 150,
                'target' => 'API Endpoints',
                'detected_at' => Carbon::now()->subHours(8),
                'status' => 'Monitoring',
                'severity' => 'Medium',
            ],
        ];
    }

    private function getSecurityAlerts()
    {
        return [
            [
                'type' => 'Critical',
                'message' => 'Multiple failed login attempts detected from same IP',
                'created_at' => Carbon::now()->subMinutes(30),
                'resolved' => false,
            ],
            [
                'type' => 'Warning',
                'message' => 'Unusual API usage pattern detected',
                'created_at' => Carbon::now()->subHours(2),
                'resolved' => false,
            ],
            [
                'type' => 'Info',
                'message' => 'Security settings updated by admin',
                'created_at' => Carbon::now()->subHours(6),
                'resolved' => true,
            ],
        ];
    }

    private function getSystemSecurityStatus()
    {
        return [
            'firewall' => ['status' => 'active', 'last_updated' => Carbon::now()->subHours(1)],
            'ssl_certificate' => ['status' => 'valid', 'expires_at' => Carbon::now()->addDays(45)],
            'security_headers' => ['status' => 'configured', 'score' => 'A+'],
            'vulnerability_scan' => ['status' => 'passed', 'last_scan' => Carbon::now()->subDays(7)],
            'backup_encryption' => ['status' => 'enabled', 'algorithm' => 'AES-256'],
            'database_encryption' => ['status' => 'enabled', 'type' => 'TDE'],
        ];
    }

    private function getIpWhitelist()
    {
        return DB::table('ip_whitelist')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->created_at = Carbon::parse($item->created_at);
                return $item;
            });
    }

    private function getIpBlacklist()
    {
        return DB::table('ip_blacklist')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->created_at = Carbon::parse($item->created_at);
                $item->expires_at = $item->expires_at ? Carbon::parse($item->expires_at) : null;
                $item->is_expired = $item->expires_at && $item->expires_at->isPast();
                return $item;
            });
    }

    private function getAccessRules()
    {
        return [
            [
                'id' => 1,
                'name' => 'Admin Panel Access',
                'description' => 'Restrict admin panel access to office IPs only',
                'condition' => 'IP in whitelist AND role = admin',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(30),
            ],
            [
                'id' => 2,
                'name' => 'API Rate Limiting',
                'description' => 'Limit API requests to 1000 per hour per IP',
                'condition' => 'API requests > 1000/hour',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => 3,
                'name' => 'Geographic Restriction',
                'description' => 'Block access from certain countries',
                'condition' => 'Country in blocked_countries',
                'status' => 'inactive',
                'created_at' => Carbon::now()->subDays(7),
            ],
        ];
    }

    private function getRecentAccessAttempts()
    {
        return [
            [
                'ip' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'endpoint' => '/admin/login',
                'result' => 'Blocked',
                'reason' => 'IP in blacklist',
                'timestamp' => Carbon::now()->subMinutes(15),
            ],
            [
                'ip' => '10.0.0.5',
                'user_agent' => 'API Client/1.0',
                'endpoint' => '/api/v1/products',
                'result' => 'Allowed',
                'reason' => 'Valid API key',
                'timestamp' => Carbon::now()->subMinutes(30),
            ],
            [
                'ip' => '172.16.0.10',
                'user_agent' => 'Bot/1.0',
                'endpoint' => '/admin/dashboard',
                'result' => 'Blocked',
                'reason' => 'Rate limit exceeded',
                'timestamp' => Carbon::now()->subHours(1),
            ],
        ];
    }

    private function getAvailablePermissions()
    {
        return [
            'users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.block' => 'Block/Unblock Users',
            ],
            'companies' => [
                'companies.view' => 'View Companies',
                'companies.create' => 'Create Companies',
                'companies.edit' => 'Edit Companies',
                'companies.delete' => 'Delete Companies',
                'companies.suspend' => 'Suspend Companies',
            ],
            'products' => [
                'products.view' => 'View Products',
                'products.create' => 'Create Products',
                'products.edit' => 'Edit Products',
                'products.delete' => 'Delete Products',
                'products.manage_categories' => 'Manage Categories',
            ],
            'orders' => [
                'orders.view' => 'View Orders',
                'orders.create' => 'Create Orders',
                'orders.edit' => 'Edit Orders',
                'orders.cancel' => 'Cancel Orders',
                'orders.refund' => 'Process Refunds',
            ],
            'reports' => [
                'reports.view' => 'View Reports',
                'reports.export' => 'Export Reports',
                'reports.analytics' => 'Access Analytics',
            ],
            'settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.system' => 'System Settings',
                'settings.security' => 'Security Settings',
            ],
            'api' => [
                'api.manage_keys' => 'Manage API Keys',
                'api.view_logs' => 'View API Logs',
                'api.webhooks' => 'Manage Webhooks',
            ],
        ];
    }

    private function getRoleStats()
    {
        return [
            'total_roles' => DB::table('roles')->count(),
            'custom_roles' => DB::table('roles')->whereNotIn('name', ['super_admin', 'admin', 'user'])->count(),
            'users_with_roles' => DB::table('users')->whereNotNull('role')->count(),
            'permissions_count' => DB::table('permissions')->count(),
        ];
    }

    private function getFailedLoginsCount($since)
    {
        // Mock implementation - implement actual failed login tracking
        return rand(10, 50);
    }

    private function getActiveSessionsCount()
    {
        // Mock implementation - implement actual session counting
        return rand(50, 200);
    }

    private function getSecurityIncidentsCount()
    {
        // Mock implementation
        return rand(0, 5);
    }

    private function getPasswordStrengthScore()
    {
        // Mock implementation - calculate based on user passwords
        return 85; // percentage
    }

    private function getTwoFactorEnabledCount()
    {
        // Mock implementation
        return rand(10, 50);
    }

    private function getSuspiciousActivitiesCount()
    {
        // Mock implementation
        return rand(2, 15);
    }

    private function getVulnerabilityScore()
    {
        // Mock implementation - lower is better
        return rand(1, 10);
    }
}
