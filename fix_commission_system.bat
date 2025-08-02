@echo off
echo ========================================
echo COMMISSION SYSTEM FIX & TEST SCRIPT
echo ========================================
echo.

echo 1. Clearing Laravel caches...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

echo.
echo 2. Checking database migrations...
php artisan migrate:status

echo.
echo 3. Running any pending migrations...
php artisan migrate --force

echo.
echo 4. Checking if commission table exists...
php artisan tinker --execute="echo DB::hasTable('commissions') ? 'Commission table exists' : 'Commission table missing';"

echo.
echo ========================================
echo COMMISSION SYSTEM FIXES APPLIED:
echo ========================================
echo ✓ Fixed JavaScript boolean validation issue
echo ✓ Updated POS controller commission processing
echo ✓ Added commission display to POS order details
echo ✓ Added commission management buttons
echo ✓ Added PosSale -> Commission relationship
echo ✓ Fixed commission routes and controller methods
echo.
echo ========================================
echo TESTING INSTRUCTIONS:
echo ========================================
echo 1. Navigate to: http://greenvalleyherbs.local:8000/admin/pos
echo 2. Create a POS sale with commission enabled
echo 3. Check the sale details page for commission info
echo 4. Test commission status updates
echo.
echo ========================================
echo TROUBLESHOOTING:
echo ========================================
echo - If commission not showing: Check database has commission record
echo - If validation fails: Check browser console for JS errors
echo - If routes not working: Check Laravel logs in storage/logs/
echo.
pause
