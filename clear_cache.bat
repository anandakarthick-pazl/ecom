@echo off
echo Clearing Laravel cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
echo Cache cleared successfully!
pause
