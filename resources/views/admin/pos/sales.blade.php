@extends('admin.layouts.app')

@section('title', 'POS Sales')
@section('page_title', 'POS Sales History')

@section('page_actions')
<a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
    <i class="fas fa-cash-register"></i> New Sale
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.pos.sales') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                        <th>Invoice #</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Cashier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
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
                                <a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-outline-secondary" title="View Receipt" target="_blank">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                {{-- <a href="{{ route('admin.pos.download-bill', $sale) }}" class="btn btn-outline-primary" title="Download Bill PDF">
                                    <i class="fas fa-download"></i>
                                </a> --}}
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load daily summary
    loadDailySummary();
    
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
