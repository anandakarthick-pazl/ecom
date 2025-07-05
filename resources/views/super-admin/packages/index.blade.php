@extends('super-admin.layouts.app')

@section('title', 'Packages Management')
@section('page-title', 'Packages Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>All Packages
                </h5>
                <a href="{{ route('super-admin.packages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Package
                </a>
            </div>
            <div class="card-body">
                <!-- Filter Bar -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="billingCycleFilter">
                            <option value="">All Billing Cycles</option>
                            @foreach(App\Models\SuperAdmin\Package::BILLING_CYCLES as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="popularFilter">
                            <option value="">All Packages</option>
                            <option value="popular">Popular Only</option>
                            <option value="regular">Regular Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search packages...">
                    </div>
                </div>

                @if($packages->count() > 0)
                    <div class="row" id="packagesGrid">
                        @foreach($packages as $package)
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 package-card" 
                             data-billing-cycle="{{ $package->billing_cycle }}" 
                             data-status="{{ $package->status }}"
                             data-popular="{{ $package->is_popular ? 'popular' : 'regular' }}"
                             data-name="{{ strtolower($package->name) }}">
                            <div class="card h-100 package-item {{ $package->is_popular ? 'border-warning' : '' }}">
                                <!-- Popular Badge -->
                                @if($package->is_popular)
                                    <div class="position-absolute top-0 start-50 translate-middle">
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-star me-1"></i>POPULAR
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Package Header -->
                                <div class="card-header text-center {{ $package->is_popular ? 'bg-warning bg-opacity-10' : '' }}">
                                    <h5 class="card-title mb-1">{{ $package->name }}</h5>
                                    <div class="mb-2">
                                        <span class="badge {{ $package->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($package->status) }}
                                        </span>
                                        <span class="badge bg-info">{{ $package->billing_cycle_name }}</span>
                                    </div>
                                    
                                    <!-- Price Display -->
                                    <div class="price-display">
                                        @if($package->price == 0)
                                            <h2 class="text-primary mb-0">FREE</h2>
                                        @else
                                            <h2 class="text-primary mb-0">
                                                ${{ number_format($package->price, 2) }}
                                                <small class="text-muted fs-6">/{{ $package->billing_cycle }}</small>
                                            </h2>
                                        @endif
                                    </div>
                                    
                                    @if($package->trial_days > 0)
                                        <small class="text-success">
                                            <i class="fas fa-gift me-1"></i>{{ $package->trial_days }}-day free trial
                                        </small>
                                    @endif
                                </div>
                                
                                <div class="card-body">
                                    <p class="card-text text-muted">{{ Str::limit($package->description, 100) }}</p>
                                    
                                    <!-- Features -->
                                    @if($package->features && count($package->features) > 0)
                                        <div class="features-list mb-3">
                                            <h6 class="text-muted mb-2">Features:</h6>
                                            <ul class="list-unstyled">
                                                @foreach(array_slice($package->features, 0, 5) as $feature)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check text-success me-2"></i>
                                                        <small>{{ $feature }}</small>
                                                    </li>
                                                @endforeach
                                                @if(count($package->features) > 5)
                                                    <li class="text-muted">
                                                        <small>+ {{ count($package->features) - 5 }} more features</small>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <!-- Limits -->
                                    @if($package->limits && count($package->limits) > 0)
                                        <div class="limits-list mb-3">
                                            <h6 class="text-muted mb-2">Limits:</h6>
                                            <div class="row">
                                                @foreach($package->limits as $key => $limit)
                                                    <div class="col-6 mb-1">
                                                        <small class="text-muted">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $limit }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $package->companies->count() }} companies
                                        </small>
                                        <small class="text-muted">
                                            Sort: {{ $package->sort_order }}
                                        </small>
                                    </div>
                                    
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('super-admin.packages.show', $package) }}" 
                                           class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.packages.edit', $package) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-status-btn" 
                                                data-package-id="{{ $package->id }}"
                                                data-current-status="{{ $package->status }}" 
                                                title="Toggle Status">
                                            <i class="fas {{ $package->status === 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('super-admin.packages.show', $package) }}">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('super-admin.packages.edit', $package) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit Package
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger delete-btn" 
                                                            data-package-id="{{ $package->id }}"
                                                            data-package-name="{{ $package->name }}"
                                                            data-companies-count="{{ $package->companies->count() }}">
                                                        <i class="fas fa-trash me-2"></i>Delete Package
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $packages->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Packages Found</h5>
                        <p class="text-muted">Start by creating your first package plan.</p>
                        <a href="{{ route('super-admin.packages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Package
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
                <p>Are you sure you want to delete the package <strong id="packageName"></strong>?</p>
                <div class="alert alert-warning" id="companiesWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This package is currently being used by <span id="companiesCount"></span> company(ies). 
                    You cannot delete a package that has active companies.
                </div>
                <div class="alert alert-info" id="safeDelete" style="display: none;">
                    <i class="fas fa-info-circle me-2"></i>
                    This package is not currently being used by any companies, so it's safe to delete.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete Package</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.package-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.package-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.package-item.border-warning {
    border-width: 2px !important;
}

.price-display h2 {
    font-weight: 700;
}

.features-list ul li {
    font-size: 0.9rem;
}

.package-card {
    transition: opacity 0.3s ease;
}

.package-card.hidden {
    opacity: 0;
    pointer-events: none;
}

.limits-list {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
}

@media (max-width: 768px) {
    .package-card {
        margin-bottom: 2rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    function filterPackages() {
        const billingCycle = $('#billingCycleFilter').val().toLowerCase();
        const status = $('#statusFilter').val().toLowerCase();
        const popular = $('#popularFilter').val().toLowerCase();
        const search = $('#searchInput').val().toLowerCase();
        
        $('.package-card').each(function() {
            const $card = $(this);
            const cardBillingCycle = $card.data('billing-cycle').toLowerCase();
            const cardStatus = $card.data('status').toLowerCase();
            const cardPopular = $card.data('popular').toLowerCase();
            const cardName = $card.data('name').toLowerCase();
            
            let show = true;
            
            if (billingCycle && cardBillingCycle !== billingCycle) show = false;
            if (status && cardStatus !== status) show = false;
            if (popular && cardPopular !== popular) show = false;
            if (search && !cardName.includes(search)) show = false;
            
            if (show) {
                $card.removeClass('hidden').show();
            } else {
                $card.addClass('hidden').hide();
            }
        });
    }
    
    // Filter event listeners
    $('#billingCycleFilter, #statusFilter, #popularFilter').on('change', filterPackages);
    $('#searchInput').on('input', filterPackages);
    
    // Toggle status
    $('.toggle-status-btn').on('click', function() {
        const packageId = $(this).data('package-id');
        const currentStatus = $(this).data('current-status');
        const $btn = $(this);
        const $icon = $btn.find('i');
        
        $.ajax({
            url: `/super-admin/packages/${packageId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                    $btn.data('current-status', newStatus);
                    
                    // Update button icon
                    if (newStatus === 'active') {
                        $icon.removeClass('fa-play').addClass('fa-pause');
                    } else {
                        $icon.removeClass('fa-pause').addClass('fa-play');
                    }
                    
                    // Update status badge
                    const $statusBadge = $btn.closest('.package-card').find('.badge').first();
                    $statusBadge.removeClass('bg-success bg-secondary')
                               .addClass(newStatus === 'active' ? 'bg-success' : 'bg-secondary')
                               .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    // Update card data attribute
                    $btn.closest('.package-card').data('status', newStatus);
                    
                    // Show success message
                    const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>Package status updated successfully!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    $('.content-wrapper').prepend(alert);
                    
                    setTimeout(() => alert.fadeOut(), 3000);
                }
            },
            error: function() {
                alert('Error updating package status. Please try again.');
            }
        });
    });
    
    // Delete package
    $('.delete-btn').on('click', function() {
        const packageId = $(this).data('package-id');
        const packageName = $(this).data('package-name');
        const companiesCount = $(this).data('companies-count');
        
        $('#packageName').text(packageName);
        $('#deleteForm').attr('action', `/super-admin/packages/${packageId}`);
        
        if (companiesCount > 0) {
            $('#companiesCount').text(companiesCount);
            $('#companiesWarning').show();
            $('#safeDelete').hide();
            $('#confirmDeleteBtn').prop('disabled', true).text('Cannot Delete');
        } else {
            $('#companiesWarning').hide();
            $('#safeDelete').show();
            $('#confirmDeleteBtn').prop('disabled', false).text('Delete Package');
        }
        
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush
