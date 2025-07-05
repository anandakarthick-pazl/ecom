@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
                    <div>
                        <button class="btn btn-outline-primary btn-sm" id="markAllReadBtn">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                        <button class="btn btn-outline-danger btn-sm" id="clearAllBtn">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="15%">Type</th>
                                        <th width="25%">Title</th>
                                        <th width="35%">Message</th>
                                        <th width="15%">Date</th>
                                        <th width="5%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr class="{{ !$notification->is_read ? 'table-warning' : '' }}">
                                            <td class="text-center">
                                                <i class="{{ $notification->icon }} text-{{ $notification->color }}"></i>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $notification->color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $notification->title }}</strong>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-danger ms-2">New</span>
                                                @endif
                                            </td>
                                            <td>{{ $notification->message }}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $notification->created_at->format('M d, Y H:i A') }}<br>
                                                    <em>{{ $notification->created_at->diffForHumans() }}</em>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(!$notification->is_read)
                                                        <button class="btn btn-outline-primary mark-read-btn" 
                                                                data-id="{{ $notification->id }}"
                                                                title="Mark as Read">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-outline-danger delete-btn" 
                                                            data-id="{{ $notification->id }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Enhanced Pagination -->
                        <div class="pagination-container">
                            <div class="pagination-info">
                                <p class="small text-muted mb-0">
                                    Showing <span class="fw-semibold text-primary">{{ $notifications->firstItem() }}</span>
                                    to <span class="fw-semibold text-primary">{{ $notifications->lastItem() }}</span>
                                    of <span class="fw-semibold text-primary">{{ $notifications->total() }}</span>
                                    notifications
                                </p>
                            </div>
                            <div class="pagination-nav">
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications found</h5>
                            <p class="text-muted">You'll see notifications here when orders are placed or system events occur.</p>
                        </div>
                    @endif
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
                Are you sure you want to delete this notification?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Clear All Confirmation Modal -->
<div class="modal fade" id="clearAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear All Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete all notifications? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmClearAll">Clear All</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let notificationToDelete = null;
    
    // Mark as read
    $('.mark-read-btn').click(function() {
        const notificationId = $(this).data('id');
        const row = $(this).closest('tr');
        
        $.post(`/admin/notifications/${notificationId}/mark-read`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function() {
            row.removeClass('table-warning');
            $(this).remove();
            toastr.success('Notification marked as read');
        })
        .fail(function() {
            toastr.error('Failed to mark notification as read');
        });
    });
    
    // Delete single notification
    $('.delete-btn').click(function() {
        notificationToDelete = $(this).data('id');
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').click(function() {
        if (notificationToDelete) {
            $.ajax({
                url: `/admin/notifications/${notificationToDelete}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                }
            })
            .done(function() {
                location.reload();
            })
            .fail(function() {
                toastr.error('Failed to delete notification');
            });
        }
        $('#deleteModal').modal('hide');
    });
    
    // Mark all as read
    $('#markAllReadBtn').click(function() {
        $.post('/admin/notifications/mark-all-read', {
            _token: '{{ csrf_token() }}'
        })
        .done(function() {
            location.reload();
        })
        .fail(function() {
            toastr.error('Failed to mark all notifications as read');
        });
    });
    
    // Clear all notifications
    $('#clearAllBtn').click(function() {
        $('#clearAllModal').modal('show');
    });
    
    $('#confirmClearAll').click(function() {
        // This would require a new route and method
        toastr.info('Clear all functionality can be implemented if needed');
        $('#clearAllModal').modal('hide');
    });
});
</script>
@endpush
