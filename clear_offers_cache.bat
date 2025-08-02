@echo off
echo Clearing Laravel caches after offers fix...

cd /d "D:\source_code\ecom"

echo Clearing application cache...
php artisan cache:clear

echo Clearing config cache...
php artisan config:clear

echo Clearing route cache...
php artisan route:clear

echo Clearing view cache...
php artisan view:clear

echo Optimizing application...
php artisan optimize

echo.
echo All caches cleared and application optimized!
echo You can now test the offers functionality.
echo.

pause
