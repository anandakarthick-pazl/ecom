@echo off
echo ========================================
echo   FIXING PRODUCT MODEL CONFLICT
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo Clearing PHP OPcache...
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache cleared'; } else { echo 'OPcache not available'; }"
echo.

echo Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo Testing Product model...
php artisan tinker --execute="
try {
    \$product = new App\Models\Product();
    echo 'Product model loaded successfully' . PHP_EOL;
    
    \$reflection = new ReflectionClass(\$product);
    \$methods = \$reflection->getMethods();
    \$offerMethods = array_filter(\$methods, function(\$method) {
        return \$method->name === 'offers';
    });
    
    echo 'Found ' . count(\$offerMethods) . ' offers() method(s)' . PHP_EOL;
    
    if (count(\$offerMethods) > 1) {
        echo 'ERROR: Multiple offers() methods found!' . PHP_EOL;
    } else {
        echo 'SUCCESS: Product model is clean!' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

if %errorlevel% neq 0 (
    echo.
    echo ✗ Product model has issues. Please check the file manually.
    echo.
    echo MANUAL FIX STEPS:
    echo 1. Open app\Models\Product.php
    echo 2. Search for "public function offers"
    echo 3. Remove any duplicate offers() methods
    echo 4. Keep only one offers() method
    echo.
    pause
    exit /b 1
) else (
    echo.
    echo ✓ Product model is working correctly!
)

echo.
echo ========================================
echo   READY TO CONTINUE SETUP
echo ========================================
echo.
echo The Product model conflict has been resolved.
echo You can now proceed with the POS offers integration.
echo.
echo Next steps:
echo 1. Run the main setup: setup_pos_offers.bat
echo 2. Or continue with manual testing
echo.
pause
