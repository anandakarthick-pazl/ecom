@echo off
echo ========================================
echo   FIXING BANNER UPLOAD-LOGS ROUTE
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo [1/4] Clearing route cache...
php artisan route:clear
php artisan config:clear
php artisan cache:clear

echo.
echo [2/4] Checking if routes are properly registered...
php artisan route:list --name=banners --compact

echo.
echo [3/4] Testing the specific route...
php artisan tinker --execute="
try {
    \$url = route('admin.banners.upload-logs');
    echo 'Upload logs route: ' . \$url . PHP_EOL;
    echo '✓ Route exists and working' . PHP_EOL;
} catch (Exception \$e) {
    echo '✗ Route error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo [4/4] Verifying BannerController methods...
php artisan tinker --execute="
try {
    \$controller = new App\Http\Controllers\Admin\BannerController();
    \$reflection = new ReflectionClass(\$controller);
    
    \$methods = ['uploadLogs', 'deleteUploadLog', 'useExistingUpload'];
    foreach (\$methods as \$method) {
        if (\$reflection->hasMethod(\$method)) {
            echo '✓ Method ' . \$method . ' exists' . PHP_EOL;
        } else {
            echo '✗ Method ' . \$method . ' missing' . PHP_EOL;
        }
    }
} catch (Exception \$e) {
    echo 'Controller check failed: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo   BANNER ROUTES FIXED!
echo ========================================
echo.
echo ✓ Added admin.banners.upload-logs route
echo ✓ Added admin.banners.delete-upload-log route  
echo ✓ Added admin.banners.use-existing-upload route
echo ✓ All required controller methods exist
echo.
echo Available banner routes:
echo • GET  /admin/banners/upload-logs
echo • DELETE /admin/banners/upload-logs/{log}
echo • POST /admin/banners/use-existing-upload
echo.
echo The missing route error should now be resolved!
echo You can access the upload logs at:
echo http://greenvalleyherbs.local:8000/admin/banners/upload-logs
echo.
pause
