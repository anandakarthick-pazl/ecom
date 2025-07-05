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
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function invoice(Order $order)
    {
        $order->load('items.product', 'customer');
        return view('admin.orders.invoice', compact('order'));
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

    public function sendInvoice(Order $order)
    {
        try {
            $order->load('items.product', 'customer');
            
            // Generate PDF
            $pdfPath = $this->generateInvoicePdf($order);
            
            // Send email with PDF attachment if email is available
            if (AppSetting::get('email_notifications', true) && !empty($order->customer_email)) {
                Mail::to($order->customer_email)
                    ->send(new OrderInvoiceMail($order, $pdfPath));
            }
            
            return redirect()->back()->with('success', 'Invoice sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to send invoice: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
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
}
