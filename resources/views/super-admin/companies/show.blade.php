@extends('super-admin.layouts.app')

@section('title', 'Company Details')
@section('page-title', 'Company Details')

@section('content')
<div class="row">
    <!-- Company Overview -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>{{ $company->name }}
                </h5>
                <div>
                    <a href="{{ route('super-admin.companies.edit', $company) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Company Name:</td>
                                <td>{{ $company->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Domain:</td>
                                <td>
                                    <a href="http://{{ $company->domain }}" target="_blank" class="text-decoration-none">
                                        {{ $company->domain }}
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Email:</td>
                                <td>
                                    <a href="mailto:{{ $company->email }}" class="text-decoration-none">
                                        {{ $company->email }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Phone:</td>
                                <td>{{ $company->phone ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status:</td>
                                <td>
                                    <span class="badge {{ $company->status === 'active' ? 'bg-success' : ($company->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Address:</td>
                                <td>{{ $company->address ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">City:</td>
                                <td>{{ $company->city ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">State:</td>
                                <td>{{ $company->state ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Country:</td>
                                <td>{{ $company->country ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Postal Code:</td>
                                <td>{{ $company->postal_code ?: 'Not provided' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>Company Users ({{ $company->users->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($company->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff" 
                                                 class="rounded-circle me-2" width="32" height="32">
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            <span title="{{ $user->last_login_at->format('M d, Y g:i A') }}">
                                                {{ $user->last_login_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No users found for this company.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Support Tickets -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-headset me-2"></i>Recent Support Tickets ({{ $company->supportTickets->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($company->supportTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->supportTickets->take(5) as $ticket)
                                <tr>
                                    <td>#{{ $ticket->id }}</td>
                                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td>
                                        <span class="badge {{ $ticket->priority === 'high' ? 'bg-danger' : ($ticket->priority === 'medium' ? 'bg-warning' : 'bg-info') }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->status === 'closed' ? 'bg-success' : ($ticket->status === 'open' ? 'bg-primary' : 'bg-warning') }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.support.show', $ticket) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($company->supportTickets->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('super-admin.support.index', ['company' => $company->id]) }}" 
                               class="btn btn-outline-primary btn-sm">
                                View All Tickets
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-headset fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No support tickets found.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Billing History -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Recent Billing History ({{ $company->billings->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($company->billings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Paid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->billings->take(5) as $billing)
                                <tr>
                                    <td>#{{ $billing->invoice_number }}</td>
                                    <td>${{ number_format($billing->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $billing->status === 'paid' ? 'bg-success' : ($billing->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $billing->due_date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($billing->paid_at)
                                            <small class="text-muted">
                                                {{ $billing->paid_at->format('M d, Y') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($company->billings->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('super-admin.billing.index', ['company' => $company->id]) }}" 
                               class="btn btn-outline-primary btn-sm">
                                View All Billing Records
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-credit-card fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No billing records found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Logo & Basic Info -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" 
                         class="img-thumbnail mb-3" style="max-width: 150px; max-height: 150px;">
                @else
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                @endif
                <h5>{{ $company->name }}</h5>
                <p class="text-muted mb-1">{{ $company->domain }}</p>
                <span class="badge {{ $company->status === 'active' ? 'bg-success' : ($company->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }} mb-3">
                    {{ ucfirst($company->status) }}
                </span>
            </div>
        </div>
        
        <!-- Configuration -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Configuration</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Theme:</label>
                    @if($company->theme)
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info me-2">{{ $company->theme->name }}</span>
                            @if($company->theme->status === 'active')
                                <i class="fas fa-check-circle text-success" title="Active"></i>
                            @else
                                <i class="fas fa-exclamation-triangle text-warning" title="Inactive"></i>
                            @endif
                        </div>
                    @else
                        <span class="text-muted">No theme assigned</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">Package:</label>
                    @if($company->package)
                        <div>
                            <span class="badge bg-success">{{ $company->package->name }}</span>
                            <br><small class="text-muted">${{ $company->package->price }}/{{ $company->package->billing_cycle }}</small>
                        </div>
                    @else
                        <span class="text-muted">No package assigned</span>
                    @endif
                </div>
                
                @if($company->trial_ends_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Trial Period:</label>
                        <div>
                            <span class="badge {{ $company->trial_ends_at->isPast() ? 'bg-danger' : 'bg-warning' }}">
                                {{ $company->trial_ends_at->isPast() ? 'Expired' : 'Active' }}
                            </span>
                            <br><small class="text-muted">
                                Ends: {{ $company->trial_ends_at->format('M d, Y') }}
                                <br>{{ $company->trial_ends_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="mb-1 text-primary">{{ $company->users->count() }}</h4>
                            <small class="text-muted">Total Users</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="mb-1 text-success">{{ $company->users->where('status', 'active')->count() }}</h4>
                        <small class="text-muted">Active Users</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-1 text-warning">{{ $company->supportTickets->where('status', 'open')->count() }}</h4>
                            <small class="text-muted">Open Tickets</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-1 text-info">{{ $company->billings->where('status', 'pending')->count() }}</h4>
                        <small class="text-muted">Pending Bills</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Company Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Company Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Company Created</h6>
                            <p class="timeline-text">{{ $company->created_at->format('M d, Y g:i A') }}</p>
                            <small class="text-muted">{{ $company->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    @if($company->users->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">First User Added</h6>
                                <p class="timeline-text">{{ $company->users->first()->created_at->format('M d, Y g:i A') }}</p>
                                <small class="text-muted">{{ $company->users->first()->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($company->users->whereNotNull('last_login_at')->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Last User Activity</h6>
                                <p class="timeline-text">{{ $company->users->whereNotNull('last_login_at')->sortByDesc('last_login_at')->first()->last_login_at->format('M d, Y g:i A') }}</p>
                                <small class="text-muted">{{ $company->users->whereNotNull('last_login_at')->sortByDesc('last_login_at')->first()->last_login_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.companies.edit', $company) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Company
                    </a>
                    
                    @if($company->trial_ends_at && !$company->trial_ends_at->isPast())
                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#extendTrialModal">
                            <i class="fas fa-clock me-2"></i>Extend Trial
                        </button>
                    @endif
                    
                    <a href="mailto:{{ $company->email }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-envelope me-2"></i>Send Email
                    </a>
                    
                    <a href="{{ route('super-admin.support.create', ['company_id' => $company->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-2"></i>Create Ticket
                    </a>
                    
                    <hr>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Company
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Trial Modal -->
<div class="modal fade" id="extendTrialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Trial Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.companies.extend-trial', $company) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="days" class="form-label">Extend trial by (days):</label>
                        <input type="number" class="form-control" id="days" name="days" 
                               min="1" max="365" value="30" required>
                        <small class="form-text text-muted">Enter number of days to extend the trial period.</small>
                    </div>
                    
                    @if($company->trial_ends_at)
                        <div class="alert alert-info">
                            <strong>Current trial end date:</strong> {{ $company->trial_ends_at->format('M d, Y g:i A') }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Extend Trial</button>
                </div>
            </form>
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
                <p>Are you sure you want to delete <strong>{{ $company->name }}</strong>?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. This will permanently delete:
                    <ul class="mb-0 mt-2">
                        <li>All company data and configurations</li>
                        <li>{{ $company->users->count() }} associated user(s)</li>
                        <li>{{ $company->supportTickets->count() }} support ticket(s)</li>
                        <li>{{ $company->billings->count() }} billing record(s)</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_name" class="form-label">Type the company name to confirm:</label>
                    <input type="text" class="form-control" id="confirm_name" placeholder="{{ $company->name }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('super-admin.companies.destroy', $company) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>Delete Company</button>
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
    // Enable delete button only when company name is typed correctly
    $('#confirm_name').on('input', function() {
        const typedName = $(this).val();
        const companyName = '{{ $company->name }}';
        const deleteBtn = $('#confirmDeleteBtn');
        
        if (typedName === companyName) {
            deleteBtn.prop('disabled', false);
        } else {
            deleteBtn.prop('disabled', true);
        }
    });
    
    // Reset confirmation input when modal is closed
    $('#deleteModal').on('hidden.bs.modal', function() {
        $('#confirm_name').val('');
        $('#confirmDeleteBtn').prop('disabled', true);
    });
});
</script>
@endpush
