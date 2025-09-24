@extends('layouts.app')

@section('title', 'Order Success - Herbal Bliss')

@section('content')
<!-- Razorpay Payment Modal -->
@if(session('initiate_payment') && session('payment_method') === 'razorpay' && $order->payment_status !== 'paid')
    <div class="modal fade" id="razorpayModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card text-primary"></i> Complete Payment
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-lock fa-3x text-primary"></i>
                    </div>
                    <h6>Secure Payment via Razorpay</h6>
                    <p class="mb-3">Amount to Pay: <strong class="text-primary">₹{{ number_format($order->total, 2) }}</strong></p>
                    <p class="text-muted small">You will be redirected to Razorpay's secure payment gateway</p>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg" id="pay-now-btn">
                            <i class="fas fa-credit-card"></i> Pay Now
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Pay Later
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> 100% Secure Payment
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Success Message with Animation -->
            <div class="card border-0 shadow mb-4" style="animation: fadeInUp 0.6s ease-out;">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <!-- Simple Working Green Checkmark -->
                        <div class="success-checkmark-working">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-success mb-3 fw-bold">🎉 Estimate Placed Successfully!</h2>
                    <p class="lead mb-4">Thank you for your Estimate.</p>
                    
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-start">
                                <strong class="text-success">📋 Estimate Number:</strong><br>
                                <span class="h4 text-primary fw-bold">{{ $order->order_number }}</span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <strong class="text-success">📅 Estimate Date:</strong><br>
                                <span class="h6">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Status Timeline -->
                    {{-- <div class="mt-4">
                        <div class="row">
                            <div class="col-3 text-center">
                                <div class="timeline-step active">
                                    <div class="timeline-icon bg-success text-white">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <small class="text-success fw-bold">Order Placed</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div class="timeline-step">
                                    <div class="timeline-icon bg-light text-muted">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <small class="text-muted">Processing</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div class="timeline-step">
                                    <div class="timeline-icon bg-light text-muted">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <small class="text-muted">Shipped</small>
                                </div>
                            </div>
                            <div class="col-3 text-center">
                                <div class="timeline-step">
                                    <div class="timeline-icon bg-light text-muted">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <small class="text-muted">Delivered</small>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            
            <!-- Order Details Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Estimate Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-shipping-fast"></i> Contact Information</h6>
                            <div class="border-start border-primary border-3 ps-3">
                                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                                <p class="mb-1"><i class="fas fa-phone text-muted"></i> {{ $order->customer_mobile }}</p>
                                @if($order->customer_email)
                                    <p class="mb-1"><i class="fas fa-envelope text-muted"></i> {{ $order->customer_email }}</p>
                                @endif
                                <p class="mb-1"><i class="fas fa-map-marker-alt text-muted"></i> {{ $order->delivery_address }}</p>
                                <p class="mb-0">{{ $order->city }}, {{ $order->state }} {{ $order->pincode }}</p>
                            </div>
                            
                            @if($order->notes)
                            <div class="mt-3">
                                <h6 class="text-primary"><i class="fas fa-sticky-note"></i> Order Notes</h6>
                                <div class="alert alert-info">
                                    <small>{{ $order->notes }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-shopping-bag"></i> Estimate Items</h6>
                            <div class="order-items">
                                @foreach($order->items as $item)
                                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                    <div class="flex-grow-1">
                                        <strong class="text-dark">{{ $item->product_name }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-boxes"></i> Qty: {{ $item->quantity }} × 
                                            <i class="fas fa-rupee-sign"></i>{{ number_format($item->price, 2) }}
                                            @if($item->tax_percentage > 0)
                                                <br><i class="fas fa-percent"></i> Tax: {{ $item->tax_percentage }}% = 
                                                <i class="fas fa-rupee-sign"></i>{{ number_format($item->tax_amount, 2) }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-success"><i class="fas fa-rupee-sign"></i>{{ number_format($item->total, 2) }}</strong>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-calculator"></i> Subtotal:</span>
                                    <span><i class="fas fa-rupee-sign"></i>{{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                
                                @if($order->discount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success"><i class="fas fa-percent"></i> Discount:</span>
                                    <span class="text-success">-<i class="fas fa-rupee-sign"></i>{{ number_format($order->discount, 2) }}</span>
                                </div>
                                @endif
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-receipt"></i> CGST:</span>
                                    <span><i class="fas fa-rupee-sign"></i>{{ number_format($order->cgst_amount, 2) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-receipt"></i> SGST:</span>
                                    <span><i class="fas fa-rupee-sign"></i>{{ number_format($order->sgst_amount, 2) }}</span>
                                </div>
                                
                                {{-- <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fas fa-shipping-fast"></i> Delivery:</span>
                                    <span>
                                        @if($order->delivery_charge == 0)
                                            <span class="text-success fw-bold">FREE</span>
                                        @else
                                            <i class="fas fa-rupee-sign"></i>{{ number_format($order->delivery_charge, 2) }}
                                        @endif
                                    </span>
                                </div> --}}
                                
                                <hr class="my-2">
                                
                                <div class="d-flex justify-content-between">
                                    <strong class="text-primary"><i class="fas fa-receipt"></i> Total:</strong>
                                    <strong class="text-primary fs-5"><i class="fas fa-rupee-sign"></i>{{ number_format($order->total, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <h6>Estimate Status</h6>
                            <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-truck fa-2x text-info mb-2"></i>
                            <h6>Estimated Delivery</h6>
                            <small class="text-muted">3-5 business days</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            @php
                                $paymentIcon = match($order->payment_method) {
                                    'razorpay' => 'fas fa-credit-card',
                                    'cod' => 'fas fa-money-bill-wave',
                                    'bank_transfer' => 'fas fa-university',
                                    'upi' => 'fas fa-mobile-alt',
                                    'gpay' => 'fab fa-google-pay',
                                    default => 'fas fa-wallet'
                                };
                                $paymentColor = match($order->payment_method) {
                                    'razorpay' => 'primary',
                                    'cod' => 'success',
                                    'bank_transfer' => 'info',
                                    'upi' => 'warning',
                                    'gpay' => 'danger',
                                    default => 'secondary'
                                };
                                $paymentName = match($order->payment_method) {
                                    'razorpay' => 'Online Payment',
                                    'cod' => 'Cash on Delivery',
                                    'bank_transfer' => 'Bank Transfer',
                                    'upi' => 'UPI Payment',
                                    'gpay' => 'Google Pay',
                                    default => 'Payment'
                                };
                            @endphp
                            <i class="{{ $paymentIcon }} fa-2x text-{{ $paymentColor }} mb-2"></i>
                            <h6>Payment Method</h6>
                            <small class="text-muted">{{ $paymentName }}</small>
                            @if($order->payment_status)
                                <br>
                                <span class="badge bg-{{ $order->payment_status_color }} mt-1">
                                    {{ $order->payment_status_text }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div> --}}
            
            <!-- Invoice Download Section -->
            <div class="card bg-light border-0 shadow-sm mt-4">
                <div class="card-body text-center">
                    <h5 class="mb-3"><i class="fas fa-download text-primary"></i> Download Your Estimate</h5>
                    <div class="d-flex justify-content-center gap-3">
                        @php
                            $companyId = $order->company_id ?? session('selected_company_id');
                            $defaultFormat = \App\Models\AppSetting::getForTenant('default_bill_format', $companyId) ?? 'a4_sheet';
                            $thermalEnabled = \App\Models\AppSetting::getForTenant('thermal_printer_enabled', $companyId) ?? false;
                            $a4Enabled = \App\Models\AppSetting::getForTenant('a4_sheet_enabled', $companyId) ?? true;
                        @endphp
                        
                        @if($defaultFormat === 'thermal' || $thermalEnabled)
                            <a href="{{ route('invoice.download', ['orderNumber' => $order->order_number, 'format' => 'thermal']) }}" 
                               class="btn btn-success btn-lg" target="_blank">
                                <i class="fas fa-receipt"></i> Thermal Estimate
                            </a>
                        @endif
                        
                        @if($defaultFormat === 'a4_sheet' || $a4Enabled)
                            <a href="{{ route('invoice.download', ['orderNumber' => $order->order_number, 'format' => 'a4_sheet']) }}" 
                               class="btn btn-info btn-lg" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF Estimate
                            </a>
                        @endif
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="fas fa-info-circle"></i> Keep this Estimate for your records and warranty claims
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('track.order') }}" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-search"></i> Track Your Estimate
                </a>
                <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-home"></i> Continue Shopping
                </a>
            </div>
            
            <!-- Share Section -->
            <div class="alert alert-light border-0 shadow-sm mt-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1"><i class="fas fa-share-alt text-primary"></i> Share with Friends!</h6>
                        <p class="mb-0 text-muted">Love our products? Share them with your friends and family.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <a href="https://wa.me/?text=Just ordered amazing herbal products! Check them out: {{ route('shop') }}" 
                           class="btn btn-success" target="_blank">
                            <i class="fab fa-whatsapp"></i> Share on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Simple Working Green Checkmark - GUARANTEED TO WORK */
.success-checkmark-working {
    width: 100px;
    height: 100px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px auto;
    box-shadow: 0 4px 20px rgba(40, 167, 69, 0.3);
    animation: checkmark-bounce 0.6s ease-in-out;
}

.success-checkmark-working i {
    color: white;
    font-size: 50px;
    font-weight: bold;
}

/* Simple bounce animation */
@keyframes checkmark-bounce {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 40px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* Timeline Styles */
.timeline-step {
    position: relative;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-size: 14px;
}

.timeline-step::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e9ecef;
    z-index: -1;
}

.timeline-step:last-child::after {
    display: none;
}

.timeline-step.active::after {
    background: #28a745;
}
</style>
@endpush
@endsection

@if(session('initiate_payment') && session('payment_method') === 'razorpay' && $order->payment_status !== 'paid')
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
/* 
 * PURE JAVASCRIPT PAYMENT SYSTEM - VERSION 2.0 
 * COMPLETELY JQUERY-FREE - NO DEPENDENCIES 
 * Last Updated: {{ date('Y-m-d H:i:s') }}
 * Cache Buster: {{ time() }}
 */

// Immediate startup - NO jQuery checks at all
console.clear(); // Clear any previous console messages
console.log('%c=== PURE JS PAYMENT SYSTEM v2.0 ===', 'color: green; font-weight: bold; font-size: 16px;');
console.log('%cOrder Page - PURE JAVASCRIPT (No jQuery Required)', 'color: blue; font-weight: bold;');
console.log('%cTimestamp: {{ date("Y-m-d H:i:s") }}', 'color: gray;');

// Completely self-contained payment system
(function() {
    'use strict';
    
    // Utility functions
    const utils = {
        getElement: function(id) {
            return document.getElementById(id);
        },
        
        showModal: function(modalId) {
            const modal = this.getElement(modalId);
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('show');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('aria-hidden', 'false');
                
                // Create backdrop
                let backdrop = document.getElementById('payment-backdrop');
                if (!backdrop) {
                    backdrop = document.createElement('div');
                    backdrop.id = 'payment-backdrop';
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040;';
                    document.body.appendChild(backdrop);
                }
                
                document.body.classList.add('modal-open');
                console.log('✅ Modal shown successfully:', modalId);
            }
        },
        
        hideModal: function(modalId) {
            const modal = this.getElement(modalId);
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                modal.removeAttribute('aria-modal');
                
                const backdrop = document.getElementById('payment-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                document.body.classList.remove('modal-open');
                console.log('✅ Modal hidden successfully:', modalId);
            }
        },
        
        updateButton: function(buttonId, disabled, html) {
            const button = this.getElement(buttonId);
            if (button) {
                button.disabled = disabled;
                button.innerHTML = html;
                console.log('✅ Button updated:', buttonId, disabled ? 'disabled' : 'enabled');
            }
        },
        
        makeRequest: function(url, method, data, onSuccess, onError) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-CSRF-TOKEN', data._token);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            onSuccess(response);
                        } catch (e) {
                            console.error('❌ Response parsing error:', e);
                            onError('Invalid response format');
                        }
                    } else {
                        console.error('❌ Request failed with status:', xhr.status);
                        onError('Request failed: ' + xhr.status);
                    }
                }
            };
            
            const params = Object.keys(data).map(key => 
                encodeURIComponent(key) + '=' + encodeURIComponent(data[key])
            ).join('&');
            
            console.log('📡 Making request to:', url);
            xhr.send(params);
        },
        
        showLoader: function() {
            let loader = this.getElement('js-payment-loader');
            if (!loader) {
                loader = document.createElement('div');
                loader.id = 'js-payment-loader';
                loader.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                        <div style="text-align: center; color: white; padding: 30px; background: rgba(0,0,0,0.8); border-radius: 10px;">
                            <div style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                            <h4>Verifying Payment...</h4>
                            <p>Please wait while we confirm your payment</p>
                        </div>
                    </div>
                `;
                
                // Add CSS animation if not exists
                if (!document.getElementById('js-spinner-style')) {
                    const style = document.createElement('style');
                    style.id = 'js-spinner-style';
                    style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
                    document.head.appendChild(style);
                }
                
                document.body.appendChild(loader);
            }
            loader.style.display = 'block';
            console.log('⏳ Payment loader shown');
        },
        
        hideLoader: function() {
            const loader = this.getElement('js-payment-loader');
            if (loader) {
                loader.remove();
                console.log('✅ Payment loader hidden');
            }
        }
    };
    
    // Payment verification function
    window.verifyPayment = function(paymentResponse) {
        console.log('💳 Starting payment verification...');
        utils.showLoader();
        
        const data = {
            razorpay_payment_id: paymentResponse.razorpay_payment_id,
            razorpay_order_id: paymentResponse.razorpay_order_id,
            razorpay_signature: paymentResponse.razorpay_signature,
            order_id: {{ $order->id }},
            _token: '{{ csrf_token() }}'
        };
        
        utils.makeRequest(
            '{{ route("razorpay.verify-payment") }}',
            'POST',
            data,
            function(response) {
                console.log('✅ Payment verification response received');
                if (response.success) {
                    console.log('✅ Payment verified successfully! Reloading page...');
                    window.location.reload();
                } else {
                    utils.hideLoader();
                    alert('Payment verification failed: ' + (response.message || 'Unknown error'));
                    utils.showModal('razorpayModal');
                }
            },
            function(error) {
                console.error('❌ Payment verification failed:', error);
                utils.hideLoader();
                alert('Payment verification failed. Please contact support.');
                utils.showModal('razorpayModal');
            }
        );
    };
    
    // Initialize payment system
    function initPaymentSystem() {
        console.log('🚀 Initializing Pure JS Payment System...');
        
        // Show modal immediately
        utils.showModal('razorpayModal');
        
        // Setup pay button
        const payButton = utils.getElement('pay-now-btn');
        if (payButton) {
            payButton.addEventListener('click', function() {
                console.log('💳 Pay Now button clicked - Starting payment process');
                utils.updateButton('pay-now-btn', true, '<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                const orderData = {
                    order_id: {{ $order->id }},
                    _token: '{{ csrf_token() }}'
                };
                
                utils.makeRequest(
                    '{{ route("razorpay.create-order") }}',
                    'POST',
                    orderData,
                    function(response) {
                        console.log('✅ Razorpay order creation response received');
                        if (response.success) {
                            console.log('✅ Razorpay order created successfully, opening payment gateway...');
                            
                            const razorpayOptions = {
                                key: response.key_id,
                                amount: response.amount,
                                currency: response.currency,
                                name: '{{ $globalCompany->company_name ?? "Your Store" }}',
                                description: 'Order #' + response.order_number,
                                order_id: response.razorpay_order_id,
                                prefill: {
                                    name: response.name,
                                    email: response.email || '',
                                    contact: response.contact
                                },
                                theme: {
                                    color: '{{ $globalCompany->primary_color ?? "#2c3e50" }}'
                                },
                                modal: {
                                    ondismiss: function() {
                                        console.log('🚪 Payment gateway dismissed by user');
                                        utils.updateButton('pay-now-btn', false, '<i class="fas fa-credit-card"></i> Pay Now');
                                        utils.showModal('razorpayModal');
                                    }
                                },
                                handler: function(response) {
                                    console.log('💳 Payment completed successfully, starting verification...');
                                    verifyPayment(response);
                                }
                            };
                            
                            utils.hideModal('razorpayModal');
                            const rzp = new Razorpay(razorpayOptions);
                            rzp.open();
                            
                        } else {
                            console.error('❌ Order creation failed:', response.message);
                            alert('Failed to create payment order: ' + (response.message || 'Unknown error'));
                            utils.updateButton('pay-now-btn', false, '<i class="fas fa-credit-card"></i> Pay Now');
                        }
                    },
                    function(error) {
                        console.error('❌ Order creation request failed:', error);
                        alert('Something went wrong. Please try again.');
                        utils.updateButton('pay-now-btn', false, '<i class="fas fa-credit-card"></i> Pay Now');
                    }
                );
            });
            
            console.log('✅ Pay button event listener attached successfully');
        } else {
            console.error('❌ Pay button not found!');
        }
        
        console.log('✅ Pure JS Payment System initialized successfully!');
        console.log('%c=== SYSTEM READY ===', 'color: green; font-weight: bold;');
    }
    
    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPaymentSystem);
    } else {
        initPaymentSystem();
    }
    
})(); // End of self-contained payment system

console.log('%cPure JS Payment System Loaded Successfully!', 'color: green; font-size: 14px; font-weight: bold;');
</script>
@endpush
@endif

