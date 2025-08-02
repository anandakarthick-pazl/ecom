<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderItemPricingService;

class UpdateOrderItemsPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-pricing 
                            {--dry-run : Preview changes without making them}
                            {--batch-size=100 : Number of items to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing order items with MRP and offer pricing details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        $this->info('Starting order items pricing update...');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        try {
            if ($dryRun) {
                // For dry run, just count the items that would be updated
                $itemsToUpdate = \App\Models\OrderItem::whereNull('mrp_price')
                                                     ->orWhere('mrp_price', 0)
                                                     ->count();
                
                $this->info("Items that would be updated: {$itemsToUpdate}");
                
                // Show sample of items
                $sampleItems = \App\Models\OrderItem::whereNull('mrp_price')
                                                   ->orWhere('mrp_price', 0)
                                                   ->with('product')
                                                   ->limit(5)
                                                   ->get();
                
                $this->info("\nSample items to be updated:");
                $this->table(
                    ['ID', 'Product Name', 'Current Price', 'MRP Price', 'Order ID'],
                    $sampleItems->map(function($item) {
                        return [
                            $item->id,
                            $item->product_name,
                            '₹' . number_format($item->price, 2),
                            $item->product ? '₹' . number_format($item->product->price, 2) : 'N/A',
                            $item->order_id
                        ];
                    })
                );
                
            } else {
                // Actually update the items
                $result = OrderItemPricingService::updateExistingOrderItemsWithPricing();
                
                $this->info("Update completed!");
                $this->info("Items updated: {$result['updated']}");
                $this->info("Items failed: {$result['failed']}");
                $this->info("Total processed: {$result['total']}");
                
                if ($result['failed'] > 0) {
                    $this->warn("Some items failed to update. Check the logs for details.");
                }
            }

        } catch (\Exception $e) {
            $this->error("Error during update: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info('Order items pricing update completed successfully!');
        return Command::SUCCESS;
    }
}
