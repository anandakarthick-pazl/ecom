<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\ProductStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BackInStockNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $notification;
    public $productUrl;
    public $unsubscribeUrl;
    public $customerName;

    /**
     * Create a new message instance.
     */
    public function __construct(Product $product, ProductStockNotification $notification)
    {
        $this->product = $product;
        $this->notification = $notification;
        $this->customerName = $notification->customer_name ?? 'Valued Customer';
        
        // Generate product URL
        $this->productUrl = route('product', $product->slug);
        
        // Generate unsubscribe URL with token for security
        $this->unsubscribeUrl = URL::signedRoute('stock-notification.unsubscribe', [
            'email' => $notification->customer_email,
            'product' => $product->id
        ]);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $companyName = $this->product->company->name ?? config('app.name', 'Our Store');
        
        return $this->view('emails.back-in-stock')
                    ->subject("🎉 Great News! \"{$this->product->name}\" is Back in Stock!")
                    ->from(config('mail.from.address'), $companyName)
                    ->with([
                        'product' => $this->product,
                        'notification' => $this->notification,
                        'customerName' => $this->customerName,
                        'productUrl' => $this->productUrl,
                        'unsubscribeUrl' => $this->unsubscribeUrl,
                        'companyName' => $companyName,
                        'companyUrl' => config('app.url'),
                        'supportEmail' => config('mail.from.address')
                    ]);
    }
}
