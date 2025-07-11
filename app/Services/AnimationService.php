<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class AnimationService
{
    /**
     * Get all animation settings for the current tenant
     *
     * @return array
     */
    public static function getAnimationSettings()
    {
        $tenantId = static::getCurrentTenantId();
        $cacheKey = "animation_settings:{$tenantId}";
        
        return Cache::remember($cacheKey, 3600, function () {
            return [
                'enabled' => static::getSetting('frontend_animations_enabled', 'true') === 'true',
                'intensity' => (int) static::getSetting('frontend_animation_intensity', '3'),
                'style' => static::getSetting('frontend_animation_style', 'crackers'),
                'celebration_enabled' => static::getSetting('frontend_celebration_enabled', 'true') === 'true',
                'fireworks_enabled' => static::getSetting('frontend_fireworks_enabled', 'true') === 'true',
                'hover_effects_enabled' => static::getSetting('frontend_hover_effects_enabled', 'true') === 'true',
                'loading_animations' => static::getSetting('frontend_loading_animations', 'true') === 'true',
                'page_transitions' => static::getSetting('frontend_page_transitions', 'true') === 'true',
                'welcome_animation' => static::getSetting('frontend_welcome_animation', 'true') === 'true',
                'animation_duration' => (int) static::getSetting('animation_duration', '600'),
                'reduce_motion_respect' => static::getSetting('reduce_motion_respect', 'true') === 'true',
            ];
        });
    }

    /**
     * Check if animations are enabled for the current tenant
     *
     * @return bool
     */
    public static function areAnimationsEnabled()
    {
        $settings = static::getAnimationSettings();
        return $settings['enabled'];
    }

    /**
     * Get animation CSS classes based on current settings
     *
     * @return string
     */
    public static function getAnimationClasses()
    {
        $settings = static::getAnimationSettings();
        
        if (!$settings['enabled']) {
            return 'animations-disabled';
        }

        $classes = ['animations-enabled'];
        
        // Add intensity class
        $classes[] = 'animation-intensity-' . $settings['intensity'];
        
        // Add style class
        $classes[] = 'animation-style-' . $settings['style'];
        
        // Add feature-specific classes
        if ($settings['hover_effects_enabled']) {
            $classes[] = 'hover-effects-enabled';
        }
        
        if ($settings['page_transitions']) {
            $classes[] = 'page-transitions-enabled';
        }
        
        if ($settings['loading_animations']) {
            $classes[] = 'loading-animations-enabled';
        }
        
        if ($settings['reduce_motion_respect']) {
            $classes[] = 'respect-reduced-motion';
        }

        return implode(' ', $classes);
    }

    /**
     * Generate animation CSS for the current tenant
     *
     * @return string
     */
    public static function generateAnimationCSS()
    {
        $settings = static::getAnimationSettings();
        
        if (!$settings['enabled']) {
            return static::getDisabledAnimationCSS();
        }

        $duration = $settings['animation_duration'];
        $intensity = $settings['intensity'];
        
        $css = "
        /* Animation Settings - Generated dynamically */
        :root {
            --animation-duration: {$duration}ms;
            --animation-intensity: {$intensity};
            --animation-enabled: 1;
        }

        /* Respect user's motion preferences */
        @media (prefers-reduced-motion: reduce) {
            .respect-reduced-motion * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        ";

        // Add base animations
        $css .= static::getBaseAnimations($settings);
        
        // Add hover effects
        if ($settings['hover_effects_enabled']) {
            $css .= static::getHoverAnimations($settings);
        }
        
        // Add page transitions
        if ($settings['page_transitions']) {
            $css .= static::getPageTransitions($settings);
        }
        
        // Add loading animations
        if ($settings['loading_animations']) {
            $css .= static::getLoadingAnimations($settings);
        }
        
        // Add celebration animations
        if ($settings['celebration_enabled'] || $settings['fireworks_enabled']) {
            $css .= static::getCelebrationAnimations($settings);
        }

        return $css;
    }

    /**
     * Generate animation JavaScript for the current tenant
     *
     * @return string
     */
    public static function generateAnimationJS()
    {
        $settings = static::getAnimationSettings();
        
        if (!$settings['enabled']) {
            return '// Animations disabled';
        }

        $jsSettings = json_encode($settings);
        
        return "
        // Animation System - Generated dynamically
        window.animationSettings = {$jsSettings};
        
        // Initialize animations when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeAnimationSystem();
        });

        function initializeAnimationSystem() {
            // Add animation classes to body
            document.body.className += ' " . static::getAnimationClasses() . "';
            
            // Initialize welcome animation
            if (window.animationSettings.welcome_animation) {
                initializeWelcomeAnimation();
            }
            
            // Initialize celebration system
            if (window.animationSettings.celebration_enabled) {
                initializeCelebrationSystem();
            }
            
            // Initialize page transitions
            if (window.animationSettings.page_transitions) {
                initializePageTransitions();
            }
        }
        
        " . static::getAnimationJavaScript($settings);
    }

    /**
     * Get current tenant ID
     *
     * @return mixed
     */
    private static function getCurrentTenantId()
    {
        if (app()->has('current_tenant')) {
            return app('current_tenant')->id;
        } elseif (request()->has('current_company_id')) {
            return request()->get('current_company_id');
        } elseif (session()->has('selected_company_id')) {
            return session('selected_company_id');
        } elseif (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }
        
        return null;
    }

    /**
     * Get setting value for current tenant
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private static function getSetting($key, $default = null)
    {
        return AppSetting::get($key, $default);
    }

    /**
     * CSS for disabled animations
     *
     * @return string
     */
    private static function getDisabledAnimationCSS()
    {
        return "
        /* Animations Disabled */
        :root {
            --animation-duration: 0ms;
            --animation-intensity: 0;
            --animation-enabled: 0;
        }
        
        * {
            animation: none !important;
            transition: none !important;
        }
        ";
    }

    /**
     * Base animation CSS
     *
     * @param array $settings
     * @return string
     */
    private static function getBaseAnimations($settings)
    {
        $duration = $settings['animation_duration'];
        
        return "
        /* Base Animations */
        .fade-in {
            animation: fadeIn {$duration}ms ease-in-out;
        }
        
        .slide-in-up {
            animation: slideInUp {$duration}ms ease-out;
        }
        
        .slide-in-down {
            animation: slideInDown {$duration}ms ease-out;
        }
        
        .scale-in {
            animation: scaleIn {$duration}ms ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes slideInDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        ";
    }

    /**
     * Hover animation CSS
     *
     * @param array $settings
     * @return string
     */
    private static function getHoverAnimations($settings)
    {
        $duration = $settings['animation_duration'];
        
        return "
        /* Hover Effects */
        .hover-effects-enabled .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all {$duration}ms ease;
        }
        
        .hover-effects-enabled .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all {$duration}ms ease;
        }
        
        .hover-effects-enabled .product-item:hover {
            transform: scale(1.02);
            transition: all {$duration}ms ease;
        }
        
        .hover-effects-enabled a:hover {
            transition: all {$duration}ms ease;
        }
        ";
    }

    /**
     * Page transition CSS
     *
     * @param array $settings
     * @return string
     */
    private static function getPageTransitions($settings)
    {
        $duration = $settings['animation_duration'];
        
        return "
        /* Page Transitions */
        .page-transition {
            opacity: 0;
            transform: translateY(20px);
            transition: all {$duration}ms ease;
        }
        
        .page-transition.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        .page-enter {
            animation: pageEnter {$duration}ms ease-out;
        }
        
        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        ";
    }

    /**
     * Loading animation CSS
     *
     * @param array $settings
     * @return string
     */
    private static function getLoadingAnimations($settings)
    {
        return "
        /* Loading Animations */
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        ";
    }

    /**
     * Celebration animation CSS
     *
     * @param array $settings
     * @return string
     */
    private static function getCelebrationAnimations($settings)
    {
        return "
        /* Celebration Animations */
        .celebration-firework {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
        }
        
        .celebration-cracker {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            animation: crackerBurst 1s ease-out forwards;
        }
        
        @keyframes crackerBurst {
            0% {
                transform: scale(0) rotate(0deg);
                opacity: 1;
            }
            50% {
                transform: scale(1.2) rotate(180deg);
                opacity: 0.8;
            }
            100% {
                transform: scale(0.8) rotate(360deg);
                opacity: 0;
            }
        }
        
        .success-animation {
            animation: successPulse 0.6s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        ";
    }

    /**
     * Animation JavaScript functions
     *
     * @param array $settings
     * @return string
     */
    private static function getAnimationJavaScript($settings)
    {
        return "
        function initializeWelcomeAnimation() {
            const mainContent = document.querySelector('main, .main-content, .container');
            if (mainContent) {
                mainContent.classList.add('page-enter');
            }
        }
        
        function initializeCelebrationSystem() {
            // Add celebration triggers for success events
            const successElements = document.querySelectorAll('.alert-success, .success-message');
            successElements.forEach(el => {
                el.classList.add('success-animation');
                if (window.animationSettings.fireworks_enabled) {
                    triggerFireworks();
                }
            });
        }
        
        function initializePageTransitions() {
            // Add transition effects to page elements
            const elements = document.querySelectorAll('.card, .product-item, .content-section');
            elements.forEach((el, index) => {
                el.classList.add('page-transition');
                setTimeout(() => {
                    el.classList.add('loaded');
                }, index * 100);
            });
        }
        
        function triggerFireworks() {
            if (!window.animationSettings.fireworks_enabled) return;
            
            for (let i = 0; i < 5; i++) {
                setTimeout(() => {
                    createFirework();
                }, i * 200);
            }
        }
        
        function createFirework() {
            const firework = document.createElement('div');
            firework.className = 'celebration-firework';
            firework.innerHTML = '✨';
            firework.style.left = Math.random() * 100 + '%';
            firework.style.top = Math.random() * 100 + '%';
            firework.style.fontSize = (Math.random() * 20 + 15) + 'px';
            firework.style.color = getRandomColor();
            
            document.body.appendChild(firework);
            
            setTimeout(() => {
                if (firework.parentNode) {
                    firework.parentNode.removeChild(firework);
                }
            }, 1000);
        }
        
        function triggerCrackers() {
            if (!window.animationSettings.celebration_enabled) return;
            
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    createCracker();
                }, i * 150);
            }
        }
        
        function createCracker() {
            const cracker = document.createElement('div');
            cracker.className = 'celebration-cracker';
            cracker.innerHTML = '🎉';
            cracker.style.left = Math.random() * 100 + '%';
            cracker.style.top = Math.random() * 100 + '%';
            cracker.style.fontSize = (Math.random() * 15 + 20) + 'px';
            
            document.body.appendChild(cracker);
            
            setTimeout(() => {
                if (cracker.parentNode) {
                    cracker.parentNode.removeChild(cracker);
                }
            }, 1000);
        }
        
        function getRandomColor() {
            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#f0932b', '#eb4d4b', '#6c5ce7'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
        
        // Export functions for global use
        window.triggerFireworks = triggerFireworks;
        window.triggerCrackers = triggerCrackers;
        ";
    }

    /**
     * Clear animation cache
     */
    public static function clearCache()
    {
        $tenantId = static::getCurrentTenantId();
        Cache::forget("animation_settings:{$tenantId}");
    }
}
