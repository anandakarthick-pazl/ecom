<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EnhancedSessionManagement
{
    /**
     * Handle an incoming request with enhanced session management
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for certain routes
        $skipRoutes = [
            'login', 'logout', 'register', 'password/*', 'debug/*', 
            'auth/check', 'super-admin/login', '_debugbar/*'
        ];
        
        foreach ($skipRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Check if session exists and is valid
        if (!$request->hasSession()) {
            Log::warning('No session found for request', [
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);
        }

        // For authenticated users, verify session integrity
        if (Auth::check()) {
            $user = Auth::user();
            $sessionCompanyId = session('selected_company_id');
            
            // For admin routes, ensure company context is maintained
            if ($request->is('admin/*')) {
                if (!$sessionCompanyId) {
                    Log::warning('Admin route accessed without company context', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'url' => $request->url()
                    ]);
                    
                    // Clear auth and redirect to login
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')->withErrors([
                        'error' => 'Session expired. Please login again.'
                    ]);
                }
                
                // Verify user still has access to the company
                if (!$user->isSuperAdmin() && (int)$user->company_id !== (int)$sessionCompanyId) {
                    Log::warning('User company mismatch detected', [
                        'user_id' => $user->id,
                        'user_company_id' => $user->company_id,
                        'session_company_id' => $sessionCompanyId
                    ]);
                    
                    Auth::logout();
                    $request->session()->invalidate();
                    
                    return redirect()->route('login')->withErrors([
                        'error' => 'Company access revoked. Please login again.'
                    ]);
                }
            }
            
            // Update session activity timestamp
            session(['last_activity' => time()]);
        }

        // Set session configuration for this request
        $this->configureSession($request);

        $response = $next($request);

        // Add session debugging headers in development
        if (config('app.debug') && $request->hasSession()) {
            $response->headers->set('X-Session-ID', session()->getId());
            $response->headers->set('X-Auth-Status', Auth::check() ? 'authenticated' : 'guest');
            
            if (Auth::check()) {
                $response->headers->set('X-User-ID', Auth::id());
                $response->headers->set('X-Company-ID', session('selected_company_id', 'none'));
            }
        }

        return $response;
    }

    /**
     * Configure session settings based on request context
     */
    private function configureSession(Request $request)
    {
        $host = $request->getHost();
        
        // For localhost, use default config
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return;
        }
        
        // For tenant domains, ensure session is scoped appropriately
        if (str_contains($host, '.local')) {
            // Set session name based on tenant domain for isolation
            $sessionName = 'tenant_' . str_replace(['.', ':'], '_', $host) . '_session';
            config(['session.cookie' => $sessionName]);
        }
    }
}
