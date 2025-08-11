<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin\Company;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class UpdateCompanyInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:update 
                          {name : The new company name}
                          {--email= : Company email address}
                          {--phone= : Company phone number}
                          {--address= : Company address}
                          {--gst= : GST registration number}
                          {--company-id= : Specific company ID to update (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update company information in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $newCompanyName = $this->argument('name');
        $newEmail = $this->option('email');
        $newPhone = $this->option('phone');
        $newAddress = $this->option('address');
        $newGst = $this->option('gst');
        $companyId = $this->option('company-id');

        try {
            // Get company to update
            if ($companyId) {
                $company = Company::find($companyId);
                if (!$company) {
                    $this->error("Company with ID {$companyId} not found!");
                    return 1;
                }
            } else {
                $company = Company::first();
                if (!$company) {
                    $this->error('No company found in the database!');
                    return 1;
                }
            }

            $this->info("Updating company: {$company->name} (ID: {$company->id})");

            // Prepare update data
            $updateData = ['name' => $newCompanyName];
            if ($newEmail) $updateData['email'] = $newEmail;
            if ($newPhone) $updateData['phone'] = $newPhone;
            if ($newAddress) $updateData['address'] = $newAddress;
            if ($newGst) $updateData['gst_number'] = $newGst;

            // Update company table
            $company->update($updateData);
            $this->info('✅ Updated companies table');

            // Update app settings for this company
            $settings = [
                'company_name' => $newCompanyName,
                'company_email' => $newEmail,
                'company_phone' => $newPhone,
                'company_address' => $newAddress,
                'company_gst_number' => $newGst
            ];

            foreach ($settings as $key => $value) {
                if (!empty($value)) {
                    // Update company-specific setting
                    AppSetting::setForTenant($key, $value, $company->id);
                    $this->info("✅ Updated setting: {$key} = {$value}");
                }
            }

            // Clear all caches
            Cache::flush();
            AppSetting::clearCache();
            $this->info('✅ Cleared application cache');

            $this->newLine();
            $this->info('🎉 Company information updated successfully!');
            $this->table(['Field', 'New Value'], [
                ['Company Name', $newCompanyName],
                ['Email', $newEmail ?: 'Not updated'],
                ['Phone', $newPhone ?: 'Not updated'],
                ['Address', $newAddress ?: 'Not updated'],
                ['GST Number', $newGst ?: 'Not updated'],
            ]);

            $this->newLine();
            $this->warn('📝 Note: The changes will appear on all new invoices and receipts.');
            $this->warn('🔄 You may need to refresh your browser cache to see changes in the admin panel.');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error updating company information: ' . $e->getMessage());
            return 1;
        }
    }
}
