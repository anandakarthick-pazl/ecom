<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;
use App\Models\SuperAdmin\Company;

return new class extends Migration
{
    public function up()
    {
        try {
            // Get all companies to create settings for each tenant
            $companies = Company::all();
            
            foreach ($companies as $company) {
                $this->createInvoiceSettings($company->id);
            }
            
            // Also create default settings for new companies (company_id = null)
            $this->createInvoiceSettings(null);
            
        } catch (Exception $e) {
            // Log error but don't fail migration
            \Log::error('Invoice numbering migration warning: ' . $e->getMessage());
        }
    }
    
    private function createInvoiceSettings($companyId)
    {
        $invoiceSettings = [
            // Online Order Invoice Settings
            [
                'key' => 'order_invoice_prefix',
                'value' => 'ORD',
                'type' => 'string',
                'group' => 'invoice_numbering',
                'label' => 'Order Invoice Prefix',
                'description' => 'Prefix for online order invoice numbers',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_separator',
                'value' => '-',
                'type' => 'string',
                'group' => 'invoice_numbering',
                'label' => 'Order Invoice Separator',
                'description' => 'Separator character between invoice components',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_digits',
                'value' => '5',
                'type' => 'integer',
                'group' => 'invoice_numbering',
                'label' => 'Order Invoice Number Digits',
                'description' => 'Number of digits for sequence number (with zero padding)',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_include_year',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Include Year in Order Invoice',
                'description' => 'Include current year in invoice number',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_include_month',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Include Month in Order Invoice',
                'description' => 'Include current month in invoice number',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_reset_yearly',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Reset Order Invoice Yearly',
                'description' => 'Reset sequence number every year',
                'company_id' => $companyId
            ],
            [
                'key' => 'order_invoice_reset_monthly',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Reset Order Invoice Monthly',
                'description' => 'Reset sequence number every month',
                'company_id' => $companyId
            ],
            
            // POS Sale Invoice Settings
            [
                'key' => 'pos_invoice_prefix',
                'value' => 'POS',
                'type' => 'string',
                'group' => 'invoice_numbering',
                'label' => 'POS Invoice Prefix',
                'description' => 'Prefix for POS sale invoice numbers',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_separator',
                'value' => '-',
                'type' => 'string',
                'group' => 'invoice_numbering',
                'label' => 'POS Invoice Separator',
                'description' => 'Separator character between invoice components',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_digits',
                'value' => '4',
                'type' => 'integer',
                'group' => 'invoice_numbering',
                'label' => 'POS Invoice Number Digits',
                'description' => 'Number of digits for sequence number (with zero padding)',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_include_year',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Include Year in POS Invoice',
                'description' => 'Include current year in invoice number',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_include_month',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Include Month in POS Invoice',
                'description' => 'Include current month in invoice number',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_reset_yearly',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Reset POS Invoice Yearly',
                'description' => 'Reset sequence number every year',
                'company_id' => $companyId
            ],
            [
                'key' => 'pos_invoice_reset_monthly',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'invoice_numbering',
                'label' => 'Reset POS Invoice Monthly',
                'description' => 'Reset sequence number every month',
                'company_id' => $companyId
            ]
        ];
        
        foreach ($invoiceSettings as $setting) {
            AppSetting::updateOrCreate(
                [
                    'key' => $setting['key'],
                    'company_id' => $setting['company_id']
                ],
                $setting
            );
        }
    }

    public function down()
    {
        // Remove invoice numbering settings
        $keys = [
            'order_invoice_prefix',
            'order_invoice_separator', 
            'order_invoice_digits',
            'order_invoice_include_year',
            'order_invoice_include_month',
            'order_invoice_reset_yearly',
            'order_invoice_reset_monthly',
            'pos_invoice_prefix',
            'pos_invoice_separator',
            'pos_invoice_digits', 
            'pos_invoice_include_year',
            'pos_invoice_include_month',
            'pos_invoice_reset_yearly',
            'pos_invoice_reset_monthly'
        ];
        
        AppSetting::whereIn('key', $keys)->delete();
        
        // Also remove any sequence tracking settings
        AppSetting::where('group', 'invoice_sequences')->delete();
    }
};
