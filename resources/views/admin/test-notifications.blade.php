@extends('admin.layouts.app')

@section('title', 'Test Order Notifications')
@section('page_title', 'Test Order Notification System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Order Notification Test Panel</h5>
                </div>
                <div class="card-body">
                    <p>Use this panel to test the order notification system without placing actual orders.</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Current Settings:</h6>
                            <ul>
                                <li>Sound Notifications: <span class="badge bg-{{ \App\Models\AppSetting::get('sound_notifications', true) ? 'success' : 'danger' }}">{{ \App\Models\AppSetting::get('sound_notifications', true) ? 'Enabled' : 'Disabled' }}</span></li>
                                <li>Popup Notifications: <span class="badge bg-{{ \App\Models\AppSetting::get('popup_notifications', true) ? 'success' : 'danger' }}">{{ \App\Models\AppSetting::get('popup_notifications', true) ? 'Enabled' : 'Disabled' }}</span></li>
                                <li>Order Notifications: <span class="badge bg-{{ \App\Models\AppSetting::get('order_notifications', true) ? 'success' : 'danger' }}">{{ \App\Models\AppSetting::get('order_notifications', true) ? 'Enabled' : 'Disabled' }}</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Browser Permissions:</h6>
                            <p id="browserPermission">Checking...</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Click the button below to simulate a new order notification.
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-lg" id="testNotificationBtn">
                        <i class="fas fa-bell"></i> Test Order Notification
                    </button>
                    
                    <hr class="my-4">
                    
                    <h6>How the System Works:</h6>
                    <ol>
                        <li>Customer places an order on the e-commerce site</li>
                        <li>System creates a notification in the database</li>
                        <li>Admin panel checks for new notifications every 10 seconds</li>
                        <li>When found, it shows:
                            <ul>
                                <li>Popup notification (if enabled)</li>
                                <li>Sound alert (if enabled)</li>
                                <li>Browser notification (if permitted)</li>
                                <li>Bell icon animation</li>
                            </ul>
                        </li>
                    </ol>
                    
                    <div class="alert alert-warning mt-3">
                        <strong>Note:</strong> In production, notifications are triggered automatically when real orders are placed. This test page simulates the notification without creating an actual order.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check browser notification permission
    if ("Notification" in window) {
        const permission = Notification.permission;
        let permissionText = '';
        let permissionClass = '';
        
        switch(permission) {
            case 'granted':
                permissionText = '<span class="text-success"><i class="fas fa-check-circle"></i> Granted - Browser notifications will work</span>';
                break;
            case 'denied':
                permissionText = '<span class="text-danger"><i class="fas fa-times-circle"></i> Denied - Please enable in browser settings</span>';
                break;
            default:
                permissionText = '<span class="text-warning"><i class="fas fa-question-circle"></i> Not set - Browser will ask for permission</span>';
                // Request permission
                Notification.requestPermission();
        }
        
        $('#browserPermission').html(permissionText);
    } else {
        $('#browserPermission').html('<span class="text-danger"><i class="fas fa-times-circle"></i> Not supported by this browser</span>');
    }
    
    // Test notification button
    $('#testNotificationBtn').click(function() {
        // Create a fake notification object
        const testNotification = {
            id: 'test-' + Date.now(),
            type: 'order_placed',
            title: 'New Order Received',
            message: 'Test Order #TEST-' + Math.floor(Math.random() * 10000) + ' placed by John Doe for ₹' + (Math.random() * 5000 + 500).toFixed(2),
            icon: 'fas fa-shopping-cart',
            color: 'success',
            created_at: 'Just now',
            data: {
                order_id: Math.floor(Math.random() * 1000),
                order_number: 'TEST-' + Math.floor(Math.random() * 10000),
                customer_name: 'John Doe (Test)',
                total: (Math.random() * 5000 + 500).toFixed(2),
                status: 'pending'
            }
        };
        
        // Show success message
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Triggering notification...');
        
        setTimeout(() => {
            // Trigger the notification handlers
            if (typeof handleNewOrderNotification === 'function') {
                handleNewOrderNotification(testNotification);
                
                // Also update the notification badge
                const currentCount = parseInt($('#notificationCount').text()) || 0;
                updateNotificationBadge(currentCount + 1);
                
                alert('Test notification triggered! Check the top-right corner of your screen.');
            } else {
                alert('Notification system not loaded. Please refresh the page.');
            }
            
            $(this).prop('disabled', false).html('<i class="fas fa-bell"></i> Test Order Notification');
        }, 1000);
    });
});
</script>
@endpush
