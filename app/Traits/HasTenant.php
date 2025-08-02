<?php

namespace App\Traits;

use App\Models\SuperAdmin\Company;
use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Boot the trait - ensure company_id is in fillable and auto-set on creation
     */
    public function initializeHasTenant()
    {
        // Ensure company_id is fillable
        if (!in_array('company_id', $this->fillable)) {
            $this->fillable[] = 'company_id';
        }
    }

    /**
     * Boot method for the trait
     */
    protected static function bootHasTenant()
    {
        // Auto-set company_id when creating new models
        static::creating(function ($model) {
            if (!$model->company_id) {
                $model->company_id = static::getCurrentTenantId();
            }
        });

        // Apply global scope for tenant filtering
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = static::getCurrentTenantId();
            if ($tenantId) {
                $builder->where($builder->getModel()->getTable() . '.company_id', $tenantId);
            }
        });
    }

    /**
     * Scope a query to the current tenant
     */
    public function scopeCurrentTenant(Builder $query)
    {
        $tenantId = static::getCurrentTenantId();
        if ($tenantId) {
            return $query->where('company_id', $tenantId);
        }
        return $query;
    }

    /**
     * Scope a query to a specific tenant
     */
    public function scopeForTenant(Builder $query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the current tenant company
     */
    public static function getCurrentTenantCompany()
    {
        // Try to get from session first (web requests)
        if (session()->has('selected_company_id')) {
            return Company::find(session('selected_company_id'));
        }

        // Try to get from app binding (API or other contexts)
        try {
            return app('tenant');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the current company ID
     */
    public static function getCurrentTenantId()
    {
        // Try to get from session first (web requests)
        if (session()->has('selected_company_id')) {
            return session('selected_company_id');
        }

        // Try to get from app binding (API or other contexts)
        try {
            $tenant = app('tenant');
            return $tenant ? $tenant->id : null;
        } catch (\Exception $e) {
            return null;
        }
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

    /**
     * Disable global tenant scope temporarily
     */
    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope('tenant');
    }

    /**
     * Get all records regardless of tenant
     */
    public static function allTenants()
    {
        return static::withoutTenantScope();
    }
}
