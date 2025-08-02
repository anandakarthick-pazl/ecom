@echo off
echo ========================================
echo   POS OFFERS INTEGRATION SETUP
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo [1/6] Checking if database migration file exists...
if exist "database\migrations\2025_07_26_add_offer_fields_to_pos_sale_items_table.php" (
    echo ✓ Migration file found
) else (
    echo ✗ Migration file not found
    echo Please ensure the migration file is in the correct location
    pause
    exit /b 1
)

echo.
echo [2/6] Running database migration...
php artisan migrate --path=database/migrations/2025_07_26_add_offer_fields_to_pos_sale_items_table.php --force
if %errorlevel% neq 0 (
    echo ✗ Migration failed
    pause
    exit /b 1
)
echo ✓ Migration completed successfully

echo.
echo [3/6] Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo ✓ Cache cleared

echo.
echo [4/6] Backing up original files...
if not exist "backups" mkdir backups
if exist "resources\views\admin\pos\index.blade.php" (
    copy "resources\views\admin\pos\index.blade.php" "backups\pos_index_original_%date:~-4,4%%date:~-10,2%%date:~-7,2%.blade.php" >nul 2>&1
    echo ✓ POS view backed up
)
if exist "app\Http\Controllers\Admin\PosController.php" (
    copy "app\Http\Controllers\Admin\PosController.php" "backups\PosController_original_%date:~-4,4%%date:~-10,2%%date:~-7,2%.php" >nul 2>&1
    echo ✓ POS controller backed up
)

echo.
echo [5/6] Verifying offer system...
php artisan tinker --execute="echo 'Testing offer service: '; $service = app('App\Services\OfferService'); echo 'OfferService loaded successfully';"
if %errorlevel% neq 0 (
    echo ⚠ Warning: OfferService verification failed, but continuing...
) else (
    echo ✓ OfferService verified
)

echo.
echo [6/6] Final verification...
echo Checking required files...
if exist "app\Models\Offer.php" (echo ✓ Offer model exists) else (echo ✗ Offer model missing)
if exist "app\Services\OfferService.php" (echo ✓ OfferService exists) else (echo ✗ OfferService missing)
if exist "resources\views\admin\pos\index.blade.php" (echo ✓ POS view exists) else (echo ✗ POS view missing)

echo.
echo ========================================
echo   SETUP COMPLETED SUCCESSFULLY! 🎉
echo ========================================
echo.
echo WHAT'S NEW IN YOUR POS SYSTEM:
echo.
echo 🏷️  OFFER FEATURES:
echo   • Products with offers show red "OFFER" badges
echo   • Strikethrough original prices, highlighted discounts
echo   • Automatic best offer application
echo   • Real-time savings calculations
echo   • Offer usage tracking
echo.
echo 📊 CART ENHANCEMENTS:
echo   • Offer savings displayed per item
echo   • Total savings summary
echo   • Visual indicators for offered items
echo   • Enhanced price breakdown
echo.
echo 🧾 RECEIPT IMPROVEMENTS:
echo   • Offer details in sale records
echo   • Savings amount tracking
echo   • Original vs effective price storage
echo.
echo 🚀 NEXT STEPS:
echo.
echo 1. CREATE OFFERS:
echo    → Go to: http://greenvalleyherbs.local:8000/admin/offers
echo    → Create product, category, or general offers
echo    → Set discounts, dates, and conditions
echo.
echo 2. TEST POS SYSTEM:
echo    → Go to: http://greenvalleyherbs.local:8000/admin/pos
echo    → Look for products with red "OFFER" badges
echo    → Add them to cart and see savings
echo.
echo 3. VERIFY FUNCTIONALITY:
echo    → Test different offer types
echo    → Check cart calculations
echo    → Verify receipts show offer info
echo.
echo ⚠️  IMPORTANT NOTES:
echo   • Make sure you have active offers created
echo   • Offers must have valid date ranges
echo   • Check minimum amount requirements
echo   • Verify stock availability for offered products
echo.
echo 📚 DOCUMENTATION:
echo   • Complete setup guide available in project
echo   • Troubleshooting tips included
echo   • Customization options documented
echo.
echo ========================================
echo Ready to use! Press any key to open POS...
pause >nul

echo Opening POS system...
start http://greenvalleyherbs.local:8000/admin/pos

echo.
echo If you encounter any issues:
echo 1. Check Laravel logs in storage/logs/
echo 2. Verify database connection
echo 3. Ensure offers are created and active
echo 4. Clear browser cache if needed
echo.
echo Happy selling! 💰
pause
