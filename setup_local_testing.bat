@echo off
echo Setting up Local Testing Environment for RRK Crackers Domain Configuration
echo =======================================================================

echo.
echo Step 1: Backing up current .env file
copy .env .env.backup
echo Backup created: .env.backup

echo.
echo Step 2: Copying production environment settings
copy .env.production .env.local
echo Local testing environment created: .env.local

echo.
echo Step 3: Updating .env for local testing
(
echo APP_NAME="RRK Crackers - Local"
echo APP_ENV=local
echo APP_KEY=base64:hO2HsAxJBZPz2IFUxFTUdrY6dlKtA9GB7sEdyVip580=
echo APP_DEBUG=true
echo APP_URL=http://rrkcrackers.com:8000
echo APP_MAIN_DOMAIN=rrkcrackers.com:8000
echo APP_MAIN_URL=http://rrkcrackers.com:8000
echo APP_FRONTEND_URL=http://rrkcrackers.com:8000
echo APP_ADMIN_URL=http://rrkcrackers.com:8000
echo APP_BASE_DOMAIN=rrkcrackers.com
echo.
echo # Super Admin Configuration
echo SUPER_ADMIN_DOMAIN=superadmin.rrkcrackers.com:8000
echo SUPER_ADMIN_URL=http://superadmin.rrkcrackers.com:8000
echo.
echo APP_LOCALE=en
echo APP_FALLBACK_LOCALE=en
echo APP_FAKER_LOCALE=en_US
echo.
echo APP_MAINTENANCE_DRIVER=file
echo YOUR_GOOGLE_MAPS_API_KEY=AIzaSyD8a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p
echo PHP_CLI_SERVER_WORKERS=4
echo.
echo BCRYPT_ROUNDS=12
echo.
echo LOG_CHANNEL=stack
echo LOG_STACK=single
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=debug
echo.
echo DB_CONNECTION=mysql
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=ecom_saas
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo SESSION_DRIVER=database
echo SESSION_LIFETIME=480
echo SESSION_ENCRYPT=false
echo SESSION_PATH=/
echo SESSION_DOMAIN=.rrkcrackers.com
echo SESSION_SECURE_COOKIE=false
echo SESSION_HTTP_ONLY=true
echo SESSION_SAME_SITE=lax
echo.
echo BROADCAST_CONNECTION=log
echo FILESYSTEM_DISK=public
echo QUEUE_CONNECTION=database
echo.
echo CACHE_STORE=file
echo.
echo MEMCACHED_HOST=127.0.0.1
echo.
echo REDIS_CLIENT=phpredis
echo REDIS_HOST=127.0.0.1
echo REDIS_PASSWORD=null
echo REDIS_PORT=6379
echo.
echo MAIL_MAILER=smtp
echo MAIL_SCHEME=null
echo MAIL_HOST=smtp.gmail.com
echo MAIL_PORT=587
echo MAIL_USERNAME=anandakarthick.s1994@gmail.com
echo MAIL_PASSWORD=vuknlubogprngijn
echo MAIL_FROM_ADDRESS=Support@rrkcrackers.com
echo MAIL_FROM_NAME="RRK Crackers"
echo.
echo # S3 Configuration
echo AWS_ACCESS_KEY_ID=AKIAYFOSJCPTJEPVHV7B
echo AWS_SECRET_ACCESS_KEY=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
echo AWS_DEFAULT_REGION=ap-south-1
echo AWS_BUCKET=kasoftware
echo AWS_USE_PATH_STYLE_ENDPOINT=false
echo AWS_URL=https://kasoftware.s3.ap-south-1.amazonaws.com
echo.
echo VITE_APP_NAME="${APP_NAME}"
echo BROADCAST_DRIVER=log
echo PUSHER_APP_ID=your-app-id
echo PUSHER_APP_KEY=your-app-key
echo PUSHER_APP_SECRET=your-app-secret
echo PUSHER_APP_CLUSTER=your-cluster
echo APP_TIMEZONE=UTC
echo MAIL_ENCRYPTION=tls
echo.
echo # Storage Configuration
echo STORAGE_TYPE=local
) > .env

echo.
echo Step 4: Clearing application caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo.
echo Step 5: Running database update
mysql -u root -p ecom_saas < update_company_domain.sql

echo.
echo Step 6: Optimizing application
php artisan config:cache
php artisan route:cache

echo.
echo =======================================================================
echo LOCAL TESTING SETUP COMPLETE
echo =======================================================================
echo.
echo To test locally, add these entries to your Windows hosts file:
echo C:\Windows\System32\drivers\etc\hosts
echo.
echo 127.0.0.1 rrkcrackers.com
echo 127.0.0.1 www.rrkcrackers.com
echo 127.0.0.1 superadmin.rrkcrackers.com
echo.
echo Then start your development server:
echo php artisan serve --host=0.0.0.0 --port=8000
echo.
echo Test URLs:
echo - Main site: http://rrkcrackers.com:8000
echo - Super Admin: http://superadmin.rrkcrackers.com:8000/super-admin/login
echo.
echo =======================================================================

pause
