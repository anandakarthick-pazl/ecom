<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class SetupTestStockNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:setup-stock-notifications {--reset : Reset all products to have stock}';

    /**
     * The console command description.
     */
    protected $description = 'Set up test products for stock notification testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('reset')) {
            return $this->resetProducts();
        }
        
        $this->info('🔧 Setting up test out-of-stock products...');
        
        try {
            // Get some random products and set them out of stock
            $products = Product::where('stock', '>', 0)
                ->inRandomOrder()
                ->limit(5)
                ->get();
            
            if ($products->isEmpty()) {
                $this->warn('⚠️  No products with stock found. Creating test scenarios with existing products.');
                $products = Product::inRandomOrder()->limit(5)->get();
            }
            
            $updatedCount = 0;
            foreach ($products as $product) {
                $oldStock = $product->stock;
                $product->update(['stock' => 0]);
                $updatedCount++;
                
                $this->line("  📦 {$product->name} (ID: {$product->id}) - Stock: {$oldStock} → 0");
            }
            
            $this->info("✅ Updated {$updatedCount} products to be out of stock");
            
            // Show current out-of-stock products
            $outOfStock = Product::where('stock', '<=', 0)->count();
            $this->info("📊 Total out-of-stock products: {$outOfStock}");
            
            $this->newLine();
            $this->info('🎯 Test the notification system at:');
            $this->line('   http://greenvalleyherbs.local:8000/offer-products');
            $this->newLine();
            $this->info('🔧 Debug commands:');
            $this->line('   Open browser console and run: testNotificationSystem()');
            $this->line('   Test route: http://greenvalleyherbs.local:8000/test-stock-notification');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function resetProducts()
    {
        $this->info('🔄 Resetting all products to have stock...');
        
        $updated = Product::where('stock', '<=', 0)
            ->update(['stock' => rand(5, 50)]);
        
        $this->info("✅ Reset {$updated} products to have stock");
        
        return 0;
    }
}
