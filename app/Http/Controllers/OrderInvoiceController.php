<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderInvoiceController extends Controller
{
    /**
     * Download order invoice as PDF
     */
    public function download($orderId)
    {
        $order = Order::with(['items.product'])->findOrFail($orderId);
        $company = CompanySetting::first();
        
        $pdf = PDF::loadView('invoices.order-invoice-pdf', [
            'order' => $order,
            'company' => $company
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Download the PDF
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
    
    /**
     * Display order invoice for printing
     */
    public function print($orderId)
    {
        $order = Order::with(['items.product'])->findOrFail($orderId);
        $company = CompanySetting::first();
        
        return view('invoices.order-invoice-print', [
            'order' => $order,
            'company' => $company
        ]);
    }
    
    /**
     * Stream order invoice PDF (view in browser)
     */
    public function stream($orderId)
    {
        $order = Order::with(['items.product'])->findOrFail($orderId);
        $company = CompanySetting::first();
        
        $pdf = PDF::loadView('invoices.order-invoice-pdf', [
            'order' => $order,
            'company' => $company
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Stream the PDF in browser
        return $pdf->stream('invoice-' . $order->order_number . '.pdf');
    }
    
    /**
     * Send invoice via email
     */
    public function sendEmail(Request $request, $orderId)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $order = Order::with(['items.product'])->findOrFail($orderId);
        $company = CompanySetting::first();
        
        $pdf = PDF::loadView('invoices.order-invoice-pdf', [
            'order' => $order,
            'company' => $company
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Save PDF temporarily
        $pdfContent = $pdf->output();
        
        // Send email with PDF attachment
        try {
            \Mail::send('emails.invoice', [
                'order' => $order,
                'company' => $company
            ], function ($message) use ($request, $order, $pdfContent, $company) {
                $message->to($request->email)
                    ->subject('Invoice #' . $order->order_number . ' - ' . ($company->company_name ?? 'Your Order'))
                    ->attachData($pdfContent, 'invoice-' . $order->order_number . '.pdf', [
                        'mime' => 'application/pdf'
                    ]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully to ' . $request->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
