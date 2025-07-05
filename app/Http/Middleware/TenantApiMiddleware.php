<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Auth;

class TenantApiMiddleware
{
    /**
     * Handle an incoming request for API routes with tenant context
     */
    public function handle(Request $request, Closure $next)
    {
        // For API routes, we'll use different methods to resolve tenant
        $tenant = $this->resolveTenantForApi($request);
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context required',
                'message' => 'No valid tenant context found for this request'
            ], 400);
        }

        // Set tenant context
        app()->instance('current_tenant', $tenant);
        config(['app.current_tenant' => $tenant]);

        return $next($request);
    }

    /**
     * Resolve tenant for API requests
     */
    private function resolveTenantForApi(Request $request)
    {
        // Method 1: From X-Tenant-ID header
        if ($request->hasHeader('X-Tenant-ID')) {
            $tenantId = $request->header('X-Tenant-ID');
            $tenant = Company::find($tenantId);
            if ($tenant && $tenant->status === 'active') {
                return $tenant;
            }
        }

        // Method 2: From domain/subdomain
        $host = $request->getHost();
        $tenant = Company::where('domain', $host)
                        ->where('status', 'active')
                        ->first();
        if ($tenant) {
            return $tenant;
        }

        // Method 3: From authenticated user's company (for API tokens)
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            if ($user->company_id) {
                return Company::find($user->company_id);
            }
        }

        // Method 4: From Bearer token with embedded tenant info
        $token = $request->bearerToken();
        if ($token) {
            // You can implement custom token parsing logic here
            // For example, if your tokens contain tenant information
            $tenant = $this->extractTenantFromToken($token);
            if ($tenant) {
                return $tenant;
            }
        }

        return null;
    }

    /**
     * Extract tenant information from custom token format
     */
    private function extractTenantFromToken($token)
    {
        // Implement your custom token parsing logic here
        // This is just an example - adapt to your token format
        
        try {
            // Example: if your token format includes tenant ID
            // You might use JWT or custom token format
            $parts = explode('.', $token);
            if (count($parts) >= 2) {
                $payload = json_decode(base64_decode($parts[1]), true);
                if (isset($payload['tenant_id'])) {
                    return Company::find($payload['tenant_id']);
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
            \Log::warning('Failed to extract tenant from token', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
