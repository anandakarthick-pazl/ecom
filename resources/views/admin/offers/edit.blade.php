@extends('admin.layouts.app')

@section('title', 'Edit Offer')
@section('page_title', 'Edit Offer: ' . $offer->name)

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
                <form action="{{ route('admin.offers.update', $offer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Offer Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $offer->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="code" class="form-label">Coupon Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $offer->code) }}" 
                                   style="text-transform: uppercase;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Offer Type *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="percentage" {{ old('type', $offer->type) == 'percentage' ? 'selected' : '' }}>Percentage Discount</option>
                                <option value="fixed" {{ old('type', $offer->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                <option value="category" {{ old('type', $offer->type) == 'category' ? 'selected' : '' }}>Category Specific</option>
                                <option value="product" {{ old('type', $offer->type) == 'product' ? 'selected' : '' }}>Product Specific</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">Discount Value *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="value-prefix">
                                    {{ $offer->type === 'percentage' ? '%' : '₹' }}
                                </span>
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value', $offer->value) }}" 
                                       step="0.01" min="0" required>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row" id="target-selection" style="display: {{ in_array($offer->type, ['category', 'product']) ? 'block' : 'none' }};">
                        <div class="col-md-6 mb-3" id="category-selection" style="display: {{ $offer->type === 'category' ? 'block' : 'none' }};">
                            <label for="category_id" class="form-label">Target Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $offer->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="product-selection" style="display: {{ $offer->type === 'product' ? 'block' : 'none' }};">
                            <label for="product_id" class="form-label">Target Product</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $offer->product_id) == $product->id ? 'selected' : '' }}>
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
                               id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $offer->minimum_amount) }}" 
                               step="0.01" min="0">
                        @error('minimum_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', $offer->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', $offer->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $offer->usage_limit) }}" 
                                   min="1">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Offer
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
                <h6>Offer Statistics</h6>
                <p><strong>Used:</strong> {{ $offer->used_count }} times</p>
                @if($offer->usage_limit)
                    <p><strong>Remaining:</strong> {{ $offer->usage_limit - $offer->used_count }}</p>
                @endif
                <p><strong>Created:</strong> {{ $offer->created_at->format('M d, Y') }}</p>
                
                @if($offer->isValid())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> This offer is currently active
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This offer is not currently active
                    </div>
                @endif
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
    
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    typeSelect.addEventListener('change', function() {
        const value = this.value;
        
        if (value === 'percentage') {
            valuePrefix.textContent = '%';
        } else {
            valuePrefix.textContent = '₹';
        }
        
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
});
</script>
@endpush
@endsection
