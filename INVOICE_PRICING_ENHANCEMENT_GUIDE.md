# Invoice Pricing Enhancement - MRP and Offer Price Details

## Overview
This update enhances the invoice system to display both MRP (Maximum Retail Price) and offer pricing details, providing complete transparency to customers about discounts and savings.

## New Features Added

### 1. Enhanced Invoice Display
- **MRP Column**: Shows the original/maximum retail price of products
- **Offer Price Column**: Shows the discounted price when offers are applied
- **Discount Percentage**: Visual indication of discount percentage
- **Savings Summary**: Total amount saved through offers and discounts
- **Offer Labels**: Shows the name of applied offers/promotions

### 2. Database Schema Updates
New fields added to `order_items` table:
- `mrp_price` (decimal): Original product price (MRP)
- `discount_amount` (decimal): Discount amount applied per unit
- `discount_percentage` (decimal): Discount percentage applied
- `offer_id` (bigint): ID of the applied offer
- `offer_name` (string): Name of the applied offer

### 3. Updated Invoice Templates
All invoice formats now include pricing details:
- **Web Invoice View** (`invoice.blade.php`)
- **PDF Invoice** (`invoice-pdf.blade.php`) 
- **A4 Print Format** (`print-a4.blade.php`)
- **Thermal Print Format** (`print-thermal.blade.php`)

### 4. Enhanced Order Processing
- **Automatic Pricing Calculation**: New orders automatically populate MRP and offer details
- **Offer Integration**: Seamless integration with existing offer system
- **Historical Data Migration**: Existing orders can be updated with pricing details

## Files Modified

### Models
- `app/Models/OrderItem.php` - Added new fields and helper methods
- Enhanced with pricing calculation methods and relationships

### Services
- `app/Services/OrderItemPricingService.php` (NEW) - Handles pricing calculations
- Provides methods for creating order items with complete pricing data
- Includes migration support for existing data

### Controllers
- `app/Http/Controllers/CheckoutController.php` - Updated to use new pricing service
- Ensures new orders include complete pricing information

### Views
- `resources/views/admin/orders/invoice.blade.php` - Enhanced with MRP/offer columns
- `resources/views/admin/orders/invoice-pdf.blade.php` - PDF format with pricing details
- `resources/views/admin/orders/print-a4.blade.php` - A4 print format with savings summary
- `resources/views/admin/orders/print-thermal.blade.php` - Compact thermal format with offers

### Database
- `database/migrations/2025_07_27_120000_add_pricing_details_to_order_items.php` (NEW)
- Adds pricing fields to order_items table

### Commands
- `app/Console/Commands/UpdateOrderItemsPricing.php` (NEW)
- Command to migrate existing order items with pricing data

## Installation Instructions

### 1. Run the Main Update Script
```bash
# Execute the main update script
update_invoice_pricing_system.bat
```

This script will:
- Run database migrations
- Clear caches
- Update existing order items
- Optimize the application

### 2. Migrate Existing Data (Optional)
```bash
# For existing order items, run the migration script
migrate_existing_order_pricing.bat
```

### 3. Manual Commands (If needed)
```bash
# Run migration only
php artisan migrate

# Update existing order items only
php artisan orders:update-pricing

# Preview changes without applying them
php artisan orders:update-pricing --dry-run
```

## New Invoice Display Features

### Regular Orders
- Shows MRP vs Offer Price comparison
- Highlights savings with green text and percentages
- Displays offer names when applicable
- Provides total MRP and total savings summary

### POS Orders
- Compatible with existing POS pricing structure
- Maintains original_price and discount_amount fields
- Enhanced display for POS receipts

## Technical Details

### Pricing Calculation Logic
1. **MRP Determination**: Uses product's base price as MRP
2. **Offer Price**: Calculated from active offers or manual discounts
3. **Savings Calculation**: MRP - Offer Price = Savings
4. **Display Logic**: Shows strikethrough MRP when offers are applied

### Helper Methods (OrderItem Model)
- `hasDiscount()` - Check if item has discount applied
- `getMrpTotalAttribute()` - Calculate MRP × quantity
- `getSavingsAttribute()` - Calculate total savings for line item
- `getEffectiveDiscountPercentageAttribute()` - Get display discount percentage

### Service Methods
- `OrderItemPricingService::createOrderItemWithPricing()` - Create order item with pricing
- `OrderItemPricingService::calculateItemPricing()` - Calculate comprehensive pricing
- `OrderItemPricingService::updateExistingOrderItemsWithPricing()` - Migrate existing data

## Backward Compatibility

### Existing Orders
- All existing functionality remains intact
- Migration script populates new fields for historical orders
- No data loss or corruption

### API/Integrations
- All existing API endpoints continue to work
- New fields are optional and don't break existing integrations
- Enhanced data available for future API enhancements

## Benefits

### For Customers
- **Complete Transparency**: See original prices and exact savings
- **Offer Visibility**: Clear indication of applied promotions
- **Trust Building**: Transparent pricing builds customer confidence

### For Business
- **Marketing Tool**: Showcase savings and offer effectiveness
- **Analytics**: Better data for offer performance analysis
- **Professional Invoices**: Enhanced invoice presentation

### For Administrators
- **Complete Records**: Full pricing history for all orders
- **Offer Tracking**: Track which offers are applied to which orders
- **Reporting**: Enhanced data for sales and discount analysis

## Customization Options

### Display Settings
- Modify templates to adjust pricing display format
- Customize colors and styling for offer highlights
- Add/remove pricing columns as needed

### Calculation Logic
- Extend `OrderItemPricingService` for custom pricing rules
- Modify offer integration logic if needed
- Add additional pricing fields if required

## Troubleshooting

### Common Issues
1. **Migration Fails**: Check database permissions and field constraints
2. **Display Issues**: Clear view cache with `php artisan view:clear`
3. **Missing Data**: Run `php artisan orders:update-pricing` for existing items

### Debug Commands
```bash
# Check migration status
php artisan migrate:status

# View specific order item data
php artisan tinker
>>> App\Models\OrderItem::with('product')->first()

# Test pricing service
>>> App\Services\OrderItemPricingService::calculateItemPricing($product)
```

## Future Enhancements

### Planned Features
- Customer-facing order history with pricing details
- Enhanced offer analytics dashboard
- Bulk pricing update tools
- Advanced discount rules integration

### Extension Points
- Custom pricing calculation hooks
- Additional offer types support
- Integration with external pricing services
- Enhanced reporting capabilities

## Support and Maintenance

### Regular Tasks
- Monitor pricing data consistency
- Update offer integration as needed
- Review invoice templates for branding changes

### Performance Considerations
- Pricing calculations are cached where possible
- Database indexes on pricing fields for fast queries
- Optimized templates for quick rendering

---

**Last Updated**: July 27, 2025
**Version**: 1.0
**Compatibility**: Laravel 8+, PHP 8.0+
