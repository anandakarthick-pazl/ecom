<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AdaptiveNavbarService;

class AdaptiveNavbar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only apply to HTML responses
        if ($response->headers->get('Content-Type') && 
            strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
            
            $company = $this->getCompanyData($request);
            
            if ($company) {
                $navbarConfig = AdaptiveNavbarService::getJSConfig($company);
                $inlineCSS = AdaptiveNavbarService::generateInlineCSS($company);
                
                // Inject configuration and styles into the response
                $content = $response->getContent();
                
                // Add CSS before closing head tag
                $cssInjection = "<style id='adaptive-navbar-css'>$inlineCSS</style>\n</head>";
                $content = str_replace('</head>', $cssInjection, $content);
                
                // Add JavaScript configuration before closing body tag
                $jsConfig = json_encode($navbarConfig);
                $jsInjection = "
                <script>
                    window.adaptiveNavbarConfig = $jsConfig;
                    
                    // Apply initial configuration
                    document.addEventListener('DOMContentLoaded', function() {
                        const navbar = document.getElementById('adaptive-navbar');
                        if (navbar && window.adaptiveNavbarConfig) {
                            const config = window.adaptiveNavbarConfig;
                            
                            // Apply size class immediately
                            if (config.sizeClass !== 'normal') {
                                navbar.classList.add(config.sizeClass);
                            }
                            
                            // Store config for runtime access
                            navbar.dataset.config = JSON.stringify(config);
                        }
                    });
                </script>
                </body>";
                $content = str_replace('</body>', $jsInjection, $content);
                
                $response->setContent($content);
            }
        }
        
        return $response;
    }
    
    /**
     * Get company data for the current request
     * 
     * @param Request $request
     * @return object|null
     */
    private function getCompanyData(Request $request)
    {
        // Try to get company data from view composer or session
        if ($request->session()->has('globalCompany')) {
            return $request->session()->get('globalCompany');
        }
        
        // Fallback to database query
        try {
            $companyModel = app()->make('App\\Models\\Company');
            return $companyModel::first();
        } catch (\Exception $e) {
            // Fallback to basic data
            return (object)[
                'company_name' => config('app.name', 'Your Store'),
                'company_logo' => null
            ];
        }
    }
}
