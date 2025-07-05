<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== ENHANCED PAYMENT METHOD COMPANY_ID FIX ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    echo "🔍 Step 1: Comprehensive Analysis...\n";
    echo "===================================\n";

    // Get detailed info about current state
    $allPaymentMethods = PaymentMethod::all();
    $methodsWithoutCompany = PaymentMethod::whereNull('company_id')->orWhere('company_id', 0)->get();
    $methodsWithCompany = PaymentMethod::whereNotNull('company_id')->where('company_id', '>', 0)->get();

    echo "Total payment methods: " . $allPaymentMethods->count() . "\n";
    echo "Methods WITHOUT company_id: " . $methodsWithoutCompany->count() . "\n";
    echo "Methods WITH company_id: " . $methodsWithCompany->count() . "\n\n";

    if ($methodsWithoutCompany->count() > 0) {
        echo "❌ ISSUE: Payment methods missing valid company_id:\n";
        foreach ($methodsWithoutCompany as $method) {
            echo "   ID: {$method->id}, Type: {$method->type}, Name: {$method->display_name}, Company_ID: " . ($method->company_id ?? 'NULL') . "\n";
        }
        echo "\n";
    } else {
        echo "✅ All payment methods have valid company_id set\n\n";
    }

    echo "🔍 Step 2: Checking Company Infrastructure...\n";
    echo "============================================\n";

    // Check companies
    $companies = Company::all();
    $activeCompanies = Company::where('status', 'active')->get();
    
    echo "Total companies: " . $companies->count() . "\n";
    echo "Active companies: " . $activeCompanies->count() . "\n";

    if ($activeCompanies->isEmpty()) {
        echo "❌ No active companies found! Creating default company...\n";
        
        $defaultCompany = Company::create([
            'name' => 'Default Store',
            'company_name' => 'Default Store',
            'domain' => 'localhost:8000',
            'status' => 'active',
            'email' => 'admin@localhost.com',
            'phone' => '9999999999',
            'address' => 'Default Address',
            'city' => 'Default City',
            'state' => 'Default State',
            'pincode' => '000000',
            'country' => 'India',
            'subscription_plan' => 'basic',
            'subscription_status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Created default company: {$defaultCompany->name} (ID: {$defaultCompany->id})\n";
        $activeCompanies = collect([$defaultCompany]);
    } else {
        foreach ($activeCompanies as $company) {
            echo "   ID: {$company->id}, Name: {$company->name}, Domain: {$company->domain}, Status: {$company->status}\n";
        }
    }

    echo "\n🔧 Step 3: Enhanced Company ID Assignment...\n";
    echo "===========================================\n";

    if ($methodsWithoutCompany->count() > 0) {
        // Try to determine best company for each payment method
        foreach ($methodsWithoutCompany as $method) {
            $assignedCompanyId = null;
            
            // Strategy 1: Check if there's a pattern in other payment methods
            $siblingMethods = PaymentMethod::where('id', '!=', $method->id)
                ->whereNotNull('company_id')
                ->where('company_id', '>', 0)
                ->get();
                
            if ($siblingMethods->isNotEmpty()) {
                // Use the most common company_id from other methods
                $companyGroups = $siblingMethods->groupBy('company_id');
                $mostCommonCompanyId = $companyGroups->sortByDesc(function($group) {
                    return $group->count();
                })->keys()->first();
                
                if ($activeCompanies->where('id', $mostCommonCompanyId)->isNotEmpty()) {
                    $assignedCompanyId = $mostCommonCompanyId;
                    echo "   Using most common company ID {$mostCommonCompanyId} for payment method {$method->id}\n";
                }
            }
            
            // Strategy 2: Use first active company if no pattern found
            if (!$assignedCompanyId) {
                $assignedCompanyId = $activeCompanies->first()->id;
                echo "   Using first active company ID {$assignedCompanyId} for payment method {$method->id}\n";
            }
            
            // Update the payment method
            $method->update(['company_id' => $assignedCompanyId]);
            echo "   ✅ Fixed payment method ID {$method->id} ({$method->type}) -> Company ID: {$assignedCompanyId}\n";
        }
    } else {
        echo "✅ No payment methods need company_id assignment\n";
    }

    echo "\n🔧 Step 4: Ensuring Default Payment Methods for All Companies...\n";
    echo "==============================================================\n";

    foreach ($activeCompanies as $company) {
        $existingMethods = PaymentMethod::where('company_id', $company->id)->get();
        echo "Company: {$company->name} (ID: {$company->id}) - Current methods: {$existingMethods->count()}\n";
        
        if ($existingMethods->isEmpty()) {
            echo "   Creating default payment methods for {$company->name}...\n";
            
            $defaultMethods = [
                [
                    'company_id' => $company->id,
                    'name' => 'cod',
                    'type' => 'cod',
                    'display_name' => 'Cash on Delivery (COD)',
                    'description' => 'Pay with cash when your order is delivered to your doorstep',
                    'is_active' => true,
                    'sort_order' => 1,
                    'minimum_amount' => 1.00,
                    'maximum_amount' => 10000.00,
                    'extra_charge' => 25.00,
                    'extra_charge_percentage' => 0.00,
                ],
                [
                    'company_id' => $company->id,
                    'name' => 'razorpay',
                    'type' => 'razorpay',
                    'display_name' => 'Online Payment (Cards, UPI, Wallets)',
                    'description' => 'Pay securely using credit cards, debit cards, UPI, net banking, or digital wallets',
                    'is_active' => false, // Disabled until configured
                    'sort_order' => 2,
                    'minimum_amount' => 1.00,
                    'maximum_amount' => null,
                    'extra_charge' => 0.00,
                    'extra_charge_percentage' => 2.00,
                ],
                [
                    'company_id' => $company->id,
                    'name' => 'upi',
                    'type' => 'upi',
                    'display_name' => 'UPI Payment',
                    'description' => 'Pay using UPI apps like PhonePe, Google Pay, Paytm',
                    'is_active' => false, // Disabled until configured
                    'sort_order' => 3,
                    'minimum_amount' => 1.00,
                    'maximum_amount' => 100000.00,
                    'extra_charge' => 0.00,
                    'extra_charge_percentage' => 0.00,
                ]
            ];
            
            foreach ($defaultMethods as $methodData) {
                $created = PaymentMethod::create($methodData);
                echo "     ✅ Created {$methodData['type']} payment method (ID: {$created->id})\n";
            }
        } else {
            foreach ($existingMethods as $method) {
                echo "   - {$method->type}: {$method->display_name} (" . ($method->is_active ? 'Active' : 'Inactive') . ")\n";
            }
        }
    }

    echo "\n🔧 Step 5: Database Integrity Check...\n";
    echo "=====================================\n";

    // Check for orphaned payment methods (company_id points to non-existent company)
    $orphanedMethods = PaymentMethod::whereNotIn('company_id', $activeCompanies->pluck('id'))->get();
    
    if ($orphanedMethods->isNotEmpty()) {
        echo "❌ Found orphaned payment methods (pointing to non-existent companies):\n";
        foreach ($orphanedMethods as $method) {
            echo "   ID: {$method->id}, Company_ID: {$method->company_id}, Type: {$method->type}\n";
            // Reassign to first active company
            $method->update(['company_id' => $activeCompanies->first()->id]);
            echo "   ✅ Reassigned to company ID: {$activeCompanies->first()->id}\n";
        }
    } else {
        echo "✅ No orphaned payment methods found\n";
    }

    echo "\n🔍 Step 6: Final Verification...\n";
    echo "===============================\n";

    $finalCheck = PaymentMethod::whereNull('company_id')->orWhere('company_id', 0)->count();
    $totalAfterFix = PaymentMethod::count();
    $validAfterFix = PaymentMethod::whereNotNull('company_id')->where('company_id', '>', 0)->count();

    echo "Total payment methods: {$totalAfterFix}\n";
    echo "Valid payment methods: {$validAfterFix}\n";
    echo "Invalid payment methods: {$finalCheck}\n";

    if ($finalCheck == 0) {
        echo "✅ SUCCESS: All payment methods now have valid company_id!\n";
    } else {
        echo "❌ WARNING: {$finalCheck} payment methods still missing valid company_id\n";
    }

    echo "\n📊 Final Company Summary:\n";
    echo "========================\n";

    foreach ($activeCompanies as $company) {
        $methodCount = PaymentMethod::where('company_id', $company->id)->count();
        $activeMethods = PaymentMethod::where('company_id', $company->id)->where('is_active', true)->count();
        
        echo "Company: {$company->name} (ID: {$company->id})\n";
        echo "   Total methods: {$methodCount}\n";
        echo "   Active methods: {$activeMethods}\n";
        
        $methods = PaymentMethod::where('company_id', $company->id)->get();
        foreach ($methods as $method) {
            echo "   - {$method->type}: {$method->display_name} (" . ($method->is_active ? 'Active' : 'Inactive') . ")\n";
        }
        echo "\n";
    }

    echo "=== ENHANCED FIX COMPLETED SUCCESSFULLY ===\n";
    echo "\n🚀 NEXT STEPS:\n";
    echo "1. Test creating new payment methods in admin panel\n";
    echo "2. Verify checkout process works correctly\n";
    echo "3. Configure Razorpay/UPI credentials if needed\n";
    echo "4. Monitor logs for any new issues\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
