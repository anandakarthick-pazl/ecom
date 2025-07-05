<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }
        
        $user = Auth::user();
        
        // Super admin can access all companies
        if ($user->is_super_admin) {
            return $next($request);
        }
        
        // Check if user belongs to current tenant company
        $currentTenant = app('current_tenant');
        if (!$currentTenant || $user->company_id !== $currentTenant->id) {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['access' => 'Access denied to this company.']);
        }
        
        // Check if user has admin privileges
        if (!in_array($user->role, ['admin', 'manager'])) {
            return redirect()->route('home')->withErrors(['access' => 'Insufficient privileges.']);
        }
        
        return $next($request);
    }
}
