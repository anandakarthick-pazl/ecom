# PDF Email Sending - Complete Setup Guide

## Overview
This guide will help you set up PDF email sending functionality for your Laravel e-commerce application. The system automatically generates and sends PDF invoices when orders are placed.

## Changes Made

### 1. Enhanced HandleOrderPlaced Listener
**File:** `app/Listeners/HandleOrderPlaced.php`
- ✅ **Uncommented email sending code** - The email functionality was previously disabled
- ✅ **Added comprehensive error handling** with fallback mechanisms
- ✅ **Added queue support** for better performance
- ✅ **Enhanced logging** for easier debugging
- ✅ **Multiple email fallback strategies** (with PDF → without PDF → queue → immediate)

### 2. Improved OrderInvoiceMail Class
**File:** `app/Mail/OrderInvoiceMail.php`
- ✅ **Enhanced PDF attachment logic** with multiple path checking
- ✅ **Added file size validation** (max 10MB attachments)
- ✅ **Improved memory management** for PDF generation
- ✅ **Better error handling and logging**
- ✅ **Added queue job failure handling**
- ✅ **Optimized PDF generation settings**

### 3. Created Queue Job for Email Sending
**File:** `app/Jobs/SendOrderInvoiceEmail.php`
- ✅ **Async email processing** for better performance
- ✅ **Automatic retry mechanism** (3 attempts with progressive backoff)
- ✅ **Comprehensive error handling** and logging
- ✅ **Fallback to no-PDF** on final attempt
- ✅ **5-minute timeout protection**

### 4. Enhanced BillPDFService Integration
**File:** `app/Services/BillPDFService.php` (already existed)
- ✅ **Comprehensive PDF generation** with multiple formats
- ✅ **Image processing for PDFs** (logo conversion to base64)
- ✅ **Caching mechanisms** for better performance
- ✅ **Memory optimization** and error handling

## Configuration Required

### 1. Email Configuration (.env file)
```bash
# Choose your mail driver
MAIL_MAILER=smtp  # Change from 'log' to 'smtp' or other driver

# SMTP Configuration (example)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Your Store Name"

# Alternative drivers:
# MAIL_MAILER=sendmail
# MAIL_MAILER=ses
# MAIL_MAILER=mailgun
```

### 2. App Settings (via admin panel or database)
Add these settings to your `app_settings` table:

```sql
INSERT INTO app_settings (key, value, company_id) VALUES
('email_notifications', 'true', NULL),
('use_email_queue', 'true', NULL),
('company_name', 'Your Company Name', 1),
('company_email', 'info@yourcompany.com', 1),
('company_phone', '+91 9876543210', 1),
('company_address', 'Your Company Address', 1);
```

### 3. Queue Configuration
```bash
# In .env file
QUEUE_CONNECTION=database  # or redis, sqs, etc.

# Run queue migration
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work --tries=3 --timeout=300
```

### 4. Storage Directories
Ensure these directories exist and are writable:
```bash
mkdir -p storage/app/temp
mkdir -p storage/app/temp/bills
chmod -R 775 storage/app/temp
```

## Testing the Setup

### 1. Run the Test Script
```bash
php test_pdf_email_sending.php
```

### 2. Manual Testing
Create a test order with a customer email and check:
- Order creation triggers email
- PDF is generated successfully
- Email is sent (check mail logs)
- Customer receives email with PDF attachment

### 3. Check Logs
Monitor these log files:
```bash
tail -f storage/logs/laravel.log | grep -i "email\|pdf\|order"
```

## Troubleshooting

### Common Issues and Solutions

#### 1. "Mail driver is set to 'log'"
**Problem:** Emails are being logged instead of sent
**Solution:** Change `MAIL_MAILER=log` to `MAIL_MAILER=smtp` in .env

#### 2. "PDF generation failed"
**Problem:** PDF creation errors
**Solutions:**
- Check if `dompdf` package is installed: `composer require barryvdh/laravel-dompdf`
- Verify view templates exist: `resources/views/admin/orders/invoice-pdf.blade.php`
- Check memory limit: increase to 512M or higher
- Verify image paths are accessible

#### 3. "SMTP connection failed"
**Problem:** Cannot connect to email server
**Solutions:**
- Verify SMTP credentials in .env
- Check firewall settings
- For Gmail: use App Passwords instead of regular password
- Test connection: `php artisan tinker` then `Mail::raw('test', function($msg) { $msg->to('test@example.com'); });`

#### 4. "Queue job failed"
**Problem:** Email queue jobs are failing
**Solutions:**
- Start queue worker: `php artisan queue:work`
- Check failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

#### 5. "PDF file too large"
**Problem:** PDF attachments exceed size limit
**Solutions:**
- Optimize images in PDF templates
- Reduce PDF DPI in BillPDFService
- Check email provider attachment limits

#### 6. "View not found"
**Problem:** Email or PDF templates missing
**Solutions:**
- Verify template exists: `resources/views/emails/order-invoice.blade.php`
- Check PDF template: `resources/views/admin/orders/invoice-pdf.blade.php`
- Clear view cache: `php artisan view:clear`

### Performance Optimization

#### 1. Use Queue for Email Sending
```php
// In app settings
'use_email_queue' => true
```

#### 2. Optimize PDF Generation
- Use thermal format for smaller files
- Enable company settings caching
- Optimize image sizes

#### 3. Monitor Queue Performance
```bash
# Monitor queue status
php artisan queue:monitor

# Optimize queue workers
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

## Security Considerations

### 1. Email Validation
- Customer emails are validated before sending
- Only orders with valid emails trigger email sending

### 2. PDF Security
- Temporary PDF files are automatically cleaned up
- PDF generation has memory and time limits
- File size validation prevents abuse

### 3. Queue Security
- Email jobs have retry limits
- Sensitive data is not logged in queue failures

## Monitoring and Maintenance

### 1. Regular Monitoring
- Check email delivery success rates
- Monitor PDF generation performance
- Review error logs regularly

### 2. Cleanup Tasks
```php
// Add to scheduler (app/Console/Kernel.php)
$schedule->call(function () {
    $billService = new BillPDFService();
    $billService->cleanupTempFiles(24); // Cleanup files older than 24 hours
})->daily();
```

### 3. Health Checks
- Verify email queue is running
- Test PDF generation periodically
- Monitor disk space in temp directories

## Next Steps

1. **Test the configuration** using the provided test script
2. **Configure your email driver** with real SMTP credentials
3. **Set up queue workers** for production environment
4. **Monitor logs** for the first few orders
5. **Optimize settings** based on your specific needs

## Support

If you encounter any issues:
1. Check the Laravel logs in `storage/logs/laravel.log`
2. Run the test script to identify configuration issues
3. Verify all dependencies are installed
4. Ensure proper file permissions

The system is now fully configured to send PDF invoices automatically when orders are placed!
