<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Register tenant resolver
        $this->app->singleton('tenant', function ($app) {
            return $this->resolveTenant();
        });

        // Register tenant helper
        $this->app->singleton('tenant.helper', function ($app) {
            return new \App\Services\TenantHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Add currentTenant query builder macro
        $this->addQueryBuilderMacros();
        
        // Share tenant with all views
        View::composer('*', function ($view) {
            $tenant = app('tenant');
            if ($tenant) {
                $view->with('currentTenant', $tenant);
                $view->with('tenant', $tenant); // Alias for convenience
            }
        });

        // Add custom validation rules for tenant-aware validation
        $this->addCustomValidationRules();
    }

    /**
     * Add query builder macros for tenant functionality
     */
    protected function addQueryBuilderMacros()
    {
        // Add currentTenant macro to Eloquent Builder
        Builder::macro('currentTenant', function () {
            $tenant = app('tenant');
            
            if (!$tenant) {
                // Try to resolve tenant using the same logic as in TenantServiceProvider
                if (app()->has('current_tenant')) {
                    $tenant = app('current_tenant');
                } elseif (session()->has('selected_company_id')) {
                    $tenant = Company::find(session('selected_company_id'));
                } elseif (auth()->check() && auth()->user()->company_id) {
                    $tenant = Company::find(auth()->user()->company_id);
                }
                
                if ($tenant) {
                    app()->instance('tenant', $tenant);
                }
            }
            
            if ($tenant) {
                return $this->where($this->getModel()->getTable() . '.company_id', $tenant->id);
            }
            
            // If no tenant context, return the query as-is or throw exception based on your needs
            // For safety, we'll return the unmodified query but you can change this behavior
            return $this;
        });
        
        // Add forTenant macro to Eloquent Builder
        Builder::macro('forTenant', function ($companyId) {
            return $this->where($this->getModel()->getTable() . '.company_id', $companyId);
        });
        
        // Add withoutTenantScope macro to Eloquent Builder
        Builder::macro('withoutTenantScope', function () {
            // This removes any tenant-related where clauses
            return $this->withoutGlobalScope('tenant');
        });
        
        // Add getCurrentTenant macro to Eloquent Builder
        Builder::macro('getCurrentTenant', function () {
            return app('tenant');
        });
        
        // Add ensureTenantContext macro to Eloquent Builder
        Builder::macro('ensureTenantContext', function () {
            $tenant = app('tenant');
            
            if (!$tenant) {
                throw new \Exception('No tenant context available. Please ensure you are authenticated and have a company assigned.');
            }
            
            return $this->currentTenant();
        });
    }

    /**
     * Resolve the current tenant
     */
    protected function resolveTenant()
    {
        // Priority order for tenant resolution
        
        // 1. From middleware (domain-based)
        if (app()->has('current_tenant')) {
            return app('current_tenant');
        }
        
        // 2. From session (admin panel)
        if (session()->has('selected_company_id')) {
            return Company::find(session('selected_company_id'));
        }
        
        // 3. From authenticated user
        if (auth()->check() && auth()->user()->company_id) {
            return Company::find(auth()->user()->company_id);
        }
        
        return null;
    }

    /**
     * Add custom validation rules for multi-tenant validation
     */
    protected function addCustomValidationRules()
    {
        // Tenant-aware exists rule
        \Illuminate\Support\Facades\Validator::extend('tenant_exists', function ($attribute, $value, $parameters, $validator) {
            $table = $parameters[0] ?? 'users';
            $column = $parameters[1] ?? 'id';
            
            $query = \Illuminate\Support\Facades\DB::table($table)->where($column, $value);
            
            $tenant = app('tenant');
            if ($tenant && !auth()->user()?->isSuperAdmin()) {
                $query->where('company_id', $tenant->id);
            }
            
            return $query->exists();
        });

        // Tenant-aware unique rule
        \Illuminate\Support\Facades\Validator::extend('tenant_unique', function ($attribute, $value, $parameters, $validator) {
            $table = $parameters[0] ?? 'users';
            $column = $parameters[1] ?? $attribute;
            $except = $parameters[2] ?? null;
            $idColumn = $parameters[3] ?? 'id';
            
            $query = \Illuminate\Support\Facades\DB::table($table)->where($column, $value);
            
            if ($except) {
                $query->where($idColumn, '!=', $except);
            }
            
            $tenant = app('tenant');
            if ($tenant && !auth()->user()?->isSuperAdmin()) {
                $query->where('company_id', $tenant->id);
            }
            
            return !$query->exists();
        });
    }
}
