<?php

namespace App\Traits;

use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;

trait HasCompanyContextEnhanced
{
    /**
     * Get the current company ID from session
     */
    protected function getCurrentCompanyId()
    {
        return session('selected_company_id');
    }
    
    /**
     * Get the current company model
     */
    protected function getCurrentCompany()
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            return null;
        }
        
        return Company::find($companyId);
    }
    
    /**
     * Ensure company context exists, redirect if not
     */
    protected function ensureCompanyContext()
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            return redirect()->route('login')->withErrors([
                'error' => 'Company context not found. Please login again.'
            ]);
        }
        
        return $companyId;
    }
    
    /**
     * Add company_id to data array
     */
    protected function addCompanyIdToData(array $data)
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            throw new \Exception('Company context not found');
        }
        
        $data['company_id'] = $companyId;
        return $data;
    }
    
    /**
     * Validate that a model belongs to the current company
     */
    protected function validateModelOwnership($model)
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            abort(403, 'Company context not found');
        }
        
        if ($model->company_id != $companyId) {
            abort(404, 'Resource not found');
        }
        
        return true;
    }
    
    /**
     * Apply company filter to query
     */
    protected function applyCompanyFilter($query)
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            throw new \Exception('Company context not found');
        }
        
        return $query->where('company_id', $companyId);
    }
    
    /**
     * Get filtered query for current company
     */
    protected function getCompanyQuery($modelClass)
    {
        $companyId = $this->getCurrentCompanyId();
        
        if (!$companyId) {
            throw new \Exception('Company context not found');
        }
        
        return $modelClass::where('company_id', $companyId);
    }
    
    /**
     * Validate request data with company context
     */
    protected function validateWithCompanyContext(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        // Ensure company context
        $companyId = $this->ensureCompanyContext();
        
        if ($companyId instanceof \Illuminate\Http\RedirectResponse) {
            return $companyId;
        }
        
        // Add company-specific validation rules if needed
        $rules = $this->addCompanyValidationRules($rules);
        
        return $request->validate($rules, $messages, $customAttributes);
    }
    
    /**
     * Add company-specific validation rules
     */
    protected function addCompanyValidationRules(array $rules)
    {
        $companyId = $this->getCurrentCompanyId();
        
        // Add company scoping to exists rules
        foreach ($rules as $field => $rule) {
            if (is_string($rule) && strpos($rule, 'exists:') === 0) {
                $rules[$field] = $rule . ',company_id,' . $companyId;
            } elseif (is_array($rule)) {
                foreach ($rule as $key => $singleRule) {
                    if (is_string($singleRule) && strpos($singleRule, 'exists:') === 0) {
                        $rules[$field][$key] = $singleRule . ',company_id,' . $companyId;
                    }
                }
            }
        }
        
        return $rules;
    }
    
    /**
     * Create model with automatic company context
     */
    protected function createWithCompanyContext($modelClass, array $data)
    {
        $data = $this->addCompanyIdToData($data);
        return $modelClass::create($data);
    }
    
    /**
     * Update model with company validation
     */
    protected function updateWithCompanyContext($model, array $data)
    {
        $this->validateModelOwnership($model);
        return $model->update($data);
    }
    
    /**
     * Delete model with company validation
     */
    protected function deleteWithCompanyContext($model)
    {
        $this->validateModelOwnership($model);
        return $model->delete();
    }
    
    /**
     * Get paginated results for current company
     */
    protected function getPaginatedForCompany($modelClass, $perPage = 15, $columns = ['*'])
    {
        return $this->getCompanyQuery($modelClass)->paginate($perPage, $columns);
    }
    
    /**
     * Search and paginate for current company
     */
    protected function searchAndPaginateForCompany($modelClass, $searchTerm, $searchFields, $perPage = 15)
    {
        $query = $this->getCompanyQuery($modelClass);
        
        if ($searchTerm) {
            $query->where(function ($q) use ($searchFields, $searchTerm) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
                }
            });
        }
        
        return $query->paginate($perPage);
    }
}
