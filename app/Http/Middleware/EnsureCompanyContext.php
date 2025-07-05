<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Log;

class EnsureCompanyContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to admin routes
        if (!$request->is('admin/*')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            // Enhanced authentication check
            $host = $request->getHost();
            
            Log::warning('EnsureCompanyContext: User not authenticated', [
                'url' => $request->url(),
                'host' => $host,
                'session_id' => session()->getId()
            ]);
            
            // Clear any stale session data
            session()->flush();
            
            return redirect()->route('login')->withErrors([
                'error' => 'Please login to access the admin panel.'
            ]);
        }

        $user = auth()->user();

        // Super admin accessing super-admin routes should not be affected
        if ($request->is('super-admin/*') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // For admin routes, ensure company context is set
        if ($request->is('admin/*')) {
            $selectedCompanyId = session('selected_company_id');
            
            Log::info('EnsureCompanyContext: Admin route accessed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'selected_company_id' => $selectedCompanyId,
                'session_data' => session()->all(),
                'url' => $request->url()
            ]);
            
            // If no company selected in session, redirect to appropriate login
            if (!$selectedCompanyId) {
                Log::warning('EnsureCompanyContext: No company context in session', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'session_id' => session()->getId(),
                    'all_session_data' => session()->all()
                ]);
                
                auth()->logout();
                session()->flush();
                
                // Check domain to determine redirect
                $host = $request->getHost();
                if ($host === 'localhost' || $host === '127.0.0.1') {
                    return redirect()->route('login')->withErrors([
                        'error' => 'Session expired. Please login again to access the admin panel.'
                    ]);
                } else {
                    // For tenant domains, redirect to login
                    return redirect()->route('login')->withErrors([
                        'error' => 'Session expired. Please login again to access the admin panel.'
                    ]);
                }
            }

            // Verify the company still exists and is active
            $company = Company::where('id', $selectedCompanyId)
                             ->where('status', 'active')
                             ->first();
            
            if (!$company) {
                auth()->logout();
                session()->forget(['selected_company_id', 'selected_company_slug', 'selected_company_name', 'selected_company_domain']);
                return redirect()->route('login')->withErrors([
                    'error' => 'The selected company is no longer available.'
                ]);
            }

            // For non-super admin users, verify they belong to this company
            if (!$user->isSuperAdmin() && (int)$user->company_id !== (int)$company->id) {
                \Illuminate\Support\Facades\Log::warning('EnsureCompanyContext: Access denied', [
                    'user_id' => $user->id,
                    'user_company_id' => $user->company_id,
                    'user_company_id_type' => gettype($user->company_id),
                    'session_company_id' => $company->id,
                    'session_company_id_type' => gettype($company->id),
                    'int_comparison' => (int)$user->company_id === (int)$company->id
                ]);
                auth()->logout();
                return redirect()->route('login')->withErrors([
                    'error' => 'You do not have access to this company.'
                ]);
            }

            // Add company data to the request for easy access in controllers
            $request->merge([
                'current_company' => $company,
                'current_company_id' => $company->id
            ]);

            // Set a global variable for views
            view()->share('currentCompany', $company);
            
            // Set current tenant in app container for BelongsToTenant trait
            app()->instance('current_tenant', $company);
        }

        return $next($request);
    }
}
