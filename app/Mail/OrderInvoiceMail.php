<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\SuperAdmin\Company;
use App\Traits\HandlesCompanyData;
use App\Services\BillPDFService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels, HandlesCompanyData;

    public $order;
    public $pdfPath;
    public $company;
    public $generatePdf;
    public $cleanupPdf;
    private $tempPdfPath;

    /**
     * Create a new message instance.
     * 
     * @param Order $order
     * @param string|null $pdfPath Path to existing PDF file
     * @param bool $generatePdf Whether to auto-generate PDF if none provided
     */
    public function __construct(Order $order, $pdfPath = null, $generatePdf = true)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->generatePdf = $generatePdf;
        $this->cleanupPdf = false;
        $this->tempPdfPath = null;
        $this->company = $this->getStandardizedCompanyData($order->company_id ?? null);
        
        // Generate PDF if none provided and auto-generation is enabled
        if (!$this->pdfPath && $this->generatePdf) {
            $this->generateInvoicePdf();
        }
    }

    /**
     * Generate invoice PDF automatically
     */
    private function generateInvoicePdf()
    {
        try {
            Log::info('Auto-generating PDF for email attachment', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number
            ]);
            
            // Try using BillPDFService first (if available)
            if (class_exists('App\Services\BillPDFService')) {
                $billService = new BillPDFService();
                $pdfResult = $billService->generateOrderBill($this->order);
                
                if ($pdfResult['success']) {
                    $this->tempPdfPath = $pdfResult['file_path'];
                    $this->pdfPath = $pdfResult['file_path'];
                    $this->cleanupPdf = true;
                    return;
                }
            }
            
            // Fallback to DomPDF generation
            $this->generateFallbackPdf();
            
        } catch (\Exception $e) {
            Log::error('Failed to auto-generate PDF for email', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            
            // Try fallback PDF generation
            $this->generateFallbackPdf();
        }
    }
    
    /**
     * Generate PDF using DomPDF as fallback
     */
    private function generateFallbackPdf()
    {
        try {
            Log::info('Generating fallback PDF using DomPDF', [
                'order_id' => $this->order->id
            ]);
            
            // Load order relationships if not already loaded
            if (!$this->order->relationLoaded('items')) {
                $this->order->load(['items.product', 'customer']);
            }
            
            // Get company data for PDF with proper tenant context
            $globalCompany = (object) $this->company;
            
            // Set memory limit for PDF generation
            $originalMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '512M');
            
            // Generate PDF using DomPDF with optimized settings
            $pdf = Pdf::loadView('admin.orders.invoice-pdf', [
                'order' => $this->order,
                'globalCompany' => $globalCompany,
                'company' => $this->company // Additional company data array
            ]);
            
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans', // Better Unicode support
                'dpi' => 96,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'debugKeepTemp' => false,
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true,
                'fontHeightRatio' => 1.1,
                'chroot' => false
            ]);
            
            // Create temporary file
            $filename = 'invoice-' . $this->order->order_number . '-' . time() . '.pdf';
            $tempDir = storage_path('app/temp');
            $tempPath = $tempDir . '/' . $filename;
            
            // Ensure temp directory exists
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Generate and save PDF
            $pdfOutput = $pdf->output();
            file_put_contents($tempPath, $pdfOutput);
            
            // Restore original memory limit
            ini_set('memory_limit', $originalMemoryLimit);
            
            // Verify file was created successfully
            if (file_exists($tempPath) && filesize($tempPath) > 0) {
                $this->tempPdfPath = $tempPath;
                $this->pdfPath = $tempPath;
                $this->cleanupPdf = true;
                
                Log::info('Fallback PDF generated successfully', [
                    'order_id' => $this->order->id,
                    'file_path' => $tempPath,
                    'file_size' => $this->formatBytes(filesize($tempPath)),
                    'company_name' => $this->company['name'] ?? 'Unknown'
                ]);
            } else {
                throw new \Exception('PDF file was not created or is empty');
            }
            
        } catch (\Exception $e) {
            // Restore original memory limit in case of error
            if (isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            
            Log::error('Fallback PDF generation failed', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'company_id' => $this->order->company_id ?? 'Unknown'
            ]);
            
            // PDF generation failed, email will be sent without attachment
            $this->pdfPath = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice for Order ' . $this->order->order_number . ' - ' . $this->company['name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
            with: [
                'order' => $this->order,
                'company' => $this->company,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->pdfPath) {
            try {
                // Check if file exists at the specified path
                $filePath = $this->pdfPath;
                
                // Try different possible paths
                $possiblePaths = [
                    $filePath, // Direct path
                    storage_path('app/public/' . $filePath), // Public storage path
                    storage_path('app/' . $filePath), // App storage path
                    public_path('storage/' . $filePath), // Public symlink path
                ];
                
                $validPath = null;
                foreach ($possiblePaths as $path) {
                    if (file_exists($path) && is_readable($path) && filesize($path) > 0) {
                        $validPath = $path;
                        break;
                    }
                }
                
                if ($validPath) {
                    $fileSize = filesize($validPath);
                    // Check if file size is reasonable (max 10MB)
                    if ($fileSize > 10 * 1024 * 1024) {
                        Log::warning('PDF file too large for email attachment', [
                            'order_id' => $this->order->id,
                            'file_size' => $fileSize,
                            'file_path' => $validPath
                        ]);
                        return $attachments;
                    }
                    
                    Log::info('Attaching PDF to email', [
                        'order_id' => $this->order->id,
                        'pdf_path' => $validPath,
                        'file_size' => $this->formatBytes($fileSize)
                    ]);
                    
                    $attachments[] = Attachment::fromPath($validPath)
                        ->as('Invoice-' . $this->order->order_number . '.pdf')
                        ->withMime('application/pdf');
                } else {
                    Log::warning('PDF file not found or not readable for email attachment', [
                        'order_id' => $this->order->id,
                        'attempted_paths' => $possiblePaths
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to attach PDF to email', [
                    'order_id' => $this->order->id,
                    'pdf_path' => $this->pdfPath,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            Log::info('No PDF path provided for email attachment', [
                'order_id' => $this->order->id,
                'generate_pdf' => $this->generatePdf
            ]);
        }
        
        return $attachments;
    }
    
    /**
     * Format bytes for human readable size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Build the message (called before sending)
     */
    public function build()
    {
        // This method is called by Laravel before sending
        // We can use it to perform any final preparations
        return $this;
    }
    
    /**
     * Handle a job failure (if queued)
     */
    public function failed(\Exception $exception)
    {
        Log::error('OrderInvoiceMail job failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Cleanup any temporary files
        $this->cleanupTempFile();
    }
    
    /**
     * Cleanup temporary files
     */
    private function cleanupTempFile()
    {
        if ($this->cleanupPdf && $this->tempPdfPath && file_exists($this->tempPdfPath)) {
            try {
                unlink($this->tempPdfPath);
                Log::info('Cleaned up temporary PDF file', [
                    'file_path' => $this->tempPdfPath
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to cleanup temporary PDF file', [
                    'file_path' => $this->tempPdfPath,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Cleanup temporary files after email is sent
     */
    public function __destruct()
    {
        $this->cleanupTempFile();
    }
}
