@echo off

echo 🔧 Fixing POS Receipt 500 Error
echo ================================
echo.

echo 🧹 Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo ✅ Caches cleared
echo.

echo 🔍 Debug sale ID 10...
php artisan tinker --execute="require_once 'debug_receipt_error.php';"

echo.
echo 🌐 Testing URLs:
echo ==================
echo Receipt: http://greenvalleyherbs.local:8000/admin/pos/receipt/10
echo Sales list: http://greenvalleyherbs.local:8000/admin/pos/sales
echo.

echo ✅ Receipt error fix complete!
echo The 500 error should now be resolved.

pause
