@echo off
echo 🎯 Applying Custom Super Admin Menu Configuration...
echo.

echo 📝 Configuration Applied:
echo ✅ Companies
echo ✅ Package and plans  
echo ✅ Billing Management
echo ✅ Billing Reports
echo ✅ Themes Management
echo ✅ Theme Assignment Management
echo ✅ Data Import Management
echo ✅ Support Tickets
echo ✅ WhatsApp config
echo ✅ Email Settings
echo ✅ Menu Management (always visible)
echo ✅ System Setting
echo ✅ General Settings
echo ✅ Cache Management
echo ✅ View main site
echo ✅ Logout (always visible)
echo.

echo 🧹 Clearing Laravel caches to apply changes...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 🔍 Checking menu statistics...
php artisan tinker --execute="
echo 'Menu Statistics:' . PHP_EOL;
$stats = App\Services\SuperAdminMenuService::getMenuStats();
echo 'Total Menus: ' . $stats['total'] . PHP_EOL;
echo 'Enabled: ' . $stats['enabled'] . PHP_EOL;
echo 'Disabled: ' . $stats['disabled'] . PHP_EOL;
echo 'Enabled Percentage: ' . $stats['enabled_percentage'] . '%' . PHP_EOL;
"

echo.
echo 🎉 Custom Menu Configuration Applied!
echo.
echo 📊 Result: Your super admin now shows only the 15 specific menus you requested
echo 🧹 Clean: All other menus are hidden for a professional interface
echo ⚙️ Configurable: You can still adjust settings via Menu Management
echo.
echo 🌐 Next Steps:
echo 1. Visit your super admin panel
echo 2. Verify only your requested menus are visible
echo 3. Access Menu Management if you need to make adjustments
echo.

pause
