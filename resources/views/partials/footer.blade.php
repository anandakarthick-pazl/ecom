{{-- Website Footer --}}
<footer class="site-footer mt-5">
    <div class="container">
        <div class="row gy-4">
            {{-- Company Information --}}
            <div class="col-lg-4 col-md-6">
                <div class="footer-section">
                    <div class="footer-brand mb-3">
                        @if($globalCompany && $globalCompany->company_logo)
                            <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                 alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" 
                                 class="footer-logo me-2">
                        @endif
                        <h5 class="mb-0">{{ $globalCompany->company_name ?? 'Your Store' }}</h5>
                    </div>
                    <p class="text-muted mb-3">
                        {{ $globalCompany->company_description ?? 'Your trusted online shopping destination for quality products.' }}
                    </p>
                    
                    {{-- Contact Information --}}
                    @if($globalCompany && ($globalCompany->company_email || $globalCompany->company_phone))
                    <div class="contact-info">
                        @if($globalCompany->company_email)
                        <div class="contact-item">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <a href="mailto:{{ $globalCompany->company_email }}" class="text-decoration-none">
                                {{ $globalCompany->company_email }}
                            </a>
                        </div>
                        @endif
                        
                        @if($globalCompany->company_phone)
                        <div class="contact-item">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <a href="tel:{{ $globalCompany->company_phone }}" class="text-decoration-none">
                                {{ $globalCompany->company_phone }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('products') }}">Products</a></li>
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories->take(3) as $category)
                            <li><a href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                        @endif
                        <li><a href="{{ route('track.order') }}">Track Order</a></li>
                    </ul>
                </div>
            </div>
            
            {{-- Categories --}}
            <div class="col-lg-2 col-md-6">
                <div class="footer-section">
                    <h6 class="footer-title">Categories</h6>
                    <ul class="footer-links">
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories->take(5) as $category)
                            <li><a href="{{ route('category', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                        @else
                            <li><a href="{{ route('products') }}">All Products</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            {{-- Customer Support --}}
            <div class="col-lg-4 col-md-6">
                <div class="footer-section">
                    <h6 class="footer-title">Connect With Us</h6>
                    <p class="text-muted mb-3">Follow us on social media for updates and offers</p>
                    
                    {{-- Social Media Links --}}
                    @include('partials.social-media-links')
                    
                    {{-- Newsletter Signup (Optional) --}}
                    <div class="newsletter-signup">
                        <h6 class="mb-2">Stay Updated</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email" id="newsletter-email">
                            <button class="btn btn-primary" type="button" onclick="subscribeNewsletter()">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Footer Bottom --}}
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        &copy; {{ date('Y') }} {{ $globalCompany->company_name ?? 'Your Store' }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-links-inline">
                        <a href="#" class="text-decoration-none me-3">Privacy Policy</a>
                        <a href="#" class="text-decoration-none me-3">Terms of Service</a>
                        <a href="#" class="text-decoration-none">Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Footer Styles --}}
<style>
.site-footer {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color: #e2e8f0;
    padding: 3rem 0 1rem;
    margin-top: auto;
}

.footer-section {
    margin-bottom: 2rem;
}

.footer-brand {
    display: flex;
    align-items: center;
}

.footer-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 8px;
}

.footer-title {
    color: #fff;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.footer-links a {
    color: #cbd5e0;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: var(--primary-color, #3b82f6);
}

.contact-info .contact-item {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.contact-info a {
    color: #cbd5e0;
    text-decoration: none;
}

.contact-info a:hover {
    color: var(--primary-color, #3b82f6);
}

.newsletter-signup {
    margin-top: 1.5rem;
}

.newsletter-signup .input-group {
    margin-top: 0.5rem;
}

.newsletter-signup .form-control {
    border: 1px solid #4a5568;
    background: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
    border-radius: 8px 0 0 8px;
}

.newsletter-signup .form-control::placeholder {
    color: #a0aec0;
}

.newsletter-signup .form-control:focus {
    border-color: var(--primary-color, #3b82f6);
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
}

.newsletter-signup .btn {
    border-radius: 0 8px 8px 0;
}

.footer-bottom {
    border-top: 1px solid #4a5568;
    padding-top: 1.5rem;
    margin-top: 2rem;
}

.footer-links-inline a {
    color: #cbd5e0;
    font-size: 0.9rem;
}

.footer-links-inline a:hover {
    color: var(--primary-color, #3b82f6);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .site-footer {
        padding: 2rem 0 1rem;
    }
    
    .footer-section {
        margin-bottom: 1.5rem;
    }
    
    .footer-bottom .col-md-6:last-child {
        text-align: center !important;
        margin-top: 1rem;
    }
    
    .footer-links-inline a {
        display: inline-block;
        margin: 0.25rem 0.5rem;
    }
}
</style>

{{-- Footer JavaScript --}}
<script>
function subscribeNewsletter() {
    const email = document.getElementById('newsletter-email').value;
    
    if (!email) {
        if (typeof showToast !== 'undefined') {
            showToast('Please enter your email address', 'error');
        } else {
            alert('Please enter your email address');
        }
        return;
    }
    
    if (!isValidEmail(email)) {
        if (typeof showToast !== 'undefined') {
            showToast('Please enter a valid email address', 'error');
        } else {
            alert('Please enter a valid email address');
        }
        return;
    }
    
    // Here you can implement newsletter subscription logic
    // For now, we'll just show a success message
    if (typeof showToast !== 'undefined') {
        showToast('Thank you for subscribing!', 'success');
    } else {
        alert('Thank you for subscribing!');
    }
    
    document.getElementById('newsletter-email').value = '';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Handle newsletter signup on Enter key
document.addEventListener('DOMContentLoaded', function() {
    const newsletterInput = document.getElementById('newsletter-email');
    if (newsletterInput) {
        newsletterInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                subscribeNewsletter();
            }
        });
    }
});
</script>
