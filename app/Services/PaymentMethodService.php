<?php

namespace App\Services;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    /**
     * Create default payment methods for a new company
     */
    public static function createDefaultForCompany($companyId)
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

    /**
     * Get active payment methods for checkout
     */
    public static function getActiveForCheckout($total = null)
    {
        // Get current company ID
        $companyId = session('selected_company_id') 
            ?? auth()->user()->company_id 
            ?? app('current_tenant')->id ?? null;
            
        $query = PaymentMethod::active()
            ->when($companyId, function($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            })
            ->orderBy('sort_order')
            ->orderBy('id');

        $methods = $query->get();

        if ($total !== null) {
            $methods = $methods->filter(function($method) use ($total) {
                return $method->isAmountValid($total);
            });
        }

        return $methods;
    }

    /**
     * Calculate payment charges for a method and amount
     */
    public static function calculateCharges(PaymentMethod $method, $amount)
    {
        $fixedCharge = $method->extra_charge;
        $percentageCharge = ($amount * $method->extra_charge_percentage) / 100;
        
        return $fixedCharge + $percentageCharge;
    }

    /**
     * Get payment method configuration for frontend
     */
    public static function getMethodConfig(PaymentMethod $method)
    {
        switch ($method->type) {
            case 'razorpay':
                return [
                    'type' => 'razorpay',
                    'key_id' => $method->razorpay_key_id,
                    'requires_online_payment' => true,
                ];
            
            case 'cod':
                return [
                    'type' => 'cod',
                    'requires_online_payment' => false,
                ];
            
            case 'bank_transfer':
                return [
                    'type' => 'bank_transfer',
                    'bank_details' => $method->bank_details,
                    'requires_online_payment' => false,
                ];
            
            case 'upi':
                return [
                    'type' => 'upi',
                    'upi_id' => $method->upi_id,
                    'qr_code' => $method->upi_qr_code ? asset('storage/' . $method->upi_qr_code) : null,
                    'requires_online_payment' => false,
                ];
            
            default:
                return [
                    'type' => $method->type,
                    'requires_online_payment' => false,
                ];
        }
    }
}
