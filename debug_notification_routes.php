<?php
// Debug script to test notification routes and find issues
// Run this with: php artisan tinker

echo "=== NOTIFICATION ROUTE PARAMETER DEBUG ===\n\n";

try {
    // 1. Test all notification routes
    echo "1. Testing route generation...\n";
    
    $routes = [
        'admin.notifications.index' => [],
        'admin.notifications.unread' => [],
        'admin.notifications.count' => [],
        'admin.notifications.mark-all-read' => [],
        'admin.notifications.bulk-mark-read' => [],
        'admin.notifications.mark-read' => ['notification' => 1],
        'admin.notifications.mark-read-by-id' => ['id' => 1],
        'admin.notifications.mark-read-by-id-body' => [],
        'admin.notifications.destroy' => ['notification' => 1],
        'admin.notifications.delete-by-id' => ['id' => 1],
    ];
    
    foreach ($routes as $routeName => $params) {
        try {
            $url = route($routeName, $params);
            echo "✅ {$routeName}: {$url}\n";
        } catch (Exception $e) {
            echo "❌ {$routeName}: " . $e->getMessage() . "\n";
        }
    }
    
    // 2. Test calling the problematic route without parameter
    echo "\n2. Testing the problematic route call...\n";
    
    try {
        // This is what's causing the error
        $badRoute = route('admin.notifications.mark-read-by-id');
        echo "❌ This should fail: {$badRoute}\n";
    } catch (Exception $e) {
        echo "✅ Expected error caught: " . $e->getMessage() . "\n";
    }
    
    // 3. Test the alternative route
    echo "\n3. Testing alternative route (without parameter)...\n";
    
    try {
        $goodRoute = route('admin.notifications.mark-read-by-id-body');
        echo "✅ Alternative route works: {$goodRoute}\n";
    } catch (Exception $e) {
        echo "❌ Alternative route failed: " . $e->getMessage() . "\n";
    }
    
    // 4. Test with sample data
    echo "\n4. Testing notification functionality...\n";
    
    if (Schema::hasTable('notifications') && class_exists('App\\Models\\Notification')) {
        // Find or create a test notification
        $company = \App\Models\SuperAdmin\Company::first();
        
        if ($company) {
            $testNotification = \App\Models\Notification::firstOrCreate([
                'company_id' => $company->id,
                'type' => 'debug_test',
                'title' => 'Debug Test Notification'
            ], [
                'message' => 'This is a test notification for debugging route issues',
                'icon' => 'fas fa-bug',
                'color' => 'warning',
                'is_read' => false,
                'data' => json_encode(['debug' => true])
            ]);
            
            echo "✅ Test notification ready: ID {$testNotification->id}\n";
            
            // Test the routes with real ID
            $testRoutes = [
                'admin.notifications.mark-read-by-id' => ['id' => $testNotification->id],
                'admin.notifications.delete-by-id' => ['id' => $testNotification->id],
            ];
            
            foreach ($testRoutes as $routeName => $params) {
                try {
                    $url = route($routeName, $params);
                    echo "✅ {$routeName} with real ID: {$url}\n";
                } catch (Exception $e) {
                    echo "❌ {$routeName} with real ID failed: " . $e->getMessage() . "\n";
                }
            }
            
        } else {
            echo "❌ No companies found for testing\n";
        }
    } else {
        echo "❌ Notifications table or model not available\n";
    }
    
    // 5. Generate example JavaScript code
    echo "\n5. Example JavaScript usage:\n";
    
    echo "
// Method 1: Use route with ID in URL (requires notification ID)
function markAsReadById(notificationId) {
    fetch(`/admin/notifications/mark-read-by-id/\${notificationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
        }
    }).then(response => response.json());
}

// Method 2: Use route with ID in request body (doesn't require URL parameter)
function markAsReadByIdBody(notificationId) {
    fetch('/admin/notifications/mark-read-by-id', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: notificationId })
    }).then(response => response.json());
}

// Method 3: Use existing model binding route (already working)
function markAsReadExisting(notificationId) {
    fetch(`/admin/notifications/\${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
        }
    }).then(response => response.json());
}
";
    
    // 6. Recommendations
    echo "\n=== RECOMMENDATIONS ===\n";
    echo "1. If you're getting the parameter error, find where route('admin.notifications.mark-read-by-id') is called\n";
    echo "2. Either pass the ID parameter: route('admin.notifications.mark-read-by-id', \$id)\n";
    echo "3. Or use the new route that accepts ID in body: route('admin.notifications.mark-read-by-id-body')\n";
    echo "4. Clear route cache: php artisan route:clear\n";
    echo "5. Check browser developer tools Network tab to see which request is failing\n";
    
} catch (Exception $e) {
    echo "❌ Debug error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
