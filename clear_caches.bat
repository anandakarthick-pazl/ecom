@echo off
echo === CLEARING LARAVEL CACHES ===

echo 1. Clearing route cache...
php artisan route:clear

echo 2. Clearing config cache...
php artisan config:clear

echo 3. Clearing view cache...
php artisan view:clear

echo 4. Clearing application cache...
php artisan cache:clear

echo 5. Optimizing for development...
php artisan optimize:clear

echo ✅ All caches cleared!

echo === CHECKING ROUTES ===
php artisan route:list --name=social-media

pause
