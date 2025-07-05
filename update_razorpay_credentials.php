<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;

echo "=== RAZORPAY CREDENTIALS SETUP ===\n\n";

echo "This script will help you update your Razorpay credentials.\n";
echo "You can get these from: https://dashboard.razorpay.com/app/keys\n\n";

// Get input function for different environments
function getInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

// Get credentials from user
echo "Please enter your Razorpay credentials:\n";
echo "=====================================\n";

$keyId = getInput("Razorpay Key ID (starts with rzp_test_ or rzp_live_): ");
$keySecret = getInput("Razorpay Key Secret: ");
$webhookSecret = getInput("Webhook Secret (optional, press Enter to skip): ");

// Validate inputs
if (empty($keyId) || empty($keySecret)) {
    echo "❌ Key ID and Key Secret are required!\n";
    exit(1);
}

if (!str_starts_with($keyId, 'rzp_test_') && !str_starts_with($keyId, 'rzp_live_')) {
    echo "⚠️  Warning: Key ID doesn't start with rzp_test_ or rzp_live_\n";
    $confirm = getInput("Continue anyway? (y/N): ");
    if (strtolower($confirm) !== 'y') {
        echo "Cancelled.\n";
        exit(0);
    }
}

echo "\nUpdating payment methods...\n";

// Update all Razorpay payment methods
$paymentMethods = PaymentMethod::where('type', 'razorpay')->get();

if ($paymentMethods->isEmpty()) {
    echo "❌ No Razorpay payment methods found! Please run setup_razorpay_payment.php first.\n";
    exit(1);
}

$updated = 0;
foreach ($paymentMethods as $method) {
    $method->update([
        'razorpay_key_id' => $keyId,
        'razorpay_key_secret' => $keySecret,
        'razorpay_webhook_secret' => $webhookSecret ?: null,
        'is_active' => true
    ]);
    
    echo "✅ Updated payment method ID: {$method->id} (Company ID: " . ($method->company_id ?? 'Global') . ")\n";
    $updated++;
}

echo "\n=== UPDATE COMPLETED ===\n";
echo "Updated {$updated} payment method(s)\n";
echo "Key ID: {$keyId}\n";
echo "Key Secret: " . str_repeat('*', strlen($keySecret) - 4) . substr($keySecret, -4) . "\n";

if ($webhookSecret) {
    echo "Webhook Secret: Set\n";
    echo "\nWebhook URL for Razorpay Dashboard:\n";
    echo "URL: " . url('/razorpay/webhook') . "\n";
    echo "Events: payment.captured, payment.failed, refund.created\n";
} else {
    echo "Webhook Secret: Not set (webhooks will not work)\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Test a payment on your website\n";
echo "2. Check the application logs for any errors\n";
echo "3. If using webhooks, configure them in Razorpay Dashboard\n";

echo "\n=== CREDENTIALS SAVED ===\n";
