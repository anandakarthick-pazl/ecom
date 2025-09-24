@extends('layouts.app')

@section('title', 'Checkout - Herbal Bliss')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                       id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="customer_mobile" class="form-label">WhatsApp Mobile Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fab fa-whatsapp"></i> +91
                                    </span>
                                    <input type="tel" class="form-control @error('customer_mobile') is-invalid @enderror" 
                                           id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile') }}" 
                                           pattern="[0-9]{10}" maxlength="10" required 
                                           placeholder="9003096885">
                                    @error('customer_mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Enter 10-digit mobile number (auto-saved as +91 prefix for WhatsApp)
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">
                                Email Address (Optional)
                                <i class="fas fa-info-circle text-primary" 
                                   data-bs-toggle="tooltip" 
                                   title="Enter email to receive order updates and invoice PDF"></i>
                            </label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                   id="customer_email" name="customer_email" value="{{ old('customer_email') }}" 
                                   placeholder="your@email.com">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-envelope"></i> Receive order updates and invoice PDF via email
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Delivery Address *</label>
                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                      id="delivery_address" name="delivery_address" rows="3" required>{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="pincode" class="form-label">PIN Code *</label>
                                <input type="text" class="form-control @error('pincode') is-invalid @enderror" 
                                       id="pincode" name="pincode" value="{{ old('pincode') }}" 
                                       pattern="[0-9]{6}" maxlength="6" required>
                                @error('pincode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2" 
                                      placeholder="Please provide contact details here or Any special instructions for delivery...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                {{-- <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        @if($paymentMethods->count() > 0)
                            <div class="payment-methods">
                                @foreach($paymentMethods as $method)
                                    <div class="form-check payment-method-option mb-3 p-3 border rounded">
                                        <input class="form-check-input" type="radio" 
                                               name="payment_method" 
                                               id="payment_{{ $method->id }}" 
                                               value="{{ $method->id }}"
                                               data-extra-charge="{{ $method->extra_charge }}"
                                               data-extra-percentage="{{ $method->extra_charge_percentage }}"
                                               {{ old('payment_method', $loop->first ? $method->id : '') == $method->id ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label w-100" for="payment_{{ $method->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        @if($method->hasImage())
                                                            <img src="{{ $method->getImageUrl() }}" 
                                                                 alt="{{ $method->display_name }}" 
                                                                 class="payment-method-image me-3"
                                                                 style="width: 40px; height: 40px; object-fit: contain; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                        @else
                                                            <i class="{{ $method->getIcon() }} text-{{ $method->getColor() }} me-3" style="font-size: 1.8rem;"></i>
                                                        @endif
                                                        <div>
                                                            <strong>{{ $method->display_name }}</strong>
                                                        </div>
                                                    </div>
                                                    @if($method->description)
                                                        <br><small class="text-muted">{{ $method->description }}</small>
                                                    @endif
                                                    
                                                    @if($method->type === 'bank_transfer' && $method->bank_details)
                                                        <div class="bank-details mt-2 small text-muted" style="display: none;">
                                                            <strong>Bank Details:</strong><br>
                                                            Bank: {{ $method->bank_details['bank_name'] ?? '' }}<br>
                                                            Account: {{ $method->bank_details['account_number'] ?? '' }}<br>
                                                            IFSC: {{ $method->bank_details['ifsc_code'] ?? '' }}<br>
                                                            Name: {{ $method->bank_details['account_name'] ?? '' }}
                                                        </div>
                                                    @endif
                                                    
                                                    @if($method->type === 'upi')
                                                        <div class="upi-details mt-2 small" style="display: none;">
                                                            @if($method->upi_id)
                                                                <strong>UPI ID:</strong> {{ $method->upi_id }}<br>
                                                            @endif
                                                            @if($method->upi_qr_code)
                                                            <img src="{{ $method->getQrCodeUrl() }}" 
                                                            alt="UPI QR Code" 
                                                            class="img-fluid mt-2" 
                                                            style="max-width: 150px;">
                                                            @endif
                                                        </div>
                                                    @endif
                                                    
                                                    @if($method->type === 'gpay')
                                                        <div class="gpay-details mt-2 small" style="display: none;">
                                                            @if($method->upi_id)
                                                                <strong>Google Pay UPI ID:</strong> {{ $method->upi_id }}<br>
                                                            @endif
                                                            @if($method->upi_qr_code)
                                                                <div class="text-center mt-2">
                                                                    <img src="{{ $method->getQrCodeUrl() }}" 
                                                                         alt="Google Pay QR Code" 
                                                                         class="img-fluid border rounded" 
                                                                         style="max-width: 200px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                                    <br>
                                                                    <small class="text-muted mt-1 d-block">
                                                                        <i class="fab fa-google-pay text-danger"></i> 
                                                                        Scan with Google Pay to pay
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    @if($method->extra_charge > 0 || $method->extra_charge_percentage > 0)
                                                        <small class="text-muted">
                                                            @if($method->extra_charge > 0)
                                                                +₹{{ number_format($method->extra_charge, 2) }}
                                                            @endif
                                                            @if($method->extra_charge_percentage > 0)
                                                                @if($method->extra_charge > 0) + @endif
                                                                {{ $method->extra_charge_percentage }}%
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No payment methods available. Please contact support.
                            </div>
                        @endif
                    </div>
                </div> --}}
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estimate Summary</h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                    @php
                        $hasDiscount = $item->product->discount_price && $item->product->discount_price > 0;
                        $offerPrice = $hasDiscount ? $item->product->discount_price : $item->product->price;
                        $originalPrice = $item->product->price;
                    @endphp
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-2">{{ $item->product->name }}</h6>
                                @if($hasDiscount)
                                    <span class="badge bg-success text-white small">OFFER</span>
                                @endif
                            </div>
                            <div class="product-pricing-info">
                                @if($hasDiscount)
                                    <small class="text-muted">Qty: {{ $item->quantity }} × 
                                        <span class="text-success fw-bold">₹{{ number_format($offerPrice, 2) }}</span>
                                        <span class="text-decoration-line-through text-muted ms-1">₹{{ number_format($originalPrice, 2) }}</span>
                                    </small>
                                    <br>
                                    <small class="text-success">
                                        <i class="fas fa-tag"></i> You save ₹{{ number_format(($originalPrice - $offerPrice) * $item->quantity, 2) }}
                                    </small>
                                @else
                                    <small class="text-muted">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}</small>
                                @endif
                            </div>
                            @if($item->product->tax_percentage > 0)
                                <br><small class="text-muted">GST: {{ $item->product->tax_percentage }}% = ₹{{ number_format($item->product->getTaxAmount($item->price) * $item->quantity, 2) }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($hasDiscount)
                                <div class="offer-pricing">
                                    <strong class="text-success">₹{{ number_format($item->total, 2) }}</strong>
                                    <br>
                                    <small class="text-decoration-line-through text-muted">
                                        ₹{{ number_format($originalPrice * $item->quantity, 2) }}
                                    </small>
                                </div>
                            @else
                                <strong>₹{{ number_format($item->total, 2) }}</strong>
                            @endif
                            @if($item->product->tax_percentage > 0)
                                <br><small class="text-muted">+Tax</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    
                    <hr>
                    
                    @php
                        // Calculate total savings from discounts
                        $totalSavings = 0;
                        foreach($cartItems as $item) {
                            if($item->product->discount_price && $item->product->discount_price > 0) {
                                $originalPrice = $item->product->price;
                                $offerPrice = $item->product->discount_price;
                                $totalSavings += ($originalPrice - $offerPrice) * $item->quantity;
                            }
                        }
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($totalSavings > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success"><i class="fas fa-tag"></i> Total Savings:</span>
                        <span class="text-success fw-bold">₹{{ number_format($totalSavings, 2) }}</span>
                    </div>
                    @endif
                    
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
                        
                        // Only include delivery charge if delivery is enabled
                        $actualDeliveryCharge = ($deliveryInfo['enabled'] ?? false) ? $deliveryCharge : 0;
                        $grandTotal = $subtotal + $totalTax + $actualDeliveryCharge - $discount;
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST:</span>
                        <span>₹{{ number_format($cgstAmount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST:</span>
                        <span>₹{{ number_format($sgstAmount, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Tax:</span>
                        <span>₹{{ number_format($totalTax, 2) }}</span>
                    </div>
                    
                    {{-- @if($deliveryInfo['enabled'])
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Charge:</span>
                        <span>
                            @if($deliveryCharge == 0)
                                <span class="text-success">FREE</span>
                            @else
                                ₹{{ number_format($deliveryCharge, 2) }}
                            @endif
                        </span>
                    </div>
                    @endif
                    
                    @if($deliveryInfo['enabled'] && $deliveryInfo['free_delivery_enabled'] && $deliveryInfo['amount_needed_for_free'] > 0)
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-gift"></i> 
                            Add ₹{{ number_format($deliveryInfo['amount_needed_for_free'], 2) }} more for <strong>FREE delivery!</strong>
                        </div>
                    @endif --}}
                    
                    @if($discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success"><i class="fas fa-tag"></i> Coupon Discount ({{ $appliedCoupon['code'] ?? 'Coupon' }}):</span>
                        <span class="text-success fw-bold">-₹{{ number_format($discount, 2) }}</span>
                    </div>
                    @endif
                    
                    {{-- <div class="d-flex justify-content-between mb-2" id="payment-charge-row" style="display: none;">
                        <span>Payment Charge:</span>
                        <span id="payment-charge">+₹0.00</span>
                    </div> --}}
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="grand-total">₹{{ number_format($grandTotal, 2) }}</strong>
                    </div>
                    
                    {{-- @if($deliveryInfo['enabled'] && $deliveryCharge == 0 && $deliveryInfo['free_delivery_enabled'])
                        <div class="alert alert-success py-2">
                            <small><i class="fas fa-check"></i> You're getting FREE delivery!</small>
                        </div>
                    @endif
                    
                    @if($deliveryInfo['enabled'] && $deliveryInfo['time_estimate'])
                        <div class="alert alert-light py-2">
                            <small>
                                <i class="fas fa-clock text-primary"></i> 
                                <strong>Estimated Delivery:</strong> {{ $deliveryInfo['time_estimate'] }}
                            </small>
                        </div>
                    @endif
                    
                    @if($deliveryInfo['enabled'] && $deliveryInfo['description'])
                        <div class="alert alert-light py-2">
                            <small>
                                <i class="fas fa-info-circle text-info"></i> 
                                {{ $deliveryInfo['description'] }}
                            </small>
                        </div>
                    @endif --}}
                    
                    <button type="submit" form="checkoutForm" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock"></i> Place Estimate
                    </button>
                    
                    {{-- <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> Secure checkout with 256-bit SSL encryption
                        </small>
                    </div> --}}
                </div>
            </div>
            
            {{-- <div class="card">
                <div class="card-body text-center">
                    <h6>Need Help?</h6>
                    <p class="mb-2">
                        <i class="fas fa-phone text-primary"></i> +91 9876543210
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope text-primary"></i> support@herbalbliss.com
                    </p>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@push('styles')
<style>
/* Offer pricing styles */
.product-pricing-info {
    line-height: 1.4;
}

.offer-pricing {
    text-align: right;
}

.text-decoration-line-through {
    text-decoration: line-through !important;
    opacity: 0.7;
}

.offer-pricing strong {
    font-size: 1.1rem;
}

.offer-pricing small {
    font-size: 0.85rem;
}

/* Enhanced savings highlight */
.text-success i {
    color: #28a745 !important;
}

/* Google Pay QR Code Display Styles */
.gpay-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dc3545;
    border-radius: 12px;
    padding: 15px;
    margin-top: 10px;
    animation: fadeIn 0.3s ease-in;
}

.gpay-details img {
    transition: transform 0.3s ease;
}

.gpay-details img:hover {
    transform: scale(1.05);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Payment method selection enhancement */
.payment-method-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-option:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.payment-method-option input:checked + label {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

/* Payment method image styling */
.payment-method-image {
    transition: transform 0.2s ease;
    background: #fff;
    border: 1px solid #e9ecef;
}

.payment-method-image:hover {
    transform: scale(1.05);
}

.payment-method-option input:checked + label .payment-method-image {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.25) !important;
}

/* WhatsApp Mobile Number Input Enhancement */
.input-group .input-group-text.bg-success {
    border-color: #28a745;
    font-weight: 600;
}

.input-group .form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.67-.17C3.14 6.55 3 6.11 3 6c0-.55-.45-1-1-1s-1 .45-1 1c0 .14.11.2.23.2C1.48 6.2 1.8 6.47 2.3 6.73z'/%3e%3c/svg%3e");
}

/* Commission Section Styling */
.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.commission-preview {
    animation: slideInDown 0.3s ease-in-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6-1.6 1.6'/%3e%3cpath d='m5.8 4.6-1.6 1.6'/%3e%3c/svg%3e");
}

.commission-amount-highlight {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.75rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
}
</style>
@endpush

@push('scripts')
<script>
// Initialize tooltips
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize payment method selection
    updatePaymentCharge();
    
    // Show details for initially selected payment method
    const initiallySelected = $('input[name="payment_method"]:checked');
    if (initiallySelected.length > 0) {
        $('.bank-details, .upi-details, .gpay-details').hide();
        const selectedMethod = initiallySelected.closest('.payment-method-option');
        selectedMethod.find('.bank-details, .upi-details, .gpay-details').show();
    }
    
    // Initialize commission functionality
    initializeCommission();
});

// Store base total
const baseTotal = {{ $grandTotal }};

// Handle payment method change
$('input[name="payment_method"]').on('change', function() {
    updatePaymentCharge();
    
    // Show/hide bank details, UPI details, and Google Pay details
    $('.bank-details, .upi-details, .gpay-details').hide();
    const selectedMethod = $(this).closest('.payment-method-option');
    selectedMethod.find('.bank-details, .upi-details, .gpay-details').show();
});

// Update payment charge
function updatePaymentCharge() {
    const selectedMethod = $('input[name="payment_method"]:checked');
    
    if (selectedMethod.length > 0) {
        const extraCharge = parseFloat(selectedMethod.data('extra-charge')) || 0;
        const extraPercentage = parseFloat(selectedMethod.data('extra-percentage')) || 0;
        
        let paymentCharge = extraCharge + (baseTotal * extraPercentage / 100);
        
        if (paymentCharge > 0) {
            $('#payment-charge-row').show();
            $('#payment-charge').text('+₹' + paymentCharge.toFixed(2));
        } else {
            $('#payment-charge-row').hide();
        }
        
        const grandTotal = baseTotal + paymentCharge;
        $('#grand-total').text('₹' + grandTotal.toFixed(2));
    }
}

// Auto-format mobile number (Indian format with +91)
$('#customer_mobile').on('input', function() {
    let value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    this.value = value;
    
    // Visual feedback for user
    if (value.length === 10) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $(this).next('.invalid-feedback').hide();
    } else {
        $(this).removeClass('is-valid');
    }
});

// Auto-format PIN code
$('#pincode').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});

// Form validation and mobile number formatting
$('#checkoutForm').on('submit', function(e) {
    const mobile = $('#customer_mobile').val();
    const pincode = $('#pincode').val();
    
    if (mobile.length !== 10) {
        e.preventDefault();
        showToast('Please enter a valid 10-digit mobile number', 'error');
        $('#customer_mobile').focus();
        return false;
    }
    
    if (pincode.length !== 6) {
        e.preventDefault();
        showToast('Please enter a valid 6-digit PIN code', 'error');
        $('#pincode').focus();
        return false;
    }
    
    // Check if payment method is selected
    // if ($('input[name="payment_method"]:checked').length === 0) {
    //     e.preventDefault();
    //     showToast('Please select a payment method', 'error');
    //     return false;
    // }
    
    // Validate commission fields if enabled
    // Commission functionality removed for online orders
    
    // AUTO-ADD +91 TO MOBILE NUMBER FOR WHATSAPP
    const mobileWithCountryCode = '+91' + mobile;
    
    // Create a hidden field to send the formatted mobile number
    if ($('#formatted_mobile').length === 0) {
        $('<input>').attr({
            type: 'hidden',
            id: 'formatted_mobile',
            name: 'formatted_mobile',
            value: mobileWithCountryCode
        }).appendTo(this);
    } else {
        $('#formatted_mobile').val(mobileWithCountryCode);
    }
    
    console.log('Mobile number formatted for WhatsApp:', mobileWithCountryCode);
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing Order...');
    
    // Prevent multiple submissions
    $(this).off('submit');
});

// Initialize on page load
initializePage();

// Show toast message
function showToast(message, type = 'success') {
    // Simple alert for now - you can enhance this with proper toast library
    alert(message);
}


</script>
@endpush
@endsection
