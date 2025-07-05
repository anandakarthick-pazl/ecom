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
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Get Razorpay payment method configuration for the order's company
        $paymentMethod = PaymentMethod::where('type', 'razorpay')
            ->where('is_active', true)
            ->where('company_id', $order->company_id)
            ->first();
            
        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay payment method is not configured'
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            $razorpayOrder = $api->order->create([
                'amount' => $order->total * 100, // Amount in paise
                'currency' => 'INR',
                'receipt' => $order->order_number,
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'company_id' => $order->company_id
                ]
            ]);
            
            // Update order with Razorpay order ID
            $order->updatePaymentStatus('processing', $razorpayOrder->id, [
                'razorpay_order_id' => $razorpayOrder->id
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
            
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order'
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        // Enhanced logging for debugging
        Log::info('Razorpay payment verification started', [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'order_id' => $request->order_id
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
            'current_payment_status' => $order->payment_status
        ]);
        
        // Get Razorpay payment method configuration for the order's company
        $paymentMethod = PaymentMethod::where('type', 'razorpay')
            ->where('is_active', true)
            ->where('company_id', $order->company_id)
            ->first();
            
        // If company-specific method not found, try without company filter as fallback
        if (!$paymentMethod) {
            Log::warning('Company-specific Razorpay method not found, trying fallback', [
                'company_id' => $order->company_id
            ]);
            
            $paymentMethod = PaymentMethod::where('type', 'razorpay')
                ->where('is_active', true)
                ->first();
        }
            
        if (!$paymentMethod) {
            Log::error('No active Razorpay payment method found', [
                'company_id' => $order->company_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Razorpay payment method is not configured. Please contact support.',
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

        // Validate that payment method has required credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error('Razorpay credentials missing', [
                'payment_method_id' => $paymentMethod->id,
                'has_key_id' => !empty($paymentMethod->razorpay_key_id),
                'has_key_secret' => !empty($paymentMethod->razorpay_key_secret)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Razorpay credentials are not properly configured. Please contact support.'
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
                'signature_length' => strlen($request->razorpay_signature)
            ]);
            
            $api->utility->verifyPaymentSignature($attributes);
            
            Log::info('Signature verification successful');
            
            // Fetch payment details
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            
            Log::info('Payment details fetched', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'status' => $payment->status,
                'method' => $payment->method ?? 'unknown'
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
                'verified_at' => now()->toISOString()
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
                'signature_provided' => substr($request->razorpay_signature, 0, 10) . '...'
            ]);
            
            // Update order payment status to failed
            $order->updatePaymentStatus('failed', $request->razorpay_payment_id, [
                'error' => 'Signature verification failed',
                'error_details' => $e->getMessage(),
                'failed_at' => now()->toISOString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. This could be due to invalid payment data or configuration issues. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'payment_method_id' => $paymentMethod->id,
                    'company_id' => $paymentMethod->company_id
                ] : null
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Razorpay payment verification error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id ?? null
            ]);
            
            // Update order payment status to failed
            $order->updatePaymentStatus('failed', $request->razorpay_payment_id, [
                'error' => $e->getMessage(),
                'failed_at' => now()->toISOString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed due to technical error. Please contact support.',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $webhookBody = $request->getContent();
        $webhookSignature = $request->header('X-Razorpay-Signature');
        
        // First, decode the payload to get company info
        $payload = json_decode($webhookBody, true);
        
        // Try to find the company from the order data
        $companyId = null;
        if (isset($payload['payload']['payment']['entity']['notes']['company_id'])) {
            $companyId = $payload['payload']['payment']['entity']['notes']['company_id'];
        } elseif (isset($payload['payload']['order']['entity']['notes']['company_id'])) {
            $companyId = $payload['payload']['order']['entity']['notes']['company_id'];
        }
        
        // Get active Razorpay payment method for the specific company
        $paymentMethod = PaymentMethod::where('type', 'razorpay')
            ->where('is_active', true)
            ->when($companyId, function($query) use ($companyId) {
                return $query->where('company_id', $companyId);
            })
            ->first();
            
        if (!$paymentMethod || !$paymentMethod->razorpay_webhook_secret) {
            Log::error('Razorpay webhook secret not configured');
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
            
            Log::info('Razorpay webhook received', ['event' => $event]);
            
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
            }
            
            return response()->json(['status' => 'success'], 200);
            
        } catch (\Exception $e) {
            Log::error('Razorpay webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 400);
        }
    }

    private function handlePaymentCaptured($payment)
    {
        // Find order by Razorpay order ID
        $order = Order::where('payment_details->razorpay_order_id', $payment['order_id'])->first();
        
        if ($order && $order->payment_status !== 'paid') {
            $order->updatePaymentStatus('paid', $payment['id'], [
                'captured_at' => now(),
                'amount_captured' => $payment['amount'] / 100
            ]);
        }
    }

    private function handlePaymentFailed($payment)
    {
        // Find order by Razorpay order ID
        $order = Order::where('payment_details->razorpay_order_id', $payment['order_id'])->first();
        
        if ($order) {
            $order->updatePaymentStatus('failed', $payment['id'], [
                'failed_at' => now(),
                'error_code' => $payment['error_code'] ?? null,
                'error_description' => $payment['error_description'] ?? null
            ]);
        }
    }

    private function handleRefundCreated($refund)
    {
        // Find order by payment ID
        $order = Order::where('payment_transaction_id', $refund['payment_id'])->first();
        
        if ($order) {
            $order->updatePaymentStatus('refunded', $refund['id'], [
                'refunded_at' => now(),
                'refund_id' => $refund['id'],
                'refund_amount' => $refund['amount'] / 100
            ]);
        }
    }
}
