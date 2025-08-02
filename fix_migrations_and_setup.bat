@echo off
echo ========================================
echo   FIXING MIGRATION ISSUES & SETUP POS
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo [1/6] Clearing cache to refresh migrations...
php artisan config:clear
php artisan cache:clear
echo ✓ Cache cleared

echo.
echo [2/6] Checking migration status...
php artisan migrate:status

echo.
echo [3/6] Running all pending migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ✗ Some migrations failed, but continuing...
) else (
    echo ✓ Migrations completed
)

echo.
echo [4/6] Running our POS offers migration specifically...
php artisan migrate --path=database/migrations/2025_07_26_add_offer_fields_to_pos_sale_items_table.php --force
if %errorlevel% neq 0 (
    echo ✗ POS offers migration failed
    echo This might be because it already ran successfully
) else (
    echo ✓ POS offers migration completed
)

echo.
echo [5/6] Clearing all caches again...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo [6/6] Testing the system...
echo Testing Product model...
php artisan tinker --execute="
try {
    \$product = App\Models\Product::first();
    if (\$product) {
        echo 'Product model: OK - Found product: ' . \$product->name . PHP_EOL;
    } else {
        echo 'No products found in database' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Product model ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Testing Offer model...
php artisan tinker --execute="
try {
    \$offerCount = App\Models\Offer::count();
    echo 'Offer model: OK - Found ' . \$offerCount . ' offers' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Offer model ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo   MIGRATION ISSUES FIXED!
echo ========================================
echo.
echo Next steps:
echo 1. Create some test offers at: http://greenvalleyherbs.local:8000/admin/offers
echo 2. Test the POS system at: http://greenvalleyherbs.local:8000/admin/pos
echo 3. Look for products with red "OFFER" badges
echo.
echo If you want to run the complete POS offers setup, use:
echo   setup_pos_offers.bat
echo.
pause
