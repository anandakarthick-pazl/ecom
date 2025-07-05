<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\AppSetting;
use App\Models\Notification;
use App\Mail\LowStockAlertMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:check-low';

    /**
     * The console command description.
     */
    protected $description = 'Check for low stock products and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lowStockThreshold = AppSetting::get('low_stock_threshold', 10);
        
        $lowStockProducts = Product::where('status', 'active')
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low stock products found.');
            return 0;
        }

        $this->info('Found ' . $lowStockProducts->count() . ' low stock products.');

        // Create admin notification
        Notification::createForAdmin(
            'low_stock',
            'Low Stock Alert',
            $lowStockProducts->count() . ' products are running low in stock',
            [
                'product_count' => $lowStockProducts->count(),
                'threshold' => $lowStockThreshold,
                'products' => $lowStockProducts->pluck('name', 'id')->toArray()
            ]
        );

        // Send email alert if enabled
        if (AppSetting::get('low_stock_alert', true) && AppSetting::get('email_notifications', true)) {
            try {
                $adminEmail = AppSetting::get('company_email');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new LowStockAlertMail($lowStockProducts));
                    $this->info('Low stock alert email sent to: ' . $adminEmail);
                }
            } catch (\Exception $e) {
                $this->error('Failed to send low stock alert email: ' . $e->getMessage());
            }
        }

        foreach ($lowStockProducts as $product) {
            $this->line("- {$product->name}: {$product->stock_quantity} units remaining");
        }

        return 0;
    }
}
