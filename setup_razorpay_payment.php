<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\DB;

echo "=== SETTING UP RAZORPAY PAYMENT METHOD ===\n\n";

// Get all companies
$companies = Company::where('status', 'active')->get();

if ($companies->isEmpty()) {
    echo "❌ No active companies found! Creating a default company...\n";
    
    // Create a default company if none exists
    $company = Company::create([
        'name' => 'Default Company',
        'company_name' => 'Default Company',
        'domain' => 'localhost:8000',
        'status' => 'active',
        'email' => 'admin@localhost.com',
        'phone' => '9999999999',
        'address' => 'Default Address',
        'city' => 'Default City',
        'state' => 'Default State',
        'pincode' => '000000',
    ]);
    
    echo "✅ Created default company with ID: {$company->id}\n";
    $companies = collect([$company]);
}

echo "Found " . $companies->count() . " active companies:\n";
foreach ($companies as $company) {
    echo "- {$company->name} (ID: {$company->id}, Domain: {$company->domain})\n";
}
echo "\n";

// Setup Razorpay for each company
foreach ($companies as $company) {
    echo "Setting up Razorpay for: {$company->name}\n";
    echo "=====================================\n";
    
    // Check if Razorpay payment method already exists for this company
    $existingMethod = PaymentMethod::where('company_id', $company->id)
                                  ->where('type', 'razorpay')
                                  ->first();
    
    if ($existingMethod) {
        echo "✅ Razorpay method already exists (ID: {$existingMethod->id})\n";
        echo "   Checking configuration...\n";
        
        // Update the existing method to ensure it has proper configuration
        $updateData = [];
        
        if (empty($existingMethod->display_name)) {
            $updateData['display_name'] = 'Online Payment (Cards/UPI/NetBanking)';
        }
        
        if (empty($existingMethod->description)) {
            $updateData['description'] = 'Pay securely with your debit/credit card, UPI, or net banking';
        }
        
        if (!$existingMethod->is_active) {
            $updateData['is_active'] = true;
        }
        
        if (empty($existingMethod->sort_order)) {
            $updateData['sort_order'] = 1;
        }
        
        // Set default credentials if not set (you need to replace these with real credentials)
        if (empty($existingMethod->razorpay_key_id)) {
            echo "⚠️  WARNING: No Razorpay Key ID found. Please add your credentials!\n";
            $updateData['razorpay_key_id'] = 'rzp_test_YOUR_KEY_ID_HERE';
        }
        
        if (empty($existingMethod->razorpay_key_secret)) {
            echo "⚠️  WARNING: No Razorpay Key Secret found. Please add your credentials!\n";
            $updateData['razorpay_key_secret'] = 'YOUR_KEY_SECRET_HERE';
        }
        
        if (!empty($updateData)) {
            $existingMethod->update($updateData);
            echo "✅ Updated Razorpay method configuration\n";
        }
        
    } else {
        echo "Creating new Razorpay payment method...\n";
        
        $paymentMethod = PaymentMethod::create([
            'company_id' => $company->id,
            'name' => 'razorpay',
            'type' => 'razorpay',
            'display_name' => 'Online Payment (Cards/UPI/NetBanking)',
            'description' => 'Pay securely with your debit/credit card, UPI, or net banking',
            'is_active' => true,
            'sort_order' => 1,
            'razorpay_key_id' => 'rzp_test_YOUR_KEY_ID_HERE',
            'razorpay_key_secret' => 'YOUR_KEY_SECRET_HERE',
            'razorpay_webhook_secret' => 'YOUR_WEBHOOK_SECRET_HERE',
            'minimum_amount' => 1.00,
            'maximum_amount' => 100000.00,
            'extra_charge' => 0.00,
            'extra_charge_percentage' => 0.00,
        ]);
        
        echo "✅ Created Razorpay payment method with ID: {$paymentMethod->id}\n";
    }
    
    echo "\n";
}

// Also create a global fallback method if needed
$globalMethod = PaymentMethod::where('company_id', null)
                             ->where('type', 'razorpay')
                             ->first();

if (!$globalMethod) {
    echo "Creating global fallback Razorpay method...\n";
    
    PaymentMethod::create([
        'company_id' => null,
        'name' => 'razorpay_global',
        'type' => 'razorpay',
        'display_name' => 'Online Payment (Cards/UPI/NetBanking)',
        'description' => 'Pay securely with your debit/credit card, UPI, or net banking',
        'is_active' => true,
        'sort_order' => 1,
        'razorpay_key_id' => 'rzp_test_YOUR_KEY_ID_HERE',
        'razorpay_key_secret' => 'YOUR_KEY_SECRET_HERE',
        'razorpay_webhook_secret' => 'YOUR_WEBHOOK_SECRET_HERE',
        'minimum_amount' => 1.00,
        'maximum_amount' => 100000.00,
        'extra_charge' => 0.00,
        'extra_charge_percentage' => 0.00,
    ]);
    
    echo "✅ Created global fallback Razorpay method\n";
}

echo "\n=== IMPORTANT: UPDATE YOUR RAZORPAY CREDENTIALS ===\n";
echo "1. Login to your Razorpay Dashboard (https://dashboard.razorpay.com/)\n";
echo "2. Go to Settings > API Keys\n";
echo "3. Copy your Key ID and Key Secret\n";
echo "4. Update the payment methods in your database with real credentials\n";
echo "5. For webhooks, go to Settings > Webhooks and create a webhook\n";
echo "\nSQL Update Query Example:\n";
echo "UPDATE payment_methods SET \n";
echo "  razorpay_key_id = 'your_real_key_id',\n";
echo "  razorpay_key_secret = 'your_real_key_secret',\n";
echo "  razorpay_webhook_secret = 'your_webhook_secret'\n";
echo "WHERE type = 'razorpay';\n";

echo "\n=== SETUP COMPLETED ===\n";
