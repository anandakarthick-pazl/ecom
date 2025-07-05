@extends('super-admin.layouts.app')

@section('title', 'Theme Preview')

@section('content')
<div class="theme-preview-container">
    <!-- Header -->
    <div class="preview-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0">Preview: {{ $theme->name }}</h4>
                    <small class="text-muted">Company: {{ $company->name }}</small>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.close()">
                        <i class="fas fa-times me-1"></i>Close Preview
                    </button>
                    <button class="btn btn-sm btn-primary" id="applyTheme">
                        <i class="fas fa-check me-1"></i>Apply This Theme
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Preview Frame -->
    <div class="preview-frame">
        <div class="device-selector">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-secondary active device-btn" data-device="desktop">
                    <i class="fas fa-desktop"></i> Desktop
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary device-btn" data-device="tablet">
                    <i class="fas fa-tablet-alt"></i> Tablet
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary device-btn" data-device="mobile">
                    <i class="fas fa-mobile-alt"></i> Mobile
                </button>
            </div>
        </div>

        <div class="preview-wrapper">
            <div class="preview-content" id="previewContent">
                <!-- Theme Preview Content -->
                <div class="theme-{{ $theme->slug }} demo-store">
                    
                    <!-- Navigation -->
                    <nav class="navbar navbar-expand-lg">
                        <div class="container">
                            <a class="navbar-brand" href="#">
                                <strong>{{ $company->name }}</strong>
                            </a>
                            <button class="navbar-toggler" type="button">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="navbar-nav ms-auto">
                                <a class="nav-link" href="#">Home</a>
                                <a class="nav-link" href="#">Products</a>
                                <a class="nav-link" href="#">About</a>
                                <a class="nav-link" href="#">Contact</a>
                                <a class="nav-link" href="#">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="badge bg-primary">3</span>
                                </a>
                            </div>
                        </div>
                    </nav>

                    <!-- Hero Section -->
                    <section class="hero-section">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <h1 class="display-4 fw-bold">Welcome to {{ $company->name }}</h1>
                                    <p class="lead">Discover our amazing collection of premium products designed just for you.</p>
                                    <div class="hero-buttons">
                                        <button class="btn btn-primary btn-lg me-3">Shop Now</button>
                                        <button class="btn btn-outline-primary btn-lg">Learn More</button>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="hero-image">
                                        <img src="https://via.placeholder.com/600x400/{{ str_replace('#', '', $theme->primary_color) }}/ffffff?text=Hero+Image" 
                                             alt="Hero Image" class="img-fluid rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Products Grid -->
                    <section class="products-section py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 text-center mb-5">
                                    <h2 class="section-title">Featured Products</h2>
                                    <p class="text-muted">Check out our most popular items</p>
                                </div>
                            </div>
                            <div class="row theme-grid">
                                @for($i = 1; $i <= 6; $i++)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <img src="https://via.placeholder.com/300x200/{{ str_replace('#', '', $theme->secondary_color) }}/ffffff?text=Product+{{ $i }}" 
                                                 alt="Product {{ $i }}" class="img-fluid">
                                            <div class="product-overlay">
                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <h5 class="product-title">Sample Product {{ $i }}</h5>
                                            <p class="product-description">Beautiful product description goes here</p>
                                            <div class="product-price">
                                                <span class="price">${{ rand(19, 99) }}.99</span>
                                                <span class="old-price">${{ rand(100, 150) }}.99</span>
                                            </div>
                                            <div class="product-rating">
                                                @for($j = 1; $j <= 5; $j++)
                                                    <i class="fas fa-star {{ $j <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="rating-count">({{ rand(10, 100) }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </section>

                    <!-- Features Section -->
                    <section class="features-section py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 text-center mb-5">
                                    <h2 class="section-title">Why Choose Us</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-icon">
                                        <i class="fas fa-shipping-fast fa-3x"></i>
                                    </div>
                                    <h5>Free Shipping</h5>
                                    <p class="text-muted">Free shipping on orders over $50</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-icon">
                                        <i class="fas fa-undo-alt fa-3x"></i>
                                    </div>
                                    <h5>Easy Returns</h5>
                                    <p class="text-muted">30-day return policy</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-icon">
                                        <i class="fas fa-headset fa-3x"></i>
                                    </div>
                                    <h5>24/7 Support</h5>
                                    <p class="text-muted">Round-the-clock customer support</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <div class="feature-icon">
                                        <i class="fas fa-shield-alt fa-3x"></i>
                                    </div>
                                    <h5>Secure Payment</h5>
                                    <p class="text-muted">100% secure transactions</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <footer class="footer py-5">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5>{{ $company->name }}</h5>
                                    <p class="text-muted">Your trusted partner for quality products and exceptional service.</p>
                                </div>
                                <div class="col-md-2">
                                    <h6>Shop</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="#">New Arrivals</a></li>
                                        <li><a href="#">Best Sellers</a></li>
                                        <li><a href="#">Sale</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-2">
                                    <h6>Support</h6>
                                    <ul class="list-unstyled">
                                        <li><a href="#">Help Center</a></li>
                                        <li><a href="#">Contact Us</a></li>
                                        <li><a href="#">FAQ</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6>Newsletter</h6>
                                    <p class="text-muted">Subscribe to get updates on new products and offers</p>
                                    <div class="input-group">
                                        <input type="email" class="form-control" placeholder="Your email">
                                        <button class="btn btn-primary">Subscribe</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Theme Colors Panel -->
<div class="theme-colors-panel">
    <h6>Theme Colors</h6>
    <div class="color-swatches">
        <div class="color-swatch" title="Primary Color">
            <div class="color-circle" style="background-color: {{ $theme->primary_color }}"></div>
            <small>Primary</small>
        </div>
        <div class="color-swatch" title="Secondary Color">
            <div class="color-circle" style="background-color: {{ $theme->secondary_color }}"></div>
            <small>Secondary</small>
        </div>
        <div class="color-swatch" title="Accent Color">
            <div class="color-circle" style="background-color: {{ $theme->accent_color }}"></div>
            <small>Accent</small>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/themes/modern-ecom-themes.css') }}" rel="stylesheet">
<style>
.theme-preview-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.preview-header {
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.preview-frame {
    flex: 1;
    background: #f8f9fa;
    position: relative;
    overflow: hidden;
}

.device-selector {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 25px;
    padding: 5px;
    backdrop-filter: blur(10px);
}

.device-btn.active {
    background: #007bff;
    color: white;
}

.preview-wrapper {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 80px 20px 20px;
}

.preview-content {
    width: 100%;
    max-width: 1200px;
    height: 100%;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: auto;
    transition: all 0.3s ease;
}

.preview-content.tablet {
    max-width: 768px;
}

.preview-content.mobile {
    max-width: 375px;
}

.demo-store {
    min-height: 100%;
}

.section-title {
    position: relative;
    display: inline-block;
    padding-bottom: 10px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background: currentColor;
}

.product-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.product-info {
    padding: 20px;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.product-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.product-price {
    margin-bottom: 10px;
}

.price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #007bff;
}

.old-price {
    text-decoration: line-through;
    color: #999;
    margin-left: 10px;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-count {
    color: #666;
    font-size: 0.9rem;
}

.feature-icon {
    margin-bottom: 20px;
    color: #007bff;
}

.footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.footer a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #007bff;
}

.footer ul {
    padding: 0;
}

.footer ul li {
    margin-bottom: 8px;
}

.theme-colors-panel {
    position: fixed;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    z-index: 1000;
}

.color-swatches {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.color-swatch {
    display: flex;
    align-items: center;
    gap: 10px;
}

.color-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid rgba(0,0,0,0.1);
}

.hero-buttons {
    margin-top: 30px;
}

.hero-image {
    text-align: center;
}

.hero-image img {
    max-width: 100%;
    height: auto;
}

@media (max-width: 768px) {
    .preview-wrapper {
        padding: 60px 10px 10px;
    }
    
    .theme-colors-panel {
        position: relative;
        right: auto;
        transform: none;
        margin: 20px;
    }
    
    .color-swatches {
        flex-direction: row;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Device switching
    $('.device-btn').on('click', function() {
        $('.device-btn').removeClass('active');
        $(this).addClass('active');
        
        const device = $(this).data('device');
        const $content = $('#previewContent');
        
        $content.removeClass('tablet mobile');
        if (device !== 'desktop') {
            $content.addClass(device);
        }
    });
    
    // Apply theme
    $('#applyTheme').on('click', function() {
        const companyId = {{ $company->id }};
        const themeId = {{ $theme->id }};
        
        $.ajax({
            url: `/super-admin/theme-assignments/companies/${companyId}/assign`,
            method: 'POST',
            data: {
                theme_id: themeId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Theme applied successfully!');
                    window.close();
                } else {
                    alert('Error applying theme: ' + response.message);
                }
            },
            error: function() {
                alert('Error applying theme. Please try again.');
            }
        });
    });
    
    // Add smooth scrolling for preview
    $('.preview-content').on('scroll', function() {
        const scrollTop = $(this).scrollTop();
        const navbar = $(this).find('.navbar');
        
        if (scrollTop > 50) {
            navbar.addClass('scrolled');
        } else {
            navbar.removeClass('scrolled');
        }
    });
});
</script>
@endpush
