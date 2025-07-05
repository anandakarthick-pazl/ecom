<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;
use App\Models\SuperAdmin\Company;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure minimum order validation settings exist for all companies
        $companies = Company::where('status', 'active')->get();
        
        foreach ($companies as $company) {
            $this->createMinOrderValidationSettings($company->id);
        }
        
        // Also create default settings for companies without tenant ID (global settings)
        $this->createMinOrderValidationSettings(null);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove minimum order validation settings
        AppSetting::whereIn('key', [
            'min_order_validation_enabled',
            'min_order_amount', 
            'min_order_message'
        ])->delete();
    }
    
    /**
     * Create minimum order validation settings for a company
     */
    private function createMinOrderValidationSettings($companyId)
    {
        $settings = [
            [
                'key' => 'min_order_validation_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'delivery',
                'label' => 'Enable Minimum Order Amount Validation',
                'description' => 'Require customers to place orders above a minimum amount for online orders',
                'company_id' => $companyId
            ],
            [
                'key' => 'min_order_amount',
                'value' => '1000.00',
                'type' => 'float',
                'group' => 'delivery',
                'label' => 'Minimum Order Amount',
                'description' => 'Minimum amount required for online orders (in ₹)',
                'company_id' => $companyId
            ],
            [
                'key' => 'min_order_message',
                'value' => 'Minimum order amount is ₹1000 for online orders.',
                'type' => 'string',
                'group' => 'delivery',
                'label' => 'Minimum Order Validation Message',
                'description' => 'Message to show when order amount is below minimum',
                'company_id' => $companyId
            ]
        ];
        
        foreach ($settings as $setting) {
            // Check if setting already exists
            $whereConditions = [
                'key' => $setting['key'],
                'company_id' => $companyId
            ];
            
            $existing = AppSetting::where($whereConditions)->first();
            
            if (!$existing) {
                AppSetting::create($setting);
                echo "Created setting '{$setting['key']}' for company " . ($companyId ?? 'global') . "\n";
            } else {
                echo "Setting '{$setting['key']}' already exists for company " . ($companyId ?? 'global') . "\n";
            }
        }
    }
};
