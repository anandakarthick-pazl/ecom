<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class StockNotificationControllerSimple extends Controller
{
    /**
     * Subscribe to stock notifications - Simplified version
     */
    public function subscribe(Request $request)
    {
        try {
            // Log the incoming request
            Log::info('Stock notification subscribe request received', [
                'data' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'customer_name' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email|max:255',
                'customer_mobile' => 'nullable|string|regex:/^[0-9]{10}$/',
                'notification_type' => 'nullable|in:email,whatsapp,both'
            ], [
                'product_id.required' => 'Product selection is required.',
                'product_id.exists' => 'Selected product does not exist.',
                'customer_email.email' => 'Please enter a valid email address.',
                'customer_mobile.regex' => 'Please enter a valid 10-digit mobile number.',
                'notification_type.in' => 'Invalid notification type selected.'
            ]);

            if ($validator->fails()) {
                Log::warning('Stock notification validation failed', [
                    'errors' => $validator->errors()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your input and try again.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ensure at least one contact method is provided
            if (empty($request->customer_email) && empty($request->customer_mobile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide either email address or mobile number to receive notifications.',
                    'errors' => ['contact' => 'At least one contact method is required.']
                ], 422);
            }

            // Get the product
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }

            // Check if product is in stock
            if ($product->isInStock()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is currently in stock. You can add it to cart now!',
                    'in_stock' => true
                ], 400);
            }

            // Determine notification type based on provided contact info
            $notificationType = $request->notification_type;
            if (!$notificationType) {
                if ($request->customer_email && $request->customer_mobile) {
                    $notificationType = 'both';
                } elseif ($request->customer_email) {
                    $notificationType = 'email';
                } else {
                    $notificationType = 'whatsapp';
                }
            }

            // Get company ID from product or session
            $companyId = $product->company_id ?? session('company_id') ?? 1;

            // Check if similar notification already exists
            $existingQuery = ProductStockNotification::where('product_id', $request->product_id)
                ->where('is_notified', false);

            if ($request->customer_email) {
                $existingQuery->where(function($query) use ($request) {
                    $query->where('customer_email', $request->customer_email);
                });
            }

            if ($request->customer_mobile) {
                $existingQuery->orWhere(function($query) use ($request) {
                    $query->where('customer_mobile', $request->customer_mobile);
                });
            }

            $existing = $existingQuery->first();

            if ($existing) {
                // Update existing notification
                $existing->update([
                    'customer_name' => $request->customer_name ?? $existing->customer_name,
                    'customer_email' => $request->customer_email ?? $existing->customer_email,
                    'customer_mobile' => $request->customer_mobile ?? $existing->customer_mobile,
                    'notification_type' => $notificationType,
                    'company_id' => $companyId,
                    'metadata' => array_merge($existing->metadata ?? [], [
                        'updated_at' => now()->toISOString(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                ]);

                Log::info('Stock notification updated', [
                    'notification_id' => $existing->id,
                    'product_id' => $request->product_id,
                    'customer_email' => $request->customer_email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Your notification preference has been updated! We\'ll notify you when this product is back in stock.',
                    'notification_id' => $existing->id
                ]);
            }

            // Create new notification
            $notification = ProductStockNotification::create([
                'company_id' => $companyId,
                'product_id' => $request->product_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_mobile' => $request->customer_mobile,
                'notification_type' => $notificationType,
                'session_id' => session()->getId(),
                'metadata' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_via' => 'web'
                ]
            ]);

            Log::info('Stock notification created', [
                'notification_id' => $notification->id,
                'product_id' => $request->product_id,
                'product_name' => $product->name,
                'customer_email' => $request->customer_email,
                'customer_mobile' => $request->customer_mobile
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Great! We\'ll notify you as soon as this product is back in stock.',
                'notification_id' => $notification->id
            ]);

        } catch (Exception $e) {
            Log::error('Stock notification subscription error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, something went wrong. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        try {
            $productId = $request->get('product_id');
            
            $query = ProductStockNotification::query();
            
            if ($productId) {
                $query->where('product_id', $productId);
            }

            $stats = [
                'total_subscriptions' => $query->count(),
                'pending_notifications' => $query->where('is_notified', false)->count(),
                'sent_notifications' => $query->where('is_notified', true)->count(),
                'by_type' => [
                    'email' => $query->where('notification_type', 'email')->count(),
                    'whatsapp' => $query->where('notification_type', 'whatsapp')->count(),
                    'both' => $query->where('notification_type', 'both')->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            Log::error('Stock notification stats error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve statistics.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Test endpoint to verify the controller is working
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Stock notification controller is working!',
            'timestamp' => now(),
            'model_exists' => class_exists(ProductStockNotification::class),
            'table_exists' => \Schema::hasTable('product_stock_notifications')
        ]);
    }
}
