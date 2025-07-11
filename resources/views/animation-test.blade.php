@extends('layouts.app')

@section('title', 'Animation Test - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Animation Status Card --}}
            <div class="card shadow-lg mb-4" @animate('fade-in')>
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-magic me-2"></i>
                        Animation System Status
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    @if($animationsEnabled ?? false)
                                        <i class="fas fa-check-circle text-success fs-2"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fs-2"></i>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-1">Animation Status</h5>
                                    <p class="mb-0 text-muted">
                                        {{ ($animationsEnabled ?? false) ? 'Enabled' : 'Disabled' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($animationsEnabled ?? false)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="fas fa-sliders-h text-info fs-2"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Animation Style</h5>
                                    <p class="mb-0 text-muted">
                                        {{ ucfirst($animationSettings['style'] ?? 'modern') }} 
                                        (Intensity: {{ $animationSettings['intensity'] ?? 3 }}/5)
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($animationsEnabled ?? false)
                    <hr class="my-4">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="text-center">
                                <i class="fas fa-mouse-pointer text-primary fs-4 mb-2"></i>
                                <div class="fw-bold">Hover Effects</div>
                                <small class="text-muted">
                                    {{ ($animationSettings['hover_effects_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="text-center">
                                <i class="fas fa-arrows-alt text-success fs-4 mb-2"></i>
                                <div class="fw-bold">Page Transitions</div>
                                <small class="text-muted">
                                    {{ ($animationSettings['page_transitions'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="text-center">
                                <i class="fas fa-spinner text-warning fs-4 mb-2"></i>
                                <div class="fw-bold">Loading Animations</div>
                                <small class="text-muted">
                                    {{ ($animationSettings['loading_animations'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="text-center">
                                <i class="fas fa-fire text-danger fs-4 mb-2"></i>
                                <div class="fw-bold">Celebrations</div>
                                <small class="text-muted">
                                    {{ ($animationSettings['celebration_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($animationsEnabled ?? false)
            {{-- Animation Test Cards --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 hover-lift" @animate('slide-in-up', ['delay' => '200'])>
                        <div class="card-body text-center">
                            <i class="fas fa-rocket text-primary fs-1 mb-3"></i>
                            <h5>Page Load Animation</h5>
                            <p class="text-muted">Elements slide in smoothly when the page loads</p>
                            <button class="btn btn-outline-primary" onclick="location.reload()">
                                <i class="fas fa-redo me-2"></i>Reload Page
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 hover-lift" @animate('slide-in-up', ['delay' => '400'])>
                        <div class="card-body text-center">
                            <i class="fas fa-hand-pointer text-success fs-1 mb-3"></i>
                            <h5>Hover Effects</h5>
                            <p class="text-muted">Cards and buttons respond to mouse interaction</p>
                            <button class="btn btn-outline-success">
                                <i class="fas fa-mouse me-2"></i>Hover Me!
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 hover-lift" @animate('slide-in-up', ['delay' => '600'])>
                        <div class="card-body text-center">
                            <i class="fas fa-star text-warning fs-1 mb-3"></i>
                            <h5>Success Celebrations</h5>
                            <p class="text-muted">Trigger festive animations for positive actions</p>
                            <button class="btn btn-warning" onclick="triggerTestCelebration()">
                                <i class="fas fa-party-horn me-2"></i>Celebrate!
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100 hover-lift" @animate('slide-in-up', ['delay' => '800'])>
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart text-info fs-1 mb-3"></i>
                            <h5>Cart Interactions</h5>
                            <p class="text-muted">Smooth animations when adding items to cart</p>
                            <button class="btn btn-info" onclick="testCartAnimation()">
                                <i class="fas fa-plus me-2"></i>Test Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            {{-- Animation Disabled Message --}}
            <div class="card border-warning">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning fs-1 mb-4"></i>
                    <h4>Animations are Disabled</h4>
                    <p class="text-muted mb-4">
                        Frontend animations are currently disabled in your admin settings. 
                        Enable them to see the animation system in action.
                    </p>
                    <a href="/admin/settings" class="btn btn-warning">
                        <i class="fas fa-cog me-2"></i>Go to Admin Settings
                    </a>
                </div>
            </div>
            @endif

            {{-- Admin Settings Link --}}
            <div class="text-center mt-4">
                <a href="/admin/settings" class="btn btn-outline-primary">
                    <i class="fas fa-cog me-2"></i>
                    Configure Animation Settings
                </a>
            </div>
        </div>
    </div>
</div>

@if($animationsEnabled ?? false)
<script>
function triggerTestCelebration() {
    if (window.triggerCrackers) {
        window.triggerCrackers();
    }
    if (window.showToast) {
        window.showToast('🎉 Celebration animation triggered!', 'success');
    }
}

function testCartAnimation() {
    if (window.showToast) {
        window.showToast('Item added to cart successfully!', 'success');
    }
    
    // Trigger cart count animation
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.style.transform = 'scale(1.2)';
        cartCount.style.transition = 'transform 0.3s ease';
        setTimeout(() => {
            cartCount.style.transform = 'scale(1)';
        }, 300);
    }
}

// Add some demo animations on load
document.addEventListener('DOMContentLoaded', function() {
    // Animate feature icons
    const icons = document.querySelectorAll('.feature-icon i');
    icons.forEach((icon, index) => {
        setTimeout(() => {
            icon.style.transform = 'scale(1.1)';
            icon.style.transition = 'transform 0.3s ease';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 300);
        }, index * 200 + 1000);
    });
});
</script>
@endif
@endsection
