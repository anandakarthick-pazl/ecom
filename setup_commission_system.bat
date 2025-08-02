@echo off
echo.
echo =====================================================
echo  COMMISSION SYSTEM SETUP
echo =====================================================
echo.

echo 1. Running database migrations...
php artisan migrate --path=database/migrations/2025_07_26_130000_add_commission_fields_to_orders_table.php

echo.
echo 2. Clearing application cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo 3. Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo =====================================================
echo  COMMISSION SYSTEM SETUP COMPLETE!
echo =====================================================
echo.
echo The following features have been added:
echo.
echo [POS System]
echo - Commission tracking already available in POS
echo - Reference name and percentage capture
echo - Commission records stored in separate table
echo.
echo [Online Orders]
echo + Commission section added to checkout form
echo + Commission fields added to orders table
echo + Commission records auto-created for online orders
echo + Commission information displayed in admin order view
echo.
echo [Database]
echo + Commission fields added to orders table
echo + Commission records link to both POS and online orders
echo.
echo NEXT STEPS:
echo 1. Test commission capture in online checkout
echo 2. Verify commission records are created
echo 3. Check admin order view shows commission info
echo 4. Test POS commission system (already working)
echo.
pause
