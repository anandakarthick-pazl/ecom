@echo off
echo ================================================================
echo    Fixing Social Media Edit Route Issue
echo ================================================================
echo.

echo [1/6] Clearing application cache...
php artisan cache:clear

echo [2/6] Clearing configuration cache...
php artisan config:clear

echo [3/6] Clearing view cache...
php artisan view:clear

echo [4/6] Clearing route cache...
php artisan route:clear

echo [5/6] Clearing compiled files...
php artisan clear-compiled

echo [6/6] Optimizing application...
php artisan optimize

echo.
echo ================================================================
echo    Social Media Route Issue Fixed!
echo ================================================================
echo.
echo What was fixed:
echo ✓ Updated route parameter from {socialMediaLink} to {social_medium}
echo ✓ Updated controller methods to use correct parameter name
echo ✓ Updated edit.blade.php to use $social_medium variable
echo ✓ Added social_media_direct.php routes to web.php
echo ✓ Applied location icon enhancements
echo.
echo The edit route should now work properly:
echo → http://greenvalleyherbs.local:8000/admin/social-media/4/edit
echo.
echo Additional features available:
echo ✓ Location icons in social media form
echo ✓ Enhanced icon selector with categories
echo ✓ CSS styling for consistent icons
echo ✓ IconClass helper for PHP usage
echo.
pause
