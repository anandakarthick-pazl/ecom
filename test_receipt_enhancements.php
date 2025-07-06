<?php

/**
 * POS Receipt Test Script
 * 
 * This script tests the enhanced receipt functionality
 * Run this to verify that receipts display discounts and taxes correctly
 */

echo "POS Receipt Enhancement Test\n";
echo "===========================\n\n";

try {
    // Test database connection
    $sales = \App\Models\PosSale::with(['items.product', 'cashier'])->latest()->take(5)->get();
    
    if ($sales->isEmpty()) {
        echo "❌ No POS sales found. Please create a test sale first.\n";
        echo "💡 Go to /admin/pos and create a sample transaction.\n";
        return;
    }
    
    echo "📊 Found " . $sales->count() . " recent POS sales\n\n";
    
    foreach ($sales as $index => $sale) {
        echo "Sale #" . ($index + 1) . " - " . $sale->invoice_number . "\n";
        echo "────────────────────────────────────────\n";
        
        // Check basic sale data
        echo "✓ Invoice: " . $sale->invoice_number . "\n";
        echo "✓ Total: ₹" . number_format($sale->total_amount, 2) . "\n";
        echo "✓ Items: " . $sale->items->count() . "\n";
        
        // Check for enhanced fields
        $hasItemDiscounts = $sale->items->sum('discount_amount') > 0;
        $hasSaleDiscount = $sale->discount_amount > 0;
        $hasTax = $sale->tax_amount > 0;
        $hasCustomTax = $sale->custom_tax_enabled;
        
        echo "✓ Item Discounts: " . ($hasItemDiscounts ? "₹" . number_format($sale->items->sum('discount_amount'), 2) : "None") . "\n";
        echo "✓ Sale Discount: " . ($hasSaleDiscount ? "₹" . number_format($sale->discount_amount, 2) : "None") . "\n";
        echo "✓ Tax Amount: " . ($hasTax ? "₹" . number_format($sale->tax_amount, 2) : "None") . "\n";
        echo "✓ Custom Tax: " . ($hasCustomTax ? "Yes" : "No") . "\n";
        
        // Check item-level data
        echo "✓ Item Details:\n";
        foreach ($sale->items as $item) {
            $itemDiscount = $item->discount_amount ?? 0;
            $itemTax = $item->tax_amount ?? 0;
            echo "  - " . ($item->product->name ?? $item->product_name) . "\n";
            echo "    Qty: " . $item->quantity . " | Price: ₹" . number_format($item->unit_price, 2);
            if ($itemDiscount > 0) {
                echo " | Discount: ₹" . number_format($itemDiscount, 2);
            }
            if ($itemTax > 0) {
                echo " | Tax: ₹" . number_format($itemTax, 2);
            }
            echo "\n";
        }
        
        echo "\n";
    }
    
    // Test receipt URLs
    $testSale = $sales->first();
    echo "🔗 Receipt URLs for Sale: " . $testSale->invoice_number . "\n";
    echo "────────────────────────────────────────────────────\n";
    echo "Web Receipt: /admin/pos/receipt/" . $testSale->id . "\n";
    echo "Download Bill: /admin/pos/sales/" . $testSale->id . "/download-bill\n";
    echo "PDF Preview: /admin/pos/sales/" . $testSale->id . "/preview-bill\n\n";
    
    // Check receipt view files
    echo "📄 Receipt View Files Status:\n";
    echo "─────────────────────────────\n";
    
    $receiptFiles = [
        'resources/views/admin/pos/receipt.blade.php',
        'resources/views/admin/pos/receipt-pdf.blade.php',
        'resources/views/admin/pos/receipt-a4.blade.php'
    ];
    
    foreach ($receiptFiles as $file) {
        if (file_exists(base_path($file))) {
            $content = file_get_contents(base_path($file));
            $hasDiscountDisplay = strpos($content, 'discount_amount') !== false;
            $hasTaxDisplay = strpos($content, 'tax_amount') !== false;
            $hasEnhancedTotals = strpos($content, 'Items Gross') !== false || strpos($content, 'Item Discounts') !== false;
            
            echo "✓ " . basename($file) . "\n";
            echo "  - Discount Display: " . ($hasDiscountDisplay ? "✓" : "❌") . "\n";
            echo "  - Tax Display: " . ($hasTaxDisplay ? "✓" : "❌") . "\n";
            echo "  - Enhanced Totals: " . ($hasEnhancedTotals ? "✓" : "❌") . "\n";
        } else {
            echo "❌ " . basename($file) . " - File not found\n";
        }
    }
    
    echo "\n📋 Test Recommendations:\n";
    echo "────────────────────────────\n";
    echo "1. Visit: /admin/pos/receipt/" . $testSale->id . "\n";
    echo "2. Check that item discounts are displayed\n";
    echo "3. Verify tax amounts are shown correctly\n";
    echo "4. Test PDF download functionality\n";
    echo "5. Check that totals section shows breakdown\n\n";
    
    echo "🎉 Receipt enhancement test completed!\n";
    echo "Visit the URLs above to verify the visual appearance.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
