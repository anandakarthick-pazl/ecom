@extends('admin.layouts.app')
<?php

// echo"<pre>";print_R($estimate);exit;
?>
@section('title', 'Edit Estimate')
@section('page_title', 'Edit Estimate #' . $estimate->estimate_number)

@section('page_actions')
    <a href="{{ route('admin.estimates.show', $estimate) }}" class="btn btn-secondary">
        <i class="fas fa-eye"></i> View Estimate
    </a>
    <a href="{{ route('admin.estimates.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
@endsection

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(1.5em + 0.5rem + 2px);
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            padding: 8px 12px;
            line-height: 1.5;
        }

        .offer-price {
            color: #dc3545;
            font-weight: bold;
        }

        .original-price {
            text-decoration: line-through;
            color: #6c757d;
        }

        .offer-badge-inline {
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .items-count-badge {
            background: #007bff;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <form action="{{ route('admin.estimates.update', $estimate) }}" method="POST" id="estimateForm">
        @csrf
        @method('PUT')

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
                                    <label for="customer_name" class="form-label">Customer Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                        id="customer_name" name="customer_name"
                                        value="{{ old('customer_name', $estimate->customer_name) }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                        id="customer_email" name="customer_email"
                                        value="{{ old('customer_email', $estimate->customer_email) }}">
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                        id="customer_phone" name="customer_phone"
                                        value="{{ old('customer_phone', $estimate->customer_phone) }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimate_date" class="form-label">Estimate Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('estimate_date') is-invalid @enderror"
                                        id="estimate_date" name="estimate_date"
                                        value="{{ old('estimate_date', optional($estimate->estimate_date)->format('Y-m-d')) }}"
                                        required>

                                    @error('estimate_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Valid Until <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                                        id="valid_until" name="valid_until"
                                        value="{{ old('valid_until', optional($estimate->valid_until)->format('Y-m-d')) }}"
                                        required>

                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Address</label>
                                    <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address"
                                        name="customer_address" rows="3">{{ old('customer_address', $estimate->customer_address) }}</textarea>
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
                        <div class="d-flex align-items-center gap-2">
                            <span class="items-count-badge">
                                <i class="fas fa-shopping-cart"></i> <span
                                    id="itemsCount">{{ $estimate->items->count() }}</span> Items
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="itemsContainer">
                            <!-- Existing items will be loaded here -->
                            @foreach ($estimate->items as $index => $item)
                                <div class="product-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label">Product <span class="text-danger">*</span></label>
                                            <select class="form-select product-select"
                                                name="items[{{ $index }}][product_id]" required>
                                                <option value="">Search and Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-price="{{ $product->effective_price ?? $product->price }}"
                                                        data-original-price="{{ $product->price }}"
                                                        data-sku="{{ $product->sku }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-has-offer="{{ $product->has_offer ?? false ? 'true' : 'false' }}"
                                                        data-discount-percentage="{{ $product->discount_percentage ?? 0 }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                        @if ($product->sku)
                                                            ({{ $product->sku }})
                                                        @endif
                                                        @if ($product->has_offer && $product->effective_price < $product->price)
                                                            - ₹{{ number_format($product->effective_price, 2) }}
                                                            (MRP: ₹{{ number_format($product->price, 2) }})
                                                            [{{ $product->discount_percentage }}% OFF]
                                                        @else
                                                            - ₹{{ number_format($product->price, 2) }}
                                                        @endif
                                                        [Stock: {{ $product->stock }}]
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control quantity-input"
                                                name="items[{{ $index }}][quantity]" min="1"
                                                value="{{ $item->quantity }}" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Unit Price <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control price-input"
                                                name="items[{{ $index }}][unit_price]" min="0"
                                                step="0.01" value="{{ $item->unit_price }}" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Total</label>
                                            <input type="text" class="form-control total-input"
                                                value="{{ $item->total_price }}" readonly>
                                        </div>

                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-outline-danger remove-item"
                                                title="Remove Item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info {{ $estimate->items->count() > 0 ? 'd-none' : '' }}"
                            id="noItemsAlert">
                            <i class="fas fa-info-circle"></i> Start by selecting a product from the dropdown below.
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
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                        placeholder="Internal notes">{{ old('notes', $estimate->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                    <textarea class="form-control @error('terms_conditions') is-invalid @enderror" id="terms_conditions"
                                        name="terms_conditions" rows="4" placeholder="Terms and conditions for this estimate">{{ old('terms_conditions', $estimate->terms_conditions) }}</textarea>
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
                        <!-- Status Badge -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                @if ($estimate->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($estimate->status === 'sent')
                                    <span class="badge bg-info">Sent</span>
                                @elseif($estimate->status === 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @elseif($estimate->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($estimate->status === 'expired')
                                    <span class="badge bg-warning">Expired</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control" id="subtotalDisplay" readonly
                                    value="{{ $estimate->subtotal }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tax_amount" class="form-label">Tax Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control @error('tax_amount') is-invalid @enderror"
                                    id="tax_amount" name="tax_amount"
                                    value="{{ old('tax_amount', $estimate->tax_amount) }}" min="0"
                                    step="0.01">
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
                                    id="discount" name="discount" value="{{ old('discount', $estimate->discount) }}"
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
                                <input type="text" class="form-control fw-bold" id="totalDisplay" readonly
                                    value="{{ $estimate->total_amount }}">
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Estimate
                            </button>
                            <a href="{{ route('admin.estimates.show', $estimate) }}" class="btn btn-secondary">
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
                <div class="col-md-5">
                    <label class="form-label">Product <span class="text-danger">*</span></label>
                    <select class="form-select product-select" name="items[__INDEX__][product_id]">
                        <option value="">Search and Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                data-price="{{ $product->effective_price ?? $product->price }}"
                                data-original-price="{{ $product->price }}" data-sku="{{ $product->sku }}"
                                data-stock="{{ $product->stock }}"
                                data-has-offer="{{ $product->has_offer ?? false ? 'true' : 'false' }}"
                                data-discount-percentage="{{ $product->discount_percentage ?? 0 }}">
                                {{ $product->name }}
                                @if ($product->sku)
                                    ({{ $product->sku }})
                                @endif
                                @if ($product->has_offer && $product->effective_price < $product->price)
                                    - ₹{{ number_format($product->effective_price, 2) }}
                                    (MRP: ₹{{ number_format($product->price, 2) }})
                                    [{{ $product->discount_percentage }}% OFF]
                                @else
                                    - ₹{{ number_format($product->price, 2) }}
                                @endif
                                [Stock: {{ $product->stock }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantity-input" name="items[__INDEX__][quantity]"
                        min="1" value="1">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                    <input type="number" class="form-control price-input" name="items[__INDEX__][unit_price]"
                        min="0" step="0.01">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control total-input" readonly>
                </div>

                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger remove-item" title="Remove Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize with the highest existing index
            let itemIndex = {{ $estimate->items->count() }};

            // Remove item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.product-row').remove();
                updateCalculations();
                updateItemsCount();
                checkEmptyItems();
            });

            // Update calculations when inputs change
            $(document).on('input', '.quantity-input, .price-input, #tax_amount, #discount', function() {
                updateRowTotal($(this).closest('.product-row'));
                updateCalculations();
            });

            // Auto-fill price when product is selected and add new row
            $(document).on('change', '.product-select', function() {
                const selectedOption = $(this).find(':selected');
                const price = selectedOption.data('price');
                const originalPrice = selectedOption.data('original-price');
                const hasOffer = selectedOption.data('has-offer') === 'true';
                const discountPercentage = selectedOption.data('discount-percentage');

                if (price) {
                    const row = $(this).closest('.product-row');
                    row.find('.price-input').val(price);

                    // Show offer information if applicable
                    if (hasOffer && price < originalPrice) {
                        row.find('.price-input').attr('title',
                            `Original Price: ₹${originalPrice} (${discountPercentage}% OFF)`);
                    } else {
                        row.find('.price-input').removeAttr('title');
                    }

                    updateRowTotal(row);
                    updateCalculations();
                    updateItemsCount();

                    // Check if this is the last row and if a product is selected
                    const isLastRow = row.is('#itemsContainer .product-row:last');
                    const hasValue = $(this).val() !== '';

                    if (isLastRow && hasValue) {
                        // Automatically add a new empty row
                        addNewItem();
                    }
                }
            });

            function addNewItem() {
                let template = $('#itemRowTemplate').html();
                template = template.replace(/__INDEX__/g, itemIndex);

                $('#itemsContainer').append(template);

                // Initialize Select2 for the newly added product select
                initializeSelect2ForRow($('#itemsContainer .product-row').last());

                itemIndex++;

                $('#noItemsAlert').addClass('d-none');
            }

            function initializeSelect2ForRow(row) {
                row.find('.product-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search and Select Product',
                    allowClear: true,
                    width: '100%',
                    // Custom matcher for searching by name, SKU, or price
                    matcher: function(params, data) {
                        // If there are no search terms, return all of the data
                        if ($.trim(params.term) === '') {
                            return data;
                        }

                        // Do not display the item if there is no 'text' property
                        if (typeof data.text === 'undefined') {
                            return null;
                        }

                        // Check if the text contains the search term (case insensitive)
                        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                            return data;
                        }

                        // Check in SKU if available
                        var $option = $(data.element);
                        var sku = $option.data('sku');
                        if (sku && sku.toString().toLowerCase().indexOf(params.term.toLowerCase()) > -
                            1) {
                            return data;
                        }

                        // Return null if the term should not be displayed
                        return null;
                    }
                });
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

            function updateItemsCount() {
                // Count only rows with selected products
                let count = 0;
                $('.product-row').each(function() {
                    if ($(this).find('.product-select').val()) {
                        count++;
                    }
                });
                $('#itemsCount').text(count);
            }

            function checkEmptyItems() {
                if ($('.product-row').length === 0) {
                    $('#noItemsAlert').removeClass('d-none');
                } else {
                    $('#noItemsAlert').addClass('d-none');
                }
            }

            // Form validation
            $('#estimateForm').submit(function(e) {
                // First, remove all empty rows (rows without selected products)
                $('.product-row').each(function() {
                    let productId = $(this).find('.product-select').val();
                    if (!productId || productId === '') {
                        $(this).remove();
                    }
                });

                // Re-index the remaining rows to ensure proper array indexing
                let newIndex = 0;
                $('.product-row').each(function() {
                    $(this).find('select, input').each(function() {
                        let name = $(this).attr('name');
                        if (name) {
                            name = name.replace(/items\[\d+\]/g, 'items[' + newIndex + ']');
                            $(this).attr('name', name);
                        }
                    });
                    newIndex++;
                });

                // Now count valid rows
                let validRows = $('.product-row').length;

                if (validRows === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the estimate.');
                    // Add back an empty row for user to continue
                    addNewItem();
                    return false;
                }

                // Check if all required fields in remaining items are filled
                let isValid = true;
                let errorMessage = '';
                $('.product-row').each(function(index) {
                    let productId = $(this).find('.product-select').val();
                    let quantity = $(this).find('.quantity-input').val();
                    let price = $(this).find('.price-input').val();

                    if (!productId || !quantity || !price) {
                        isValid = false;
                        errorMessage = 'Please fill all required fields for item #' + (index + 1);
                        return false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
            });

            // Initialize Select2 for all existing product selects
            function initializeAllSelect2() {
                $('.product-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        initializeSelect2ForRow($(this).closest('.product-row'));
                    }
                });
            }

            // Initialize calculations for existing items
            $('.product-row').each(function() {
                updateRowTotal($(this));
            });
            updateCalculations();
            checkEmptyItems();

            // Initialize Select2 for existing items
            initializeAllSelect2();

            // Add an empty row at the end for easier editing
            if ($('.product-row').length > 0) {
                addNewItem();
            }

            // Reinitialize Select2 when modal is shown (if using modals)
            $(document).on('shown.bs.modal', function() {
                initializeAllSelect2();
            });
        });
    </script>
@endpush
