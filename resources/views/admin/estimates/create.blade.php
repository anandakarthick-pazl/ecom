@extends('admin.layouts.app')

@section('title', 'Create Estimate')
@section('page_title', 'Create Estimate')

@section('page_actions')
<a href="{{ route('admin.estimates.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
</a>
@endsection

@section('content')
<form action="{{ route('admin.estimates.store') }}" method="POST" id="estimateForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                       id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                       id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimate_date" class="form-label">Estimate Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('estimate_date') is-invalid @enderror" 
                                       id="estimate_date" name="estimate_date" value="{{ old('estimate_date', date('Y-m-d')) }}" required>
                                @error('estimate_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valid_until" class="form-label">Valid Until <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                       id="valid_until" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Address</label>
                                <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                          id="customer_address" name="customer_address" rows="3">{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estimate Items -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Estimate Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addItem">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </div>
                    
                    <div class="alert alert-info d-none" id="noItemsAlert">
                        <i class="fas fa-info-circle"></i> No items added yet. Click "Add Item" to start.
                    </div>
                </div>
            </div>

            <!-- Notes and Terms -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4" placeholder="Internal notes">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control @error('terms_conditions') is-invalid @enderror" 
                                          id="terms_conditions" name="terms_conditions" rows="4" 
                                          placeholder="Terms and conditions for this estimate">{{ old('terms_conditions') }}</textarea>
                                @error('terms_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Calculation Section -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estimate Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="text" class="form-control" id="subtotalDisplay" readonly value="0.00">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tax_amount" class="form-label">Tax Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control @error('tax_amount') is-invalid @enderror" 
                                   id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" 
                                   min="0" step="0.01">
                        </div>
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control @error('discount') is-invalid @enderror" 
                                   id="discount" name="discount" value="{{ old('discount', 0) }}" 
                                   min="0" step="0.01">
                        </div>
                        @error('discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Total Amount</strong></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="text" class="form-control fw-bold" id="totalDisplay" readonly value="0.00">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Estimate
                        </button>
                        <a href="{{ route('admin.estimates.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <div class="product-row border rounded p-3 mb-3" data-index="__INDEX__">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Product <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="items[__INDEX__][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control quantity-input" 
                       name="items[__INDEX__][quantity]" min="1" required>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                <input type="number" class="form-control price-input" 
                       name="items[__INDEX__][unit_price]" min="0" step="0.01" required>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <input type="text" class="form-control total-input" readonly>
            </div>
            
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-outline-danger remove-item" title="Remove Item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-12">
                <label class="form-label">Description (Optional)</label>
                <input type="text" class="form-control" name="items[__INDEX__][description]" 
                       placeholder="Additional description for this item">
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemIndex = 0;
    
    // Add new item
    $('#addItem').click(function() {
        addNewItem();
    });
    
    // Remove item
    $(document).on('click', '.remove-item', function() {
        $(this).closest('.product-row').remove();
        updateCalculations();
        checkEmptyItems();
    });
    
    // Update calculations when inputs change
    $(document).on('input', '.quantity-input, .price-input, #tax_amount, #discount', function() {
        updateRowTotal($(this).closest('.product-row'));
        updateCalculations();
    });
    
    // Auto-fill price when product is selected
    $(document).on('change', '.product-select', function() {
        const price = $(this).find(':selected').data('price');
        if (price) {
            $(this).closest('.product-row').find('.price-input').val(price);
            updateRowTotal($(this).closest('.product-row'));
            updateCalculations();
        }
    });
    
    function addNewItem() {
        let template = $('#itemRowTemplate').html();
        template = template.replace(/__INDEX__/g, itemIndex);
        
        $('#itemsContainer').append(template);
        itemIndex++;
        
        $('#noItemsAlert').addClass('d-none');
    }
    
    function updateRowTotal(row) {
        let quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        let price = parseFloat(row.find('.price-input').val()) || 0;
        let total = quantity * price;
        
        row.find('.total-input').val(total.toFixed(2));
    }
    
    function updateCalculations() {
        let subtotal = 0;
        
        $('.product-row').each(function() {
            let quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            let price = parseFloat($(this).find('.price-input').val()) || 0;
            subtotal += quantity * price;
        });
        
        let taxAmount = parseFloat($('#tax_amount').val()) || 0;
        let discount = parseFloat($('#discount').val()) || 0;
        let total = subtotal + taxAmount - discount;
        
        $('#subtotalDisplay').val(subtotal.toFixed(2));
        $('#totalDisplay').val(total.toFixed(2));
    }
    
    function checkEmptyItems() {
        if ($('.product-row').length === 0) {
            $('#noItemsAlert').removeClass('d-none');
        }
    }
    
    // Form validation
    $('#estimateForm').submit(function(e) {
        if ($('.product-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the estimate.');
            return false;
        }
        
        // Check if all required fields in items are filled
        let isValid = true;
        $('.product-row').each(function() {
            let productId = $(this).find('.product-select').val();
            let quantity = $(this).find('.quantity-input').val();
            let price = $(this).find('.price-input').val();
            
            if (!productId || !quantity || !price) {
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields for each item.');
            return false;
        }
    });
    
    // Initialize with one empty item
    addNewItem();
});
</script>
@endpush
