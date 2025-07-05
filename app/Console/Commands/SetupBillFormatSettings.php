<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin\Company;
use App\Models\AppSetting;

class SetupBillFormatSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'herbal:setup-bill-format-settings 
                            {--company-id= : Setup for specific company ID only}
                            {--force : Force setup even if settings already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup default bill format settings for all companies or a specific company';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up bill format settings...');

        $companyId = $this->option('company-id');
        $force = $this->option('force');

        if ($companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                $this->error("Company with ID {$companyId} not found.");
                return 1;
            }
            $companies = collect([$company]);
            $this->info("Setting up bill format settings for company: {$company->name}");
        } else {
            $companies = Company::all();
            $this->info("Setting up bill format settings for all companies (" . $companies->count() . " companies)");
        }

        $defaultSettings = $this->getDefaultBillFormatSettings();
        $setupCount = 0;
        $skippedCount = 0;

        foreach ($companies as $company) {
            $this->line("Processing company: {$company->name} (ID: {$company->id})");

            $settingsSetup = 0;
            foreach ($defaultSettings as $setting) {
                $existingSetting = AppSetting::where('key', $setting['key'])
                    ->where('company_id', $company->id)
                    ->first();

                if ($existingSetting && !$force) {
                    $this->comment("  - {$setting['key']}: Already exists (skipped)");
                    $skippedCount++;
                    continue;
                }

                if ($existingSetting && $force) {
                    $this->warn("  - {$setting['key']}: Updating existing setting");
                    $existingSetting->update([
                        'value' => $setting['value'],
                        'type' => $setting['type'],
                        'group' => $setting['group'],
                        'label' => $setting['label'],
                        'description' => $setting['description']
                    ]);
                } else {
                    $this->info("  - {$setting['key']}: Creating new setting");
                    AppSetting::create(array_merge($setting, [
                        'company_id' => $company->id
                    ]));
                }

                $settingsSetup++;
            }

            if ($settingsSetup > 0) {
                $setupCount++;
                $this->info("  ✓ Setup {$settingsSetup} settings for {$company->name}");
            } else {
                $this->comment("  - All settings already exist for {$company->name}");
            }
        }

        // Also setup global fallback settings
        if (!$companyId) {
            $this->line("\nSetting up global fallback settings...");
            foreach ($defaultSettings as $setting) {
                $existingGlobalSetting = AppSetting::where('key', $setting['key'])
                    ->whereNull('company_id')
                    ->first();

                if (!$existingGlobalSetting || $force) {
                    if ($existingGlobalSetting && $force) {
                        $existingGlobalSetting->update($setting);
                        $this->info("  - Updated global {$setting['key']}");
                    } else {
                        AppSetting::create($setting);
                        $this->info("  - Created global {$setting['key']}");
                    }
                } else {
                    $this->comment("  - Global {$setting['key']}: Already exists");
                }
            }
        }

        // Clear cache
        AppSetting::clearCache();
        $this->info("\nCache cleared successfully.");

        $this->info("\n✅ Bill format settings setup completed!");
        $this->info("Companies processed: {$companies->count()}");
        $this->info("Companies with new settings: {$setupCount}");
        if ($skippedCount > 0) {
            $this->info("Settings skipped (already exist): {$skippedCount}");
        }

        return 0;
    }

    /**
     * Get default bill format settings
     */
    private function getDefaultBillFormatSettings(): array
    {
        return [
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
    }
}
