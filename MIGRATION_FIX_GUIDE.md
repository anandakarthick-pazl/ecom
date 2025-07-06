# Migration Error Fix Guide

## ❌ Error: getDoctrineSchemaManager does not exist

This error occurs due to Laravel version compatibility. Here's how to fix it:

### Quick Fix (Recommended)

Run this single command:

```bash
php artisan tinker --execute="require_once 'fix_pos_migration.php';"
```

### Manual Fix

If the above doesn't work, run these commands:

```bash
# 1. Reset the failed migration
php artisan migrate:rollback --step=1

# 2. Run the fixed migration
php artisan migrate

# 3. Clear caches
php artisan cache:clear
php artisan config:clear
```

### Alternative Method

If migrations are still failing, you can apply the database changes manually:

1. **Run the fix script:**
   ```bash
   php artisan tinker
   ```
   
2. **In the tinker console, paste:**
   ```php
   require_once 'fix_pos_migration.php';
   exit();
   ```

### What the Fix Does

The fix script:
- ✅ Adds missing columns to `pos_sale_items` table
- ✅ Adds missing columns to `pos_sales` table  
- ✅ Updates payment method enum values
- ✅ Adds performance indexes safely
- ✅ Marks migrations as completed

### Verify the Fix

After running the fix, verify it worked:

```bash
php artisan tinker --execute="
echo 'Checking pos_sale_items columns:' . PHP_EOL;
echo 'tax_percentage: ' . (Schema::hasColumn('pos_sale_items', 'tax_percentage') ? 'YES' : 'NO') . PHP_EOL;
echo 'tax_amount: ' . (Schema::hasColumn('pos_sale_items', 'tax_amount') ? 'YES' : 'NO') . PHP_EOL;
echo 'discount_percentage: ' . (Schema::hasColumn('pos_sale_items', 'discount_percentage') ? 'YES' : 'NO') . PHP_EOL;
echo 'company_id: ' . (Schema::hasColumn('pos_sale_items', 'company_id') ? 'YES' : 'NO') . PHP_EOL;
echo PHP_EOL . 'Checking pos_sales columns:' . PHP_EOL;
echo 'company_id: ' . (Schema::hasColumn('pos_sales', 'company_id') ? 'YES' : 'NO') . PHP_EOL;
"
```

All should show "YES" if the fix worked correctly.

### Next Steps

Once the fix is complete:

1. **Test the POS interface:** Go to `/admin/pos`
2. **Try adding items to cart**
3. **Test discount functionality**
4. **Verify tax calculations**

### If You Still Have Issues

If you continue to have problems:

1. Check your Laravel version: `php artisan --version`
2. Ensure database connection is working
3. Check the Laravel logs for specific errors
4. Consider running `composer update` if using older packages

The enhanced POS system should work perfectly after applying this fix!
