@extends('layouts.app')

@section('title', 'Secure Checkout - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
{{-- Include Enhanced Components --}}
@include('enhanced-components.shared-animations')
@include('enhanced-components.fireworks-system')

<!-- Enhanced Page Header -->
<div class="page-header-enhanced checkout-header">
    <div class="container">
        <div class="header-content text-center">
            <h1 class="display-4 mb-3 fw-bold animate-fade-in">
                <i class="fas fa-lock me-3"></i>
                Secure Checkout
            </h1>
            <p class="lead animate-slide-up">Complete your order securely with our encrypted checkout process</p>
            
            <!-- Checkout Progress -->
            <div class="checkout-progress animate-bounce-in">
                <div class="progress-step completed">
                    <div class="step-circle">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="step-label">Cart</span>
                </div>
                <div class="progress-line completed"></div>
                <div class="progress-step active">
                    <div class="step-circle">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <span class="step-label">Checkout</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="step-label">Complete</span>
                </div>
            </div>
            
            <div class="security-badges">
                <span class="security-badge">
                    <i class="fas fa-shield-alt text-success"></i>
                    <small>256-bit SSL</small>
                </span>
                <span class="security-badge">
                    <i class="fas fa-lock text-primary"></i>
                    <small>Secure Payment</small>
                </span>
                <span class="security-badge">
                    <i class="fas fa-user-shield text-warning"></i>
                    <small>Privacy Protected</small>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container checkout-page-container">
    <div class="row g-4">
        <!-- Enhanced Checkout Form -->
        <div class="col-lg-8">
            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutFormEnhanced" class="checkout-form-enhanced">
                @csrf
                
                <!-- Enhanced Delivery Information -->
                <div class="form-section-enhanced animate-slide-up">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="section-info">
                            <h5 class="section-title">Delivery Information</h5>
                            <p class="section-subtitle">Where should we deliver your order?</p>
                        </div>
                        <div class="section-indicator">
                            <span class="step-number">1</span>
                        </div>
                    </div>
                    
                    <div class="section-content">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-enhanced">
                                    <label for="customer_name" class="form-label-enhanced">
                                        <i class="fas fa-user me-2"></i>Full Name *
                                    </label>
                                    <input type="text" 
                                           class="form-control-enhanced @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" 
                                           name="customer_name" 
                                           value="{{ old('customer_name') }}" 
                                           required
                                           placeholder="Enter your full name">
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-enhanced">
                                    <label for="customer_mobile" class="form-label-enhanced">
                                        <i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp Mobile Number *
                                    </label>
                                    <div class="input-group-enhanced">
                                        <span class="input-group-text-enhanced">
                                            <i class="fab fa-whatsapp text-success"></i> +91
                                        </span>
                                        <input type="tel" 
                                               class="form-control-enhanced @error('customer_mobile') is-invalid @enderror" 
                                               id="customer_mobile" 
                                               name="customer_mobile" 
                                               value="{{ old('customer_mobile') }}" 
                                               pattern="[0-9]{10}" 
                                               maxlength="10" 
                                               required 
                                               placeholder="9003096885">
                                        @error('customer_mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-help-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Order updates will be sent via WhatsApp
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-enhanced">
                            <label for="customer_email" class="form-label-enhanced">
                                <i class="fas fa-envelope me-2"></i>Email Address (Optional)
                                <span class="label-badge">Get invoice PDF</span>
                            </label>
                            <input type="email" 
                                   class="form-control-enhanced @error('customer_email') is-invalid @enderror" 
                                   id="customer_email" 
                                   name="customer_email" 
                                   value="{{ old('customer_email') }}" 
                                   placeholder="your@email.com">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-help-text">
                                <i class="fas fa-file-pdf me-1"></i>
                                Receive order updates and invoice PDF via email
                            </small>
                        </div>
                        
                        <div class="form-group-enhanced">
                            <label for="delivery_address" class="form-label-enhanced">
                                <i class="fas fa-map-marker-alt me-2"></i>Delivery Address *
                            </label>
                            <textarea class="form-control-enhanced @error('delivery_address') is-invalid @enderror" 
                                      id="delivery_address" 
                                      name="delivery_address" 
                                      rows="3" 
                                      required
                                      placeholder="Enter your complete delivery address with landmarks">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group-enhanced">
                                    <label for="city" class="form-label-enhanced">
                                        <i class="fas fa-city me-2"></i>City *
                                    </label>
                                    <input type="text" 
                                           class="form-control-enhanced @error('city') is-invalid @enderror" 
                                           id="city" 
                                           name="city" 
                                           value="{{ old('city') }}" 
                                           required
                                           placeholder="Enter city">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group-enhanced">
                                    <label for="state" class="form-label-enhanced">
                                        <i class="fas fa-map me-2"></i>State
                                    </label>
                                    <input type="text" 
                                           class="form-control-enhanced @error('state') is-invalid @enderror" 
                                           id="state" 
                                           name="state" 
                                           value="{{ old('state') }}"
                                           placeholder="Enter state">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group-enhanced">
                                    <label for="pincode" class="form-label-enhanced">
                                        <i class="fas fa-mail-bulk me-2"></i>PIN Code *
                                    </label>
                                    <input type="text" 
                                           class="form-control-enhanced @error('pincode') is-invalid @enderror" 
                                           id="pincode" 
                                           name="pincode" 
                                           value="{{ old('pincode') }}" 
                                           pattern="[0-9]{6}" 
                                           maxlength="6" 
                                           required
                                           placeholder="Enter 6-digit PIN">
                                    @error('pincode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group-enhanced">
                            <label for="notes" class="form-label-enhanced">
                                <i class="fas fa-sticky-note me-2"></i>Order Notes (Optional)
                            </label>
                            <textarea class="form-control-enhanced @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="2" 
                                      placeholder="Any special instructions for delivery...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced Payment Method Selection -->
                <div class="form-section-enhanced animate-slide-up animate-stagger-2">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="section-info">
                            <h5 class="section-title">Payment Method</h5>
                            <p class="section-subtitle">Choose your preferred payment option</p>
                        </div>
                        <div class="section-indicator">
                            <span class="step-number">2</span>
                        </div>
                    </div>
                    
                    <div class="section-content">
                        @if($paymentMethods->count() > 0)
                            <div class="payment-methods-enhanced">
                                @foreach($paymentMethods as $method)
                                    <div class="payment-method-enhanced">
                                        <input class="payment-method-input" 
                                               type="radio" 
                                               name="payment_method" 
                                               id="payment_{{ $method->id }}" 
                                               value="{{ $method->id }}"
                                               data-extra-charge="{{ $method->extra_charge }}"
                                               data-extra-percentage="{{ $method->extra_charge_percentage }}"
                                               {{ old('payment_method', $loop->first ? $method->id : '') == $method->id ? 'checked' : '' }}
                                               required>
                                        <label class="payment-method-label" for="payment_{{ $method->id }}">
                                            <div class="payment-method-content">
                                                <div class="payment-method-header">
                                                    <div class="payment-icon">
                                                        <i class="{{ $method->getIcon() }} text-{{ $method->getColor() }}"></i>
                                                    </div>
                                                    <div class="payment-info">
                                                        <h6 class="payment-name">{{ $method->display_name }}</h6>
                                                        @if($method->description)
                                                            <p class="payment-description">{{ $method->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="payment-charge">
                                                        @if($method->extra_charge > 0 || $method->extra_charge_percentage > 0)
                                                            <span class="charge-badge">
                                                                @if($method->extra_charge > 0)
                                                                    +₹{{ number_format($method->extra_charge, 2) }}
                                                                @endif
                                                                @if($method->extra_charge_percentage > 0)
                                                                    @if($method->extra_charge > 0) + @endif
                                                                    {{ $method->extra_charge_percentage }}%
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="free-badge">Free</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                @if($method->type === 'bank_transfer' && $method->bank_details)
                                                    <div class="payment-details bank-details" style="display: none;">
                                                        <div class="details-header">
                                                            <i class="fas fa-university me-2"></i>Bank Transfer Details
                                                        </div>
                                                        <div class="details-grid">
                                                            <div class="detail-item">
                                                                <span class="detail-label">Bank Name:</span>
                                                                <span class="detail-value">{{ $method->bank_details['bank_name'] ?? '' }}</span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Account Number:</span>
                                                                <span class="detail-value">{{ $method->bank_details['account_number'] ?? '' }}</span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">IFSC Code:</span>
                                                                <span class="detail-value">{{ $method->bank_details['ifsc_code'] ?? '' }}</span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Account Name:</span>
                                                                <span class="detail-value">{{ $method->bank_details['account_name'] ?? '' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if($method->type === 'upi' || $method->type === 'gpay')
                                                    <div class="payment-details upi-details" style="display: none;">
                                                        <div class="details-header">
                                                            <i class="fab fa-{{ $method->type === 'gpay' ? 'google-pay' : 'cc-paypal' }} me-2"></i>
                                                            {{ $method->type === 'gpay' ? 'Google Pay' : 'UPI' }} Payment
                                                        </div>
                                                        @if($method->upi_id)
                                                            <div class="upi-id-section">
                                                                <span class="upi-label">UPI ID:</span>
                                                                <span class="upi-id">{{ $method->upi_id }}</span>
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-outline-primary copy-upi-btn"
                                                                        onclick="copyUpiId('{{ $method->upi_id }}')">
                                                                    <i class="fas fa-copy"></i> Copy
                                                                </button>
                                                            </div>
                                                        @endif
                                                        @if($method->upi_qr_code)
                                                            <div class="qr-code-section">
                                                                <div class="qr-code-container">
                                                                    <img src="{{ Storage::url($method->upi_qr_code) }}" 
                                                                         alt="{{ $method->type === 'gpay' ? 'Google Pay' : 'UPI' }} QR Code" 
                                                                         class="qr-code-image">
                                                                    <div class="qr-code-overlay">
                                                                        <i class="fas fa-expand-alt"></i>
                                                                        <span>Tap to enlarge</span>
                                                                    </div>
                                                                </div>
                                                                <p class="qr-code-instructions">
                                                                    <i class="fas fa-mobile-alt me-1"></i>
                                                                    Scan with any UPI app to pay
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        @else
                            <div class="alert-enhanced alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <h6>No Payment Methods Available</h6>
                                    <p>Please contact our support team to enable payment methods.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Enhanced Order Summary -->
        <div class="col-lg-4">
            <div class="order-summary-enhanced sticky-summary animate-scale-in">
                <div class="summary-header">
                    <h5 class="summary-title">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                    <div class="summary-badge">
                        {{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}
                    </div>
                </div>
                
                <div class="summary-content">
                    <!-- Order Items -->
                    <div class="order-items-section">
                        @foreach($cartItems as $item)
                        <div class="order-item">
                            <div class="item-image">
                                @if($item->product->featured_image)
                                    <img src="{{ Storage::url($item->product->featured_image) }}" 
                                         alt="{{ $item->product->name }}">
                                @else
                                    <div class="image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                                <span class="item-quantity">{{ $item->quantity }}</span>
                            </div>
                            <div class="item-details">
                                <h6 class="item-name">{{ $item->product->name }}</h6>
                                <div class="item-price-info">
                                    <span class="item-unit-price">₹{{ number_format($item->price, 2) }} each</span>
                                    @if($item->product->tax_percentage > 0)
                                        <small class="item-tax">GST: {{ $item->product->tax_percentage }}%</small>
                                    @endif
                                </div>
                            </div>
                            <div class="item-total-price">
                                ₹{{ number_format($item->total, 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
                        <div class="breakdown-row">
                            <span class="breakdown-label">
                                <i class="fas fa-box me-2"></i>Subtotal:
                            </span>
                            <span class="breakdown-value">₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @php
                            // Calculate tax amounts
                            $totalTax = 0;
                            $cgstAmount = 0;
                            $sgstAmount = 0;
                            
                            foreach($cartItems as $item) {
                                $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                                $totalTax += $itemTax;
                                $cgstAmount += ($itemTax / 2);
                                $sgstAmount += ($itemTax / 2);
                            }
                            
                            $grandTotal = $subtotal + $totalTax + $deliveryCharge - $discount;
                        @endphp
                        
                        <div class="breakdown-row tax-row">
                            <span class="breakdown-label">
                                <i class="fas fa-percentage me-2"></i>CGST:
                            </span>
                            <span class="breakdown-value">₹{{ number_format($cgstAmount, 2) }}</span>
                        </div>
                        
                        <div class="breakdown-row tax-row">
                            <span class="breakdown-label">
                                <i class="fas fa-percentage me-2"></i>SGST:
                            </span>
                            <span class="breakdown-value">₹{{ number_format($sgstAmount, 2) }}</span>
                        </div>
                        
                        <div class="breakdown-row delivery-row">
                            <span class="breakdown-label">
                                <i class="fas fa-shipping-fast me-2"></i>Delivery:
                            </span>
                            <span class="breakdown-value">
                                @if($deliveryCharge == 0)
                                    <span class="text-success fw-bold">FREE</span>
                                @else
                                    ₹{{ number_format($deliveryCharge, 2) }}
                                @endif
                            </span>
                        </div>
                        
                        @if($discount > 0)
                        <div class="breakdown-row discount-row">
                            <span class="breakdown-label">
                                <i class="fas fa-tag me-2"></i>Discount:
                            </span>
                            <span class="breakdown-value text-success">-₹{{ number_format($discount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="breakdown-row payment-charge-row" id="payment-charge-row" style="display: none;">
                            <span class="breakdown-label">
                                <i class="fas fa-credit-card me-2"></i>Payment Charge:
                            </span>
                            <span class="breakdown-value" id="payment-charge">+₹0.00</span>
                        </div>
                    </div>
                    
                    <!-- Free Shipping Progress -->
                    @if($deliveryCharge > 0 && $subtotal < 500)
                    <div class="free-shipping-section">
                        <div class="shipping-progress-header">
                            <i class="fas fa-truck me-2"></i>
                            <span>Add ₹{{ number_format(500 - $subtotal, 2) }} for FREE delivery!</span>
                        </div>
                        <div class="shipping-progress-bar">
                            <div class="progress-fill" style="width: {{ ($subtotal / 500) * 100 }}%"></div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Total Section -->
                    <div class="total-section">
                        <div class="total-row">
                            <span class="total-label">Grand Total:</span>
                            <span class="total-value" id="grand-total">₹{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        
                        @php
                            $totalSavings = 0;
                            foreach($cartItems as $item) {
                                if($item->product->price > $item->price) {
                                    $totalSavings += ($item->product->price - $item->price) * $item->quantity;
                                }
                            }
                        @endphp
                        @if($totalSavings > 0)
                            <div class="savings-row">
                                <i class="fas fa-tag text-success me-1"></i>
                                You're saving ₹{{ number_format($totalSavings, 2) }}!
                            </div>
                        @endif
                    </div>
                    
                    <!-- Place Order Button -->
                    <div class="place-order-section">
                        <button type="submit" 
                                form="checkoutFormEnhanced" 
                                class="btn-place-order-enhanced">
                            <div class="btn-content">
                                <i class="fas fa-lock me-2"></i>
                                <span class="btn-text">Place Secure Order</span>
                                <i class="fas fa-arrow-right ms-2"></i>
                            </div>
                            <div class="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                <span>Processing...</span>
                            </div>
                        </button>
                        
                        <div class="security-info">
                            <div class="security-badges">
                                <span class="security-item">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <small>SSL Secured</small>
                                </span>
                                <span class="security-item">
                                    <i class="fas fa-lock text-primary"></i>
                                    <small>Encrypted</small>
                                </span>
                                <span class="security-item">
                                    <i class="fas fa-user-shield text-warning"></i>
                                    <small>Privacy Protected</small>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery Information -->
                    @if($deliveryInfo['enabled'])
                    <div class="delivery-info-section">
                        @if($deliveryInfo['time_estimate'])
                            <div class="delivery-estimate">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Estimated Delivery:</strong> {{ $deliveryInfo['time_estimate'] }}
                            </div>
                        @endif
                        
                        @if($deliveryInfo['description'])
                            <div class="delivery-description">
                                <i class="fas fa-info-circle text-info me-2"></i>
                                {{ $deliveryInfo['description'] }}
                            </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Payment Methods Accepted -->
                    <div class="accepted-payments">
                        <h6 class="payments-title">We Accept:</h6>
                        <div class="payment-icons">
                            <i class="fab fa-cc-visa" title="Visa"></i>
                            <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                            <i class="fab fa-google-pay" title="Google Pay"></i>
                            <i class="fab fa-cc-paypal" title="UPI"></i>
                            <i class="fas fa-university" title="Bank Transfer"></i>
                            <i class="fas fa-mobile-alt" title="Mobile Wallets"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="help-section-enhanced animate-bounce-in">
                <div class="help-header">
                    <i class="fas fa-headset me-2"></i>
                    <h6>Need Help?</h6>
                </div>
                <div class="help-content">
                    <div class="contact-item">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <span>+91 9876543210</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope text-success me-2"></i>
                        <span>support@{{ request()->getHost() }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        <span>WhatsApp Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Checkout Header */
    .checkout-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    /* Checkout Progress */
    .checkout-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 30px 0;
        gap: 20px;
    }
    
    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.7);
        font-size: 1.2rem;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .progress-step.completed .step-circle {
        background: rgba(255,255,255,0.9);
        color: #28a745;
        border-color: rgba(255,255,255,0.9);
    }
    
    .progress-step.active .step-circle {
        background: white;
        color: #28a745;
        border-color: white;
        box-shadow: 0 0 20px rgba(255,255,255,0.5);
        animation: pulse 2s infinite;
    }
    
    .step-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255,255,255,0.9);
    }
    
    .progress-line {
        width: 60px;
        height: 3px;
        background: rgba(255,255,255,0.2);
        border-radius: 2px;
    }
    
    .progress-line.completed {
        background: rgba(255,255,255,0.8);
    }
    
    /* Security Badges */
    .security-badges {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }
    
    .security-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: rgba(255,255,255,0.9);
    }
    
    .security-badge i {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .security-badge small {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    
    /* Checkout Page Container */
    .checkout-page-container {
        background: white;
        border-radius: 25px 25px 0 0;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        padding: 60px 15px 40px 15px;
        position: relative;
        z-index: 2;
        margin-top: -40px;
    }
    
    /* Enhanced Form Sections */
    .form-section-enhanced {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .section-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .section-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .section-info {
        flex-grow: 1;
    }
    
    .section-title {
        margin: 0 0 5px 0;
        font-weight: 700;
        color: #333;
        font-size: 1.25rem;
    }
    
    .section-subtitle {
        margin: 0;
        color: #666;
        font-size: 14px;
    }
    
    .section-indicator {
        margin-left: 20px;
    }
    
    .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        font-weight: 700;
        font-size: 18px;
    }
    
    .section-content {
        padding: 30px;
    }
    
    /* Enhanced Form Groups */
    .form-group-enhanced {
        margin-bottom: 25px;
    }
    
    .form-label-enhanced {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        font-size: 14px;
    }
    
    .label-badge {
        background: var(--primary-color);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-left: 8px;
    }
    
    .form-control-enhanced {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-enhanced:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color), 0.15);
        transform: translateY(-1px);
    }
    
    .form-control-enhanced.is-valid {
        border-color: #28a745;
        background-image: none;
    }
    
    .input-group-enhanced {
        position: relative;
        display: flex;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .input-group-enhanced:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-color), 0.15);
        transform: translateY(-1px);
    }
    
    .input-group-text-enhanced {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .input-group-enhanced .form-control-enhanced {
        border: none;
        border-radius: 0;
        box-shadow: none;
    }
    
    .input-group-enhanced .form-control-enhanced:focus {
        transform: none;
    }
    
    .form-help-text {
        color: #666;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }
    
    /* Enhanced Payment Methods */
    .payment-methods-enhanced {
        display: grid;
        gap: 15px;
    }
    
    .payment-method-enhanced {
        position: relative;
    }
    
    .payment-method-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .payment-method-label {
        display: block;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .payment-method-label:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .payment-method-input:checked + .payment-method-label {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(var(--primary-color), 0.05) 0%, rgba(var(--primary-color), 0.1) 100%);
        box-shadow: 0 8px 25px rgba(var(--primary-color), 0.2);
    }
    
    .payment-method-content {
        padding: 20px;
    }
    
    .payment-method-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .payment-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .payment-info {
        flex-grow: 1;
    }
    
    .payment-name {
        margin: 0 0 5px 0;
        font-weight: 700;
        color: #333;
        font-size: 16px;
    }
    
    .payment-description {
        margin: 0;
        color: #666;
        font-size: 13px;
        line-height: 1.4;
    }
    
    .payment-charge {
        text-align: right;
    }
    
    .charge-badge {
        background: #ffc107;
        color: #333;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .free-badge {
        background: #28a745;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .payment-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }
    
    .details-header {
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .details-grid {
        display: grid;
        gap: 10px;
    }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #666;
        font-size: 13px;
    }
    
    .detail-value {
        font-weight: 600;
        color: #333;
        font-size: 13px;
    }
    
    .upi-id-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .upi-label {
        font-weight: 600;
        color: #666;
        font-size: 13px;
    }
    
    .upi-id {
        flex-grow: 1;
        font-weight: 700;
        color: #333;
        font-family: monospace;
        font-size: 14px;
    }
    
    .copy-upi-btn {
        padding: 4px 8px;
        font-size: 11px;
    }
    
    .qr-code-section {
        text-align: center;
    }
    
    .qr-code-container {
        position: relative;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .qr-code-image {
        max-width: 200px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
    .qr-code-image:hover {
        transform: scale(1.05);
    }
    
    .qr-code-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .qr-code-container:hover .qr-code-overlay {
        opacity: 1;
    }
    
    .qr-code-instructions {
        color: #666;
        font-size: 13px;
        margin: 0;
    }
    
    /* Enhanced Order Summary */
    .order-summary-enhanced {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }
    
    .sticky-summary {
        position: sticky;
        top: 100px;
    }
    
    .summary-header {
        background: var(--primary-gradient);
        color: white;
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .summary-title {
        margin: 0;
        font-weight: 700;
    }
    
    .summary-badge {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .summary-content {
        padding: 0;
    }
    
    /* Order Items */
    .order-items-section {
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .order-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .order-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .item-image {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        overflow: hidden;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-placeholder {
        width: 100%;
        height: 100%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    
    .item-quantity {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--primary-color);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        border: 2px solid white;
    }
    
    .item-details {
        flex-grow: 1;
        margin-right: 10px;
    }
    
    .item-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin: 0 0 5px 0;
        line-height: 1.3;
    }
    
    .item-price-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .item-unit-price {
        font-size: 12px;
        color: #666;
    }
    
    .item-tax {
        font-size: 11px;
        color: #999;
    }
    
    .item-total-price {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary-color);
        text-align: right;
    }
    
    /* Price Breakdown */
    .price-breakdown {
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .breakdown-row:last-child {
        border-bottom: none;
    }
    
    .breakdown-label {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }
    
    .breakdown-value {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }
    
    .tax-row .breakdown-label,
    .tax-row .breakdown-value {
        font-size: 12px;
        color: #888;
    }
    
    .delivery-row {
        background: #f8f9fa;
        margin: 0 -10px;
        padding: 12px 10px 8px 10px;
        border-radius: 8px;
    }
    
    .discount-row .breakdown-value {
        color: #28a745;
        font-weight: 700;
    }
    
    /* Free Shipping Section */
    .free-shipping-section {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-radius: 12px;
        padding: 15px;
        margin: 20px 25px;
        border: 1px solid #ffeaa7;
    }
    
    .shipping-progress-header {
        font-size: 12px;
        font-weight: 600;
        color: #856404;
        margin-bottom: 8px;
    }
    
    .shipping-progress-bar {
        height: 8px;
        background: rgba(255,255,255,0.5);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    
    /* Total Section */
    .total-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .total-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
    }
    
    .total-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
    }
    
    .savings-row {
        text-align: center;
        font-size: 13px;
        color: #28a745;
        font-weight: 600;
    }
    
    /* Place Order Button */
    .place-order-section {
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .btn-place-order-enhanced {
        width: 100%;
        padding: 18px 20px;
        border-radius: 15px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(var(--primary-color), 0.3);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .btn-place-order-enhanced:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(var(--primary-color), 0.4);
    }
    
    .btn-place-order-enhanced:active {
        transform: translateY(-1px);
    }
    
    .btn-place-order-enhanced:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }
    
    .btn-content,
    .btn-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .security-info {
        text-align: center;
    }
    
    .security-badges {
        display: flex;
        justify-content: space-around;
    }
    
    .security-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 10px;
        color: #666;
    }
    
    .security-item i {
        font-size: 1.2rem;
        margin-bottom: 4px;
    }
    
    /* Delivery Info */
    .delivery-info-section {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        background: #f8f9fa;
    }
    
    .delivery-estimate,
    .delivery-description {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    
    .delivery-estimate:last-child,
    .delivery-description:last-child {
        margin-bottom: 0;
    }
    
    /* Accepted Payments */
    .accepted-payments {
        padding: 20px 25px;
        text-align: center;
    }
    
    .payments-title {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .payment-icons {
        display: flex;
        justify-content: space-around;
        font-size: 1.5rem;
        color: #999;
    }
    
    .payment-icons i {
        transition: color 0.3s ease;
    }
    
    .payment-icons i:hover {
        color: var(--primary-color);
    }
    
    /* Help Section */
    .help-section-enhanced {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #f0f0f0;
    }
    
    .help-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: #333;
    }
    
    .help-header h6 {
        margin: 0;
        font-weight: 700;
    }
    
    .help-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .contact-item {
        font-size: 14px;
        color: #666;
        display: flex;
        align-items: center;
    }
    
    /* Alert Enhanced */
    .alert-enhanced {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .alert-enhanced i {
        font-size: 1.5rem;
        color: #856404;
    }
    
    .alert-enhanced h6 {
        margin: 0 0 5px 0;
        color: #856404;
        font-weight: 700;
    }
    
    .alert-enhanced p {
        margin: 0;
        color: #856404;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .checkout-page-container {
            padding: 40px 15px 20px 15px;
        }
        
        .checkout-progress {
            flex-direction: column;
            gap: 15px;
        }
        
        .progress-line {
            width: 3px;
            height: 30px;
        }
        
        .security-badges {
            flex-direction: column;
            gap: 10px;
        }
        
        .section-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .section-icon {
            margin-right: 0;
        }
        
        .section-content {
            padding: 20px;
        }
        
        .payment-method-header {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .payment-icon {
            margin-right: 0;
        }
        
        .sticky-summary {
            position: static;
            margin-top: 30px;
        }
        
        .order-items-section {
            max-height: none;
        }
        
        .qr-code-image {
            max-width: 150px;
        }
        
        .upi-id-section {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
        
        .help-content {
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .step-circle {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .section-content {
            padding: 15px;
        }
        
        .payment-method-content {
            padding: 15px;
        }
        
        .summary-content {
            font-size: 14px;
        }
        
        .total-value {
            font-size: 1.25rem;
        }
        
        .btn-place-order-enhanced {
            padding: 15px;
            font-size: 1rem;
        }
    }
</style>

<script>
// Enhanced Checkout Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Trigger welcome fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        setTimeout(() => {
            window.enhancedFireworks.triggerWelcomeFireworks();
        }, 1000);
    }
    
    // Initialize checkout functionality
    initializeCheckoutEnhanced();
    
    console.log('💳 Enhanced Checkout Page initialized successfully!');
});

// Store base total
const baseTotal = {{ $grandTotal }};

// Initialize checkout functionality
function initializeCheckoutEnhanced() {
    // Initialize payment charge calculation
    updatePaymentChargeEnhanced();
    
    // Show details for initially selected payment method
    const initiallySelected = document.querySelector('input[name="payment_method"]:checked');
    if (initiallySelected) {
        showPaymentDetails(initiallySelected);
    }
    
    // Add event listeners
    addEventListeners();
}

// Add event listeners
function addEventListeners() {
    // Payment method change
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.addEventListener('change', function() {
            updatePaymentChargeEnhanced();
            showPaymentDetails(this);
            
            // Trigger fireworks
            if (typeof window.enhancedFireworks !== 'undefined') {
                window.enhancedFireworks.triggerOnAction(this.closest('.payment-method-enhanced'));
            }
        });
    });
    
    // Mobile number formatting
    const mobileInput = document.getElementById('customer_mobile');
    if (mobileInput) {
        mobileInput.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            this.value = value;
            
            // Visual feedback
            if (value.length === 10) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });
    }
    
    // PIN code formatting
    const pincodeInput = document.getElementById('pincode');
    if (pincodeInput) {
        pincodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    }
    
    // Form submission
    const checkoutForm = document.getElementById('checkoutFormEnhanced');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            handleFormSubmission(e, this);
        });
    }
}

// Update payment charge
function updatePaymentChargeEnhanced() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (selectedMethod) {
        const extraCharge = parseFloat(selectedMethod.getAttribute('data-extra-charge')) || 0;
        const extraPercentage = parseFloat(selectedMethod.getAttribute('data-extra-percentage')) || 0;
        
        let paymentCharge = extraCharge + (baseTotal * extraPercentage / 100);
        
        const chargeRow = document.getElementById('payment-charge-row');
        const chargeValue = document.getElementById('payment-charge');
        const grandTotalElement = document.getElementById('grand-total');
        
        if (paymentCharge > 0) {
            chargeRow.style.display = 'flex';
            chargeValue.textContent = '+₹' + paymentCharge.toFixed(2);
        } else {
            chargeRow.style.display = 'none';
        }
        
        const grandTotal = baseTotal + paymentCharge;
        grandTotalElement.textContent = '₹' + grandTotal.toFixed(2);
    }
}

// Show payment details
function showPaymentDetails(selectedInput) {
    // Hide all payment details
    document.querySelectorAll('.payment-details').forEach(detail => {
        detail.style.display = 'none';
    });
    
    // Show selected payment details
    const selectedMethod = selectedInput.closest('.payment-method-enhanced');
    const paymentDetails = selectedMethod.querySelector('.payment-details');
    if (paymentDetails) {
        paymentDetails.style.display = 'block';
        
        // Add entrance animation
        paymentDetails.style.opacity = '0';
        paymentDetails.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            paymentDetails.style.transition = 'all 0.3s ease';
            paymentDetails.style.opacity = '1';
            paymentDetails.style.transform = 'translateY(0)';
        }, 100);
    }
}

// Copy UPI ID function
function copyUpiId(upiId) {
    navigator.clipboard.writeText(upiId).then(function() {
        // Show success notification
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('UPI ID copied to clipboard!', 'success', 2000);
        }
        
        // Trigger fireworks
        if (typeof window.enhancedFireworks !== 'undefined') {
            window.enhancedFireworks.createCelebrationBurst();
        }
    }).catch(function(err) {
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Failed to copy UPI ID', 'error', 2000);
        }
    });
}

// Handle form submission
function handleFormSubmission(e, form) {
    const mobile = document.getElementById('customer_mobile').value;
    const pincode = document.getElementById('pincode').value;
    
    // Validate mobile number
    if (mobile.length !== 10) {
        e.preventDefault();
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Please enter a valid 10-digit mobile number', 'error', 3000);
        }
        document.getElementById('customer_mobile').focus();
        return false;
    }
    
    // Validate PIN code
    if (pincode.length !== 6) {
        e.preventDefault();
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Please enter a valid 6-digit PIN code', 'error', 3000);
        }
        document.getElementById('pincode').focus();
        return false;
    }
    
    // Check if payment method is selected
    if (!document.querySelector('input[name="payment_method"]:checked')) {
        e.preventDefault();
        if (typeof window.showEnhancedNotification === 'function') {
            window.showEnhancedNotification('Please select a payment method', 'error', 3000);
        }
        return false;
    }
    
    // Add formatted mobile number for WhatsApp
    const mobileWithCountryCode = '+91' + mobile;
    
    let formattedMobileInput = document.getElementById('formatted_mobile');
    if (!formattedMobileInput) {
        formattedMobileInput = document.createElement('input');
        formattedMobileInput.type = 'hidden';
        formattedMobileInput.id = 'formatted_mobile';
        formattedMobileInput.name = 'formatted_mobile';
        form.appendChild(formattedMobileInput);
    }
    formattedMobileInput.value = mobileWithCountryCode;
    
    // Show loading state
    const submitBtn = form.querySelector('.btn-place-order-enhanced');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-content').style.display = 'none';
        submitBtn.querySelector('.btn-loading').style.display = 'flex';
    }
    
    // Trigger celebration fireworks
    if (typeof window.enhancedFireworks !== 'undefined') {
        window.enhancedFireworks.createCelebrationBurst();
    }
    
    // Show processing notification
    if (typeof window.showEnhancedNotification === 'function') {
        window.showEnhancedNotification('Processing your order...', 'info', 3000);
    }
    
    // Prevent multiple submissions
    form.removeEventListener('submit', handleFormSubmission);
    
    console.log('Order being processed with mobile:', mobileWithCountryCode);
}

// QR Code enlargement (if needed)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('qr-code-image')) {
        // Create modal for QR code enlargement
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            cursor: pointer;
        `;
        
        const img = document.createElement('img');
        img.src = e.target.src;
        img.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        `;
        
        modal.appendChild(img);
        document.body.appendChild(modal);
        
        modal.addEventListener('click', function() {
            document.body.removeChild(modal);
        });
    }
});
</script>
@endsection
