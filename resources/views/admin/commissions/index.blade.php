@extends('admin.layouts.app')

@section('title', 'Commission Management')
@section('page_title', 'Commission Management')

@push('styles')
<style>
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        border: none;
        padding: 12px 8px;
        font-size: 12px;
    }
    
    .table td {
        vertical-align: middle;
        text-align: center;
        padding: 10px 8px;
        border: 1px solid #e3e6f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn {
        margin: 0 1px;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .commission-checkbox {
        transform: scale(1.2);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,.1);
    }
</style>
@endpush

@section('page_actions')
<div class="btn-group" role="group">
    <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Sales Report
    </a>
    <button type="button" class="btn btn-success" onclick="bulkMarkAsPaid()">
        <i class="fas fa-check-circle"></i> Bulk Mark as Paid
    </button>
    <button type="button" class="btn btn-info" onclick="exportCommissions()">
        <i class="fas fa-file-excel"></i> Export
    </button>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_pending'], 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $stats['count_pending'] }} records</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_paid'], 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $stats['count_paid'] }} records</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_this_month'], 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $stats['count_this_month'] }} records</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Commission</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">₹{{ number_format($stats['total_pending'] + $stats['total_paid'], 2) }}</div>
                            <div class="text-xs text-gray-500">All time</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.commissions.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Reference Name</label>
                            <input type="text" name="reference_name" class="form-control" value="{{ request('reference_name') }}" placeholder="Search by reference name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Commission Records</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square"></i> Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-square"></i> Clear All
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                            </th>
                            <th>Invoice #</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Commission</th>
                            <th>Cashier</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                            <tr>
                                <td>
                                    <input type="checkbox" name="commission_ids[]" value="{{ $commission->id }}" 
                                           class="commission-checkbox" {{ $commission->status !== 'pending' ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    @if($commission->posSale)
                                        <a href="{{ route('admin.pos.show', $commission->posSale) }}" target="_blank" class="text-primary font-weight-bold">
                                            {{ $commission->posSale->invoice_number }}
                                        </a>
                                        <br><small class="text-muted">{{ $commission->reference_name }}</small>
                                    @elseif($commission->order)
                                        <a href="{{ route('admin.orders.show', $commission->order) }}" target="_blank" class="text-primary font-weight-bold">
                                            {{ $commission->order->order_number }}
                                        </a>
                                        <br><small class="text-muted">{{ $commission->reference_name }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                        <br><small class="text-muted">{{ $commission->reference_name }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($commission->posSale)
                                        <strong>{{ $commission->posSale->created_at->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $commission->posSale->created_at->format('h:i A') }}</small>
                                    @elseif($commission->order)
                                        <strong>{{ $commission->order->created_at->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $commission->order->created_at->format('h:i A') }}</small>
                                    @else
                                        <strong>{{ $commission->created_at->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">{{ $commission->created_at->format('h:i A') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($commission->posSale && $commission->posSale->customer_name)
                                        <strong>{{ $commission->posSale->customer_name }}</strong>
                                        @if($commission->posSale->customer_phone)
                                            <br><small class="text-muted">{{ $commission->posSale->customer_phone }}</small>
                                        @endif
                                    @elseif($commission->order && $commission->order->customer)
                                        <strong>{{ $commission->order->customer->name }}</strong>
                                        @if($commission->order->customer->phone)
                                            <br><small class="text-muted">{{ $commission->order->customer->phone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Walk-in Customer</span>
                                    @endif
                                </td>
                                <td>
                                    @if($commission->posSale)
                                        <strong>{{ $commission->posSale->items->count() ?? 0 }} items</strong>
                                        @if($commission->posSale->items->count() > 0)
                                            <br><small class="text-muted">{{ $commission->posSale->items->sum('quantity') }} qty</small>
                                        @endif
                                    @elseif($commission->order)
                                        <strong>{{ $commission->order->items->count() ?? 0 }} items</strong>
                                        @if($commission->order->items->count() > 0)
                                            <br><small class="text-muted">{{ $commission->order->items->sum('quantity') }} qty</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-success">{{ $commission->formatted_base_amount }}</strong>
                                    @if($commission->posSale && $commission->posSale->tax_amount > 0)
                                        <br><small class="text-muted">+₹{{ number_format($commission->posSale->tax_amount, 2) }} tax</small>
                                    @endif
                                </td>
                                <td>
                                    @if($commission->posSale)
                                        <span class="badge badge-info">{{ ucfirst($commission->posSale->payment_method ?? 'Cash') }}</span>
                                        @if($commission->posSale->payment_status)
                                            <br><small class="text-muted">{{ ucfirst($commission->posSale->payment_status) }}</small>
                                        @endif
                                    @elseif($commission->order)
                                        <span class="badge badge-info">{{ ucfirst($commission->order->payment_method ?? 'Online') }}</span>
                                        @if($commission->order->payment_status)
                                            <br><small class="text-muted">{{ ucfirst($commission->order->payment_status) }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $commission->formatted_commission_amount }}</strong>
                                    <br><small class="text-muted">{{ $commission->formatted_commission_percentage }}</small>
                                </td>
                                <td>
                                    @if($commission->posSale && $commission->posSale->user)
                                        <strong>{{ $commission->posSale->user->name }}</strong>
                                        @if($commission->posSale->user->role)
                                            <br><small class="text-muted">{{ ucfirst($commission->posSale->user->role) }}</small>
                                        @endif
                                    @elseif($commission->paidBy)
                                        <strong>{{ $commission->paidBy->name }}</strong>
                                        <br><small class="text-muted">Paid by</small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $commission->status_color }}">
                                        {{ $commission->status_text }}
                                    </span>
                                    @if($commission->paid_at)
                                        <br><small class="text-muted">{{ $commission->paid_at->format('M d, Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($commission->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="markAsPaid({{ $commission->id }})" title="Mark as Paid">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="cancelCommission({{ $commission->id }})" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($commission->status === 'paid')
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="revertToPending({{ $commission->id }})" title="Revert to Pending">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="viewDetails({{ $commission->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">No commission records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($commissions->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $commissions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Commission Details Modal -->
<div class="modal fade" id="commissionDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commissionDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Payment Modal -->
<div class="modal fade" id="bulkPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Mark as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark <span id="selectedCount">0</span> commission(s) as paid?</p>
                <div class="form-group">
                    <label>Payment Notes (Optional)</label>
                    <textarea class="form-control" id="bulkPaymentNotes" placeholder="Add notes about this bulk payment..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="processBulkPayment()">
                    <i class="fas fa-check-circle"></i> Mark as Paid
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select/Deselect functionality
function selectAll() {
    $('.commission-checkbox:not(:disabled)').prop('checked', true);
    updateSelectedCount();
}

function clearSelection() {
    $('.commission-checkbox').prop('checked', false);
    $('#selectAllCheckbox').prop('checked', false);
    updateSelectedCount();
}

function toggleSelectAll() {
    const isChecked = $('#selectAllCheckbox').prop('checked');
    $('.commission-checkbox:not(:disabled)').prop('checked', isChecked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCount = $('.commission-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);
}

// Update count when individual checkboxes change
$(document).on('change', '.commission-checkbox', function() {
    updateSelectedCount();
});

// Individual commission actions
function markAsPaid(commissionId) {
    if (confirm('Mark this commission as paid?')) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/mark-paid`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to update commission status'));
            }
        });
    }
}

function cancelCommission(commissionId) {
    const reason = prompt('Please provide a reason for cancelling this commission:');
    if (reason && reason.trim()) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/cancel`,
            method: 'POST',
            data: {
                reason: reason.trim(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to cancel commission'));
            }
        });
    }
}

function revertToPending(commissionId) {
    if (confirm('Revert this commission back to pending status?')) {
        $.ajax({
            url: `/admin/commissions/${commissionId}/revert-pending`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert('Error: ' + (response ? response.message : 'Failed to revert commission status'));
            }
        });
    }
}

function viewDetails(commissionId) {
    $.ajax({
        url: `/admin/commissions/${commissionId}/details`,
        method: 'GET',
        success: function(response) {
            $('#commissionDetailsContent').html(response);
            $('#commissionDetailsModal').modal('show');
        },
        error: function() {
            alert('Failed to load commission details');
        }
    });
}

// Bulk operations
function bulkMarkAsPaid() {
    const selectedCommissions = $('.commission-checkbox:checked').length;
    if (selectedCommissions === 0) {
        alert('Please select at least one commission to mark as paid');
        return;
    }
    
    $('#bulkPaymentModal').modal('show');
}

function processBulkPayment() {
    const selectedIds = $('.commission-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    const notes = $('#bulkPaymentNotes').val();
    
    $.ajax({
        url: '{{ route("admin.commissions.bulk-mark-paid") }}',
        method: 'POST',
        data: {
            commission_ids: selectedIds,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#bulkPaymentModal').modal('hide');
            alert(`Successfully marked ${response.updated_count} commission(s) as paid`);
            location.reload();
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Error: ' + (response ? response.message : 'Failed to process bulk payment'));
        }
    });
}

function exportCommissions() {
    const form = document.getElementById('filterForm');
    const exportInput = document.createElement('input');
    exportInput.type = 'hidden';
    exportInput.name = 'export';
    exportInput.value = '1';
    form.appendChild(exportInput);
    form.submit();
    form.removeChild(exportInput);
}
</script>
@endpush
