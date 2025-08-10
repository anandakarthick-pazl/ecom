@if($flashOffer && $flashOffer->isFlashOfferActive() && $flashOffer->show_popup && (request()->routeIs('shop') || request()->routeIs('home')))
@php
    $timeRemaining = $flashOffer->getTimeRemaining();
@endphp

<!-- Flash Offer Popup Modal -->
<div class="modal fade" id="flashOfferModal" tabindex="-1" aria-labelledby="flashOfferModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg flash-offer-modal">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
            
            <!-- Modal Body -->
            <div class="modal-body p-0 position-relative">
                <!-- Flash Offer Content -->
                <div class="flash-offer-content">
                    @if($flashOffer->banner_image)
                        <!-- Background Image -->
                        <div class="flash-offer-bg" style="background-image: url('{{ asset('storage/' . $flashOffer->banner_image) }}');">
                            <div class="flash-offer-overlay"></div>
                        </div>
                    @endif
                    
                    <div class="flash-offer-inner {{ $flashOffer->banner_image ? 'with-bg' : 'no-bg' }}">
                        <div class="row align-items-center h-100">
                            <div class="col-md-8">
                                <!-- Flash Icon -->
                                <div class="flash-icon mb-3">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                
                                <!-- Title -->
                                @if($flashOffer->banner_title)
                                    <h2 class="flash-title mb-3">{{ $flashOffer->banner_title }}</h2>
                                @else
                                    <h2 class="flash-title mb-3">Flash Sale!</h2>
                                @endif
                                
                                <!-- Description -->
                                @if($flashOffer->banner_description)
                                    <p class="flash-description mb-4">{{ $flashOffer->banner_description }}</p>
                                @else
                                    <p class="flash-description mb-4">Limited time offer! Don't miss out on amazing savings!</p>
                                @endif
                                
                                <!-- Discount Badge -->
                                <div class="discount-badge mb-4">
                                    <span class="discount-value">{{ $flashOffer->discount_value_display }}</span>
                                    <span class="discount-text">OFF</span>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="flash-actions">
                                    @if($flashOffer->banner_button_url)
                                        <a href="{{ $flashOffer->banner_button_url }}" class="btn btn-flash-action btn-lg">
                                    @else
                                        <a href="{{ url('/') }}" class="btn btn-flash-action btn-lg">
                                    @endif
                                        <i class="fas fa-shopping-bag me-2"></i>
                                        {{ $flashOffer->banner_button_text ?: 'Shop Now' }}
                                    </a>
                                </div>
                            </div>
                            
                            @if($flashOffer->show_countdown && !$timeRemaining['expired'])
                            <div class="col-md-4">
                                <!-- Countdown Timer -->
                                <div class="countdown-section">
                                    <div class="countdown-text mb-3">
                                        {{ $flashOffer->countdown_text ?: 'Hurry! Limited time offer' }}
                                    </div>
                                    
                                    <div class="countdown-timer" id="flashOfferCountdown" data-end-time="{{ $flashOffer->end_date->endOfDay()->timestamp }}">
                                        <div class="countdown-item">
                                            <div class="countdown-number" id="countdown-days">{{ $timeRemaining['days'] }}</div>
                                            <div class="countdown-label">Days</div>
                                        </div>
                                        <div class="countdown-item">
                                            <div class="countdown-number" id="countdown-hours">{{ $timeRemaining['hours'] }}</div>
                                            <div class="countdown-label">Hours</div>
                                        </div>
                                        <div class="countdown-item">
                                            <div class="countdown-number" id="countdown-minutes">{{ $timeRemaining['minutes'] }}</div>
                                            <div class="countdown-label">Minutes</div>
                                        </div>
                                        <div class="countdown-item">
                                            <div class="countdown-number" id="countdown-seconds">{{ $timeRemaining['seconds'] }}</div>
                                            <div class="countdown-label">Seconds</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Offer Styles -->
<style>
.flash-offer-modal .modal-content {
    border-radius: 20px;
    overflow: hidden;
    min-height: 400px;
}

.flash-offer-content {
    position: relative;
    min-height: 400px;
    display: flex;
    align-items: center;
}

.flash-offer-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}

.flash-offer-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
    z-index: 2;
}

.flash-offer-inner {
    position: relative;
    z-index: 3;
    padding: 40px;
    width: 100%;
}

.flash-offer-inner.no-bg {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 50%, #e55039 100%);
    color: white;
}

.flash-offer-inner.with-bg {
    color: white;
}

.flash-icon {
    font-size: 3rem;
    color: #feca57;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: flashPulse 2s infinite;
}

@keyframes flashPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.flash-title {
    font-size: 2.5rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    line-height: 1.2;
}

.flash-description {
    font-size: 1.2rem;
    opacity: 0.9;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.discount-badge {
    display: inline-block;
    background: linear-gradient(45deg, #feca57, #ff9ff3);
    padding: 15px 30px;
    border-radius: 50px;
    box-shadow: 0 8px 25px rgba(254, 202, 87, 0.4);
    animation: discountPulse 3s infinite;
}

@keyframes discountPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 8px 25px rgba(254, 202, 87, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 12px 35px rgba(254, 202, 87, 0.6); }
}

.discount-value {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    margin-right: 8px;
}

.discount-text {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.btn-flash-action {
    background: linear-gradient(45deg, #00b894, #00cec9);
    border: none;
    color: white;
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: bold;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 8px 25px rgba(0, 184, 148, 0.4);
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-flash-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0, 184, 148, 0.6);
    color: white;
}

.countdown-section {
    text-align: center;
    padding: 20px;
    background: rgba(255,255,255,0.1);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.countdown-text {
    font-size: 1.1rem;
    font-weight: bold;
    color: #feca57;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.countdown-timer {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.countdown-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 60px;
}

.countdown-number {
    font-size: 2rem;
    font-weight: bold;
    background: linear-gradient(45deg, #feca57, #ff9ff3);
    color: #2c3e50;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
    box-shadow: 0 4px 15px rgba(254, 202, 87, 0.3);
    animation: countdownPulse 1s infinite;
}

@keyframes countdownPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.countdown-label {
    font-size: 0.9rem;
    color: #feca57;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .flash-offer-inner {
        padding: 20px;
    }
    
    .flash-title {
        font-size: 2rem;
    }
    
    .flash-description {
        font-size: 1rem;
    }
    
    .discount-value {
        font-size: 1.5rem;
    }
    
    .discount-text {
        font-size: 1.2rem;
    }
    
    .btn-flash-action {
        padding: 12px 30px;
        font-size: 1rem;
    }
    
    .countdown-timer {
        gap: 10px;
    }
    
    .countdown-number {
        font-size: 1.5rem;
        width: 40px;
        height: 40px;
    }
}

/* Modal entrance animation */
.flash-offer-modal {
    animation: modalFadeIn 0.5s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<!-- Flash Offer JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize flash offer popup on home page
    const isHomePage = {{ (request()->routeIs('shop') || request()->routeIs('home')) ? 'true' : 'false' }};
    
    if (!isHomePage) {
        console.log('Flash Offer: Not on home page, skipping popup initialization');
        return;
    }
    
    console.log('Flash Offer: DOM loaded, checking for active offer on home page...');
    
    // Debug: Check if we have a flash offer
    const flashOfferId = '{{ $flashOffer->id }}';
    const popupDelay = {{ $flashOffer->popup_delay ?: 3000 }};
    const popupFrequency = '{{ $flashOffer->popup_frequency ?? "always" }}';
    
    console.log('Flash Offer ID:', flashOfferId);
    console.log('Popup Delay:', popupDelay + 'ms');
    console.log('Popup Frequency:', popupFrequency);
    console.log('Current Route:', '{{ request()->route()->getName() }}');
    
    // Check if we should show the popup based on frequency setting
    let shouldShowPopup = true;
    
    if (popupFrequency !== 'always') {
        const flashOfferKey = 'flash_offer_seen_' + flashOfferId;
        const lastSeen = localStorage.getItem(flashOfferKey);
        const now = new Date();
        
        if (lastSeen) {
            const lastSeenDate = new Date(lastSeen);
            
            switch (popupFrequency) {
                case 'once_per_session':
                    // Don't show again in this session (cleared when browser is closed)
                    shouldShowPopup = false;
                    break;
                    
                case 'once_per_day':
                    // Don't show again today
                    if (lastSeenDate.toDateString() === now.toDateString()) {
                        shouldShowPopup = false;
                    }
                    break;
                    
                case 'once_per_week':
                    // Don't show again this week
                    const daysSince = Math.floor((now - lastSeenDate) / (1000 * 60 * 60 * 24));
                    if (daysSince < 7) {
                        shouldShowPopup = false;
                    }
                    break;
            }
        }
    }
    
    console.log('Should show popup:', shouldShowPopup);
    
    if (shouldShowPopup) {
        console.log('Flash Offer: Showing popup after delay...');
        
        // Show popup after delay
        setTimeout(function() {
            console.log('Flash Offer: Attempting to show modal...');
            
            const modalElement = document.getElementById('flashOfferModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Flash Offer: Modal shown successfully!');
                
                // Mark as seen based on frequency setting
                if (popupFrequency !== 'always') {
                    const storageKey = 'flash_offer_seen_' + flashOfferId;
                    if (popupFrequency === 'once_per_session') {
                        // Use sessionStorage for session-based tracking
                        sessionStorage.setItem(storageKey, new Date().toISOString());
                    } else {
                        // Use localStorage for day/week-based tracking
                        localStorage.setItem(storageKey, new Date().toISOString());
                    }
                    console.log('Flash Offer: Marked as seen (frequency: ' + popupFrequency + ')');
                }
            } else {
                console.error('Flash Offer: Modal element not found!');
            }
        }, popupDelay);
    } else {
        console.log('Flash Offer: Not showing popup due to frequency setting');
    }
    
    // Debug button to force show popup (for testing)
    if (window.location.search.includes('debug=flash')) {
        setTimeout(function() {
            // Clear storage for debugging
            const storageKey = 'flash_offer_seen_' + flashOfferId;
            localStorage.removeItem(storageKey);
            sessionStorage.removeItem(storageKey);
            
            const modalElement = document.getElementById('flashOfferModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Flash Offer: Debug mode - showing popup');
            }
        }, 1000);
    }
    
    // Countdown timer functionality
    @if($flashOffer->show_countdown && !$timeRemaining['expired'])
    const countdownTimer = document.getElementById('flashOfferCountdown');
    if (countdownTimer) {
        console.log('Flash Offer: Starting countdown timer...');
        const endTime = parseInt(countdownTimer.dataset.endTime) * 1000;
        
        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;
            
            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                const daysEl = document.getElementById('countdown-days');
                const hoursEl = document.getElementById('countdown-hours');
                const minutesEl = document.getElementById('countdown-minutes');
                const secondsEl = document.getElementById('countdown-seconds');
                
                if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
                if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
                if (secondsEl) secondsEl.textContent = seconds.toString().padStart(2, '0');
            } else {
                console.log('Flash Offer: Countdown expired, hiding modal');
                // Offer expired, hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('flashOfferModal'));
                if (modal) {
                    modal.hide();
                }
                clearInterval(countdownInterval);
            }
        }
        
        // Update countdown immediately and then every second
        updateCountdown();
        const countdownInterval = setInterval(updateCountdown, 1000);
    }
    @endif
});
</script>
@endif
