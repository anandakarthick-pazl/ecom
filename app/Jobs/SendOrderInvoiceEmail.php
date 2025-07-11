<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\OrderInvoiceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\AppSetting;

class SendOrderInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $email;
    protected $pdfPath;
    protected $generatePdf;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, $email = null, $pdfPath = null, $generatePdf = true)
    {
        $this->order = $order;
        $this->email = $email ?: ($order->customer_email ?? $order->customer->email ?? null);
        $this->pdfPath = $pdfPath;
        $this->generatePdf = $generatePdf;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Validate email
            if (empty($this->email)) {
                throw new \Exception('No email address provided for order invoice');
            }

            // Check if email notifications are enabled
            if (!AppSetting::get('email_notifications', true)) {
                Log::info('Email notifications disabled, skipping invoice email', [
                    'order_id' => $this->order->id
                ]);
                return;
            }

            Log::info('Processing order invoice email job', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'email' => $this->email,
                'generate_pdf' => $this->generatePdf,
                'attempt' => $this->attempts()
            ]);

            // Load order relationships if needed
            if (!$this->order->relationLoaded('items')) {
                $this->order->load(['items.product', 'customer']);
            }

            // Send the email
            Mail::to($this->email)
                ->send(new OrderInvoiceMail($this->order, $this->pdfPath, $this->generatePdf));

            Log::info('Order invoice email sent successfully via queue', [
                'order_id' => $this->order->id,
                'email' => $this->email,
                'attempt' => $this->attempts()
            ]);

        } catch (\Exception $e) {
            Log::error('Order invoice email job failed', [
                'order_id' => $this->order->id,
                'email' => $this->email,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // If this is the last attempt, try sending without PDF
            if ($this->attempts() >= $this->tries && $this->generatePdf) {
                Log::info('Final attempt: trying to send email without PDF');
                
                try {
                    Mail::to($this->email)
                        ->send(new OrderInvoiceMail($this->order, null, false));
                    
                    Log::info('Order invoice email sent without PDF as final fallback');
                    return;
                } catch (\Exception $fallbackError) {
                    Log::error('Final fallback email also failed', [
                        'order_id' => $this->order->id,
                        'error' => $fallbackError->getMessage()
                    ]);
                }
            }

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Order invoice email job permanently failed', [
            'order_id' => $this->order->id,
            'email' => $this->email,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // You could add additional failure handling here, such as:
        // - Sending a notification to admin
        // - Creating a failed email record for retry later
        // - Updating order status or adding a note
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        // Progressive backoff: 30 seconds, then 5 minutes
        return [30, 300];
    }
}
