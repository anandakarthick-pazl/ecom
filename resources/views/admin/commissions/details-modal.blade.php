<div class="row">
    <div class="col-md-6">
        <h6 class="font-weight-bold text-primary">Commission Information</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <td><strong>Reference Name:</strong></td>
                <td>{{ $commission->reference_name }}</td>
            </tr>
            <tr>
                <td><strong>Commission %:</strong></td>
                <td>{{ $commission->formatted_commission_percentage }}</td>
            </tr>
            <tr>
                <td><strong>Base Amount:</strong></td>
                <td>{{ $commission->formatted_base_amount }}</td>
            </tr>
            <tr>
                <td><strong>Commission Amount:</strong></td>
                <td class="text-success"><strong>{{ $commission->formatted_commission_amount }}</strong></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge badge-{{ $commission->status_color }}">
                        {{ $commission->status_text }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Created:</strong></td>
                <td>{{ $commission->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            @if($commission->paid_at)
            <tr>
                <td><strong>Paid Date:</strong></td>
                <td>{{ $commission->paid_at->format('M d, Y h:i A') }}</td>
            </tr>
            @endif
            @if($commission->paidBy)
            <tr>
                <td><strong>Paid By:</strong></td>
                <td>{{ $commission->paidBy->name }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="font-weight-bold text-primary">Related Sale Information</h6>
        @if($commission->posSale)
            <table class="table table-sm table-borderless">
                <tr>
                    <td><strong>Invoice Number:</strong></td>
                    <td>
                        <a href="{{ route('admin.pos.show', $commission->posSale) }}" target="_blank">
                            {{ $commission->posSale->invoice_number }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>Sale Date:</strong></td>
                    <td>{{ $commission->posSale->sale_date }}</td>
                </tr>
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td>{{ $commission->posSale->customer_name ?? 'Walk-in' }}</td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>₹{{ number_format($commission->posSale->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td>{{ ucfirst($commission->posSale->payment_method) }}</td>
                </tr>
                @if($commission->posSale->cashier)
                <tr>
                    <td><strong>Cashier:</strong></td>
                    <td>{{ $commission->posSale->cashier->name }}</td>
                </tr>
                @endif
            </table>
        @elseif($commission->order)
            <table class="table table-sm table-borderless">
                <tr>
                    <td><strong>Order Number:</strong></td>
                    <td>{{ $commission->order->order_number }}</td>
                </tr>
                <tr>
                    <td><strong>Order Date:</strong></td>
                    <td>{{ $commission->order->created_at->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td>{{ $commission->order->customer_name }}</td>
                </tr>
                <tr>
                    <td><strong>Total Amount:</strong></td>
                    <td>₹{{ number_format($commission->order->total, 2) }}</td>
                </tr>
            </table>
        @else
            <p class="text-muted">No related sale information available.</p>
        @endif
    </div>
</div>

@if($commission->notes)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="font-weight-bold text-primary">Notes</h6>
        <div class="alert alert-light">
            {!! nl2br(e($commission->notes)) !!}
        </div>
    </div>
</div>
@endif

<div class="row mt-3">
    <div class="col-12">
        <h6 class="font-weight-bold text-primary">Actions</h6>
        <div class="btn-group" role="group">
            @if($commission->status === 'pending')
                <button type="button" class="btn btn-success btn-sm" onclick="markAsPaid({{ $commission->id }})">
                    <i class="fas fa-check"></i> Mark as Paid
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="cancelCommission({{ $commission->id }})">
                    <i class="fas fa-times"></i> Cancel
                </button>
            @elseif($commission->status === 'paid')
                <button type="button" class="btn btn-warning btn-sm" onclick="revertToPending({{ $commission->id }})">
                    <i class="fas fa-undo"></i> Revert to Pending
                </button>
            @endif
            @if($commission->posSale)
                <a href="{{ route('admin.pos.show', $commission->posSale) }}" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Sale
                </a>
            @endif
        </div>
    </div>
</div>
