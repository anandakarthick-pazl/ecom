<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\PaymentMethod;
use Razorpay\Api\Api;

echo "=== RAZORPAY BAD REQUEST DEBUG ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

echo "🔍 Analyzing RAZORPAY_BAD_REQUEST error...\n";
echo "==========================================\n";

// Get recent orders that might be causing the issue
$recentOrders = Order::where('created_at', '>=', now()->subHours(1))
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();

if ($recentOrders->isEmpty()) {
    echo "No recent orders found. Creating test scenario...\n";
    
    // Create a test order to analyze
    $testOrder = new Order([
        'total' => 100.50,
        'order_number' => 'TEST_' . time(),
        'customer_name' => 'Test Customer',
        'customer_mobile' => '9876543210',
        'company_id' => 1
    ]);
    $recentOrders = collect([$testOrder]);
}

foreach ($recentOrders as $order) {
    echo "\n📋 ORDER ANALYSIS:\n";
    echo "==================\n";
    echo "Order Number: {$order->order_number}\n";
    echo "Total Amount: ₹{$order->total}\n";
    echo "Amount in Paise: " . intval($order->total * 100) . "\n";
    
    // Check common issues
    $issues = [];
    
    // 1. Amount validation
    $amountInPaise = intval($order->total * 100);
    if ($amountInPaise < 100) {
        $issues[] = "❌ Amount too small: {$amountInPaise} paise (min 100 paise = ₹1)";
    } elseif ($amountInPaise != ($order->total * 100)) {
        $issues[] = "⚠️ Amount precision issue: " . ($order->total * 100) . " → {$amountInPaise}";
    } else {
        echo "✅ Amount: {$amountInPaise} paise (₹{$order->total})\n";
    }
    
    // 2. Receipt validation
    if (strlen($order->order_number) > 40) {
        $issues[] = "❌ Receipt too long: " . strlen($order->order_number) . " chars (max 40)";
    } elseif (preg_match('/[^a-zA-Z0-9_\-]/', $order->order_number)) {
        $issues[] = "❌ Invalid receipt characters in: {$order->order_number}";
    } else {
        echo "✅ Receipt: {$order->order_number} (" . strlen($order->order_number) . " chars)\n";
    }
    
    // 3. Currency check
    echo "✅ Currency: INR\n";
    
    // 4. Notes validation
    $notes = [
        'order_id' => $order->id ?? 'test',
        'order_number' => $order->order_number,
        'company_id' => $order->company_id ?? 1,
        'customer_name' => $order->customer_name ?? 'Test',
        'customer_mobile' => $order->customer_mobile ?? '9876543210',
        'created_at' => now()->toISOString()
    ];
    
    $notesJson = json_encode($notes);
    if (strlen($notesJson) > 512) {
        $issues[] = "❌ Notes too large: " . strlen($notesJson) . " chars (max 512)";
    } else {
        echo "✅ Notes: " . strlen($notesJson) . " chars\n";
    }
    
    if (!empty($issues)) {
        echo "\n🚨 ISSUES FOUND:\n";
        foreach ($issues as $issue) {
            echo "   {$issue}\n";
        }
    } else {
        echo "\n✅ All parameters look valid for this order\n";
    }
    
    // Try to create actual order with Razorpay to see exact error
    echo "\n🧪 TESTING API CALL:\n";
    echo "===================\n";
    
    $paymentMethod = PaymentMethod::where('type', 'razorpay')->where('is_active', true)->first();
    
    if ($paymentMethod) {
        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            $orderData = [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $order->order_number,
                'notes' => $notes
            ];
            
            echo "Request Data:\n";
            echo json_encode($orderData, JSON_PRETTY_PRINT) . "\n\n";
            
            $razorpayOrder = $api->order->create($orderData);
            
            echo "✅ SUCCESS! Order created: {$razorpayOrder->id}\n";
            
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            echo "❌ RAZORPAY BAD REQUEST ERROR:\n";
            echo "Error: " . $e->getMessage() . "\n";
            
            // Parse specific error details
            $errorData = json_decode($e->getMessage(), true);
            if ($errorData && isset($errorData['error'])) {
                echo "Code: " . ($errorData['error']['code'] ?? 'unknown') . "\n";
                echo "Description: " . ($errorData['error']['description'] ?? 'none') . "\n";
                echo "Field: " . ($errorData['error']['field'] ?? 'none') . "\n";
                echo "Source: " . ($errorData['error']['source'] ?? 'none') . "\n";
            }
            
            // Common fixes based on error
            if (str_contains($e->getMessage(), 'amount')) {
                echo "\n💡 FIX: Check amount calculation\n";
                echo "   Current: {$amountInPaise} paise\n";
                echo "   Try: " . max(100, $amountInPaise) . " paise\n";
            }
            
            if (str_contains($e->getMessage(), 'receipt')) {
                echo "\n💡 FIX: Simplify receipt format\n";
                echo "   Current: {$order->order_number}\n";
                echo "   Try: " . preg_replace('/[^a-zA-Z0-9]/', '', $order->order_number) . "\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ OTHER ERROR: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ No payment method found for testing\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
}

echo "\n🔧 COMMON SOLUTIONS:\n";
echo "===================\n";
echo "1. Amount issues: Ensure amount is ≥ ₹1 and properly converted to paise\n";
echo "2. Receipt issues: Use only alphanumeric, underscore, hyphen (max 40 chars)\n";
echo "3. Notes too large: Reduce notes data if > 512 characters\n";
echo "4. Decimal precision: Use intval() for amount conversion\n";
echo "\nNext: Run fix_bad_request_error.php to apply automatic fixes\n";
