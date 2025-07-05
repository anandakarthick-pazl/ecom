<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure bill format settings exist with proper defaults
        $this->insertBillFormatSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove bill format settings
        AppSetting::whereIn('key', [
            'thermal_printer_enabled',
            'a4_sheet_enabled', 
            'default_bill_format',
            'thermal_printer_width',
            'thermal_printer_auto_cut',
            'a4_sheet_orientation',
            'bill_logo_enabled',
            'bill_company_info_enabled'
        ])->delete();
    }

    /**
     * Insert default bill format settings for all companies
     */
    private function insertBillFormatSettings(): void
    {
        $defaultSettings = [
            [
                'key' => 'thermal_printer_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'bill_format',
                'label' => 'Enable Thermal Printer Format',
                'description' => 'Enable thermal printer format for bills and receipts'
            ],
            [
                'key' => 'a4_sheet_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'bill_format',
                'label' => 'Enable A4 Sheet PDF Format',
                'description' => 'Enable A4 sheet PDF format for bills and receipts'
            ],
            [
                'key' => 'default_bill_format',
                'value' => 'a4_sheet',
                'type' => 'string',
                'group' => 'bill_format',
                'label' => 'Default Bill Format',
                'description' => 'Default format to use when both are enabled'
            ],
            [
                'key' => 'thermal_printer_width',
                'value' => '80',
                'type' => 'integer',
                'group' => 'bill_format',
                'label' => 'Thermal Printer Width (mm)',
                'description' => 'Width of thermal printer paper in mm'
            ],
            [
                'key' => 'thermal_printer_auto_cut',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'bill_format',
                'label' => 'Thermal Printer Auto Cut',
                'description' => 'Automatically cut paper after printing'
            ],
            [
                'key' => 'a4_sheet_orientation',
                'value' => 'portrait',
                'type' => 'string',
                'group' => 'bill_format',
                'label' => 'A4 Sheet Orientation',
                'description' => 'Orientation for A4 sheet PDF (portrait/landscape)'
            ],
            [
                'key' => 'bill_logo_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'bill_format',
                'label' => 'Show Company Logo on Bills',
                'description' => 'Display company logo on bills and receipts'
            ],
            [
                'key' => 'bill_company_info_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'bill_format',
                'label' => 'Show Company Info on Bills',
                'description' => 'Display company information on bills and receipts'
            ]
        ];

        // Get all companies to ensure settings for each tenant
        $companies = \App\Models\SuperAdmin\Company::all();

        foreach ($companies as $company) {
            foreach ($defaultSettings as $setting) {
                // Check if setting already exists for this company
                $existingSetting = AppSetting::where('key', $setting['key'])
                    ->where('company_id', $company->id)
                    ->first();

                if (!$existingSetting) {
                    AppSetting::create(array_merge($setting, [
                        'company_id' => $company->id
                    ]));
                }
            }
        }

        // Also create global settings (without company_id) as fallback
        foreach ($defaultSettings as $setting) {
            $existingGlobalSetting = AppSetting::where('key', $setting['key'])
                ->whereNull('company_id')
                ->first();

            if (!$existingGlobalSetting) {
                AppSetting::create($setting);
            }
        }
    }
};
