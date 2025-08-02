@extends('admin.layouts.app')

@section('title', 'Edit Payment Method')
@section('page_title', 'Edit Payment Method: ' . $paymentMethod->display_name)

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
                <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Method Type</label>
                        <div class="form-control-plaintext">
                            <span class="badge bg-{{ $paymentMethod->getColor() }}">
                                <i class="{{ $paymentMethod->getIcon() }}"></i> {{ ucfirst($paymentMethod->type) }}
                            </span>
                        </div>
                        <input type="hidden" name="type" value="{{ $paymentMethod->type }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name *</label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" 
                               value="{{ old('display_name', $paymentMethod->display_name) }}" required>
                        <small class="text-muted">This name will be shown to customers</small>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
                        <small class="text-muted">Additional information shown to customers</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Payment Method Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Payment Method Image/Logo</label>
                        @if($paymentMethod->hasImage())
                            <div class="mb-2">
                                <img src="{{ $paymentMethod->getImageUrl() }}" 
                                     class="img-thumbnail" 
                                     style="max-height: 60px; max-width: 100px;" 
                                     alt="{{ $paymentMethod->display_name }}">
                                <small class="d-block text-muted">Current image</small>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        <small class="text-muted">Upload a new image to replace current one (optional). Recommended size: 100x60px</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Razorpay Fields -->
                    @if($paymentMethod->type === 'razorpay')
                        <div id="razorpay-fields" class="method-fields">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-credit-card"></i> Razorpay Configuration
                            </h6>
                            
                            <div class="mb-3">
                                <label for="razorpay_key_id" class="form-label">Razorpay Key ID *</label>
                                <input type="text" class="form-control @error('razorpay_key_id') is-invalid @enderror" 
                                       id="razorpay_key_id" name="razorpay_key_id" 
                                       value="{{ old('razorpay_key_id', $paymentMethod->razorpay_key_id) }}">
                                <small class="text-muted">Get this from your Razorpay dashboard</small>
                                @error('razorpay_key_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="razorpay_key_secret" class="form-label">Razorpay Key Secret</label>
                                <input type="password" class="form-control @error('razorpay_key_secret') is-invalid @enderror" 
                                       id="razorpay_key_secret" name="razorpay_key_secret" 
                                       placeholder="Leave empty to keep current secret">
                                <small class="text-muted">Only enter if you want to change the secret</small>
                                @error('razorpay_key_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="razorpay_webhook_secret" class="form-label">Webhook Secret</label>
                                <input type="password" class="form-control @error('razorpay_webhook_secret') is-invalid @enderror" 
                                       id="razorpay_webhook_secret" name="razorpay_webhook_secret"
                                       placeholder="Leave empty to keep current secret">
                                <small class="text-muted">Optional: For webhook verification</small>
                                @error('razorpay_webhook_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    
                    <!-- Bank Transfer Fields -->
                    @if($paymentMethod->type === 'bank_transfer')
                        @php
                            $bankDetails = $paymentMethod->bank_details ?? [];
                        @endphp
                        <div id="bank-transfer-fields" class="method-fields">
                            <h6 class="text-info mb-3">
                                <i class="fas fa-university"></i> Bank Account Details
                            </h6>
                            
                            <div class="mb-3">
                                <label for="bank_name" class="form-label">Bank Name *</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" 
                                       value="{{ old('bank_name', $bankDetails['bank_name'] ?? '') }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="account_name" class="form-label">Account Holder Name *</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                       id="account_name" name="account_name" 
                                       value="{{ old('account_name', $bankDetails['account_name'] ?? '') }}">
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="account_number" class="form-label">Account Number *</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" 
                                       value="{{ old('account_number', $bankDetails['account_number'] ?? '') }}">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="ifsc_code" class="form-label">IFSC Code *</label>
                                <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" 
                                       id="ifsc_code" name="ifsc_code" 
                                       value="{{ old('ifsc_code', $bankDetails['ifsc_code'] ?? '') }}">
                                @error('ifsc_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="branch_name" class="form-label">Branch Name</label>
                                <input type="text" class="form-control @error('branch_name') is-invalid @enderror" 
                                       id="branch_name" name="branch_name" 
                                       value="{{ old('branch_name', $bankDetails['branch_name'] ?? '') }}">
                                @error('branch_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    
                    <!-- UPI Fields -->
                    @if($paymentMethod->type === 'upi')
                        <div id="upi-fields" class="method-fields">
                            <h6 class="text-warning mb-3">
                                <i class="fas fa-mobile-alt"></i> UPI Configuration
                            </h6>
                            
                            <div class="mb-3">
                                <label for="upi_id" class="form-label">UPI ID *</label>
                                <input type="text" class="form-control @error('upi_id') is-invalid @enderror" 
                                       id="upi_id" name="upi_id" 
                                       value="{{ old('upi_id', $paymentMethod->upi_id) }}"
                                       placeholder="example@paytm">
                                @error('upi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="upi_qr_code" class="form-label">UPI QR Code</label>
                                @if($paymentMethod->upi_qr_code)
                                    <div class="mb-2">
                                        <img src="{{ $paymentMethod->getQrCodeUrl() }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                        <small class="d-block text-muted">Current QR code</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('upi_qr_code') is-invalid @enderror" 
                                       id="upi_qr_code" name="upi_qr_code" accept="image/*">
                                <small class="text-muted">Upload new QR code to replace existing</small>
                                @error('upi_qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    
                    <!-- G Pay Fields -->
                    @if($paymentMethod->type === 'gpay')
                        <div id="gpay-fields" class="method-fields">
                            <h6 class="text-danger mb-3">
                                <i class="fab fa-google-pay"></i> Google Pay Configuration
                            </h6>
                            
                            <div class="mb-3">
                                <label for="upi_id" class="form-label">Google Pay UPI ID *</label>
                                <input type="text" class="form-control @error('upi_id') is-invalid @enderror" 
                                       id="upi_id" name="upi_id" 
                                       value="{{ old('upi_id', $paymentMethod->upi_id) }}"
                                       placeholder="yourname@okaxis">
                                <small class="text-muted">Your Google Pay UPI ID (e.g., yourname@okaxis)</small>
                                @error('upi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="upi_qr_code" class="form-label">Google Pay QR Code</label>
                                @if($paymentMethod->upi_qr_code)
                                    <div class="mb-2">
                                        <img src="{{ $paymentMethod->getQrCodeUrl() }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                        <small class="d-block text-muted">Current Google Pay QR code</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('upi_qr_code') is-invalid @enderror" 
                                       id="upi_qr_code" name="upi_qr_code" accept="image/*">
                                <small class="text-muted">Upload new Google Pay QR code to replace existing</small>
                                @error('upi_qr_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <h6>Charges & Limits</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="extra_charge" class="form-label">Fixed Extra Charge (₹)</label>
                            <input type="number" class="form-control @error('extra_charge') is-invalid @enderror" 
                                   id="extra_charge" name="extra_charge" 
                                   value="{{ old('extra_charge', $paymentMethod->extra_charge) }}" 
                                   step="0.01" min="0">
                            @error('extra_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="extra_charge_percentage" class="form-label">Percentage Charge (%)</label>
                            <input type="number" class="form-control @error('extra_charge_percentage') is-invalid @enderror" 
                                   id="extra_charge_percentage" name="extra_charge_percentage" 
                                   value="{{ old('extra_charge_percentage', $paymentMethod->extra_charge_percentage) }}" 
                                   step="0.01" min="0" max="100">
                            @error('extra_charge_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="minimum_amount" class="form-label">Minimum Order Amount (₹)</label>
                            <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                   id="minimum_amount" name="minimum_amount" 
                                   value="{{ old('minimum_amount', $paymentMethod->minimum_amount) }}" 
                                   step="0.01" min="0">
                            <small class="text-muted">Leave empty for no minimum</small>
                            @error('minimum_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="maximum_amount" class="form-label">Maximum Order Amount (₹)</label>
                            <input type="number" class="form-control @error('maximum_amount') is-invalid @enderror" 
                                   id="maximum_amount" name="maximum_amount" 
                                   value="{{ old('maximum_amount', $paymentMethod->maximum_amount) }}" 
                                   step="0.01" min="0">
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
                                   id="sort_order" name="sort_order" 
                                   value="{{ old('sort_order', $paymentMethod->sort_order) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Available for customers)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Payment Method
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
                <h6>Method Information</h6>
                <p><strong>Type:</strong> {{ ucfirst($paymentMethod->type) }}</p>
                <p><strong>Created:</strong> {{ $paymentMethod->created_at->format('M d, Y') }}</p>
                <p><strong>Last Updated:</strong> {{ $paymentMethod->updated_at->format('M d, Y') }}</p>
                
                <hr>
                
                <h6>Current Status</h6>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $paymentMethod->is_active ? 'success' : 'danger' }}">
                        {{ $paymentMethod->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                
                @if($paymentMethod->type === 'razorpay')
                    <hr>
                    <h6>Webhook URL</h6>
                    <p class="small">Configure this URL in your Razorpay dashboard:</p>
                    <code class="small">{{ url('/razorpay/webhook') }}</code>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
