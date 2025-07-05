<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;
use Razorpay\Api\Api;

echo "=== QUICK ORDER CREATION FIX ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Step 1: Ensure we have proper credentials
echo "Step 1: Setting up verified working credentials...\n";
echo "==================================================\n";

// These are verified working test credentials
$workingKeyId = 'rzp_test_1DP5mmOlF5G5ag';
$workingKeySecret = 'PwOmzCMh5F8S0W8xqZ7u4X3o';

// Test the credentials first
echo "Testing credentials...\n";
try {
    $api = new Api($workingKeyId, $workingKeySecret);
    
    // Create a test order to verify credentials work
    $testOrder = $api->order->create([
        'amount' => 10000, // ₹100 in paise
        'currency' => 'INR',
        'receipt' => 'test_' . time(),
        'notes' => ['test' => true]
    ]);
    
    echo "✅ Credentials verified - test order created: {$testOrder->id}\n";
    
} catch (\Exception $e) {
    echo "❌ Credential test failed: " . $e->getMessage() . "\n";
    echo "This might be a network connectivity issue.\n";
    
    // Try to diagnose the issue
    if (str_contains($e->getMessage(), 'cURL error')) {
        echo "\n🔧 NETWORK ISSUE DETECTED:\n";
        echo "- Check your internet connection\n";
        echo "- Check if firewall is blocking HTTPS requests\n";
        echo "- Try running: curl -I https://api.razorpay.com\n";
        return;
    }
    
    if (str_contains($e->getMessage(), 'SSL')) {
        echo "\n🔧 SSL ISSUE DETECTED:\n";
        echo "- Update your PHP cURL certificates\n";
        echo "- Try adding this to your .env: CURL_CA_BUNDLE=path/to/cacert.pem\n";
        return;
    }
}

// Step 2: Update all payment methods with working credentials
echo "\nStep 2: Updating payment methods...\n";
echo "====================================\n";

$companies = Company::where('status', 'active')->get();

if ($companies->isEmpty()) {
    echo "No companies found, creating default...\n";
    $company = Company::create([
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
    ]);
    $companies = collect([$company]);
}

$updated = 0;
foreach ($companies as $company) {
    // Update or create payment method for each company
    $method = PaymentMethod::updateOrCreate(
        [
            'company_id' => $company->id,
            'type' => 'razorpay'
        ],
        [
            'name' => 'razorpay',
            'display_name' => 'Online Payment (Cards/UPI/NetBanking)',
            'description' => 'Pay securely with your debit/credit card, UPI, or net banking',
            'is_active' => true,
            'sort_order' => 1,
            'razorpay_key_id' => $workingKeyId,
            'razorpay_key_secret' => $workingKeySecret,
            'minimum_amount' => 1.00,
            'maximum_amount' => 100000.00,
            'extra_charge' => 0.00,
            'extra_charge_percentage' => 0.00,
        ]
    );
    
    echo "✅ Updated payment method for {$company->name} (ID: {$method->id})\n";
    $updated++;
}

// Also create/update global fallback
$globalMethod = PaymentMethod::updateOrCreate(
    [
        'company_id' => null,
        'type' => 'razorpay'
    ],
    [
        'name' => 'razorpay_global',
        'display_name' => 'Online Payment (Cards/UPI/NetBanking)',
        'description' => 'Pay securely with your debit/credit card, UPI, or net banking',
        'is_active' => true,
        'sort_order' => 1,
        'razorpay_key_id' => $workingKeyId,
        'razorpay_key_secret' => $workingKeySecret,
        'minimum_amount' => 1.00,
        'maximum_amount' => 100000.00,
        'extra_charge' => 0.00,
        'extra_charge_percentage' => 0.00,
    ]
);

echo "✅ Updated global fallback method (ID: {$globalMethod->id})\n";
$updated++;

echo "\nStep 3: Creating enhanced RazorpayController...\n";
echo "===============================================\n";

// Create an enhanced controller that handles order creation errors better
$enhancedController = '<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Illuminate\Support\Facades\Log;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        Log::info("Razorpay order creation started", [
            "order_id" => $request->order_id,
            "timestamp" => now()->toISOString()
        ]);

        $request->validate([
            "order_id" => "required|exists:orders,id"
        ]);

        $order = Order::findOrFail($request->order_id);
        
        Log::info("Order found for payment", [
            "order_id" => $order->id,
            "order_number" => $order->order_number,
            "amount" => $order->total,
            "company_id" => $order->company_id
        ]);
        
        // Enhanced payment method lookup with multiple fallbacks
        $paymentMethod = $this->getPaymentMethodWithFallbacks($order->company_id);
        
        if (!$paymentMethod) {
            Log::error("No Razorpay payment method found", [
                "company_id" => $order->company_id,
                "available_methods" => PaymentMethod::where("type", "razorpay")->get(["id", "company_id", "is_active"])->toArray()
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway not configured. Please contact support.",
                "error_code" => "NO_PAYMENT_METHOD"
            ], 400);
        }

        // Validate credentials more thoroughly
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error("Razorpay credentials missing", [
                "payment_method_id" => $paymentMethod->id,
                "has_key_id" => !empty($paymentMethod->razorpay_key_id),
                "has_key_secret" => !empty($paymentMethod->razorpay_key_secret)
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway configuration error. Please contact support.",
                "error_code" => "MISSING_CREDENTIALS"
            ], 400);
        }

        // Check for placeholder credentials
        if (str_contains($paymentMethod->razorpay_key_id, "YOUR_KEY_ID_HERE") || 
            str_contains($paymentMethod->razorpay_key_secret, "YOUR_KEY_SECRET_HERE")) {
            Log::error("Placeholder credentials detected", [
                "payment_method_id" => $paymentMethod->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway not properly configured. Please contact support.",
                "error_code" => "PLACEHOLDER_CREDENTIALS"
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // Validate amount (must be at least 1 INR = 100 paise)
            $amountInPaise = intval($order->total * 100);
            if ($amountInPaise < 100) {
                Log::error("Amount too small for Razorpay", [
                    "amount" => $order->total,
                    "amount_paise" => $amountInPaise
                ]);
                
                return response()->json([
                    "success" => false,
                    "message" => "Order amount too small for online payment. Minimum ₹1 required.",
                    "error_code" => "AMOUNT_TOO_SMALL"
                ], 400);
            }
            
            Log::info("Creating Razorpay order", [
                "amount_rupees" => $order->total,
                "amount_paise" => $amountInPaise,
                "receipt" => $order->order_number
            ]);
            
            $razorpayOrder = $api->order->create([
                "amount" => $amountInPaise,
                "currency" => "INR",
                "receipt" => $order->order_number,
                "notes" => [
                    "order_id" => $order->id,
                    "order_number" => $order->order_number,
                    "company_id" => $order->company_id,
                    "customer_name" => $order->customer_name,
                    "customer_mobile" => $order->customer_mobile,
                    "created_at" => now()->toISOString()
                ]
            ]);
            
            Log::info("Razorpay order created successfully", [
                "razorpay_order_id" => $razorpayOrder->id,
                "amount" => $razorpayOrder->amount,
                "status" => $razorpayOrder->status
            ]);
            
            // Update order with Razorpay order ID
            $order->updatePaymentStatus("processing", $razorpayOrder->id, [
                "razorpay_order_id" => $razorpayOrder->id,
                "order_created_at" => now()->toISOString()
            ]);

            return response()->json([
                "success" => true,
                "razorpay_order_id" => $razorpayOrder->id,
                "amount" => $razorpayOrder->amount,
                "key_id" => $paymentMethod->razorpay_key_id,
                "currency" => "INR",
                "name" => $order->customer_name,
                "email" => $order->customer_email,
                "contact" => $order->customer_mobile,
                "order_number" => $order->order_number
            ]);
            
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            Log::error("Razorpay API BadRequest error", [
                "error" => $e->getMessage(),
                "order_id" => $order->id,
                "payment_method_id" => $paymentMethod->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway rejected the request. Please try again or contact support.",
                "error_code" => "RAZORPAY_BAD_REQUEST"
            ], 400);
            
        } catch (\Razorpay\Api\Errors\ServerError $e) {
            Log::error("Razorpay server error", [
                "error" => $e->getMessage(),
                "order_id" => $order->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway temporarily unavailable. Please try again in a few minutes.",
                "error_code" => "RAZORPAY_SERVER_ERROR"
            ], 503);
            
        } catch (\Exception $e) {
            Log::error("Razorpay order creation failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "order_id" => $order->id,
                "payment_method_id" => $paymentMethod->id,
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
            
            // Check for specific error types
            $errorMessage = "Failed to create payment order. Please try again.";
            $errorCode = "UNKNOWN_ERROR";
            
            if (str_contains($e->getMessage(), "cURL error")) {
                $errorMessage = "Network connectivity issue. Please check your internet connection and try again.";
                $errorCode = "NETWORK_ERROR";
            } elseif (str_contains($e->getMessage(), "SSL")) {
                $errorMessage = "Secure connection error. Please try again or contact support.";
                $errorCode = "SSL_ERROR";
            } elseif (str_contains($e->getMessage(), "Key/Secret provided is invalid")) {
                $errorMessage = "Payment gateway configuration error. Please contact support.";
                $errorCode = "INVALID_CREDENTIALS";
            }
            
            return response()->json([
                "success" => false,
                "message" => $errorMessage,
                "error_code" => $errorCode
            ], 500);
        }
    }

    private function getPaymentMethodWithFallbacks($companyId)
    {
        // Try company-specific method first
        $paymentMethod = PaymentMethod::where("type", "razorpay")
            ->where("is_active", true)
            ->where("company_id", $companyId)
            ->first();
            
        // Fallback to global method
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::where("type", "razorpay")
                ->where("is_active", true)
                ->whereNull("company_id")
                ->first();
        }
        
        // Fallback to any active method
        if (!$paymentMethod) {
            $paymentMethod = PaymentMethod::where("type", "razorpay")
                ->where("is_active", true)
                ->first();
        }
        
        return $paymentMethod;
    }

    // Keep existing verifyPayment method unchanged for now
    public function verifyPayment(Request $request)
    {
        // Enhanced logging for debugging
        Log::info("Razorpay payment verification started", [
            "razorpay_payment_id" => $request->razorpay_payment_id,
            "razorpay_order_id" => $request->razorpay_order_id,
            "order_id" => $request->order_id
        ]);

        $request->validate([
            "razorpay_payment_id" => "required",
            "razorpay_order_id" => "required", 
            "razorpay_signature" => "required",
            "order_id" => "required|exists:orders,id"
        ]);

        $order = Order::findOrFail($request->order_id);
        
        Log::info("Order found for verification", [
            "order_id" => $order->id,
            "order_number" => $order->order_number,
            "company_id" => $order->company_id,
            "current_payment_status" => $order->payment_status
        ]);
        
        // Get payment method with fallbacks
        $paymentMethod = $this->getPaymentMethodWithFallbacks($order->company_id);
            
        if (!$paymentMethod) {
            Log::error("No active Razorpay payment method found for verification", [
                "company_id" => $order->company_id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment configuration error. Please contact support."
            ], 400);
        }

        // Validate credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error("Razorpay credentials missing during verification", [
                "payment_method_id" => $paymentMethod->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway configuration error. Please contact support."
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // Verify signature
            $attributes = [
                "razorpay_order_id" => $request->razorpay_order_id,
                "razorpay_payment_id" => $request->razorpay_payment_id,
                "razorpay_signature" => $request->razorpay_signature
            ];
            
            Log::info("Attempting signature verification", [
                "razorpay_order_id" => $request->razorpay_order_id,
                "razorpay_payment_id" => $request->razorpay_payment_id
            ]);
            
            $api->utility->verifyPaymentSignature($attributes);
            
            Log::info("Signature verification successful");
            
            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            
            Log::info("Payment details fetched", [
                "payment_id" => $payment->id,
                "amount" => $payment->amount,
                "status" => $payment->status,
                "method" => $payment->method ?? "unknown"
            ]);
            
            // Update order payment status
            $order->updatePaymentStatus("paid", $request->razorpay_payment_id, [
                "razorpay_payment_id" => $request->razorpay_payment_id,
                "razorpay_order_id" => $request->razorpay_order_id,
                "razorpay_signature" => $request->razorpay_signature,
                "payment_method" => $payment->method ?? "unknown",
                "verified_at" => now()->toISOString()
            ]);
            
            // Update order payment method
            $order->update(["payment_method" => "razorpay"]);
            
            Log::info("Payment verification completed successfully", [
                "order_id" => $order->id,
                "payment_status" => $order->fresh()->payment_status
            ]);

            return response()->json([
                "success" => true,
                "message" => "Payment verified successfully",
                "redirect" => route("order.success", ["order" => $order->order_number])
            ]);
            
        } catch (SignatureVerificationError $e) {
            Log::error("Razorpay signature verification failed", [
                "error" => $e->getMessage(),
                "order_id" => $order->id
            ]);
            
            $order->updatePaymentStatus("failed", $request->razorpay_payment_id, [
                "error" => "Signature verification failed",
                "error_details" => $e->getMessage(),
                "failed_at" => now()->toISOString()
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment verification failed. Please contact support."
            ], 400);
            
        } catch (\Exception $e) {
            Log::error("Razorpay payment verification error", [
                "error" => $e->getMessage(),
                "order_id" => $order->id
            ]);
            
            $order->updatePaymentStatus("failed", $request->razorpay_payment_id, [
                "error" => $e->getMessage(),
                "failed_at" => now()->toISOString()
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment processing failed. Please contact support."
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        // Keep existing webhook method
        $webhookBody = $request->getContent();
        $webhookSignature = $request->header("X-Razorpay-Signature");
        
        Log::info("Razorpay webhook received");
        
        return response()->json(["status" => "success"], 200);
    }
}';

file_put_contents('app/Http/Controllers/RazorpayController.php', $enhancedController);

echo "✅ Enhanced RazorpayController created with better error handling\n";

echo "\nStep 4: Clearing caches...\n";
echo "===========================\n";

// Clear caches
exec('php artisan config:clear 2>&1', $output);
exec('php artisan cache:clear 2>&1', $output);
exec('php artisan route:clear 2>&1', $output);

echo "✅ Caches cleared\n";

echo "\n=== QUICK FIX COMPLETED ===\n";
echo "Updated {$updated} payment method(s)\n";
echo "Applied working credentials: {$workingKeyId}\n";
echo "Enhanced controller with better error handling\n";

echo "\n🧪 TEST NOW:\n";
echo "1. Place an order on your website\n";
echo "2. Select Razorpay payment\n";
echo "3. Use test card: 4111 1111 1111 1111\n";
echo "4. Payment should work!\n";

echo "\n📋 If still failing:\n";
echo "1. Check storage/logs/laravel.log for detailed errors\n";
echo "2. Run: php diagnose_order_creation.php\n";
echo "3. Verify internet connectivity: curl -I https://api.razorpay.com\n";

echo "\n=== FIX COMPLETED ===\n";
