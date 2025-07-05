<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\SuperAdmin\Company;
use App\Services\ThemeService;

class ApplyTheme
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get current company based on domain or subdomain
        $company = $this->getCurrentCompany($request);
        
        if ($company) {
            // Get company theme
            $theme = $this->themeService->getCompanyTheme($company);
            
            if ($theme) {
                // Generate theme CSS
                $themeCSS = $this->themeService->generateCustomizedCSS($company);
                
                // Get theme variables
                $themeVariables = $this->themeService->getThemeVariables($theme);
                
                // Get theme customizations
                $customizations = $this->themeService->getCompanyCustomizations($company);
                
                // Share theme data with all views
                View::share('currentTheme', $theme);
                View::share('themeCSS', $themeCSS);
                View::share('themeVariables', $themeVariables);
                View::share('themeCustomizations', $customizations);
                View::share('currentCompany', $company);
                
                // Add theme class to body
                $themeClass = 'theme-' . $theme->slug;
                View::share('themeClass', $themeClass);
            }
        }
        
        return $next($request);
    }

    /**
     * Get current company based on request
     */
    private function getCurrentCompany(Request $request): ?Company
    {
        $host = $request->getHost();
        
        // Cache company lookup for performance
        return Cache::remember("company_by_domain_{$host}", 3600, function () use ($host) {
            // Try to find company by domain
            $company = Company::where('domain', $host)->first();
            
            if (!$company) {
                // Try to find by subdomain
                $subdomain = explode('.', $host)[0];
                $company = Company::where('slug', $subdomain)->first();
            }
            
            return $company;
        });
    }
}
