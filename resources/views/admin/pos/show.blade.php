@extends('admin.layouts.app')

@section('title', 'POS Sale Details - #' . $sale->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt text-primary"></i> 
            POS Sale Details - #{{ $sale->invoice_number }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Sales
            </a>
            <a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-print me-2"></i>Print Receipt
            </a>
            <a href="{{ route('admin.pos.download-bill', $sale) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Download Bill
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sale Details -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-shopping-cart me-2"></i>Sale Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary">Customer Information:</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $sale->customer_name ?: 'Walk-in Customer' }}</p>
                            <p class="mb-1"><strong>Phone:</strong> {{ $sale->customer_phone ?: 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary">Sale Details:</h5>
                            <p class="mb-1"><strong>Invoice:</strong> #{{ $sale->invoice_number }}</p>
                            <p class="mb-1"><strong>Date:</strong> {{ $sale->sale_date->format('M d, Y') }}</p>
                            <p class="mb-1"><strong>Cashier:</strong> {{ $sale->cashier ? $sale->cashier->name : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Tax</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->offer_applied)
                                            <br><small class="text-success">Offer: {{ $item->offer_applied }}</small>
                                        @endif
                                        @if($item->notes)
                                            <br><small class="text-muted">{{ $item->notes }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">
                                        ₹{{ number_format($item->unit_price, 2) }}
                                        @if($item->original_price > $item->unit_price)
                                            <br><small class="text-muted"><del>₹{{ number_format($item->original_price, 2) }}</del></small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item->discount_amount > 0)
                                            ₹{{ number_format($item->discount_amount, 2) }}
                                            @if($item->discount_percentage > 0)
                                                <br><small>({{ number_format($item->discount_percentage, 1) }}%)</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item->tax_amount > 0)
                                            ₹{{ number_format($item->tax_amount, 2) }}
                                            <br><small>({{ number_format($item->tax_percentage, 1) }}%)</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">₹{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">₹{{ number_format($sale->subtotal, 2) }}</td>
                                </tr>
                                @if($sale->tax_amount > 0)
                                <tr>
                                    <td colspan="6" class="text-end">Tax:</td>
                                    <td class="text-end">₹{{ number_format($sale->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($sale->discount_amount > 0)
                                <tr>
                                    <td colspan="6" class="text-end">Discount:</td>
                                    <td class="text-end text-danger">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-dark">
                                    <td colspan="6" class="text-end fw-bold">Total Amount:</td>
                                    <td class="text-end fw-bold">₹{{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($sale->notes)
                    <div class="mt-4">
                        <h6 class="text-primary">Notes:</h6>
                        <p class="border p-3 bg-light">{{ $sale->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Payment Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-credit-card me-2"></i>Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Payment Method:</label>
                        <p class="text-capitalize">
                            @if($sale->payment_method === 'cash')
                                <i class="fas fa-money-bill-wave text-success"></i> Cash
                            @elseif($sale->payment_method === 'card')
                                <i class="fas fa-credit-card text-primary"></i> Card
                            @elseif($sale->payment_method === 'upi')
                                <i class="fas fa-mobile-alt text-info"></i> UPI
                            @else
                                <i class="fas fa-wallet text-warning"></i> {{ ucfirst($sale->payment_method) }}
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Total Amount:</label>
                        <p class="h5 text-primary">₹{{ number_format($sale->total_amount, 2) }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Paid Amount:</label>
                        <p class="h5 text-success">₹{{ number_format($sale->paid_amount, 2) }}</p>
                    </div>
                    
                    @if($sale->change_amount > 0)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Change:</label>
                        <p class="h5 text-warning">₹{{ number_format($sale->change_amount, 2) }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Status:</label>
                        <p>
                            @if($sale->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($sale->status === 'refunded')
                                <span class="badge bg-danger">Refunded</span>
                            @elseif($sale->status === 'partial_refund')
                                <span class="badge bg-warning">Partial Refund</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($sale->status) }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Commission Information (if applicable) -->
            @if(isset($sale->commission) && $sale->commission)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-percentage me-2"></i>Commission Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="fw-bold text-muted">Reference:</label>
                        <p>{{ $sale->commission->reference_name }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold text-muted">Percentage:</label>
                        <p>{{ $sale->commission->commission_percentage }}%</p>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold text-muted">Amount:</label>
                        <p class="h5 text-info">₹{{ number_format($sale->commission->commission_amount, 2) }}</p>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold text-muted">Status:</label>
                        <p>
                            @if($sale->commission->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($sale->commission->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($sale->commission->status) }}</span>
                            @endif
                        </p>
                    </div>
                    @if($sale->commission->notes)
                    <div class="mb-2">
                        <label class="fw-bold text-muted">Notes:</label>
                        <p>{{ $sale->commission->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tools me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($sale->status === 'completed')
                            <button class="btn btn-danger btn-sm" onclick="refundSale()">
                                <i class="fas fa-undo me-2"></i>Process Refund
                            </button>
                        @endif
                        <a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-print me-2"></i>Print Receipt
                        </a>
                        <a href="{{ route('admin.pos.download-bill', $sale) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-2"></i>Download Bill
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function refundSale() {
        if (confirm('Are you sure you want to refund this sale? This action will restore the stock.')) {
            // Implement refund logic here
            alert('Refund functionality to be implemented');
        }
    }
</script>
@endpush
