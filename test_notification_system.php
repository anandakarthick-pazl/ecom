<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test notification creation
echo "🔔 Testing Notification System\n";
echo "================================\n\n";

try {
    // Find a company to use for testing
    $company = \App\Models\SuperAdmin\Company::first();
    
    if (!$company) {
        echo "❌ No companies found. Please create a company first.\n";
        exit(1);
    }
    
    echo "✅ Found company: {$company->name} (ID: {$company->id})\n";
    
    // Set the current tenant context
    app()->instance('current_tenant', $company);
    
    // Create a test notification
    $notification = \App\Models\Notification::createForAdmin(
        'test_notification',
        'Test Notification',
        'This is a test notification to verify the notification system is working properly.',
        [
            'test_data' => 'Test value',
            'created_by' => 'notification_test_script'
        ]
    );
    
    echo "✅ Created test notification (ID: {$notification->id})\n";
    
    // Get notification count
    $unreadCount = \App\Models\Notification::currentTenant()
        ->forAdmin()
        ->unread()
        ->count();
    
    echo "✅ Unread notifications count: {$unreadCount}\n";
    
    // Test the notification attributes
    echo "📋 Notification Details:\n";
    echo "   - Type: {$notification->type}\n";
    echo "   - Title: {$notification->title}\n";
    echo "   - Message: {$notification->message}\n";
    echo "   - Icon: {$notification->icon}\n";
    echo "   - Color: {$notification->color}\n";
    echo "   - Is Read: " . ($notification->is_read ? 'Yes' : 'No') . "\n";
    echo "   - Company ID: {$notification->company_id}\n";
    
    echo "\n🎉 Notification system is working correctly!\n";
    echo "💡 Now you can test the frontend notification icon.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
