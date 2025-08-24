{{-- Modern Premium Footer --}}
<footer class="modern-footer">
    {{-- Gradient Wave Background --}}
    <div class="footer-wave">
        <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
            <path d="M0,40 C150,80 350,20 600,50 C850,80 1050,20 1290,40 L1440,30 L1440,100 L0,100 Z" fill="url(#footerGradient)"/>
            <defs>
                <linearGradient id="footerGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>
    </div>
    
    {{-- Main Footer Content --}}
    <div class="footer-main">
        <div class="container">
            <div class="row">
                {{-- Company Info Section --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <div class="footer-brand">
                            @if($globalCompany->company_logo)
                                <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                     alt="{{ $globalCompany->company_name }}" 
                                     class="footer-logo">
                            @else
                                <div class="footer-logo-placeholder">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                            <h3 class="footer-company-name">{{ $globalCompany->company_name ?? 'Your Store' }}</h3>
                        </div>
                        
                        <p class="footer-description">
                            {{ $globalCompany->company_description ?? 'Your premium destination for quality products. We deliver excellence with every purchase.' }}
                        </p>
                        
                        {{-- Social Media Links --}}
                        <div class="footer-social">
                            <h4 class="social-title">Connect With Us</h4>
                            <div class="social-links">
                                @php
                                    $socialLinks = \App\Models\SocialMediaLink::where('is_active', true)
                                        ->orderBy('sort_order')
                                        ->get();
                                @endphp
                                
                                @forelse($socialLinks as $social)
                                    <a href="{{ $social->url }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="social-link"
                                       data-tooltip="{{ $social->platform }}">
                                        <i class="{{ $social->icon_class }}"></i>
                                    </a>
                                @empty
                                    <a href="#" class="social-link" data-tooltip="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="social-link" data-tooltip="Twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="social-link" data-tooltip="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="social-link" data-tooltip="LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Quick Links --}}
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3 class="footer-title">
                            <span class="title-icon"><i class="fas fa-link"></i></span>
                            Quick Links
                        </h3>
                        <ul class="footer-links">
                            <li>
                                <a href="{{ route('shop') }}" class="footer-link">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Home</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('products') }}" class="footer-link">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>All Products</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('offer.products') }}" class="footer-link">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Special Offers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('track.order') }}" class="footer-link">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Track Order</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cart.index') }}" class="footer-link">
                                    <i class="fas fa-chevron-right"></i>
                                    <span>Shopping Cart</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                {{-- Categories --}}
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3 class="footer-title">
                            <span class="title-icon"><i class="fas fa-th-large"></i></span>
                            Categories
                        </h3>
                        <ul class="footer-links">
                            @php
                                $footerCategories = \App\Models\Category::active()
                                    ->parent()
                                    ->orderBy('sort_order')
                                    ->limit(6)
                                    ->get();
                            @endphp
                            @foreach($footerCategories as $category)
                                <li>
                                    <a href="{{ route('category', $category->slug) }}" class="footer-link">
                                        <i class="fas fa-chevron-right"></i>
                                        <span>{{ $category->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                {{-- Contact Info --}}
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h3 class="footer-title">
                            <span class="title-icon"><i class="fas fa-envelope"></i></span>
                            Get In Touch
                        </h3>
                        
                        <div class="contact-info">
                            @if($globalCompany->company_address ?? null)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Our Location</h5>
                                    <p>{{ $globalCompany->company_address }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($globalCompany->company_phone ?? null)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Phone Number</h5>
                                    <a href="tel:{{ $globalCompany->company_phone }}">{{ $globalCompany->company_phone }}</a>
                                </div>
                            </div>
                            @endif
                            
                            @if($globalCompany->company_email ?? null)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Email Address</h5>
                                    <a href="mailto:{{ $globalCompany->company_email }}">{{ $globalCompany->company_email }}</a>
                                </div>
                            </div>
                            @endif
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-details">
                                    <h5>Business Hours</h5>
                                    <p>Mon - Sat: 9:00 AM - 8:00 PM</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Newsletter Subscription --}}
                        <div class="newsletter-box">
                            <h4 class="newsletter-title">
                                <i class="fas fa-paper-plane"></i>
                                Subscribe to Newsletter
                            </h4>
                            <p class="newsletter-text">Get exclusive offers and updates!</p>
                            <form class="newsletter-form">
                                <div class="input-group">
                                    <input type="email" 
                                           class="newsletter-input" 
                                           placeholder="Your email address"
                                           required>
                                    <button type="submit" class="newsletter-btn">
                                        Subscribe
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Footer Bottom --}}
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="copyright">
                        <p>&copy; {{ date('Y') }} <strong>{{ $globalCompany->company_name ?? 'Your Store' }}</strong>. All rights reserved.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="footer-bottom-links">
                        <a href="#">Privacy Policy</a>
                        <span class="separator">•</span>
                        <a href="#">Terms of Service</a>
                        <span class="separator">•</span>
                        <a href="#">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Back to Top Button --}}
    {{-- <button class="back-to-top" id="backToTop" onclick="scrollToTop()">
        <i class="fas fa-chevron-up"></i>
    </button> --}}
</footer>

{{-- Modern Footer Styles --}}
<style>
/* Modern Footer Styles */
.modern-footer {
    position: relative;
    margin-top: 80px;
    overflow: hidden;
}

/* Wave Background */
.footer-wave {
    position: absolute;
    top: -99px;
    left: 0;
    width: 100%;
    height: 100px;
    z-index: 1;
}

.footer-wave svg {
    width: 100%;
    height: 100%;
}

/* Main Footer */
.footer-main {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0 40px;
    position: relative;
    z-index: 2;
}

.footer-widget {
    margin-bottom: 30px;
}

/* Footer Brand */
.footer-brand {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.footer-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    background: white;
    padding: 8px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.footer-logo-placeholder {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #667eea;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.footer-company-name {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.footer-description {
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 14px;
}

/* Social Media */
.footer-social {
    margin-top: 30px;
}

.social-title {
    font-size: 14px;
    font-weight: 600;
    color: white;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.social-links {
    display: flex;
    gap: 10px;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.social-link::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: white;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
}

.social-link:hover {
    transform: translateY(-3px);
    border-color: white;
}

.social-link:hover::before {
    width: 100%;
    height: 100%;
}

.social-link:hover i {
    color: #667eea;
    position: relative;
    z-index: 1;
}

.social-link[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.social-link:hover[data-tooltip]::after {
    opacity: 1;
}

/* Footer Titles */
.footer-title {
    font-size: 18px;
    font-weight: 600;
    color: white;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0));
    border-radius: 3px;
}

.title-icon {
    width: 30px;
    height: 30px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

/* Footer Links */
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    position: relative;
}

.footer-link i {
    font-size: 10px;
    transition: all 0.3s ease;
}

.footer-link:hover {
    color: white;
    transform: translateX(5px);
}

.footer-link:hover i {
    transform: translateX(3px);
}

/* Contact Info */
.contact-info {
    margin-top: 20px;
}

.contact-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.contact-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.contact-details h5 {
    margin: 0 0 5px 0;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contact-details p,
.contact-details a {
    margin: 0;
    color: white;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.contact-details a:hover {
    color: rgba(255, 255, 255, 0.8);
}

/* Newsletter */
.newsletter-box {
    margin-top: 30px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.newsletter-title {
    font-size: 16px;
    font-weight: 600;
    color: white;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.newsletter-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 13px;
    margin-bottom: 15px;
}

.newsletter-form .input-group {
    display: flex;
    gap: 10px;
}

.newsletter-input {
    flex: 1;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 10px;
    font-size: 14px;
    color: #333;
    transition: all 0.3s ease;
}

.newsletter-input:focus {
    outline: none;
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
}

.newsletter-input::placeholder {
    color: #999;
}

.newsletter-btn {
    padding: 12px 25px;
    background: white;
    color: #667eea;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.newsletter-btn:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Footer Bottom */
.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 20px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.copyright p {
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.copyright strong {
    color: white;
}

.footer-bottom-links {
    text-align: right;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

.footer-bottom-links a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.footer-bottom-links a:hover {
    color: white;
}

.footer-bottom-links .separator {
    color: rgba(255, 255, 255, 0.4);
    font-size: 12px;
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
    z-index: 1000;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-main {
        padding: 60px 0 30px;
    }
    
    .footer-company-name {
        font-size: 20px;
    }
    
    .footer-bottom-links {
        text-align: center;
        justify-content: center;
        margin-top: 15px;
    }
    
    .copyright {
        text-align: center;
    }
}

@media (max-width: 768px) {
    .footer-wave {
        top: -49px;
        height: 50px;
    }
    
    .footer-brand {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-widget {
        text-align: center;
    }
    
    .footer-title {
        justify-content: center;
    }
    
    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-links {
        text-align: center;
    }
    
    .footer-link {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .newsletter-form .input-group {
        flex-direction: column;
    }
    
    .newsletter-btn {
        width: 100%;
        justify-content: center;
    }
    
    .back-to-top {
        width: 45px;
        height: 45px;
        font-size: 18px;
        bottom: 20px;
        right: 20px;
    }
}

@media (max-width: 576px) {
    .modern-footer {
        margin-top: 60px;
    }
    
    .footer-main {
        padding: 50px 0 20px;
    }
    
    .footer-company-name {
        font-size: 18px;
    }
    
    .footer-bottom-links {
        flex-direction: column;
        gap: 5px;
    }
    
    .footer-bottom-links .separator {
        display: none;
    }
}

/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.footer-logo {
    animation: float 3s ease-in-out infinite;
}

/* Loading Animation for Newsletter */
.newsletter-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.newsletter-btn.loading::after {
    content: '';
    width: 14px;
    height: 14px;
    border: 2px solid #667eea;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    margin-left: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Success State */
.newsletter-box.success {
    animation: pulse 0.5s ease;
}

.newsletter-box.success .newsletter-input {
    border-color: #4caf50;
}
</style>

{{-- Modern Footer JavaScript --}}
<script>
// Back to Top Button
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });
});

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Newsletter Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('.newsletter-btn');
            const input = this.querySelector('.newsletter-input');
            const originalBtnContent = btn.innerHTML;
            
            // Add loading state
            btn.classList.add('loading');
            btn.innerHTML = 'Subscribing...';
            
            // Simulate API call (replace with actual API call)
            setTimeout(() => {
                // Success state
                btn.classList.remove('loading');
                btn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                btn.style.background = '#4caf50';
                btn.style.color = 'white';
                
                // Add success animation to box
                const newsletterBox = document.querySelector('.newsletter-box');
                newsletterBox.classList.add('success');
                
                // Clear input
                input.value = '';
                
                // Reset after 3 seconds
                setTimeout(() => {
                    btn.innerHTML = originalBtnContent;
                    btn.style.background = '';
                    btn.style.color = '';
                    newsletterBox.classList.remove('success');
                }, 3000);
            }, 1500);
        });
    }
});

// Smooth hover effect for contact items
document.querySelectorAll('.contact-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Animate footer elements on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Add animation to footer widgets
document.addEventListener('DOMContentLoaded', function() {
    const footerWidgets = document.querySelectorAll('.footer-widget');
    footerWidgets.forEach((widget, index) => {
        widget.style.opacity = '0';
        widget.style.transform = 'translateY(20px)';
        widget.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(widget);
    });
});
</script>
