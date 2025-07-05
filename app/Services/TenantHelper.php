<?php

namespace App\Services;

use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Config;

class TenantHelper
{
    /**
     * Get current tenant
     */
    public function current()
    {
        return app('tenant');
    }

    /**
     * Get current tenant ID
     */
    public function currentId()
    {
        return $this->current()?->id;
    }

    /**
     * Check if current user is super admin
     */
    public function isSuperAdmin()
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    /**
     * Set tenant context programmatically
     */
    public function setContext(Company $company)
    {
        app()->instance('current_tenant', $company);
        Config::set('app.current_tenant', $company);
        view()->share('currentTenant', $company);
        
        return $this;
    }

    /**
     * Clear tenant context
     */
    public function clearContext()
    {
        app()->forgetInstance('current_tenant');
        Config::set('app.current_tenant', null);
        
        return $this;
    }

    /**
     * Execute callback with specific tenant context
     */
    public function withTenant(Company $company, callable $callback)
    {
        $originalTenant = $this->current();
        
        try {
            $this->setContext($company);
            return $callback();
        } finally {
            if ($originalTenant) {
                $this->setContext($originalTenant);
            } else {
                $this->clearContext();
            }
        }
    }

    /**
     * Execute callback without tenant context (for super admin operations)
     */
    public function withoutTenant(callable $callback)
    {
        $originalTenant = $this->current();
        
        try {
            $this->clearContext();
            return $callback();
        } finally {
            if ($originalTenant) {
                $this->setContext($originalTenant);
            }
        }
    }

    /**
     * Get tenant-aware database connection name
     */
    public function getConnectionName()
    {
        // If you need tenant-specific database connections
        // For now, return default connection
        return config('database.default');
    }

    /**
     * Check if tenant has feature access
     */
    public function hasFeature($feature)
    {
        $tenant = $this->current();
        
        if (!$tenant) {
            return false;
        }

        // Check against tenant's package/subscription
        return $tenant->package?->features[$feature] ?? false;
    }

    /**
     * Get tenant setting
     */
    public function getSetting($key, $default = null)
    {
        return \App\Models\AppSetting::get($key, $default);
    }

    /**
     * Set tenant setting
     */
    public function setSetting($key, $value, $type = 'string', $group = 'general')
    {
        return \App\Models\AppSetting::set($key, $value, $type, $group);
    }

    /**
     * Validate tenant ownership of model
     */
    public function validateOwnership($model)
    {
        if (!$this->isSuperAdmin() && 
            $this->currentId() && 
            $model->company_id !== $this->currentId()) {
            throw new \Exception('Access denied: Resource belongs to different tenant');
        }
    }

    /**
     * Get tenant storage path
     */
    public function getStoragePath($path = '')
    {
        $tenantId = $this->currentId();
        $basePath = storage_path("app/tenants/{$tenantId}");
        
        if (!file_exists($basePath)) {
            mkdir($basePath, 0755, true);
        }
        
        return $path ? "{$basePath}/{$path}" : $basePath;
    }

    /**
     * Get tenant public storage path
     */
    public function getPublicStoragePath($path = '')
    {
        $tenantId = $this->currentId();
        $basePath = public_path("storage/tenants/{$tenantId}");
        
        if (!file_exists($basePath)) {
            mkdir($basePath, 0755, true);
        }
        
        return $path ? "{$basePath}/{$path}" : $basePath;
    }

    /**
     * Get tenant URL for asset
     */
    public function asset($path)
    {
        $tenantId = $this->currentId();
        return asset("storage/tenants/{$tenantId}/{$path}");
    }

    /**
     * Switch to tenant context in admin panel
     */
    public function switchToTenant($companyId)
    {
        $company = Company::findOrFail($companyId);
        
        // Verify user has access to this company
        if (!$this->isSuperAdmin() && auth()->user()->company_id !== $company->id) {
            throw new \Exception('Access denied to this company');
        }
        
        session([
            'selected_company_id' => $company->id,
            'selected_company_slug' => $company->slug,
            'selected_company_name' => $company->name,
            'selected_company_domain' => $company->domain,
        ]);
        
        $this->setContext($company);
        
        return $company;
    }
}
