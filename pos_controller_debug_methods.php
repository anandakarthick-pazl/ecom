<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosSale;
use App\Services\BillPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Temporary POS Controller additions for debugging
 * Add these methods to your existing PosController
 */
class PosControllerDebugMethods
{
    /**
     * Simple debug version of downloadBill method
     * Add this as a new method in your PosController
     */
    public function downloadBillDebug(PosSale $sale)
    {
        try {
            Log::info('Debug: downloadBillDebug started', ['sale_id' => $sale->id]);
            
            // Load sale relationships
            $sale->load(['items.product', 'cashier']);
            
            // Get simple company data
            $globalCompany = (object) [
                'company_name' => 'Green Valley Herbs',
                'company_address' => 'Natural & Organic Products Store',
                'company_phone' => '',
                'company_email' => '',
                'gst_number' => '',
                'company_logo' => null
            ];
            
            Log::info('Debug: Data prepared', [
                'sale_id' => $sale->id,
                'items_count' => $sale->items->count(),
                'company_name' => $globalCompany->company_name
            ]);
            
            // Test view rendering first
            try {
                $html = view('admin.pos.receipt-a4', compact('sale', 'globalCompany'))->render();
                Log::info('Debug: View rendered successfully', ['html_length' => strlen($html)]);
            } catch (\Exception $e) {
                Log::error('Debug: View rendering failed', ['error' => $e->getMessage()]);
                return response('View rendering failed: ' . $e->getMessage(), 500);
            }
            
            // Test PDF generation
            try {
                Log::info('Debug: Starting PDF generation');
                
                $pdf = Pdf::loadView('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
                $pdf->setPaper('A4', 'portrait');
                
                // Set PDF options for better compatibility
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'DejaVu Sans',
                    'dpi' => 96,
                    'debugKeepTemp' => false,
                    'chroot' => storage_path('app'),
                    'logOutputFile' => storage_path('logs/dompdf.log'),
                    'tempDir' => storage_path('app/temp'),
                    'fontDir' => storage_path('fonts'),
                    'fontCache' => storage_path('fonts'),
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false
                ]);
                
                Log::info('Debug: PDF object created, generating output');
                
                $pdfOutput = $pdf->output();
                
                Log::info('Debug: PDF output generated', [
                    'size' => strlen($pdfOutput),
                    'is_pdf' => substr($pdfOutput, 0, 4) === '%PDF'
                ]);
                
                if (substr($pdfOutput, 0, 4) !== '%PDF') {
                    Log::error('Debug: Generated content is not a valid PDF', [
                        'first_100_chars' => substr($pdfOutput, 0, 100)
                    ]);
                    return response('Invalid PDF generated', 500);
                }
                
                // Generate filename
                $filename = 'debug_bill_' . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
                
                Log::info('Debug: Returning PDF response', ['filename' => $filename]);
                
                // Return PDF with explicit headers
                return response($pdfOutput, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($pdfOutput),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-PDF-Debug' => 'true'
                ]);
                
            } catch (\Exception $e) {
                Log::error('Debug: PDF generation failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                return response('PDF generation failed: ' . $e->getMessage(), 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Debug: downloadBillDebug failed completely', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response('Complete failure: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Test method to return just the HTML view
     * Add this as a new method in your PosController
     */
    public function viewBillDebug(PosSale $sale)
    {
        try {
            // Load sale relationships
            $sale->load(['items.product', 'cashier']);
            
            // Get simple company data
            $globalCompany = (object) [
                'company_name' => 'Green Valley Herbs',
                'company_address' => 'Natural & Organic Products Store',
                'company_phone' => '',
                'company_email' => '',
                'gst_number' => '',
                'company_logo' => null
            ];
            
            // Return just the HTML view for testing
            return view('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
            
        } catch (\Exception $e) {
            return response('View debug failed: ' . $e->getMessage(), 500);
        }
    }
}
