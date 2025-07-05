{{-- Enhanced Fireworks Animation System Component --}}

{{-- Fireworks Container --}}
<div class="fireworks-container" id="fireworks-container"></div>

<style>
    /* Enhanced Fireworks Animation Styles */
    .fireworks-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
        overflow: hidden;
    }
    
    .firework {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: firework-trail 1.5s ease-out forwards;
        box-shadow: 0 0 15px currentColor;
    }
    
    .firework::before {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: inherit;
        opacity: 0.4;
        filter: blur(3px);
    }
    
    .firework::after {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: white;
        opacity: 0.8;
        animation: glow-pulse 0.4s ease-out;
    }
    
    .spark {
        position: absolute;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        animation: spark-explosion 2s ease-out forwards;
        box-shadow: 0 0 8px currentColor;
    }
    
    .spark::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
        opacity: 0.6;
        animation: spark-glow 0.3s ease-out;
    }
    
    .cracker-burst {
        position: absolute;
        width: 6px;
        height: 6px;
        background: radial-gradient(circle, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        border-radius: 50%;
        animation: cracker-burst 1s ease-out forwards;
        box-shadow: 0 0 20px currentColor;
    }
    
    .cracker-stream {
        position: absolute;
        width: 3px;
        height: 25px;
        background: linear-gradient(to bottom, #ffd700, #ff6b6b, transparent);
        animation: cracker-stream 0.5s ease-out forwards;
        border-radius: 2px;
    }
    
    .celebration-star {
        position: absolute;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 8px solid #ffd700;
        animation: star-twinkle 1.2s ease-out forwards;
        transform: rotate(35deg);
    }
    
    .celebration-star::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 3px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 8px solid #ffd700;
        transform: rotate(-70deg);
    }
    
    .celebration-star::after {
        content: '';
        position: absolute;
        left: -6px;
        top: 3px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 8px solid #ffd700;
        transform: rotate(70deg);
    }
    
    @keyframes firework-trail {
        0% {
            opacity: 1;
            transform: scale(1) translateY(0);
            filter: brightness(1.8);
        }
        70% {
            opacity: 0.9;
            transform: scale(1.3) translateY(-85vh);
        }
        100% {
            opacity: 0;
            transform: scale(0.2) translateY(-105vh);
            filter: brightness(0.3);
        }
    }
    
    @keyframes spark-explosion {
        0% {
            opacity: 1;
            transform: scale(1) translate(0, 0) rotate(0deg);
            filter: brightness(1.8);
        }
        50% {
            opacity: 0.9;
            transform: scale(1.8) translate(calc(var(--spark-x, 60px) * 0.7), calc(var(--spark-y, 60px) * 0.7)) rotate(180deg);
        }
        100% {
            opacity: 0;
            transform: scale(0.1) translate(var(--spark-x, 60px), var(--spark-y, 60px)) rotate(360deg);
            filter: brightness(0.2);
        }
    }
    
    @keyframes cracker-burst {
        0% {
            opacity: 1;
            transform: scale(0) rotate(0deg);
            filter: brightness(2.5);
        }
        30% {
            opacity: 1;
            transform: scale(5) rotate(120deg);
            filter: brightness(2);
        }
        60% {
            opacity: 0.9;
            transform: scale(8) rotate(240deg);
        }
        100% {
            opacity: 0;
            transform: scale(2) rotate(360deg);
            filter: brightness(0.3);
        }
    }
    
    @keyframes cracker-stream {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(40px) scale(0.2);
        }
    }
    
    @keyframes star-twinkle {
        0% {
            opacity: 1;
            transform: rotate(35deg) scale(0);
        }
        50% {
            opacity: 1;
            transform: rotate(35deg) scale(2);
        }
        100% {
            opacity: 0;
            transform: rotate(35deg) scale(0.3);
        }
    }
    
    @keyframes glow-pulse {
        0% {
            opacity: 1;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(2.5);
        }
    }
    
    @keyframes spark-glow {
        0% {
            opacity: 0.8;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(2);
        }
    }
    
    .celebration-burst {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, 
            #ff6b6b 0%, 
            #feca57 15%, 
            #48dbfb 30%, 
            #ff9ff3 45%, 
            #54a0ff 60%,
            #5f27cd 75%,
            #ffd700 100%);
        border-radius: 50%;
        animation: celebration-burst 1.5s ease-out forwards;
        pointer-events: none;
        z-index: 10000;
        box-shadow: 0 0 40px rgba(255, 215, 0, 0.8);
    }
    
    .celebration-burst::before {
        content: '';
        position: absolute;
        top: -15px;
        left: -15px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, 
            transparent 25%,
            rgba(255, 215, 0, 0.4) 35%,
            rgba(255, 107, 107, 0.3) 55%,
            transparent 85%);
        border-radius: 50%;
        animation: celebration-halo 1.5s ease-out forwards;
    }
    
    @keyframes celebration-burst {
        0% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0) rotate(0deg);
            filter: brightness(2.5);
        }
        30% {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.8) rotate(120deg);
            filter: brightness(2.2);
        }
        60% {
            opacity: 0.9;
            transform: translate(-50%, -50%) scale(3.5) rotate(240deg);
            filter: brightness(1.5);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(6) rotate(360deg);
            filter: brightness(0.3);
        }
    }
    
    @keyframes celebration-halo {
        0% {
            opacity: 0;
            transform: scale(0) rotate(-45deg);
        }
        40% {
            opacity: 0.8;
            transform: scale(1.5) rotate(0deg);
        }
        100% {
            opacity: 0;
            transform: scale(3) rotate(45deg);
        }
    }
    
    /* Disable animations for reduced motion preference */
    @media (prefers-reduced-motion: reduce) {
        .fireworks-container,
        .firework,
        .spark,
        .cracker-burst,
        .cracker-stream,
        .celebration-star,
        .celebration-burst {
            display: none !important;
        }
    }
    
    .animations-disabled .fireworks-container,
    .animations-disabled .firework,
    .animations-disabled .spark,
    .animations-disabled .cracker-burst,
    .animations-disabled .cracker-stream,
    .animations-disabled .celebration-star,
    .animations-disabled .celebration-burst {
        display: none !important;
    }
</style>

<script>
// Enhanced Fireworks System
class EnhancedFireworksSystem {
    constructor() {
        this.container = document.getElementById('fireworks-container');
        this.isActive = false;
        this.colors = [
            '#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', 
            '#54a0ff', '#5f27cd', '#00d2d3', '#ff9ff3',
            '#ffa726', '#26c6da', '#ab47bc', '#ef5350',
            '#66bb6a', '#ffd54f', '#ff8a65', '#ba68c8'
        ];
        this.animationSettings = window.enhancedAnimationSettings || {
            enabled: true,
            intensity: 3,
            fireworksEnabled: true,
            celebrationEnabled: true
        };
    }
    
    createFirework(x, y) {
        if (!this.shouldAnimate() || !this.container) return;
        
        const firework = document.createElement('div');
        firework.className = 'firework';
        firework.style.left = x + 'px';
        firework.style.top = y + 'px';
        firework.style.background = this.getRandomColor();
        firework.style.color = firework.style.background;
        
        this.container.appendChild(firework);
        
        // Create trailing sparks during flight
        for (let i = 0; i < 4; i++) {
            setTimeout(() => {
                this.createTrailSpark(x + (Math.random() - 0.5) * 25, y + i * 35);
            }, i * 120);
        }
        
        // Create explosion after delay
        setTimeout(() => {
            this.createExplosion(x, y);
            if (this.container.contains(firework)) {
                this.container.removeChild(firework);
            }
        }, 1500);
    }
    
    createTrailSpark(x, y) {
        if (!this.shouldAnimate() || !this.container) return;
        
        const trail = document.createElement('div');
        trail.className = 'cracker-stream';
        trail.style.left = x + 'px';
        trail.style.top = y + 'px';
        
        this.container.appendChild(trail);
        
        setTimeout(() => {
            if (this.container.contains(trail)) {
                this.container.removeChild(trail);
            }
        }, 500);
    }
    
    createExplosion(x, y) {
        if (!this.shouldAnimate()) return;
        
        const sparkCount = this.animationSettings.intensity * 15;
        const starCount = this.animationSettings.intensity * 5;
        
        // Create main explosion sparks
        for (let i = 0; i < sparkCount; i++) {
            const spark = document.createElement('div');
            spark.className = 'spark';
            
            const angle = (360 / sparkCount) * i + Math.random() * 20;
            const distance = 50 + Math.random() * 150;
            const sparkX = Math.cos(angle * Math.PI / 180) * distance;
            const sparkY = Math.sin(angle * Math.PI / 180) * distance;
            
            spark.style.left = x + 'px';
            spark.style.top = y + 'px';
            const color = this.getRandomColor();
            spark.style.background = color;
            spark.style.color = color;
            spark.style.setProperty('--spark-x', sparkX + 'px');
            spark.style.setProperty('--spark-y', sparkY + 'px');
            
            this.container.appendChild(spark);
            
            setTimeout(() => {
                if (this.container.contains(spark)) {
                    this.container.removeChild(spark);
                }
            }, 2000);
        }
        
        // Create decorative stars
        for (let i = 0; i < starCount; i++) {
            setTimeout(() => {
                const star = document.createElement('div');
                star.className = 'celebration-star';
                
                const starX = x + (Math.random() - 0.5) * 100;
                const starY = y + (Math.random() - 0.5) * 100;
                
                star.style.left = starX + 'px';
                star.style.top = starY + 'px';
                
                this.container.appendChild(star);
                
                setTimeout(() => {
                    if (this.container.contains(star)) {
                        this.container.removeChild(star);
                    }
                }, 1200);
            }, i * 120);
        }
        
        // Create cracker burst effect
        const burst = document.createElement('div');
        burst.className = 'cracker-burst';
        burst.style.left = x + 'px';
        burst.style.top = y + 'px';
        burst.style.color = this.getRandomColor();
        
        this.container.appendChild(burst);
        
        setTimeout(() => {
            if (this.container.contains(burst)) {
                this.container.removeChild(burst);
            }
        }, 1000);
    }
    
    createCelebrationBurst() {
        if (!this.shouldAnimate() || !this.animationSettings.celebrationEnabled) return;
        
        const burst = document.createElement('div');
        burst.className = 'celebration-burst';
        document.body.appendChild(burst);
        
        setTimeout(() => {
            if (document.body.contains(burst)) {
                document.body.removeChild(burst);
            }
        }, 1500);
        
        // Create additional random fireworks for celebration
        for (let i = 0; i < this.animationSettings.intensity; i++) {
            setTimeout(() => {
                const x = Math.random() * window.innerWidth;
                const y = Math.random() * (window.innerHeight * 0.6) + (window.innerHeight * 0.2);
                this.createFirework(x, window.innerHeight + 100);
            }, i * 200);
        }
    }
    
    startRandomFireworks() {
        if (!this.shouldAnimate()) return;
        
        this.isActive = true;
        const fireworkInterval = Math.max(300, 1200 / this.animationSettings.intensity);
        
        const interval = setInterval(() => {
            if (!this.isActive) {
                clearInterval(interval);
                return;
            }
            
            const burstCount = Math.max(1, Math.floor(this.animationSettings.intensity / 2));
            
            for (let i = 0; i < burstCount; i++) {
                setTimeout(() => {
                    const x = Math.random() * window.innerWidth;
                    const startY = window.innerHeight + 100;
                    
                    this.createFirework(x, startY);
                }, i * 150);
            }
        }, fireworkInterval);
        
        // Stop after duration based on intensity
        const duration = 4000 + (this.animationSettings.intensity * 1500);
        setTimeout(() => {
            this.isActive = false;
        }, duration);
    }
    
    triggerOnAction(element) {
        if (!this.shouldAnimate()) return;
        
        const rect = element.getBoundingClientRect();
        const x = rect.left + rect.width / 2;
        const y = rect.top + rect.height / 2;
        
        // Create immediate explosion at element
        this.createExplosion(x, y);
        
        // Add some random fireworks nearby with staggered timing
        for (let i = 0; i < this.animationSettings.intensity; i++) {
            setTimeout(() => {
                const offsetX = x + (Math.random() - 0.5) * 400;
                const offsetY = y + (Math.random() - 0.5) * 300;
                this.createFirework(offsetX, Math.max(100, offsetY - 150));
            }, i * 250);
        }
        
        // Add celebration burst for high-intensity animations
        if (this.animationSettings.intensity >= 3) {
            setTimeout(() => {
                this.createCelebrationBurst();
            }, 600);
        }
    }
    
    triggerWelcomeFireworks() {
        if (!this.shouldAnimate()) return;
        
        // Create a spectacular welcome display
        setTimeout(() => {
            this.startRandomFireworks();
        }, 500);
        
        // Add celebration burst
        setTimeout(() => {
            this.createCelebrationBurst();
        }, 1500);
    }
    
    shouldAnimate() {
        return this.animationSettings.enabled && 
               this.animationSettings.fireworksEnabled && 
               !document.body.classList.contains('animations-disabled');
    }
    
    getRandomColor() {
        return this.colors[Math.floor(Math.random() * this.colors.length)];
    }
    
    stop() {
        this.isActive = false;
    }
    
    destroy() {
        this.stop();
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// Initialize Enhanced Fireworks System
let enhancedFireworks;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize fireworks system
    enhancedFireworks = new EnhancedFireworksSystem();
    
    // Make fireworks available globally
    window.enhancedFireworks = enhancedFireworks;
    window.fireworks = enhancedFireworks; // Backward compatibility
    
    // Enhanced functions for backward compatibility
    window.triggerFireworks = function(element) {
        if (enhancedFireworks) {
            enhancedFireworks.triggerOnAction(element);
        }
    };
    
    window.startFireworks = function() {
        if (enhancedFireworks) {
            enhancedFireworks.startRandomFireworks();
        }
    };
    
    window.triggerCelebration = function() {
        if (enhancedFireworks) {
            enhancedFireworks.createCelebrationBurst();
        }
    };
    
    console.log('🎆 Enhanced Fireworks System initialized!');
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (enhancedFireworks) {
        enhancedFireworks.destroy();
    }
});
</script>
