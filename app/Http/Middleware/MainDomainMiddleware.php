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
        
        // Check if this is the main domain
        if ($host !== 'localhost' && $host !== '127.0.0.1' && !str_contains($host, 'localhost')) {
            // If not main domain, redirect to root which will handle tenant routing
            return redirect('/');
        }
        
        return $next($request);
    }
}
