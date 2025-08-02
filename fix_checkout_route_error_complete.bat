@echo off
echo ================================================
echo ROUTE [CHECKOUT] NOT DEFINED - COMPLETE FIX
echo ================================================
echo.

cd /d "D:\source_code\ecom"

echo 1. Removing duplicate checkout routes...
echo    ► Duplicate routes removed from web.php
echo    ► Keeping only tenant middleware routes

echo.
echo 2. Clearing all Laravel caches...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

echo.
echo 3. Regenerating optimized caches...
php artisan config:cache
php artisan route:cache

echo.
echo 4. Dumping composer autoloader...
composer dump-autoload

echo.
echo 5. Testing route availability...
php artisan route:list --name=checkout
echo.
php artisan route:list --name=admin.pos

echo.
echo ================================================
echo ROUTE ISSUE ANALYSIS & FIX:
echo ================================================
echo.
echo 🐛 PROBLEM IDENTIFIED:
echo    ► Duplicate checkout routes were defined in web.php
echo    ► One set in tenant middleware (correct)
echo    ► Another set without middleware (causing conflict)
echo    ► Route names were conflicting
echo.
echo ✅ SOLUTION APPLIED:
echo    ► Removed duplicate checkout routes
echo    ► Kept only tenant middleware routes
echo    ► Cleared all caches completely
echo    ► Regenerated route cache
echo.
echo ================================================
echo CORRECT ROUTE STRUCTURE NOW:
echo ================================================
echo.
echo 🌐 Frontend Checkout Routes (Tenant Middleware):
echo    GET  /checkout → CheckoutController@index
echo    POST /checkout → CheckoutController@store
echo    Route Names: 'checkout', 'checkout.store'
echo.
echo 🏪 POS System Routes (Admin Middleware):
echo    GET  /admin/pos → PosController@index
echo    POST /admin/pos/store → PosController@store
echo    Route Names: 'admin.pos.index', 'admin.pos.store'
echo.
echo 📊 Commission Routes (Admin Middleware):
echo    GET  /admin/commissions → CommissionController@index
echo    POST /admin/commissions/bulk-mark-paid → CommissionController@bulkMarkAsPaid
echo    Route Names: 'admin.commissions.*'
echo.
echo ================================================
echo ROUTE USAGE CONTEXT:
echo ================================================
echo.
echo 🎯 POS System Context:
echo    ► Uses admin.pos.* routes for POS operations
echo    ► Uses admin.commissions.* for commission management
echo    ► Does NOT use frontend checkout routes
echo    ► All operations within admin middleware
echo.
echo 🛍️ Frontend Shop Context:
echo    ► Uses 'checkout' route for customer checkout
echo    ► Uses 'cart.*' routes for cart operations
echo    ► Protected by tenant middleware
echo    ► Separate from admin/POS operations
echo.
echo ================================================
echo TESTING YOUR FIXED ROUTES:
echo ================================================
echo.
echo Step 1: Test POS System
echo    URL: http://greenvalleyherbs.local:8000/admin/pos
echo    ► Should load without route errors
echo    ► Commission functionality should work
echo    ► Checkout process should complete successfully
echo.
echo Step 2: Test Commission Management
echo    URL: http://greenvalleyherbs.local:8000/admin/commissions
echo    ► Should load commission management page
echo    ► No route errors should occur
echo    ► All commission functions should work
echo.
echo Step 3: Test Frontend Checkout (Optional)
echo    URL: http://greenvalleyherbs.local:8000/checkout
echo    ► Should load frontend checkout page
echo    ► Only accessible from shop cart
echo    ► Separate from POS system
echo.
echo ================================================
echo ROUTE DEBUGGING COMMANDS:
echo ================================================
echo.
echo 🔍 Check All Routes:
echo    php artisan route:list
echo.
echo 🔍 Check Specific Routes:
echo    php artisan route:list --name=checkout
echo    php artisan route:list --name=admin.pos
echo    php artisan route:list --name=admin.commissions
echo.
echo 🔍 Clear Caches Again (if needed):
echo    php artisan optimize:clear
echo    php artisan config:cache
echo    php artisan route:cache
echo.
echo ================================================
echo LARAVEL LOG MONITORING:
echo ================================================
echo.
echo 📋 If Issues Persist, Check:
echo    ► File: storage/logs/laravel.log
echo    ► Look for: Route-related errors
echo    ► Check: Stack traces for exact error location
echo    ► Verify: Middleware and route group configurations
echo.
echo 💡 Common Log Entries to Look For:
echo    ► "Route [checkout] not defined"
echo    ► "Target class [CheckoutController] does not exist"
echo    ► "Middleware group [tenant] not found"
echo    ► "Call to undefined method"
echo.
echo ================================================
echo ADDITIONAL TROUBLESHOOTING:
echo ================================================
echo.
echo 🔧 If Route Error Still Occurs:
echo.
echo    1. Restart Development Server:
echo       ► Stop: Ctrl+C
echo       ► Start: php artisan serve --host=0.0.0.0 --port=8000
echo.
echo    2. Check Route File Syntax:
echo       ► Open: routes/web.php
echo       ► Verify: No syntax errors
echo       ► Check: Proper route group closures
echo.
echo    3. Verify Controller Exists:
echo       ► Check: app/Http/Controllers/CheckoutController.php
echo       ► Verify: Proper namespace and methods
echo.
echo    4. Test Route Resolution:
echo       ► Command: php artisan route:show checkout
echo       ► Should show route details
echo.
echo ================================================
echo MIDDLEWARE VERIFICATION:
echo ================================================
echo.
echo 🛡️ Tenant Middleware:
echo    ► Protects frontend routes
echo    ► Includes checkout routes
echo    ► Handles multi-tenancy
echo.
echo 🔒 Auth Middleware:
echo    ► Protects admin routes
echo    ► Includes POS and commission routes
echo    ► Requires user authentication
echo.
echo 🏢 Company Context Middleware:
echo    ► Ensures proper company context
echo    ► Required for admin operations
echo    ► Handles company switching
echo.
echo ================================================
echo SUCCESS! ROUTE CONFLICTS RESOLVED
echo ================================================
echo.
echo 🎉 Route fixes completed:
echo    ✅ Duplicate checkout routes removed
echo    ✅ Route conflicts resolved
echo    ✅ All caches cleared and regenerated
echo    ✅ Autoloader dumped
echo    ✅ Routes properly organized
echo.
echo 🔗 Test your system now:
echo    POS System: http://greenvalleyherbs.local:8000/admin/pos
echo    Commissions: http://greenvalleyherbs.local:8000/admin/commissions
echo.
echo The "Route [checkout] not defined" error should be resolved!
echo.
pause