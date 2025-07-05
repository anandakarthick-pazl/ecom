<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users
     */
    public function index(Request $request)
    {
        $query = User::with('company')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->company, function ($q) use ($request) {
                $q->where('company_id', $request->company);
            })
            ->when($request->role, function ($q) use ($request) {
                $q->where('role', $request->role);
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->whereNull('blocked_at');
                } elseif ($request->status === 'blocked') {
                    $q->whereNotNull('blocked_at');
                }
            });

        $users = $query->latest()->paginate(20);
        $companies = Company::where('status', 'active')->get();
        
        $stats = [
            'total' => User::count(),
            'active' => User::whereNull('blocked_at')->count(),
            'blocked' => User::whereNotNull('blocked_at')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'recent' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
        ];

        return view('super-admin.users.index', compact('users', 'companies', 'stats'));
    }

    /**
     * Display admin users
     */
    public function admins(Request $request)
    {
        $query = User::with('company')
            ->where(function ($q) {
                $q->where('role', 'admin')
                  ->orWhere('role', 'super_admin');
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            });

        $admins = $query->latest()->paginate(20);
        
        $stats = [
            'total_admins' => User::where('role', 'admin')->count(),
            'super_admins' => User::where('role', 'super_admin')->count(),
            'active_admins' => User::where('role', 'admin')->whereNull('blocked_at')->count(),
            'recent_logins' => User::where('role', 'admin')
                ->where('last_login_at', '>=', Carbon::now()->subDays(7))
                ->count(),
        ];

        return view('super-admin.users.admins', compact('admins', 'stats'));
    }

    /**
     * Display blocked users
     */
    public function blocked(Request $request)
    {
        $query = User::with(['company', 'blockedBy'])
            ->whereNotNull('blocked_at')
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            });

        $blockedUsers = $query->latest('blocked_at')->paginate(20);
        
        $stats = [
            'total_blocked' => User::whereNotNull('blocked_at')->count(),
            'blocked_today' => User::whereDate('blocked_at', Carbon::today())->count(),
            'blocked_this_week' => User::where('blocked_at', '>=', Carbon::now()->subDays(7))->count(),
            'permanently_blocked' => User::whereNotNull('blocked_at')
                ->whereNull('block_expires_at')
                ->count(),
        ];

        return view('super-admin.users.blocked', compact('blockedUsers', 'stats'));
    }

    /**
     * Block a user
     */
    public function block(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'duration' => 'nullable|in:temporary,permanent',
            'expires_at' => 'nullable|required_if:duration,temporary|date|after:now',
        ]);

        // Don't allow blocking super admins
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot block a super admin user.');
        }

        // Don't allow blocking yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot block yourself.');
        }

        DB::transaction(function () use ($user, $request) {
            $user->update([
                'blocked_at' => now(),
                'blocked_by' => auth()->id(),
                'block_reason' => $request->reason,
                'block_expires_at' => $request->duration === 'temporary' ? $request->expires_at : null,
            ]);

            // Log the action
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'reason' => $request->reason,
                    'duration' => $request->duration,
                    'expires_at' => $request->expires_at,
                ])
                ->log('User blocked');
        });

        return back()->with('success', 'User has been blocked successfully.');
    }

    /**
     * Unblock a user
     */
    public function unblock(User $user)
    {
        if (!$user->blocked_at) {
            return back()->with('error', 'User is not currently blocked.');
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'blocked_at' => null,
                'blocked_by' => null,
                'block_reason' => null,
                'block_expires_at' => null,
            ]);

            // Log the action
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('User unblocked');
        });

        return back()->with('success', 'User has been unblocked successfully.');
    }

    /**
     * Delete a user permanently
     */
    public function destroy(User $user)
    {
        // Don't allow deleting super admins
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete a super admin user.');
        }

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        DB::transaction(function () use ($user) {
            // Log the action before deletion
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'company_id' => $user->company_id,
                ])
                ->log('User deleted');

            $user->delete();
        });

        return back()->with('success', 'User has been deleted permanently.');
    }

    /**
     * Create a new user
     */
    public function create()
    {
        $companies = Company::where('status', 'active')->get();
        return view('super-admin.users.create', compact('companies'));
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,super_admin',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'company_id' => $request->company_id,
                'email_verified_at' => now(),
            ]);

            // Log the action
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('User created');
        });

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User has been created successfully.');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load(['company', 'blockedBy']);
        
        // Get user activity log
        $activities = activity()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('super-admin.users.show', compact('user', 'activities'));
    }

    /**
     * Edit user
     */
    public function edit(User $user)
    {
        $companies = Company::where('status', 'active')->get();
        return view('super-admin.users.edit', compact('user', 'companies'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin,super_admin',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        // Don't allow changing your own role
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return back()->with('error', 'You cannot change your own role.');
        }

        DB::transaction(function () use ($request, $user) {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'company_id' => $request->company_id,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Log the action
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('User updated');
        });

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $query = User::with('company');

        if ($request->filled('company')) {
            $query->where('company_id', $request->company);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('blocked_at');
            } elseif ($request->status === 'blocked') {
                $query->whereNotNull('blocked_at');
            }
        }

        $users = $query->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Role', 'Company', 'Status', 
                'Created At', 'Last Login', 'Blocked At', 'Block Reason'
            ]);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->company ? $user->company->name : 'N/A',
                    $user->blocked_at ? 'Blocked' : 'Active',
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '',
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    $user->blocked_at ? $user->blocked_at->format('Y-m-d H:i:s') : '',
                    $user->block_reason ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:block,unblock,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'reason' => 'required_if:action,block|string|max:500',
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        // Remove current user and super admins from the list
        $userIds = array_filter($userIds, function ($id) {
            return $id != auth()->id();
        });

        $users = User::whereIn('id', $userIds)->get();

        // Filter out super admins if action is block or delete
        if (in_array($action, ['block', 'delete'])) {
            $users = $users->filter(function ($user) {
                return !$user->isSuperAdmin();
            });
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'No valid users selected for this action.');
        }

        DB::transaction(function () use ($users, $action, $request) {
            foreach ($users as $user) {
                switch ($action) {
                    case 'block':
                        $user->update([
                            'blocked_at' => now(),
                            'blocked_by' => auth()->id(),
                            'block_reason' => $request->reason,
                        ]);
                        break;

                    case 'unblock':
                        $user->update([
                            'blocked_at' => null,
                            'blocked_by' => null,
                            'block_reason' => null,
                            'block_expires_at' => null,
                        ]);
                        break;

                    case 'delete':
                        $user->delete();
                        break;
                }

                // Log the action
                activity()
                    ->performedOn($user)
                    ->causedBy(auth()->user())
                    ->withProperties(['bulk_action' => true])
                    ->log('User ' . $action . 'ed (bulk action)');
            }
        });

        $count = $users->count();
        return back()->with('success', "Successfully {$action}ed {$count} user(s).");
    }
}
