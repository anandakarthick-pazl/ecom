@extends('layouts.app')

@section('title')
    Flash Offers - {{ $globalCompany->company_name ?? 'Your Store' }}
@endsection

@section('meta_description')
    Don't miss out! Lightning-fast deals and limited-time flash offers at {{ $globalCompany->company_name ?? 'Your Store' }}. Shop now before they're gone!
@endsection

@section('content')
<div class="main-container">
    <!-- Flash Offers Hero Section -->
    <section class="flash-offers-hero">
        <div class="flash-hero-content">
            <div class="flash-icon-large">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="flash-hero-title">⚡ Flash Offers</h1>
            <p class="flash-hero-subtitle">Lightning-fast deals • Limited time only • While stocks last</p>
            
            @if($flashOffers->count() > 0)
                <div class="active-offers-count">
                    <span class="badge">{{ $flashOffers->count() }} Active Flash {{ $flashOffers->count() == 1 ? 'Offer' : 'Offers' }}</span>
                </div>
            @endif
        </div>
    </section>

    <!-- Flash Offers Grid -->
    <section class="flash-offers-section">
        @if($flashOffers->count() > 0)
            <!-- Active Flash Offers Info -->
            <div class="flash-offers-info">
                <div class="row">
                    @foreach($flashOffers as $offer)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="flash-offer-card">
                                @if($offer->banner_image)
                                    <div class="flash-offer-image">
                                        <img src="{{ $offer->banner_image_url }}" alt="{{ $offer->banner_title ?: $offer->name }}" class="img-fluid">
                                    </div>
                                @endif
                                
                                <div class="flash-offer-content">
                                    <h3 class="flash-offer-title">{{ $offer->banner_title ?: $offer->name }}</h3>
                                    @if($offer->banner_description)
                                        <p class="flash-offer-description">{{ $offer->banner_description }}</p>
                                    @endif
                                    
                                    <div class="flash-offer-discount">
                                        <span class="discount-value">{{ $offer->discount_value_display }}</span>
                                        <span class="discount-text">OFF</span>
                                    </div>
                                    
                                    @if($offer->show_countdown)
                                        @php
                                            $timeRemaining = $offer->getTimeRemaining();
                                        @endphp
                                        
                                        @if(!$timeRemaining['expired'])
                                            <div class="flash-countdown-small" data-end-time="{{ $offer->end_date->endOfDay()->timestamp }}">
                                                <div class="countdown-item">
                                                    <span class="countdown-number">{{ $timeRemaining['days'] }}</span>
                                                    <span class="countdown-label">Days</span>
                                                </div>
                                                <div class="countdown-item">
                                                    <span class="countdown-number">{{ $timeRemaining['hours'] }}</span>
                                                    <span class="countdown-label">Hours</span>
                                                </div>
                                                <div class="countdown-item">
                                                    <span class="countdown-number">{{ $timeRemaining['minutes'] }}</span>
                                                    <span class="countdown-label">Min</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Products Section -->
        <div class="flash-products-section">
            <div class="section-header">
                <h2 class="section-title">Flash Offer Products</h2>
                <div class="products-count" id="products-count">
                    @if($products && (method_exists($products, 'total') ? $products->total() > 0 : count($products) > 0))
                        Showing {{ method_exists($products, 'total') ? $products->total() : count($products) }} products
                    @endif
                </div>
            </div>

            <div id="products-container">
                @if($products && (method_exists($products, 'count') ? $products->count() > 0 : count($products) > 0))
                    <div class="products-grid-compact flash-offers">
                        @foreach($products as $product)
                            @include('partials.product-card-modern', ['product' => $product, 'flash' => true])
                        @endforeach
                    </div>

                    @if($frontendPaginationSettings['enabled'] && method_exists($products, 'appends'))
                        <div class="pagination-container" id="pagination-container">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    @include('partials.empty-state', [
                        'icon' => 'bolt',
                        'title' => 'No Flash Offers Available',
                        'message' => 'Stay tuned for lightning-fast deals and limited-time offers.',
                        'action' => 'Browse All Products',
                        'actionUrl' => route('products')
                    ])
                @endif
            </div>
        </div>
    </section>
</div>

<style>
.flash-offers-hero {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 50%, #e55039 100%);
    color: white;
    padding: 60px 0;
    text-align: center;
    margin-bottom: 40px;
}

.flash-hero-content {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 20px;
}

.flash-icon-large {
    font-size: 4rem;
    color: #feca57;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: flashPulse 2s infinite;
    margin-bottom: 20px;
}

.flash-hero-title {
    font-size: 3rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    margin-bottom: 15px;
    line-height: 1.2;
}

.flash-hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    margin-bottom: 25px;
}

.active-offers-count .badge {
    background: linear-gradient(45deg, #feca57, #ff9ff3);
    color: #2c3e50;
    font-size: 1.1rem;
    padding: 12px 25px;
    border-radius: 50px;
    font-weight: bold;
    box-shadow: 0 8px 25px rgba(254, 202, 87, 0.4);
    animation: badgePulse 3s infinite;
}

@keyframes flashPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes badgePulse {
    0%, 100% { transform: scale(1); box-shadow: 0 8px 25px rgba(254, 202, 87, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 12px 35px rgba(254, 202, 87, 0.6); }
}

.flash-offers-info {
    margin-bottom: 50px;
}

.flash-offer-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.flash-offer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.flash-offer-image {
    height: 200px;
    overflow: hidden;
}

.flash-offer-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.flash-offer-card:hover .flash-offer-image img {
    transform: scale(1.05);
}

.flash-offer-content {
    padding: 25px;
}

.flash-offer-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 10px;
}

.flash-offer-description {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
}

.flash-offer-discount {
    display: inline-block;
    background: linear-gradient(45deg, #feca57, #ff9ff3);
    padding: 10px 20px;
    border-radius: 25px;
    margin-bottom: 20px;
}

.discount-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin-right: 5px;
}

.discount-text {
    font-size: 1.2rem;
    font-weight: bold;
    color: #2c3e50;
}

.flash-countdown-small {
    display: flex;
    justify-content: center;
    gap: 15px;
}

.flash-countdown-small .countdown-item {
    text-align: center;
}

.flash-countdown-small .countdown-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #e55039;
    background: #fff2f0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
}

.flash-countdown-small .countdown-label {
    font-size: 0.9rem;
    color: #666;
    font-weight: bold;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.section-title {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.products-count {
    font-size: 1.1rem;
    color: #666;
    font-weight: 500;
}

.products-grid-compact.flash-offers .product-card-modern {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.products-grid-compact.flash-offers .product-card-modern:hover {
    border-color: #ff6b6b;
    transform: translateY(-3px);
}

.products-grid-compact.flash-offers .discount-badge {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
    animation: flashDiscountPulse 2s infinite;
}

@keyframes flashDiscountPulse {
    0%, 100% { 
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    50% { 
        background: linear-gradient(45deg, #ee5a24, #e55039);
        box-shadow: 0 8px 25px rgba(238, 90, 36, 0.5);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .flash-hero-title {
        font-size: 2.2rem;
    }
    
    .flash-hero-subtitle {
        font-size: 1.1rem;
    }
    
    .flash-icon-large {
        font-size: 3rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .flash-countdown-small {
        gap: 10px;
    }
    
    .flash-countdown-small .countdown-number {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
}
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize countdown timers for flash offers
    document.querySelectorAll('.flash-countdown-small').forEach(function(countdown) {
        const endTime = parseInt(countdown.dataset.endTime) * 1000;
        
        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;
            
            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                
                const daysEl = countdown.querySelector('.countdown-item:nth-child(1) .countdown-number');
                const hoursEl = countdown.querySelector('.countdown-item:nth-child(2) .countdown-number');
                const minutesEl = countdown.querySelector('.countdown-item:nth-child(3) .countdown-number');
                
                if (daysEl) daysEl.textContent = days.toString().padStart(2, '0');
                if (hoursEl) hoursEl.textContent = hours.toString().padStart(2, '0');
                if (minutesEl) minutesEl.textContent = minutes.toString().padStart(2, '0');
            } else {
                // Offer expired
                countdown.innerHTML = '<div class="text-danger"><strong>Offer Expired</strong></div>';
            }
        }
        
        // Update immediately and then every minute
        updateCountdown();
        setInterval(updateCountdown, 60000);
    });
});
</script>
@endpush
