@echo off
echo ================================================
echo TESTING INVOICE PRICING SYSTEM
echo ================================================
echo.
echo This script will test the new invoice pricing features...
echo.

echo 1. Checking database migration status...
php artisan migrate:status | findstr "add_pricing_details_to_order_items"

echo.
echo 2. Checking if OrderItemPricingService exists...
php artisan tinker --execute="echo class_exists('App\\Services\\OrderItemPricingService') ? 'Service exists' : 'Service missing';"

echo.
echo 3. Testing order item with pricing data...
php artisan tinker --execute="$item = App\Models\OrderItem::first(); if($item) { echo 'Order item found - ID: ' . $item->id; echo ', MRP: ' . ($item->mrp_price ?: 'Not set'); } else { echo 'No order items found'; }"

echo.
echo 4. Checking invoice views...
if exist "resources\views\admin\orders\invoice.blade.php" (
    echo ✓ Main invoice view exists
) else (
    echo ✗ Main invoice view missing
)

if exist "resources\views\admin\orders\invoice-pdf.blade.php" (
    echo ✓ PDF invoice view exists
) else (
    echo ✗ PDF invoice view missing
)

if exist "resources\views\admin\orders\print-a4.blade.php" (
    echo ✓ A4 print view exists
) else (
    echo ✗ A4 print view missing
)

if exist "resources\views\admin\orders\print-thermal.blade.php" (
    echo ✓ Thermal print view exists
) else (
    echo ✗ Thermal print view missing
)

echo.
echo 5. Checking service files...
if exist "app\Services\OrderItemPricingService.php" (
    echo ✓ OrderItemPricingService exists
) else (
    echo ✗ OrderItemPricingService missing
)

if exist "app\Console\Commands\UpdateOrderItemsPricing.php" (
    echo ✓ Pricing update command exists
) else (
    echo ✗ Pricing update command missing
)

echo.
echo ================================================
echo TEST RESULTS SUMMARY
echo ================================================
echo.
echo If all items show ✓, the installation was successful.
echo If any items show ✗, please re-run the installation.
echo.
echo To test invoice display:
echo 1. Go to /admin/orders in your browser
echo 2. Click on any order to view its details
echo 3. Check the invoice display for MRP and offer pricing
echo 4. Test PDF download and print functionality
echo.
pause
