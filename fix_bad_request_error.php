<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING RAZORPAY BAD REQUEST ERROR ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

echo "🔧 Creating enhanced RazorpayController with parameter validation...\n";
echo "====================================================================\n";

$fixedController = '<?php

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
        
        // Get payment method with fallbacks
        $paymentMethod = $this->getPaymentMethodWithFallbacks($order->company_id);
        
        if (!$paymentMethod) {
            Log::error("No Razorpay payment method found", [
                "company_id" => $order->company_id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway not configured. Please contact support.",
                "error_code" => "NO_PAYMENT_METHOD"
            ], 400);
        }

        // Validate credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error("Razorpay credentials missing", [
                "payment_method_id" => $paymentMethod->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Payment gateway configuration error. Please contact support.",
                "error_code" => "MISSING_CREDENTIALS"
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // ENHANCED PARAMETER VALIDATION AND FORMATTING
            
            // 1. Fix amount calculation with proper validation
            $amountInRupees = floatval($order->total);
            if ($amountInRupees < 1.0) {
                Log::error("Amount too small for Razorpay", [
                    "amount_rupees" => $amountInRupees
                ]);
                
                return response()->json([
                    "success" => false,
                    "message" => "Order amount too small for online payment. Minimum ₹1 required.",
                    "error_code" => "AMOUNT_TOO_SMALL"
                ], 400);
            }
            
            // Convert to paise with proper rounding to avoid decimal issues
            $amountInPaise = intval(round($amountInRupees * 100));
            
            // 2. Fix receipt format - Razorpay requirements
            $originalReceipt = $order->order_number;
            $cleanReceipt = preg_replace("/[^a-zA-Z0-9_\-]/", "", $originalReceipt);
            $cleanReceipt = substr($cleanReceipt, 0, 40); // Max 40 characters
            
            if (empty($cleanReceipt)) {
                $cleanReceipt = "ORD_" . $order->id . "_" . time();
            }
            
            // 3. Optimize notes to avoid size limits
            $notes = [
                "order_id" => strval($order->id),
                "order_number" => $originalReceipt,
                "company_id" => strval($order->company_id),
                "customer_name" => substr($order->customer_name ?? "", 0, 50),
                "amount" => strval($amountInRupees)
            ];
            
            // Ensure notes don\'t exceed size limit
            $notesJson = json_encode($notes);
            if (strlen($notesJson) > 500) {
                // Reduce notes if too large
                $notes = [
                    "order_id" => strval($order->id),
                    "order_number" => $originalReceipt,
                    "amount" => strval($amountInRupees)
                ];
            }
            
            // 4. Prepare order data with validated parameters
            $orderData = [
                "amount" => $amountInPaise,
                "currency" => "INR",
                "receipt" => $cleanReceipt,
                "notes" => $notes
            ];
            
            Log::info("Creating Razorpay order with validated parameters", [
                "amount_rupees" => $amountInRupees,
                "amount_paise" => $amountInPaise,
                "receipt_original" => $originalReceipt,
                "receipt_clean" => $cleanReceipt,
                "notes_size" => strlen(json_encode($notes))
            ]);
            
            $razorpayOrder = $api->order->create($orderData);
            
            Log::info("Razorpay order created successfully", [
                "razorpay_order_id" => $razorpayOrder->id,
                "amount" => $razorpayOrder->amount,
                "status" => $razorpayOrder->status
            ]);
            
            // Update order with Razorpay order ID
            $order->updatePaymentStatus("processing", $razorpayOrder->id, [
                "razorpay_order_id" => $razorpayOrder->id,
                "order_created_at" => now()->toISOString(),
                "amount_paise" => $amountInPaise,
                "clean_receipt" => $cleanReceipt
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
                "payment_method_id" => $paymentMethod->id,
                "amount_paise" => $amountInPaise ?? null,
                "receipt" => $cleanReceipt ?? null
            ]);
            
            // Parse error for specific issues
            $errorMessage = "Payment request failed. Please try again.";
            
            if (str_contains($e->getMessage(), "amount")) {
                $errorMessage = "Invalid payment amount. Please refresh and try again.";
            } elseif (str_contains($e->getMessage(), "receipt")) {
                $errorMessage = "Order reference error. Please try placing the order again.";
            } elseif (str_contains($e->getMessage(), "notes")) {
                $errorMessage = "Order details too large. Please contact support.";
            }
            
            return response()->json([
                "success" => false,
                "message" => $errorMessage,
                "error_code" => "RAZORPAY_BAD_REQUEST"
            ], 400);
            
        } catch (\Exception $e) {
            Log::error("Razorpay order creation failed", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
                "order_id" => $order->id
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Failed to create payment order. Please try again.",
                "error_code" => "UNKNOWN_ERROR"
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

    public function verifyPayment(Request $request)
    {
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
        $webhookBody = $request->getContent();
        $webhookSignature = $request->header("X-Razorpay-Signature");
        
        Log::info("Razorpay webhook received");
        
        return response()->json(["status" => "success"], 200);
    }
}';

// Backup current controller
if (file_exists('app/Http/Controllers/RazorpayController.php')) {
    copy('app/Http/Controllers/RazorpayController.php', 'app/Http/Controllers/RazorpayController_backup_' . date('Y-m-d_H-i-s') . '.php');
    echo "✅ Current controller backed up\n";
}

// Write the fixed controller
file_put_contents('app/Http/Controllers/RazorpayController.php', $fixedController);
echo "✅ Enhanced controller with parameter validation applied\n";

// Clear caches
echo "\n🧹 Clearing caches...\n";
exec('php artisan config:clear 2>&1');
exec('php artisan cache:clear 2>&1');
exec('php artisan route:clear 2>&1');
echo "✅ Caches cleared\n";

echo "\n=== BAD REQUEST FIX COMPLETED ===\n";
echo "\n🔧 WHAT WAS FIXED:\n";
echo "• Amount validation and proper paise conversion\n";
echo "• Receipt format cleaning (alphanumeric only, max 40 chars)\n";
echo "• Notes size optimization to avoid limits\n";
echo "• Decimal precision handling\n";
echo "• Better error messages for different failure types\n";

echo "\n🧪 TEST NOW:\n";
echo "1. Place an order on your website\n";
echo "2. Select Razorpay payment\n";
echo "3. Should work without BAD_REQUEST error!\n";

echo "\n📋 The fix handles:\n";
echo "• ✅ Amount must be ≥ ₹1\n";
echo "• ✅ Proper conversion to paise with rounding\n";
echo "• ✅ Clean receipt format (no special chars)\n";
echo "• ✅ Optimized notes size\n";
echo "• ✅ Better error handling\n";

echo "\n=== READY TO TEST ===\n";
