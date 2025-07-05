<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Events\OrderPlaced;
use Illuminate\Http\Request;

class TestNotificationController extends Controller
{
    public function test()
    {
        $results = [];
        
        // Test 1: Check current tenant
        $results['current_tenant'] = [
            'has_current_tenant' => app()->has('current_tenant'),
            'tenant_id' => app()->has('current_tenant') ? app('current_tenant')->id : null,
            'tenant_name' => app()->has('current_tenant') ? app('current_tenant')->name : null,
        ];
        
        // Test 2: Check session
        $results['session'] = [
            'selected_company_id' => session('selected_company_id'),
            'selected_company_name' => session('selected_company_name'),
        ];
        
        // Test 3: Check notification model
        $results['notification_model'] = [
            'uses_tenant_trait' => in_array('App\Traits\BelongsToTenantEnhanced', class_uses(Notification::class)),
            'has_company_id_fillable' => in_array('company_id', (new Notification)->getFillable()),
        ];
        
        // Test 4: Check database
        try {
            $columns = \DB::select('SHOW COLUMNS FROM notifications WHERE Field = ?', ['company_id']);
            $results['database'] = [
                'has_company_id_column' => count($columns) > 0,
            ];
        } catch (\Exception $e) {
            $results['database'] = [
                'error' => $e->getMessage(),
            ];
        }
        
        // Test 5: Count notifications
        try {
            $results['notification_counts'] = [
                'total' => Notification::count(),
                'with_company_id' => Notification::whereNotNull('company_id')->count(),
                'without_company_id' => Notification::whereNull('company_id')->count(),
                'current_tenant' => Notification::currentTenant()->count(),
                'admin_notifications' => Notification::currentTenant()->forAdmin()->count(),
                'unread' => Notification::currentTenant()->forAdmin()->unread()->count(),
            ];
        } catch (\Exception $e) {
            $results['notification_counts'] = [
                'error' => $e->getMessage(),
            ];
        }
        
        // Test 6: Test creating a notification
        try {
            $testNotification = Notification::createForAdmin(
                'test',
                'Test Notification',
                'This is a test notification to check company_id',
                ['test' => true]
            );
            
            $results['test_notification'] = [
                'created' => true,
                'id' => $testNotification->id,
                'company_id' => $testNotification->company_id,
            ];
            
            // Clean up
            $testNotification->delete();
        } catch (\Exception $e) {
            $results['test_notification'] = [
                'created' => false,
                'error' => $e->getMessage(),
            ];
        }
        
        // Test 7: Check last order
        try {
            $lastOrder = Order::latest()->first();
            if ($lastOrder) {
                $results['last_order'] = [
                    'id' => $lastOrder->id,
                    'order_number' => $lastOrder->order_number,
                    'company_id' => $lastOrder->company_id,
                    'created_at' => $lastOrder->created_at->toDateTimeString(),
                ];
                
                // Check if notification exists for this order
                $orderNotification = Notification::where('type', 'order_placed')
                    ->where('data->order_id', $lastOrder->id)
                    ->first();
                    
                $results['order_notification'] = $orderNotification ? [
                    'exists' => true,
                    'company_id' => $orderNotification->company_id,
                    'created_at' => $orderNotification->created_at->toDateTimeString(),
                ] : ['exists' => false];
            }
        } catch (\Exception $e) {
            $results['last_order'] = [
                'error' => $e->getMessage(),
            ];
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
    
    public function createTestOrder()
    {
        try {
            // Create a simple test order
            $order = Order::create([
                'customer_name' => 'Test Customer',
                'customer_mobile' => '9999999999',
                'customer_email' => 'test@example.com',
                'delivery_address' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'pincode' => '123456',
                'subtotal' => 100,
                'discount' => 0,
                'delivery_charge' => 0,
                'total' => 100,
                'status' => 'pending',
            ]);
            
            // Fire the event
            event(new OrderPlaced($order));
            
            // Check if notification was created
            $notification = Notification::where('type', 'order_placed')
                ->where('data->order_id', $order->id)
                ->first();
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'company_id' => $order->company_id,
                ],
                'notification' => $notification ? [
                    'created' => true,
                    'id' => $notification->id,
                    'company_id' => $notification->company_id,
                ] : ['created' => false],
            ], 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
}
