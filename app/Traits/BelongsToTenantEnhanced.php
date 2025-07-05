<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenantEnhanced
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToTenantEnhanced()
    {
        // Automatically set company_id when creating
        static::creating(function (Model $model) {
            if (empty($model->company_id)) {
                $model->company_id = static::getCurrentCompanyId();
            }
        });

        // Add global scope to filter by current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (static::shouldApplyTenantScope()) {
                $builder->where($builder->getModel()->getTable() . '.company_id', static::getCurrentCompanyId());
            }
        });
    }

    /**
     * Scope a query to the current tenant
     */
    public function scopeCurrentTenant(Builder $query)
    {
        return $query->where($this->getTable() . '.company_id', static::getCurrentCompanyId());
    }

    /**
     * Scope a query to a specific tenant
     */
    public function scopeForTenant(Builder $query, $companyId)
    {
        return $query->where($this->getTable() . '.company_id', $companyId);
    }

    /**
     * Get the current company ID
     */
    public static function getCurrentCompanyId()
    {
        // Try to get from current tenant context (set by TenantMiddleware)
        if (app()->has('current_tenant')) {
            return app('current_tenant')->id;
        }
        
        // Try to get from config (alternative tenant setting)
        if (config('app.current_tenant')) {
            return config('app.current_tenant')->id;
        }
        
        // Try to get from session first (for multi-tenant admin)
        if (session()->has('selected_company_id')) {
            return session('selected_company_id');
        }

        // Fall back to authenticated user's company
        if (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }

        // If no company context, return null (will be handled by validation)
        return null;
    }

    /**
     * Determine if tenant scope should be applied
     */
    protected static function shouldApplyTenantScope()
    {
        // Don't apply scope if we're in console (for seeding, etc.)
        if (app()->runningInConsole()) {
            return false;
        }

        // Don't apply scope if explicitly disabled
        if (static::getTenantScopeDisabled()) {
            return false;
        }

        // Check if we have tenant context
        $currentCompanyId = static::getCurrentCompanyId();
        
        // Don't apply scope if no current company
        if (!$currentCompanyId) {
            return false;
        }

        return true;
    }

    /**
     * Check if tenant scope is disabled
     */
    protected static function getTenantScopeDisabled()
    {
        return request()->header('X-Disable-Tenant-Scope') === 'true' ||
               session('disable_tenant_scope') === true;
    }

    /**
     * Disable tenant scope for this request
     */
    public static function withoutTenantScope($callback)
    {
        session(['disable_tenant_scope' => true]);
        
        try {
            return $callback();
        } finally {
            session()->forget('disable_tenant_scope');
        }
    }

    /**
     * Create a model for a specific tenant
     */
    public static function createForTenant($companyId, array $attributes = [])
    {
        $attributes['company_id'] = $companyId;
        return static::create($attributes);
    }

    /**
     * Get all tenants that have this model
     */
    public static function getTenantsWithModel()
    {
        return static::withoutTenantScope(function () {
            return static::distinct('company_id')
                        ->whereNotNull('company_id')
                        ->pluck('company_id');
        });
    }

    /**
     * Get count of records per tenant
     */
    public static function getCountPerTenant()
    {
        return static::withoutTenantScope(function () {
            return static::groupBy('company_id')
                        ->selectRaw('company_id, count(*) as count')
                        ->pluck('count', 'company_id');
        });
    }

    /**
     * Check if model belongs to current tenant
     */
    public function belongsToCurrentTenant()
    {
        return $this->company_id === static::getCurrentCompanyId();
    }

    /**
     * Check if model belongs to specific tenant
     */
    public function belongsToTenant($companyId)
    {
        return $this->company_id == $companyId;
    }

    /**
     * Ensure model belongs to current tenant
     */
    public function ensureBelongsToCurrentTenant()
    {
        if (!$this->belongsToCurrentTenant()) {
            abort(404, 'Resource not found.');
        }

        return $this;
    }

    /**
     * Get the tenant (company) relationship
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\SuperAdmin\Company::class, 'company_id');
    }

    /**
     * Get company relationship (alias for tenant)
     */
    public function company()
    {
        return $this->tenant();
    }

    /**
     * Move model to different tenant
     */
    public function moveToTenant($companyId)
    {
        $this->company_id = $companyId;
        return $this->save();
    }

    /**
     * Duplicate model for different tenant
     */
    public function duplicateForTenant($companyId, array $overrides = [])
    {
        $attributes = $this->getAttributes();
        
        // Remove unique identifiers
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
        
        // Set new tenant
        $attributes['company_id'] = $companyId;
        
        // Apply overrides
        $attributes = array_merge($attributes, $overrides);
        
        return static::create($attributes);
    }

    /**
     * Get models for all tenants (admin function)
     */
    public static function forAllTenants()
    {
        return static::withoutTenantScope(function () {
            return static::all();
        });
    }

    /**
     * Get models for specific tenants
     */
    public static function forTenants(array $companyIds)
    {
        return static::withoutTenantScope(function () use ($companyIds) {
            return static::whereIn('company_id', $companyIds)->get();
        });
    }

    /**
     * Validate tenant context
     */
    protected function validateTenantContext()
    {
        if (!static::getCurrentCompanyId()) {
            throw new \Exception('No tenant context available. Please ensure you are authenticated and have a company assigned.');
        }
    }

    /**
     * Boot method called when trait is used
     */
    public function initializeBelongsToTenantEnhanced()
    {
        // Add company_id to fillable if not already there
        if (!in_array('company_id', $this->fillable)) {
            $this->fillable[] = 'company_id';
        }
    }
}
