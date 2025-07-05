@extends('admin.layouts.app')

@section('title', 'Branch Details')
@section('page_title', $branch->name . ' - Branch Details')

@section('page_actions')
    <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit Branch
    </a>
    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Branches
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Branch Header Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $branch->name }}</h4>
                            <p class="text-muted mb-1">
                                <span class="badge bg-info me-2">{{ $branch->code }}</span>
                                <span class="badge bg-{{ $branch->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($branch->status) }}
                                </span>
                            </p>
                            @if($branch->description)
                                <p class="text-muted mb-0">{{ $branch->description }}</p>
                            @endif
                        </div>
                        <div class="text-end">
                            <button class="btn btn-outline-primary btn-sm toggle-status" 
                                    data-branch-id="{{ $branch->id }}"
                                    data-current-status="{{ $branch->status }}">
                                <i class="fas fa-{{ $branch->status === 'active' ? 'pause' : 'play' }}"></i>
                                {{ $branch->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Created On</h6>
                    <p class="mb-1"><strong>{{ $branch->created_at->format('M d, Y') }}</strong></p>
                    <small class="text-muted">{{ $branch->created_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                        <p class="mb-0">Total Orders</p>
                    </div>
                    <div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_customers'] }}</h3>
                        <p class="mb-0">Customers</p>
                    </div>
                    <div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                        <p class="mb-0">Products</p>
                    </div>
                    <div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                        <p class="mb-0">Staff</p>
                    </div>
                    <div>
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-calendar-month"></i> This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-primary mb-1">{{ $stats['monthly_orders'] }}</h4>
                                <p class="text-muted mb-0">Orders</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success mb-1">₹{{ number_format($stats['monthly_revenue'], 2) }}</h4>
                                <p class="text-muted mb-0">Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> All Time
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-success mb-1">₹{{ number_format($stats['total_revenue'], 2) }}</h4>
                        <p class="text-muted mb-0">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="row">
        <!-- Contact & Address Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-address-card"></i> Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 30%;">Email:</td>
                            <td>
                                @if($branch->email)
                                    <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td>
                                @if($branch->phone)
                                    <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Address:</td>
                            <td>
                                @if($branch->full_address)
                                    {{ $branch->full_address }}
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        @if($branch->hasLocation())
                            <tr>
                                <td class="text-muted">Coordinates:</td>
                                <td>
                                    {{ $branch->latitude }}, {{ $branch->longitude }}
                                    <a href="https://maps.google.com/?q={{ $branch->latitude }},{{ $branch->longitude }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-map-marker-alt"></i> View on Map
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Manager Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-tie"></i> Manager Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($branch->manager_name)
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 30%;">Name:</td>
                                <td><strong>{{ $branch->manager_name }}</strong></td>
                            </tr>
                            @if($branch->manager_email)
                                <tr>
                                    <td class="text-muted">Email:</td>
                                    <td><a href="mailto:{{ $branch->manager_email }}">{{ $branch->manager_email }}</a></td>
                                </tr>
                            @endif
                            @if($branch->manager_phone)
                                <tr>
                                    <td class="text-muted">Phone:</td>
                                    <td><a href="tel:{{ $branch->manager_phone }}">{{ $branch->manager_phone }}</a></td>
                                </tr>
                            @endif
                        </table>
                    @else
                        <p class="text-muted mb-0">No manager assigned to this branch.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Staff and Recent Activities -->
        <div class="col-md-6">
            <!-- Assigned Staff -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-users"></i> Assigned Staff ({{ $branch->users->count() }})
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignUsersModal">
                        <i class="fas fa-plus"></i> Assign Users
                    </button>
                </div>
                <div class="card-body">
                    @if($branch->users->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($branch->users as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="rounded-circle me-2" 
                                                 width="32" 
                                                 height="32">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }} • {{ ucfirst($user->role) }}</small>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.branches.remove-user', [$branch, $user]) }}" 
                                          method="POST" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Remove {{ $user->name }} from this branch?')"
                                                title="Remove from branch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No staff assigned to this branch.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Recent Orders
                    </h6>
                </div>
                <div class="card-body">
                    @if($branch->orders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($branch->orders->take(5) as $order)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="fw-bold">#{{ $order->order_number }}</div>
                                        <small class="text-muted">
                                            {{ $order->customer_name }} • {{ $order->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success">₹{{ number_format($order->total, 2) }}</div>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }} badge-sm">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($branch->orders->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.orders.index', ['branch' => $branch->id]) }}" class="btn btn-sm btn-outline-primary">
                                    View All Orders
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">No orders found for this branch.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Users Modal -->
<div class="modal fade" id="assignUsersModal" tabindex="-1" aria-labelledby="assignUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignUsersModalLabel">Assign Users to Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.branches.assign-users', $branch) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Select users to assign to this branch:</p>
                    <div id="availableUsers">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading available users...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Selected Users</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle status functionality
    $('.toggle-status').click(function() {
        const button = $(this);
        const branchId = button.data('branch-id');
        const currentStatus = button.data('current-status');
        
        button.prop('disabled', true);
        
        $.ajax({
            url: `/admin/branches/${branchId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Update button
                    const newStatus = response.status;
                    button.data('current-status', newStatus);
                    
                    if (newStatus === 'active') {
                        button.html('<i class="fas fa-pause"></i> Deactivate');
                        button.removeClass('btn-outline-success').addClass('btn-outline-warning');
                    } else {
                        button.html('<i class="fas fa-play"></i> Activate');
                        button.removeClass('btn-outline-warning').addClass('btn-outline-success');
                    }
                    
                    // Update status badge
                    location.reload();
                } else {
                    showAlert('error', 'Failed to update branch status');
                }
            },
            error: function() {
                showAlert('error', 'Failed to update branch status');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
    
    // Load available users when modal is shown
    $('#assignUsersModal').on('show.bs.modal', function() {
        loadAvailableUsers();
    });
    
    function loadAvailableUsers() {
        $.get(`/admin/branches/{{ $branch->id }}/available-users`, function(users) {
            let html = '';
            
            if (users.length === 0) {
                html = '<p class="text-muted">No unassigned users found.</p>';
            } else {
                users.forEach(function(user) {
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="user_ids[]" value="${user.id}" id="user_${user.id}">
                            <label class="form-check-label" for="user_${user.id}">
                                <strong>${user.name}</strong> (${user.email})
                                <span class="badge bg-secondary ms-2">${user.role}</span>
                            </label>
                        </div>
                    `;
                });
            }
            
            $('#availableUsers').html(html);
        }).fail(function() {
            $('#availableUsers').html('<p class="text-danger">Failed to load available users.</p>');
        });
    }
    
    // Function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush
