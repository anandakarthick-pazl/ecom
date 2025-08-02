@echo off
echo Running migration to add offer fields to POS sale items...
cd /d "D:\source_code\ecom"
php artisan migrate --path=database/migrations/2025_07_26_add_offer_fields_to_pos_sale_items_table.php --force
echo Migration completed!
pause
