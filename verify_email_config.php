<?php

/**
 * Quick Email Configuration Verification Script
 * 
 * This script quickly checks if your email and PDF functionality
 * is properly configured.
 * 
 * Usage: php verify_email_config.php
 */

echo "=== Email & PDF Configuration Verification ===\n\n";

// Check if running in Laravel context
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "❌ Run this script from your Laravel project root directory\n";
    exit(1);
}

// Load Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "✅ Laravel application loaded\n";

// Check .env file
if (!file_exists(__DIR__ . '/.env')) {
    echo "❌ .env file not found\n";
    exit(1);
}

echo "✅ .env file exists\n";

// Check mail configuration
$mailDriver = env('MAIL_MAILER', 'log');
$mailHost = env('MAIL_HOST', 'localhost');
$mailFrom = env('MAIL_FROM_ADDRESS', 'hello@example.com');

echo "\n--- Mail Configuration ---\n";
echo "Mail Driver: {$mailDriver}\n";

if ($mailDriver === 'log') {
    echo "⚠️  WARNING: Mail driver is set to 'log' - emails will not be sent!\n";
    echo "   Change MAIL_MAILER in .env to 'smtp' or another driver\n";
} else {
    echo "✅ Mail driver configured: {$mailDriver}\n";
}

echo "Mail Host: {$mailHost}\n";
echo "Mail From: {$mailFrom}\n";

// Check required directories
echo "\n--- Storage Directories ---\n";
$directories = [
    'storage/app/temp',
    'storage/app/public',
    'storage/logs'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ {$dir} (writable)\n";
        } else {
            echo "⚠️  {$dir} (not writable)\n";
        }
    } else {
        echo "❌ {$dir} (does not exist)\n";
        // Try to create it
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created {$dir}\n";
        } else {
            echo "❌ Failed to create {$dir}\n";
        }
    }
}

// Check required files
echo "\n--- Required Files ---\n";
$files = [
    'app/Listeners/HandleOrderPlaced.php',
    'app/Mail/OrderInvoiceMail.php',
    'app/Jobs/SendOrderInvoiceEmail.php',
    'app/Services/BillPDFService.php',
    'resources/views/emails/order-invoice.blade.php',
    'resources/views/admin/orders/invoice-pdf.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} (missing)\n";
    }
}

// Check PHP extensions
echo "\n--- PHP Extensions ---\n";
$extensions = ['gd', 'zip', 'fileinfo', 'mbstring', 'dom', 'xml'];

foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ {$ext}\n";
    } else {
        echo "❌ {$ext} (missing)\n";
    }
}

// Quick recommendations
echo "\n--- Quick Setup Recommendations ---\n";

if ($mailDriver === 'log') {
    echo "1. Update .env file:\n";
    echo "   MAIL_MAILER=smtp\n";
    echo "   MAIL_HOST=your-smtp-host\n";
    echo "   MAIL_PORT=587\n";
    echo "   MAIL_USERNAME=your-email@domain.com\n";
    echo "   MAIL_PASSWORD=your-password\n";
    echo "   MAIL_ENCRYPTION=tls\n";
    echo "   MAIL_FROM_ADDRESS=your-email@domain.com\n\n";
}

echo "2. Run the full test script:\n";
echo "   php test_pdf_email_sending.php\n\n";

echo "3. Start queue worker for email processing:\n";
echo "   php artisan queue:work\n\n";

echo "4. Test with a real order to verify functionality\n\n";

echo "=== Verification Complete ===\n";

if ($mailDriver !== 'log') {
    echo "🎉 Configuration looks good! Your PDF email system should work.\n";
} else {
    echo "⚠️  Please configure your email driver to start sending emails.\n";
}

echo "\nFor detailed setup instructions, see: PDF_EMAIL_SETUP_GUIDE.md\n";
