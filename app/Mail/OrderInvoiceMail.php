<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\SuperAdmin\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfPath;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $pdfPath = null)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->company = $this->getCompanyData();
    }

    /**
     * Get company data from companies table
     */
    private function getCompanyData()
    {
        // Get company from various sources
        $company = null;
        
        // 1. Try from order's company_id
        if ($this->order->company_id) {
            $company = Company::find($this->order->company_id);
        }
        
        // 2. Try from authenticated user
        if (!$company && auth()->user() && auth()->user()->company_id) {
            $company = Company::find(auth()->user()->company_id);
        }
        
        // 3. Try from session
        if (!$company && session('selected_company_id')) {
            $company = Company::find(session('selected_company_id'));
        }
        
        // 4. Get first active company as fallback
        if (!$company) {
            $company = Company::where('status', 'active')->first();
        }
        
        // Return company data or defaults
        if ($company) {
            return [
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => trim($company->address . ' ' . $company->city . ' ' . $company->state . ' ' . $company->postal_code),
                'logo' => $company->logo,
                'primary_color' => '#2d5016', // Default green theme
                'secondary_color' => '#4a7c28',
            ];
        }
        
        // Default fallback values
        return [
            'name' => 'Herbal Bliss',
            'email' => 'info@herbalbliss.com',
            'phone' => '+91 9876543210',
            'address' => '',
            'logo' => '',
            'primary_color' => '#2d5016',
            'secondary_color' => '#4a7c28',
        ];
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
        
        if ($this->pdfPath && file_exists(storage_path('app/public/' . $this->pdfPath))) {
            $attachments[] = Attachment::fromPath(storage_path('app/public/' . $this->pdfPath))
                ->as('Invoice-' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
