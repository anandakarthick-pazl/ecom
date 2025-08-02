@echo off
echo Production Deployment Script for RRK Crackers Domain Configuration
echo =====================================================================

echo.
echo Step 1: Backing up current .env file
copy .env .env.backup.%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
echo Backup created with timestamp

echo.
echo Step 2: Copying production environment settings
copy .env.production .env
echo Production environment activated

echo.
echo Step 3: Clearing all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan queue:clear

echo.
echo Step 4: Running database migrations and updates
php artisan migrate --force
mysql -u root -p ecom_saas < update_company_domain.sql

echo.
echo Step 5: Optimizing for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo.
echo Step 6: Setting proper permissions
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T

echo.
echo Step 7: Generating application key if needed
php artisan key:generate --force

echo.
echo =====================================================================
echo PRODUCTION DEPLOYMENT COMPLETE
echo =====================================================================
echo.
echo Make sure to:
echo 1. Configure your web server (Apache/Nginx) to point to the public directory
echo 2. Set up SSL certificates for:
echo    - rrkcrackers.com
echo    - www.rrkcrackers.com
echo    - superadmin.rrkcrackers.com
echo 3. Configure DNS records to point to your server IP
echo 4. Test the following URLs:
echo    - https://rrkcrackers.com (main site)
echo    - https://superadmin.rrkcrackers.com/super-admin/login (super admin)
echo.
echo =====================================================================

pause
