<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all active companies
        $companies = Company::where('status', 'active')->get();

        foreach ($companies as $company) {
            $this->createDefaultPaymentMethods($company->id);
        }
    }

    /**
     * Create default payment methods for a company
     */
    public function createDefaultPaymentMethods($companyId)
    {
        $defaultMethods = [
            [
                'company_id' => $companyId,
                'name' => 'razorpay',
                'type' => 'razorpay',
                'display_name' => 'Online Payment (Cards, UPI, Wallets)',
                'description' => 'Pay securely using credit cards, debit cards, UPI, net banking, or digital wallets',
                'is_active' => false, // Disabled by default until configured
                'sort_order' => 1,
                'minimum_amount' => 1.00,
                'maximum_amount' => null,
                'extra_charge' => 0.00,
                'extra_charge_percentage' => 2.00, // 2% gateway charge
                'razorpay_key_id' => null,
                'razorpay_key_secret' => null,
                'razorpay_webhook_secret' => null,
            ],
            [
                'company_id' => $companyId,
                'name' => 'cod',
                'type' => 'cod',
                'display_name' => 'Cash on Delivery (COD)',
                'description' => 'Pay with cash when your order is delivered to your doorstep',
                'is_active' => true, // Enabled by default
                'sort_order' => 2,
                'minimum_amount' => 100.00,
                'maximum_amount' => 10000.00,
                'extra_charge' => 25.00, // COD handling charge
                'extra_charge_percentage' => 0.00,
            ],
            [
                'company_id' => $companyId,
                'name' => 'bank_transfer',
                'type' => 'bank_transfer',
                'display_name' => 'Direct Bank Transfer',
                'description' => 'Transfer payment directly to our bank account using NEFT/RTGS/IMPS',
                'is_active' => false, // Disabled by default until bank details are configured
                'sort_order' => 3,
                'minimum_amount' => 500.00,
                'maximum_amount' => null,
                'extra_charge' => 0.00,
                'extra_charge_percentage' => 0.00,
                'bank_details' => [
                    'bank_name' => '',
                    'account_name' => '',
                    'account_number' => '',
                    'ifsc_code' => '',
                    'branch_name' => '',
                ],
            ],
            [
                'company_id' => $companyId,
                'name' => 'upi',
                'type' => 'upi',
                'display_name' => 'UPI Payment',
                'description' => 'Pay instantly using your UPI app by scanning QR code or UPI ID',
                'is_active' => false, // Disabled by default until UPI details are configured
                'sort_order' => 4,
                'minimum_amount' => 1.00,
                'maximum_amount' => 100000.00,
                'extra_charge' => 0.00,
                'extra_charge_percentage' => 0.00,
                'upi_id' => null,
                'upi_qr_code' => null,
            ],
        ];

        foreach ($defaultMethods as $method) {
            // Check if payment method already exists for this company
            $exists = PaymentMethod::where('company_id', $companyId)
                ->where('type', $method['type'])
                ->exists();

            if (!$exists) {
                PaymentMethod::create($method);
            }
        }
    }
}
