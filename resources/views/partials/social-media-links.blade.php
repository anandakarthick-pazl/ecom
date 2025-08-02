{{-- Social Media Links Component --}}
<div class="social-media-links" id="social-media-container">
    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3" id="social-icons">
        {{-- Social media icons will be loaded here via JavaScript --}}
        <div class="text-center">
            <div class="spinner-border spinner-border-sm text-muted" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<style>
.social-media-links {
    margin: 2rem 0;
}

.social-icon-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

/* Footer specific styling */
.footer-modern .social-icon-link {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.25rem;
    width: auto;
    height: auto;
    border-radius: 0;
    background: none !important;
    padding: 0;
}

.footer-modern .social-icon-link:hover {
    color: var(--primary-color, #3b82f6);
    transform: translateY(-2px);
    background: none !important;
}

/* Regular page styling */
.social-media-links:not(.footer-modern .social-media-links) .social-icon-link {
}

.social-icon-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.social-icon-link:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

.social-icon-link:hover::before {
    opacity: 1;
}

.social-icon-link:active {
    transform: translateY(-1px) scale(1.05);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .social-icon-link {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .social-icon-link {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .social-media-links {
        margin: 1.5rem 0;
    }
}

/* Loading animation */
.social-loading {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Hide when no social media links */
.social-media-links.d-none {
    display: none !important;
}

/* Success state animation */
.social-loaded {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSocialMediaLinks();
});

function loadSocialMediaLinks() {
    const container = document.getElementById('social-icons');
    
    // Fetch social media links from API
    @php
        try {
            $apiUrl = route('api.social-media-links');
        } catch (Exception $e) {
            $apiUrl = '/api/social-media-links';
        }
    @endphp
    
    const apiUrl = '{{ $apiUrl }}';
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                // Clear loading spinner
                container.innerHTML = '';
                
                // Add social media icons
                data.data.forEach(function(link, index) {
                    const socialIcon = createSocialIcon(link, index);
                    container.appendChild(socialIcon);
                });
                
                // Add loaded animation
                container.classList.add('social-loaded');
                
                // Show container if hidden
                document.getElementById('social-media-container').classList.remove('d-none');
            } else {
                // Hide container if no social media links
                document.getElementById('social-media-container').classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error loading social media links:', error);
            // Hide container on error
            document.getElementById('social-media-container').classList.add('d-none');
        });
}

function createSocialIcon(link, index) {
    const iconElement = document.createElement('a');
    iconElement.href = link.url;
    iconElement.target = '_blank';
    iconElement.rel = 'noopener noreferrer';
    iconElement.className = 'social-icon-link';
    iconElement.style.backgroundColor = link.color;
    iconElement.style.animationDelay = (index * 0.1) + 's';
    iconElement.title = `Follow us on ${link.name}`;
    
    // Add icon
    const icon = document.createElement('i');
    icon.className = link.icon_class;
    iconElement.appendChild(icon);
    
    // Add click tracking (optional)
    iconElement.addEventListener('click', function() {
        // You can add analytics tracking here
        console.log(`Social media link clicked: ${link.name}`);
        
        // Optional: Track with Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', 'social_media_click', {
                'social_platform': link.name,
                'link_url': link.url
            });
        }
    });
    
    return iconElement;
}

// Refresh social media links (useful for dynamic updates)
window.refreshSocialMediaLinks = function() {
    loadSocialMediaLinks();
};
</script>
