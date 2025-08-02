@echo off
echo ================================================================
echo    Updating Location Icon System
echo ================================================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear

echo [2/5] Clearing configuration cache...
php artisan config:clear

echo [3/5] Clearing view cache...
php artisan view:clear

echo [4/5] Clearing route cache...
php artisan route:clear

echo [5/5] Optimizing application...
php artisan optimize

echo.
echo ================================================================
echo    Location Icon System Updated Successfully!
echo ================================================================
echo.
echo New features added:
echo ✓ IconClass helper with comprehensive icon sets
echo ✓ Location icon selector component
echo ✓ Enhanced social media form with location icons
echo ✓ CSS styling for consistent icon display
echo ✓ Usage examples and documentation
echo.
echo Files created/updated:
echo ✓ app\Helpers\IconClass.php
echo ✓ app\Providers\IconServiceProvider.php
echo ✓ bootstrap\providers.php
echo ✓ resources\views\components\location-icon-selector.blade.php
echo ✓ resources\views\admin\social-media\create.blade.php
echo ✓ public\css\icons.css
echo ✓ public\location-icon-examples.html
echo.
echo Usage:
echo 1. Include icons.css in your layout
echo 2. Use @include('components.location-icon-selector') in forms
echo 3. Use \App\Helpers\IconClass methods in controllers
echo 4. Visit /location-icon-examples.html for usage examples
echo.
pause
