@extends('admin.layouts.app')

@section('title', 'Create Stock Adjustment')
@section('page_title', 'Create Stock Adjustment')

@section('page_actions')
<a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
</a>
@endsection

@section('content')
<form action="{{ route('admin.stock-adjustments.store') }}" method="POST" id="adjustmentForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adjustment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="adjustment_date" class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('adjustment_date') is-invalid @enderror" 
                                       id="adjustment_date" name="adjustment_date" value="{{ old('adjustment_date', date('Y-m-d')) }}" required>
                                @error('adjustment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="increase" {{ old('type') == 'increase' ? 'selected' : '' }}>Stock Increase</option>
                                    <option value="decrease" {{ old('type') == 'decrease' ? 'selected' : '' }}>Stock Decrease</option>
                                    <option value="recount" {{ old('type') == 'recount' ? 'selected' : '' }}>Stock Recount</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('reason') is-invalid @enderror" 
                                       id="reason" name="reason" value="{{ old('reason') }}" 
                                       placeholder="Reason for stock adjustment" required>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this adjustment">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adjustment Items -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Adjustment Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="addItem">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </div>
                    
                    <div class="alert alert-info d-none" id="noItemsAlert">
                        <i class="fas fa-info-circle"></i> No products added yet. Click "Add Product" to start.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Adjustment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Total Products</label>
                        <input type="text" class="form-control" id="totalProducts" readonly value="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Quantity Change</label>
                        <input type="text" class="form-control" id="totalQtyChange" readonly value="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Value Impact</label>
                        <input type="text" class="form-control" id="totalValueImpact" readonly value="₹0.00">
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Stock adjustments will directly modify inventory levels. Please ensure all quantities are accurate before submitting.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Adjustment
                        </button>
                        <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Product Search Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="productSearch" placeholder="Search products...">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productList">
                            @foreach($products as $product)
                                <tr data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" 
                                    data-current-stock="{{ $product->stock }}" data-price="{{ $product->price }}">
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>₹{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary select-product">Select</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <div class="adjustment-row border rounded p-3 mb-3" data-index="__INDEX__">
        <input type="hidden" name="items[__INDEX__][product_id]" class="product-id">
        <input type="hidden" name="items[__INDEX__][current_stock]" class="current-stock">
        
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Product</label>
                <input type="text" class="form-control product-name" readonly>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Current Stock</label>
                <input type="text" class="form-control current-stock-display" readonly>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Adjustment <span class="text-danger">*</span></label>
                <input type="number" class="form-control adjusted-quantity" 
                       name="items[__INDEX__][adjusted_quantity]" required>
                <small class="text-muted">Use negative values to decrease stock</small>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">New Stock</label>
                <input type="text" class="form-control new-stock" readonly>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Unit Cost</label>
                <input type="number" class="form-control unit-cost" 
                       name="items[__INDEX__][unit_cost]" min="0" step="0.01">
            </div>
            
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger remove-item" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-12">
                <label class="form-label">Item Reason (Optional)</label>
                <input type="text" class="form-control" name="items[__INDEX__][reason]" 
                       placeholder="Specific reason for this product adjustment">
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemIndex = 0;
    let selectedProducts = [];
    
    // Add new item
    $('#addItem').click(function() {
        $('#productModal').modal('show');
    });
    
    // Product search in modal
    $('#productSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        
        $('#productList tr').each(function() {
            const productName = $(this).find('td:first').text().toLowerCase();
            if (productName.includes(search) || search === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Select product from modal
    $(document).on('click', '.select-product', function() {
        const row = $(this).closest('tr');
        const productId = row.data('product-id');
        const productName = row.data('product-name');
        const currentStock = row.data('current-stock');
        const price = row.data('price');
        
        if (selectedProducts.includes(productId)) {
            alert('Product already added to adjustment list!');
            return;
        }
        
        addAdjustmentItem(productId, productName, currentStock, price);
        selectedProducts.push(productId);
        $('#productModal').modal('hide');
    });
    
    // Remove item
    $(document).on('click', '.remove-item', function() {
        const row = $(this).closest('.adjustment-row');
        const productId = parseInt(row.find('.product-id').val());
        
        selectedProducts = selectedProducts.filter(id => id !== productId);
        row.remove();
        updateSummary();
        checkEmptyItems();
    });
    
    // Update calculations when quantities change
    $(document).on('input', '.adjusted-quantity, .unit-cost', function() {
        updateRowCalculation($(this).closest('.adjustment-row'));
        updateSummary();
    });
    
    function addAdjustmentItem(productId, productName, currentStock, price) {
        let template = $('#itemRowTemplate').html();
        template = template.replace(/__INDEX__/g, itemIndex);
        
        const itemRow = $(template);
        itemRow.find('.product-id').val(productId);
        itemRow.find('.product-name').val(productName);
        itemRow.find('.current-stock').val(currentStock);
        itemRow.find('.current-stock-display').val(currentStock);
        itemRow.find('.unit-cost').val(price);
        
        $('#itemsContainer').append(itemRow);
        itemIndex++;
        
        $('#noItemsAlert').addClass('d-none');
        updateSummary();
    }
    
    function updateRowCalculation(row) {
        const currentStock = parseInt(row.find('.current-stock').val()) || 0;
        const adjustedQuantity = parseInt(row.find('.adjusted-quantity').val()) || 0;
        const newStock = currentStock + adjustedQuantity;
        
        row.find('.new-stock').val(newStock);
        
        // Color coding for new stock
        const newStockField = row.find('.new-stock');
        if (newStock < 0) {
            newStockField.addClass('text-danger');
        } else if (newStock > currentStock) {
            newStockField.removeClass('text-danger').addClass('text-success');
        } else {
            newStockField.removeClass('text-danger text-success');
        }
    }
    
    function updateSummary() {
        let totalProducts = 0;
        let totalQtyChange = 0;
        let totalValueImpact = 0;
        
        $('.adjustment-row').each(function() {
            const adjustedQty = parseInt($(this).find('.adjusted-quantity').val()) || 0;
            const unitCost = parseFloat($(this).find('.unit-cost').val()) || 0;
            
            if (adjustedQty !== 0) {
                totalProducts++;
                totalQtyChange += adjustedQty;
                totalValueImpact += Math.abs(adjustedQty) * unitCost;
            }
            
            updateRowCalculation($(this));
        });
        
        $('#totalProducts').val(totalProducts);
        $('#totalQtyChange').val(totalQtyChange);
        $('#totalValueImpact').val('₹' + totalValueImpact.toFixed(2));
    }
    
    function checkEmptyItems() {
        if ($('.adjustment-row').length === 0) {
            $('#noItemsAlert').removeClass('d-none');
            selectedProducts = [];
        }
    }
    
    // Form validation
    $('#adjustmentForm').submit(function(e) {
        if ($('.adjustment-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one product to adjust.');
            return false;
        }
        
        let hasValidAdjustments = false;
        let hasNegativeStock = false;
        
        $('.adjustment-row').each(function() {
            const adjustedQty = parseInt($(this).find('.adjusted-quantity').val()) || 0;
            const newStock = parseInt($(this).find('.new-stock').val()) || 0;
            
            if (adjustedQty !== 0) {
                hasValidAdjustments = true;
            }
            
            if (newStock < 0) {
                hasNegativeStock = true;
            }
        });
        
        if (!hasValidAdjustments) {
            e.preventDefault();
            alert('Please enter adjustment quantities for at least one product.');
            return false;
        }
        
        if (hasNegativeStock) {
            if (!confirm('Some products will have negative stock after adjustment. Do you want to continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Initialize
    checkEmptyItems();
});
</script>
@endpush
