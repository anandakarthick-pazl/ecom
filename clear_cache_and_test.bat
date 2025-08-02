@echo off
echo Clearing Laravel caches and testing fix...
echo.

echo 1. Clearing route cache...
php artisan route:clear

echo 2. Clearing view cache...
php artisan view:clear

echo 3. Clearing config cache...
php artisan config:clear

echo 4. Clearing application cache...
php artisan cache:clear

echo 5. Regenerating application key if needed...
php artisan key:generate --show

echo.
echo Cache cleared successfully!
echo.
echo Now testing the application...
echo Navigate to: http://greenvalleyherbs.local:8000/shop
echo.
echo If you still see errors, check the Laravel logs at:
echo storage\logs\laravel.log
echo.
pause
