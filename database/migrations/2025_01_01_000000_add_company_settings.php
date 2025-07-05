<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the current tenant company ID if available
        $companyId = null;
        if (class_exists('\App\Models\Company')) {
            // Try to get the first company or the current tenant
            $company = \App\Models\Company::first();
            $companyId = $company ? $company->id : null;
        }

        // Insert default company settings if they don't exist
        $settings = [
            [
                'key' => 'company_name',
                'value' => 'Herbal Bliss',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Name',
                'description' => 'Your company name'
            ],
            [
                'key' => 'company_tagline',
                'value' => 'Natural & Organic Products',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Tagline',
                'description' => 'Your company tagline or slogan'
            ],
            [
                'key' => 'company_email',
                'value' => 'info@herbalbliss.com',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Email',
                'description' => 'Primary contact email'
            ],
            [
                'key' => 'company_phone',
                'value' => '+91 9876543210',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Phone',
                'description' => 'Primary contact phone number'
            ],
            [
                'key' => 'company_address',
                'value' => '123 Green Street, Nature Park, Chennai - 600001',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Address',
                'description' => 'Full company address'
            ],
            [
                'key' => 'company_logo',
                'value' => '',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Company Logo',
                'description' => 'Company logo file path'
            ],
            [
                'key' => 'primary_color',
                'value' => '#2d5016',
                'type' => 'string',
                'group' => 'theme',
                'label' => 'Primary Color',
                'description' => 'Primary brand color'
            ],
            [
                'key' => 'secondary_color',
                'value' => '#4a7c28',
                'type' => 'string',
                'group' => 'theme',
                'label' => 'Secondary Color',
                'description' => 'Secondary brand color'
            ]
        ];

        foreach ($settings as $setting) {
            // Check if the setting already exists
            $existingQuery = \DB::table('app_settings')->where('key', $setting['key']);
            
            // If company_id column exists, include it in the query
            if (Schema::hasColumn('app_settings', 'company_id') && $companyId) {
                $existingQuery->where('company_id', $companyId);
                $setting['company_id'] = $companyId;
            }
            
            if (!$existingQuery->exists()) {
                \DB::table('app_settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'company_name',
            'company_tagline',
            'company_email',
            'company_phone',
            'company_address',
            'company_logo',
            'primary_color',
            'secondary_color'
        ];
        
        \DB::table('app_settings')->whereIn('key', $keys)->delete();
    }
};
