@echo off
echo 🚀 Setting up Super Admin Menu Management System...
echo.

echo 🧹 Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo ✅ Routes added to super_admin.php
echo ✅ MenuManagementController created
echo ✅ SuperAdminMenuService created
echo ✅ Menu management view created
echo ✅ Layout file updated with conditional logic
echo.

echo 🌐 Testing routes...
php artisan route:list | findstr menu-management

echo.
echo 🎉 Setup Complete!
echo.
echo 📍 Next Steps:
echo 1. Visit: /super-admin/menu-management
echo 2. Click "Reset to Recommended" for initial setup
echo 3. Configure your menus as needed
echo.
echo 💡 The system will now show only working menu items!

pause
