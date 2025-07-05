@extends('admin.layouts.app')

@section('title', 'Create Purchase Order')
@section('page_title', 'Create Purchase Order')

@section('page_actions')
<a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
</a>
@endsection

@push('styles')
<style>
    .product-row {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        margin-bottom: 10px;
        padding: 15px;
        background: #f9f9f9;
    }
    .remove-item {
        color: #dc3545;
        border: none;
        background: none;
        font-size: 18px;
    }
    .remove-item:hover {
        color: #c82333;
    }
    .calculation-section {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.purchase-orders.store') }}" method="POST" id="poForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="po_date" class="form-label">PO Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('po_date') is-invalid @enderror" 
                                       id="po_date" name="po_date" value="{{ old('po_date', date('Y-m-d')) }}" required>
                                @error('po_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                                <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" 
                                       id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Order Items -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Purchase Order Items</h5>
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
                                          placeholder="Terms and conditions for this purchase order">{{ old('terms_conditions') }}</textarea>
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
                    <h5 class="card-title mb-0">Order Summary</h5>
                </div>
                <div class="card-body calculation-section">
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
                            <i class="fas fa-save"></i> Create Purchase Order
                        </button>
                        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
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
    <div class="product-row" data-index="__INDEX__">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Product <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="items[__INDEX__][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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
                <button type="button" class="remove-item btn" title="Remove Item">
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
    $('#poForm').submit(function(e) {
        if ($('.product-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order.');
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
    checkEmptyItems();
});
</script>
@endpush
