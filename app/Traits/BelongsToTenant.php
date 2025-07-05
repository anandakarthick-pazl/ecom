<?php

namespace App\Traits;

use App\Models\SuperAdmin\Company;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // Automatically set company_id when creating
        static::creating(function ($model) {
            if (!$model->company_id && app()->has('current_tenant')) {
                $model->company_id = app('current_tenant')->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForTenant(Builder $query, $companyId = null)
    {
        $companyId = $companyId ?: (app()->has('current_tenant') ? app('current_tenant')->id : null);
        
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        
        return $query;
    }

    public function scopeCurrentTenant(Builder $query)
    {
        if (app()->has('current_tenant')) {
            return $query->where('company_id', app('current_tenant')->id);
        }
        
        return $query;
    }
}
