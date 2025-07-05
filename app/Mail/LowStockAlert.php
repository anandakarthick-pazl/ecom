<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Low Stock Alert - ' . count($this->products) . ' Products Below Threshold',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: [
                'products' => $this->products,
            ],
        );
    }
}
