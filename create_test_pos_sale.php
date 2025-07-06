<?php

/**
 * Create Test POS Sale with Discounts and Taxes
 * 
 * This script creates a sample POS sale to test the enhanced
 * discount and tax functionality
 */

echo "Creating Test POS Sale\n";
echo "======================\n\n";

try {
    // Get a sample product
    $product = \App\Models\Product::where('stock', '>', 0)->first();
    
    if (!$product) {
        echo "❌ No products found with stock. Please add some products first.\n";
        return;
    }
    
    echo "🛍️  Using product: {$product->name} (Stock: {$product->stock})\n";
    echo "💰 Product price: ₹" . number_format($product->price, 2) . "\n";
    echo "📊 Product tax: {$product->tax_percentage}%\n\n";
    
    // Get current company ID
    $companyId = session('selected_company_id') ?? auth()->user()->company_id ?? 1;
    echo "🏢 Company ID: {$companyId}\n\n";
    
    // Simulate a POS sale with discounts and taxes
    DB::beginTransaction();
    
    try {
        // Create sample sale data
        $quantity = 2;
        $unitPrice = $product->price;
        $itemGrossAmount = $quantity * $unitPrice;
        $itemDiscountAmount = 15.00; // ₹15 discount
        $itemDiscountPercentage = round(($itemDiscountAmount / $itemGrossAmount) * 100, 2);
        $itemNetAmount = $itemGrossAmount - $itemDiscountAmount;
        
        // Calculate tax on net amount
        $itemTaxAmount = ($itemNetAmount * $product->tax_percentage) / 100;
        $itemTotalAmount = $itemNetAmount + $itemTaxAmount;
        
        // Sale level calculations
        $subtotal = $itemNetAmount;
        $totalTax = $itemTaxAmount;
        $cgstAmount = $totalTax / 2;
        $sgstAmount = $totalTax / 2;
        $saleDiscountAmount = 10.00; // Additional ₹10 sale discount
        $finalTotal = $subtotal + $totalTax - $saleDiscountAmount;
        
        echo "📋 Creating test sale with following details:\n";
        echo "─────────────────────────────────────────────\n";
        echo "Item: {$product->name} x {$quantity}\n";
        echo "Gross Amount: ₹" . number_format($itemGrossAmount, 2) . "\n";
        echo "Item Discount: ₹" . number_format($itemDiscountAmount, 2) . " ({$itemDiscountPercentage}%)\n";
        echo "Net Amount: ₹" . number_format($itemNetAmount, 2) . "\n";
        echo "Tax ({$product->tax_percentage}%): ₹" . number_format($itemTaxAmount, 2) . "\n";
        echo "CGST: ₹" . number_format($cgstAmount, 2) . "\n";
        echo "SGST: ₹" . number_format($sgstAmount, 2) . "\n";
        echo "Sale Discount: ₹" . number_format($saleDiscountAmount, 2) . "\n";
        echo "Final Total: ₹" . number_format($finalTotal, 2) . "\n\n";
        
        // Create the POS sale
        $sale = \App\Models\PosSale::create([
            'company_id' => $companyId,
            'sale_date' => now()->toDateString(),
            'customer_name' => 'Test Customer',
            'customer_phone' => '9876543210',
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'custom_tax_enabled' => false,
            'custom_tax_amount' => 0,
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'discount_amount' => $saleDiscountAmount,
            'total_amount' => $finalTotal,
            'paid_amount' => $finalTotal,
            'change_amount' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'notes' => 'Test sale created by verification script',
            'tax_notes' => null,
            'cashier_id' => auth()->id() ?? 1
        ]);
        
        echo "✅ Created POS Sale: {$sale->invoice_number}\n";
        
        // Create the sale item
        $saleItem = \App\Models\PosSaleItem::create([
            'pos_sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $itemDiscountAmount,
            'discount_percentage' => $itemDiscountPercentage,
            'tax_percentage' => $product->tax_percentage,
            'tax_amount' => $itemTaxAmount,
            'total_amount' => $itemTotalAmount,
            'company_id' => $companyId
        ]);
        
        echo "✅ Created Sale Item: {$saleItem->product_name}\n\n";
        
        DB::commit();
        
        echo "🎉 Test sale created successfully!\n\n";
        
        echo "🔗 Test URLs:\n";
        echo "═══════════\n";
        echo "Web Receipt: /admin/pos/receipt/{$sale->id}\n";
        echo "Download Bill: /admin/pos/sales/{$sale->id}/download-bill\n";
        echo "Sale Details: /admin/pos/sales/{$sale->id}\n\n";
        
        echo "📋 What to check in the receipt:\n";
        echo "═══════════════════════════════\n";
        echo "✓ Item shows: {$product->name}\n";
        echo "✓ Quantity: {$quantity} x ₹" . number_format($unitPrice, 2) . "\n";
        echo "✓ Item Discount: -₹" . number_format($itemDiscountAmount, 2) . " ({$itemDiscountPercentage}%)\n";
        echo "✓ Tax: {$product->tax_percentage}% = ₹" . number_format($itemTaxAmount, 2) . "\n";
        echo "✓ Item Total: ₹" . number_format($itemTotalAmount, 2) . "\n";
        echo "✓ CGST: ₹" . number_format($cgstAmount, 2) . "\n";
        echo "✓ SGST: ₹" . number_format($sgstAmount, 2) . "\n";
        echo "✓ Additional Discount: ₹" . number_format($saleDiscountAmount, 2) . "\n";
        echo "✓ Final Total: ₹" . number_format($finalTotal, 2) . "\n\n";
        
        echo "🧪 Now visit the receipt URL to see the enhanced display!\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "❌ Error creating test sale: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "💡 Make sure:\n";
    echo "1. Database migrations have been run\n";
    echo "2. Products exist with stock > 0\n";
    echo "3. You are logged in\n";
}
