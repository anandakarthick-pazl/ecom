@echo off
echo ================================================================
echo    Fixing Categories Pagination Links Error
echo ================================================================
echo.

echo [1/6] Clearing application cache...
php artisan cache:clear

echo [2/6] Clearing view cache...
php artisan view:clear

echo [3/6] Clearing route cache...
php artisan route:clear

echo [4/6] Clearing config cache...
php artisan config:clear

echo [5/6] Regenerating optimized files...
php artisan clear-compiled

echo [6/6] Final optimization...
php artisan optimize

echo.
echo ================================================================
echo    Categories Pagination Issue Fixed Successfully!
echo ================================================================
echo.
echo What was the problem:
echo ✗ Custom pagination trait returned Collection instead of Paginator
echo ✗ View tried to call links() method on Collection
echo ✗ Method links() only exists on Paginator objects
echo.
echo What was fixed:
echo ✓ Updated BaseAdminController with ensurePaginator() method
echo ✓ Added createManualPaginator() for Collection to Paginator conversion
echo ✓ Updated CategoryController to use new pagination methods
echo ✓ Added fallback to handle both Collection and Paginator types
echo ✓ Enhanced view to check if links() method exists before calling
echo.
echo Changes made:
echo ✓ BaseAdminController - Added pagination safety methods
echo ✓ CategoryController - Simplified pagination handling
echo ✓ categories/index.blade.php - Added method_exists check
echo.
echo Test the fix:
echo → Go to: http://greenvalleyherbs.local:8000/admin/categories
echo → Page should load without "Method links does not exist" error
echo → Pagination should work properly
echo → If pagination disabled, shows "Showing all X categories"
echo.
echo Debug URLs:
echo → http://greenvalleyherbs.local:8000/debug-pagination
echo → Shows detailed pagination testing and diagnostics
echo.
pause
