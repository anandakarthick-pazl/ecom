@extends('admin.layouts.app')

@section('title', 'Commission Details')
@section('page_title', 'Commission Details #' . $commission->id)

@section('page_actions')
<div class="btn-group">
    @if($commission->canBePaid())
        <button type="button" class="btn btn-success" onclick="markAsPaid({{ $commission->id }})">
            <i class="fas fa-check"></i> Mark as Paid
        </button>
    @endif
    
    @if($commission->canBeCancelled())
        <button type="button" class="btn btn-danger" onclick="markAsCancelled({{ $commission->id }})">
            <i class="fas fa-times"></i> Cancel
        </button>
    @endif
    
    <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Commission Details -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-percentage"></i> Commission Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="section-title">Commission Details</h6>
                        <p class="mb-1">
                            <strong><i class="fas fa-hashtag"></i> Commission ID:</strong>
                            <span class="badge bg-dark">#{{ $commission->id }}</span>
                        </p>
                        <p class="mb-1">
                            <strong><i class="fas fa-user-tie"></i> Reference Name:</strong>
                            <span class="text-primary">{{ $commission->reference_name }}</span>
                        </p>
                        <p class="mb-1">
                            <strong><i class="fas fa-tag"></i> Reference Type:</strong>
                            @if($commission->reference_type == 'pos_sale')
                                <span class="badge bg-info">POS Sale</span>
                            @else
                                <span class="badge bg-primary">Online Order</span>
                            @endif
                        </p>
                        <p class="mb-1">
                            <strong><i class="fas fa-percent"></i> Commission Rate:</strong>
                            <span class="badge bg-secondary">{{ number_format($commission->commission_percentage, 2) }}%</span>
                        </p>
                        <p class="mb-3">
                            <strong><i class="fas fa-calendar"></i> Created:</strong>
                            {{ $commission->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="section-title">Amount Calculation</h6>
                        <div class="commission-calculation">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Base Amount:</span>
                                <span>₹{{ number_format($commission->base_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Commission Rate:</span>
                                <span>{{ number_format($commission->commission_percentage, 2) }}%</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Commission Amount:</strong>
                                <strong class="text-success fs-5">₹{{ number_format($commission->commission_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($commission->notes)
                    <hr>
                    <h6 class="section-title">Notes</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-sticky-note"></i>
                        {{ $commission->notes }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Related Sale/Order -->
        @if($commission->reference_type == 'pos_sale' && $commission->posSale)
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cash-register"></i> Related POS Sale
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Invoice Number:</strong> {{ $commission->posSale->invoice_number }}
                            </p>
                            <p class="mb-1">
                                <strong>Customer:</strong> {{ $commission->posSale->customer_name ?? 'Walk-in' }}
                            </p>
                            <p class="mb-1">
                                <strong>Sale Date:</strong> {{ $commission->posSale->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Total Amount:</strong> ₹{{ number_format($commission->posSale->total_amount, 2) }}
                            </p>
                            <p class="mb-1">
                                <strong>Payment Method:</strong> {{ ucfirst($commission->posSale->payment_method) }}
                            </p>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $commission->posSale->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($commission->posSale->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.pos.sales.show', $commission->posSale) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View POS Sale
                        </a>
                    </div>
                </div>
            </div>
        @elseif($commission->reference_type == 'order' && $commission->order)
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart"></i> Related Online Order
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Order Number:</strong> {{ $commission->order->order_number }}
                            </p>
                            <p class="mb-1">
                                <strong>Customer:</strong> {{ $commission->order->customer_name }}
                            </p>
                            <p class="mb-1">
                                <strong>Order Date:</strong> {{ $commission->order->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <strong>Total Amount:</strong> ₹{{ number_format($commission->order->total, 2) }}
                            </p>
                            <p class="mb-1">
                                <strong>Payment Method:</strong> {{ ucfirst($commission->order->payment_method) }}
                            </p>
                            <p class="mb-1">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $commission->order->status_color }}">
                                    {{ ucfirst($commission->order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.show', $commission->order) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Order
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Status Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Commission Status
                </h6>
            </div>
            <div class="card-body text-center">
                @if($commission->status === 'paid')
                    <div class="status-display text-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>Commission Paid</h5>
                        <p class="text-muted">₹{{ number_format($commission->commission_amount, 2) }}</p>
                        @if($commission->paid_at)
                            <small class="text-muted">
                                Paid on {{ $commission->paid_at->format('M d, Y h:i A') }}
                            </small>
                        @endif
                    </div>
                @elseif($commission->status === 'cancelled')
                    <div class="status-display text-danger">
                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                        <h5>Commission Cancelled</h5>
                        <p class="text-muted">₹{{ number_format($commission->commission_amount, 2) }}</p>
                    </div>
                @else
                    <div class="status-display text-warning">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h5>Payment Pending</h5>
                        <p class="text-muted">₹{{ number_format($commission->commission_amount, 2) }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Payment Information -->
        @if($commission->status === 'paid')
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Payment Information
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Paid At:</strong><br>
                        {{ $commission->paid_at->format('M d, Y h:i A') }}
                    </p>
                    @if($commission->paidBy)
                        <p class="mb-1">
                            <strong>Paid By:</strong><br>
                            {{ $commission->paidBy->name }}
                        </p>
                    @endif
                    <p class="mb-0">
                        <strong>Amount:</strong><br>
                        <span class="text-success fs-5">₹{{ number_format($commission->commission_amount, 2) }}</span>
                    </p>
                </div>
            </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h6>
            </div>
            <div class="card-body">
                @if($commission->canBePaid())
                    <button type="button" class="btn btn-success btn-sm w-100 mb-2" 
                            onclick="markAsPaid({{ $commission->id }})">
                        <i class="fas fa-check"></i> Mark as Paid
                    </button>
                @endif
                
                @if($commission->canBeCancelled())
                    <button type="button" class="btn btn-danger btn-sm w-100 mb-2" 
                            onclick="markAsCancelled({{ $commission->id }})">
                        <i class="fas fa-times"></i> Cancel Commission
                    </button>
                @endif
                
                <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary btn-sm w-100">
                    <i class="fas fa-list"></i> All Commissions
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Commission as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAsPaidForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Commission Amount:</strong> ₹{{ number_format($commission->commission_amount, 2) }}<br>
                        <strong>Reference:</strong> {{ $commission->reference_name }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Add any notes about the payment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Mark as Paid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="markAsCancelledModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="markAsCancelledForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Commission Amount:</strong> ₹{{ number_format($commission->commission_amount, 2) }}<br>
                        <strong>Reference:</strong> {{ $commission->reference_name }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required
                                  placeholder="Please provide reason for cancellation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel Commission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsPaid(commissionId) {
    const form = document.getElementById('markAsPaidForm');
    form.action = `/admin/commissions/${commissionId}/mark-as-paid`;
    
    const modal = new bootstrap.Modal(document.getElementById('markAsPaidModal'));
    modal.show();
}

function markAsCancelled(commissionId) {
    const form = document.getElementById('markAsCancelledForm');
    form.action = `/admin/commissions/${commissionId}/mark-as-cancelled`;
    
    const modal = new bootstrap.Modal(document.getElementById('markAsCancelledModal'));
    modal.show();
}
</script>
@endpush
