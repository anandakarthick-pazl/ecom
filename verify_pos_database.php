<?php

/**
 * POS Database Structure Verification Script
 * 
 * This script checks if the database has all the required fields
 * for the enhanced POS discount and tax functionality
 */

echo "POS Database Structure Verification\n";
echo "===================================\n\n";

try {
    // Check if pos_sales table has the required fields
    echo "🔍 Checking pos_sales table structure...\n";
    
    $posSalesFields = [
        'company_id' => 'Company ID for multi-tenant support',
        'cgst_amount' => 'CGST amount field',
        'sgst_amount' => 'SGST amount field',
        'custom_tax_enabled' => 'Custom tax enabled flag',
        'custom_tax_amount' => 'Custom tax amount',
        'tax_notes' => 'Tax notes field'
    ];
    
    foreach ($posSalesFields as $field => $description) {
        if (Schema::hasColumn('pos_sales', $field)) {
            echo "✅ {$field} - {$description}\n";
        } else {
            echo "❌ {$field} - MISSING! {$description}\n";
        }
    }
    
    echo "\n🔍 Checking pos_sale_items table structure...\n";
    
    $posSaleItemsFields = [
        'tax_percentage' => 'Tax percentage per item',
        'tax_amount' => 'Tax amount per item', 
        'discount_amount' => 'Discount amount per item',
        'discount_percentage' => 'Discount percentage per item',
        'company_id' => 'Company ID for multi-tenant support'
    ];
    
    foreach ($posSaleItemsFields as $field => $description) {
        if (Schema::hasColumn('pos_sale_items', $field)) {
            echo "✅ {$field} - {$description}\n";
        } else {
            echo "❌ {$field} - MISSING! {$description}\n";
        }
    }
    
    echo "\n📊 Checking recent POS sales data...\n";
    
    // Get recent sales to check data
    $recentSales = \App\Models\PosSale::with(['items'])->latest()->take(3)->get();
    
    if ($recentSales->count() > 0) {
        echo "Found {$recentSales->count()} recent sales:\n\n";
        
        foreach ($recentSales as $index => $sale) {
            echo "Sale #" . ($index + 1) . " - {$sale->invoice_number}\n";
            echo "─────────────────────────────────────\n";
            
            // Check main sale fields
            echo "Main Sale Data:\n";
            echo "  Subtotal: ₹" . number_format($sale->subtotal ?? 0, 2) . "\n";
            echo "  Tax Amount: ₹" . number_format($sale->tax_amount ?? 0, 2) . "\n";
            echo "  CGST: ₹" . number_format($sale->cgst_amount ?? 0, 2) . "\n";
            echo "  SGST: ₹" . number_format($sale->sgst_amount ?? 0, 2) . "\n";
            echo "  Discount: ₹" . number_format($sale->discount_amount ?? 0, 2) . "\n";
            echo "  Custom Tax: " . ($sale->custom_tax_enabled ? 'Yes' : 'No') . "\n";
            echo "  Total: ₹" . number_format($sale->total_amount ?? 0, 2) . "\n";
            
            echo "\nItems Data:\n";
            foreach ($sale->items as $itemIndex => $item) {
                echo "  Item " . ($itemIndex + 1) . ": {$item->product_name}\n";
                echo "    Qty: {$item->quantity} | Price: ₹" . number_format($item->unit_price ?? 0, 2) . "\n";
                echo "    Discount: ₹" . number_format($item->discount_amount ?? 0, 2);
                if (isset($item->discount_percentage)) {
                    echo " ({$item->discount_percentage}%)";
                }
                echo "\n";
                echo "    Tax: {$item->tax_percentage}% = ₹" . number_format($item->tax_amount ?? 0, 2) . "\n";
                echo "    Total: ₹" . number_format($item->total_amount ?? 0, 2) . "\n";
            }
            echo "\n";
        }
    } else {
        echo "❌ No recent POS sales found. Create a test sale to verify functionality.\n\n";
    }
    
    echo "🔧 Database Fix Actions:\n";
    echo "═══════════════════════\n";
    
    // Check if migrations need to be run
    $migrationsToRun = [];
    
    if (!Schema::hasColumn('pos_sale_items', 'tax_percentage')) {
        $migrationsToRun[] = '2025_07_06_000001_add_tax_fields_to_pos_sale_items';
    }
    
    if (!Schema::hasColumn('pos_sales', 'custom_tax_enabled')) {
        $migrationsToRun[] = '2025_07_05_000005_add_custom_tax_to_pos_sales';
    }
    
    if (count($migrationsToRun) > 0) {
        echo "⚠️  Missing database fields detected!\n";
        echo "Run these commands to fix:\n\n";
        echo "php artisan migrate\n";
        echo "# OR run the fix script:\n";
        echo "php artisan tinker --execute=\"require_once 'fix_pos_migration.php';\"\n\n";
    } else {
        echo "✅ All required database fields are present!\n\n";
    }
    
    echo "🧪 Test Receipt Display:\n";
    echo "═══════════════════════\n";
    
    if ($recentSales->count() > 0) {
        $testSale = $recentSales->first();
        echo "Test your enhanced receipts:\n";
        echo "🌐 Web Receipt: /admin/pos/receipt/{$testSale->id}\n";
        echo "📄 Download Bill: /admin/pos/sales/{$testSale->id}/download-bill\n\n";
        
        echo "What you should see:\n";
        echo "• Item-level discounts with percentages\n";
        echo "• Tax amounts per item\n";
        echo "• Enhanced totals breakdown\n";
        echo "• CGST/SGST split display\n";
        echo "• Professional formatting\n\n";
    }
    
    echo "🎯 Next Steps:\n";
    echo "═════════════\n";
    echo "1. If missing fields: Run migration fix script\n";
    echo "2. Create new POS sale with discounts and taxes\n";
    echo "3. Check receipt display shows enhanced details\n";
    echo "4. Test PDF download functionality\n\n";
    
    echo "✅ Database structure verification complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error during verification: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "💡 Try running the migration fix script:\n";
    echo "php artisan tinker --execute=\"require_once 'fix_pos_migration.php';\"\n";
}
