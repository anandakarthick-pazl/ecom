@extends('super-admin.layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

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
                                <i class="fas fa-ticket-alt fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Total Tickets</div>
                                    <div class="h4 mb-0">{{ $tickets->total() }}</div>
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
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Open Tickets</div>
                                    <div class="h4 mb-0">{{ $tickets->where('status', 'open')->count() + $tickets->where('status', 'in_progress')->count() }}</div>
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
                                <i class="fas fa-fire fa-2x text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-white">
                                    <div class="small">Urgent Tickets</div>
                                    <div class="h4 mb-0">{{ $tickets->where('priority', 'urgent')->count() }}</div>
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
                                    <div class="small">Resolved Today</div>
                                    <div class="h4 mb-0">{{ $tickets->where('status', 'resolved')->filter(function($ticket) { return $ticket->resolved_at && $ticket->resolved_at->isToday(); })->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Support Tickets -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-headset me-2"></i>All Support Tickets
                </h5>
                <a href="{{ route('super-admin.support.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Ticket
                </a>
            </div>
            <div class="card-body">
                <!-- Filter Bar -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            @foreach(App\Models\SuperAdmin\SupportTicket::STATUSES as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="priorityFilter">
                            <option value="">All Priority</option>
                            @foreach(App\Models\SuperAdmin\SupportTicket::PRIORITIES as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach(App\Models\SuperAdmin\SupportTicket::CATEGORIES as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="assignedFilter">
                            <option value="">All Assignees</option>
                            <option value="unassigned">Unassigned</option>
                            @foreach($tickets->pluck('assignedTo')->filter()->unique('id') as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search tickets...">
                    </div>
                </div>

                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Title</th>
                                    <th>Company</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr class="ticket-row" 
                                    data-status="{{ $ticket->status }}"
                                    data-priority="{{ $ticket->priority }}"
                                    data-category="{{ $ticket->category }}"
                                    data-assigned="{{ $ticket->assigned_to ?? 'unassigned' }}"
                                    data-title="{{ strtolower($ticket->title) }}"
                                    data-company="{{ strtolower($ticket->company->name ?? '') }}">
                                    <td>
                                        <strong>#{{ $ticket->id }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.support.show', $ticket) }}" class="text-decoration-none">
                                            <strong>{{ Str::limit($ticket->title, 40) }}</strong>
                                        </a>
                                        <br><small class="text-muted">{{ Str::limit($ticket->description, 60) }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($ticket->company->logo)
                                                <img src="{{ asset('storage/' . $ticket->company->logo) }}" 
                                                     class="rounded me-2" width="24" height="24" 
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 24px; height: 24px; font-size: 10px;">
                                                    {{ strtoupper(substr($ticket->company->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $ticket->company->name }}</strong>
                                                <br><small class="text-muted">{{ $ticket->company->domain }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->priority === 'urgent' ? 'bg-danger' : ($ticket->priority === 'high' ? 'bg-warning text-dark' : ($ticket->priority === 'medium' ? 'bg-info' : 'bg-secondary')) }}">
                                            {{ $ticket->priority_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-ticket-id="{{ $ticket->id }}" 
                                                style="width: auto; min-width: 120px;">
                                            @foreach(App\Models\SuperAdmin\SupportTicket::STATUSES as $key => $name)
                                                <option value="{{ $key }}" {{ $ticket->status === $key ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $ticket->category_name }}</span>
                                    </td>
                                    <td>
                                        @if($ticket->assignedTo)
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->assignedTo->name) }}&background=667eea&color=fff" 
                                                     class="rounded-circle me-1" width="20" height="20">
                                                <small>{{ $ticket->assignedTo->name }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                            <br><button type="button" class="btn btn-xs btn-outline-primary assign-btn" 
                                                       data-ticket-id="{{ $ticket->id }}">
                                                <i class="fas fa-user-plus"></i> Assign
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ticket->created_at->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('super-admin.support.show', $ticket) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.support.edit', $ticket) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('super-admin.support.show', $ticket) }}">
                                                            <i class="fas fa-eye me-2"></i>View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('super-admin.companies.show', $ticket->company) }}">
                                                            <i class="fas fa-building me-2"></i>View Company
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger delete-btn" 
                                                                data-ticket-id="{{ $ticket->id }}"
                                                                data-ticket-title="{{ $ticket->title }}">
                                                            <i class="fas fa-trash me-2"></i>Delete Ticket
                                                        </button>
                                                    </li>
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
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-headset fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Support Tickets Found</h5>
                        <p class="text-muted">Start by creating your first support ticket.</p>
                        <a href="{{ route('super-admin.support.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Ticket
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign to Agent:</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select Agent</option>
                            @foreach(App\Models\User::superAdmins()->get() as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Ticket</button>
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
                <p>Are you sure you want to delete the ticket <strong id="ticketTitle"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All replies will also be deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Ticket</button>
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
    function filterTickets() {
        const status = $('#statusFilter').val().toLowerCase();
        const priority = $('#priorityFilter').val().toLowerCase();
        const category = $('#categoryFilter').val().toLowerCase();
        const assigned = $('#assignedFilter').val().toLowerCase();
        const search = $('#searchInput').val().toLowerCase();
        
        $('.ticket-row').each(function() {
            const $row = $(this);
            const rowStatus = $row.data('status').toLowerCase();
            const rowPriority = $row.data('priority').toLowerCase();
            const rowCategory = $row.data('category').toLowerCase();
            const rowAssigned = $row.data('assigned').toString().toLowerCase();
            const rowTitle = $row.data('title').toLowerCase();
            const rowCompany = $row.data('company').toLowerCase();
            
            let show = true;
            
            if (status && rowStatus !== status) show = false;
            if (priority && rowPriority !== priority) show = false;
            if (category && rowCategory !== category) show = false;
            if (assigned && rowAssigned !== assigned) show = false;
            if (search && !rowTitle.includes(search) && !rowCompany.includes(search)) show = false;
            
            if (show) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }
    
    // Filter event listeners
    $('#statusFilter, #priorityFilter, #categoryFilter, #assignedFilter').on('change', filterTickets);
    $('#searchInput').on('input', filterTickets);
    
    // Status change handler
    $('.status-select').on('change', function() {
        const ticketId = $(this).data('ticket-id');
        const status = $(this).val();
        
        $.ajax({
            url: `/super-admin/support/${ticketId}/status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>Ticket status updated successfully!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    $('.content-wrapper').prepend(alert);
                    
                    setTimeout(() => alert.fadeOut(), 3000);
                }
            },
            error: function() {
                alert('Error updating ticket status. Please try again.');
            }
        });
    });
    
    // Assign ticket
    let currentTicketId = null;
    $('.assign-btn').on('click', function() {
        currentTicketId = $(this).data('ticket-id');
        $('#assignModal').modal('show');
    });
    
    $('#assignForm').on('submit', function(e) {
        e.preventDefault();
        const assignedTo = $('#assigned_to').val();
        
        $.ajax({
            url: `/super-admin/support/${currentTicketId}/assign`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                assigned_to: assignedTo
            },
            success: function(response) {
                if (response.success) {
                    $('#assignModal').modal('hide');
                    location.reload();
                }
            },
            error: function() {
                alert('Error assigning ticket. Please try again.');
            }
        });
    });
    
    // Delete ticket
    $('.delete-btn').on('click', function() {
        const ticketId = $(this).data('ticket-id');
        const ticketTitle = $(this).data('ticket-title');
        
        $('#ticketTitle').text(ticketTitle);
        $('#deleteForm').attr('action', `/super-admin/support/${ticketId}`);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
