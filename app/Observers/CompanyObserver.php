<?php

namespace App\Observers;

use App\Models\SuperAdmin\Company;
use App\Services\PaymentMethodService;
use App\Services\DeliveryService;
use Illuminate\Support\Facades\Log;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        try {
            // Create default payment methods for the new company
            PaymentMethodService::createDefaultForCompany($company->id);
            
            // Create default delivery settings for the new company
            DeliveryService::createDefaultForCompany($company->id);
            
            Log::info('Default payment methods and delivery settings created for company', [
                'company_id' => $company->id,
                'company_name' => $company->name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment methods and delivery settings for new company', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'error' => $e->getMessage()
            ]);
        }
    }
}
