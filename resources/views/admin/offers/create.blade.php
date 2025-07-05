@extends('admin.layouts.app')

@section('title', 'Create Offer')
@section('page_title', 'Create Offer')

@section('page_actions')
<a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Offers
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.offers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Offer Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required 
                                   placeholder="e.g., Summer Sale, New Customer Discount">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code') }}" 
                                   placeholder="SAVE20" style="text-transform: uppercase;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Leave empty for automatic discounts</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Offer Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Offer Type</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                <option value="category" {{ old('type') == 'category' ? 'selected' : '' }}>Category Specific</option>
                                <option value="product" {{ old('type') == 'product' ? 'selected' : '' }}>Product Specific</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">Discount Value *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="value-prefix">%</span>
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value') }}" 
                                       step="0.01" min="0" required>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row" id="target-selection" style="display: none;">
                        <div class="col-md-6 mb-3" id="category-selection">
                            <label for="category_id" class="form-label">Target Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="product-selection">
                            <label for="product_id" class="form-label">Target Product</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="minimum_amount" class="form-label">Minimum Order Amount (₹)</label>
                        <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                               id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}" 
                               step="0.01" min="0" placeholder="0.00">
                        @error('minimum_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional: Minimum cart value required for this offer</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" 
                                   min="1" placeholder="Unlimited">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optional: Maximum number of times this offer can be used</small>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Offer
                        </button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Offer Types Guide</h6>
                <ul class="list-unstyled small">
                    <li><strong>Percentage:</strong> 10% off, 25% off</li>
                    <li><strong>Fixed Amount:</strong> ₹50 off, ₹100 off</li>
                    <li><strong>Category:</strong> Discount on specific category</li>
                    <li><strong>Product:</strong> Discount on specific product</li>
                </ul>
                
                <hr>
                
                <h6>Best Practices</h6>
                <ul class="list-unstyled small">
                    <li><i class="fas fa-check text-success"></i> Set clear expiry dates</li>
                    <li><i class="fas fa-check text-success"></i> Use attractive coupon codes</li>
                    <li><i class="fas fa-check text-success"></i> Set reasonable limits</li>
                    <li><i class="fas fa-check text-success"></i> Monitor usage regularly</li>
                </ul>
                
                <hr>
                
                <h6>Example Codes</h6>
                <ul class="list-unstyled small">
                    <li>WELCOME10, SAVE20</li>
                    <li>FIRSTBUY, SUMMER25</li>
                    <li>HERBAL15, NATURAL30</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valuePrefix = document.getElementById('value-prefix');
    const targetSelection = document.getElementById('target-selection');
    const categorySelection = document.getElementById('category-selection');
    const productSelection = document.getElementById('product-selection');
    const codeInput = document.getElementById('code');
    
    // Auto-uppercase coupon code
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    // Handle offer type changes
    typeSelect.addEventListener('change', function() {
        const value = this.value;
        
        // Update value prefix
        if (value === 'percentage') {
            valuePrefix.textContent = '%';
        } else {
            valuePrefix.textContent = '₹';
        }
        
        // Show/hide target selection
        if (value === 'category' || value === 'product') {
            targetSelection.style.display = 'block';
            
            if (value === 'category') {
                categorySelection.style.display = 'block';
                productSelection.style.display = 'none';
            } else {
                categorySelection.style.display = 'none';
                productSelection.style.display = 'block';
            }
        } else {
            targetSelection.style.display = 'none';
        }
    });
    
    // Trigger change event for initial state
    typeSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection
