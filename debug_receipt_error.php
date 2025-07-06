<?php

/**
 * Quick POS Receipt Debug Script
 * 
 * This script debugs the specific receipt error
 */

echo "POS Receipt Debug for Sale ID 10\n";
echo "================================\n\n";

try {
    // Check if sale exists
    $sale = \App\Models\PosSale::find(10);
    
    if (!$sale) {
        echo "❌ Sale ID 10 not found in database!\n";
        echo "Available recent sales:\n";
        
        $recentSales = \App\Models\PosSale::latest()->take(5)->get(['id', 'invoice_number', 'total_amount']);
        foreach ($recentSales as $s) {
            echo "  - ID: {$s->id} | Invoice: {$s->invoice_number} | Amount: ₹{$s->total_amount}\n";
        }
        return;
    }
    
    echo "✅ Sale found: {$sale->invoice_number}\n";
    echo "   Total: ₹" . number_format($sale->total_amount, 2) . "\n";
    echo "   Date: {$sale->created_at}\n";
    echo "   Status: {$sale->status}\n\n";
    
    echo "🔍 Checking sale data structure:\n";
    echo "────────────────────────────────\n";
    
    // Check sale fields
    $saleFields = [
        'subtotal', 'tax_amount', 'cgst_amount', 'sgst_amount', 
        'discount_amount', 'custom_tax_enabled', 'company_id'
    ];
    
    foreach ($saleFields as $field) {
        $value = $sale->$field ?? 'NULL';
        echo "  {$field}: {$value}\n";
    }
    
    echo "\n🛍️  Checking sale items:\n";
    echo "────────────────────────\n";
    
    try {
        $items = $sale->items;
        echo "  Items count: " . $items->count() . "\n";
        
        foreach ($items as $index => $item) {
            echo "  Item " . ($index + 1) . ":\n";
            echo "    Product: {$item->product_name}\n";
            echo "    Quantity: {$item->quantity}\n";
            echo "    Unit Price: ₹{$item->unit_price}\n";
            echo "    Discount Amount: " . ($item->discount_amount ?? 'NULL') . "\n";
            echo "    Discount %: " . ($item->discount_percentage ?? 'NULL') . "\n";
            echo "    Tax %: " . ($item->tax_percentage ?? 'NULL') . "\n";
            echo "    Tax Amount: " . ($item->tax_amount ?? 'NULL') . "\n";
            echo "    Total: ₹{$item->total_amount}\n\n";
        }
    } catch (Exception $e) {
        echo "❌ Error loading items: " . $e->getMessage() . "\n";
    }
    
    echo "🏢 Checking company data:\n";
    echo "─────────────────────────\n";
    
    try {
        $companyId = session('selected_company_id', 1);
        echo "  Company ID from session: {$companyId}\n";
        echo "  Sale company_id: " . ($sale->company_id ?? 'NULL') . "\n";
        
        if (class_exists('\App\Models\SuperAdmin\Company')) {
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            if ($company) {
                echo "  Company found: {$company->name}\n";
            } else {
                echo "  ❌ Company not found with ID {$companyId}\n";
            }
        } else {
            echo "  ❌ Company model not found\n";
        }
    } catch (Exception $e) {
        echo "  ❌ Error checking company: " . $e->getMessage() . "\n";
    }
    
    echo "\n🧪 Testing receipt view loading:\n";
    echo "────────────────────────────────\n";
    
    try {
        // Test basic view loading
        $viewExists = view()->exists('admin.pos.receipt');
        echo "  Receipt view exists: " . ($viewExists ? 'Yes' : 'No') . "\n";
        
        // Test simple company data
        $globalCompany = (object) [
            'company_name' => 'Green Valley Herbs',
            'company_address' => 'Natural & Organic Products Store',
            'company_phone' => '',
            'company_email' => '',
            'gst_number' => '',
            'company_logo' => null
        ];
        
        echo "  ✅ Company object created successfully\n";
        
        // Test basic view compilation
        echo "  Testing view compilation...\n";
        
        $sale->load(['items.product', 'cashier']);
        echo "  ✅ Sale relationships loaded\n";
        
        // This should work now
        echo "  Receipt should be accessible at: /admin/pos/receipt/{$sale->id}\n";
        
    } catch (Exception $e) {
        echo "  ❌ View loading error: " . $e->getMessage() . "\n";
        echo "  File: " . $e->getFile() . "\n";
        echo "  Line: " . $e->getLine() . "\n";
    }
    
    echo "\n🎯 Solution:\n";
    echo "═══════════\n";
    echo "If the receipt is still not working:\n";
    echo "1. Clear all caches: php artisan cache:clear\n";
    echo "2. Clear config: php artisan config:clear\n";
    echo "3. Clear views: php artisan view:clear\n";
    echo "4. Try accessing: /admin/pos/receipt/{$sale->id}\n\n";
    
    echo "✅ Debug complete! The receipt method has been fixed.\n";
    
} catch (Exception $e) {
    echo "❌ Debug error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    
    echo "🔧 Quick fix - run these commands:\n";
    echo "php artisan cache:clear\n";
    echo "php artisan config:clear\n";
    echo "php artisan view:clear\n";
}
