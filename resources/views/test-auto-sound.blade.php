<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Sound Enable Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-good { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-error { color: #dc3545; }
        .log-container { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6;
            max-height: 300px;
            overflow-y: auto;
            font-family: monospace;
        }
        .log-entry {
            padding: 4px 8px;
            border-bottom: 1px solid #e9ecef;
            font-size: 12px;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .test-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2><i class="fas fa-volume-up"></i> Auto Sound Enable Test</h2>
                <p class="text-muted">This page tests automatic sound enablement without user prompts.</p>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card test-card">
                    <div class="card-body text-center">
                        <h5>Sound Manager</h5>
                        <h3 id="soundManagerStatus" class="status-warning">
                            <i class="fas fa-spinner fa-spin"></i>
                        </h3>
                        <small class="text-muted">Initialization</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card test-card">
                    <div class="card-body text-center">
                        <h5>Auto Enable</h5>
                        <h3 id="autoEnableStatus" class="status-warning">
                            <i class="fas fa-spinner fa-spin"></i>
                        </h3>
                        <small class="text-muted">Force Enable</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card test-card">
                    <div class="card-body text-center">
                        <h5>Audio Ready</h5>
                        <h3 id="audioReadyStatus" class="status-warning">
                            <i class="fas fa-spinner fa-spin"></i>
                        </h3>
                        <small class="text-muted">Playback Ready</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card test-card">
                    <div class="card-body text-center">
                        <h5>Browser Support</h5>
                        <h3 id="browserSupportStatus" class="status-warning">
                            <i class="fas fa-spinner fa-spin"></i>
                        </h3>
                        <small class="text-muted">Compatibility</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Controls -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-play"></i> Test Controls</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <button id="testAutoPlay" class="btn btn-primary w-100">
                                    <i class="fas fa-magic"></i> Test Auto Play
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button id="simulateOrderNotification" class="btn btn-success w-100">
                                    <i class="fas fa-shopping-cart"></i> Simulate New Order
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button id="clearLogs" class="btn btn-secondary w-100">
                                    <i class="fas fa-trash"></i> Clear Logs
                                </button>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <button id="forceEnable" class="btn btn-warning w-100">
                                    <i class="fas fa-power-off"></i> Force Enable Sound
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button id="checkStatus" class="btn btn-info w-100">
                                    <i class="fas fa-info-circle"></i> Check Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Display -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-list"></i> System Logs</h5>
                        <small class="text-muted">Real-time status updates</small>
                    </div>
                    <div class="card-body p-0">
                        <div id="logContainer" class="log-container">
                            <div class="log-entry text-muted">Initializing tests...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Audio Element -->
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source src="/admin/sounds/notification.mp3" type="audio/mpeg">
        <source src="/admin/sounds/notification.ogg" type="audio/ogg">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjGH0fPTgjMGHm7A7+OZURE" type="audio/wav">
    </audio>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('js/force-sound-enable.js') }}"></script>
    <script src="{{ asset('js/notification-sound-manager.js') }}"></script>

    <script>
        let logEntries = [];
        
        function addLog(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const entry = {
                time: timestamp,
                message: message,
                type: type
            };
            
            logEntries.push(entry);
            updateLogDisplay();
        }
        
        function updateLogDisplay() {
            const container = $('#logContainer');
            let html = '';
            
            logEntries.slice(-20).forEach(entry => {
                const typeClass = entry.type === 'success' ? 'text-success' : 
                                 entry.type === 'error' ? 'text-danger' : 
                                 entry.type === 'warning' ? 'text-warning' : '';
                html += `<div class="log-entry ${typeClass}">[${entry.time}] ${entry.message}</div>`;
            });
            
            container.html(html);
            container.scrollTop(container[0].scrollHeight);
        }
        
        function updateStatus(element, status, success) {
            const icon = success ? 'fas fa-check-circle' : 'fas fa-times-circle';
            const className = success ? 'status-good' : 'status-error';
            
            $(element).removeClass('status-good status-warning status-error')
                     .addClass(className)
                     .html(`<i class="${icon}"></i>`);
        }
        
        function checkBrowserSupport() {
            const support = {
                audio: !!window.Audio,
                audioContext: !!(window.AudioContext || window.webkitAudioContext),
                notifications: !!window.Notification,
                promises: typeof Promise !== 'undefined'
            };
            
            const allSupported = Object.values(support).every(v => v);
            updateStatus('#browserSupportStatus', 'Browser Support', allSupported);
            
            addLog(`Browser support check: Audio=${support.audio}, AudioContext=${support.audioContext}, Notifications=${support.notifications}`, 
                   allSupported ? 'success' : 'warning');
            
            return allSupported;
        }
        
        function checkSoundManager() {
            const managerExists = !!window.notificationSoundManager;
            updateStatus('#soundManagerStatus', 'Sound Manager', managerExists);
            
            if (managerExists) {
                addLog('Sound manager loaded successfully', 'success');
                return true;
            } else {
                addLog('Sound manager not found!', 'error');
                return false;
            }
        }
        
        function testAutoEnable() {
            addLog('Testing automatic sound enablement...', 'info');
            
            if (window.notificationSoundManager) {
                const wasEnabled = window.notificationSoundManager.forceEnable();
                updateStatus('#autoEnableStatus', 'Auto Enable', wasEnabled);
                
                if (wasEnabled) {
                    addLog('Sound auto-enabled successfully', 'success');
                } else {
                    addLog('Sound auto-enable failed', 'error');
                }
                
                // Test if audio is ready to play
                setTimeout(() => {
                    const isReady = window.notificationSoundManager.isReady();
                    updateStatus('#audioReadyStatus', 'Audio Ready', isReady);
                    
                    if (isReady) {
                        addLog('Audio is ready for playback', 'success');
                    } else {
                        addLog('Audio not ready for playback', 'warning');
                    }
                }, 500);
                
                return wasEnabled;
            } else {
                updateStatus('#autoEnableStatus', 'Auto Enable', false);
                addLog('Cannot test auto-enable: sound manager not available', 'error');
                return false;
            }
        }
        
        function testAutoPlay() {
            addLog('Testing automatic sound playback...', 'info');
            
            if (window.notificationSoundManager) {
                window.notificationSoundManager.play()
                    .then(() => {
                        addLog('✅ AUTOMATIC SOUND PLAYBACK SUCCESSFUL!', 'success');
                        updateStatus('#audioReadyStatus', 'Audio Ready', true);
                    })
                    .catch(error => {
                        addLog(`❌ Automatic sound playback failed: ${error.message}`, 'error');
                        updateStatus('#audioReadyStatus', 'Audio Ready', false);
                    });
            } else {
                addLog('Cannot test playback: sound manager not available', 'error');
            }
        }
        
        function simulateOrderNotification() {
            addLog('🛒 Simulating new order notification...', 'info');
            
            // Simulate the exact notification flow from admin panel
            const mockNotification = {
                id: Date.now(),
                type: 'order_placed',
                title: 'New Order Received!',
                message: 'Order #ORD-2025-TEST placed by Test Customer',
                data: {
                    order_number: 'ORD-2025-TEST',
                    customer_name: 'Test Customer',
                    total: '₹1,234.56',
                    order_id: 123
                }
            };
            
            // Use the same logic as the admin panel
            if (window.notificationSoundManager) {
                addLog('Forcing sound enable for new order...', 'info');
                window.notificationSoundManager.forceEnable();
                
                window.notificationSoundManager.play()
                    .then(() => {
                        addLog('🔔 NEW ORDER SOUND PLAYED AUTOMATICALLY!', 'success');
                        addLog('✅ This proves sound will work for real orders', 'success');
                    })
                    .catch(error => {
                        addLog(`❌ Order notification sound failed: ${error.message}`, 'error');
                        addLog('This indicates a problem that needs fixing', 'warning');
                    });
            }
            
            // Show browser notification if supported
            if (window.Notification && Notification.permission === 'granted') {
                new Notification(mockNotification.title, {
                    body: mockNotification.message,
                    icon: '/favicon.ico'
                });
                addLog('Browser notification shown', 'info');
            }
        }
        
        function runAllTests() {
            addLog('🚀 Starting comprehensive sound tests...', 'info');
            
            setTimeout(() => checkBrowserSupport(), 100);
            setTimeout(() => checkSoundManager(), 300);
            setTimeout(() => testAutoEnable(), 500);
            
            addLog('All tests initiated. Check status cards above.', 'info');
        }
        
        // Event handlers
        $('#testAutoPlay').click(testAutoPlay);
        $('#simulateOrderNotification').click(simulateOrderNotification);
        $('#forceEnable').click(() => {
            if (window.notificationSoundManager) {
                window.notificationSoundManager.forceEnable();
                addLog('Force enable called', 'info');
                setTimeout(() => testAutoEnable(), 200);
            }
        });
        $('#checkStatus').click(runAllTests);
        $('#clearLogs').click(() => {
            logEntries = [];
            updateLogDisplay();
        });
        
        // Initialize on page load
        $(document).ready(function() {
            addLog('🎵 Auto Sound Test Page Loaded', 'info');
            addLog('This page tests automatic sound without user prompts', 'info');
            
            // Run tests after scripts load
            setTimeout(runAllTests, 1000);
            
            // Periodic status check
            setInterval(() => {
                if (window.notificationSoundManager && window.notificationSoundManager.isReady()) {
                    updateStatus('#audioReadyStatus', 'Audio Ready', true);
                }
            }, 2000);
        });
        
        // Log any console messages
        const originalLog = console.log;
        console.log = function(...args) {
            if (args[0] && typeof args[0] === 'string' && args[0].includes('sound')) {
                addLog(`Console: ${args.join(' ')}`, 'info');
            }
            originalLog.apply(console, arguments);
        };
    </script>
</body>
</html>
