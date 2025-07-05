<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMiddlewareEnhanced
{
    public function handle(Request $request, Closure $next)
    {
        // Get current domain
        $host = $request->getHost();
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('TenantMiddleware Debug', [
            'host' => $host,
            'url' => $request->url(),
            'path' => $request->path()
        ]);
        
        // Find company by custom domain only (no subdomain logic)
        $company = Company::where('domain', $host)
                         ->where('status', 'active')
                         ->first();
        
        \Illuminate\Support\Facades\Log::info('Company Resolution', [
            'company_found' => !is_null($company),
            'company_name' => $company ? $company->name : 'null',
            'company_domain' => $company ? $company->domain : 'null'
        ]);
        
        if (!$company) {
            // If no company found, redirect to main SaaS landing page
            \Illuminate\Support\Facades\Log::warning('No company found for domain: ' . $host);
            return redirect()->to(config('app.main_url', 'http://localhost:8000'));
        }
        
        // Check if company is active and subscription is valid
        if (!$this->isCompanyAccessible($company)) {
            return view('tenant.suspended', compact('company'));
        }
        
        // Set current tenant in multiple ways for maximum compatibility
        app()->instance('current_tenant', $company);
        Config::set('app.current_tenant', $company);
        
        // Share with all views
        view()->share('currentTenant', $company);
        
        // Apply enhanced tenant scope to all queries
        $this->applyEnhancedTenantScope($company);
        
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
    
    private function applyEnhancedTenantScope(Company $company)
    {
        // Enhanced list of models with tenant isolation
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
                // Only add global scope if the model uses the BelongsToTenantEnhanced trait
                if (method_exists($model, 'bootBelongsToTenantEnhanced')) {
                    // The trait will handle the global scope automatically
                    continue;
                }
                
                // Fallback for models not using the enhanced trait
                $model::addGlobalScope('tenant', function ($builder) use ($company) {
                    $builder->where($builder->getModel()->getTable() . '.company_id', $company->id);
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
