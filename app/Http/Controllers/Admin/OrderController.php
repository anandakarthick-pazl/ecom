<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\AppSetting;
use App\Mail\OrderStatusMail;
use App\Mail\OrderInvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SuperAdmin\WhatsAppConfig;
use App\Services\TwilioWhatsAppService;
use App\Services\BillPDFService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Add payment status filter
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('customer_mobile', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'customer');
        return view('admin.orders.show', compact('order'));
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,processing,paid,failed,refunded',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        $oldPaymentStatus = $order->payment_status;
        
        // Update payment status
        $order->update([
            'payment_status' => $request->payment_status,
            'payment_verified_at' => $request->payment_status === 'paid' ? now() : null
        ]);
        
        // Add payment notes to order notes if provided
        if ($request->payment_notes) {
            $currentNotes = $order->admin_notes;
            $newNote = "Payment Status Update: {$request->payment_notes}";
            $updatedNotes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
            $order->update(['admin_notes' => $updatedNotes]);
        }

        // Send email notification if payment status changes to paid and email notifications are enabled
        if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid' && 
            AppSetting::get('email_notifications', true) && !empty($order->customer_email)) {
            try {
                Mail::to($order->customer_email)
                    ->send(new OrderStatusMail($order, 'Payment confirmed! Your order is being processed.'));
            } catch (\Exception $e) {
                \Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
            }
        }

        // Send WhatsApp notification if payment status changes to paid and WhatsApp notifications are enabled
        if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid' && 
            AppSetting::get('whatsapp_notifications', false) && !empty($order->customer_mobile)) {
            try {
                $this->sendPaymentStatusWhatsApp($order, $oldPaymentStatus, $request->payment_status);
            } catch (\Exception $e) {
                \Log::error('Failed to send payment confirmation WhatsApp: ' . $e->getMessage());
            }
        }

        // Create notification for payment status change
        Notification::createForAdmin(
            'payment_updated',
            'Payment Status Updated',
            "Payment status for order {$order->order_number} changed from {$oldPaymentStatus} to {$request->payment_status}",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $request->payment_status,
                'customer_name' => $order->customer_name
            ]
        );

        // Auto-update order status based on payment status
        if ($request->payment_status === 'paid' && $order->status === 'pending') {
            $order->updateStatus('processing');
        }

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $oldStatus = $order->status;
        $order->updateStatus($request->status);
        
        if ($request->admin_notes) {
            $order->update(['admin_notes' => $request->admin_notes]);
        }

        // Send email notification to customer if email notifications are enabled and email is available
        if (AppSetting::get('email_notifications', true) && !empty($order->customer_email)) {
            try {
                Mail::to($order->customer_email)
                    ->send(new OrderStatusMail($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send order status email: ' . $e->getMessage());
            }
        }

        // Send WhatsApp notification to customer if WhatsApp notifications are enabled and mobile is available
        if (AppSetting::get('whatsapp_notifications', false) && !empty($order->customer_mobile)) {
            try {
                $this->sendOrderStatusWhatsApp($order, $oldStatus, $request->status);
            } catch (\Exception $e) {
                \Log::error('Failed to send order status WhatsApp: ' . $e->getMessage());
            }
        }

        // Create notification for status change
        Notification::createForAdmin(
            'order_updated',
            'Order Status Updated',
            "Order {$order->order_number} status changed from {$oldStatus} to {$request->status}",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'customer_name' => $order->customer_name
            ]
        );

        // Update customer stats if order is completed or cancelled
        if (in_array($request->status, ['delivered', 'cancelled'])) {
            $order->customer?->updateOrderStats();
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function generateInvoicePdf(Order $order)
    {
        $order->load('items.product', 'customer');
        
        $pdf = Pdf::loadView('admin.orders.invoice-pdf', compact('order'));
        
        $filename = 'invoice-' . $order->order_number . '.pdf';
        $path = 'invoices/' . $filename;
        
        // Save PDF to storage
        \Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }

    public function export(Request $request)
    {
        $query = Order::with('customer');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Order Number', 'Customer Name', 'Mobile', 'Email', 'Total', 'Payment Method', 'Payment Status', 'Order Status', 'Order Date'
            ]);

            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_mobile,
                    $order->customer_email ?: 'N/A',
                    $order->total,
                    ucfirst(str_replace('_', ' ', $order->payment_method)),
                    ucfirst(str_replace('_', ' ', $order->payment_status)),
                    ucfirst($order->status),
                    $order->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function recentOrders()
    {
        $orders = Order::with('customer')
                      ->latest()
                      ->limit(10)
                      ->get()
                      ->map(function ($order) {
                          return [
                              'id' => $order->id,
                              'order_number' => $order->order_number,
                              'customer_name' => $order->customer_name,
                              'total' => $order->total,
                              'status' => $order->status,
                              'payment_status' => $order->payment_status,
                              'created_at' => $order->created_at->diffForHumans()
                          ];
                      });

        return response()->json($orders);
    }

    // New method to get payment statistics
    public function paymentStats()
    {
        $stats = [
            'paid' => Order::where('payment_status', 'paid')->count(),
            'pending' => Order::where('payment_status', 'pending')->count(),
            'failed' => Order::where('payment_status', 'failed')->count(),
            'processing' => Order::where('payment_status', 'processing')->count(),
            'refunded' => Order::where('payment_status', 'refunded')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Send WhatsApp notification for order status change
     */
    private function sendOrderStatusWhatsApp(Order $order, $oldStatus, $newStatus)
    {
        try {
            // Get current tenant ID
            $tenantId = $this->getCurrentTenantId();
            if (!$tenantId) {
                Log::warning('Unable to determine tenant ID for WhatsApp notification');
                return;
            }

            // Get WhatsApp configuration for this company
            $whatsappConfig = WhatsAppConfig::where('company_id', $tenantId)->first();
            
            if (!$whatsappConfig || !$whatsappConfig->isConfigured() || !$whatsappConfig->is_enabled) {
                Log::info('WhatsApp not configured or disabled for company: ' . $tenantId);
                return;
            }

            // Get order status message template
            $message = $this->getOrderStatusMessage($order, $oldStatus, $newStatus);
            
            // Send WhatsApp message
            $whatsappService = new TwilioWhatsAppService($whatsappConfig);
            $result = $whatsappService->sendTestMessage($order->customer_mobile, $message);

            if ($result['success']) {
                Log::info('Order status WhatsApp sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_phone' => $order->customer_mobile,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'message_sid' => $result['message_sid'],
                    'company_id' => $tenantId
                ]);

                // Create notification for WhatsApp sent
                Notification::createForAdmin(
                    'whatsapp_order_status_sent',
                    'Order Status WhatsApp Sent',
                    "Order status update sent via WhatsApp for order {$order->order_number}",
                    [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_phone' => $order->customer_mobile,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'message_sid' => $result['message_sid']
                    ]
                );
            } else {
                Log::error('Order status WhatsApp sending failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $result['error']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception in sendOrderStatusWhatsApp', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp notification for payment status change
     */
    private function sendPaymentStatusWhatsApp(Order $order, $oldPaymentStatus, $newPaymentStatus)
    {
        try {
            // Get current tenant ID
            $tenantId = $this->getCurrentTenantId();
            if (!$tenantId) {
                Log::warning('Unable to determine tenant ID for WhatsApp payment notification');
                return;
            }

            // Get WhatsApp configuration for this company
            $whatsappConfig = WhatsAppConfig::where('company_id', $tenantId)->first();
            
            if (!$whatsappConfig || !$whatsappConfig->isConfigured() || !$whatsappConfig->is_enabled) {
                Log::info('WhatsApp not configured or disabled for company: ' . $tenantId);
                return;
            }

            // Get payment status message template
            $message = $this->getPaymentStatusMessage($order, $oldPaymentStatus, $newPaymentStatus);
            
            // Send WhatsApp message
            $whatsappService = new TwilioWhatsAppService($whatsappConfig);
            $result = $whatsappService->sendTestMessage($order->customer_mobile, $message);

            if ($result['success']) {
                Log::info('Payment status WhatsApp sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_phone' => $order->customer_mobile,
                    'old_payment_status' => $oldPaymentStatus,
                    'new_payment_status' => $newPaymentStatus,
                    'message_sid' => $result['message_sid'],
                    'company_id' => $tenantId
                ]);

                // Create notification for WhatsApp sent
                Notification::createForAdmin(
                    'whatsapp_payment_status_sent',
                    'Payment Status WhatsApp Sent',
                    "Payment status update sent via WhatsApp for order {$order->order_number}",
                    [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_phone' => $order->customer_mobile,
                        'old_payment_status' => $oldPaymentStatus,
                        'new_payment_status' => $newPaymentStatus,
                        'message_sid' => $result['message_sid']
                    ]
                );
            } else {
                Log::error('Payment status WhatsApp sending failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $result['error']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception in sendPaymentStatusWhatsApp', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get order status message template
     */
    private function getOrderStatusMessage(Order $order, $oldStatus, $newStatus)
    {
        // Get custom templates from settings or use defaults
        $templates = [
            'pending' => AppSetting::get('whatsapp_template_pending', 
                "Hello {{customer_name}},\n\nYour order #{{order_number}} is now PENDING.\n\nWe have received your order and it's being processed.\n\nOrder Total: ₹{{total}}\nOrder Date: {{order_date}}\n\nThank you for choosing {{company_name}}!"),
            
            'processing' => AppSetting::get('whatsapp_template_processing', 
                "Hello {{customer_name}},\n\nGreat news! Your order #{{order_number}} is now PROCESSING.\n\nWe are preparing your items for shipment.\n\nOrder Total: ₹{{total}}\nExpected Processing: 1-2 business days\n\nThank you for your patience!\n\n{{company_name}}"),
            
            'shipped' => AppSetting::get('whatsapp_template_shipped', 
                "🚚 Hello {{customer_name}},\n\nExciting news! Your order #{{order_number}} has been SHIPPED!\n\nYour package is on its way to you.\n\nOrder Total: ₹{{total}}\nExpected Delivery: 2-5 business days\n\nTrack your order for real-time updates.\n\nThanks for shopping with {{company_name}}!"),
            
            'delivered' => AppSetting::get('whatsapp_template_delivered', 
                "✅ Hello {{customer_name}},\n\nWonderful! Your order #{{order_number}} has been DELIVERED!\n\nWe hope you love your purchase.\n\nOrder Total: ₹{{total}}\nDelivered on: {{order_date}}\n\nPlease let us know if you have any questions or feedback.\n\nThank you for choosing {{company_name}}!"),
            
            'cancelled' => AppSetting::get('whatsapp_template_cancelled', 
                "❌ Hello {{customer_name}},\n\nWe're sorry to inform you that your order #{{order_number}} has been CANCELLED.\n\nOrder Total: ₹{{total}}\nCancellation Date: {{order_date}}\n\nIf you have any questions about this cancellation, please contact our customer support.\n\nWe apologize for any inconvenience.\n\n{{company_name}}")
        ];

        $template = $templates[$newStatus] ?? $templates['processing'];
        
        return $this->replaceMessagePlaceholders($template, $order);
    }

    /**
     * Get payment status message template
     */
    private function getPaymentStatusMessage(Order $order, $oldPaymentStatus, $newPaymentStatus)
    {
        // Get custom template from settings or use default
        $template = AppSetting::get('whatsapp_template_payment_confirmed', 
            "💳 Hello {{customer_name}},\n\nGreat news! Your payment for order #{{order_number}} has been CONFIRMED!\n\nPayment Status: {{payment_status}}\nOrder Total: ₹{{total}}\nPayment Date: {{order_date}}\n\nYour order is now being processed and will be shipped soon.\n\nThank you for your payment!\n\n{{company_name}}");
        
        return $this->replaceMessagePlaceholders($template, $order);
    }

    /**
     * Replace message placeholders with actual order data
     */
    private function replaceMessagePlaceholders($template, Order $order)
    {
        // Load company relationship if not already loaded
        if (!$order->relationLoaded('company')) {
            $order->load('company');
        }

        // Get company name from relationship or fallback to app setting
        $companyName = $order->company->name ?? AppSetting::getForTenant('company_name', $order->company_id) ?? 'Our Store';
        
        $placeholders = [
            '{{customer_name}}' => $order->customer_name,
            '{{order_number}}' => $order->order_number,
            '{{total}}' => number_format($order->total, 2),
            '{{company_name}}' => $companyName,
            '{{order_date}}' => $order->created_at->format('d M Y'),
            '{{status}}' => ucfirst($order->status),
            '{{payment_status}}' => ucfirst($order->payment_status ?? 'pending'),
            '{{customer_mobile}}' => $order->customer_mobile,
            '{{customer_email}}' => $order->customer_email ?? 'N/A',
            '{{order_time}}' => $order->created_at->format('h:i A'),
            '{{order_datetime}}' => $order->created_at->format('d M Y, h:i A'),
            '{{currency}}' => '₹', // Can be made dynamic from settings
            '{{items_count}}' => $order->items->count() ?? 0,
            '{{total_formatted}}' => '₹' . number_format($order->total, 2)
        ];

        // Log the replacement for debugging
        Log::debug('WhatsApp message placeholder replacement', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'template_length' => strlen($template),
            'placeholders_count' => count($placeholders),
            'customer_name' => $order->customer_name,
            'company_name' => $companyName
        ]);

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $template);
        
        // Log the final message for debugging
        Log::debug('WhatsApp message after replacement', [
            'order_id' => $order->id,
            'message_preview' => substr($message, 0, 100) . '...',
            'message_length' => strlen($message),
            'has_unreplaced_placeholders' => strpos($message, '{{') !== false
        ]);
        
        return $message;
    }

    /**
     * Get current tenant ID from various sources
     */
    private function getCurrentTenantId()
    {
        // Try multiple sources for company_id with better error handling
        try {
            if (app()->has('current_tenant')) {
                $tenant = app('current_tenant');
                if ($tenant && isset($tenant->id)) {
                    return $tenant->id;
                }
            }
            
            if (request()->has('current_company_id')) {
                $companyId = request()->get('current_company_id');
                if ($companyId) {
                    return $companyId;
                }
            }
            
            if (session()->has('selected_company_id')) {
                $companyId = session('selected_company_id');
                if ($companyId) {
                    return $companyId;
                }
            }
            
            if (auth()->check() && auth()->user()->company_id) {
                return auth()->user()->company_id;
            }

            // Try to get company from domain
            $host = request()->getHost();
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            if ($company) {
                return $company->id;
            }
            
        } catch (\Exception $e) {
            Log::warning('Error getting tenant ID', [
                'error' => $e->getMessage(),
                'host' => request()->getHost(),
                'user_id' => auth()->id()
            ]);
        }
        
        return null;
    }

    /**
     * Check WhatsApp configuration status for the current company
     */
    public function checkWhatsAppStatus()
    {
        try {
            // Get current tenant ID
            $tenantId = $this->getCurrentTenantId();
            
            if (!$tenantId) {
                return response()->json([
                    'configured' => false,
                    'enabled' => false,
                    'message' => 'Unable to determine company context'
                ]);
            }

            // Get WhatsApp configuration for this company
            $whatsappConfig = WhatsAppConfig::where('company_id', $tenantId)->first();
            
            if (!$whatsappConfig) {
                return response()->json([
                    'configured' => false,
                    'enabled' => false,
                    'message' => 'WhatsApp is not configured for this company'
                ]);
            }

            $isConfigured = $whatsappConfig->isConfigured();
            $isEnabled = $whatsappConfig->is_enabled;

            return response()->json([
                'configured' => $isConfigured,
                'enabled' => $isEnabled,
                'message' => $this->getWhatsAppStatusMessage($isConfigured, $isEnabled),
                'phone_number' => $whatsappConfig->getFormattedPhoneNumber(),
                'rate_limit' => $whatsappConfig->getRateLimitStatus()
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp status check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'configured' => false,
                'enabled' => false,
                'message' => 'Failed to check WhatsApp status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download bill PDF for an order based on admin configuration
     */
    public function downloadBill(Order $order)
    {
        try {
            $billService = new BillPDFService();
            
            // Use admin-configured format (no manual override unless specifically requested)
            $format = request()->get('format'); // Allow manual format selection via query parameter
            
            Log::info('Downloading order bill', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'requested_format' => $format,
                'company_id' => $order->company_id
            ]);
            
            return $billService->downloadOrderBill($order, $format);
            
        } catch (\Exception $e) {
            Log::error('Order bill download failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'company_id' => $order->company_id
            ]);

            return redirect()->back()->with('error', 'Failed to download bill: ' . $e->getMessage());
        }
    }

    /**
     * Get available bill formats for order
     */
    public function getBillFormats(Order $order)
    {
        try {
            $companyId = $this->getCurrentTenantId();
            if (!$companyId) {
                return response()->json(['error' => 'Company context not found'], 400);
            }

            $billService = new BillPDFService();
            $config = $billService->getBillFormatConfig($companyId);
            $formats = $billService->getAvailableFormats($companyId);

            return response()->json([
                'success' => true,
                'formats' => $formats,
                'config' => $config,
                'order_info' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'company_id' => $order->company_id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get bill formats for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test WhatsApp media URL generation (debug endpoint)
     */
    public function testWhatsAppMediaUrl(Order $order)
    {
        try {
            // Create a test PDF file
            $billService = new BillPDFService();
            $pdfResult = $billService->generateOrderBill($order);
            
            if (!$pdfResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to generate test PDF: ' . $pdfResult['error']
                ]);
            }
            
            // Test URL generation without actually sending
            $whatsappService = new TwilioWhatsAppService();
            $testUrl = $whatsappService->testUrlGeneration($pdfResult['file_path'], $order);
            
            // Clean up test file
            if (file_exists($pdfResult['file_path'])) {
                unlink($pdfResult['file_path']);
            }
            
            return response()->json([
                'success' => true,
                'test_url' => $testUrl,
                'message' => 'URL generation test completed',
                'order_number' => $order->order_number
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send bill PDF via WhatsApp
     */
    public function sendBillWhatsApp(Request $request, Order $order)
    {
        try {
            // Validate request
            $request->validate([
                'message' => 'nullable|string|max:1000'
            ]);

            // Get current tenant ID
            $tenantId = $this->getCurrentTenantId();
            if (!$tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to determine company context'
                ], 400);
            }

            // Check if order has customer mobile
            if (empty($order->customer_mobile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer mobile number is not available for this order'
                ], 400);
            }

            // Get WhatsApp configuration
            $whatsappConfig = WhatsAppConfig::where('company_id', $tenantId)->first();
            
            if (!$whatsappConfig || !$whatsappConfig->isConfigured() || !$whatsappConfig->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp is not configured or enabled for this company'
                ], 400);
            }

            // Generate bill PDF
            $billService = new BillPDFService();
            $pdfResult = $billService->generateOrderBill($order);
            
            if (!$pdfResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate bill PDF: ' . $pdfResult['error']
                ], 500);
            }

            // Send via WhatsApp
            $whatsappService = new TwilioWhatsAppService($whatsappConfig);
            $customMessage = $request->input('message');
            $result = $whatsappService->sendBillPDF($order, $pdfResult['file_path'], $customMessage);

            // Clean up temporary file
            if (file_exists($pdfResult['file_path'])) {
                unlink($pdfResult['file_path']);
            }

            if ($result['success']) {
                // Log successful sending
                Log::info('WhatsApp bill sent successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_phone' => $order->customer_mobile,
                    'message_sid' => $result['message_sid'],
                    'company_id' => $tenantId,
                    'custom_message' => !empty($customMessage)
                ]);

                // Create notification for admin
                Notification::createForAdmin(
                    'whatsapp_bill_sent',
                    'Bill Sent via WhatsApp',
                    "Bill for order {$order->order_number} sent via WhatsApp to {$result['sent_to']}",
                    [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_phone' => $result['sent_to'],
                        'message_sid' => $result['message_sid'],
                        'sent_at' => $result['sent_at']
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'sent_to' => $result['sent_to'],
                    'message_sid' => $result['message_sid'],
                    'sent_at' => $result['sent_at']->format('Y-m-d H:i:s')
                ]);
            } else {
                // Log failure
                Log::error('WhatsApp bill sending failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_phone' => $order->customer_mobile,
                    'error' => $result['error'],
                    'company_id' => $tenantId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('WhatsApp bill sending exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send bill via WhatsApp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice via email (enhanced functionality)
     */
    public function sendInvoice(Order $order)
    {
        try {
            // Validate that customer email exists
            if (empty($order->customer_email)) {
                return redirect()->back()->with('error', 'Customer email is not available for this order.');
            }

            Log::info('Sending invoice email', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email
            ]);

            // Send email with auto-generated PDF
            // The OrderInvoiceMail class will automatically generate the PDF
            Mail::to($order->customer_email)
                ->send(new OrderInvoiceMail($order));

            // Log successful sending
            Log::info('Invoice email sent successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email
            ]);

            // Create notification for admin
            Notification::createForAdmin(
                'invoice_email_sent',
                'Invoice Email Sent',
                "Invoice for order {$order->order_number} sent to {$order->customer_email}",
                [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->customer_email,
                    'sent_at' => now()->toDateTimeString()
                ]
            );

            return redirect()->back()->with('success', "Invoice sent successfully to {$order->customer_email}");

        } catch (\Exception $e) {
            Log::error('Invoice email sending failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_email' => $order->customer_email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice view (existing functionality)
     */
    public function invoice(Order $order)
    {
        $order->load(['items.product', 'customer']);
        $companySettings = $this->getCompanySettings($order->company_id);
        
        return view('admin.orders.invoice', compact('order', 'companySettings'));
    }

    /**
     * Test email functionality (for debugging)
     */
    public function testInvoiceEmail(Order $order)
    {
        try {
            // Test email address - use admin email or provided test email
            $testEmail = request()->get('test_email') ?? auth()->user()->email ?? 'test@example.com';
            
            Log::info('Testing invoice email', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'test_email' => $testEmail
            ]);

            // Send test email
            Mail::to($testEmail)
                ->send(new OrderInvoiceMail($order));

            Log::info('Test invoice email sent successfully', [
                'order_id' => $order->id,
                'test_email' => $testEmail
            ]);

            return response()->json([
                'success' => true,
                'message' => "Test invoice sent successfully to {$testEmail}",
                'order_number' => $order->order_number,
                'sent_to' => $testEmail,
                'sent_at' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('Test invoice email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test invoice: ' . $e->getMessage(),
                'order_number' => $order->order_number
            ], 500);
        }
    }

    /**
     * Preview invoice email content (for debugging)
     */
    public function previewInvoiceEmail(Order $order)
    {
        try {
            // Create mail instance without sending
            $mail = new OrderInvoiceMail($order, null, false); // Don't generate PDF for preview
            $content = $mail->content();
            
            // Get company data
            $company = $mail->company;
            
            Log::info('Invoice email preview generated', [
                'order_id' => $order->id,
                'has_pdf' => !empty($mail->pdfPath)
            ]);
            
            // Return the email view directly
            return view($content->view, [
                'order' => $order,
                'company' => $company
            ]);
            
        } catch (\Exception $e) {
            Log::error('Invoice email preview failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response('Failed to preview invoice email: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get WhatsApp status message
     */
    private function getWhatsAppStatusMessage($isConfigured, $isEnabled)
    {
        if (!$isConfigured) {
            return 'WhatsApp is not configured for this company. Please contact super admin to configure Twilio WhatsApp integration.';
        }
        
        if (!$isEnabled) {
            return 'WhatsApp is configured but disabled for this company.';
        }
        
        return 'WhatsApp is configured and enabled for this company.';
    }

    /**
     * Get company settings for invoices
     */
    private function getCompanySettings($companyId)
    {
        return [
            'name' => AppSetting::getForTenant('company_name', $companyId) ?? 'Your Company',
            'address' => AppSetting::getForTenant('company_address', $companyId) ?? '',
            'phone' => AppSetting::getForTenant('company_phone', $companyId) ?? '',
            'email' => AppSetting::getForTenant('company_email', $companyId) ?? '',
            'website' => AppSetting::getForTenant('company_website', $companyId) ?? '',
            'gst_number' => AppSetting::getForTenant('company_gst_number', $companyId) ?? '',
            'logo' => AppSetting::getForTenant('company_logo', $companyId) ?? '',
            'currency' => AppSetting::getForTenant('currency', $companyId) ?? '₹',
            'tax_name' => AppSetting::getForTenant('tax_name', $companyId) ?? 'GST',
            'tax_rate' => AppSetting::getForTenant('tax_rate', $companyId) ?? 18,
        ];
    }

    /**
     * Debug WhatsApp media URL configuration
     */
    public function debugWhatsAppMedia(Order $order)
    {
        try {
            $tenantId = $this->getCurrentTenantId();
            $whatsappConfig = WhatsAppConfig::where('company_id', $tenantId)->first();
            
            $debugInfo = [
                'order_info' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'company_id' => $order->company_id
                ],
                'environment' => [
                    'app_env' => config('app.env'),
                    'app_url' => config('app.url'),
                    'app_debug' => config('app.debug'),
                    'request_url' => request()->url(),
                    'request_host' => request()->getHost(),
                    'request_scheme' => request()->getScheme()
                ],
                'storage' => [
                    'public_path' => public_path('storage'),
                    'storage_path' => storage_path('app/public'),
                    'symlink_exists' => is_link(public_path('storage')),
                    'storage_disk_url' => Storage::disk('public')->url('test.txt'),
                    'asset_url' => asset('storage/test.txt')
                ],
                'whatsapp' => [
                    'configured' => $whatsappConfig ? $whatsappConfig->isConfigured() : false,
                    'enabled' => $whatsappConfig ? $whatsappConfig->is_enabled : false,
                    'phone_number' => $whatsappConfig ? $whatsappConfig->getFormattedPhoneNumber() : null
                ],
                'directories' => [
                    'whatsapp_bills_exists' => Storage::disk('public')->exists('whatsapp-bills'),
                    'invoices_exists' => Storage::disk('public')->exists('invoices'),
                    'receipts_exists' => Storage::disk('public')->exists('receipts')
                ]
            ];
            
            Log::info('WhatsApp Media Debug Info', $debugInfo);
            
            return response()->json([
                'success' => true,
                'debug_info' => $debugInfo,
                'recommendations' => $this->getDebugRecommendations($debugInfo)
            ]);
            
        } catch (\Exception $e) {
            Log::error('WhatsApp media debug failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get debug recommendations based on configuration
     */
    private function getDebugRecommendations($debugInfo)
    {
        $recommendations = [];
        
        if ($debugInfo['environment']['app_env'] === 'local') {
            $recommendations[] = 'Set APP_ENV=production in .env file';
        }
        
        if (str_contains($debugInfo['environment']['app_url'], 'localhost')) {
            $recommendations[] = 'Update APP_URL to your production domain in .env file';
        }
        
        if (!$debugInfo['storage']['symlink_exists']) {
            $recommendations[] = 'Run: php artisan storage:link or php artisan storage:fix-setup';
        }
        
        if (!$debugInfo['whatsapp']['configured']) {
            $recommendations[] = 'Configure WhatsApp settings in super admin panel';
        }
        
        if (!$debugInfo['whatsapp']['enabled']) {
            $recommendations[] = 'Enable WhatsApp notifications in super admin panel';
        }
        
        if (!$debugInfo['directories']['whatsapp_bills_exists']) {
            $recommendations[] = 'Run: php artisan storage:fix-setup to create required directories';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Configuration looks good! Test WhatsApp bill sending.';
        }
        
        return $recommendations;
    }

    /**
     * Test WhatsApp message template replacement (for debugging)
     */
    public function testWhatsAppMessage(Request $request, Order $order)
    {
        try {
            // Test order status message
            $orderStatusMessage = $this->getOrderStatusMessage($order, 'pending', 'shipped');
            
            // Test payment status message  
            $paymentStatusMessage = $this->getPaymentStatusMessage($order, 'pending', 'paid');
            
            // Test manual template
            $manualTemplate = $request->input('template', 'Hello {{customer_name}}, your order #{{order_number}} for ₹{{total}} from {{company_name}} is ready!');
            $manualMessage = $this->replaceMessagePlaceholders($manualTemplate, $order);
            
            return response()->json([
                'success' => true,
                'order_info' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total' => $order->total,
                    'company_id' => $order->company_id
                ],
                'test_results' => [
                    'order_status_message' => $orderStatusMessage,
                    'payment_status_message' => $paymentStatusMessage,
                    'manual_message' => $manualMessage,
                    'manual_template' => $manualTemplate
                ],
                'message' => 'WhatsApp message templates tested successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('WhatsApp message test failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
