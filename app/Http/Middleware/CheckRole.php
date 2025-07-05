<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Allow super admins to access everything
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Authentication required'], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();

        // If no specific roles are required, just check authentication
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        $hasRole = false;
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. You do not have the required role.',
                    'required_roles' => $roles
                ], 403);
            }

            abort(403, 'Access denied. You do not have the required role.');
        }

        return $next($request);
    }
}
