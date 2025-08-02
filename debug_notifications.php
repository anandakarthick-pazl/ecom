<?php
// Notification Routes Debug Script
// Run this with: php artisan tinker

echo "=== NOTIFICATION ROUTES DEBUG ===\n\n";

try {
    // 1. Check if notification routes are registered
    echo "1. Checking notification routes...\n";
    
    $notificationRoutes = [
        'admin.notifications.index',
        'admin.notifications.unread',
        'admin.notifications.check-new',
        'admin.notifications.mark-read',
        'admin.notifications.mark-read-by-id',
        'admin.notifications.mark-all-read',
        'admin.notifications.bulk-mark-read',
        'admin.notifications.count',
        'admin.notifications.destroy',
        'admin.notifications.delete-by-id'
    ];
    
    foreach ($notificationRoutes as $routeName) {
        try {
            if ($routeName === 'admin.notifications.mark-read' || $routeName === 'admin.notifications.destroy') {
                $route = route($routeName, ['notification' => 1]);
            } elseif (strpos($routeName, 'by-id') !== false || $routeName === 'admin.notifications.delete-by-id') {
                $route = route($routeName, ['id' => 1]);
            } else {
                $route = route($routeName);
            }
            echo "✅ {$routeName}: {$route}\n";
        } catch (Exception $e) {
            echo "❌ {$routeName}: " . $e->getMessage() . "\n";
        }
    }
    
    // 2. Check notification model and database
    echo "\n2. Checking notification model and database...\n";
    
    if (class_exists('App\\Models\\Notification')) {
        echo "✅ Notification model exists\n";
        
        try {
            $model = new \App\Models\Notification();
            echo "✅ Model can be instantiated\n";
        } catch (Exception $e) {
            echo "❌ Model instantiation failed: " . $e->getMessage() . "\n";
        }
        
        // Check if table exists
        if (Schema::hasTable('notifications')) {
            echo "✅ notifications table exists\n";
            
            $count = DB::table('notifications')->count();
            echo "📊 Total notifications: {$count}\n";
            
            if ($count > 0) {
                $recent = DB::table('notifications')->latest()->limit(3)->get();
                echo "Recent notifications:\n";
                foreach ($recent as $notification) {
                    echo "  - ID: {$notification->id}, Type: {$notification->type ?? 'N/A'}, Read: " . ($notification->is_read ?? false ? 'Yes' : 'No') . "\n";
                }
            }
            
        } else {
            echo "❌ notifications table does not exist\n";
            echo "💡 Check if notifications migration has been run\n";
        }
        
    } else {
        echo "❌ Notification model not found\n";
    }
    
    // 3. Check controller
    echo "\n3. Checking NotificationController...\n";
    
    if (class_exists('App\\Http\\Controllers\\Admin\\NotificationController')) {
        echo "✅ NotificationController exists\n";
        
        $controller = new \App\Http\Controllers\Admin\NotificationController();
        echo "✅ Controller can be instantiated\n";
        
        // Check if required methods exist
        $requiredMethods = [
            'index',
            'getUnread',
            'checkNew',
            'markAsRead',
            'markAsReadById',
            'markAllAsRead',
            'bulkMarkAsRead',
            'getUnreadCount',
            'destroy',
            'destroyById'
        ];
        
        foreach ($requiredMethods as $method) {
            if (method_exists($controller, $method)) {
                echo "✅ Method {$method} exists\n";
            } else {
                echo "❌ Method {$method} missing\n";
            }
        }
        
    } else {
        echo "❌ NotificationController not found\n";
    }
    
    // 4. Test creating a sample notification
    echo "\n4. Testing notification creation...\n";
    
    if (Schema::hasTable('notifications')) {
        try {
            // Find a company to test with
            $company = \App\Models\SuperAdmin\Company::first();
            
            if ($company) {
                echo "✅ Found test company: {$company->name} (ID: {$company->id})\n";
                
                // Try to create a test notification
                $testNotification = \App\Models\Notification::updateOrCreate([
                    'company_id' => $company->id,
                    'type' => 'test',
                    'title' => 'Test Notification'
                ], [
                    'message' => 'This is a test notification to verify the system works',
                    'icon' => 'fas fa-bell',
                    'color' => 'primary',
                    'is_read' => false,
                    'data' => json_encode(['test' => true])
                ]);
                
                echo "✅ Test notification created/updated: ID {$testNotification->id}\n";
                
            } else {
                echo "❌ No companies found for testing\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Notification creation test failed: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Quick fixes and suggestions
    echo "\n=== QUICK FIXES ===\n";
    
    if (!Schema::hasTable('notifications')) {
        echo "1. Run migration: php artisan migrate\n";
    }
    
    echo "2. Clear route cache: php artisan route:clear\n";
    echo "3. Clear config cache: php artisan config:clear\n";
    echo "4. Check route list: php artisan route:list --name=notifications\n";
    
    echo "\n=== TESTING URLs ===\n";
    echo "After clearing caches, test these URLs:\n";
    echo "- Admin notifications: http://greenvalleyherbs.local:8000/admin/notifications\n";
    echo "- Unread notifications API: http://greenvalleyherbs.local:8000/admin/notifications/unread\n";
    echo "- Notification count API: http://greenvalleyherbs.local:8000/admin/notifications/count\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
