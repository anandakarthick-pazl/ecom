@echo off
echo ========================================
echo   TESTING POS OFFERS INTEGRATION
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo [TEST 1] Checking Product model...
php artisan tinker --execute="
try {
    \$product = App\Models\Product::first();
    if (\$product) {
        echo 'Product model: OK' . PHP_EOL;
        echo 'Product name: ' . \$product->name . PHP_EOL;
        echo 'Product price: ₹' . \$product->price . PHP_EOL;
    } else {
        echo 'No products found in database' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Product model ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo [TEST 2] Checking Offer model...
php artisan tinker --execute="
try {
    \$offer = App\Models\Offer::first();
    if (\$offer) {
        echo 'Offer model: OK' . PHP_EOL;
        echo 'Offer name: ' . \$offer->name . PHP_EOL;
        echo 'Offer type: ' . \$offer->type . PHP_EOL;
        echo 'Offer value: ' . \$offer->value . PHP_EOL;
    } else {
        echo 'No offers found - you need to create some offers first' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Offer model ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo [TEST 3] Checking OfferService...
php artisan tinker --execute="
try {
    \$service = app('App\Services\OfferService');
    echo 'OfferService: OK' . PHP_EOL;
    
    \$product = App\Models\Product::first();
    if (\$product) {
        \$effectivePrice = \$service->getEffectivePrice(\$product);
        echo 'Effective price calculation: OK' . PHP_EOL;
        echo 'Original price: ₹' . \$product->price . PHP_EOL;
        echo 'Effective price: ₹' . \$effectivePrice . PHP_EOL;
        
        if (\$effectivePrice < \$product->price) {
            echo 'OFFER APPLIED! Savings: ₹' . (\$product->price - \$effectivePrice) . PHP_EOL;
        } else {
            echo 'No offers applicable for this product' . PHP_EOL;
        }
    }
} catch (Exception \$e) {
    echo 'OfferService ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo [TEST 4] Checking POS Controller...
php artisan route:list --name=pos --compact
if %errorlevel% neq 0 (
    echo ✗ POS routes not found
) else (
    echo ✓ POS routes available
)

echo.
echo [TEST 5] Checking database migration...
php artisan migrate:status | findstr "pos_sale_items"
if %errorlevel% neq 0 (
    echo ✗ POS sale items migration not found
) else (
    echo ✓ POS sale items migration found
)

echo.
echo ========================================
echo   TEST SUMMARY
echo ========================================
echo.
echo If all tests passed, your POS offers integration is ready!
echo.
echo TO CREATE TEST OFFERS:
echo 1. Go to: http://greenvalleyherbs.local:8000/admin/offers
echo 2. Click "Create New Offer"
echo 3. Set up a test offer (e.g., 10%% off all products)
echo 4. Make sure it's active and has valid dates
echo.
echo TO TEST POS SYSTEM:
echo 1. Go to: http://greenvalleyherbs.local:8000/admin/pos
echo 2. Look for products with red "OFFER" badges
echo 3. Add them to cart and verify discounted prices
echo.
echo If you see any errors above, please fix them before proceeding.
echo.
pause
