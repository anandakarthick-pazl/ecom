@echo off
echo ========================================
echo    FLASH OFFER SYSTEM SETUP
echo ========================================
echo.

echo Step 1: Running flash offer field migration...
php artisan migrate --path=database/migrations/2025_07_25_000001_add_flash_offer_fields_to_offers_table.php --force
IF %ERRORLEVEL% NEQ 0 (
    echo ERROR: Flash offer fields migration failed!
    pause
    exit /b 1
)

echo.
echo Step 2: Running offers type enum update migration...
php artisan migrate --path=database/migrations/2025_07_25_000002_update_offers_type_enum.php --force
IF %ERRORLEVEL% NEQ 0 (
    echo ERROR: Offers type enum migration failed!
    pause
    exit /b 1
)

echo.
echo Step 3: Running popup frequency migration...
php artisan migrate --path=database/migrations/2025_07_25_000003_add_popup_frequency_to_offers_table.php --force
IF %ERRORLEVEL% NEQ 0 (
    echo ERROR: Popup frequency migration failed!
    pause
    exit /b 1
)

echo.
echo Step 4: Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 5: Creating storage link (if needed)...
php artisan storage:link

echo.
echo ========================================
echo    FLASH OFFER SYSTEM SETUP COMPLETE!
echo ========================================
echo.
echo Your Flash Offer System is now ready to use!
echo.
echo Next Steps:
echo 1. Go to: http://greenvalleyherbs.local:8000/admin/offers
echo 2. Click "Create New Offer"
echo 3. Select "Flash Offer" as the offer type
echo 4. Configure your flash offer settings
echo 5. Set Popup Frequency to "Every Page Visit" for maximum visibility
echo 6. Upload a banner image (800x400px recommended)
echo 7. Set the popup and countdown options
echo 8. Test the popup on your shop page
echo.
echo Popup Frequency Options:
echo - Every Page Visit: Shows popup every time user visits any page
echo - Once Per Session: Shows once until browser is closed
echo - Once Per Day: Shows once per day
echo - Once Per Week: Shows once per week
echo.
echo Features Available:
echo - Banner Image Upload
echo - Customizable Title and Description
echo - Button Text and URL Configuration
echo - Popup Delay Settings (0-60 seconds)
echo - Popup Frequency Control (Always/Session/Day/Week)
echo - Countdown Timer with Custom Text
echo - Smart Display Logic
echo - Mobile Responsive Design
echo - Modern Animations and Effects
echo.
echo Visit your shop page to see the flash offer popup in action!
echo Shop URL: http://greenvalleyherbs.local:8000/shop
echo Debug URL: http://greenvalleyherbs.local:8000/shop?debug=flash
echo.
pause
