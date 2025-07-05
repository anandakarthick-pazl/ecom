<?php

namespace App\Traits;

use App\Models\SuperAdmin\Company;
use Illuminate\Database\Eloquent\Builder;

trait HasTenantQueries
{
    /**
     * Boot the trait - ensure company_id is in fillable
     */
    public function initializeHasTenantQueries()
    {
        if (!in_array('company_id', $this->fillable)) {
            $this->fillable[] = 'company_id';
        }
    }

    /**
     * Scope a query to the current tenant
     */
    public function scopeCurrentTenant(Builder $query)
    {
        return $query->currentTenant();
    }

    /**
     * Scope a query to a specific tenant
     */
    public function scopeForTenant(Builder $query, $companyId)
    {
        return $query->forTenant($companyId);
    }

    /**
     * Get the current tenant company
     */
    public static function getCurrentTenantCompany()
    {
        return app('tenant');
    }

    /**
     * Get the current company ID
     */
    public static function getCurrentTenantId()
    {
        $tenant = static::getCurrentTenantCompany();
        return $tenant ? $tenant->id : null;
    }

    /**
     * Create a model for the current tenant
     */
    public static function createForCurrentTenant(array $attributes = [])
    {
        $tenantId = static::getCurrentTenantId();
        
        if (!$tenantId) {
            throw new \Exception('No tenant context available for creating model.');
        }
        
        $attributes['company_id'] = $tenantId;
        return static::create($attributes);
    }

    /**
     * Check if this model belongs to the current tenant
     */
    public function belongsToCurrentTenant()
    {
        $currentTenantId = static::getCurrentTenantId();
        return $currentTenantId && $this->company_id == $currentTenantId;
    }

    /**
     * Ensure this model belongs to the current tenant
     */
    public function ensureBelongsToCurrentTenant()
    {
        if (!$this->belongsToCurrentTenant()) {
            abort(404, 'Resource not found.');
        }

        return $this;
    }

    /**
     * Get the tenant relationship
     */
    public function tenant()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get company relationship (alias for tenant)
     */
    public function company()
    {
        return $this->tenant();
    }
}
