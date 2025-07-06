<?php $__env->startSection('title', 'All Products - ' . ($globalCompany->company_name ?? 'Your Store')); ?>
<?php $__env->startSection('meta_description', 'Browse all our products. Find what you need from our complete product catalog.'); ?>

<?php $__env->startSection('content'); ?>
<style>
    /* Enhanced Compact Product Design */
    .products-container {
        position: relative;
        overflow: hidden;
    }
    
    .product-grid-enhanced {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        padding: 15px 0;
    }
    
    .product-card-compact {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        border: 1px solid #f0f0f0;
    }
    
    .product-card-compact:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: var(--primary-color, #007bff);
    }
    
    .product-image-container {
        position: relative;
        height: 120px;
        overflow: hidden;
        border-radius: 12px 12px 0 0;
    }
    
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    
    .product-card-compact:hover .product-image {
        transform: scale(1.1);
    }
    
    .product-badges {
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        z-index: 2;
    }
    
    .badge-compact {
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    .product-content {
        padding: 8px;
    }
    
    .product-category {
        font-size: 9px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    
    .product-title-compact {
        font-size: 12px;
        font-weight: 600;
        color: #333;
        margin: 0 0 4px 0;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 29px;
    }
    
    .product-price-section {
        margin: 4px 0;
    }
    
    .price-current {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-color, #007bff);
    }
    
    .price-original {
        font-size: 10px;
        color: #999;
        text-decoration: line-through;
        margin-left: 4px;
    }
    
    .product-actions-compact {
        display: flex;
        gap: 4px;
        margin-top: 6px;
    }
    
    .btn-compact {
        padding: 4px 8px;
        font-size: 10px;
        border-radius: 4px;
        flex: 1;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .stock-indicator {
        font-size: 9px;
        padding: 1px 4px;
        border-radius: 6px;
        margin: 2px 0;
    }
    
    .stock-low {
        background: #fff3cd;
        color: #856404;
    }
    
    .stock-out {
        background: #f8d7da;
        color: #721c24;
    }
    
    /* Enhanced Cracker/Fireworks Animation Styles */
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
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: firework-trail 1.2s ease-out forwards;
        box-shadow: 0 0 10px currentColor;
    }
    
    .firework::before {
        content: '';
        position: absolute;
        top: -3px;
        left: -3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: inherit;
        opacity: 0.4;
        filter: blur(2px);
    }
    
    .firework::after {
        content: '';
        position: absolute;
        top: -1px;
        left: -1px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
        opacity: 0.8;
        animation: glow-pulse 0.3s ease-out;
    }
    
    .spark {
        position: absolute;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        animation: spark-explosion 1.5s ease-out forwards;
        box-shadow: 0 0 6px currentColor;
    }
    
    .spark::before {
        content: '';
        position: absolute;
        top: -1px;
        left: -1px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: white;
        opacity: 0.6;
        animation: spark-glow 0.2s ease-out;
    }
    
    .cracker-burst {
        position: absolute;
        width: 4px;
        height: 4px;
        background: radial-gradient(circle, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        border-radius: 50%;
        animation: cracker-burst 0.8s ease-out forwards;
        box-shadow: 0 0 15px currentColor;
    }
    
    .cracker-stream {
        position: absolute;
        width: 2px;
        height: 20px;
        background: linear-gradient(to bottom, #ffd700, #ff6b6b, transparent);
        animation: cracker-stream 0.4s ease-out forwards;
        border-radius: 1px;
    }
    
    .celebration-star {
        position: absolute;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 7px solid #ffd700;
        animation: star-twinkle 1s ease-out forwards;
        transform: rotate(35deg);
    }
    
    .celebration-star::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 3px;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 7px solid #ffd700;
        transform: rotate(-70deg);
    }
    
    .celebration-star::after {
        content: '';
        position: absolute;
        left: -5px;
        top: 3px;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 7px solid #ffd700;
        transform: rotate(70deg);
    }
    
    @keyframes firework-trail {
        0% {
            opacity: 1;
            transform: scale(1) translateY(0);
            filter: brightness(1.5);
        }
        70% {
            opacity: 0.8;
            transform: scale(1.2) translateY(-80vh);
        }
        100% {
            opacity: 0;
            transform: scale(0.3) translateY(-100vh);
            filter: brightness(0.5);
        }
    }
    
    @keyframes spark-explosion {
        0% {
            opacity: 1;
            transform: scale(1) translate(0, 0) rotate(0deg);
            filter: brightness(1.5);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.5) translate(calc(var(--spark-x, 50px) * 0.7), calc(var(--spark-y, 50px) * 0.7)) rotate(180deg);
        }
        100% {
            opacity: 0;
            transform: scale(0.2) translate(var(--spark-x, 50px), var(--spark-y, 50px)) rotate(360deg);
            filter: brightness(0.3);
        }
    }
    
    @keyframes cracker-burst {
        0% {
            opacity: 1;
            transform: scale(0) rotate(0deg);
            filter: brightness(2);
        }
        30% {
            opacity: 1;
            transform: scale(4) rotate(120deg);
            filter: brightness(1.5);
        }
        60% {
            opacity: 0.8;
            transform: scale(6) rotate(240deg);
        }
        100% {
            opacity: 0;
            transform: scale(1) rotate(360deg);
            filter: brightness(0.5);
        }
    }
    
    @keyframes cracker-stream {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(30px) scale(0.3);
        }
    }
    
    @keyframes star-twinkle {
        0% {
            opacity: 1;
            transform: rotate(35deg) scale(0);
        }
        50% {
            opacity: 1;
            transform: rotate(35deg) scale(1.5);
        }
        100% {
            opacity: 0;
            transform: rotate(35deg) scale(0.5);
        }
    }
    
    @keyframes glow-pulse {
        0% {
            opacity: 1;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(2);
        }
    }
    
    @keyframes spark-glow {
        0% {
            opacity: 0.8;
            transform: scale(1);
        }
        100% {
            opacity: 0;
            transform: scale(1.5);
        }
    }
    
    .celebration-burst {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, 
            #ff6b6b 0%, 
            #feca57 20%, 
            #48dbfb 40%, 
            #ff9ff3 60%, 
            #54a0ff 80%,
            #ffd700 100%);
        border-radius: 50%;
        animation: celebration-burst 1.2s ease-out forwards;
        pointer-events: none;
        z-index: 10000;
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.6);
    }
    
    .celebration-burst::before {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        width: 140px;
        height: 140px;
        background: radial-gradient(circle, 
            transparent 30%,
            rgba(255, 215, 0, 0.3) 40%,
            rgba(255, 107, 107, 0.2) 60%,
            transparent 80%);
        border-radius: 50%;
        animation: celebration-halo 1.2s ease-out forwards;
    }
    
    @keyframes celebration-burst {
        0% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0) rotate(0deg);
            filter: brightness(2);
        }
        30% {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.5) rotate(120deg);
            filter: brightness(1.8);
        }
        60% {
            opacity: 0.8;
            transform: translate(-50%, -50%) scale(3) rotate(240deg);
            filter: brightness(1.2);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(5) rotate(360deg);
            filter: brightness(0.5);
        }
    }
    
    @keyframes celebration-halo {
        0% {
            opacity: 0;
            transform: scale(0) rotate(-45deg);
        }
        40% {
            opacity: 0.6;
            transform: scale(1.2) rotate(0deg);
        }
        100% {
            opacity: 0;
            transform: scale(2) rotate(45deg);
        }
    }
    
    /* Enhanced Header Styles */
    .page-header-enhanced {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
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
    
    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(-50px, -50px) rotate(360deg); }
    }
    
    .header-content {
        position: relative;
        z-index: 1;
    }
    
    .filter-section-enhanced {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .category-filter-enhanced {
        padding: 8px 16px;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        background: white;
        color: #666;
        transition: all 0.3s ease;
        margin: 4px;
        font-weight: 500;
    }
    
    .category-filter-enhanced:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: var(--primary-color, #007bff);
    }
    
    .category-filter-enhanced.active {
        background: var(--primary-color, #007bff);
        border-color: var(--primary-color, #007bff);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }
    
    /* Stats Section */
    .stats-section {
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 40px 0;
        margin-top: 50px;
        position: relative;
        overflow: hidden;
    }
    
    .stats-card-enhanced {
        text-align: center;
        padding: 20px;
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        margin: 10px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    /* Loading Animation */
    .loading-enhanced {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
    }
    
    .spinner-enhanced {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color, #007bff);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .product-grid-enhanced {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
        }
        
        .product-image-container {
            height: 100px;
        }
        
        .product-content {
            padding: 6px;
        }
        
        .page-header-enhanced {
            padding: 20px 0;
        }
        
        .filter-section-enhanced {
            padding: 15px;
        }
    }
    
    @media (max-width: 576px) {
        .product-grid-enhanced {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 6px;
        }
        
        .product-image-container {
            height: 80px;
        }
        
        .product-content {
            padding: 4px;
        }
        
        .category-filter-enhanced {
            padding: 6px 12px;
            font-size: 12px;
        }
    }
    
    /* Animation Toggle */
    .animations-disabled .fireworks-container,
    .animations-disabled .cracker-burst,
    .animations-disabled .celebration-burst {
        display: none !important;
    }
</style>

<div class="container-fluid px-0">
    <!-- Enhanced Page Header -->
    <div class="page-header-enhanced">
        <div class="container">
            <div class="header-content text-center">
                <h1 class="display-4 mb-3 fw-bold">
                    <i class="fas fa-sparkles me-3"></i>
                    Discover Amazing Products
                </h1>
                <p class="lead">Find exactly what you're looking for in our curated collection</p>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Enhanced Categories Filter -->
        <?php if($categories->count() > 0): ?>
        <div class="filter-section-enhanced">
            <h6 class="mb-3 fw-bold text-dark">
                <i class="fas fa-filter me-2"></i>Filter by Category
            </h6>
            <div class="d-flex flex-wrap justify-content-center" id="category-filters">
                <button class="btn category-filter-enhanced <?php echo e(request('category', 'all') === 'all' ? 'active' : ''); ?>" data-category="all">
                    <i class="fas fa-th-large me-1"></i>All Products
                </button>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="btn category-filter-enhanced <?php echo e(request('category') === $category->slug ? 'active' : ''); ?>" data-category="<?php echo e($category->slug); ?>">
                    <i class="fas fa-tag me-1"></i><?php echo e($category->name); ?>

                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Loading Spinner -->
        <div class="loading-enhanced" id="loading-spinner" style="display: none;">
            <div class="spinner-enhanced"></div>
            <p class="text-muted">Loading amazing products...</p>
        </div>

        <!-- Products Grid Container -->
        <div class="products-container" id="products-container">
            <?php echo $__env->make('partials.products-grid-enhanced', ['products' => $products], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Pagination -->
        <?php if($enablePagination && isset($products) && method_exists($products, 'appends')): ?>
        <div class="d-flex justify-content-center mt-5" id="pagination-container">
            <?php echo e($products->appends(request()->query())->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Enhanced Product Stats Section -->
<?php if($products->count() > 0): ?>
<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card-enhanced">
                    <div class="stats-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stats-number"><?php echo e($enablePagination && method_exists($products, 'total') ? $products->total() : $products->count()); ?></div>
                    <div class="stats-label">Amazing Products</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card-enhanced">
                    <div class="stats-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stats-number"><?php echo e($categories->count()); ?></div>
                    <div class="stats-label">Categories</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card-enhanced">
                    <div class="stats-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <div class="stats-number">Free</div>
                    <div class="stats-label">Shipping on ₹500+</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Fireworks Container for Animations -->
<div class="fireworks-container" id="fireworks-container"></div>

<script>
// Enhanced Products JavaScript with Cracker Animations
(function() {
    // Animation settings from backend
    const animationsEnabled = <?php echo e(\App\Models\AppSetting::get('frontend_animations_enabled', 'true') === 'true' ? 'true' : 'false'); ?>;
    const animationIntensity = <?php echo e(\App\Models\AppSetting::get('frontend_animation_intensity', '3')); ?>;
    const showCelebration = <?php echo e(\App\Models\AppSetting::get('frontend_celebration_enabled', 'true') === 'true' ? 'true' : 'false'); ?>;
            const fireworksEnabled = <?php echo e(\App\Models\AppSetting::get('frontend_fireworks_enabled', 'true') === 'true' ? 'true' : 'false'); ?>;
            const hoverEffectsEnabled = <?php echo e(\App\Models\AppSetting::get('frontend_hover_effects_enabled', 'true') === 'true' ? 'true' : 'false'); ?>;
            const welcomeAnimationEnabled = <?php echo e(\App\Models\AppSetting::get('frontend_welcome_animation', 'true') === 'true' ? 'true' : 'false'); ?>;
            const animationStyle = '<?php echo e(\App\Models\AppSetting::get('frontend_animation_style', 'crackers')); ?>';
            const animationDuration = <?php echo e(\App\Models\AppSetting::get('animation_duration', '600')); ?>;
            const respectReducedMotion = <?php echo e(\App\Models\AppSetting::get('reduce_motion_respect', 'true') === 'true' ? 'true' : 'false'); ?>;
            
            // Check for reduced motion preference
            const prefersReducedMotion = respectReducedMotion && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const effectiveAnimationsEnabled = animationsEnabled && !prefersReducedMotion;
    
    // Apply animation classes to body
    if (!effectiveAnimationsEnabled) {
    document.body.classList.add('animations-disabled');
    } else {
                document.body.classList.add('animations-enabled');
                document.body.setAttribute('data-animation-style', animationStyle);
                document.body.setAttribute('data-animation-intensity', animationIntensity);
                
                // Set CSS custom properties for animation duration
                document.documentElement.style.setProperty('--animation-duration', animationDuration + 'ms');
            }
    
    // Fireworks Animation System
    class FireworksSystem {
        constructor() {
            this.container = document.getElementById('fireworks-container');
            this.isActive = false;
            this.colors = [
                '#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', 
                '#54a0ff', '#5f27cd', '#00d2d3', '#ff9ff3',
                '#ffa726', '#26c6da', '#ab47bc', '#ef5350'
            ];
        }
        
        createFirework(x, y) {
            if (!effectiveAnimationsEnabled || !fireworksEnabled || !this.container) return;
            
            const firework = document.createElement('div');
            firework.className = 'firework';
            firework.style.left = x + 'px';
            firework.style.top = y + 'px';
            firework.style.background = this.colors[Math.floor(Math.random() * this.colors.length)];
            firework.style.color = firework.style.background; // For box-shadow
            
            this.container.appendChild(firework);
            
            // Create trailing sparks during flight
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    this.createTrailSpark(x + (Math.random() - 0.5) * 20, y + i * 30);
                }, i * 100);
            }
            
            // Create explosion after delay
            setTimeout(() => {
                this.createExplosion(x, y);
                if (this.container.contains(firework)) {
                    this.container.removeChild(firework);
                }
            }, 1200);
        }
        
        createTrailSpark(x, y) {
            if (!animationsEnabled || !this.container) return;
            
            const trail = document.createElement('div');
            trail.className = 'cracker-stream';
            trail.style.left = x + 'px';
            trail.style.top = y + 'px';
            
            this.container.appendChild(trail);
            
            setTimeout(() => {
                if (this.container.contains(trail)) {
                    this.container.removeChild(trail);
                }
            }, 400);
        }
        
        createExplosion(x, y) {
            if (!effectiveAnimationsEnabled || !fireworksEnabled) return;
            
            const sparkCount = animationIntensity * 12;
            const starCount = animationIntensity * 4;
            
            // Create main explosion sparks
            for (let i = 0; i < sparkCount; i++) {
                const spark = document.createElement('div');
                spark.className = 'spark';
                
                const angle = (360 / sparkCount) * i + Math.random() * 15;
                const distance = 40 + Math.random() * 120;
                const sparkX = Math.cos(angle * Math.PI / 180) * distance;
                const sparkY = Math.sin(angle * Math.PI / 180) * distance;
                
                spark.style.left = x + 'px';
                spark.style.top = y + 'px';
                const color = this.colors[Math.floor(Math.random() * this.colors.length)];
                spark.style.background = color;
                spark.style.color = color; // For box-shadow
                spark.style.setProperty('--spark-x', sparkX + 'px');
                spark.style.setProperty('--spark-y', sparkY + 'px');
                
                this.container.appendChild(spark);
                
                setTimeout(() => {
                    if (this.container.contains(spark)) {
                        this.container.removeChild(spark);
                    }
                }, 1500);
            }
            
            // Create decorative stars
            for (let i = 0; i < starCount; i++) {
                setTimeout(() => {
                    const star = document.createElement('div');
                    star.className = 'celebration-star';
                    
                    const starX = x + (Math.random() - 0.5) * 80;
                    const starY = y + (Math.random() - 0.5) * 80;
                    
                    star.style.left = starX + 'px';
                    star.style.top = starY + 'px';
                    
                    this.container.appendChild(star);
                    
                    setTimeout(() => {
                        if (this.container.contains(star)) {
                            this.container.removeChild(star);
                        }
                    }, 1000);
                }, i * 100);
            }
            
            // Create cracker burst effect
            const burst = document.createElement('div');
            burst.className = 'cracker-burst';
            burst.style.left = x + 'px';
            burst.style.top = y + 'px';
            burst.style.color = this.colors[Math.floor(Math.random() * this.colors.length)];
            
            this.container.appendChild(burst);
            
            setTimeout(() => {
                if (this.container.contains(burst)) {
                    this.container.removeChild(burst);
                }
            }, 800);
        }
        
        createCelebrationBurst() {
            if (!effectiveAnimationsEnabled || !showCelebration) return;
            
            const burst = document.createElement('div');
            burst.className = 'celebration-burst';
            document.body.appendChild(burst);
            
            setTimeout(() => {
                document.body.removeChild(burst);
            }, 1000);
        }
        
        startRandomFireworks() {
            if (!effectiveAnimationsEnabled || !fireworksEnabled) return;
            
            this.isActive = true;
            const fireworkInterval = Math.max(500, 1500 / animationIntensity);
            
            const interval = setInterval(() => {
                if (!this.isActive) {
                    clearInterval(interval);
                    return;
                }
                
                // Create multiple fireworks for higher intensity
                const burstCount = Math.max(1, Math.floor(animationIntensity / 2));
                
                for (let i = 0; i < burstCount; i++) {
                    setTimeout(() => {
                        const x = Math.random() * window.innerWidth;
                        const startY = window.innerHeight + 50;
                        const endY = Math.random() * (window.innerHeight * 0.4) + (window.innerHeight * 0.1);
                        
                        this.createFirework(x, startY);
                    }, i * 100);
                }
            }, fireworkInterval);
            
            // Stop after duration based on intensity
            const duration = 3000 + (animationIntensity * 1000);
            setTimeout(() => {
                this.isActive = false;
            }, duration);
        }
        
        triggerOnAction(element) {
            if (!effectiveAnimationsEnabled) return;
            
            const rect = element.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            
            // Create immediate explosion at element
            this.createExplosion(x, y);
            
            // Add some random fireworks nearby with staggered timing
            for (let i = 0; i < animationIntensity; i++) {
                setTimeout(() => {
                    const offsetX = x + (Math.random() - 0.5) * 300;
                    const offsetY = y + (Math.random() - 0.5) * 200;
                    this.createFirework(offsetX, Math.max(50, offsetY - 100));
                }, i * 200);
            }
            
            // Add celebration burst for high-intensity animations
            if (animationIntensity >= 3) {
                setTimeout(() => {
                    this.createCelebrationBurst();
                }, 500);
            }
        }
    }
    
    // Initialize Fireworks System
    const fireworks = new FireworksSystem();
    
    // Make fireworks available globally for the enhanced grid
    window.fireworks = fireworks;
    
    // Product Filtering and Loading System
    function initializeProductFilters() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded. Product filters cannot be initialized.');
            return;
        }
        
        const urlParams = new URLSearchParams(window.location.search);
        let currentCategory = urlParams.get('category') || 'all';
        let currentPage = parseInt(urlParams.get('page')) || 1;
        let isLoading = false;
        let enablePagination = <?php echo e($enablePagination ? 'true' : 'false'); ?>;
        
        // Set initial active filter
        $('.category-filter-enhanced').removeClass('active');
        $('.category-filter-enhanced[data-category="' + currentCategory + '"]').addClass('active');
        
        // Category filter click with fireworks
        $('.category-filter-enhanced').on('click', function(e) {
            e.preventDefault();
            
            if (isLoading) return;
            
            const category = $(this).data('category');
            if (category === currentCategory) return;
            
            // Trigger fireworks on category change
            fireworks.triggerOnAction(this);
            
            currentCategory = category;
            currentPage = 1;
            
            $('.category-filter-enhanced').removeClass('active');
            $(this).addClass('active');
            
            loadProducts(category, 1);
        });
        
        // Pagination click
        $(document).on('click', '#pagination-container .pagination a', function(e) {
            e.preventDefault();
            
            if (isLoading || !enablePagination) return;
            
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page');
            
            if (page && page !== currentPage.toString()) {
                currentPage = parseInt(page);
                loadProducts(currentCategory, page);
            }
        });
        
        function loadProducts(category, page) {
            if (isLoading) return;
            
            isLoading = true;
            
            $('#loading-spinner').show();
            $('#products-container').hide();
            if (enablePagination) {
                $('#pagination-container').hide();
            }
            
            let requestData = {};
            if (category && category !== 'all') {
                requestData.category = category;
            }
            if (enablePagination && page && page > 1) {
                requestData.page = page;
            }
            
            $.ajax({
                url: '<?php echo e(route("products")); ?>',
                method: 'GET',
                data: requestData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.html) {
                        $('#products-container').html(response.html).show();
                        
                        if (enablePagination && response.pagination) {
                            $('#pagination-container').html(response.pagination).show();
                        } else if (enablePagination) {
                            $('#pagination-container').hide();
                        }
                        
                        // Trigger fireworks when products load
                        if (effectiveAnimationsEnabled && fireworksEnabled) {
                            setTimeout(() => {
                                fireworks.startRandomFireworks();
                            }, 500);
                        }
                        
                        // Update page title
                        let title = 'All Products';
                        if (category !== 'all') {
                            const categoryName = $('.category-filter-enhanced[data-category="' + category + '"]').text().trim();
                            title = categoryName.replace(/.*\s/, '') + ' Products';
                        }
                        document.title = title + ' - <?php echo e($globalCompany->company_name ?? "Your Store"); ?>';
                        
                        // Update URL
                        let newUrl = '<?php echo e(route("products")); ?>';
                        let params = [];
                        if (category && category !== 'all') {
                            params.push('category=' + encodeURIComponent(category));
                        }
                        if (enablePagination && page && page > 1) {
                            params.push('page=' + page);
                        }
                        if (params.length > 0) {
                            newUrl += '?' + params.join('&');
                        }
                        
                        window.history.pushState({ category, page }, '', newUrl);
                    }
                    
                    isLoading = false;
                },
                error: function(xhr) {
                    $('#products-container').html(
                        '<div class="alert alert-danger text-center">Error loading products. Please try again.</div>'
                    ).show();
                    
                    isLoading = false;
                },
                complete: function() {
                    $('#loading-spinner').hide();
                }
            });
        }
        
        // Handle browser back/forward
        window.addEventListener('popstate', function(e) {
            const state = e.state || {};
            const urlParams = new URLSearchParams(window.location.search);
            const category = state.category || urlParams.get('category') || 'all';
            const page = state.page || parseInt(urlParams.get('page')) || 1;
            
            currentCategory = category;
            currentPage = page;
            
            $('.category-filter-enhanced').removeClass('active');
            $('.category-filter-enhanced[data-category="' + category + '"]').addClass('active');
            
            loadProducts(category, page);
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeProductFilters, 100);
            
            // Start welcome fireworks after page load
            if (effectiveAnimationsEnabled && welcomeAnimationEnabled) {
                setTimeout(() => {
                    fireworks.startRandomFireworks();
                }, 1000);
            }
        });
    } else {
        setTimeout(initializeProductFilters, 100);
        
        // Start welcome fireworks
        if (effectiveAnimationsEnabled && welcomeAnimationEnabled) {
            setTimeout(() => {
                fireworks.startRandomFireworks();
            }, 1000);
        }
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/products.blade.php ENDPATH**/ ?>