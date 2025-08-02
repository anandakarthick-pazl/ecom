// Updated JavaScript for notifications with proper parameter handling
// Add this to your admin layout or notification views

document.addEventListener('DOMContentLoaded', function() {
    
    // Method 1: Mark as read using URL parameter (recommended)
    function markNotificationAsReadById(notificationId) {
        if (!notificationId) {
            console.error('Notification ID is required');
            return;
        }
        
        fetch(`/admin/notifications/mark-read-by-id/${notificationId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Notification marked as read');
                // Update UI - remove unread styling
                const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                if (notificationElement) {
                    notificationElement.classList.add('read');
                    notificationElement.classList.remove('unread');
                }
            } else {
                console.error('Failed to mark notification as read:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Method 2: Mark as read using request body (alternative)
    function markNotificationAsReadByBody(notificationId) {
        if (!notificationId) {
            console.error('Notification ID is required');
            return;
        }
        
        fetch('/admin/notifications/mark-read-by-id', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Notification marked as read');
            } else {
                console.error('Failed to mark notification as read:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Method 3: Use existing model binding route (this already works)
    function markNotificationAsReadExisting(notificationId) {
        if (!notificationId) {
            console.error('Notification ID is required');
            return;
        }
        
        fetch(`/admin/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Notification marked as read');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Bulk mark as read
    function markMultipleNotificationsAsRead(notificationIds) {
        if (!Array.isArray(notificationIds) || notificationIds.length === 0) {
            console.error('Notification IDs array is required');
            return;
        }
        
        fetch('/admin/notifications/bulk-mark-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                notification_ids: notificationIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(`Marked ${data.updated_count} notifications as read`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Get unread notification count
    function getUnreadNotificationCount() {
        fetch('/admin/notifications/count')
            .then(response => response.json())
            .then(data => {
                console.log(`Unread notifications: ${data.count}`);
                // Update badge
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
    }
    
    // Event listeners for notification buttons
    document.addEventListener('click', function(e) {
        // Mark as read button
        if (e.target.classList.contains('mark-read-btn') || e.target.closest('.mark-read-btn')) {
            e.preventDefault();
            const button = e.target.classList.contains('mark-read-btn') ? e.target : e.target.closest('.mark-read-btn');
            const notificationId = button.dataset.id;
            
            if (notificationId) {
                markNotificationAsReadById(notificationId);
                
                // Remove the button and update UI immediately
                button.remove();
                const row = button.closest('tr');
                if (row) {
                    row.classList.remove('table-warning');
                }
            }
        }
        
        // Delete notification button
        if (e.target.classList.contains('delete-notification-btn') || e.target.closest('.delete-notification-btn')) {
            e.preventDefault();
            const button = e.target.classList.contains('delete-notification-btn') ? e.target : e.target.closest('.delete-notification-btn');
            const notificationId = button.dataset.id;
            
            if (notificationId && confirm('Are you sure you want to delete this notification?')) {
                fetch(`/admin/notifications/delete-by-id/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = button.closest('tr');
                        if (row) {
                            row.remove();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error deleting notification:', error);
                });
            }
        }
    });
    
    // Mark all as read
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated state
                }
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }
    
    // Refresh notification count periodically
    setInterval(getUnreadNotificationCount, 30000); // Every 30 seconds
    
    // Initial count load
    getUnreadNotificationCount();
    
    // Make functions globally available if needed
    window.markNotificationAsReadById = markNotificationAsReadById;
    window.markNotificationAsReadByBody = markNotificationAsReadByBody;
    window.markNotificationAsReadExisting = markNotificationAsReadExisting;
    window.markMultipleNotificationsAsRead = markMultipleNotificationsAsRead;
    window.getUnreadNotificationCount = getUnreadNotificationCount;
});
