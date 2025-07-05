@extends('admin.layouts.app')

@section('title', 'Add Payment Method')
@section('page_title', 'Add Payment Method')

@section('page_actions')
<a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Payment Methods
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" id="payment-method-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="type" class="form-label">Payment Method Type *</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required onchange="toggleMethodFields()">
                            <option value="">Select Payment Method</option>
                            <option value="razorpay" {{ old('type') == 'razorpay' ? 'selected' : '' }}>
                                Razorpay (Online Payment Gateway)
                            </option>
                            <option value="cod" {{ old('type') == 'cod' ? 'selected' : '' }}>
                                Cash on Delivery (COD)
                            </option>
                            <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>
                                Bank Transfer
                            </option>
                            <option value="upi" {{ old('type') == 'upi' ? 'selected' : '' }}>
                                UPI Payment
                            </option>
                            <option value="gpay" {{ old('type') == 'gpay' ? 'selected' : '' }}>
                                Google Pay (G Pay)
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name *</label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                        <small class="text-muted">This name will be shown to customers</small>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        <small class="text-muted">Additional information shown to customers</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Payment Method Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Payment Method Image/Logo</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        <small class="text-muted">Upload an image/logo for this payment method (optional). Recommended size: 100x60px</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Razorpay Fields -->
                    <div id="razorpay-fields" class="method-fields" style="display: none;">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-credit-card"></i> Razorpay Configuration
                        </h6>
                        
                        <div class="mb-3">
                            <label for="razorpay_key_id" class="form-label">Razorpay Key ID *</label>
                            <input type="text" class="form-control @error('razorpay_key_id') is-invalid @enderror" 
                                   id="razorpay_key_id" name="razorpay_key_id" value="{{ old('razorpay_key_id') }}">
                            <small class="text-muted">Get this from your Razorpay dashboard</small>
                            @error('razorpay_key_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="razorpay_key_secret" class="form-label">Razorpay Key Secret *</label>
                            <input type="password" class="form-control @error('razorpay_key_secret') is-invalid @enderror" 
                                   id="razorpay_key_secret" name="razorpay_key_secret">
                            <small class="text-muted">Keep this secret and secure</small>
                            @error('razorpay_key_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="razorpay_webhook_secret" class="form-label">Webhook Secret</label>
                            <input type="password" class="form-control @error('razorpay_webhook_secret') is-invalid @enderror" 
                                   id="razorpay_webhook_secret" name="razorpay_webhook_secret">
                            <small class="text-muted">Optional: For webhook verification</small>
                            @error('razorpay_webhook_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Bank Transfer Fields -->
                    <div id="bank-transfer-fields" class="method-fields" style="display: none;">
                        <h6 class="text-info mb-3">
                            <i class="fas fa-university"></i> Bank Account Details
                        </h6>
                        
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name *</label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                   id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Holder Name *</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                   id="account_name" name="account_name" value="{{ old('account_name') }}">
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number *</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                   id="account_number" name="account_number" value="{{ old('account_number') }}">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="ifsc_code" class="form-label">IFSC Code *</label>
                            <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" 
                                   id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}">
                            @error('ifsc_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="branch_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control @error('branch_name') is-invalid @enderror" 
                                   id="branch_name" name="branch_name" value="{{ old('branch_name') }}">
                            @error('branch_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- UPI Fields -->
                    <div id="upi-fields" class="method-fields" style="display: none;">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-mobile-alt"></i> UPI Configuration
                        </h6>
                        
                        <div class="mb-3">
                            <label for="upi_id" class="form-label">UPI ID *</label>
                            <input type="text" class="form-control @error('upi_id') is-invalid @enderror" 
                                   id="upi_id" name="upi_id" value="{{ old('upi_id') }}"
                                   placeholder="example@paytm">
                            @error('upi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="upi_qr_code" class="form-label">UPI QR Code</label>
                            <input type="file" class="form-control @error('upi_qr_code') is-invalid @enderror" 
                                   id="upi_qr_code" name="upi_qr_code" accept="image/*">
                            <small class="text-muted">Upload QR code image for easy scanning</small>
                            @error('upi_qr_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- G Pay Fields -->
                    <div id="gpay-fields" class="method-fields" style="display: none;">
                        <h6 class="text-danger mb-3">
                            <i class="fab fa-google-pay"></i> Google Pay Configuration
                        </h6>
                        
                        <div class="mb-3">
                            <label for="gpay_upi_id" class="form-label">Google Pay UPI ID *</label>
                            <input type="text" class="form-control @error('upi_id') is-invalid @enderror" 
                                   id="gpay_upi_id" name="upi_id" value="{{ old('upi_id') }}"
                                   placeholder="yourname@okaxis">
                            <small class="text-muted">Your Google Pay UPI ID (e.g., yourname@okaxis)</small>
                            @error('upi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="gpay_qr_code" class="form-label">Google Pay QR Code</label>
                            <input type="file" class="form-control @error('upi_qr_code') is-invalid @enderror" 
                                   id="gpay_qr_code" name="upi_qr_code" accept="image/*">
                            <small class="text-muted">Upload your Google Pay QR code for customer payments</small>
                            @error('upi_qr_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Charges & Limits</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="extra_charge" class="form-label">Fixed Extra Charge (₹)</label>
                            <input type="number" class="form-control @error('extra_charge') is-invalid @enderror" 
                                   id="extra_charge" name="extra_charge" value="{{ old('extra_charge', '0.00') }}" 
                                   step="0.01" min="0" placeholder="0.00">
                            @error('extra_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="extra_charge_percentage" class="form-label">Percentage Charge (%)</label>
                            <input type="number" class="form-control @error('extra_charge_percentage') is-invalid @enderror" 
                                   id="extra_charge_percentage" name="extra_charge_percentage" 
                                   value="{{ old('extra_charge_percentage', '0.00') }}" 
                                   step="0.01" min="0" max="100" placeholder="0.00">
                            @error('extra_charge_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="minimum_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                   id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', '0.00') }}" 
                                   step="0.01" min="0" placeholder="0.00">
                            <small class="text-muted">Leave as 0.00 for no minimum</small>
                            @error('minimum_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="maximum_amount" class="form-label">Maximum Order Amount (₹)</label>
                            <input type="number" class="form-control @error('maximum_amount') is-invalid @enderror" 
                                   id="maximum_amount" name="maximum_amount" value="{{ old('maximum_amount') }}" 
                                   step="0.01" min="0" placeholder="Leave empty for no maximum">
                            <small class="text-muted">Leave empty for no maximum</small>
                            @error('maximum_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', '0') }}" min="0" placeholder="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Available for customers)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Payment Method
                        </button>
                        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Payment Method Guidelines</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Choose appropriate payment type</li>
                    <li><i class="fas fa-check text-success"></i> Use clear display names</li>
                    <li><i class="fas fa-check text-success"></i> Add helpful descriptions</li>
                    <li><i class="fas fa-check text-success"></i> Set reasonable limits</li>
                    <li><i class="fas fa-check text-success"></i> Configure charges carefully</li>
                    <li><i class="fas fa-check text-success"></i> Keep credentials secure</li>
                </ul>
                
                <hr>
                
                <h6>Security Tips</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-info text-info"></i> Never share API secrets</li>
                    <li><i class="fas fa-info text-info"></i> Use test mode first</li>
                    <li><i class="fas fa-info text-info"></i> Enable webhook verification</li>
                    <li><i class="fas fa-info text-info"></i> Monitor transactions regularly</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleMethodFields() {
    const type = document.getElementById('type').value;
    
    // Hide all method-specific fields
    document.querySelectorAll('.method-fields').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show relevant fields based on selected type
    if (type === 'razorpay') {
        document.getElementById('razorpay-fields').style.display = 'block';
    } else if (type === 'bank_transfer') {
        document.getElementById('bank-transfer-fields').style.display = 'block';
    } else if (type === 'upi') {
        document.getElementById('upi-fields').style.display = 'block';
    } else if (type === 'gpay') {
        document.getElementById('gpay-fields').style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMethodFields();
});
</script>
@endpush
