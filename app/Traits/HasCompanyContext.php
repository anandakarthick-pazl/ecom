<?php

namespace App\Traits;

trait HasCompanyContext
{
    /**
     * Get the current company ID from session
     */
    protected function getCurrentCompanyId()
    {
        return session('selected_company_id');
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
}
