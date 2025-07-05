<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Don't redirect JSON requests
        if ($request->expectsJson()) {
            return null;
        }
        
        $host = $request->getHost();
        $path = $request->path();
        
        Log::info('Authentication required - redirecting to login', [
            'host' => $host,
            'path' => $path,
            'url' => $request->url(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Always redirect to the universal login route
        // The login route will handle domain-specific logic
        return route('login');
    }
    
    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        Log::warning('User session expired or unauthenticated access attempted', [
            'host' => $request->getHost(),
            'path' => $request->path(),
            'guards' => $guards,
            'session_id' => $request->session()->getId(),
            'ip' => $request->ip()
        ]);
        
        // Clear any stale session data
        $request->session()->forget([
            'selected_company_id',
            'selected_company_slug', 
            'selected_company_name',
            'selected_company_domain',
            'acting_as_company_admin',
            'original_user_company_id'
        ]);
        
        // Call parent method to handle the redirect
        parent::unauthenticated($request, $guards);
    }
}
