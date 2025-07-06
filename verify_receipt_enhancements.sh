#!/bin/bash

# POS Receipt Enhancement Verification Script

echo "=================================================="
echo "POS Receipt & Bill Download Verification"
echo "=================================================="
echo ""

echo "🔍 Checking receipt view files..."

# Check if receipt files exist and contain enhanced features
RECEIPT_FILES=(
    "resources/views/admin/pos/receipt.blade.php"
    "resources/views/admin/pos/receipt-pdf.blade.php" 
    "resources/views/admin/pos/receipt-a4.blade.php"
)

for file in "${RECEIPT_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
        
        # Check for discount display
        if grep -q "discount_amount" "$file"; then
            echo "  ✅ Contains discount display code"
        else
            echo "  ❌ Missing discount display code"
        fi
        
        # Check for tax display
        if grep -q "tax_amount" "$file"; then
            echo "  ✅ Contains tax display code"
        else
            echo "  ❌ Missing tax display code"
        fi
        
        # Check for enhanced totals
        if grep -q "Items Gross\|Item Discounts" "$file"; then
            echo "  ✅ Contains enhanced totals section"
        else
            echo "  ❌ Missing enhanced totals section"
        fi
        
    else
        echo "❌ $file not found"
    fi
    echo ""
done

echo "🧪 Running receipt functionality test..."
php artisan tinker --execute="require_once 'test_receipt_enhancements.php';"

echo ""
echo "🌐 Next Steps:"
echo "=============="
echo "1. Visit your POS system: /admin/pos"
echo "2. Create a test sale with discounts and taxes"
echo "3. Check the receipt at: /admin/pos/receipt/{sale_id}"
echo "4. Test bill download at: /admin/pos/sales/{sale_id}/download-bill"
echo ""
echo "✨ Features to verify:"
echo "• Item-level discounts are shown on each line"
echo "• Tax amounts are displayed with percentages"
echo "• Totals section shows discount breakdown"
echo "• PDF downloads are properly formatted"
echo "• Strikethrough pricing for discounted items"
echo ""
echo "📄 Documentation:"
echo "• POS_RECEIPT_ENHANCEMENT_SUMMARY.md - Complete details"
echo "• POS_QUICK_REFERENCE_GUIDE.md - User guide"
echo ""
echo "🎉 Receipt enhancement verification complete!"
