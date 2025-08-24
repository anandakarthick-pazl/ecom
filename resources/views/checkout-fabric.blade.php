@extends('layouts.app-fabric')

@section('title', 'Checkout - ' . ($globalCompany->company_name ?? 'Your Store'))

@section('content')
<section style="padding: 3rem 0; background: #f8f9fa; min-height: 80vh;">
    <div class="container">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Checkout</h2>
        
        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row">
                <!-- Customer Information -->
                <div class="col-lg-7">
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
                        <h4 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 1.5rem;">Customer Information</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Full Name *</label>
                                <input type="text" 
                                       name="customer_name" 
                                       class="form-control @error('customer_name') is-invalid @enderror" 
                                       value="{{ old('customer_name') }}" 
                                       required
                                       style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                                @error('customer_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Mobile Number *</label>
                                <input type="tel" 
                                       name="customer_mobile" 
                                       class="form-control @error('customer_mobile') is-invalid @enderror" 
                                       value="{{ old('customer_mobile') }}" 
                                       pattern="[0-9]{10}"
                                       maxlength="10"
                                       required
                                       style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                                @error('customer_mobile')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Email Address</label>
                            <input type="email" 
                                   name="customer_email" 
                                   class="form-control @error('customer_email') is-invalid @enderror" 
                                   value="{{ old('customer_email') }}"
                                   style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                            @error('customer_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <h4 style="font-size: 1.3rem; font-weight: 600; margin: 2rem 0 1.5rem;">Delivery Address</h4>
                        
                        <div class="mb-3">
                            <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Street Address *</label>
                            <textarea name="delivery_address" 
                                      class="form-control @error('delivery_address') is-invalid @enderror" 
                                      rows="3" 
                                      required
                                      style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">City *</label>
                                <input type="text" 
                                       name="city" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       value="{{ old('city') }}" 
                                       required
                                       style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                                @error('city')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">State</label>
                                <input type="text" 
                                       name="state" 
                                       class="form-control @error('state') is-invalid @enderror" 
                                       value="{{ old('state') }}"
                                       style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                                @error('state')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Pincode *</label>
                                <input type="text" 
                                       name="pincode" 
                                       class="form-control @error('pincode') is-invalid @enderror" 
                                       value="{{ old('pincode') }}" 
                                       pattern="[0-9]{6}"
                                       maxlength="6"
                                       required
                                       style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                                @error('pincode')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem;">Order Notes</label>
                            <textarea name="notes" 
                                      class="form-control" 
                                      rows="2"
                                      style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;"
                                      placeholder="Any special instructions for delivery">{{ old('notes') }}</textarea>
                        </div>
                        
                        <h4 style="font-size: 1.3rem; font-weight: 600; margin: 2rem 0 1.5rem;">Payment Method</h4>
                        
                        <div class="payment-methods">
                            @foreach($paymentMethods as $method)
                            <div style="padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 1rem; cursor: pointer; transition: all 0.3s;"
                                 onclick="selectPaymentMethod({{ $method->id }}, this)">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="payment_method" 
                                           id="payment_{{ $method->id }}" 
                                           value="{{ $method->id }}"
                                           {{ old('payment_method') == $method->id ? 'checked' : '' }}
                                           required>
                                    <label class="form-check-label" for="payment_{{ $method->id }}" style="font-weight: 500;">
                                        {{ $method->name }}
                                    </label>
                                    @if($method->description)
                                        <small class="text-muted d-block">{{ $method->description }}</small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: sticky; top: 80px;">
                        <h4 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 1.5rem;">Order Summary</h4>
                        
                        <!-- Cart Items -->
                        @foreach($cartItems as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0;">
                            <div style="flex: 1;">
                                <p style="font-size: 0.9rem; font-weight: 500; margin-bottom: 0.25rem;">{{ $item->product->name }}</p>
                                <small style="color: #6c757d;">Qty: {{ $item->quantity }}</small>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 0.9rem; font-weight: 600; margin: 0;">₹{{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Totals -->
                        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid #e0e0e0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Subtotal:</span>
                                <span style="font-weight: 600;">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            @if($discount > 0)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: #4caf50;">
                                <span>Discount:</span>
                                <span style="font-weight: 600;">-₹{{ number_format($discount, 2) }}</span>
                            </div>
                            @endif
                            
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Delivery Charge:</span>
                                <span style="font-weight: 600;">
                                    @if($deliveryCharge > 0)
                                        ₹{{ number_format($deliveryCharge, 2) }}
                                    @else
                                        <span style="color: #4caf50;">FREE</span>
                                    @endif
                                </span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e0e0e0; font-size: 1.2rem;">
                                <span style="font-weight: 600;">Total:</span>
                                <span style="font-weight: 700; color: #28a745;">₹{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <!-- Place Order Button -->
                        <button type="submit" 
                                style="width: 100%; padding: 1rem; background: #28a745; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 1.5rem; cursor: pointer; transition: all 0.3s;"
                                onmouseover="this.style.background='#1e7e34'" 
                                onmouseout="this.style.background='#28a745'">
                            Place Order
                        </button>
                        
                        <!-- Security Note -->
                        <p style="font-size: 0.8rem; color: #6c757d; text-align: center; margin-top: 1rem;">
                            <i class="fas fa-lock"></i> Your payment information is secure and encrypted
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script>
function selectPaymentMethod(methodId, element) {
    // Remove active class from all payment methods
    document.querySelectorAll('.payment-methods > div').forEach(el => {
        el.style.borderColor = '#e0e0e0';
        el.style.background = 'white';
    });
    
    // Add active class to selected method
    element.style.borderColor = '#28a745';
    element.style.background = '#d4edda';
    
    // Check the radio button
    document.getElementById('payment_' + methodId).checked = true;
}

// Initialize selected payment method styling
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
    if (checkedRadio) {
        const parentDiv = checkedRadio.closest('.payment-methods > div');
        if (parentDiv) {
            parentDiv.style.borderColor = '#28a745';
            parentDiv.style.background = '#d4edda';
        }
    }
});
</script>
@endsection
