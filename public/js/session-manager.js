/**
 * Session Manager for Herbal Ecom SaaS Platform
 * Handles session expiration, auto-refresh, and user notifications
 */

class SessionManager {
    constructor(options = {}) {
        this.options = {
            checkInterval: 60000, // Check every minute
            warningTime: 300, // Warn 5 minutes before expiration
            autoRefresh: true,
            redirectUrl: '/login',
            ...options
        };
        
        this.sessionExpiry = null;
        this.warningShown = false;
        this.checkTimer = null;
        this.isActive = true;
        
        this.init();
    }
    
    init() {
        this.loadSessionInfo();
        this.setupEventListeners();
        this.startSessionCheck();
        
        console.log('SessionManager initialized');
    }
    
    async loadSessionInfo() {
        try {
            const response = await fetch('/auth/check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.authenticated) {
                    // Calculate session expiry time
                    this.sessionExpiry = Date.now() + (data.expires_in * 1000);
                    console.log('Session expires in:', Math.round(data.expires_in / 60), 'minutes');
                } else {
                    console.log('User not authenticated');
                    this.handleSessionExpired();
                }
            } else {
                console.warn('Failed to check session status');
            }
        } catch (error) {
            console.error('Error checking session:', error);
        }
    }
    
    setupEventListeners() {
        // Track user activity
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                this.onUserActivity();
            }, { passive: true });
        });
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.onPageVisible();
            }
        });
        
        // Handle before unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    }
    
    startSessionCheck() {
        this.checkTimer = setInterval(() => {
            this.checkSession();
        }, this.options.checkInterval);
    }
    
    stopSessionCheck() {
        if (this.checkTimer) {
            clearInterval(this.checkTimer);
            this.checkTimer = null;
        }
    }
    
    checkSession() {
        if (!this.sessionExpiry) {
            return;
        }
        
        const timeRemaining = this.sessionExpiry - Date.now();
        const secondsRemaining = Math.round(timeRemaining / 1000);
        
        console.log('Session check - seconds remaining:', secondsRemaining);
        
        if (timeRemaining <= 0) {
            this.handleSessionExpired();
        } else if (timeRemaining <= (this.options.warningTime * 1000) && !this.warningShown) {
            this.showExpirationWarning(secondsRemaining);
        }
    }
    
    showExpirationWarning(secondsRemaining) {
        this.warningShown = true;
        
        const minutes = Math.ceil(secondsRemaining / 60);
        
        // Create warning notification
        const notification = this.createNotification({
            type: 'warning',
            title: 'Session Expiring Soon',
            message: `Your session will expire in ${minutes} minute(s). Click to extend your session.`,
            actions: [
                {
                    text: 'Extend Session',
                    action: () => this.extendSession()
                },
                {
                    text: 'Logout Now',
                    action: () => this.logout()
                }
            ]
        });
        
        console.warn(`Session expires in ${minutes} minutes`);
    }
    
    async extendSession() {
        try {
            // Make a simple request to extend the session
            const response = await fetch('/auth/check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.authenticated) {
                    // Update session expiry
                    this.sessionExpiry = Date.now() + (data.expires_in * 1000);
                    this.warningShown = false;
                    
                    this.showNotification({
                        type: 'success',
                        title: 'Session Extended',
                        message: 'Your session has been extended successfully.',
                        autoHide: true
                    });
                    
                    console.log('Session extended successfully');
                } else {
                    this.handleSessionExpired();
                }
            } else {
                throw new Error('Failed to extend session');
            }
        } catch (error) {
            console.error('Error extending session:', error);
            this.showNotification({
                type: 'error',
                title: 'Session Extension Failed',
                message: 'Unable to extend session. Please login again.',
                autoHide: true
            });
        }
    }
    
    handleSessionExpired() {
        this.cleanup();
        
        console.warn('Session expired - redirecting to login');
        
        this.showNotification({
            type: 'error',
            title: 'Session Expired',
            message: 'Your session has expired. Redirecting to login...',
            autoHide: false
        });
        
        // Clear any sensitive data from localStorage/sessionStorage
        this.clearClientStorage();
        
        // Redirect to login after a brief delay
        setTimeout(() => {
            window.location.href = this.options.redirectUrl;
        }, 2000);
    }
    
    async logout() {
        try {
            const response = await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            if (response.ok) {
                window.location.href = '/login';
            } else {
                // Force redirect even if logout request fails
                window.location.href = '/login';
            }
        } catch (error) {
            console.error('Logout error:', error);
            window.location.href = '/login';
        }
    }
    
    onUserActivity() {
        if (!this.isActive) {
            this.isActive = true;
            console.log('User became active');
        }
        
        // Auto-refresh session if near expiration and user is active
        if (this.options.autoRefresh && this.sessionExpiry) {
            const timeRemaining = this.sessionExpiry - Date.now();
            
            if (timeRemaining > 0 && timeRemaining <= (this.options.warningTime * 1000) && !this.warningShown) {
                this.extendSession();
            }
        }
    }
    
    onPageVisible() {
        // Check session when page becomes visible
        this.loadSessionInfo();
    }
    
    createNotification(options) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.session-notification');
        existingNotifications.forEach(n => n.remove());
        
        const notification = document.createElement('div');\n        notification.className = `session-notification fixed top-4 right-4 z-50 max-w-sm bg-white border border-gray-300 rounded-lg shadow-lg p-4 ${this.getNotificationClass(options.type)}`;\n        \n        const icon = this.getNotificationIcon(options.type);\n        \n        notification.innerHTML = `\n            <div class=\"flex items-start\">\n                <div class=\"flex-shrink-0\">\n                    ${icon}\n                </div>\n                <div class=\"ml-3 flex-1\">\n                    <h3 class=\"text-sm font-medium text-gray-900\">${options.title}</h3>\n                    <p class=\"mt-1 text-sm text-gray-700\">${options.message}</p>\n                    ${options.actions ? this.createActionButtons(options.actions) : ''}\n                </div>\n                <div class=\"ml-4 flex-shrink-0\">\n                    <button class=\"inline-flex text-gray-400 hover:text-gray-600\" onclick=\"this.parentElement.parentElement.parentElement.remove()\">\n                        <span class=\"sr-only\">Close</span>\n                        <svg class=\"h-5 w-5\" fill=\"currentColor\" viewBox=\"0 0 20 20\">\n                            <path fill-rule=\"evenodd\" d=\"M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z\" clip-rule=\"evenodd\"></path>\n                        </svg>\n                    </button>\n                </div>\n            </div>\n        `;\n        \n        document.body.appendChild(notification);\n        \n        // Auto-hide if specified\n        if (options.autoHide) {\n            setTimeout(() => {\n                if (notification.parentNode) {\n                    notification.remove();\n                }\n            }, 5000);\n        }\n        \n        return notification;\n    }\n    \n    showNotification(options) {\n        return this.createNotification(options);\n    }\n    \n    createActionButtons(actions) {\n        return `\n            <div class=\"mt-3 flex space-x-2\">\n                ${actions.map(action => `\n                    <button onclick=\"${action.action.toString().replace('() =>', '')}\" \n                            class=\"px-3 py-1 text-xs font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50\">\n                        ${action.text}\n                    </button>\n                `).join('')}\n            </div>\n        `;\n    }\n    \n    getNotificationClass(type) {\n        const classes = {\n            success: 'border-green-300',\n            warning: 'border-yellow-300',\n            error: 'border-red-300',\n            info: 'border-blue-300'\n        };\n        return classes[type] || classes.info;\n    }\n    \n    getNotificationIcon(type) {\n        const icons = {\n            success: '<svg class=\"h-5 w-5 text-green-500\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\" clip-rule=\"evenodd\"></path></svg>',\n            warning: '<svg class=\"h-5 w-5 text-yellow-500\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z\" clip-rule=\"evenodd\"></path></svg>',\n            error: '<svg class=\"h-5 w-5 text-red-500\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z\" clip-rule=\"evenodd\"></path></svg>',\n            info: '<svg class=\"h-5 w-5 text-blue-500\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"></path></svg>'\n        };\n        return icons[type] || icons.info;\n    }\n    \n    getCSRFToken() {\n        const metaTag = document.querySelector('meta[name=\"csrf-token\"]');\n        return metaTag ? metaTag.getAttribute('content') : '';\n    }\n    \n    clearClientStorage() {\n        // Clear sensitive data from client storage\n        try {\n            // Remove specific keys rather than clearing all\n            const keysToRemove = [\n                'auth_token', 'user_data', 'session_data', \n                'company_context', 'admin_data'\n            ];\n            \n            keysToRemove.forEach(key => {\n                localStorage.removeItem(key);\n                sessionStorage.removeItem(key);\n            });\n            \n            console.log('Client storage cleared');\n        } catch (error) {\n            console.warn('Error clearing client storage:', error);\n        }\n    }\n    \n    cleanup() {\n        this.stopSessionCheck();\n        this.isActive = false;\n        console.log('SessionManager cleaned up');\n    }\n    \n    destroy() {\n        this.cleanup();\n        \n        // Remove event listeners\n        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];\n        activityEvents.forEach(event => {\n            document.removeEventListener(event, this.onUserActivity);\n        });\n        \n        document.removeEventListener('visibilitychange', this.onPageVisible);\n        \n        console.log('SessionManager destroyed');\n    }\n}\n\n// Auto-initialize session manager if user is authenticated\nif (typeof window !== 'undefined') {\n    document.addEventListener('DOMContentLoaded', function() {\n        // Only initialize for authenticated pages\n        const isAuthPage = window.location.pathname.includes('/admin') || \n                          window.location.pathname.includes('/super-admin');\n        \n        if (isAuthPage) {\n            window.sessionManager = new SessionManager({\n                checkInterval: 60000, // Check every minute\n                warningTime: 300, // Warn 5 minutes before expiration\n                autoRefresh: true,\n                redirectUrl: '/login'\n            });\n            \n            console.log('Session manager started for authenticated page');\n        }\n    });\n}\n\n// Export for manual usage\nif (typeof module !== 'undefined' && module.exports) {\n    module.exports = SessionManager;\n}