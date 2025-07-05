@extends('admin.layouts.app')

@section('title', 'Add Supplier')
@section('page_title', 'Add New Supplier')

@section('page_actions')
<a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Suppliers
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.suppliers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Contact Person Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control @error('mobile') is-invalid @enderror" 
                                   id="mobile" name="mobile" value="{{ old('mobile') }}">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gst_number" class="form-label">GST Number</label>
                            <input type="text" class="form-control @error('gst_number') is-invalid @enderror" 
                                   id="gst_number" name="gst_number" value="{{ old('gst_number') }}" 
                                   maxlength="15" placeholder="22AAAAA0000A1Z5">
                            @error('gst_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number</label>
                            <input type="text" class="form-control @error('pan_number') is-invalid @enderror" 
                                   id="pan_number" name="pan_number" value="{{ old('pan_number') }}" 
                                   maxlength="10" placeholder="ABCDE1234F">
                            @error('pan_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="credit_limit" class="form-label">Credit Limit (₹)</label>
                            <input type="number" class="form-control @error('credit_limit') is-invalid @enderror" 
                                   id="credit_limit" name="credit_limit" value="{{ old('credit_limit', 0) }}" 
                                   step="0.01" min="0">
                            @error('credit_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="credit_days" class="form-label">Credit Days</label>
                            <input type="number" class="form-control @error('credit_days') is-invalid @enderror" 
                                   id="credit_days" name="credit_days" value="{{ old('credit_days', 30) }}" 
                                   min="0" max="365">
                            @error('credit_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="opening_balance" class="form-label">Opening Balance (₹)</label>
                            <input type="number" class="form-control @error('opening_balance') is-invalid @enderror" 
                                   id="opening_balance" name="opening_balance" value="{{ old('opening_balance', 0) }}" 
                                   step="0.01">
                            @error('opening_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Any additional notes about this supplier...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Supplier
                        </button>
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Supplier Guidelines</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Verify contact information</li>
                    <li><i class="fas fa-check text-success"></i> Collect GST/PAN details</li>
                    <li><i class="fas fa-check text-success"></i> Set appropriate credit terms</li>
                    <li><i class="fas fa-check text-success"></i> Document business agreements</li>
                    <li><i class="fas fa-check text-success"></i> Regular performance review</li>
                </ul>
                
                <hr>
                
                <h6>Credit Terms</h6>
                <ul class="list-unstyled small">
                    <li><strong>Credit Limit:</strong> Maximum outstanding amount</li>
                    <li><strong>Credit Days:</strong> Payment terms (e.g., 30 days)</li>
                    <li><strong>Opening Balance:</strong> Any existing dues</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-format GST number
$('#gst_number').on('input', function() {
    this.value = this.value.toUpperCase();
});

// Auto-format PAN number
$('#pan_number').on('input', function() {
    this.value = this.value.toUpperCase();
});

// Auto-format PIN code
$('#pincode').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>
@endpush
@endsection
