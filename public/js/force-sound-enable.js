// Force Sound Enable Script for Admin Panel
(function() {
    'use strict';
    
    let soundEnabled = false;
    
    function enableSoundImmediately() {
        if (soundEnabled) return;
        
        try {
            // Try multiple methods to enable sound
            console.log('Attempting to enable sound automatically...');
            
            // Method 1: Force enable via sound manager
            if (window.notificationSoundManager) {
                window.notificationSoundManager.forceEnable();
                console.log('Sound enabled via notification manager');
                soundEnabled = true;
            }
            
            // Method 2: Try to create and play silent audio
            const audio = new Audio();
            audio.volume = 0;
            audio.play().then(() => {
                audio.pause();
                console.log('Sound unlocked via silent audio');
                soundEnabled = true;
            }).catch(() => {
                console.log('Silent audio method failed');
            });
            
            // Method 3: Try Web Audio API
            if (window.AudioContext || window.webkitAudioContext) {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('AudioContext resumed');
                        soundEnabled = true;
                    });
                }
            }
            
        } catch (error) {
            console.log('Sound enable attempt failed:', error);
        }
    }
    
    // Enable sound on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enableSoundImmediately);
    } else {
        enableSoundImmediately();
    }
    
    // Enable on any user interaction
    const enableOnInteraction = function() {
        enableSoundImmediately();
        // Remove listeners after first interaction
        document.removeEventListener('click', enableOnInteraction);
        document.removeEventListener('keydown', enableOnInteraction);
        document.removeEventListener('scroll', enableOnInteraction, { passive: true });
        document.removeEventListener('mousedown', enableOnInteraction);
        document.removeEventListener('touchstart', enableOnInteraction, { passive: true });
    };
    
    document.addEventListener('click', enableOnInteraction);
    document.addEventListener('keydown', enableOnInteraction);
    document.addEventListener('scroll', enableOnInteraction, { passive: true });
    document.addEventListener('mousedown', enableOnInteraction);
    document.addEventListener('touchstart', enableOnInteraction, { passive: true });
    
    // Enable when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            enableSoundImmediately();
        }
    });
    
    // Enable on window focus
    window.addEventListener('focus', enableSoundImmediately);
    
    // Try periodic enabling (last resort)
    let attempts = 0;
    const periodicEnable = setInterval(() => {
        attempts++;
        enableSoundImmediately();
        
        if (soundEnabled || attempts > 10) {
            clearInterval(periodicEnable);
        }
    }, 2000);
    
    console.log('Force sound enable script loaded');
})();
