@extends('super-admin.layouts.app')

@section('title', 'Package Details')
@section('page-title', 'Package Details')

@section('content')
<div class="row">
    <!-- Package Overview -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>{{ $package->name }}
                    @if($package->is_popular)
                        <span class="badge bg-warning text-dark ms-2">
                            <i class="fas fa-star me-1"></i>POPULAR
                        </span>
                    @endif
                </h5>
                <div>
                    <a href="{{ route('super-admin.packages.edit', $package) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('super-admin.packages.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Package Name:</td>
                                <td>{{ $package->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Price:</td>
                                <td>
                                    <h5 class="text-primary mb-0">
                                        {{ $package->formatted_price }}
                                        @if($package->price > 0)
                                            <small class="text-muted">/{{ $package->billing_cycle }}</small>
                                        @endif
                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Billing Cycle:</td>
                                <td>
                                    <span class="badge bg-info">{{ $package->billing_cycle_name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status:</td>
                                <td>
                                    <span class="badge {{ $package->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($package->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Trial Period:</td>
                                <td>
                                    @if($package->trial_days > 0)
                                        <span class="badge bg-success">{{ $package->trial_days }} days</span>
                                    @else
                                        <span class="text-muted">No trial</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Created:</td>
                                <td>{{ $package->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Last Updated:</td>
                                <td>{{ $package->updated_at->format('M d, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Companies Using:</td>
                                <td>
                                    <span class="badge bg-primary">{{ $package->companies->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Sort Order:</td>
                                <td>{{ $package->sort_order }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Popular Package:</td>
                                <td>
                                    @if($package->is_popular)
                                        <i class="fas fa-star text-warning me-1"></i>Yes
                                    @else
                                        <span class="text-muted">No</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted">Description:</h6>
                        <p class="text-muted">{{ $package->description }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Package Features -->
        @if($package->features && count($package->features) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Package Features ({{ count($package->features) }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($package->features as $feature)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Package Limits -->
        @if($package->limits && count($package->limits) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-ruler me-2"></i>Package Limits
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($package->limits as $key => $limit)
                            <div class="col-md-6 mb-3">
                                <div class="limit-item">
                                    <span class="fw-bold text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="badge bg-warning text-dark ms-2">{{ $limit }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Companies Using This Package -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>Companies Using This Package ({{ $package->companies->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($package->companies->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Company</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Trial Status</th>
                                    <th>Since</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($package->companies as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($company->logo)
                                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                                     class="rounded me-2" width="32" height="32" 
                                                     style="object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px; font-size: 12px;">
                                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <span>{{ $company->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="http://{{ $company->domain }}" target="_blank" class="text-decoration-none">
                                            {{ $company->domain }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $company->status === 'active' ? 'bg-success' : ($company->status === 'inactive' ? 'bg-secondary' : 'bg-danger') }}">
                                            {{ ucfirst($company->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($company->trial_ends_at)
                                            @if($company->trial_ends_at->isPast())
                                                <span class="badge bg-danger">Trial Expired</span>
                                            @else
                                                <span class="badge bg-success">
                                                    {{ $company->trial_ends_at->diffInDays(now()) }} days left
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">No Trial</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $company->created_at->format('M d, Y') }}
                                            <br>{{ $company->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.companies.show', $company) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-building fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No companies are currently using this package.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Package Preview Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                @if($package->is_popular)
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2">
                            <i class="fas fa-star me-1"></i>POPULAR CHOICE
                        </span>
                    </div>
                @endif
                
                <h4>{{ $package->name }}</h4>
                <div class="mb-3">
                    <span class="badge {{ $package->status === 'active' ? 'bg-success' : 'bg-secondary' }} me-2">
                        {{ ucfirst($package->status) }}
                    </span>
                    <span class="badge bg-info">{{ $package->billing_cycle_name }}</span>
                </div>
                
                <div class="price-display mb-3">
                    <h2 class="text-primary mb-0">{{ $package->formatted_price }}</h2>
                    @if($package->price > 0)
                        <small class="text-muted">/{{ $package->billing_cycle }}</small>
                    @endif
                </div>
                
                @if($package->trial_days > 0)
                    <div class="mb-3">
                        <span class="badge bg-success">
                            <i class="fas fa-gift me-1"></i>{{ $package->trial_days }}-day free trial
                        </span>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Package Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="mb-1 text-primary">{{ $package->companies->count() }}</h4>
                            <small class="text-muted">Total Companies</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="mb-1 text-success">{{ $package->companies->where('status', 'active')->count() }}</h4>
                        <small class="text-muted">Active Companies</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-1 text-warning">{{ $package->companies->filter(function($company) { return $company->trial_ends_at && $company->trial_ends_at->isFuture(); })->count() }}</h4>
                            <small class="text-muted">On Trial</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-1 text-info">{{ $package->created_at->diffInDays(now()) }}</h4>
                        <small class="text-muted">Days Available</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Analytics -->
        @if($package->companies->count() > 0 && $package->price > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Revenue Analytics</h6>
                </div>
                <div class="card-body">
                    @php
                        $activeCompanies = $package->companies->where('status', 'active')->count();
                        $potentialRevenue = $activeCompanies * $package->price;
                        $trialCompanies = $package->companies->filter(function($company) { 
                            return $company->trial_ends_at && $company->trial_ends_at->isFuture(); 
                        })->count();
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Potential {{ ucfirst($package->billing_cycle) }} Revenue</small>
                            <small class="fw-bold">${{ number_format($potentialRevenue, 2) }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Companies on Paid Plans</small>
                            <small>{{ $activeCompanies - $trialCompanies }}/{{ $activeCompanies }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $activeCompanies > 0 ? round((($activeCompanies - $trialCompanies) / $activeCompanies) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            Avg. revenue per company: <strong>${{ $activeCompanies > 0 ? number_format($potentialRevenue / $activeCompanies, 2) : '0.00' }}</strong>
                        </small>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Package Performance -->
        @if($package->companies->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Package Performance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Adoption Rate</small>
                            <small>{{ round(($package->companies->where('status', 'active')->count() / max($package->companies->count(), 1)) * 100) }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ round(($package->companies->where('status', 'active')->count() / max($package->companies->count(), 1)) * 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Trial Conversion</small>
                            @php
                                $expiredTrials = $package->companies->filter(function($company) { 
                                    return $company->trial_ends_at && $company->trial_ends_at->isPast(); 
                                })->count();
                                $activeAfterTrial = $package->companies->filter(function($company) { 
                                    return $company->trial_ends_at && $company->trial_ends_at->isPast() && $company->status === 'active'; 
                                })->count();
                                $conversionRate = $expiredTrials > 0 ? round(($activeAfterTrial / $expiredTrials) * 100) : 0;
                            @endphp
                            <small>{{ $conversionRate }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $conversionRate }}%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            Most recent signup: 
                            @if($package->companies->count() > 0)
                                {{ $package->companies->sortByDesc('created_at')->first()->created_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.packages.edit', $package) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit Package
                    </a>
                    
                    <button type="button" class="btn btn-outline-{{ $package->status === 'active' ? 'warning' : 'success' }} btn-sm toggle-status-btn" 
                            data-package-id="{{ $package->id }}"
                            data-current-status="{{ $package->status }}">
                        <i class="fas {{ $package->status === 'active' ? 'fa-pause' : 'fa-play' }} me-2"></i>
                        {{ $package->status === 'active' ? 'Deactivate' : 'Activate' }} Package
                    </button>
                    
                    <a href="{{ route('super-admin.companies.create', ['package_id' => $package->id]) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Company with Package
                    </a>
                    
                    @if($package->is_popular)
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePopular({{ $package->id }}, false)">
                            <i class="fas fa-star-half-alt me-2"></i>Remove Popular Status
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="togglePopular({{ $package->id }}, true)">
                            <i class="fas fa-star me-2"></i>Mark as Popular
                        </button>
                    @endif
                    
                    <hr>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            {{ $package->companies->count() > 0 ? 'disabled' : '' }}>
                        <i class="fas fa-trash me-2"></i>Delete Package
                    </button>
                    
                    @if($package->companies->count() > 0)
                        <small class="text-muted text-center d-block mt-1">
                            Cannot delete package with active companies
                        </small>
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
                <p>Are you sure you want to delete the package <strong>{{ $package->name }}</strong>?</p>
                
                @if($package->companies->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> This package cannot be deleted because it has {{ $package->companies->count() }} active companies. 
                        Please move all companies to other packages first.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This package is not currently being used by any companies, so it's safe to delete.
                    </div>
                @endif
                
                <div class="mb-3">
                    <label for="confirm_name" class="form-label">Type the package name to confirm:</label>
                    <input type="text" class="form-control" id="confirm_name" placeholder="{{ $package->name }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('super-admin.packages.destroy', $package) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" 
                            {{ $package->companies->count() > 0 ? 'disabled' : '' }}>
                        Delete Package
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle package status
    $('.toggle-status-btn').on('click', function() {
        const packageId = $(this).data('package-id');
        const currentStatus = $(this).data('current-status');
        const $btn = $(this);
        
        $.ajax({
            url: `/super-admin/packages/${packageId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Reload page to reflect changes
                    location.reload();
                }
            },
            error: function() {
                alert('Error updating package status. Please try again.');
            }
        });
    });
    
    // Enable delete button only when package name is typed correctly
    $('#confirm_name').on('input', function() {
        const typedName = $(this).val();
        const packageName = '{{ $package->name }}';
        const deleteBtn = $('#confirmDeleteBtn');
        
        if (typedName === packageName && {{ $package->companies->count() }} === 0) {
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

// Toggle popular status
function togglePopular(packageId, makePopular) {
    $.ajax({
        url: `/super-admin/packages/${packageId}`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            is_popular: makePopular ? 1 : 0,
            // Include other required fields to avoid validation errors
            name: '{{ $package->name }}',
            description: '{{ addslashes($package->description) }}',
            price: {{ $package->price }},
            billing_cycle: '{{ $package->billing_cycle }}',
            trial_days: {{ $package->trial_days }},
            status: '{{ $package->status }}',
            sort_order: {{ $package->sort_order }}
        },
        success: function(response) {
            location.reload();
        },
        error: function() {
            alert('Error updating popular status. Please try again.');
        }
    });
}
</script>
@endpush
