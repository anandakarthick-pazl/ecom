<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $statusMessage;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $statusMessage = null)
    {
        $this->order = $order;
        $this->statusMessage = $statusMessage ?? $this->getDefaultStatusMessage();
        $this->company = $this->getCompanyData();
    }

    /**
     * Get company data from Super Admin settings and tenant settings
     */
    private function getCompanyData()
    {
        // Get Super Admin settings
        $superAdminSettings = cache('super_admin_settings', []);
        
        // Get tenant settings if available
        $tenantSettings = [];
        try {
            if (class_exists('\App\Models\AppSetting')) {
                $tenantSettings = [
                    'company_name' => \App\Models\AppSetting::get('company_name', ''),
                    'company_email' => \App\Models\AppSetting::get('company_email', ''),
                    'company_phone' => \App\Models\AppSetting::get('company_phone', ''),
                    'company_address' => \App\Models\AppSetting::get('company_address', ''),
                    'company_logo' => \App\Models\AppSetting::get('company_logo', ''),
                    'primary_color' => \App\Models\AppSetting::get('primary_color', ''),
                    'secondary_color' => \App\Models\AppSetting::get('secondary_color', ''),
                ];
            }
        } catch (\Exception $e) {
            // Handle case where database/model is not available
        }
        
        return [
            'name' => $superAdminSettings['site_name'] ?? $tenantSettings['company_name'] ?? $superAdminSettings['company_name'] ?? 'Your Store',
            'email' => $superAdminSettings['admin_email'] ?? $tenantSettings['company_email'] ?? 'admin@example.com',
            'phone' => $superAdminSettings['company_phone'] ?? $tenantSettings['company_phone'] ?? '',
            'address' => $superAdminSettings['company_address'] ?? $tenantSettings['company_address'] ?? '',
            'logo' => $superAdminSettings['site_logo'] ?? $tenantSettings['company_logo'] ?? '',
            'primary_color' => $superAdminSettings['primary_color'] ?? $tenantSettings['primary_color'] ?? '#2c3e50',
            'secondary_color' => $tenantSettings['secondary_color'] ?? '#34495e',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Status Update - ' . $this->order->order_number . ' - ' . $this->company['name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
            with: [
                'order' => $this->order,
                'statusMessage' => $this->statusMessage,
                'company' => $this->company,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get default status message based on order status
     */
    private function getDefaultStatusMessage(): string
    {
        return match($this->order->status) {
            'pending' => 'Your order has been received and is being processed.',
            'processing' => 'Your order is currently being prepared for shipment.',
            'shipped' => 'Your order has been shipped and is on its way to you.',
            'delivered' => 'Your order has been successfully delivered.',
            'cancelled' => 'Your order has been cancelled.',
            default => 'Your order status has been updated.',
        };
    }
}
