<?php

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
}