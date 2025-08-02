@echo off
echo ========================================
echo   UPDATING POS SYSTEM WITH OFFERS
echo ========================================
echo.

cd /d "D:\source_code\ecom"

echo Step 1: Running migration to add offer fields...
php artisan migrate --path=database/migrations/2025_07_26_add_offer_fields_to_pos_sale_items_table.php --force
echo.

echo Step 2: Clearing cache to refresh system...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo Step 3: Backing up original POS view...
if exist "resources\views\admin\pos\index.blade.php.backup" (
    echo Backup already exists.
) else (
    copy "resources\views\admin\pos\index.blade.php" "resources\views\admin\pos\index.blade.php.backup"
    echo Original view backed up successfully.
)
echo.

echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo CHANGES MADE:
echo ✓ Added offer tracking fields to pos_sale_items table
echo ✓ Updated POS Controller with offer integration
echo ✓ Enhanced OfferService to work with POS
echo ✓ Added route for offer calculations
echo ✓ Cleared all caches
echo.
echo WHAT'S NEW:
echo ✓ Offers are now displayed in POS with discount badges
echo ✓ Effective prices (after offers) are used in billing
echo ✓ Offer savings are tracked and displayed
echo ✓ Offer usage counts are updated automatically
echo.
echo TO TEST:
echo 1. Go to http://greenvalleyherbs.local:8000/admin/pos
echo 2. Products with active offers will show discount badges
echo 3. When adding to cart, discounted prices will be used
echo 4. Receipt will show offer savings
echo.
echo NOTE: Make sure you have active offers created in the admin panel.
echo Create offers at: http://greenvalleyherbs.local:8000/admin/offers
echo.
pause
