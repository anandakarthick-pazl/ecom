<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\SuperAdmin\Company;
use Razorpay\Api\Api;

echo "=== RAZORPAY ORDER CREATION DEBUG ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check if payment methods exist and are valid
echo "1. CHECKING PAYMENT METHODS:\n";
echo "============================\n";

$paymentMethods = PaymentMethod::where('type', 'razorpay')->where('is_active', true)->get();

if ($paymentMethods->isEmpty()) {
    echo "❌ NO active Razorpay payment methods found!\n";
    echo "SOLUTION: Run immediate_credential_fix.php\n\n";
} else {
    foreach ($paymentMethods as $method) {
        echo "Method ID: {$method->id}\n";
        echo "Company ID: " . ($method->company_id ?? 'Global') . "\n";
        echo "Key ID: {$method->razorpay_key_id}\n";
        echo "Key Secret Length: " . strlen($method->razorpay_key_secret) . " chars\n";
        
        // Test API connection
        try {
            echo "Testing API connection...\n";
            $api = new Api($method->razorpay_key_id, $method->razorpay_key_secret);
            
            // Try to fetch a dummy payment (this will fail but test connectivity)
            try {
                $api->payment->fetch('dummy_payment_id');
            } catch (\Razorpay\Api\Errors\BadRequestError $e) {
                if (str_contains($e->getMessage(), 'payment_id is invalid')) {
                    echo "✅ API connection working (expected error for dummy ID)\n";
                } else {
                    echo "❌ API Error: " . $e->getMessage() . "\n";
                }
            } catch (\Exception $e) {
                echo "❌ Connection Error: " . $e->getMessage() . "\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Failed to create API instance: " . $e->getMessage() . "\n";
        }
        echo "-------------------\n";
    }
}

// Test 2: Check recent failed orders
echo "\n2. CHECKING RECENT FAILED ORDERS:\n";
echo "==================================\n";

$recentOrders = Order::where('created_at', '>=', now()->subHours(2))
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

if ($recentOrders->isEmpty()) {
    echo "No recent orders found\n";
} else {
    foreach ($recentOrders as $order) {
        echo "Order: {$order->order_number}\n";
        echo "Amount: ₹{$order->total}\n";
        echo "Company ID: {$order->company_id}\n";
        echo "Payment Status: {$order->payment_status}\n";
        
        if ($order->payment_details) {
            $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
            if (isset($details['error'])) {
                echo "Error: {$details['error']}\n";
            }
        }
        echo "-------------------\n";
    }
}

// Test 3: Try creating a test order
echo "\n3. TESTING ORDER CREATION:\n";
echo "===========================\n";

$paymentMethod = PaymentMethod::where('type', 'razorpay')->where('is_active', true)->first();

if ($paymentMethod) {
    try {
        echo "Using Payment Method ID: {$paymentMethod->id}\n";
        echo "Key ID: {$paymentMethod->razorpay_key_id}\n";
        
        $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
        
        // Create test order
        $testOrder = $api->order->create([
            'amount' => 10000, // ₹100 in paise
            'currency' => 'INR',
            'receipt' => 'test_' . time(),
            'notes' => [
                'test' => true,
                'created_at' => now()->toISOString()
            ]
        ]);
        
        echo "✅ SUCCESS! Test order created:\n";
        echo "   Order ID: {$testOrder->id}\n";
        echo "   Amount: {$testOrder->amount} paise\n";
        echo "   Status: {$testOrder->status}\n";
        
    } catch (\Exception $e) {
        echo "❌ FAILED to create test order:\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
        
        // Check specific error types
        if (str_contains($e->getMessage(), 'Key/Secret provided is invalid')) {
            echo "\n🔧 SOLUTION: Invalid credentials - run immediate_credential_fix.php\n";
        } elseif (str_contains($e->getMessage(), 'cURL error')) {
            echo "\n🔧 SOLUTION: Network connectivity issue - check internet connection\n";
        } elseif (str_contains($e->getMessage(), 'SSL')) {
            echo "\n🔧 SOLUTION: SSL certificate issue - update cURL certificates\n";
        }
    }
} else {
    echo "❌ No payment method available for testing\n";
}

// Test 4: Check Laravel logs for recent errors
echo "\n4. CHECKING RECENT LOGS:\n";
echo "========================\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    
    $recentErrors = [];
    $today = date('Y-m-d');
    
    foreach (array_reverse($lines) as $line) {
        if (str_contains($line, $today) && 
            (str_contains($line, 'Razorpay') || str_contains($line, 'Failed to create payment order')) &&
            str_contains($line, 'ERROR')) {
            $recentErrors[] = $line;
            if (count($recentErrors) >= 3) break;
        }
    }
    
    if (!empty($recentErrors)) {
        echo "Recent Razorpay errors:\n";
        foreach ($recentErrors as $error) {
            echo "- " . substr($error, 0, 150) . "...\n";
        }
    } else {
        echo "✅ No recent Razorpay errors in logs\n";
    }
} else {
    echo "❌ Log file not found\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "\nNEXT STEPS:\n";
echo "1. If credentials are invalid: run immediate_credential_fix.php\n";
echo "2. If connection fails: check internet and firewall\n";
echo "3. If SSL errors: update PHP/cURL certificates\n";
echo "4. If still failing: run quick_order_creation_fix.php\n";
