@extends('super-admin.layouts.app')

@section('title', 'Billing Management')
@section('page-title', 'Billing Management')

@section('content')
<div class="row">
    <!-- Quick Stats -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Total Revenue</div>
                                    <div class="h4 mb-0">${{ number_format($billings->where('status', 'paid')->sum('amount'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Paid This Month</div>
                                    <div class="h4 mb-0">${{ number_format($billings->where('status', 'paid')->filter(function($billing) { return $billing->paid_at && $billing->paid_at->isCurrentMonth(); })->sum('amount'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Pending Amount</div>
                                    <div class="h4 mb-0">${{ number_format($billings->where('status', 'pending')->sum('amount'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card stat-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Overdue Amount</div>
                                    <div class="h4 mb-0">${{ number_format($billings->filter(function($billing) { return $billing->isOverdue(); })->sum('amount'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Billing Records -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>All Billing Records
                </h5>
                <div>
                    <a href="{{ route('super-admin.billing.reports') }}" class="btn btn-outline-info btn-sm me-2">
                        <i class="fas fa-chart-bar me-2"></i>Reports
                    </a>
                    <a href="{{ route('super-admin.billing.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Create Billing
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Bar -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            @foreach(App\Models\SuperAdmin\Billing::STATUSES as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="billingCycleFilter">
                            <option value="">All Cycles</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="month" class="form-control" id="monthFilter" placeholder="Filter by month">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search companies...">
                    </div>
                </div>

                @if($billings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Company</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Billing Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($billings as $billing)
                                <tr class="billing-row" 
                                    data-status="{{ $billing->status }}"
                                    data-billing-cycle="{{ $billing->billing_cycle }}"
                                    data-month="{{ $billing->billing_date->format('Y-m') }}"
                                    data-company="{{ strtolower($billing->company->name ?? '') }}">
                                    <td>
                                        <strong>{{ $billing->invoice_number }}</strong>
                                        @if($billing->transaction_id)
                                            <br><small class="text-muted">{{ $billing->transaction_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($billing->company->logo)
                                                <img src="{{ asset('storage/' . $billing->company->logo) }}" 
                                                     class="rounded me-2" width="32" height="32" 
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ strtoupper(substr($billing->company->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $billing->company->name }}</strong>
                                                <br><small class="text-muted">{{ $billing->company->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $billing->package->name }}</span>
                                        <br><small class="text-muted">{{ ucfirst($billing->billing_cycle) }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $billing->formatted_amount }}</strong>
                                        @if($billing->status === 'paid' && $billing->paid_at)
                                            <br><small class="text-success">Paid {{ $billing->paid_at->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $billing->billing_date->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $billing->billing_date->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        {{ $billing->due_date->format('M d, Y') }}
                                        @if($billing->isOverdue())
                                            <br><small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Overdue
                                            </small>
                                        @elseif($billing->status === 'pending')
                                            <br><small class="text-muted">{{ $billing->due_date->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $billing->status === 'paid' ? 'bg-success' : ($billing->status === 'pending' ? 'bg-warning text-dark' : ($billing->isOverdue() ? 'bg-danger' : 'bg-secondary')) }}">
                                            {{ $billing->status_name }}
                                        </span>
                                        @if($billing->status === 'pending' && !$billing->isOverdue())
                                            <br><button type="button" class="btn btn-xs btn-outline-success mt-1 mark-paid-btn" 
                                                       data-billing-id="{{ $billing->id }}">
                                                <i class="fas fa-check"></i> Mark Paid
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if($billing->payment_method)
                                            <span class="badge bg-light text-dark">{{ $billing->payment_method }}</span>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td class="actions-column">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.billing.show', $billing) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.billing.invoice', $billing) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Invoice" target="_blank">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            @if($billing->status !== 'paid')
                                                <a href="{{ route('super-admin.billing.edit', $billing) }}" 
                                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('super-admin.billing.show', $billing) }}">
                                                            <i class="fas fa-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('super-admin.billing.invoice', $billing) }}" target="_blank">
                                                            <i class="fas fa-file-invoice me-2"></i>View Invoice
                                                        </a>
                                                    </li>
                                                    @if($billing->status !== 'paid')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('super-admin.billing.edit', $billing) }}">
                                                                <i class="fas fa-edit me-2"></i>Edit Record
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger delete-btn" 
                                                                    data-billing-id="{{ $billing->id }}"
                                                                    data-invoice-number="{{ $billing->invoice_number }}">
                                                                <i class="fas fa-trash me-2"></i>Delete Record
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $billings->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Billing Records Found</h5>
                        <p class="text-muted">Start by creating your first billing record.</p>
                        <a href="{{ route('super-admin.billing.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Billing Record
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete billing record <strong id="invoiceNumber"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. Only unpaid records can be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    function filterBillings() {
        const status = $('#statusFilter').val().toLowerCase();
        const billingCycle = $('#billingCycleFilter').val().toLowerCase();
        const month = $('#monthFilter').val();
        const search = $('#searchInput').val().toLowerCase();
        
        $('.billing-row').each(function() {
            const $row = $(this);
            const rowStatus = $row.data('status').toLowerCase();
            const rowCycle = $row.data('billing-cycle').toLowerCase();
            const rowMonth = $row.data('month');
            const rowCompany = $row.data('company').toLowerCase();
            
            let show = true;
            
            if (status && rowStatus !== status) show = false;
            if (billingCycle && rowCycle !== billingCycle) show = false;
            if (month && rowMonth !== month) show = false;
            if (search && !rowCompany.includes(search)) show = false;
            
            if (show) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }
    
    // Filter event listeners
    $('#statusFilter, #billingCycleFilter, #monthFilter').on('change', filterBillings);
    $('#searchInput').on('input', filterBillings);
    
    // Mark as paid
    $('.mark-paid-btn').on('click', function() {
        const billingId = $(this).data('billing-id');
        const $btn = $(this);
        
        if (confirm('Mark this billing record as paid?')) {
            $.ajax({
                url: `/super-admin/billing/${billingId}/mark-paid`,
                method: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function() {
                    alert('Error marking billing as paid. Please try again.');
                }
            });
        }
    });
    
    // Delete billing record
    $('.delete-btn').on('click', function() {
        const billingId = $(this).data('billing-id');
        const invoiceNumber = $(this).data('invoice-number');
        
        $('#invoiceNumber').text(invoiceNumber);
        $('#deleteForm').attr('action', `/super-admin/billing/${billingId}`);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
