<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnhancedStockNotificationService;
use Illuminate\Support\Facades\Log;

class CheckRestockedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-restocked 
                            {--product= : Check specific product ID}
                            {--dry-run : Show what would be notified without sending}
                            {--force : Force notifications even if recently sent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for restocked products and send back-in-stock notifications to customers';

    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(EnhancedStockNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for restocked products...');
        
        $startTime = microtime(true);

        try {
            if ($this->option('product')) {
                $result = $this->checkSpecificProduct($this->option('product'));
            } else {
                $result = $this->checkAllProducts();
            }

            $executionTime = round(microtime(true) - $startTime, 2);

            if ($result['success']) {
                $this->info("✅ Task completed successfully in {$executionTime} seconds");
                $this->displayResults($result);
            } else {
                $this->error("❌ Task failed: " . $result['message']);
            }

            return $result['success'] ? 0 : 1;

        } catch (\Exception $e) {
            $this->error("💥 Command failed with exception: " . $e->getMessage());
            Log::error('CheckRestockedProducts command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Check specific product
     */
    protected function checkSpecificProduct($productId)
    {
        $this->info("🎯 Checking specific product ID: {$productId}");

        if ($this->option('dry-run')) {
            $this->warn("🔍 DRY RUN MODE - No notifications will be sent");
            
            // Get product info and pending notifications
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return ['success' => false, 'message' => "Product not found: {$productId}"];
            }

            $notifications = \App\Models\ProductStockNotification::getActiveNotificationsForProduct($productId);
            
            $this->table([
                'Product ID', 'Product Name', 'Current Stock', 'Pending Notifications', 'Will Notify'
            ], [[
                $product->id,
                $product->name,
                $product->stock,
                $notifications->count(),
                $product->isInStock() && $notifications->count() > 0 ? 'YES' : 'NO'
            ]]);

            return [
                'success' => true,
                'message' => 'Dry run completed',
                'processed' => $notifications->count() > 0 ? 1 : 0
            ];
        }

        return $this->notificationService->notifyCustomers($productId);
    }

    /**
     * Check all products
     */
    protected function checkAllProducts()
    {
        $this->info("🌍 Checking all products with pending notifications...");

        if ($this->option('dry-run')) {
            $this->warn("🔍 DRY RUN MODE - No notifications will be sent");
            return $this->showDryRunResults();
        }

        return $this->notificationService->checkAndNotifyRestockedProducts();
    }

    /**
     * Show dry run results
     */
    protected function showDryRunResults()
    {
        $pendingSummary = $this->notificationService->getPendingNotificationsSummary();
        
        $this->info("📊 Pending Notifications Summary:");
        $this->info("Total products with notifications: " . $pendingSummary['total_products']);
        $this->info("Total pending notifications: " . $pendingSummary['total_notifications']);
        $this->info("Email subscribers: " . $pendingSummary['total_email_subscribers']);
        $this->info("WhatsApp subscribers: " . $pendingSummary['total_whatsapp_subscribers']);

        if (!empty($pendingSummary['products'])) {
            $tableData = [];
            foreach ($pendingSummary['products'] as $product) {
                $tableData[] = [
                    $product['product_id'],
                    $product['product_name'],
                    $product['current_stock'],
                    $product['is_in_stock'] ? 'In Stock' : 'Out of Stock',
                    $product['notification_count'],
                    $product['email_subscribers'],
                    $product['whatsapp_subscribers'],
                    $product['is_in_stock'] ? 'YES' : 'NO'
                ];
            }

            $this->table([
                'ID', 'Product Name', 'Stock', 'Status', 'Total Notifications', 'Email', 'WhatsApp', 'Will Notify'
            ], $tableData);

            $willNotify = collect($pendingSummary['products'])->where('is_in_stock', true)->count();
            $this->info("🎯 Products that will trigger notifications: {$willNotify}");
        } else {
            $this->info("📭 No pending notifications found");
        }

        return [
            'success' => true,
            'message' => 'Dry run completed',
            'processed' => $pendingSummary['total_products']
        ];
    }

    /**
     * Display command results
     */
    protected function displayResults($result)
    {
        if (isset($result['processed'])) {
            $this->info("📈 Products processed: " . $result['processed']);
        }

        if (isset($result['stats'])) {
            $stats = $result['stats'];
            $this->info("📧 Email notifications sent: " . $stats['email_sent']);
            $this->info("📱 WhatsApp notifications sent: " . $stats['whatsapp_sent']);
            if ($stats['errors'] > 0) {
                $this->warn("⚠️  Errors encountered: " . $stats['errors']);
            }
        }

        if (isset($result['results']) && !empty($result['results'])) {
            $this->info("\n📋 Detailed Results:");
            foreach ($result['results'] as $productResult) {
                $status = $productResult['result']['success'] ? '✅' : '❌';
                $this->line("{$status} {$productResult['product_name']} (ID: {$productResult['product_id']})");
                
                if (isset($productResult['result']['stats'])) {
                    $stats = $productResult['result']['stats'];
                    $this->line("   📧 Email: {$stats['email_sent']}, 📱 WhatsApp: {$stats['whatsapp_sent']}, ❌ Errors: {$stats['errors']}");
                }
            }
        }

        // Show tips based on results
        if (isset($result['stats']) && $result['stats']['errors'] > 0) {
            $this->warn("\n💡 Tips for reducing errors:");
            $this->warn("   • Check WhatsApp configuration in admin panel");
            $this->warn("   • Verify email settings are properly configured");
            $this->warn("   • Check logs for detailed error information");
        }

        if (isset($result['processed']) && $result['processed'] > 0) {
            $this->info("\n🚀 Pro tip: Set up a cron job to run this command automatically:");
            $this->info("   */15 * * * * php artisan stock:check-restocked");
        }
    }
}
