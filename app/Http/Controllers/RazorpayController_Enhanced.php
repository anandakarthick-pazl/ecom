<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Illuminate\Support\Facades\Log;
use Exception;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        // Enhanced logging for debugging
        Log::info('Razorpay order creation started', [
            'order_id' => $request->order_id,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        Log::info('Order found for payment', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->total,
            'company_id' => $order->company_id
        ]);
        
        // Enhanced payment method lookup with better fallback logic
        $paymentMethod = $this->getPaymentMethod($order->company_id);
        
        if (!$paymentMethod) {
            Log::error('No Razorpay payment method found', [
                'company_id' => $order->company_id,
                'available_methods' => PaymentMethod::where('type', 'razorpay')->get(['id', 'company_id', 'is_active'])->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Razorpay payment method is not configured for this store. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'company_id' => $order->company_id,
                    'available_methods' => PaymentMethod::where('type', 'razorpay')->get(['id', 'company_id', 'is_active'])
                ] : null
            ], 400);
        }

        // Validate credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error('Razorpay credentials missing', [
                'payment_method_id' => $paymentMethod->id,
                'has_key_id' => !empty($paymentMethod->razorpay_key_id),
                'has_key_secret' => !empty($paymentMethod->razorpay_key_secret)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway configuration error. Please contact support.'
            ], 400);
        }

        // Check for placeholder credentials
        if (str_contains($paymentMethod->razorpay_key_id, 'YOUR_KEY_ID_HERE') || 
            str_contains($paymentMethod->razorpay_key_secret, 'YOUR_KEY_SECRET_HERE')) {
            Log::error('Placeholder Razorpay credentials detected', [
                'payment_method_id' => $paymentMethod->id,
                'key_id' => $paymentMethod->razorpay_key_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway is not properly configured. Please contact support.'
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            Log::info('Creating Razorpay order', [
                'amount_paisa' => $order->total * 100,
                'order_number' => $order->order_number
            ]);
            
            $razorpayOrder = $api->order->create([
                'amount' => $order->total * 100, // Amount in paise
                'currency' => 'INR',
                'receipt' => $order->order_number,
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'company_id' => $order->company_id,
                    'customer_name' => $order->customer_name,
                    'customer_mobile' => $order->customer_mobile
                ]
            ]);
            
            Log::info('Razorpay order created successfully', [
                'razorpay_order_id' => $razorpayOrder->id,
                'amount' => $razorpayOrder->amount,
                'status' => $razorpayOrder->status
            ]);
            
            // Update order with Razorpay order ID
            $order->updatePaymentStatus('processing', $razorpayOrder->id, [
                'razorpay_order_id' => $razorpayOrder->id,
                'payment_initiated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder->id,
                'amount' => $razorpayOrder->amount,
                'key_id' => $paymentMethod->razorpay_key_id,
                'currency' => 'INR',
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'contact' => $order->customer_mobile,
                'order_number' => $order->order_number
            ]);
            
        } catch (Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order. Please try again or contact support.',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'payment_method_id' => $paymentMethod->id
                ] : null
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        // Enhanced logging for debugging
        Log::info('Razorpay payment verification started', [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'order_id' => $request->order_id,
            'has_signature' => !empty($request->razorpay_signature),
            'signature_length' => strlen($request->razorpay_signature ?? ''),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);

        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        Log::info('Order found for verification', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'company_id' => $order->company_id,
            'current_payment_status' => $order->payment_status,
            'total_amount' => $order->total
        ]);
        
        // Enhanced payment method lookup
        $paymentMethod = $this->getPaymentMethod($order->company_id);
            
        if (!$paymentMethod) {
            Log::error('No Razorpay payment method found for verification', [
                'company_id' => $order->company_id,
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment configuration error. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'company_id' => $order->company_id,
                    'available_methods' => PaymentMethod::where('type', 'razorpay')->get(['id', 'company_id', 'is_active'])
                ] : null
            ], 400);
        }
        
        Log::info('Payment method found for verification', [
            'payment_method_id' => $paymentMethod->id,
            'company_id' => $paymentMethod->company_id,
            'has_key_id' => !empty($paymentMethod->razorpay_key_id),
            'has_key_secret' => !empty($paymentMethod->razorpay_key_secret)
        ]);

        // Validate credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error('Razorpay credentials missing during verification', [
                'payment_method_id' => $paymentMethod->id,
                'has_key_id' => !empty($paymentMethod->razorpay_key_id),
                'has_key_secret' => !empty($paymentMethod->razorpay_key_secret)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway configuration error. Please contact support.'
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];
            
            Log::info('Attempting signature verification', [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'signature_length' => strlen($request->razorpay_signature),
                'key_id_prefix' => substr($paymentMethod->razorpay_key_id, 0, 12) . '...'
            ]);
            
            $api->utility->verifyPaymentSignature($attributes);
            
            Log::info('Signature verification successful');
            
            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            
            Log::info('Payment details fetched', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'method' => $payment->method ?? 'unknown',
                'captured' => $payment->captured ?? false
            ]);
            
            // Update order payment status
            $order->updatePaymentStatus('paid', $request->razorpay_payment_id, [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_method' => $payment->method ?? 'unknown',
                'card_id' => $payment->card_id ?? null,
                'bank' => $payment->bank ?? null,
                'wallet' => $payment->wallet ?? null,
                'vpa' => $payment->vpa ?? null,
                'email' => $payment->email ?? null,
                'contact' => $payment->contact ?? null,
                'verified_at' => now()->toISOString(),
                'payment_captured' => $payment->captured ?? false,
                'payment_amount' => $payment->amount ?? 0
            ]);
            
            // Update order payment method
            $order->update(['payment_method' => 'razorpay']);
            
            Log::info('Payment verification completed successfully', [
                'order_id' => $order->id,
                'payment_status' => $order->fresh()->payment_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'redirect' => route('order.success', ['order' => $order->order_number])
            ]);
            
        } catch (SignatureVerificationError $e) {
            Log::error('Razorpay signature verification failed', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'signature_provided' => substr($request->razorpay_signature, 0, 10) . '...',
                'key_id_used' => substr($paymentMethod->razorpay_key_id, 0, 12) . '...'
            ]);
            
            // Update order payment status to failed
            $order->updatePaymentStatus('failed', $request->razorpay_payment_id, [
                'error' => 'Signature verification failed',
                'error_details' => $e->getMessage(),
                'failed_at' => now()->toISOString(),
                'verification_data' => [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'signature_length' => strlen($request->razorpay_signature)
                ]
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. This could be due to invalid payment data or configuration issues. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'payment_method_id' => $paymentMethod->id,
                    'company_id' => $paymentMethod->company_id,
                    'key_id_prefix' => substr($paymentMethod->razorpay_key_id, 0, 12) . '...'
                ] : null
            ], 400);
            
        } catch (Exception $e) {
            Log::error('Razorpay payment verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Update order payment status to failed
            $order->updatePaymentStatus('failed', $request->razorpay_payment_id, [
                'error' => $e->getMessage(),
                'failed_at' => now()->toISOString(),
                'error_type' => get_class($e)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed due to technical error. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'type' => get_class($e)
                ] : null
            ], 500);
        }
    }

    /**
     * Enhanced payment method lookup with better fallback logic
     */
    private function getPaymentMethod($companyId)
    {
        // Try to get company-specific method first
        $paymentMethod = PaymentMethod::where('type', 'razorpay')
            ->where('is_active', true)
            ->where('company_id', $companyId)
            ->first();
            
        // If company-specific method not found, try global method
        if (!$paymentMethod) {
            Log::warning('Company-specific Razorpay method not found, trying global fallback', [
                'company_id' => $companyId
            ]);
            
            $paymentMethod = PaymentMethod::where('type', 'razorpay')
                ->where('is_active', true)
                ->whereNull('company_id')
                ->first();
        }
        
        // If still not found, try any active Razorpay method as last resort
        if (!$paymentMethod) {
            Log::warning('No global Razorpay method found, trying any active method', [
                'company_id' => $companyId
            ]);
            
            $paymentMethod = PaymentMethod::where('type', 'razorpay')
                ->where('is_active', true)
                ->first();
        }
        
        return $paymentMethod;
    }

    public function webhook(Request $request)
    {
        $webhookBody = $request->getContent();
        $webhookSignature = $request->header('X-Razorpay-Signature');
        
        Log::info('Razorpay webhook received', [
            'signature_present' => !empty($webhookSignature),
            'body_length' => strlen($webhookBody),
            'ip' => $request->ip()
        ]);
        
        // First, decode the payload to get company info
        $payload = json_decode($webhookBody, true);
        
        if (!$payload) {
            Log::error('Invalid webhook payload received');
            return response()->json(['status' => 'error'], 400);
        }
        
        // Try to find the company from the order data
        $companyId = null;
        if (isset($payload['payload']['payment']['entity']['notes']['company_id'])) {
            $companyId = $payload['payload']['payment']['entity']['notes']['company_id'];
        } elseif (isset($payload['payload']['order']['entity']['notes']['company_id'])) {
            $companyId = $payload['payload']['order']['entity']['notes']['company_id'];
        }
        
        // Get payment method for webhook verification
        $paymentMethod = $this->getPaymentMethod($companyId);
            
        if (!$paymentMethod || !$paymentMethod->razorpay_webhook_secret) {
            Log::error('Razorpay webhook secret not configured', [
                'company_id' => $companyId,
                'payment_method_found' => !is_null($paymentMethod),
                'has_webhook_secret' => $paymentMethod ? !empty($paymentMethod->razorpay_webhook_secret) : false
            ]);
            return response()->json(['status' => 'error'], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // Verify webhook signature
            $api->utility->verifyWebhookSignature(
                $webhookBody,
                $webhookSignature,
                $paymentMethod->razorpay_webhook_secret
            );
            
            $event = $payload['event'];
            
            Log::info('Razorpay webhook verified', [
                'event' => $event,
                'company_id' => $companyId
            ]);
            
            switch ($event) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($payload['payload']['payment']['entity']);
                    break;
                    
                case 'payment.failed':
                    $this->handlePaymentFailed($payload['payload']['payment']['entity']);
                    break;
                    
                case 'refund.created':
                    $this->handleRefundCreated($payload['payload']['refund']['entity']);
                    break;
                    
                default:
                    Log::info('Unhandled webhook event', ['event' => $event]);
            }
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (Exception $e) {
            Log::error('Razorpay webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error'], 400);
        }
    }

    private function handlePaymentCaptured($payment)
    {
        // Find order by Razorpay order ID
        $order = Order::where('payment_details->razorpay_order_id', $payment['order_id'])->first();
        
        if ($order && $order->payment_status !== 'paid') {
            Log::info('Webhook: Payment captured', [
                'order_id' => $order->id,
                'payment_id' => $payment['id'],
                'amount' => $payment['amount']
            ]);
            
            $order->updatePaymentStatus('paid', $payment['id'], [
                'captured_at' => now(),
                'amount_captured' => $payment['amount'] / 100,
                'webhook_received' => true
            ]);
        }
    }

    private function handlePaymentFailed($payment)
    {
        // Find order by Razorpay order ID
        $order = Order::where('payment_details->razorpay_order_id', $payment['order_id'])->first();
        
        if ($order) {
            Log::info('Webhook: Payment failed', [
                'order_id' => $order->id,
                'payment_id' => $payment['id'],
                'error_code' => $payment['error_code'] ?? null
            ]);
            
            $order->updatePaymentStatus('failed', $payment['id'], [
                'failed_at' => now(),
                'error_code' => $payment['error_code'] ?? null,
                'error_description' => $payment['error_description'] ?? null,
                'webhook_received' => true
            ]);
        }
    }

    private function handleRefundCreated($refund)
    {
        // Find order by payment ID
        $order = Order::where('payment_transaction_id', $refund['payment_id'])->first();
        
        if ($order) {
            Log::info('Webhook: Refund created', [
                'order_id' => $order->id,
                'refund_id' => $refund['id'],
                'amount' => $refund['amount']
            ]);
            
            $order->updatePaymentStatus('refunded', $refund['id'], [
                'refunded_at' => now(),
                'refund_id' => $refund['id'],
                'refund_amount' => $refund['amount'] / 100,
                'webhook_received' => true
            ]);
        }
    }
}
