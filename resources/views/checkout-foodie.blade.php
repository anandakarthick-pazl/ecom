@extends('layouts.app-foodie')

@section('title', 'Checkout - ' . ($globalCompany->company_name ?? 'Crackers Store'))
@section('meta_description', 'Complete your order for crackers and fireworks.')

@section('content')

<!-- Page Header -->
<section style="background: linear-gradient(135deg, #fff5f3 0%, #ffe8e3 100%); padding: 40px 0;">
    <div class="container">
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-primary);">Checkout</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Complete your order details</p>
        </div>
    </div>
</section>

<!-- Checkout Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <!-- Display any validation errors -->
        @if($errors->any())
        <div class="alert alert-danger" style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <h5 style="margin-bottom: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</h5>
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <!-- Display success/error messages -->
        @if(session('success'))
        <div class="alert alert-success" style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger" style="background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif
        
        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row">
                <!-- Checkout Form -->
                <div class="col-lg-8">
                    <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Delivery Information</h3>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-weight: 600; margin-bottom: 0.5rem;">Full Name *</label>
                                <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                                       value="{{ old('customer_name') }}" required
                                       style="border: 1px solid var(--border); padding: 12px;">
                                @error('customer_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label style="font-weight: 600; margin-bottom: 0.5rem;">Mobile Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--background); border: 1px solid var(--border);">+91</span>
                                    <input type="text" name="customer_mobile" class="form-control @error('customer_mobile') is-invalid @enderror" 
                                           value="{{ old('customer_mobile') }}" required maxlength="10" pattern="[0-9]{10}"
                                           style="border: 1px solid var(--border); padding: 12px;">
                                </div>
                                @error('customer_mobile')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-weight: 600; margin-bottom: 0.5rem;">Email Address</label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" 
                                   value="{{ old('customer_email') }}"
                                   style="border: 1px solid var(--border); padding: 12px;">
                            @error('customer_email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-weight: 600; margin-bottom: 0.5rem;">Delivery Address *</label>
                            <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" 
                                      rows="3" required
                                      style="border: 1px solid var(--border); padding: 12px;">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label style="font-weight: 600; margin-bottom: 0.5rem;">City *</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                       value="{{ old('city') }}" required
                                       style="border: 1px solid var(--border); padding: 12px;">
                                @error('city')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label style="font-weight: 600; margin-bottom: 0.5rem;">State</label>
                                <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" 
                                       value="{{ old('state') }}"
                                       style="border: 1px solid var(--border); padding: 12px;">
                                @error('state')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label style="font-weight: 600; margin-bottom: 0.5rem;">Pincode *</label>
                                <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror" 
                                       value="{{ old('pincode') }}" required maxlength="6" pattern="[0-9]{6}"
                                       style="border: 1px solid var(--border); padding: 12px;">
                                @error('pincode')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-weight: 600; margin-bottom: 0.5rem;">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      style="border: 1px solid var(--border); padding: 12px;"
                                      placeholder="Any special instructions for delivery...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm);">
                        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Payment Method</h3>
                        
                        @foreach($paymentMethods as $method)
                        <div class="form-check mb-3" style="padding: 1rem; border: 1px solid var(--border); border-radius: 12px;">
                            <input class="form-check-input" type="radio" name="payment_method" 
                                   id="payment_{{ $method->id }}" value="{{ $method->id }}" 
                                   {{ $loop->first ? 'checked' : '' }} required>
                            <label class="form-check-label" for="payment_{{ $method->id }}" style="width: 100%; cursor: pointer;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $method->name }}</strong>
                                        @if($method->description)
                                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">{{ $method->description }}</p>
                                        @endif
                                    </div>
                                    @if($method->icon)
                                        <i class="{{ $method->icon }}" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @endforeach
                        @error('payment_method')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div style="background: white; border-radius: 16px; padding: 2rem; box-shadow: var(--shadow-sm); position: sticky; top: 100px;">
                        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Order Summary</h3>
                        
                        <!-- Cart Items -->
                        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1rem;">
                            @foreach($cartItems as $item)
                            <div class="mb-3" style="padding-bottom: 0.5rem; border-bottom: 1px solid #f0f0f0;">
                                <p style="font-weight: 500; margin: 0 0 0.25rem 0;">{{ $item->product->name }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small style="color: var(--text-secondary);">Qty: {{ $item->quantity }} × </small>
                                        <small style="color: var(--primary-color); font-weight: 600;">₹{{ number_format($item->price, 2) }}</small>
                                        @if($item->product->price > $item->price)
                                            <small style="text-decoration: line-through; color: #999; margin-left: 0.25rem;">₹{{ number_format($item->product->price, 2) }}</small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <div style="font-weight: 600;">₹{{ number_format($item->price * $item->quantity, 2) }}</div>
                                        @if($item->product->price > $item->price)
                                            <small style="text-decoration: line-through; color: #999;">₹{{ number_format($item->product->price * $item->quantity, 2) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <hr>
                        
                        @php
                            // Calculate total MRP and savings
                            $totalMRP = 0;
                            $totalSavings = 0;
                            foreach($cartItems as $item) {
                                $totalMRP += $item->product->price * $item->quantity;
                                if($item->product->price > $item->price) {
                                    $totalSavings += ($item->product->price - $item->price) * $item->quantity;
                                }
                            }
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total MRP</span>
                            <span>₹{{ number_format($totalMRP, 2) }}</span>
                        </div>
                        
                        @if($totalSavings > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--success-color);">
                            <span>Product Discount</span>
                            <span>-₹{{ number_format($totalSavings, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($discount > 0)
                        <div class="d-flex justify-content-between mb-2" style="color: var(--success-color);">
                            <span>Coupon Discount</span>
                            <span>-₹{{ number_format($discount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Charges</span>
                            <span>₹{{ number_format($deliveryCharge, 2) }}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size: 1.25rem; font-weight: 700;">Total Amount</span>
                            <span style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color);">₹{{ number_format($total, 2) }}</span>
                        </div>
                        
                        @if($totalSavings + $discount > 0)
                        <div style="background: #e8f5e9; padding: 8px; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                            <small style="color: var(--success-color); font-weight: 600;">
                                🎉 You saved ₹{{ number_format($totalSavings + $discount, 2) }}!
                            </small>
                        </div>
                        @endif
                        
                        <button type="submit" class="btn-foodie btn-foodie-primary w-100" id="place-order-btn">
                            <span id="btn-text">Place Order</span>
                            <span id="btn-loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                        
                        <p style="text-align: center; color: var(--text-secondary); font-size: 0.85rem; margin-top: 1rem;">
                            <i class="fas fa-lock"></i> Secure Checkout
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Handle form submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        // Get the submit button
        const submitBtn = document.getElementById('place-order-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');
        
        // Prevent double submission
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        // Validate required fields
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Check if at least one payment method is selected
        const paymentMethodSelected = this.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethodSelected) {
            isValid = false;
            alert('Please select a payment method');
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        
        // Log submission for debugging
        console.log('Form is being submitted...');
    });
    
    // Auto-format mobile number
    const mobileInput = document.querySelector('input[name="customer_mobile"]');
    if (mobileInput) {
        mobileInput.addEventListener('input', function(e) {
            // Remove non-digits
            let value = this.value.replace(/\D/g, '');
            
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substr(0, 10);
            }
            
            this.value = value;
        });
    }
    
    // Auto-format pincode
    const pincodeInput = document.querySelector('input[name="pincode"]');
    if (pincodeInput) {
        pincodeInput.addEventListener('input', function(e) {
            // Remove non-digits
            let value = this.value.replace(/\D/g, '');
            
            // Limit to 6 digits
            if (value.length > 6) {
                value = value.substr(0, 6);
            }
            
            this.value = value;
        });
    }
</script>
@endpush
