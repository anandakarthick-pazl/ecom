<?php

namespace App\Traits;

use App\Models\SuperAdmin\Company;

trait HandlesCompanyData
{
    /**
     * Get standardized company data for email templates
     * 
     * @param int|null $companyId
     * @return array
     */
    protected function getStandardizedCompanyData($companyId = null): array
    {
        // Get company from various sources
        $company = null;
        
        // 1. Try with provided company ID
        if ($companyId) {
            $company = Company::find($companyId);
        }
        
        // 2. Try from order's company_id (if order exists)
        if (!$company && isset($this->order) && $this->order->company_id) {
            $company = Company::find($this->order->company_id);
        }
        
        // 3. Try from current tenant (app container)
        if (!$company && app()->has('current_tenant')) {
            $tenant = app('current_tenant');
            if ($tenant && isset($tenant->id)) {
                $company = Company::find($tenant->id);
            }
        }
        
        // 4. Try from authenticated user
        if (!$company && auth()->check() && auth()->user()->company_id) {
            $company = Company::find(auth()->user()->company_id);
        }
        
        // 5. Try from session
        if (!$company && session('selected_company_id')) {
            $company = Company::find(session('selected_company_id'));
        }
        
        // 6. Try from request domain
        if (!$company) {
            $host = request()->getHost();
            $company = Company::where('domain', $host)->first();
        }
        
        // 7. Get first active company as fallback
        if (!$company) {
            $company = Company::where('status', 'active')->first();
        }
        
        // Return standardized company data
        if ($company) {
            return [
                'id' => $company->id,
                'name' => $this->getCompanyField($company, ['name', 'company_name'], 'Your Company'),
                'email' => $this->getCompanyField($company, ['email', 'company_email'], ''),
                'phone' => $this->getCompanyField($company, ['phone', 'company_phone'], ''),
                'address' => $this->buildCompanyAddress($company),
                'logo' => $this->getCompanyField($company, ['logo', 'company_logo'], ''),
                'gst_number' => $this->getCompanyField($company, ['gst_number', 'gst_no'], ''),
                'website' => $this->getCompanyField($company, ['website', 'company_website'], ''),
                'city' => $this->getCompanyField($company, ['city'], ''),
                'state' => $this->getCompanyField($company, ['state'], ''),
                'postal_code' => $this->getCompanyField($company, ['postal_code', 'pincode', 'zip'], ''),
                'country' => $this->getCompanyField($company, ['country'], 'India'),
                'currency' => $this->getCompanyField($company, ['currency'], 'RS'),
                'currency_code' => $this->getCompanyField($company, ['currency_code'], 'INR'),
                'tax_name' => $this->getCompanyField($company, ['tax_name'], 'GST'),
                'tax_rate' => $this->getCompanyField($company, ['tax_rate'], '18'),
                'primary_color' => $this->getCompanyField($company, ['primary_color'], '#2d5016'),
                'secondary_color' => $this->getCompanyField($company, ['secondary_color'], '#4a7c28'),
                'status' => $this->getCompanyField($company, ['status'], 'active'),
                // Additional fields for better display
                'company_name' => $this->getCompanyField($company, ['name', 'company_name'], 'Your Company'),
                'company_email' => $this->getCompanyField($company, ['email', 'company_email'], ''),
                'company_phone' => $this->getCompanyField($company, ['phone', 'company_phone'], ''),
                'company_address' => $this->buildCompanyAddress($company),
                'company_logo' => $this->getCompanyField($company, ['logo', 'company_logo'], ''),
            ];
        }
        
        // Default fallback values - try to get from app settings if no company found
        $defaultName = app()->has('app.name') ? config('app.name') : 'Your Company';
        
        return [
            'id' => null,
            'name' => $defaultName,
            'email' => '',
            'phone' => '',
            'address' => '',
            'logo' => '',
            'gst_number' => '',
            'website' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => 'India',
            'currency' => 'RS',
            'currency_code' => 'INR',
            'tax_name' => 'GST',
            'tax_rate' => '18',
            'primary_color' => '#2d5016',
            'secondary_color' => '#4a7c28',
            'status' => 'active',
            // Additional fields for better display
            'company_name' => $defaultName,
            'company_email' => '',
            'company_phone' => '',
            'company_address' => '',
            'company_logo' => '',
        ];
    }
    
    /**
     * Get company field value with fallback options
     * 
     * @param object $company
     * @param array $fieldNames
     * @param string $default
     * @return string
     */
    private function getCompanyField($company, array $fieldNames, string $default = ''): string
    {
        foreach ($fieldNames as $fieldName) {
            if (isset($company->$fieldName) && !empty($company->$fieldName)) {
                return $company->$fieldName;
            }
        }
        
        return $default;
    }
    
    /**
     * Build complete company address from available fields
     * 
     * @param object $company
     * @return string
     */
    private function buildCompanyAddress($company): string
    {
        $addressParts = [];
        
        // Primary address
        $address = $this->getCompanyField($company, ['address', 'company_address']);
        if (!empty($address)) {
            $addressParts[] = $address;
        }
        
        // City
        $city = $this->getCompanyField($company, ['city']);
        if (!empty($city)) {
            $addressParts[] = $city;
        }
        
        // State
        $state = $this->getCompanyField($company, ['state']);
        if (!empty($state)) {
            $addressParts[] = $state;
        }
        
        // Postal code
        $postalCode = $this->getCompanyField($company, ['postal_code', 'pincode', 'zip']);
        if (!empty($postalCode)) {
            $addressParts[] = $postalCode;
        }
        
        return implode(', ', array_filter($addressParts));
    }
    
    /**
     * Check if company data has all required fields
     * 
     * @param array $companyData
     * @return bool
     */
    protected function hasRequiredCompanyData(array $companyData): bool
    {
        $requiredFields = ['name', 'email'];
        
        foreach ($requiredFields as $field) {
            if (empty($companyData[$field])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get company logo URL with fallback
     * 
     * @param array $companyData
     * @return string|null
     */
    protected function getCompanyLogoUrl(array $companyData): ?string
    {
        if (empty($companyData['logo'])) {
            return null;
        }
        
        // Check if it's already a full URL
        if (filter_var($companyData['logo'], FILTER_VALIDATE_URL)) {
            return $companyData['logo'];
        }
        
        // Build asset URL
        return asset('storage/' . $companyData['logo']);
    }
}