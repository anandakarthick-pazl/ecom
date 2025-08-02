@echo off
REM Social Media Functionality Fix Script for Windows
REM Run this script to fix the social media issues

echo 🔧 SOCIAL MEDIA FUNCTIONALITY FIX
echo ==================================

echo Starting social media functionality fix...

REM Step 1: Clear all caches
echo.
echo 1. Clearing all caches...
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

REM Step 2: Run migrations
echo.
echo 2. Running database migrations...
php artisan migrate

REM Step 3: Verify routes are registered
echo.
echo 3. Checking social media routes...
php artisan route:list --name=social-media

REM Step 4: Run diagnostic
echo.
echo 4. Running diagnostic script...
if exist "social_media_diagnostic.php" (
    php social_media_diagnostic.php
) else (
    echo ⚠️  Diagnostic script not found. Skipping...
)

REM Step 5: Create sample social media data
echo.
echo 5. Creating sample social media data...
echo This will create test social media links for the first company found.

php artisan tinker --execute="try { $company = \App\Models\SuperAdmin\Company::first(); if ($company) { $link = \App\Models\SocialMediaLink::updateOrCreate([ 'company_id' => $company->id, 'name' => 'Facebook' ], [ 'icon_class' => 'fab fa-facebook-f', 'url' => 'https://facebook.com/yourpage', 'color' => '#1877f2', 'sort_order' => 1, 'is_active' => true ]); echo 'Sample Facebook link created for company: ' . $company->name . PHP_EOL; $link2 = \App\Models\SocialMediaLink::updateOrCreate([ 'company_id' => $company->id, 'name' => 'Instagram' ], [ 'icon_class' => 'fab fa-instagram', 'url' => 'https://instagram.com/yourusername', 'color' => '#e4405f', 'sort_order' => 2, 'is_active' => true ]); echo 'Sample Instagram link created for company: ' . $company->name . PHP_EOL; } else { echo 'No company found. Please create a company first.' . PHP_EOL; } } catch (Exception $e) { echo 'Error: ' . $e->getMessage() . PHP_EOL; }"

echo.
echo ✅ Fix script completed!
echo.
echo Next steps:
echo 1. Visit http://your-domain.local:8000/admin/social-media
echo 2. Try adding a social media link using the 'Quick Add' button
echo 3. Check browser console (F12) for any JavaScript errors
echo 4. If issues persist, check Laravel logs in storage/logs/
echo.
echo Common troubleshooting:
echo - Ensure you're logged in as an admin user
echo - Verify your company context is set correctly
echo - Check if FontAwesome is loaded for social media icons
echo - Ensure jQuery and Bootstrap are loaded properly

pause