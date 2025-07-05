<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use App\Models\Order;

echo "=== TESTING RAZORPAY CONFIGURATION ===\n\n";

// Test 1: Check payment methods
echo "Test 1: Payment Methods Configuration\n";
echo "=====================================\n";

$razorpayMethods = PaymentMethod::where('type', 'razorpay')->where('is_active', true)->get();

if ($razorpayMethods->isEmpty()) {
    echo "❌ FAIL: No active Razorpay payment methods found!\n";
} else {
    echo "✅ PASS: Found " . $razorpayMethods->count() . " active Razorpay methods\n";
    
    foreach ($razorpayMethods as $method) {
        echo "\nMethod ID: {$method->id}\n";
        echo "Company ID: " . ($method->company_id ?? 'Global') . "\n";
        echo "Key ID: " . (strlen($method->razorpay_key_id) > 10 ? '✅ Set' : '❌ Missing/Invalid') . "\n";
        echo "Key Secret: " . (strlen($method->razorpay_key_secret) > 10 ? '✅ Set' : '❌ Missing/Invalid') . "\n";
        
        // Test if credentials look like real Razorpay credentials
        if (str_starts_with($method->razorpay_key_id, 'rzp_test_') || str_starts_with($method->razorpay_key_id, 'rzp_live_')) {
            echo "Credentials Format: ✅ Valid Razorpay format\n";
        } else {
            echo "Credentials Format: ⚠️  Please update with real Razorpay credentials\n";
        }
    }
}

echo "\n";

// Test 2: Company context
echo "Test 2: Company Context\n";
echo "=======================\n";

$companies = Company::where('status', 'active')->get();
echo "Active Companies: " . $companies->count() . "\n";

foreach ($companies as $company) {
    $companyMethods = PaymentMethod::where('company_id', $company->id)
                                  ->where('type', 'razorpay')
                                  ->where('is_active', true)
                                  ->count();
    echo "Company '{$company->name}': {$companyMethods} Razorpay methods\n";
}

echo "\n";

// Test 3: Recent orders with payment issues
echo "Test 3: Recent Payment Issues\n";
echo "==============================\n";

$recentOrders = Order::where('payment_method', 'razorpay')
                    ->where('payment_status', '!=', 'paid')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

if ($recentOrders->isEmpty()) {
    echo "✅ No recent failed Razorpay payments\n";
} else {
    echo "Found " . $recentOrders->count() . " recent orders with payment issues:\n";
    
    foreach ($recentOrders as $order) {
        echo "\nOrder: {$order->order_number}\n";
        echo "Status: {$order->payment_status}\n";
        echo "Amount: ₹{$order->total}\n";
        echo "Created: {$order->created_at}\n";
        echo "Company ID: {$order->company_id}\n";
        
        // Check if payment method exists for this order's company
        $availableMethod = PaymentMethod::where('company_id', $order->company_id)
                                       ->where('type', 'razorpay')
                                       ->where('is_active', true)
                                       ->first();
        
        if ($availableMethod) {
            echo "Payment Method: ✅ Available\n";
        } else {
            echo "Payment Method: ❌ Not found for this company\n";
        }
    }
}

echo "\n";

// Test 4: Route testing
echo "Test 4: Route Configuration\n";
echo "============================\n";

try {
    $createOrderRoute = route('razorpay.create-order');
    $verifyPaymentRoute = route('razorpay.verify-payment');
    $webhookRoute = route('razorpay.webhook');
    
    echo "✅ Create Order Route: {$createOrderRoute}\n";
    echo "✅ Verify Payment Route: {$verifyPaymentRoute}\n";
    echo "✅ Webhook Route: {$webhookRoute}\n";
} catch (Exception $e) {
    echo "❌ Route Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Required classes and dependencies
echo "Test 5: Dependencies Check\n";
echo "===========================\n";

$dependencies = [
    'Razorpay\Api\Api' => 'Razorpay SDK',
    'App\Http\Controllers\RazorpayController' => 'Razorpay Controller',
    'App\Models\PaymentMethod' => 'Payment Method Model',
];

foreach ($dependencies as $class => $name) {
    if (class_exists($class)) {
        echo "✅ {$name}: Available\n";
    } else {
        echo "❌ {$name}: Missing\n";
    }
}

echo "\n=== DIAGNOSTIC SUMMARY ===\n";
echo "1. Check if payment methods are properly configured above\n";
echo "2. Ensure you have real Razorpay credentials (not placeholder values)\n";
echo "3. Verify the company context matches your current domain\n";
echo "4. Test the payment flow after updating credentials\n";

echo "\n=== NEXT STEPS TO FIX ===\n";
echo "1. Get real Razorpay credentials from https://dashboard.razorpay.com/\n";
echo "2. Update payment methods with real credentials\n";
echo "3. Test payment flow\n";
echo "4. Check browser console for JavaScript errors during payment\n";

echo "\n=== END TEST ===\n";
