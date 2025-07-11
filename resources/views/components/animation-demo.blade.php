{{-- Animation Demo Component --}}
@if(isset($animationsEnabled) && $animationsEnabled)
<div class="animation-demo-container">
    {{-- Welcome Animation --}}
    @if($animationSettings['welcome_animation'] ?? false)
        <div class="welcome-animation" style="position: fixed; top: 20px; right: 20px; z-index: 9999; pointer-events: none;">
            <div class="badge bg-primary animate__animated animate__bounceIn">
                ✨ Welcome! Animations are active
            </div>
        </div>
    @endif
    
    {{-- Animation Test Buttons (only show in debug mode) --}}
    @if(config('app.debug'))
        <div class="animation-test-panel" style="position: fixed; bottom: 20px; left: 20px; z-index: 9998; background: rgba(0,0,0,0.8); color: white; padding: 15px; border-radius: 10px;">
            <h6 style="color: white; margin-bottom: 10px;">🎨 Animation Test Panel</h6>
            <button class="btn btn-sm btn-success mb-2" onclick="testFireworks()">
                🎆 Test Fireworks
            </button>
            <button class="btn btn-sm btn-warning mb-2" onclick="testCrackers()">
                🎉 Test Crackers
            </button>
            <button class="btn btn-sm btn-info mb-2" onclick="testSuccessAnimation()">
                ✅ Test Success
            </button>
            <div style="font-size: 10px; color: #ccc; margin-top: 5px;">
                Debug mode only
            </div>
        </div>
        
        <script>
        function testFireworks() {
            if (window.triggerFireworks) {
                window.triggerFireworks();
            }
        }
        
        function testCrackers() {
            if (window.triggerCrackers) {
                window.triggerCrackers();
            }
        }
        
        function testSuccessAnimation() {
            if (window.showToast) {
                window.showToast('🎉 Animation test successful!', 'success');
            }
        }
        </script>
    @endif
</div>

{{-- Auto-hide welcome message --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const welcomeEl = document.querySelector('.welcome-animation');
    if (welcomeEl) {
        setTimeout(() => {
            welcomeEl.style.opacity = '0';
            welcomeEl.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (welcomeEl.parentNode) {
                    welcomeEl.parentNode.removeChild(welcomeEl);
                }
            }, 500);
        }, 3000);
    }
});
</script>
@endif
