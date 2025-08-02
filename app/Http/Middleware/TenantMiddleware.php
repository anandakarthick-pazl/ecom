<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip tenant checking for certain routes
        $excludedPaths = [
            'logout', 
            'register',
            'contact',
            'features',
            'pricing',
            'landing-home',
            'landing-pricing',
            'landing-features',
            'landing-contact',
            'success/*',
            'check-slug',
            'super-admin/*',
            'auth/*',
            'debug/*'
        ];
        
        foreach ($excludedPaths as $path) {
            if ($request->is($path)) {
                Log::info('TenantMiddleware: Skipping excluded path', [
                    'path' => $request->path(),
                    'excluded_pattern' => $path
                ]);
                return $next($request);
            }
        }
        
        // Get current domain
        $host = $request->getHost();
        
        // Debug logging
        Log::info('TenantMiddleware Debug', [
            'host' => $host,
            'url' => $request->url(),
            'path' => $request->path(),
            'method' => $request->method(),
            'is_login_route' => $request->is('login'),
            'is_admin_route' => $request->is('admin/*')
        ]);
        
        // Main domains - skip tenant resolution
        $mainDomains = [
            'rrkcrackers.com',
            'www.rrkcrackers.com',
            'localhost',
            '127.0.0.1'
        ];
        
        if (in_array($host, $mainDomains) || str_contains($host, 'localhost')) {
            Log::info('TenantMiddleware: Main domain access - skipping tenant resolution');
            return $next($request);
        }
        
        // Find company by custom domain only (no subdomain logic)
        $company = Company::where('domain', $host)
                         ->where('status', 'active')
                         ->first();
        
        Log::info('Company Resolution', [
            'company_found' => !is_null($company),
            'company_name' => $company ? $company->name : 'null',
            'company_domain' => $company ? $company->domain : 'null',
            'company_id' => $company ? $company->id : 'null',
            'company_status' => $company ? $company->status : 'null'
        ]);
        
        if (!$company) {
            Log::warning('No company found for domain: ' . $host);
            
            // For login routes, allow access and let auth controller handle the error
            if ($request->is('login') || $request->method() === 'POST' && $request->is('login')) {
                Log::info('TenantMiddleware: Allowing login route without company');
                return $next($request);
            }
            
            // For other routes, redirect to main domain
            return redirect()->to(config('app.main_url', 'https://rrkcrackers.com'));
        }
        
        // Check if company is active and subscription is valid
        if (!$this->isCompanyAccessible($company)) {
            Log::warning('Company not accessible', [
                'company_id' => $company->id,
                'company_status' => $company->status
            ]);
            return view('tenant.suspended', compact('company'));
        }
        
        // Set current tenant in multiple places for compatibility
        app()->instance('current_tenant', $company);
        Config::set('app.current_tenant', $company);
        
        Log::info('TenantMiddleware: Tenant context set', [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'app_tenant_set' => app()->has('current_tenant')
        ]);
        
        // Apply tenant scope to all queries
        $this->applyTenantScope($company);
        
        // Set theme configuration
        $this->setThemeConfiguration($company);
        
        return $next($request);
    }
    
    private function isCompanyAccessible(Company $company)
    {
        // Check if company is active
        if ($company->status !== 'active') {
            return false;
        }
        
        // Check trial period
        if ($company->trial_ends_at && $company->trial_ends_at->isPast()) {
            // Trial ended, check subscription
            if (!$company->subscription_ends_at || $company->subscription_ends_at->isPast()) {
                // No valid subscription
                $company->update(['status' => 'suspended']);
                return false;
            }
        }
        
        return true;
    }
    
    private function applyTenantScope(Company $company)
    {
        // Add global scope for tenant isolation
        $models = [
            \App\Models\Category::class,
            \App\Models\Product::class,
            \App\Models\Customer::class,
            \App\Models\Order::class,
            \App\Models\OrderItem::class,
            \App\Models\Banner::class,
            \App\Models\Offer::class,
            \App\Models\Cart::class,
            \App\Models\Supplier::class,
            \App\Models\PurchaseOrder::class,
            \App\Models\PurchaseOrderItem::class,
            \App\Models\Estimate::class,
            \App\Models\EstimateItem::class,
            \App\Models\GoodsReceiptNote::class,
            \App\Models\GrnItem::class,
            \App\Models\StockAdjustment::class,
            \App\Models\StockAdjustmentItem::class,
            \App\Models\PosSale::class,
            \App\Models\PosSaleItem::class,
            \App\Models\AppSetting::class,
            \App\Models\Notification::class,
        ];
        
        foreach ($models as $model) {
            if (class_exists($model)) {
                $model::addGlobalScope('tenant', function ($builder) use ($company) {
                    $builder->where('company_id', $company->id);
                });
            }
        }
    }
    
    private function setThemeConfiguration(Company $company)
    {
        if ($company->theme) {
            Config::set('app.theme', $company->theme->slug);
            Config::set('app.theme_config', $company->theme->config ?? []);
        }
    }
}
