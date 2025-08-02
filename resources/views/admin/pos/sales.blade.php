@extends('admin.layouts.app')

@section('title', 'POS Sales')
@section('page_title', 'POS Sales History')

@section('page_actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
        <i class="fas fa-cash-register"></i> New Sale
    </a>
    
    <!-- Bulk Actions Dropdown -->
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-tasks"></i> Bulk Actions
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="showBulkDownloadModal()">
                <i class="fas fa-download text-primary"></i> Download Selected Invoices
            </a></li>
            <li><a class="dropdown-item" href="#" onclick="showDateRangeDownloadModal()">
                <i class="fas fa-calendar-alt text-info"></i> Download by Date Range
            </a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="exportSales()">
                <i class="fas fa-file-excel text-success"></i> Export to Excel
            </a></li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pos.sales') }}">
            <div class="row">
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="payment_method">
                        <option value="">All Payment Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="upi" {{ request('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="gpay" {{ request('payment_method') == 'gpay' ? 'selected' : '' }}>GPay</option>
                        <option value="paytm" {{ request('payment_method') == 'paytm' ? 'selected' : '' }}>Paytm</option>
                        <option value="phonepe" {{ request('payment_method') == 'phonepe' ? 'selected' : '' }}>PhonePe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="commission_status">
                        <option value="">All Commission</option>
                        <option value="with_commission" {{ request('commission_status') == 'with_commission' ? 'selected' : '' }}>With Commission</option>
                        <option value="without_commission" {{ request('commission_status') == 'without_commission' ? 'selected' : '' }}>Without Commission</option>
                        <option value="pending" {{ request('commission_status') == 'pending' ? 'selected' : '' }}>Pending Commission</option>
                        <option value="paid" {{ request('commission_status') == 'paid' ? 'selected' : '' }}>Paid Commission</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.pos.sales') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daily Summary Card -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white " style="background-color: #007bff !important;">
            <div class="card-body">
                <h5>Today's Sales</h5>
                <h3 id="todaysSales">-</h3>
                <small>Total transactions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Today's Revenue</h5>
                <h3 id="todaysRevenue">-</h3>
                <small>Total amount</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Cash Sales</h5>
                <h3 id="cashSales">-</h3>
                <small>Cash transactions</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Digital Sales</h5>
                <h3 id="digitalSales">-</h3>
                <small>Card/UPI/Net Banking</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($sales->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input" title="Select All">
                        </th>
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Commission</th>
                        {{-- <th>Cashier</th> --}}
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input sale-checkbox" value="{{ $sale->id }}" data-invoice="{{ $sale->invoice_number }}">
                        </td>
                        <td>
                            <strong>{{ $sale->invoice_number }}</strong>
                        </td>
                        <td>
                            {{ $sale->created_at->format('M d, Y') }}
                            <br><small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            @if($sale->customer_name)
                                <strong>{{ $sale->customer_name }}</strong>
                                @if($sale->customer_phone)
                                    <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                                @endif
                            @else
                                <span class="text-muted">Walk-in Customer</span>
                            @endif
                        </td>
                        <td>
                            {{ $sale->total_items }} items
                            <br><small class="text-muted">{{ $sale->items->count() }} different products</small>
                        </td>
                        <td>
                            <strong>₹{{ number_format($sale->total_amount, 2) }}</strong>
                            @if($sale->discount_amount > 0)
                                <br><small class="text-success">-₹{{ number_format($sale->discount_amount, 2) }} discount</small>
                            @endif
                            @if($sale->tax_amount > 0)
                                <br><small class="text-info">+₹{{ number_format($sale->tax_amount, 2) }} tax</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                            <br><small class="text-muted">
                                Paid: ₹{{ number_format($sale->paid_amount, 2) }}
                                @if($sale->change_amount > 0)
                                    <br>Change: ₹{{ number_format($sale->change_amount, 2) }}
                                @endif
                            </small>
                        </td>
                        <td>
                            {{ $sale->cashier->name }}
                            <br><small class="text-muted">{{ $sale->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sale->status_color }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.pos.show', $sale) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($defaultBillFormat === 'thermal')
                                    <!-- Show only thermal receipt option -->
                                    <a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-outline-secondary" title="View Receipt (Thermal)" target="_blank">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                @else
                                    <!-- Show only A4 invoice option -->
                                    <a href="{{ route('admin.pos.preview-enhanced-invoice', $sale) }}" class="btn btn-outline-primary" title="Preview A4 Invoice" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                                
                                @if($sale->status === 'completed')
                                    <button type="button" class="btn btn-outline-warning" title="Process Refund" 
                                            onclick="showRefundModal({{ $sale->id }})">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $sales->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
            <h5>No sales found</h5>
            <p class="text-muted">Start making sales to see them here.</p>
            <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Sale
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="refundModalBody">
                <!-- Refund form will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Bulk Download Modal -->
{{-- <div class="modal fade" id="bulkDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download text-primary"></i> Bulk Download Invoices
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    You have selected <span id="selectedCount">0</span> invoice(s) for download.
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Download Format</label>
                    <select class="form-select" id="bulkDownloadFormat">
                        <option value="enhanced">Enhanced A4 Invoice (Recommended)</option>
                        <option value="thermal">Thermal Receipt (80mm)</option>
                        <option value="simple">Simple Receipt</option>
                        <option value="compact">Compact Multi-page PDF</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Selected Invoices</label>
                    <div id="selectedInvoicesList" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                        <!-- Selected invoices will be listed here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processBulkDownload()">
                    <i class="fas fa-download"></i> Download Selected
                </button>
            </div>
        </div>
    </div>
</div> --}}

<!-- Date Range Download Modal -->
<div class="modal fade" id="dateRangeDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt text-info"></i> Download by Date Range
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" id="dateRangeFrom" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" id="dateRangeTo" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">Download Format</label>
                    <select class="form-select" id="dateRangeFormat">
                        @if($defaultBillFormat === 'thermal')
                            <option value="thermal">Thermal Receipt (80mm)</option>
                        @else
                            <option value="enhanced">A4 Sheet Invoice</option>
                        @endif
                    </select>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">Maximum Records</label>
                    <select class="form-select" id="dateRangeLimit">
                        <option value="20">20 invoices</option>
                        <option value="50">50 invoices</option>
                        <option value="100">100 invoices</option>
                    </select>
                    <small class="text-muted">Limit to prevent large file generation</small>
                </div>
                
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="dateRangeCompletedOnly" checked>
                        <label class="form-check-label" for="dateRangeCompletedOnly">
                            Only completed sales
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="processDateRangeDownload()">
                    <i class="fas fa-download"></i> Download Range
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load daily summary
    loadDailySummary();
    
    // Initialize bulk selection functionality
    initializeBulkSelection();
    
    function loadDailySummary() {
        fetch('{{ route("admin.pos.summary.daily") }}')
            .then(response => response.json())
            .then(data => {
                $('#todaysSales').text(data.total_sales);
                $('#todaysRevenue').text('₹' + parseFloat(data.total_amount).toLocaleString());
                $('#cashSales').text('₹' + parseFloat(data.cash_sales).toLocaleString());
                
                const digitalSales = parseFloat(data.card_sales) + parseFloat(data.upi_sales);
                $('#digitalSales').text('₹' + digitalSales.toLocaleString());
            })
            .catch(error => console.error('Error loading daily summary:', error));
    }
    
    function initializeBulkSelection() {
        // Select All functionality
        $('#selectAll').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.sale-checkbox').prop('checked', isChecked);
            updateBulkActionButtons();
        });
        
        // Individual checkbox change
        $(document).on('change', '.sale-checkbox', function() {
            updateSelectAllState();
            updateBulkActionButtons();
        });
    }
    
    function updateSelectAllState() {
        const totalCheckboxes = $('.sale-checkbox').length;
        const checkedCheckboxes = $('.sale-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAll').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAll').prop('indeterminate', true).prop('checked', false);
        }
    }
    
    function updateBulkActionButtons() {
        const selectedCount = $('.sale-checkbox:checked').length;
        // You can enable/disable bulk action buttons based on selection
        console.log('Selected sales:', selectedCount);
    }
    
    // Bulk download functions
    window.showBulkDownloadModal = function() {
        const selectedSales = [];
        $('.sale-checkbox:checked').each(function() {
            selectedSales.push({
                id: $(this).val(),
                invoice: $(this).data('invoice')
            });
        });
        
        if (selectedSales.length === 0) {
            alert('Please select at least one sale to download.');
            return;
        }
        
        // Update modal content
        $('#selectedCount').text(selectedSales.length);
        
        let invoicesList = '';
        selectedSales.forEach(sale => {
            invoicesList += `<div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <span><i class="fas fa-file-pdf text-danger"></i> ${sale.invoice}</span>
                <small class="text-muted">ID: ${sale.id}</small>
            </div>`;
        });
        $('#selectedInvoicesList').html(invoicesList);
        
        $('#bulkDownloadModal').modal('show');
    };
    
    window.showDateRangeDownloadModal = function() {
        $('#dateRangeDownloadModal').modal('show');
    };
    
    window.processBulkDownload = function() {
        const selectedSales = [];
        $('.sale-checkbox:checked').each(function() {
            selectedSales.push($(this).val());
        });
        
        if (selectedSales.length === 0) {
            alert('No sales selected.');
            return;
        }
        
        const format = $('#bulkDownloadFormat').val();
        
        // Show loading state
        const btn = $(event.target);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        btn.prop('disabled', true);
        
        // Create form and submit
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.pos.download-multiple-receipts") }}'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        // Add selected sale IDs
        selectedSales.forEach(saleId => {
            form.append($('<input>', {
                type: 'hidden',
                name: 'sale_ids[]',
                value: saleId
            }));
        });
        
        // Add format
        form.append($('<input>', {
            type: 'hidden',
            name: 'format',
            value: format
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Reset button state after delay
        setTimeout(() => {
            btn.html(originalText);
            btn.prop('disabled', false);
            $('#bulkDownloadModal').modal('hide');
        }, 3000);
    };
    
    window.processDateRangeDownload = function() {
        const dateFrom = $('#dateRangeFrom').val();
        const dateTo = $('#dateRangeTo').val();
        const format = $('#dateRangeFormat').val();
        const limit = $('#dateRangeLimit').val();
        const completedOnly = $('#dateRangeCompletedOnly').prop('checked');
        
        if (!dateFrom || !dateTo) {
            alert('Please select both from and to dates.');
            return;
        }
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            alert('From date cannot be later than to date.');
            return;
        }
        
        // Show loading state
        const btn = $(event.target);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        btn.prop('disabled', true);
        
        // Create form and submit
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.pos.download-receipts-by-date") }}'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        // Add parameters
        form.append($('<input>', { type: 'hidden', name: 'date_from', value: dateFrom }));
        form.append($('<input>', { type: 'hidden', name: 'date_to', value: dateTo }));
        form.append($('<input>', { type: 'hidden', name: 'format', value: format }));
        form.append($('<input>', { type: 'hidden', name: 'limit', value: limit }));
        form.append($('<input>', { type: 'hidden', name: 'completed_only', value: completedOnly ? '1' : '0' }));
        
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Reset button state after delay
        setTimeout(() => {
            btn.html(originalText);
            btn.prop('disabled', false);
            $('#dateRangeDownloadModal').modal('hide');
        }, 3000);
    };
    
    window.exportSales = function() {
        // You can implement Excel export functionality here
        alert('Excel export functionality will be implemented in future update.');
    };
    
    window.showRefundModal = function(saleId) {
        // Load refund form
        fetch(`{{ url('admin/pos/sales') }}/${saleId}`)
            .then(response => response.text())
            .then(html => {
                // Extract sale items for refund form
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                let refundForm = `
                    <form id="refundForm" data-sale-id="${saleId}">
                        <div class="mb-3">
                            <label class="form-label">Refund Reason</label>
                            <input type="text" class="form-control" name="reason" required 
                                   placeholder="Reason for refund">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Items to Refund</label>
                            <div id="refundItems">
                                <!-- Items will be loaded here -->
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Process Refund</button>
                        </div>
                    </form>
                `;
                
                $('#refundModalBody').html(refundForm);
                $('#refundModal').modal('show');
            })
            .catch(error => {
                console.error('Error loading sale details:', error);
                alert('Error loading sale details');
            });
    };
    
    // Handle refund form submission
    $(document).on('submit', '#refundForm', function(e) {
        e.preventDefault();
        
        const saleId = $(this).data('sale-id');
        const formData = new FormData(this);
        
        fetch(`{{ url('admin/pos/sales') }}/${saleId}/refund`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Refund processed successfully!');
                $('#refundModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error processing refund:', error);
            alert('Error processing refund');
        });
    });
});
</script>
@endpush
