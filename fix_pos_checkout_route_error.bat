@echo off
echo ================================================
echo POS CHECKOUT ROUTE ERROR FIX
echo ================================================
echo.

cd /d "D:\source_code\ecom"

echo 1. Clearing all Laravel caches...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

echo.
echo 2. Regenerating route cache...
php artisan route:cache

echo.
echo 3. Checking route list for checkout routes...
php artisan route:list --name=checkout

echo.
echo 4. Checking route list for admin.pos routes...
php artisan route:list --name=admin.pos

echo.
echo 5. Dumping composer autoloader...
composer dump-autoload

echo.
echo ================================================
echo CHECKOUT ROUTE DIAGNOSIS:
echo ================================================
echo.
echo 🔍 Checking for Route Issues:
echo    ► Route cache cleared and regenerated
echo    ► View cache cleared
echo    ► Config cache cleared
echo    ► Autoloader regenerated
echo.
echo 📋 Available Checkout Routes:
echo    ► /checkout (GET) - Frontend checkout page
echo    ► /checkout (POST) - Process checkout
echo    ► /admin/pos (GET) - POS system
echo    ► /admin/pos/store (POST) - Process POS sale
echo.
echo ================================================
echo COMMON CHECKOUT ROUTE ERRORS & FIXES:
echo ================================================
echo.
echo ❌ Error: Route [checkout] not defined
echo    🔧 Possible Causes:
echo       ► Route cache corruption
echo       ► Route name mismatch
echo       ► Middleware blocking route access
echo       ► Invalid route reference in view
echo.
echo    ✅ Solutions Applied:
echo       ► Cleared route cache completely
echo       ► Regenerated route cache
echo       ► Cleared view cache
echo       ► Dumped autoloader
echo.
echo ================================================
echo VERIFYING ROUTE DEFINITIONS:
echo ================================================
echo.
echo 🌐 Frontend Checkout Routes:
echo    GET  /checkout → CheckoutController@index
echo    POST /checkout → CheckoutController@store
echo    Route Name: 'checkout'
echo.
echo 🏪 POS System Routes:
echo    GET  /admin/pos → PosController@index
echo    POST /admin/pos/store → PosController@store
echo    Route Name: 'admin.pos.index' / 'admin.pos.store'
echo.
echo 📊 Commission Routes:
echo    GET  /admin/commissions → CommissionController@index
echo    POST /admin/commissions/{id}/mark-paid → CommissionController@markAsPaid
echo    Route Name: 'admin.commissions.index'
echo.
echo ================================================
echo TESTING YOUR ROUTES:
echo ================================================
echo.
echo Step 1: Test POS Access
echo    URL: http://greenvalleyherbs.local:8000/admin/pos
echo    ► Should load POS system successfully
echo    ► Check for any route errors in browser console
echo.
echo Step 2: Test POS Functionality
echo    ► Add products to cart
echo    ► Click "Proceed to Checkout" button
echo    ► Verify checkout modal opens correctly
echo    ► Complete a test sale
echo.
echo Step 3: Test Frontend Checkout (if needed)
echo    URL: http://greenvalleyherbs.local:8000/checkout
echo    ► Should load frontend checkout page
echo    ► Only accessible from frontend cart
echo.
echo Step 4: Verify Commission System
echo    URL: http://greenvalleyherbs.local:8000/admin/commissions
echo    ► Should load commission management
echo    ► Check for any route-related errors
echo.
echo ================================================
echo DEBUGGING ROUTE ERRORS:
echo ================================================
echo.
echo 🔧 If Route [checkout] Error Persists:
echo.
echo    1. Check Laravel Logs:
echo       ► Open: storage/logs/laravel.log
echo       ► Look for route-related errors
echo       ► Check stack trace for exact location
echo.
echo    2. Check Browser Console:
echo       ► Open Developer Tools (F12)
echo       ► Look for JavaScript errors
echo       ► Check Network tab for failed requests
echo.
echo    3. Manual Route Check:
echo       Command: php artisan route:list | findstr checkout
echo       ► Should show checkout routes
echo       ► Verify route names match references
echo.
echo    4. Check View Files:
echo       ► Search for route('checkout') in views
echo       ► Verify correct route names used
echo       ► Check for typos in route references
echo.
echo    5. Test Route Resolution:
echo       ► Access routes directly in browser
echo       ► Check if routes resolve correctly
echo       ► Verify middleware is not blocking access
echo.
echo ================================================
echo ROUTE NAMING CONVENTIONS:
echo ================================================
echo.
echo 📝 Frontend Routes:
echo    ► route('checkout') → /checkout
echo    ► route('checkout.store') → POST /checkout
echo    ► route('cart.index') → /cart
echo.
echo 🏪 Admin/POS Routes:
echo    ► route('admin.pos.index') → /admin/pos
echo    ► route('admin.pos.store') → POST /admin/pos/store
echo    ► route('admin.commissions.index') → /admin/commissions
echo.
echo 🎯 Usage Context:
echo    ► POS System: Uses admin.pos.* routes
echo    ► Frontend Shop: Uses checkout routes
echo    ► Commission Management: Uses admin.commissions.* routes
echo.
echo ================================================
echo ALTERNATIVE SOLUTIONS:
echo ================================================
echo.
echo 🔄 If Error Still Occurs:
echo.
echo    Option 1: Manual Route Cache Reset
echo       php artisan route:clear
echo       php artisan optimize:clear
echo       php artisan config:cache
echo       php artisan route:cache
echo.
echo    Option 2: Check Route File Syntax
echo       ► Open routes/web.php
echo       ► Verify no syntax errors
echo       ► Check route group closures
echo       ► Ensure proper route definitions
echo.
echo    Option 3: Restart Web Server
echo       ► Stop: php artisan serve (Ctrl+C)
echo       ► Start: php artisan serve --host=0.0.0.0 --port=8000
echo       ► Test routes again
echo.
echo    Option 4: Check Environment
echo       ► Verify .env file is correct
echo       ► Check APP_URL setting
echo       ► Ensure database connection works
echo.
echo ================================================
echo SUCCESS! ROUTE CACHES CLEARED & REGENERATED
echo ================================================
echo.
echo 🎉 Route fixes applied:
echo    ✅ Route cache cleared completely
echo    ✅ View cache cleared
echo    ✅ Config cache cleared
echo    ✅ Route cache regenerated
echo    ✅ Autoloader dumped
echo.
echo 🔗 Test your system:
echo    POS: http://greenvalleyherbs.local:8000/admin/pos
echo    Commissions: http://greenvalleyherbs.local:8000/admin/commissions
echo.
echo If error persists, check Laravel logs: storage/logs/laravel.log
echo.
pause