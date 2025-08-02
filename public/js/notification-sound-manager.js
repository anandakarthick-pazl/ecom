// Enhanced Notification Sound Manager with Auto-Enable
class NotificationSoundManager {
    constructor() {
        this.currentAudio = null;
        this.soundEnabled = true;
        this.audioElement = null;
        this.isPlaying = false;
        this.userInteracted = false;
        this.autoplayAttempted = false;
        this.pendingSound = false;
        this.init();
    }
    
    init() {
        // Get or create audio element
        this.audioElement = document.getElementById('notificationSound');
        if (!this.audioElement) {
            this.createAudioElement();
        }
        
        // Add event listeners
        this.addEventListeners();
        
        // Set up automatic enablement
        this.setupAutoEnable();
        
        // Preload and prepare audio
        this.prepareAudio();
    }
    
    createAudioElement() {
        this.audioElement = document.createElement('audio');
        this.audioElement.id = 'notificationSound';
        this.audioElement.preload = 'auto';
        this.audioElement.style.display = 'none';
        this.audioElement.volume = 0.8; // Set moderate volume
        
        // Add multiple source formats for better compatibility
        const sources = [
            { src: '/admin/sounds/notification.mp3', type: 'audio/mpeg' },
            { src: '/admin/sounds/notification.ogg', type: 'audio/ogg' },
            { src: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBjGH0fPTgjMGHm7A7+OZURE', type: 'audio/wav' }
        ];
        
        sources.forEach(source => {
            const sourceElement = document.createElement('source');
            sourceElement.src = source.src;
            sourceElement.type = source.type;
            this.audioElement.appendChild(sourceElement);
        });
        
        document.body.appendChild(this.audioElement);
    }
    
    addEventListeners() {
        if (this.audioElement) {
            this.audioElement.addEventListener('play', () => {
                this.isPlaying = true;
                this.userInteracted = true; // Mark that audio is working
                console.log('Notification sound started playing');
            });
            
            this.audioElement.addEventListener('pause', () => {
                this.isPlaying = false;
                console.log('Notification sound paused');
            });
            
            this.audioElement.addEventListener('ended', () => {
                this.isPlaying = false;
                this.currentAudio = null;
                console.log('Notification sound ended');
            });
            
            this.audioElement.addEventListener('canplaythrough', () => {
                console.log('Audio loaded and ready to play');
            });
            
            this.audioElement.addEventListener('error', (e) => {
                console.log('Audio error:', e);
                this.handleAudioError();
            });
        }
    }
    
    setupAutoEnable() {
        // List of user interaction events that enable audio
        const interactionEvents = [
            'click', 'keydown', 'keyup', 'mousedown', 'mouseup', 
            'touchstart', 'touchend', 'scroll', 'mousemove'
        ];
        
        const enableAudio = () => {
            this.userInteracted = true;
            console.log('User interaction detected - audio enabled');
            
            // Try to enable audio context
            this.enableAudioContext();
            
            // If there's a pending sound, play it
            if (this.pendingSound) {
                this.pendingSound = false;
                setTimeout(() => this.play(), 100);
            }
        };
        
        // Add listeners for user interactions (remove after first interaction)
        interactionEvents.forEach(event => {
            document.addEventListener(event, enableAudio, { 
                once: true, // Remove after first use
                passive: true 
            });
        });
        
        // Special handling for admin page load
        if (window.location.pathname.includes('/admin')) {
            // Try to enable on page load
            setTimeout(() => {
                this.enableAudioContext();
            }, 500);
        }
    }
    
    enableAudioContext() {
        try {
            // Try to create and resume audio context if needed
            if (window.AudioContext || window.webkitAudioContext) {
                if (!this.audioContext) {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume().then(() => {
                        console.log('Audio context resumed');
                        this.userInteracted = true;
                    }).catch(e => {
                        console.log('Failed to resume audio context:', e);
                    });
                }
            }
            
            // Try a silent play to unlock audio
            if (this.audioElement && !this.autoplayAttempted) {
                this.autoplayAttempted = true;
                this.audioElement.volume = 0;
                this.audioElement.play().then(() => {
                    this.audioElement.pause();
                    this.audioElement.volume = 0.8;
                    this.userInteracted = true;
                    console.log('Audio unlocked via silent play');
                }).catch(e => {
                    this.audioElement.volume = 0.8;
                    console.log('Silent audio unlock failed:', e);
                });
            }
        } catch (e) {
            console.log('Audio context setup failed:', e);
        }
    }
    
    prepareAudio() {
        if (this.audioElement) {
            // Preload the audio
            this.audioElement.load();
            
            // Try to prepare for playback
            setTimeout(() => {
                if (this.audioElement.readyState >= 2) {
                    console.log('Audio preloaded successfully');
                }
            }, 1000);
        }
    }
    
    play() {
        if (!this.soundEnabled || !this.audioElement) {
            console.log('Sound disabled or audio element not available');
            return Promise.resolve();
        }
        
        // If user hasn't interacted yet, store the request
        if (!this.userInteracted) {
            console.log('User interaction required - sound will play after next interaction');
            this.pendingSound = true;
            this.showSilentEnablePrompt();
            return Promise.resolve();
        }
        
        try {
            // Stop any currently playing sound
            this.stop();
            
            // Reset and play
            this.audioElement.currentTime = 0;
            this.currentAudio = this.audioElement;
            
            const playPromise = this.audioElement.play();
            
            if (playPromise !== undefined) {
                return playPromise
                    .then(() => {
                        console.log('Notification sound started successfully');
                        return Promise.resolve();
                    })
                    .catch(error => {
                        console.log('Could not play notification sound:', error);
                        this.handlePlayError(error);
                        return Promise.reject(error);
                    });
            }
            
            return Promise.resolve();
        } catch (error) {
            console.log('Error playing notification sound:', error);
            this.handlePlayError(error);
            return Promise.reject(error);
        }
    }
    
    stop() {
        try {
            if (this.currentAudio && this.isPlaying) {
                this.currentAudio.pause();
                this.currentAudio.currentTime = 0;
                this.currentAudio = null;
                this.isPlaying = false;
                console.log('Notification sound stopped');
            }
        } catch (error) {
            console.log('Error stopping notification sound:', error);
        }
    }
    
    toggle() {
        this.soundEnabled = !this.soundEnabled;
        if (!this.soundEnabled) {
            this.stop();
        }
        console.log('Sound notifications', this.soundEnabled ? 'enabled' : 'disabled');
        return this.soundEnabled;
    }
    
    handlePlayError(error) {
        // Handle autoplay policy violations
        if (error.name === 'NotAllowedError' || error.message.includes('play() request was interrupted')) {
            console.log('Autoplay prevented. Enabling on next user interaction.');
            this.userInteracted = false;
            this.pendingSound = true;
            this.setupAutoEnable(); // Re-setup if needed
        } else if (error.name === 'AbortError') {
            console.log('Audio play was aborted');
        } else {
            console.log('Audio play error:', error);
            this.handleAudioError();
        }
    }
    
    handleAudioError() {
        console.log('Audio error detected, falling back to system beep');
        // Try system beep as fallback
        try {
            // Create a simple beep sound programmatically
            this.createFallbackBeep();
        } catch (e) {
            console.log('Fallback beep also failed:', e);
        }
    }
    
    createFallbackBeep() {
        try {
            if (this.audioContext) {
                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.1, this.audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);
                
                oscillator.start();
                oscillator.stop(this.audioContext.currentTime + 0.5);
                
                console.log('Fallback beep played');
            }
        } catch (e) {
            console.log('Could not create fallback beep:', e);
        }
    }
    
    showSilentEnablePrompt() {
        // Show a very subtle, non-intrusive prompt
        if (document.querySelector('.sound-enable-hint')) return; // Don't show multiple times
        
        const hint = document.createElement('div');
        hint.className = 'sound-enable-hint';
        hint.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            max-width: 200px;
        `;
        hint.innerHTML = '<i class="fas fa-volume-up"></i> Sound ready';
        
        document.body.appendChild(hint);
        
        // Fade in
        setTimeout(() => {
            hint.style.opacity = '1';
        }, 100);
        
        // Fade out and remove
        setTimeout(() => {
            hint.style.opacity = '0';
            setTimeout(() => {
                if (hint.parentNode) {
                    hint.parentNode.removeChild(hint);
                }
            }, 300);
        }, 2000);
    }
    
    // Public method to check if sound is ready
    isReady() {
        return this.userInteracted && this.soundEnabled && this.audioElement;
    }
    
    // Public method to force enable (for settings)
    forceEnable() {
        this.soundEnabled = true;
        this.userInteracted = true;
        this.enableAudioContext();
        this.prepareAudio();
        return true;
    }
}

// Initialize the sound manager
const notificationSoundManager = new NotificationSoundManager();

// Make it globally available
window.notificationSoundManager = notificationSoundManager;

// Convenience functions for backward compatibility
window.playNotificationSound = () => notificationSoundManager.play();
window.stopNotificationSound = () => notificationSoundManager.stop();

// Auto-enable on common admin actions
if (window.location.pathname.includes('/admin')) {
    // Enable on common admin interactions
    const adminElements = ['button', 'a', 'input', 'select', 'textarea'];
    adminElements.forEach(tag => {
        document.addEventListener('click', function(e) {
            if (e.target.tagName.toLowerCase() === tag) {
                notificationSoundManager.enableAudioContext();
            }
        }, { once: true, passive: true });
    });
    
    // Enable on page visibility change (when user comes back to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            notificationSoundManager.enableAudioContext();
        }
    }, { once: true });
}

console.log('Enhanced Notification Sound Manager loaded');
