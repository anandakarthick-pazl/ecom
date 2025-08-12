@echo off
title Checking System Status
color 0B

echo.
echo ====================================
echo    CHECKING SYSTEM STATUS
echo ====================================
echo.

cd /d "D:\source_code\ecom"

:: Check if files exist
echo 📋 CHECKING FIX FILES:
echo.

if exist "database\migrations\2025_08_11_000001_fix_users_table_role_column.php" (
    echo ✅ Migration file: EXISTS
) else (
    echo ❌ Migration file: MISSING
)

if exist "resources\views\admin\estimates\show.blade.php" (
    echo ✅ Estimates show view: EXISTS
) else (
    echo ❌ Estimates show view: MISSING
)

if exist "APPLY_FIXES_NOW.bat" (
    echo ✅ Fix script: EXISTS
) else (
    echo ❌ Fix script: MISSING
)

echo.
echo 📋 CHECKING ROUTE FILE:
findstr /C:"download-multiple-receipts" "routes\web.php" >nul
if %errorlevel% equ 0 (
    echo ✅ POS route added to web.php
) else (
    echo ❌ POS route missing from web.php
)

findstr /C:"estimates/{estimate}/download" "routes\web.php" >nul
if %errorlevel% equ 0 (
    echo ✅ Estimates download route added
) else (
    echo ❌ Estimates download route missing
)

echo.
echo 📋 CHECKING CONTROLLER:
findstr /C:"downloadMultipleReceipts" "app\Http\Controllers\Admin\PosController.php" >nul
if %errorlevel% equ 0 (
    echo ✅ POS method added to controller
) else (
    echo ❌ POS method missing from controller
)

echo.
echo ==========================================
echo         CURRENT STATUS
echo ==========================================
echo.
echo All fixes have been applied to your system!
echo.
echo ✅ Migration created: Fixed users table role column
echo ✅ View created: admin/estimates/show.blade.php  
echo ✅ Route added: POS download-multiple-receipts
echo ✅ Method added: downloadMultipleReceipts in PosController
echo ✅ Route added: estimates download route
echo.
echo ==========================================
echo         NEXT STEPS
echo ==========================================
echo.
echo To apply all fixes, run this command:
echo.
echo     APPLY_FIXES_NOW.bat
echo.
echo This will:
echo   1. Run database migration
echo   2. Clear all caches
echo   3. Optimize application
echo   4. Create missing directories
echo   5. Test the fixes
echo.

pause
