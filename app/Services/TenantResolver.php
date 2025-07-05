<?php

namespace App\Services;

use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantResolver
{
    /**
     * Resolve tenant from domain
     */
    public function resolveFromDomain(string $domain): ?Company
    {
        return Cache::remember("tenant.domain.{$domain}", 300, function () use ($domain) {
            return Company::where('domain', $domain)
                         ->where('status', 'active')
                         ->first();
        });
    }

    /**
     * Resolve tenant from subdomain
     */
    public function resolveFromSubdomain(string $subdomain): ?Company
    {
        return Cache::remember("tenant.subdomain.{$subdomain}", 300, function () use ($subdomain) {
            return Company::where('slug', $subdomain)
                         ->where('status', 'active')
                         ->first();
        });
    }

    /**
     * Resolve tenant from request
     */
    public function resolveFromRequest(Request $request): ?Company
    {
        $host = $request->getHost();
        
        // First try exact domain match
        $tenant = $this->resolveFromDomain($host);
        
        if (!$tenant) {
            // Try subdomain match
            $hostParts = explode('.', $host);
            if (count($hostParts) >= 2) {
                $subdomain = $hostParts[0];
                $tenant = $this->resolveFromSubdomain($subdomain);
            }
        }
        
        return $tenant;
    }

    /**
     * Get current tenant ID from various sources
     */
    public function getCurrentTenantId(): ?int
    {
        // Priority order: explicit tenant, session, app instance, auth user
        
        // 1. Check if explicitly set in app (from middleware)
        if (app()->has('current_tenant')) {
            return app('current_tenant')->id;
        }
        
        // 2. Check session (for admin panel)
        if (session()->has('selected_company_id')) {
            return session('selected_company_id');
        }
        
        // 3. Check authenticated user's company
        if (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }
        
        // 4. Check config
        if (config('app.current_tenant.id')) {
            return config('app.current_tenant.id');
        }
        
        return null;
    }

    /**
     * Get current tenant
     */
    public function getCurrentTenant(): ?Company
    {
        $tenantId = $this->getCurrentTenantId();
        
        if (!$tenantId) {
            return null;
        }

        return Cache::remember("tenant.{$tenantId}", 300, function () use ($tenantId) {
            return Company::find($tenantId);
        });
    }

    /**
     * Set current tenant
     */
    public function setCurrentTenant(Company $tenant): void
    {
        app()->instance('current_tenant', $tenant);
        config(['app.current_tenant' => $tenant]);
    }

    /**
     * Clear current tenant
     */
    public function clearCurrentTenant(): void
    {
        app()->forgetInstance('current_tenant');
        config(['app.current_tenant' => null]);
    }

    /**
     * Check if tenant is accessible
     */
    public function isTenantAccessible(Company $tenant): bool
    {
        // Check if company is active
        if ($tenant->status !== 'active') {
            return false;
        }
        
        // Check trial period
        if ($tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
            // Trial ended, check subscription
            if (!$tenant->subscription_ends_at || $tenant->subscription_ends_at->isPast()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get tenant configuration
     */
    public function getTenantConfig(Company $tenant): array
    {
        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'domain' => $tenant->domain,
            'slug' => $tenant->slug,
            'theme' => $tenant->theme ? $tenant->theme->slug : 'default',
            'theme_config' => $tenant->theme ? $tenant->theme->config : [],
            'settings' => $tenant->settings ?? [],
        ];
    }
}
