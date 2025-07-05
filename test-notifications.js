// Test Notification System in Browser Console
// Copy and paste this in your browser console while on the admin panel

// Function to test notification system
async function testNotificationSystem() {
    console.log('Testing Notification System...\n');
    
    // 1. Check current status
    console.log('1. Checking notification system status...');
    try {
        const statusResponse = await fetch('/admin/test-notification');
        const status = await statusResponse.json();
        console.log('Status:', status);
    } catch (error) {
        console.error('Error checking status:', error);
    }
    
    // 2. Create test order
    console.log('\n2. Creating test order with notification...');
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const orderResponse = await fetch('/admin/test-notification/create-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const orderResult = await orderResponse.json();
        console.log('Order creation result:', orderResult);
    } catch (error) {
        console.error('Error creating test order:', error);
    }
    
    // 3. Check for new notifications
    console.log('\n3. Checking for new notifications...');
    try {
        const notifResponse = await fetch('/admin/notifications/unread');
        const notifications = await notifResponse.json();
        console.log('Unread notifications:', notifications);
    } catch (error) {
        console.error('Error checking notifications:', error);
    }
}

// Function to manually check for new notifications
async function checkNewNotifications() {
    try {
        const response = await fetch('/admin/notifications/check-new?last_check=' + new Date().toISOString());
        const data = await response.json();
        console.log('New notifications check:', data);
        
        if (data.hasNew) {
            console.log('Found', data.count, 'new notifications!');
            data.notifications.forEach(n => {
                console.log(`- ${n.type}: ${n.title} - ${n.message}`);
            });
        } else {
            console.log('No new notifications');
        }
    } catch (error) {
        console.error('Error checking new notifications:', error);
    }
}

// Run the test
console.log('=== NOTIFICATION SYSTEM TEST ===');
console.log('Run testNotificationSystem() to test the complete system');
console.log('Run checkNewNotifications() to manually check for new notifications');
console.log('================================\n');

// Auto-run test after 1 second
setTimeout(() => {
    console.log('Auto-running notification system test...\n');
    testNotificationSystem();
}, 1000);
