@extends('admin.layouts.app')

@section('title', 'Social Media Links')
@section('page_title', 'Social Media Links')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Social Media Links</h4>
        <p class="text-muted mb-0">Manage your social media links displayed on the website</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#quickAddModal">
            <i class="fas fa-plus-circle me-1"></i> Quick Add
        </button>
        <a href="{{ route('admin.social-media.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Custom Link
        </a>
    </div>
</div>

<!-- Debug Information (remove in production) -->
@if(config('app.debug'))
{{-- <div class="alert alert-info mb-4">
    <h6><i class="fas fa-info-circle"></i> Debug Information</h6>
    <small>
        <strong>Company ID:</strong> {{ session('selected_company_id', 'Not set') }}<br>
        <strong>Total Links:</strong> {{ $socialMediaLinks->total() ?? 0 }}<br>
        <strong>Predefined Platforms:</strong> {{ count($predefinedPlatforms ?? []) }}<br>
        <strong>User:</strong> {{ auth()->user()->email ?? 'Not authenticated' }}
    </small>
</div> --}}
@endif

@if($socialMediaLinks->count() > 0)
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="socialMediaTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="50"><i class="fas fa-arrows-alt text-muted"></i></th>
                            <th>Platform</th>
                            <th>URL</th>
                            <th width="100">Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-social-media">
                        @foreach($socialMediaLinks as $link)
                        <tr data-id="{{ $link->id }}" data-sort-order="{{ $link->sort_order }}">
                            <td class="text-center">
                                <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: move;"></i>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="social-icon me-3" style="background-color: {{ $link->brand_color }};">
                                        <i class="{{ $link->icon_class }} text-white"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $link->name }}</div>
                                        <small class="text-muted">{{ $link->icon_class }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ $link->formatted_url }}" target="_blank" class="text-primary text-decoration-none">
                                    {{ Str::limit($link->url, 50) }}
                                    <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox" 
                                           {{ $link->is_active ? 'checked' : '' }}
                                           data-id="{{ $link->id }}">
                                    <label class="form-check-label small">
                                        {{ $link->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.social-media.edit', $link) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger delete-btn" 
                                            data-id="{{ $link->id }}" 
                                            data-name="{{ $link->name }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($socialMediaLinks->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                {{ $socialMediaLinks->links() }}
                <div class="text-muted">
                    Showing {{ $socialMediaLinks->firstItem() ?? 0 }} to {{ $socialMediaLinks->lastItem() ?? 0 }} 
                    of {{ $socialMediaLinks->total() }} links
                </div>
            </div>
        </div>
        @endif
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-share-alt fa-3x text-muted mb-3"></i>
            <h5>No Social Media Links Found</h5>
            <p class="text-muted">Add your first social media link to get started.</p>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                <i class="fas fa-plus-circle me-1"></i> Quick Add Popular Platform
            </button>
            <a href="{{ route('admin.social-media.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Custom Link
            </a>
        </div>
    </div>
@endif

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-labelledby="quickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddModalLabel">Quick Add Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="platformSelect" class="form-label">Select Platform</label>
                        <select class="form-select" name="platform" id="platformSelect" required>
                            <option value="">Choose a platform...</option>
                            @if(isset($predefinedPlatforms) && is_array($predefinedPlatforms))
                                @foreach($predefinedPlatforms as $key => $platform)
                                <option value="{{ $key }}" 
                                        data-icon="{{ $platform['icon_class'] }}" 
                                        data-color="{{ $platform['color'] }}"
                                        data-placeholder="{{ $platform['placeholder'] }}">
                                    {{ $platform['name'] }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="urlInput" class="form-label">Profile URL</label>
                        <input type="url" class="form-control" name="url" id="urlInput" 
                               placeholder="Enter your profile URL..." required>
                        <div class="form-text">Enter the complete URL to your profile or page</div>
                    </div>
                    
                    <div id="platformPreview" class="d-none">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="social-icon me-3" id="previewIcon">
                                    <i class="text-white"></i>
                                </div>
                                <div>
                                    <strong id="previewName"></strong> link will be added
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error display -->
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-plus me-1"></i> Add Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the <strong id="deleteName"></strong> social media link?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

#sortable-social-media tr {
    transition: all 0.3s ease;
}

#sortable-social-media tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.drag-handle:hover {
    color: var(--bs-primary) !important;
}

.ui-sortable-helper {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: white !important;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Social Media page loaded');
    
    // Initialize sortable if we have items
    if (document.getElementById('sortable-social-media')) {
        const sortable = new Sortable(document.getElementById('sortable-social-media'), {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                updateSortOrder();
            }
        });
        console.log('Sortable initialized');
    }

    // Platform selection change
    $('#platformSelect').change(function() {
        console.log('Platform selected:', $(this).val());
        
        const option = $(this).find('option:selected');
        const placeholder = option.data('placeholder');
        const icon = option.data('icon');
        const color = option.data('color');
        const name = option.text();
        
        if (placeholder) {
            $('#urlInput').attr('placeholder', placeholder);
            $('#previewIcon').css('background-color', color);
            $('#previewIcon i').attr('class', icon + ' text-white');
            $('#previewName').text(name);
            $('#platformPreview').removeClass('d-none');
        } else {
            $('#platformPreview').addClass('d-none');
            $('#urlInput').attr('placeholder', 'Enter your profile URL...');
        }
    });

    // Quick add form submission
    $('#quickAddForm').submit(function(e) {
        e.preventDefault();
        console.log('Quick add form submitted');
        
        const $submitBtn = $('#submitBtn');
        const originalText = $submitBtn.html();
        
        // Show loading state
        $submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...').prop('disabled', true);
        
        // Hide previous errors
        $('#formErrors').addClass('d-none');
        
        const formData = $(this).serialize();
        console.log('Form data:', formData);
        
        $.ajax({
            url: '{{ route("admin.social-media.quick-add") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.success) {
                    $('#quickAddModal').modal('hide');
                    
                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('success', response.message || 'Social media link added successfully!');
                    } else {
                        alert(response.message || 'Social media link added successfully!');
                    }
                    
                    // Reload page to show new data
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showError(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                console.error('Error response:', xhr);
                
                let errorMessage = 'An error occurred. Please try again.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    }
                }
                
                showError(errorMessage);
            },
            complete: function() {
                // Reset button state
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Status toggle
    $('.status-toggle').change(function() {
        const $toggle = $(this);
        const id = $toggle.data('id');
        const isActive = $toggle.is(':checked');
        
        console.log('Status toggle:', id, isActive);
        
        $.ajax({
            url: `/admin/social-media/${id}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Toggle success:', response);
                
                if (response.success) {
                    const label = $toggle.siblings('.form-check-label');
                    label.text(isActive ? 'Active' : 'Inactive');
                    
                    if (typeof showToast === 'function') {
                        showToast('success', response.message || 'Status updated successfully!');
                    }
                }
            },
            error: function(xhr) {
                console.error('Toggle error:', xhr);
                
                // Revert toggle on error
                $toggle.prop('checked', !isActive);
                
                if (typeof showToast === 'function') {
                    showToast('error', 'Error updating status');
                } else {
                    alert('Error updating status');
                }
            }
        });
    });

    // Delete functionality
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        console.log('Delete clicked:', id, name);
        
        $('#deleteName').text(name);
        $('#deleteForm').attr('action', `/admin/social-media/${id}`);
        $('#deleteModal').modal('show');
    });

    // Update sort order
    function updateSortOrder() {
        const items = [];
        $('#sortable-social-media tr').each(function(index) {
            items.push({
                id: $(this).data('id'),
                sort_order: index + 1
            });
        });

        console.log('Updating sort order:', items);

        $.ajax({
            url: '{{ route("admin.social-media.update-sort-order") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items
            },
            success: function(response) {
                console.log('Sort order updated:', response);
                
                if (response.success) {
                    // Visual feedback
                    $('#sortable-social-media').addClass('table-success');
                    setTimeout(() => {
                        $('#sortable-social-media').removeClass('table-success');
                    }, 500);
                    
                    if (typeof showToast === 'function') {
                        showToast('success', 'Order updated successfully!');
                    }
                }
            },
            error: function(xhr) {
                console.error('Sort order error:', xhr);
                
                if (typeof showToast === 'function') {
                    showToast('error', 'Error updating sort order');
                } else {
                    alert('Error updating sort order');
                }
            }
        });
    }
    
    // Helper function to show errors
    function showError(message) {
        $('#formErrors').html(message).removeClass('d-none');
    }
    
    // Reset modal when closed
    $('#quickAddModal').on('hidden.bs.modal', function() {
        $('#quickAddForm')[0].reset();
        $('#platformPreview').addClass('d-none');
        $('#formErrors').addClass('d-none');
        $('#urlInput').attr('placeholder', 'Enter your profile URL...');
    });
});
</script>
@endpush
@endsection