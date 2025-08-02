<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MainDomainMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures the request is from the main domain (localhost)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Production and development domains
        $mainDomains = [
            'rrkcrackers.com',
            'www.rrkcrackers.com',
            'localhost',
            '127.0.0.1'
        ];
        
        // Check if this is the main domain
        if (!in_array($host, $mainDomains) && !str_contains($host, 'localhost')) {
            // If not main domain, redirect to root which will handle tenant routing
            return redirect('/');
        }
        
        return $next($request);
    }
}
