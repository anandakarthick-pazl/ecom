@echo off
echo ================================================================
echo    Fixing POS Category Dropdown Issue
echo ================================================================
echo.

echo [1/5] Clearing application cache...
php artisan cache:clear

echo [2/5] Clearing view cache...
php artisan view:clear

echo [3/5] Clearing route cache...
php artisan route:clear

echo [4/5] Clearing config cache...
php artisan config:clear

echo [5/5] Optimizing application...
php artisan optimize

echo.
echo ================================================================
echo    POS Category Dropdown Fixed Successfully!
echo ================================================================
echo.
echo What was fixed:
echo ✓ Updated POS controller to fetch categories from Category model
echo ✓ Fixed category filter to use category ID instead of name
echo ✓ Updated category dropdown to show "ID - Name" format
echo ✓ Categories now sorted by sort_order column then by name
echo ✓ Fixed category filter logic to work with category IDs
echo.
echo Changes made:
echo ✓ PosController::index() - Fixed category fetching
echo ✓ pos/index.blade.php - Updated category dropdown display
echo ✓ Category filter now uses proper ID-based filtering
echo.
echo Expected result:
echo → Categories show as "4 - One Sound Crackers" format
echo → Categories sorted by sort_order column
echo → Filter works correctly with category selection
echo → No more JSON objects in dropdown
echo.
echo Test the fix:
echo → Go to: http://greenvalleyherbs.local:8000/admin/pos
echo → Check category dropdown shows proper format
echo → Test filtering by selecting a category
echo.
echo Debug URL:
echo → http://greenvalleyherbs.local:8000/debug-pos-categories
echo → Shows detailed category information and testing
echo.
pause
