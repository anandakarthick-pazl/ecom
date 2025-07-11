<?php

namespace App\Helpers;

use App\Services\AnimationService;

class AnimationHelper
{
    /**
     * Generate inline JavaScript to trigger celebration animations
     *
     * @param string $type
     * @return string
     */
    public static function triggerCelebration($type = 'crackers')
    {
        if (!AnimationService::areAnimationsEnabled()) {
            return '';
        }

        $settings = AnimationService::getAnimationSettings();
        
        if (!$settings['celebration_enabled'] && !$settings['fireworks_enabled']) {
            return '';
        }

        switch ($type) {
            case 'fireworks':
                if ($settings['fireworks_enabled']) {
                    return 'setTimeout(() => { if(window.triggerFireworks) window.triggerFireworks(); }, 100);';
                }
                break;
                
            case 'crackers':
            default:
                if ($settings['celebration_enabled']) {
                    return 'setTimeout(() => { if(window.triggerCrackers) window.triggerCrackers(); }, 100);';
                }
                break;
        }

        return '';
    }

    /**
     * Generate animation classes for an element
     *
     * @param string $animation
     * @param array $options
     * @return string
     */
    public static function animationClass($animation = 'fade-in', $options = [])
    {
        if (!AnimationService::areAnimationsEnabled()) {
            return '';
        }

        $classes = [$animation];
        
        if (isset($options['delay'])) {
            $classes[] = 'animation-delay-' . $options['delay'];
        }

        if (isset($options['duration'])) {
            $classes[] = 'animation-duration-' . $options['duration'];
        }

        return implode(' ', $classes);
    }

    /**
     * Check if animations are enabled and return HTML attribute string
     *
     * @param string $animation
     * @param array $options
     * @return string
     */
    public static function animationAttributes($animation = 'fade-in', $options = [])
    {
        if (!AnimationService::areAnimationsEnabled()) {
            return '';
        }

        $attributes = [];
        $attributes['class'] = static::animationClass($animation, $options);
        
        if (isset($options['trigger'])) {
            $attributes['data-animation-trigger'] = $options['trigger'];
        }

        $result = [];
        foreach ($attributes as $key => $value) {
            if ($value) {
                $result[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
            }
        }

        return implode(' ', $result);
    }

    /**
     * Generate JavaScript to initialize page animations
     *
     * @return string
     */
    public static function initializePageAnimations()
    {
        if (!AnimationService::areAnimationsEnabled()) {
            return '';
        }

        return "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize page animations
            const animatedElements = document.querySelectorAll('.fade-in, .slide-in-up, .slide-in-down, .scale-in');
            animatedElements.forEach((el, index) => {
                el.style.opacity = '0';
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.classList.add('animated');
                }, index * 100 + 200);
            });
        });
        </script>
        ";
    }

    /**
     * Generate success animation trigger for forms
     *
     * @return string
     */
    public static function successAnimationScript()
    {
        if (!AnimationService::areAnimationsEnabled()) {
            return '';
        }

        return "
        <script>
        // Success animation for form submissions
        function triggerSuccessAnimation() {
            " . static::triggerCelebration('crackers') . "
            
            // Add success animation class to relevant elements
            const successElements = document.querySelectorAll('.alert-success, .success-message, .btn-success');
            successElements.forEach(el => {
                el.classList.add('success-animation');
            });
        }
        
        // Auto-trigger on page load if there's a success message
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('.alert-success')) {
                triggerSuccessAnimation();
            }
        });
        </script>
        ";
    }

    /**
     * Create a Blade directive for animations
     *
     * @return array
     */
    public static function getBladeDirectives()
    {
        return [
            'animate' => function ($expression) {
                return "<?php echo App\\Helpers\\AnimationHelper::animationAttributes({$expression}); ?>";
            },
            'animateClass' => function ($expression) {
                return "<?php echo App\\Helpers\\AnimationHelper::animationClass({$expression}); ?>";
            },
            'triggerCelebration' => function ($expression = "'crackers'") {
                return "<?php echo App\\Helpers\\AnimationHelper::triggerCelebration({$expression}); ?>";
            },
            'animationScript' => function () {
                return "<?php echo App\\Helpers\\AnimationHelper::initializePageAnimations(); ?>";
            }
        ];
    }
}
