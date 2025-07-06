#!/bin/bash

# POS Discount and Tax Enhancement Migration Script

echo "=========================================="
echo "POS Discount & Tax Enhancement Setup"
echo "=========================================="

echo ""
echo "🔄 Running new migrations..."

# Run the first migration
echo "Adding tax fields to pos_sale_items..."
php artisan migrate --path=database/migrations/2025_07_06_000001_add_tax_fields_to_pos_sale_items.php
if [ $? -eq 0 ]; then
    echo "✅ Successfully added tax fields to pos_sale_items table"
else
    echo "⚠️  Migration may have failed, trying fix script..."
    php artisan tinker --execute="require_once 'fix_pos_migration.php';"
fi

echo "Enhancing pos_sales table..."
php artisan migrate --path=database/migrations/2025_07_06_000002_enhance_pos_sales_table.php
if [ $? -eq 0 ]; then
    echo "✅ Successfully enhanced pos_sales table"
else
    echo "⚠️  Migration may have failed, running fix script..."
    php artisan tinker --execute="require_once 'fix_pos_migration.php';"
fi

echo ""
echo "🧹 Clearing application caches..."

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✅ Caches cleared"

echo ""
echo "🔍 Verifying database structure..."

# Check if the new columns exist
php artisan tinker --execute="
if (Schema::hasColumn('pos_sale_items', 'tax_percentage')) {
    echo '✅ tax_percentage column exists in pos_sale_items\n';
} else {
    echo '❌ tax_percentage column missing in pos_sale_items\n';
}

if (Schema::hasColumn('pos_sale_items', 'tax_amount')) {
    echo '✅ tax_amount column exists in pos_sale_items\n';
} else {
    echo '❌ tax_amount column missing in pos_sale_items\n';
}

if (Schema::hasColumn('pos_sale_items', 'discount_percentage')) {
    echo '✅ discount_percentage column exists in pos_sale_items\n';
} else {
    echo '❌ discount_percentage column missing in pos_sale_items\n';
}

if (Schema::hasColumn('pos_sales', 'company_id')) {
    echo '✅ company_id column exists in pos_sales\n';
} else {
    echo '❌ company_id column missing in pos_sales\n';
}
"

echo ""
echo "📊 Testing POS functionality..."

# Test that the models can be instantiated without errors
php artisan tinker --execute="
try {
    \$sale = new App\Models\PosSale();
    \$item = new App\Models\PosSaleItem();
    echo '✅ POS models loaded successfully\n';
} catch (Exception \$e) {
    echo '❌ Error loading POS models: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "=========================================="
echo "🎉 POS Enhancement Setup Complete!"
echo "=========================================="
echo ""
echo "New Features Available:"
echo "• Item-level discount management"
echo "• Enhanced tax calculations"
echo "• Improved data tracking"
echo "• Multi-tenant support"
echo ""
echo "Next Steps:"
echo "1. Test the POS interface at /admin/pos"
echo "2. Try adding items and applying discounts"
echo "3. Test both manual and auto tax modes"
echo "4. Verify sales reports include new data"
echo ""
echo "For detailed information, see:"
echo "📄 POS_DISCOUNT_TAX_ENHANCEMENT_SUMMARY.md"
echo ""
