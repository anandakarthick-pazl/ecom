<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\Company;
use App\Services\DeliveryService;

class DeliverySettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all active companies
        $companies = Company::where('status', 'active')->get();

        foreach ($companies as $company) {
            // Check if delivery settings already exist for this company
            $existingSettings = \App\Models\AppSetting::where('company_id', $company->id)
                ->where('group', 'delivery')
                ->exists();

            if (!$existingSettings) {
                // Create default delivery settings for the company
                DeliveryService::createDefaultForCompany($company->id);
                
                $this->command->info("Created default delivery settings for company: {$company->name}");
            } else {
                $this->command->info("Delivery settings already exist for company: {$company->name}");
            }
        }

        $this->command->info('Delivery settings seeder completed successfully!');
    }
}
