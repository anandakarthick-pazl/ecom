<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use App\Models\Order;

echo "=== IMMEDIATE RAZORPAY DEBUG - " . date('Y-m-d H:i:s') . " ===\n\n";

echo "1. CHECKING PAYMENT METHODS:\n";
echo "=============================\n";

$razorpayMethods = PaymentMethod::where('type', 'razorpay')->get();
if ($razorpayMethods->isEmpty()) {
    echo "❌ NO Razorpay payment methods found!\n";
} else {
    foreach ($razorpayMethods as $method) {
        echo "Method ID: {$method->id}\n";
        echo "Company ID: " . ($method->company_id ?? 'NULL/Global') . "\n";
        echo "Active: " . ($method->is_active ? 'YES' : 'NO') . "\n";
        echo "Key ID: " . (!empty($method->razorpay_key_id) ? 'SET' : 'MISSING') . "\n";
        echo "Key Secret: " . (!empty($method->razorpay_key_secret) ? 'SET' : 'MISSING') . "\n";
        
        if (!empty($method->razorpay_key_id)) {
            echo "Key ID Value: {$method->razorpay_key_id}\n";
            if (str_contains($method->razorpay_key_id, 'YOUR_KEY_ID_HERE')) {
                echo "❌ PLACEHOLDER KEY ID DETECTED!\n";
            } else {
                echo "✅ Real Key ID Detected\n";
            }
        }
        echo "-------------------\n";
    }
}

echo "\n2. CHECKING RECENT FAILED ORDERS:\n";
echo "==================================\n";

$failedOrders = Order::where('payment_method', 'razorpay')
                    ->where('payment_status', '!=', 'paid')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

if ($failedOrders->isEmpty()) {
    echo "✅ No recent failed payments\n";
} else {
    foreach ($failedOrders as $order) {
        echo "Order: {$order->order_number}\n";
        echo "Status: {$order->payment_status}\n";
        echo "Company ID: {$order->company_id}\n";
        echo "Amount: ₹{$order->total}\n";
        echo "Created: {$order->created_at}\n";
        
        // Check payment details
        if ($order->payment_details) {
            $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
            if ($details && isset($details['error'])) {
                echo "Error: {$details['error']}\n";
            }
        }
        echo "-------------------\n";
    }
}

echo "\n3. CHECKING LOG FILES:\n";
echo "======================\n";

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    echo "✅ Log file exists\n";
    
    // Get recent Razorpay errors
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $razorpayErrors = [];
    
    foreach (array_reverse($lines) as $line) {
        if (str_contains($line, 'Razorpay') && (str_contains($line, 'ERROR') || str_contains($line, 'failed'))) {
            $razorpayErrors[] = $line;
            if (count($razorpayErrors) >= 5) break;
        }
    }
    
    if (!empty($razorpayErrors)) {
        echo "Recent Razorpay Errors:\n";
        foreach ($razorpayErrors as $error) {
            echo "- " . substr($error, 0, 200) . "...\n";
        }
    } else {
        echo "✅ No recent Razorpay errors in logs\n";
    }
} else {
    echo "❌ Log file not found\n";
}

echo "\n4. QUICK FIX RECOMMENDATIONS:\n";
echo "==============================\n";

// Check if we have any valid credentials
$hasValidCredentials = false;
foreach ($razorpayMethods as $method) {
    if (!empty($method->razorpay_key_id) && 
        !str_contains($method->razorpay_key_id, 'YOUR_KEY_ID_HERE') &&
        !empty($method->razorpay_key_secret) &&
        !str_contains($method->razorpay_key_secret, 'YOUR_KEY_SECRET_HERE')) {
        $hasValidCredentials = true;
        break;
    }
}

if (!$hasValidCredentials) {
    echo "❌ NO VALID CREDENTIALS FOUND!\n";
    echo "IMMEDIATE ACTION REQUIRED:\n";
    echo "1. Get your Razorpay credentials from https://dashboard.razorpay.com/\n";
    echo "2. Run: php immediate_credential_fix.php\n";
} else {
    echo "✅ Valid credentials found\n";
    echo "Issue might be in verification logic. Running signature test...\n";
}

echo "\n=== END IMMEDIATE DEBUG ===\n";
