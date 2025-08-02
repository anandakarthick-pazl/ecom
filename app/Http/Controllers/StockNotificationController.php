<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockNotification;
use App\Services\EnhancedStockNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(EnhancedStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Subscribe to stock notifications
     */
    public function subscribe(Request $request)
    {
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

        $customerData = [
            'name' => $request->customer_name,
            'email' => $request->customer_email,
            'mobile' => $request->customer_mobile,
            'notification_type' => $notificationType
        ];

        $result = $this->notificationService->subscribe(
            $request->product_id,
            $customerData,
            session()->getId()
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Show notification form (for testing or separate page)
     */
    public function showForm($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->isInStock()) {
            return redirect()->route('product', $product->slug)
                ->with('info', 'This product is currently in stock. You can add it to cart now!');
        }

        return view('stock-notification-form', compact('product'));
    }

    /**
     * Get notification statistics (Admin use)
     */
    public function getStats(Request $request)
    {
        $productId = $request->get('product_id');
        $stats = $this->notificationService->getStats($productId);
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Send notifications for a product (Admin use)
     */
    public function sendNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product ID.',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->notificationService->notifyCustomers($request->product_id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * List notifications for a product (Admin use)
     */
    public function listNotifications(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $notifications = ProductStockNotification::where('product_id', $productId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
                'is_in_stock' => $product->isInStock()
            ],
            'notifications' => $notifications
        ]);
    }

    /**
     * Quick notification form for AJAX (returns HTML)
     */
    public function quickForm($productId)
    {
        $product = Product::findOrFail($productId);
        
        if ($product->isInStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is currently in stock',
                'in_stock' => true
            ]);
        }

        $html = view('partials.quick-notification-form', compact('product'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'product_name' => $product->name
        ]);
    }

    /**
     * Unsubscribe from notifications
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:mobile|email',
            'mobile' => 'required_without:email|string',
            'product_id' => 'nullable|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data.',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = ProductStockNotification::where('is_notified', false);

        if ($request->email) {
            $query->where('customer_email', $request->email);
        }

        if ($request->mobile) {
            $query->where('customer_mobile', $request->mobile);
        }

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $count = $query->count();
        $query->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully unsubscribed from {$count} notification(s).",
            'count' => $count
        ]);
    }

    /**
     * Get pending notifications summary for admin dashboard
     */
    public function getPendingSummary(Request $request)
    {
        $summary = $this->notificationService->getPendingNotificationsSummary();
        
        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    /**
     * Trigger manual stock check (Admin use)
     */
    public function triggerStockCheck(Request $request)
    {
        $productId = $request->get('product_id');
        
        if ($productId) {
            $result = $this->notificationService->notifyCustomers($productId);
        } else {
            $result = $this->notificationService->checkAndNotifyRestockedProducts();
        }
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Clean up old notifications (Admin use)
     */
    public function cleanupOldNotifications(Request $request)
    {
        $days = $request->get('days', 30);
        $deleted = $this->notificationService->cleanupOldNotifications($days);
        
        return response()->json([
            'success' => true,
            'message' => "Cleaned up {$deleted} old notifications",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Unsubscribe via signed URL (for email links)
     */
    public function signedUnsubscribe(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return view('errors.invalid-signature')->with([
                'message' => 'This unsubscribe link is invalid or has expired.'
            ]);
        }

        $email = $request->get('email');
        $productId = $request->get('product');
        
        $query = ProductStockNotification::where('customer_email', $email)
                                        ->where('is_notified', false);
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        $count = $query->count();
        $query->delete();
        
        return view('stock-notification.unsubscribed')->with([
            'email' => $email,
            'count' => $count,
            'product_specific' => (bool) $productId
        ]);
    }
}
