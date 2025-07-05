@extends('admin.layouts.app')

@section('title', 'Create GRN')
@section('page_title', 'Create Goods Receipt Note')

@section('page_actions')
<a href="{{ route('admin.grns.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
</a>
@endsection

@section('content')
<form action="{{ route('admin.grns.store') }}" method="POST" id="grnForm">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">GRN Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_order_id" class="form-label">Purchase Order <span class="text-danger">*</span></label>
                                <select class="form-select @error('purchase_order_id') is-invalid @enderror" 
                                        id="purchase_order_id" name="purchase_order_id" required>
                                    <option value="">Select Purchase Order</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}" {{ (old('purchase_order_id') == $po->id || request('po_id') == $po->id) ? 'selected' : '' }}>
                                            {{ $po->po_number }} - {{ $po->supplier->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('purchase_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="received_date" class="form-label">Received Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('received_date') is-invalid @enderror" 
                                       id="received_date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required>
                                @error('received_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" 
                                       id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}">
                                @error('invoice_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" 
                                       id="invoice_date" name="invoice_date" value="{{ old('invoice_date') }}">
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="invoice_amount" class="form-label">Invoice Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control @error('invoice_amount') is-invalid @enderror" 
                                           id="invoice_amount" name="invoice_amount" value="{{ old('invoice_amount') }}" 
                                           min="0" step="0.01">
                                </div>
                                @error('invoice_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Additional notes about the goods receipt">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PO Items -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Purchase Order Items</h5>
                </div>
                <div class="card-body">
                    <div id="poItemsContainer">
                        @if($selectedPo)
                            @foreach($selectedPo->items as $index => $item)
                                <div class="po-item-row border rounded p-3 mb-3">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][ordered_quantity]" value="{{ $item->quantity }}">
                                    
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Product</label>
                                            <input type="text" class="form-control" value="{{ $item->product->name }}" readonly>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Ordered Qty</label>
                                            <input type="text" class="form-control" value="{{ $item->quantity }}" readonly>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Received Qty <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control received-qty" 
                                                   name="items[{{ $index }}][received_quantity]" 
                                                   min="0" max="{{ $item->quantity }}" value="{{ old('items.'.$index.'.received_quantity', 0) }}" required>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Cost</label>
                                            <input type="number" class="form-control unit-cost" 
                                                   name="items[{{ $index }}][unit_cost]" 
                                                   min="0" step="0.01" value="{{ old('items.'.$index.'.unit_cost', $item->unit_price) }}">
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <label class="form-label">Total Cost</label>
                                            <input type="text" class="form-control total-cost" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info" id="noPOSelected">
                                <i class="fas fa-info-circle"></i> Please select a purchase order to load items.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">Receipt Summary</h5>
                </div>
                <div class="card-body">
                    <div id="poSummary" style="{{ $selectedPo ? '' : 'display: none;' }}">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="supplierName" readonly 
                                   value="{{ $selectedPo ? $selectedPo->supplier->display_name : '' }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">PO Date</label>
                            <input type="text" class="form-control" id="poDate" readonly 
                                   value="{{ $selectedPo ? $selectedPo->po_date->format('M d, Y') : '' }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">PO Amount</label>
                            <input type="text" class="form-control" id="poAmount" readonly 
                                   value="{{ $selectedPo ? '₹'.number_format($selectedPo->total_amount, 2) : '' }}">
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Items</label>
                            <input type="text" class="form-control" id="totalItems" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Received Qty</label>
                            <input type="text" class="form-control" id="totalReceivedQty" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Received Value</label>
                            <input type="text" class="form-control" id="totalReceivedValue" readonly>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn" {{ $selectedPo ? '' : 'disabled' }}>
                            <i class="fas fa-save"></i> Create GRN
                        </button>
                        <a href="{{ route('admin.grns.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load PO items when PO is selected
    $('#purchase_order_id').change(function() {
        const poId = $(this).val();
        
        if (!poId) {
            $('#poItemsContainer').html('<div class="alert alert-info" id="noPOSelected"><i class="fas fa-info-circle"></i> Please select a purchase order to load items.</div>');
            $('#poSummary').hide();
            $('#submitBtn').prop('disabled', true);
            return;
        }
        
        // Load PO items via AJAX
        fetch(`{{ url('admin/grns/po') }}/${poId}/items`)
            .then(response => response.json())
            .then(data => {
                loadPOItems(data);
                updateSummary(data);
                $('#submitBtn').prop('disabled', false);
            })
            .catch(error => {
                console.error('Error loading PO items:', error);
                alert('Error loading purchase order items');
            });
    });
    
    // Update calculations when quantities change
    $(document).on('input', '.received-qty, .unit-cost', function() {
        updateRowTotal($(this).closest('.po-item-row'));
        updateTotalSummary();
    });
    
    function loadPOItems(po) {
        let html = '';
        
        po.items.forEach((item, index) => {
            html += `
                <div class="po-item-row border rounded p-3 mb-3">
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][ordered_quantity]" value="${item.quantity}">
                    
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" value="${item.product.name}" readonly>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Ordered Qty</label>
                            <input type="text" class="form-control" value="${item.quantity}" readonly>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Received Qty <span class="text-danger">*</span></label>
                            <input type="number" class="form-control received-qty" 
                                   name="items[${index}][received_quantity]" 
                                   min="0" max="${item.quantity}" value="0" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Unit Cost</label>
                            <input type="number" class="form-control unit-cost" 
                                   name="items[${index}][unit_cost]" 
                                   min="0" step="0.01" value="${item.unit_price}">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Total Cost</label>
                            <input type="text" class="form-control total-cost" readonly>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#poItemsContainer').html(html);
        updateTotalSummary();
    }
    
    function updateSummary(po) {
        $('#supplierName').val(po.supplier.display_name);
        $('#poDate').val(new Date(po.po_date).toLocaleDateString());
        $('#poAmount').val('₹' + parseFloat(po.total_amount).toLocaleString());
        $('#totalItems').val(po.items.length);
        $('#poSummary').show();
    }
    
    function updateRowTotal(row) {
        const receivedQty = parseFloat(row.find('.received-qty').val()) || 0;
        const unitCost = parseFloat(row.find('.unit-cost').val()) || 0;
        const total = receivedQty * unitCost;
        
        row.find('.total-cost').val('₹' + total.toFixed(2));
    }
    
    function updateTotalSummary() {
        let totalReceivedQty = 0;
        let totalReceivedValue = 0;
        
        $('.po-item-row').each(function() {
            const receivedQty = parseFloat($(this).find('.received-qty').val()) || 0;
            const unitCost = parseFloat($(this).find('.unit-cost').val()) || 0;
            
            totalReceivedQty += receivedQty;
            totalReceivedValue += receivedQty * unitCost;
            
            updateRowTotal($(this));
        });
        
        $('#totalReceivedQty').val(totalReceivedQty);
        $('#totalReceivedValue').val('₹' + totalReceivedValue.toFixed(2));
    }
    
    // Form validation
    $('#grnForm').submit(function(e) {
        const poId = $('#purchase_order_id').val();
        if (!poId) {
            e.preventDefault();
            alert('Please select a purchase order.');
            return false;
        }
        
        let hasReceivedItems = false;
        $('.received-qty').each(function() {
            if (parseFloat($(this).val()) > 0) {
                hasReceivedItems = true;
                return false;
            }
        });
        
        if (!hasReceivedItems) {
            e.preventDefault();
            alert('Please enter received quantities for at least one item.');
            return false;
        }
    });
    
    // Initialize if PO is already selected
    if ($('#purchase_order_id').val()) {
        updateTotalSummary();
    }
});
</script>
@endpush
