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
    /**
     * Initiate payment page that auto-redirects to Razorpay
     */
    public function initiatePayment($order_id)
    {
        $order = Order::findOrFail($order_id);
        
        // Security check: Only allow payment for pending orders
        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', $order->order_number)
                           ->with('info', 'This order has already been paid.');
        }
        
        // Check which theme to use
        $theme = \App\Models\AppSetting::get('store_theme', 'default');
        $host = request()->getHost();
        
        // Determine the view based on theme
        $viewName = 'razorpay-payment';
        if ($host === 'greenvalleyherbs.local' || request()->get('theme') === 'foodie') {
            $viewName = 'razorpay-payment-foodie';
        } elseif (request()->get('theme') === 'fabric' || $theme === 'fabric') {
            $viewName = 'razorpay-payment-fabric';
        }
        
        return view($viewName, compact('order'));
    }
    
    public function createOrder(Request $request)
    {
        // Handle both JSON and form-encoded requests
        if ($request->isJson() || $request->wantsJson()) {
            $data = $request->json()->all();
            $request->merge($data);
        }
        // Enhanced logging for debugging
        Log::info('Razorpay order creation started', [
            'order_id' => $request->order_id,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        Log::info('Order found for payment', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $order->total,
            'company_id' => $order->company_id,
            'current_payment_status' => $order->payment_status
        ]);
        
        // Get Razorpay payment method configuration for the order's company
        $paymentMethod = PaymentMethod::where('type', 'razorpay')
            ->where('is_active', true)
            ->where('company_id', $order->company_id)
            ->first();
            
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
                'company_id' => $order->company_id,
                'available_methods' => PaymentMethod::where('type', 'razorpay')->get(['id', 'company_id', 'is_active'])->toArray()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not configured. Please contact support.',
                'error_code' => 'NO_PAYMENT_METHOD'
            ], 400);
        }

        // Validate that payment method has required credentials
        if (empty($paymentMethod->razorpay_key_id) || empty($paymentMethod->razorpay_key_secret)) {
            Log::error('Razorpay credentials missing', [
                'payment_method_id' => $paymentMethod->id,
                'has_key_id' => !empty($paymentMethod->razorpay_key_id),
                'has_key_secret' => !empty($paymentMethod->razorpay_key_secret)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway configuration error. Please contact support.',
                'error_code' => 'MISSING_CREDENTIALS'
            ], 400);
        }

        try {
            $api = new Api($paymentMethod->razorpay_key_id, $paymentMethod->razorpay_key_secret);
            
            // CRITICAL: Fix amount calculation to prevent BAD_REQUEST
            $amountInRupees = floatval($order->total);
            
            // Validate minimum amount (Razorpay requires at least ₹1)
            if ($amountInRupees < 1.0) {
                Log::error('Amount too small for Razorpay', [
                    'amount_rupees' => $amountInRupees,
                    'minimum_required' => 1.0
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Order amount too small for online payment. Minimum ₹1 required.',
                    'error_code' => 'AMOUNT_TOO_SMALL'
                ], 400);
            }
            
            // Convert to paise with proper rounding to avoid decimal precision issues
            $amountInPaise = intval(round($amountInRupees * 100));
            
            // CRITICAL: Fix receipt format to prevent BAD_REQUEST
            $originalReceipt = $order->order_number;
            
            // Clean receipt: only alphanumeric, underscore, hyphen allowed
            $cleanReceipt = preg_replace('/[^a-zA-Z0-9_\-]/', '', $originalReceipt);
            
            // Ensure receipt is not empty and not too long (max 40 chars)
            if (empty($cleanReceipt)) {
                $cleanReceipt = 'ORD_' . $order->id . '_' . time();
            }
            $cleanReceipt = substr($cleanReceipt, 0, 40);
            
            // CRITICAL: Optimize notes to prevent size limit issues
            $notes = [
                'order_id' => strval($order->id),
                'order_number' => $originalReceipt,
                'company_id' => strval($order->company_id ?? ''),
                'customer_name' => substr($order->customer_name ?? '', 0, 50),
                'amount_rupees' => strval($amountInRupees)
            ];
            
            // Check notes size and reduce if necessary
            $notesJson = json_encode($notes);
            if (strlen($notesJson) > 500) {
                // Reduce to essential data only
                $notes = [
                    'order_id' => strval($order->id),
                    'order_number' => $originalReceipt,
                    'amount' => strval($amountInRupees)
                ];
            }
            
            // Prepare validated order data
            $orderData = [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $cleanReceipt,
                'notes' => $notes
            ];
            
            Log::info('Creating Razorpay order with validated parameters', [
                'amount_rupees' => $amountInRupees,
                'amount_paise' => $amountInPaise,
                'receipt_original' => $originalReceipt,
                'receipt_clean' => $cleanReceipt,
                'receipt_length' => strlen($cleanReceipt),
                'notes_size' => strlen(json_encode($notes)),
                'currency' => 'INR'
            ]);
            
            $razorpayOrder = $api->order->create($orderData);
            
            Log::info('Razorpay order created successfully', [
                'razorpay_order_id' => $razorpayOrder->id,
                'amount' => $razorpayOrder->amount,
                'status' => $razorpayOrder->status,
                'currency' => $razorpayOrder->currency
            ]);
            
            // Update order with Razorpay order ID
            $order->updatePaymentStatus('processing', $razorpayOrder->id, [
                'razorpay_order_id' => $razorpayOrder->id,
                'amount_paise' => $amountInPaise,
                'clean_receipt' => $cleanReceipt,
                'order_created_at' => now()->toISOString()
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
            
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            Log::error('Razorpay API BadRequest error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'amount_paise' => $amountInPaise ?? null,
                'receipt' => $cleanReceipt ?? null,
                'order_data' => $orderData ?? null
            ]);
            
            // Parse specific error details from Razorpay
            $errorMessage = 'Payment request failed. Please try again.';
            $errorDetails = $e->getMessage();
            
            if (str_contains($errorDetails, 'amount')) {
                $errorMessage = 'Invalid payment amount. Please refresh the page and try again.';
            } elseif (str_contains($errorDetails, 'receipt')) {
                $errorMessage = 'Order reference error. Please place a new order.';
            } elseif (str_contains($errorDetails, 'notes')) {
                $errorMessage = 'Order details too large. Please contact support.';
            } elseif (str_contains($errorDetails, 'currency')) {
                $errorMessage = 'Currency error. Please contact support.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_code' => 'RAZORPAY_BAD_REQUEST',
                'debug_info' => app()->environment('local') ? [
                    'razorpay_error' => $errorDetails,
                    'amount_paise' => $amountInPaise ?? null,
                    'receipt_clean' => $cleanReceipt ?? null,
                    'notes_size' => isset($notes) ? strlen(json_encode($notes)) : null
                ] : null
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id,
                'payment_method_id' => $paymentMethod->id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order. Please try again or contact support.',
                'error_code' => 'UNKNOWN_ERROR'
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        // Handle both JSON and form-encoded requests
        if ($request->isJson() || $request->wantsJson()) {
            $data = $request->json()->all();
            $request->merge($data);
        }
        
        // Enhanced logging for debugging
        Log::info('Razorpay payment verification started', [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'order_id' => $request->order_id,
            'has_signature' => !empty($request->razorpay_signature),
            'signature_length' => strlen($request->razorpay_signature ?? ''),
            'timestamp' => now()->toISOString()
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
            Log::error('No active Razorpay payment method found for verification', [
                'company_id' => $order->company_id
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

        // Validate that payment method has required credentials
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
                'key_id_prefix' => substr($paymentMethod->razorpay_key_id, 0, 15) . '...'
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
                'redirect' => route('order.success', ['orderNumber' => $order->order_number])
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
                'message' => 'Payment verification failed. Please contact support if you believe this is an error.',
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
                'message' => 'Payment processing failed. Please contact support.',
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
