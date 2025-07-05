<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAllPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Allow super admins to access everything
        if (auth()->user()->is_super_admin) {
            return $next($request);
        }

        // Check if user has all of the required permissions
        if (!auth()->user()->hasAllPermissions($permissions)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have all required permissions to access this resource.',
                    'required_permissions' => $permissions,
                    'type' => 'all'
                ], 403);
            }

            abort(403, 'You do not have all required permissions to access this resource.');
        }

        return $next($request);
    }
}
