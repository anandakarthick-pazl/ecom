{{-- Enhanced Shared Animations and Styles Component --}}
<style>
    /* =============================================
       ENHANCED SHARED ANIMATIONS & STYLES SYSTEM
       ============================================= */
    
    :root {
        --animation-duration: {{ \App\Models\AppSetting::get('animation_duration', '600') }}ms;
        --animation-intensity: {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') }};
        --primary-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        --success-gradient: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
        --warning-gradient: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        --danger-gradient: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        --info-gradient: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    }
    
    /* Enhanced Page Headers */
    .page-header-enhanced {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 25px 25px;
    }
    
    .page-header-enhanced::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.3;0.1" dur="3s" repeatCount="indefinite"/></circle><circle cx="75" cy="75" r="1.5" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.4;0.1" dur="2s" repeatCount="indefinite"/></circle><circle cx="85" cy="25" r="1" fill="white" opacity="0.1"><animate attributeName="opacity" values="0.1;0.2;0.1" dur="4s" repeatCount="indefinite"/></circle></svg>');
        animation: float 20s linear infinite;
    }
    
    .page-header-enhanced .header-content {
        position: relative;
        z-index: 1;
    }
    
    .page-header-enhanced h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .page-header-enhanced .lead {
        font-size: 1.25rem;
        opacity: 0.9;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }
    
    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-50px, -50px) rotate(360deg); }
    }
    
    /* Enhanced Card System */
    .card-enhanced {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
        position: relative;
    }
    
    .card-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .card-enhanced:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .card-enhanced:hover::before {
        opacity: 1;
    }
    
    /* Enhanced Product Cards */
    .product-card-enhanced {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid #f0f0f0;
        position: relative;
        height: 100%;
    }
    
    .product-card-enhanced:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 25px 70px rgba(0,0,0,0.2);
        border-color: var(--primary-color);
    }
    
    .product-card-enhanced .product-image-enhanced {
        position: relative;
        height: 280px;
        overflow: hidden;
        border-radius: 20px 20px 0 0;
    }
    
    .product-card-enhanced .product-image-enhanced img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .product-card-enhanced:hover .product-image-enhanced img {
        transform: scale(1.15);
    }
    
    .product-card-enhanced .product-badges-enhanced {
        position: absolute;
        top: 15px;
        left: 15px;
        right: 15px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        z-index: 2;
    }
    
    .badge-enhanced {
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
    }
    
    .product-card-enhanced .card-body-enhanced {
        padding: 25px;
        display: flex;
        flex-direction: column;
        height: calc(100% - 280px);
    }
    
    .product-card-enhanced .product-category-enhanced {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        font-weight: 600;
    }
    
    .product-card-enhanced .product-title-enhanced {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin: 0 0 12px 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 48px;
    }
    
    .product-card-enhanced .product-description-enhanced {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }
    
    .product-card-enhanced .price-section-enhanced {
        margin: 15px 0;
        padding: 15px 0;
        border-top: 1px solid #f0f0f0;
    }
    
    .price-current-enhanced {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary-color);
        display: block;
    }
    
    .price-original-enhanced {
        font-size: 16px;
        color: #999;
        text-decoration: line-through;
        margin-left: 8px;
    }
    
    .savings-enhanced {
        font-size: 14px;
        color: #28a745;
        font-weight: 600;
        margin-top: 4px;
    }
    
    /* Enhanced Buttons */
    .btn-enhanced {
        padding: 12px 24px;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .btn-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .btn-enhanced:hover::before {
        left: 100%;
    }
    
    .btn-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .btn-enhanced:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .btn-primary-enhanced {
        background: var(--primary-gradient);
        color: white;
    }
    
    .btn-secondary-enhanced {
        background: var(--secondary-color);
        color: white;
    }
    
    .btn-success-enhanced {
        background: var(--success-gradient);
        color: white;
    }
    
    .btn-outline-enhanced {
        background: transparent;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-enhanced:hover {
        background: var(--primary-color);
        color: white;
    }
    
    /* Enhanced Section Containers */
    .section-enhanced {
        padding: 80px 0;
        position: relative;
    }
    
    .section-enhanced.bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .section-enhanced .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .section-enhanced .section-title {
        font-size: 3rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        position: relative;
    }
    
    .section-enhanced .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary-gradient);
        border-radius: 2px;
    }
    
    .section-enhanced .section-subtitle {
        font-size: 1.25rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Enhanced Features */
    .feature-enhanced {
        text-align: center;
        padding: 40px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transition: all 0.4s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    
    .feature-enhanced:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    
    .feature-enhanced .feature-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .feature-enhanced .feature-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }
    
    .feature-enhanced .feature-description {
        color: #666;
        line-height: 1.6;
    }
    
    /* Enhanced Animations */
    .animate-fade-in {
        animation: enhancedFadeIn 1s ease-out;
        animation-fill-mode: both;
    }
    
    .animate-slide-up {
        animation: enhancedSlideUp 0.8s ease-out;
        animation-fill-mode: both;
    }
    
    .animate-scale-in {
        animation: enhancedScaleIn 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .animate-bounce-in {
        animation: enhancedBounceIn 0.8s ease-out;
        animation-fill-mode: both;
    }
    
    /* Stagger Animation Delays */
    .animate-stagger-1 { animation-delay: 0.1s; }
    .animate-stagger-2 { animation-delay: 0.2s; }
    .animate-stagger-3 { animation-delay: 0.3s; }
    .animate-stagger-4 { animation-delay: 0.4s; }
    .animate-stagger-5 { animation-delay: 0.5s; }
    .animate-stagger-6 { animation-delay: 0.6s; }
    .animate-stagger-7 { animation-delay: 0.7s; }
    .animate-stagger-8 { animation-delay: 0.8s; }
    .animate-stagger-9 { animation-delay: 0.9s; }
    .animate-stagger-10 { animation-delay: 1.0s; }
    .animate-stagger-11 { animation-delay: 1.1s; }
    .animate-stagger-12 { animation-delay: 1.2s; }
    
    @keyframes enhancedFadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes enhancedSlideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes enhancedScaleIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    @keyframes enhancedBounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Enhanced Loading States */
    .loading-enhanced {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px;
    }
    
    .spinner-enhanced {
        width: 60px;
        height: 60px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 30px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 1.25rem;
        color: #666;
        font-weight: 500;
    }
    
    /* Enhanced Quantity Selector */
    .quantity-selector-enhanced {
        display: flex;
        align-items: center;
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        overflow: hidden;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 15px 0;
    }
    
    .quantity-btn-enhanced {
        background: #f8f9fa;
        border: none;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .quantity-btn-enhanced:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }
    
    .quantity-btn-enhanced:active {
        transform: scale(0.95);
    }
    
    .quantity-btn-enhanced:disabled {
        background: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
        transform: none;
    }
    
    .quantity-input-enhanced {
        flex: 1;
        border: none;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        padding: 12px;
        background: white;
        color: #333;
        min-width: 60px;
    }
    
    .quantity-input-enhanced:focus {
        outline: none;
        background: #f8f9fa;
    }
    
    /* Enhanced Badges and Tags */
    .tag-enhanced {
        display: inline-block;
        padding: 8px 16px;
        background: var(--primary-gradient);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 4px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .tag-enhanced:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    /* Enhanced Stats Cards */
    .stats-card-enhanced {
        text-align: center;
        padding: 40px 20px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        margin: 15px;
        backdrop-filter: blur(15px);
        border: 2px solid rgba(255,255,255,0.2);
        transition: all 0.4s ease;
    }
    
    .stats-card-enhanced:hover {
        transform: translateY(-10px);
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.4);
    }
    
    .stats-icon-enhanced {
        font-size: 3.5rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }
    
    .stats-number-enhanced {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .stats-label-enhanced {
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 500;
    }
    
    /* Enhanced Filter Section */
    .filter-section-enhanced {
        background: white;
        border-radius: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 40px;
        border: 1px solid #f0f0f0;
    }
    
    .category-filter-enhanced {
        padding: 12px 24px;
        border: 2px solid #e9ecef;
        border-radius: 30px;
        background: white;
        color: #666;
        transition: all 0.4s ease;
        margin: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    
    .category-filter-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        transition: left 0.4s ease;
        z-index: -1;
    }
    
    .category-filter-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
        color: white;
    }
    
    .category-filter-enhanced:hover::before {
        left: 0;
    }
    
    .category-filter-enhanced.active {
        background: var(--primary-gradient);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--primary-color), 0.3);
    }
    
    /* Enhanced Empty States */
    .empty-state-enhanced {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        margin: 40px 0;
    }
    
    .empty-state-enhanced .empty-icon {
        font-size: 5rem;
        color: #ddd;
        margin-bottom: 30px;
    }
    
    .empty-state-enhanced .empty-title {
        font-size: 2rem;
        font-weight: 700;
        color: #666;
        margin-bottom: 15px;
    }
    
    .empty-state-enhanced .empty-description {
        font-size: 1.1rem;
        color: #999;
        margin-bottom: 30px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header-enhanced {
            padding: 40px 0;
        }
        
        .page-header-enhanced h1 {
            font-size: 2.5rem;
        }
        
        .section-enhanced {
            padding: 60px 0;
        }
        
        .section-enhanced .section-title {
            font-size: 2rem;
        }
        
        .product-card-enhanced .product-image-enhanced {
            height: 220px;
        }
        
        .filter-section-enhanced {
            padding: 20px;
        }
        
        .category-filter-enhanced {
            padding: 8px 16px;
            font-size: 14px;
            margin: 4px;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            font-size: 14px;
        }
        
        .feature-enhanced {
            padding: 30px 15px;
        }
        
        .feature-enhanced .feature-icon {
            font-size: 3rem;
        }
        
        .stats-card-enhanced {
            margin: 10px 0;
            padding: 30px 15px;
        }
        
        .stats-number-enhanced {
            font-size: 2rem;
        }
    }
    
    @media (max-width: 576px) {
        .page-header-enhanced h1 {
            font-size: 2rem;
        }
        
        .section-enhanced .section-title {
            font-size: 1.75rem;
        }
        
        .product-card-enhanced .product-image-enhanced {
            height: 180px;
        }
        
        .category-filter-enhanced {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .quantity-btn-enhanced {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }
        
        .quantity-input-enhanced {
            font-size: 14px;
            padding: 8px;
        }
    }
    
    /* Disable animations for reduced motion preference */
    @media (prefers-reduced-motion: reduce) {
        .card-enhanced,
        .product-card-enhanced,
        .btn-enhanced,
        .feature-enhanced,
        .category-filter-enhanced,
        .quantity-btn-enhanced,
        .tag-enhanced,
        .stats-card-enhanced {
            animation: none !important;
            transition: none !important;
        }
        
        .card-enhanced:hover,
        .product-card-enhanced:hover,
        .feature-enhanced:hover,
        .category-filter-enhanced:hover,
        .btn-enhanced:hover,
        .stats-card-enhanced:hover {
            transform: none !important;
        }
    }
    
    /* Animation disable class */
    .animations-disabled .animate-fade-in,
    .animations-disabled .animate-slide-up,
    .animations-disabled .animate-scale-in,
    .animations-disabled .animate-bounce-in,
    .animations-disabled .card-enhanced,
    .animations-disabled .product-card-enhanced,
    .animations-disabled .btn-enhanced,
    .animations-disabled .feature-enhanced {
        animation: none !important;
        transition: none !important;
    }
    
    .animations-disabled .card-enhanced:hover,
    .animations-disabled .product-card-enhanced:hover,
    .animations-disabled .feature-enhanced:hover {
        transform: none !important;
    }
</style>

{{-- Include Fireworks System from Products Page --}}
<script>
// Enhanced Global Animation Settings
const enhancedAnimationSettings = {
    enabled: {{ \App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true' ? 'true' : 'false' }},
    intensity: {{ \App\Models\AppSetting::get('frontend_animation_intensity', '3') }},
    duration: {{ \App\Models\AppSetting::get('animation_duration', '600') }},
    fireworksEnabled: {{ \App\Models\AppSetting::get('frontend_fireworks_enabled', 'true') === 'true' ? 'true' : 'false' }},
    hoverEffectsEnabled: {{ \App\Models\AppSetting::get('frontend_hover_effects_enabled', 'true') === 'true' ? 'true' : 'false' }},
    welcomeAnimationEnabled: {{ \App\Models\AppSetting::get('frontend_welcome_animation', 'true') === 'true' ? 'true' : 'false' }},
    celebrationEnabled: {{ \App\Models\AppSetting::get('frontend_celebration_enabled', 'true') === 'true' ? 'true' : 'false' }},
    animationStyle: '{{ \App\Models\AppSetting::get('frontend_animation_style', 'crackers') }}',
    respectReducedMotion: {{ \App\Models\AppSetting::get('reduce_motion_respect', 'true') === 'true' ? 'true' : 'false' }}
};

// Check for reduced motion preference
const prefersReducedMotion = enhancedAnimationSettings.respectReducedMotion && 
                             window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const effectiveAnimationsEnabled = enhancedAnimationSettings.enabled && !prefersReducedMotion;

// Apply animation classes to body
document.addEventListener('DOMContentLoaded', function() {
    if (!effectiveAnimationsEnabled) {
        document.body.classList.add('animations-disabled');
    } else {
        document.body.classList.add('animations-enabled');
        document.body.setAttribute('data-animation-style', enhancedAnimationSettings.animationStyle);
        document.body.setAttribute('data-animation-intensity', enhancedAnimationSettings.intensity);
        
        // Set CSS custom properties
        document.documentElement.style.setProperty('--animation-duration', enhancedAnimationSettings.duration + 'ms');
    }
});

// Enhanced Page Loading Animation System
class EnhancedPageAnimations {
    constructor() {
        this.observer = null;
        this.animatedElements = new Set();
        this.init();
    }
    
    init() {
        if (!effectiveAnimationsEnabled) return;
        
        // Initialize Intersection Observer for scroll animations
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.animatedElements.has(entry.target)) {
                    this.animateElement(entry.target);
                    this.animatedElements.add(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        // Observe elements when DOM is ready
        this.observeElements();
        
        // Add entrance animations to immediate visible elements
        setTimeout(() => {
            this.addInitialAnimations();
        }, 100);
    }
    
    observeElements() {
        const elementsToObserve = document.querySelectorAll(
            '.card-enhanced, .product-card-enhanced, .feature-enhanced, ' +
            '.stats-card-enhanced, .section-enhanced, .empty-state-enhanced'
        );
        
        elementsToObserve.forEach(element => {
            this.observer.observe(element);
        });
    }
    
    animateElement(element) {
        const animations = ['animate-fade-in', 'animate-slide-up', 'animate-scale-in'];
        const randomAnimation = animations[Math.floor(Math.random() * animations.length)];
        
        element.classList.add(randomAnimation);
        
        // Add stagger delay based on element position
        const siblings = Array.from(element.parentNode.children);
        const index = siblings.indexOf(element);
        
        if (index > 0) {
            element.classList.add(`animate-stagger-${Math.min(index, 12)}`);
        }
    }
    
    addInitialAnimations() {
        const immediateElements = document.querySelectorAll(
            '.page-header-enhanced, .filter-section-enhanced'
        );
        
        immediateElements.forEach((element, index) => {
            if (!this.animatedElements.has(element)) {
                setTimeout(() => {
                    this.animateElement(element);
                    this.animatedElements.add(element);
                }, index * 100);
            }
        });
    }
    
    destroy() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}

// Enhanced Notification System
class EnhancedNotificationSystem {
    constructor() {
        this.notifications = [];
        this.container = null;
        this.createContainer();
    }
    
    createContainer() {
        this.container = document.createElement('div');
        this.container.id = 'enhanced-notifications';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
    }
    
    show(message, type = 'info', duration = 4000) {
        const notification = this.createNotification(message, type);
        this.container.appendChild(notification);
        this.notifications.push(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            this.remove(notification);
        }, duration);
        
        // Trigger celebration if success
        if (type === 'success' && effectiveAnimationsEnabled && enhancedAnimationSettings.celebrationEnabled) {
            this.triggerCelebration();
        }
    }
    
    createNotification(message, type) {
        const notification = document.createElement('div');
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        notification.style.cssText = `
            background: white;
            color: #333;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            margin-bottom: 12px;
            min-width: 320px;
            max-width: 400px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid ${colors[type] || colors.info};
            pointer-events: auto;
            backdrop-filter: blur(10px);
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="${icons[type] || icons.info}" style="color: ${colors[type] || colors.info}; font-size: 18px;"></i>
                <span style="flex: 1; font-weight: 500;">${message}</span>
                <button onclick="enhancedNotifications.remove(this.parentElement.parentElement)" 
                        style="background: none; border: none; color: #999; cursor: pointer; font-size: 18px; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s ease;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        return notification;
    }
    
    remove(notification) {
        if (notification && notification.parentElement) {
            notification.style.transform = 'translateX(400px)';
            notification.style.opacity = '0';
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                    const index = this.notifications.indexOf(notification);
                    if (index > -1) {
                        this.notifications.splice(index, 1);
                    }
                }
            }, 400);
        }
    }
    
    triggerCelebration() {
        // Create simple celebration effect
        if (typeof window.fireworks !== 'undefined') {
            window.fireworks.createCelebrationBurst();
        } else {
            // Fallback celebration
            this.createSimpleCelebration();
        }
    }
    
    createSimpleCelebration() {
        const colors = ['#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', '#54a0ff'];
        
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                width: 8px;
                height: 8px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%;
                pointer-events: none;
                z-index: 10001;
                animation: celebrate 1.5s ease-out forwards;
            `;
            
            const angle = (360 / 20) * i;
            particle.style.setProperty('--angle', angle + 'deg');
            
            document.body.appendChild(particle);
            
            setTimeout(() => particle.remove(), 1500);
        }
        
        // Add CSS for celebration animation
        if (!document.getElementById('celebration-styles')) {
            const style = document.createElement('style');
            style.id = 'celebration-styles';
            style.textContent = `
                @keyframes celebrate {
                    0% {
                        transform: translate(-50%, -50%) rotate(var(--angle)) translateY(0) scale(1);
                        opacity: 1;
                    }
                    100% {
                        transform: translate(-50%, -50%) rotate(var(--angle)) translateY(-200px) scale(0);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }
}

// Initialize Enhanced Systems
let enhancedPageAnimations;
let enhancedNotifications;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animation system
    enhancedPageAnimations = new EnhancedPageAnimations();
    
    // Initialize notification system
    enhancedNotifications = new EnhancedNotificationSystem();
    
    // Make notification system globally available
    window.enhancedNotifications = enhancedNotifications;
    
    // Enhanced show notification function
    window.showEnhancedNotification = function(message, type = 'info', duration = 4000) {
        enhancedNotifications.show(message, type, duration);
    };
    
    console.log('✅ Enhanced animation and notification systems initialized!');
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (enhancedPageAnimations) {
        enhancedPageAnimations.destroy();
    }
});
</script>
