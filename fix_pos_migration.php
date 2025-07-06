<?php

/**
 * Quick fix script for POS migration issues
 * Run this if you encounter migration errors
 */

echo "POS Migration Fix Script\n";
echo "========================\n\n";

try {
    // Check Laravel version
    $laravel = app();
    echo "Laravel Version: " . $laravel->version() . "\n";
    
    // Check if tables exist
    $tablesExist = [
        'pos_sales' => Schema::hasTable('pos_sales'),
        'pos_sale_items' => Schema::hasTable('pos_sale_items'),
    ];
    
    echo "Table Status:\n";
    foreach ($tablesExist as $table => $exists) {
        echo "- {$table}: " . ($exists ? "✅ Exists" : "❌ Missing") . "\n";
    }
    
    if (!$tablesExist['pos_sales'] || !$tablesExist['pos_sale_items']) {
        echo "\n❌ Core POS tables missing. Please run basic migrations first.\n";
        exit(1);
    }
    
    echo "\n🔄 Applying fixes...\n";
    
    // Fix pos_sale_items table
    if (!Schema::hasColumn('pos_sale_items', 'tax_percentage')) {
        Schema::table('pos_sale_items', function ($table) {
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('discount_amount');
        });
        echo "✅ Added tax_percentage to pos_sale_items\n";
    }
    
    if (!Schema::hasColumn('pos_sale_items', 'tax_amount')) {
        Schema::table('pos_sale_items', function ($table) {
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_percentage');
        });
        echo "✅ Added tax_amount to pos_sale_items\n";
    }
    
    if (!Schema::hasColumn('pos_sale_items', 'discount_percentage')) {
        Schema::table('pos_sale_items', function ($table) {
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_amount');
        });
        echo "✅ Added discount_percentage to pos_sale_items\n";
    }
    
    if (!Schema::hasColumn('pos_sale_items', 'company_id')) {
        Schema::table('pos_sale_items', function ($table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });
        echo "✅ Added company_id to pos_sale_items\n";
    }
    
    // Fix pos_sales table
    if (!Schema::hasColumn('pos_sales', 'company_id')) {
        Schema::table('pos_sales', function ($table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });
        echo "✅ Added company_id to pos_sales\n";
    }
    
    // Check current payment method enum values
    try {
        DB::statement("ALTER TABLE pos_sales MODIFY COLUMN payment_method ENUM('cash', 'card', 'upi', 'gpay', 'paytm', 'phonepe', 'mixed') DEFAULT 'cash'");
        echo "✅ Updated payment_method enum values\n";
    } catch (Exception $e) {
        echo "⚠️  Payment method enum update skipped (might already be updated)\n";
    }
    
    // Add indexes safely
    try {
        Schema::table('pos_sales', function ($table) {
            $table->index('company_id');
        });
        echo "✅ Added company_id index to pos_sales\n";
    } catch (Exception $e) {
        echo "⚠️  Company ID index might already exist\n";
    }
    
    try {
        Schema::table('pos_sales', function ($table) {
            $table->index('customer_phone');
        });
        echo "✅ Added customer_phone index to pos_sales\n";
    } catch (Exception $e) {
        echo "⚠️  Customer phone index might already exist\n";
    }
    
    echo "\n🎉 Migration fixes completed successfully!\n";
    
    // Mark migrations as completed
    $timestamp1 = '2025_07_06_000001_add_tax_fields_to_pos_sale_items';
    $timestamp2 = '2025_07_06_000002_enhance_pos_sales_table';
    
    DB::table('migrations')->updateOrInsert(
        ['migration' => $timestamp1],
        ['migration' => $timestamp1, 'batch' => DB::table('migrations')->max('batch') + 1]
    );
    
    DB::table('migrations')->updateOrInsert(
        ['migration' => $timestamp2],
        ['migration' => $timestamp2, 'batch' => DB::table('migrations')->max('batch')]
    );
    
    echo "✅ Migration records updated\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
    exit(1);
}

echo "\nDatabase structure is now ready for enhanced POS functionality!\n";
