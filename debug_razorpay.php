<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;

echo "=== RAZORPAY PAYMENT DEBUG SCRIPT ===\n\n";

echo "1. Checking Payment Methods:\n";
echo "============================\n";

$paymentMethods = PaymentMethod::where('type', 'razorpay')->get();

if ($paymentMethods->isEmpty()) {
    echo "❌ NO Razorpay payment methods found in database!\n";
    echo "This is likely the main issue.\n\n";
} else {
    foreach ($paymentMethods as $method) {
        echo "✅ Razorpay Payment Method Found:\n";
        echo "   ID: {$method->id}\n";
        echo "   Company ID: {$method->company_id}\n";
        echo "   Name: {$method->name}\n";
        echo "   Display Name: {$method->display_name}\n";
        echo "   Is Active: " . ($method->is_active ? 'Yes' : 'No') . "\n";
        echo "   Has Key ID: " . (!empty($method->razorpay_key_id) ? 'Yes' : 'No') . "\n";
        echo "   Has Key Secret: " . (!empty($method->razorpay_key_secret) ? 'Yes' : 'No') . "\n";
        echo "   Has Webhook Secret: " . (!empty($method->razorpay_webhook_secret) ? 'Yes' : 'No') . "\n";
        echo "   Min Amount: {$method->minimum_amount}\n";
        echo "   Max Amount: {$method->maximum_amount}\n";
        echo "   Extra Charge: {$method->extra_charge}\n";
        echo "   Extra %: {$method->extra_charge_percentage}\n";
        echo "   -------------------------\n";
    }
}

echo "\n2. Checking Companies:\n";
echo "======================\n";

$companies = Company::all();
foreach ($companies as $company) {
    echo "Company: {$company->name} (ID: {$company->id})\n";
    echo "   Domain: {$company->domain}\n";
    echo "   Status: {$company->status}\n";
    
    $companyPaymentMethods = PaymentMethod::where('company_id', $company->id)->where('type', 'razorpay')->get();
    echo "   Razorpay Methods: " . $companyPaymentMethods->count() . "\n";
    echo "   -------------------------\n";
}

echo "\n3. Environment Check:\n";
echo "=====================\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG') . "\n";
echo "Current Domain: " . request()->getHost() . "\n";

echo "\n=== END DEBUG ===\n";
