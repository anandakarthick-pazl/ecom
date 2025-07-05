@extends('super-admin.layouts.app')

@section('title', 'Billing Record Details')
@section('page-title', 'Billing Record Details')

@section('content')
<div class="row">
    <!-- Billing Overview -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>{{ $billing->invoice_number }}
                    <span class="badge {{ $billing->status === 'paid' ? 'bg-success' : ($billing->status === 'pending' ? 'bg-warning text-dark' : ($billing->isOverdue() ? 'bg-danger' : 'bg-secondary')) }} ms-2">
                        {{ $billing->status_name }}
                    </span>
                </h5>
                <div>
                    <a href="{{ route('super-admin.billing.invoice', $billing) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-file-invoice me-2"></i>Invoice
                    </a>
                    @if($billing->status !== 'paid')
                        <a href="{{ route('super-admin.billing.edit', $billing) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    @endif
                    <a href="{{ route('super-admin.billing.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3">Billing Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Invoice Number:</td>
                                <td>{{ $billing->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Amount:</td>
                                <td>
                                    <h5 class="text-primary mb-0">{{ $billing->formatted_amount }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Billing Cycle:</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($billing->billing_cycle) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Payment Method:</td>
                                <td>
                                    @if($billing->payment_method)
                                        <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $billing->payment_method)) }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                            </tr>
                            @if($billing->transaction_id)
                                <tr>
                                    <td class="fw-bold text-muted">Transaction ID:</td>
                                    <td>
                                        <code>{{ $billing->transaction_id }}</code>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted mb-3">Important Dates</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Billing Date:</td>
                                <td>{{ $billing->billing_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Due Date:</td>
                                <td>
                                    {{ $billing->due_date->format('M d, Y') }}
                                    @if($billing->isOverdue() && $billing->status === 'pending')
                                        <br><small class="text-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Overdue by {{ $billing->due_date->diffForHumans(null, true) }}
                                        </small>
                                    @elseif($billing->status === 'pending')
                                        <br><small class="text-muted">{{ $billing->due_date->diffForHumans() }}</small>
                                    @endif
                                </td>
                            </tr>
                            @if($billing->paid_at)
                                <tr>
                                    <td class="fw-bold text-muted">Paid Date:</td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>
                                            {{ $billing->paid_at->format('M d, Y g:i A') }}
                                        </span>
                                        <br><small class="text-muted">{{ $billing->paid_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-bold text-muted">Created:</td>
                                <td>{{ $billing->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Last Updated:</td>
                                <td>{{ $billing->updated_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($billing->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-muted">Notes</h6>
                            <div class="alert alert-light">
                                {{ $billing->notes }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Company Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Company Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-start">
                            @if($billing->company->logo)
                                <img src="{{ asset('storage/' . $billing->company->logo) }}" 
                                     class="rounded me-3" width="64" height="64" 
                                     style="object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" 
                                     style="width: 64px; height: 64px; font-size: 24px;">
                                    {{ strtoupper(substr($billing->company->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-1">{{ $billing->company->name }}</h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-globe me-1"></i>
                                    <a href="http://{{ $billing->company->domain }}" target="_blank" class="text-decoration-none">
                                        {{ $billing->company->domain }}
                                    </a>
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-envelope me-1"></i>
                                    <a href="mailto:{{ $billing->company->email }}" class="text-decoration-none">
                                        {{ $billing->company->email }}
                                    </a>
                                </p>
                                @if($billing->company->phone)
                                    <p class="text-muted mb-1">
                                        <i class="fas fa-phone me-1"></i>{{ $billing->company->phone }}
                                    </p>
                                @endif
                                <span class="badge {{ $billing->company->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($billing->company->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('super-admin.companies.show', $billing->company) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-building me-2"></i>View Company Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Package Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>Package Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-2">{{ $billing->package->name }}</h5>
                        <p class="text-muted mb-3">{{ $billing->package->description }}</p>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <small class="text-muted">Package Price:</small>
                                <div class="fw-bold">${{ number_format($billing->package->price, 2) }}/{{ $billing->package->billing_cycle }}</div>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted">Trial Period:</small>
                                <div class="fw-bold">
                                    @if($billing->package->trial_days > 0)
                                        {{ $billing->package->trial_days }} days
                                    @else
                                        No trial
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        @if($billing->package->features && count($billing->package->features) > 0)
                            <div class="mt-3">
                                <small class="text-muted">Package Features:</small>
                                <div class="mt-1">
                                    @foreach(array_slice($billing->package->features, 0, 3) as $feature)
                                        <span class="badge bg-light text-dark me-1">{{ $feature }}</span>
                                    @endforeach
                                    @if(count($billing->package->features) > 3)
                                        <span class="badge bg-light text-dark">+{{ count($billing->package->features) - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('super-admin.packages.show', $billing->package) }}" 
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-box me-2"></i>View Package Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($billing->status === 'pending')
                        <button type="button" class="btn btn-success btn-sm mark-paid-btn" 
                                data-billing-id="{{ $billing->id }}">
                            <i class="fas fa-check me-2"></i>Mark as Paid
                        </button>
                    @endif
                    
                    <a href="{{ route('super-admin.billing.invoice', $billing) }}" 
                       class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-file-invoice me-2"></i>View/Download Invoice
                    </a>
                    
                    @if($billing->status !== 'paid')
                        <a href="{{ route('super-admin.billing.edit', $billing) }}" 
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Record
                        </a>
                    @endif
                    
                    <a href="{{ route('super-admin.companies.show', $billing->company) }}" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-building me-2"></i>View Company
                    </a>
                    
                    <a href="{{ route('super-admin.packages.show', $billing->package) }}" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-box me-2"></i>View Package
                    </a>
                    
                    @if($billing->status !== 'paid')
                        <hr>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Record
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Payment Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Payment Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Invoice Created</h6>
                            <p class="timeline-text">{{ $billing->created_at->format('M d, Y g:i A') }}</p>
                            <small class="text-muted">{{ $billing->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $billing->billing_date->isPast() ? 'bg-info' : 'bg-secondary' }}"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Billing Period Started</h6>
                            <p class="timeline-text">{{ $billing->billing_date->format('M d, Y') }}</p>
                            <small class="text-muted">{{ $billing->billing_date->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    @if($billing->status === 'paid' && $billing->paid_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Payment Received</h6>
                                <p class="timeline-text">{{ $billing->paid_at->format('M d, Y g:i A') }}</p>
                                <small class="text-muted">{{ $billing->paid_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @else
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $billing->isOverdue() ? 'bg-danger' : 'bg-warning' }}"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">
                                    @if($billing->isOverdue())
                                        Payment Overdue
                                    @else
                                        Payment Due
                                    @endif
                                </h6>
                                <p class="timeline-text">{{ $billing->due_date->format('M d, Y') }}</p>
                                <small class="text-muted">{{ $billing->due_date->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Related Records -->
        @php
            $relatedBillings = App\Models\SuperAdmin\Billing::where('company_id', $billing->company_id)
                                ->where('id', '!=', $billing->id)
                                ->latest()
                                ->take(5)
                                ->get();
        @endphp
        
        @if($relatedBillings->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Other Billing Records</h6>
                </div>
                <div class="card-body">
                    @foreach($relatedBillings as $related)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <strong>{{ $related->invoice_number }}</strong>
                                <br><small class="text-muted">{{ $related->billing_date->format('M d, Y') }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $related->formatted_amount }}</div>
                                <span class="badge {{ $related->status === 'paid' ? 'bg-success' : ($related->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ $related->status_name }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('super-admin.billing.index', ['company_id' => $billing->company_id]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            View All Records
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Billing Statistics -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Billing Statistics</h6>
            </div>
            <div class="card-body">
                @php
                    $companyBillings = App\Models\SuperAdmin\Billing::where('company_id', $billing->company_id);
                    $totalPaid = $companyBillings->clone()->where('status', 'paid')->sum('amount');
                    $totalPending = $companyBillings->clone()->where('status', 'pending')->sum('amount');
                    $totalRecords = $companyBillings->clone()->count();
                @endphp
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h5 class="mb-1 text-success">${{ number_format($totalPaid, 2) }}</h5>
                            <small class="text-muted">Total Paid</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h5 class="mb-1 text-warning">${{ number_format($totalPending, 2) }}</h5>
                        <small class="text-muted">Total Pending</small>
                    </div>
                    <div class="col-12">
                        <h5 class="mb-1 text-primary">{{ $totalRecords }}</h5>
                        <small class="text-muted">Total Records</small>
                    </div>
                </div>
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
                <p>Are you sure you want to delete billing record <strong>{{ $billing->invoice_number }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('super-admin.billing.destroy', $billing) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 0.85rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Mark as paid
    $('.mark-paid-btn').on('click', function() {
        const billingId = $(this).data('billing-id');
        
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
});
</script>
@endpush
