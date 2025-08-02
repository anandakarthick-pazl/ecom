<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Sound Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-volume-up"></i> Notification Sound Test</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> Use the buttons below to test the notification sound functionality.
                            The sound should stop when you click "View Order" or "Dismiss" buttons.
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button id="playBtn" class="btn btn-success w-100">
                                    <i class="fas fa-play"></i> Play Notification Sound
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button id="stopBtn" class="btn btn-danger w-100">
                                    <i class="fas fa-stop"></i> Stop Sound
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button id="showNotificationBtn" class="btn btn-primary w-100">
                                    <i class="fas fa-bell"></i> Show Test Order Notification
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button id="toggleSoundBtn" class="btn btn-warning w-100">
                                    <i class="fas fa-volume-mute"></i> Toggle Sound On/Off
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Status</h5>
                            <div id="statusDisplay" class="alert alert-secondary">
                                Ready to test...
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Test Log</h6>
                            <div id="logContainer" class="border p-3" style="height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                <small class="text-muted">Test logs will appear here...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Toast Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
        <div id="testOrderToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-shopping-cart me-2"></i>
                <strong class="me-auto">New Order Received!</strong>
                <small class="text-white-50">Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div>
                    <div class="mb-2">
                        <strong>Order #ORD-2025-TEST-001</strong>
                    </div>
                    <div class="mb-1">
                        <i class="fas fa-user"></i> Customer: <strong>Test Customer</strong>
                    </div>
                    <div class="mb-1">
                        <i class="fas fa-money-bill"></i> Total: <strong class="text-success">₹1234.56</strong>
                    </div>
                    <div class="mb-1">
                        <i class="fas fa-clock"></i> Time: Just now
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="button" id="testViewOrderBtn" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Order
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">
                        <i class="fas fa-times"></i> Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Element -->
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source src="/admin/sounds/notification.mp3" type="audio/mpeg">
        <source src="/admin/sounds/notification.ogg" type="audio/ogg">
        <!-- Fallback beep sound -->
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjGH0fPTgjMGHm7A7+OZURE" type="audio/wav">
    </audio>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('js/notification-sound-manager.js') }}"></script>
    
    <script>
        let testLog = [];
        
        function addLog(message) {
            const timestamp = new Date().toLocaleTimeString();
            testLog.push(`[${timestamp}] ${message}`);
            updateLogDisplay();
        }
        
        function updateLogDisplay() {
            const container = $('#logContainer');
            let html = '';
            testLog.slice(-10).forEach(log => {
                html += `<div class="text-muted small">${log}</div>`;
            });
            container.html(html);
            container.scrollTop(container[0].scrollHeight);
        }
        
        function updateStatus(message, type = 'info') {
            $('#statusDisplay').removeClass('alert-secondary alert-success alert-danger alert-warning')
                               .addClass(`alert-${type}`)
                               .text(message);
        }
        
        // Test buttons
        $('#playBtn').click(function() {
            addLog('Play button clicked');
            updateStatus('Playing notification sound...', 'success');
            if (window.playNotificationSound) {
                playNotificationSound();
                addLog('playNotificationSound() called');
            } else if (window.notificationSoundManager) {
                window.notificationSoundManager.play();
                addLog('notificationSoundManager.play() called');
            } else {
                addLog('ERROR: No sound manager available');
                updateStatus('Error: Sound manager not loaded', 'danger');
            }
        });
        
        $('#stopBtn').click(function() {
            addLog('Stop button clicked');
            updateStatus('Stopping notification sound...', 'warning');
            if (window.stopNotificationSound) {
                stopNotificationSound();
                addLog('stopNotificationSound() called');
            } else if (window.notificationSoundManager) {
                window.notificationSoundManager.stop();
                addLog('notificationSoundManager.stop() called');
            } else {
                addLog('ERROR: No sound manager available');
            }
            updateStatus('Sound stopped', 'secondary');
        });
        
        $('#showNotificationBtn').click(function() {
            addLog('Show notification button clicked');
            updateStatus('Showing test notification...', 'primary');
            
            // Create a mock notification object with an ID
            const mockNotification = {
                id: Date.now(), // Use timestamp as mock ID
                type: 'order_placed',
                title: 'New Order Received!',
                message: 'Order #ORD-2025-TEST-001 has been placed',
                created_at: 'Just now',
                data: {
                    order_number: 'ORD-2025-TEST-001',
                    customer_name: 'Test Customer',
                    total: '1234.56',
                    order_id: 1
                }
            };
            
            // Store the mock notification ID globally for testing
            window.currentTestNotificationId = mockNotification.id;
            
            // Play sound
            if (window.playNotificationSound) {
                playNotificationSound();
                addLog('Playing notification sound');
            }
            
            // Show toast - simulate the showOrderPopup function
            const orderData = mockNotification.data || {};
            
            // Format order details
            let orderDetails = `
                <div class="mb-2">
                    <strong>Order #${orderData.order_number || 'N/A'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-user"></i> Customer: <strong>${orderData.customer_name || 'Guest'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-money-bill"></i> Total: <strong class="text-success">₹${orderData.total || '0.00'}</strong>
                </div>
                <div class="mb-1">
                    <i class="fas fa-clock"></i> Time: ${mockNotification.created_at || 'Just now'}
                </div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-info-circle"></i> Test Notification ID: ${mockNotification.id}
                </div>
            `;
            
            // Update popup content
            $('#testOrderToast .toast-body > div').first().html(orderDetails);
            
            // Store notification ID in the toast
            $('#testOrderToast').attr('data-notification-id', mockNotification.id);
            
            // Show the toast
            const toastEl = document.getElementById('testOrderToast');
            const toast = new bootstrap.Toast(toastEl, {
                autohide: false
            });
            toast.show();
            addLog('Toast notification shown with ID: ' + mockNotification.id);
        });
        
        $('#toggleSoundBtn').click(function() {
            if (window.notificationSoundManager) {
                const enabled = window.notificationSoundManager.toggle();
                updateStatus(`Sound notifications ${enabled ? 'enabled' : 'disabled'}`, enabled ? 'success' : 'warning');
                addLog(`Sound notifications ${enabled ? 'enabled' : 'disabled'}`);
                $(this).html(`<i class="fas fa-volume-${enabled ? 'up' : 'mute'}"></i> Turn Sound ${enabled ? 'Off' : 'On'}`);
            }
        });
        
        // Test the "View Order" button in the toast
        $('#testViewOrderBtn').click(function() {
            addLog('View Order button clicked in toast');
            updateStatus('View Order clicked - stopping sound...', 'info');
            
            // Get the notification ID
            const notificationId = $('#testOrderToast').attr('data-notification-id') || window.currentTestNotificationId;
            
            if (window.stopNotificationSound) {
                stopNotificationSound();
                addLog('Sound stopped via stopNotificationSound()');
            }
            
            // Simulate marking notification as read (in real app this would be an AJAX call)
            if (notificationId) {
                addLog(`Simulating notification mark as read for ID: ${notificationId}`);
                // In real application, this would call markNotificationAsReadById(notificationId)
                addLog('Would call: markNotificationAsReadById(' + notificationId + ')');
            }
            
            // Hide the toast
            const toastEl = document.getElementById('testOrderToast');
            const toast = bootstrap.Toast.getInstance(toastEl);
            if (toast) {
                toast.hide();
                addLog('Toast hidden');
            }
            
            updateStatus('Order viewed - sound stopped - notification marked as read', 'success');
        });
        
        // Test the dismiss button
        $(document).on('click', '[data-bs-dismiss="toast"]', function() {
            const targetToast = $(this).closest('.toast');
            if (targetToast.attr('id') === 'testOrderToast') {
                addLog('Dismiss button clicked in toast');
                updateStatus('Toast dismissed - stopping sound...', 'warning');
                
                // Get the notification ID
                const notificationId = targetToast.attr('data-notification-id') || window.currentTestNotificationId;
                
                if (window.stopNotificationSound) {
                    stopNotificationSound();
                    addLog('Sound stopped via stopNotificationSound()');
                }
                
                // Simulate marking notification as read
                if (notificationId) {
                    addLog(`Simulating notification mark as read for ID: ${notificationId}`);
                    addLog('Would call: markNotificationAsReadById(' + notificationId + ')');
                }
                
                updateStatus('Toast dismissed - sound stopped - notification marked as read', 'secondary');
            }
        });
        
        // Listen for toast hide events
        $('#testOrderToast').on('hidden.bs.toast', function () {
            addLog('Toast hidden event triggered');
            
            // Get the notification ID
            const notificationId = $(this).attr('data-notification-id') || window.currentTestNotificationId;
            
            if (window.stopNotificationSound) {
                stopNotificationSound();
                addLog('Sound stopped on toast hide');
            }
            
            // Simulate marking notification as read if not already done
            if (notificationId) {
                addLog(`Final check - marking notification as read for ID: ${notificationId}`);
                addLog('Would call: markNotificationAsReadById(' + notificationId + ')');
            }
            
            // Clear stored ID
            $(this).removeAttr('data-notification-id');
            window.currentTestNotificationId = null;
            addLog('Cleared notification ID from toast');
        });
        
        // Initialize
        $(document).ready(function() {
            addLog('Test page loaded');
            updateStatus('Ready to test notification sounds', 'info');
            
            // Check if sound manager is available
            setTimeout(function() {
                if (window.notificationSoundManager) {
                    addLog('NotificationSoundManager loaded successfully');
                } else {
                    addLog('WARNING: NotificationSoundManager not found');
                    updateStatus('Warning: Sound manager not loaded', 'warning');
                }
            }, 500);
        });
    </script>
</body>
</html>
