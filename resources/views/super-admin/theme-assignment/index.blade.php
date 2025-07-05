@extends('super-admin.layouts.app')

@section('title', 'Theme Assignment')
@section('page-title', 'Theme Assignment Management')

@section('content')
<div class="theme-assignment-management">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="theme-icon me-3">
                        <i class="fas fa-paintbrush fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Theme Assignment Center</h2>
                        <p class="text-muted mb-0">Assign and manage themes for your companies</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-info" id="viewStats">
                        <i class="fas fa-chart-bar me-2"></i>View Statistics
                    </button>
                    <button class="btn btn-outline-success" id="bulkAssign">
                        <i class="fas fa-layer-group me-2"></i>Bulk Assign
                    </button>
                    <button class="btn btn-outline-warning" id="generateReport">
                        <i class="fas fa-file-export me-2"></i>Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-gradient-primary text-white">
                <div class="stats-content">
                    <div class="stats-number">{{ $companies->total() }}</div>
                    <div class="stats-label">Total Companies</div>
                    <div class="stats-icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-success text-white">
                <div class="stats-content">
                    <div class="stats-number">{{ $companies->where('theme_id', '!=', null)->count() }}</div>
                    <div class="stats-label">Companies with Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-warning text-white">
                <div class="stats-content">
                    <div class="stats-number">{{ $companies->where('theme_id', null)->count() }}</div>
                    <div class="stats-label">Unassigned Companies</div>
                    <div class="stats-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-info text-white">
                <div class="stats-content">
                    <div class="stats-number">{{ $themes->count() }}</div>
                    <div class="stats-label">Available Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-swatchbook"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter by Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Companies</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Theme Assignment</label>
                    <select class="form-select" id="themeFilter">
                        <option value="">All</option>
                        <option value="assigned">With Theme</option>
                        <option value="unassigned">Without Theme</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Theme Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        @foreach(App\Models\SuperAdmin\Theme::CATEGORIES as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search companies...">
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Quick Selection -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Quick Theme Selection</h5>
        </div>
        <div class="card-body">
            <div class="theme-quick-selection">
                @foreach($themes as $theme)
                <div class="quick-theme-card" data-theme-id="{{ $theme->id }}">
                    <div class="theme-preview">
                        @if($theme->preview_image)
                            <img src="{{ asset('images/' . $theme->preview_image) }}" alt="{{ $theme->name }}">
                        @else
                            <div class="placeholder">
                                <i class="fas fa-palette"></i>
                            </div>
                        @endif
                    </div>
                    <div class="theme-info">
                        <h6>{{ $theme->name }}</h6>
                        <small class="text-muted">{{ $theme->category_name }}</small>
                        <div class="theme-colors">
                            @if($theme->color_scheme)
                                @foreach(['primary', 'secondary', 'accent'] as $color)
                                    @if(isset($theme->color_scheme[$color]))
                                        <span class="color-dot" style="background-color: {{ $theme->color_scheme[$color] }}"></span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <small class="usage-count">{{ $theme->companies->count() }} companies</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Companies List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Companies</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" id="selectAll">
                        <i class="fas fa-check-double me-1"></i>Select All
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="clearSelection">
                        <i class="fas fa-times me-1"></i>Clear Selection
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($companies->count() > 0)
                <div class="companies-grid">
                    @foreach($companies as $company)
                    <div class="company-card" 
                         data-company-id="{{ $company->id }}"
                         data-status="{{ $company->status }}"
                         data-theme-assigned="{{ $company->theme_id ? 'assigned' : 'unassigned' }}"
                         data-theme-category="{{ $company->theme ? $company->theme->category : '' }}"
                         data-name="{{ strtolower($company->name) }}">
                        
                        <div class="company-header">
                            <div class="company-select">
                                <input type="checkbox" class="company-checkbox" value="{{ $company->id }}">
                            </div>
                            <div class="company-info">
                                <h6 class="company-name">{{ $company->name }}</h6>
                                <small class="company-domain">{{ $company->domain }}</small>
                            </div>
                            <div class="company-status">
                                <span class="badge {{ $company->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="company-body">
                            @if($company->theme)
                                <div class="current-theme">
                                    <div class="theme-preview-small">
                                        @if($company->theme->preview_image)
                                            <img src="{{ asset('images/' . $company->theme->preview_image) }}" alt="{{ $company->theme->name }}">
                                        @else
                                            <div class="placeholder-small">
                                                <i class="fas fa-palette"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="theme-details">
                                        <strong>{{ $company->theme->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $company->theme->category_name }}</small>
                                        <div class="theme-colors-small">
                                            @if($company->theme->color_scheme)
                                                @foreach(['primary', 'secondary', 'accent'] as $color)
                                                    @if(isset($company->theme->color_scheme[$color]))
                                                        <span class="color-dot-small" style="background-color: {{ $company->theme->color_scheme[$color] }}"></span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="no-theme">
                                    <i class="fas fa-exclamation-circle text-warning"></i>
                                    <span class="text-muted">No theme assigned</span>
                                </div>
                            @endif
                        </div>

                        <div class="company-actions">
                            <div class="btn-group w-100">
                                <button class="btn btn-sm btn-outline-primary assign-theme-btn" 
                                        data-company-id="{{ $company->id }}"
                                        data-company-name="{{ $company->name }}">
                                    <i class="fas fa-paintbrush me-1"></i>{{ $company->theme ? 'Change' : 'Assign' }}
                                </button>
                                
                                @if($company->theme)
                                    <button class="btn btn-sm btn-outline-info preview-theme-btn" 
                                            data-company-id="{{ $company->id }}"
                                            data-theme-id="{{ $company->theme->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary customize-theme-btn" 
                                            data-company-id="{{ $company->id }}">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger unassign-theme-btn" 
                                            data-company-id="{{ $company->id }}"
                                            data-company-name="{{ $company->name }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $companies->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-building fa-4x text-muted"></i>
                    </div>
                    <h4 class="empty-title">No Companies Found</h4>
                    <p class="empty-description">No companies match your current filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Theme Assignment Modal -->
<div class="modal fade" id="themeAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Theme to <span id="targetCompanyName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="theme-selection-grid">
                    @foreach($themes as $theme)
                    <div class="theme-option" data-theme-id="{{ $theme->id }}">
                        <div class="theme-preview-modal">
                            @if($theme->preview_image)
                                <img src="{{ asset('images/' . $theme->preview_image) }}" alt="{{ $theme->name }}">
                            @else
                                <div class="placeholder-modal">
                                    <i class="fas fa-palette fa-2x"></i>
                                </div>
                            @endif
                            <div class="theme-overlay">
                                <div class="theme-actions">
                                    <button class="btn btn-sm btn-light preview-btn" 
                                            data-theme-id="{{ $theme->id }}"
                                            data-theme-name="{{ $theme->name }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary select-btn" 
                                            data-theme-id="{{ $theme->id }}"
                                            data-theme-name="{{ $theme->name }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="theme-info-modal">
                            <h6>{{ $theme->name }}</h6>
                            <small class="text-muted">{{ $theme->category_name }}</small>
                            <div class="theme-colors-modal">
                                @if($theme->color_scheme)
                                    @foreach(['primary', 'secondary', 'accent'] as $color)
                                        @if(isset($theme->color_scheme[$color]))
                                            <span class="color-dot" style="background-color: {{ $theme->color_scheme[$color] }}"></span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <div class="theme-meta">
                                <small>{{ $theme->companies->count() }} companies</small>
                                @if($theme->rating)
                                    <small class="ms-2">
                                        <i class="fas fa-star text-warning"></i>
                                        {{ number_format($theme->rating, 1) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Assignment Modal -->
<div class="modal fade" id="bulkAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Theme Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="text-muted">Select a theme to assign to <span id="selectedCompaniesCount">0</span> selected companies.</p>
                </div>
                <div class="theme-selection-grid">
                    @foreach($themes as $theme)
                    <div class="theme-option-bulk" data-theme-id="{{ $theme->id }}">
                        <div class="theme-preview-modal">
                            @if($theme->preview_image)
                                <img src="{{ asset('images/' . $theme->preview_image) }}" alt="{{ $theme->name }}">
                            @else
                                <div class="placeholder-modal">
                                    <i class="fas fa-palette fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="theme-info-modal">
                            <h6>{{ $theme->name }}</h6>
                            <small class="text-muted">{{ $theme->category_name }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAssign">Assign Theme</button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Theme Assignment Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="statisticsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.theme-assignment-management {
    padding: 20px 0;
}

.stats-card {
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-content {
    position: relative;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.stats-icon {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 2rem;
    opacity: 0.3;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.theme-quick-selection {
    display: flex;
    gap: 15px;
    overflow-x: auto;
    padding: 10px 0;
}

.quick-theme-card {
    min-width: 200px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.quick-theme-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.2);
}

.quick-theme-card.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.quick-theme-card .theme-preview {
    width: 100%;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 10px;
}

.quick-theme-card .theme-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.quick-theme-card .placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #6c757d;
}

.theme-colors {
    display: flex;
    justify-content: center;
    gap: 3px;
    margin: 5px 0;
}

.color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
}

.color-dot-small {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
}

.companies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.company-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.company-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.company-card.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.company-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.company-select input {
    transform: scale(1.2);
}

.company-info {
    flex: 1;
}

.company-name {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.company-domain {
    color: #6c757d;
}

.company-body {
    margin-bottom: 15px;
}

.current-theme {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.theme-preview-small {
    width: 50px;
    height: 30px;
    border-radius: 4px;
    overflow: hidden;
}

.theme-preview-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-small {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #6c757d;
}

.theme-colors-small {
    display: flex;
    gap: 2px;
    margin-top: 3px;
}

.no-theme {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 8px;
    color: #856404;
}

.theme-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    max-height: 500px;
    overflow-y: auto;
}

.theme-option, .theme-option-bulk {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-option:hover, .theme-option-bulk:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.2);
}

.theme-option.selected, .theme-option-bulk.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.theme-preview-modal {
    position: relative;
    width: 100%;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 10px;
}

.theme-preview-modal img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-modal {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.theme-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.theme-option:hover .theme-overlay {
    opacity: 1;
}

.theme-actions {
    display: flex;
    gap: 10px;
}

.theme-info-modal {
    text-align: center;
}

.theme-colors-modal {
    display: flex;
    justify-content: center;
    gap: 3px;
    margin: 5px 0;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    margin-bottom: 20px;
}

.empty-title {
    color: #333;
    margin-bottom: 10px;
}

.empty-description {
    color: #666;
    margin-bottom: 30px;
}

.company-card.hidden {
    display: none;
}

@media (max-width: 768px) {
    .companies-grid {
        grid-template-columns: 1fr;
    }
    
    .theme-quick-selection {
        flex-direction: column;
        align-items: center;
    }
    
    .quick-theme-card {
        min-width: 100%;
    }
    
    .theme-selection-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedTheme = null;
    let selectedCompanies = [];
    let targetCompany = null;

    // Filter functionality
    function filterCompanies() {
        const status = $('#statusFilter').val().toLowerCase();
        const themeAssignment = $('#themeFilter').val().toLowerCase();
        const category = $('#categoryFilter').val().toLowerCase();
        const search = $('#searchInput').val().toLowerCase();
        
        $('.company-card').each(function() {
            const $card = $(this);
            const cardStatus = $card.data('status');
            const cardThemeAssigned = $card.data('theme-assigned');
            const cardCategory = $card.data('theme-category');
            const cardName = $card.data('name');
            
            let show = true;
            
            if (status && cardStatus !== status) show = false;
            if (themeAssignment && cardThemeAssigned !== themeAssignment) show = false;
            if (category && cardCategory !== category) show = false;
            if (search && !cardName.includes(search)) show = false;
            
            if (show) {
                $card.removeClass('hidden').show();
            } else {
                $card.addClass('hidden').hide();
            }
        });
    }
    
    // Filter event listeners
    $('#statusFilter, #themeFilter, #categoryFilter').on('change', filterCompanies);
    $('#searchInput').on('input', filterCompanies);

    // Quick theme selection
    $('.quick-theme-card').on('click', function() {
        $('.quick-theme-card').removeClass('selected');
        $(this).addClass('selected');
        selectedTheme = $(this).data('theme-id');
    });

    // Company selection
    $('.company-checkbox').on('change', function() {
        const companyId = $(this).val();
        const $card = $(this).closest('.company-card');
        
        if ($(this).is(':checked')) {
            selectedCompanies.push(companyId);
            $card.addClass('selected');
        } else {
            selectedCompanies = selectedCompanies.filter(id => id !== companyId);
            $card.removeClass('selected');
        }
        
        updateSelectionCount();
    });

    // Select all companies
    $('#selectAll').on('click', function() {
        $('.company-checkbox:visible').prop('checked', true).trigger('change');
    });

    // Clear selection
    $('#clearSelection').on('click', function() {
        $('.company-checkbox').prop('checked', false).trigger('change');
        selectedCompanies = [];
        $('.company-card').removeClass('selected');
        updateSelectionCount();
    });

    function updateSelectionCount() {
        $('#selectedCompaniesCount').text(selectedCompanies.length);
    }

    // Assign theme to single company
    $('.assign-theme-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        const companyName = $(this).data('company-name');
        
        targetCompany = companyId;
        $('#targetCompanyName').text(companyName);
        $('#themeAssignmentModal').modal('show');
    });

    // Theme selection in modal
    $('.theme-option .select-btn').on('click', function() {
        const themeId = $(this).data('theme-id');
        const themeName = $(this).data('theme-name');
        
        if (targetCompany) {
            assignTheme(targetCompany, themeId, themeName);
        }
        
        $('#themeAssignmentModal').modal('hide');
    });

    // Bulk assignment
    $('#bulkAssign').on('click', function() {
        if (selectedCompanies.length === 0) {
            showNotification('Please select at least one company', 'error');
            return;
        }
        
        updateSelectionCount();
        $('#bulkAssignmentModal').modal('show');
    });

    // Bulk theme selection
    $('.theme-option-bulk').on('click', function() {
        $('.theme-option-bulk').removeClass('selected');
        $(this).addClass('selected');
        selectedTheme = $(this).data('theme-id');
    });

    // Confirm bulk assignment
    $('#confirmBulkAssign').on('click', function() {
        if (!selectedTheme) {
            showNotification('Please select a theme', 'error');
            return;
        }
        
        if (selectedCompanies.length === 0) {
            showNotification('Please select at least one company', 'error');
            return;
        }
        
        bulkAssignTheme(selectedCompanies, selectedTheme);
        $('#bulkAssignmentModal').modal('hide');
    });

    // Unassign theme
    $('.unassign-theme-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        const companyName = $(this).data('company-name');
        
        if (confirm(`Are you sure you want to unassign the theme from ${companyName}?`)) {
            unassignTheme(companyId);
        }
    });

    // Preview theme
    $('.preview-theme-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        const themeId = $(this).data('theme-id');
        
        window.open(`/super-admin/theme-assignments/companies/${companyId}/themes/${themeId}/preview`, '_blank');
    });

    // Customize theme
    $('.customize-theme-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        
        // TODO: Implement theme customization modal
        showNotification('Theme customization coming soon!', 'info');
    });

    // View statistics
    $('#viewStats').on('click', function() {
        loadStatistics();
        $('#statisticsModal').modal('show');
    });

    // Generate report
    $('#generateReport').on('click', function() {
        generateReport();
    });

    // Functions
    function assignTheme(companyId, themeId, themeName) {
        $.ajax({
            url: `/super-admin/theme-assignments/companies/${companyId}/assign`,
            method: 'POST',
            data: {
                theme_id: themeId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification(`Theme "${themeName}" assigned successfully!`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(response.message || 'Error assigning theme', 'error');
                }
            },
            error: function() {
                showNotification('Error assigning theme. Please try again.', 'error');
            }
        });
    }

    function bulkAssignTheme(companyIds, themeId) {
        $.ajax({
            url: '/super-admin/theme-assignments/bulk-assign',
            method: 'POST',
            data: {
                companies: companyIds,
                theme_id: themeId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(response.message || 'Error assigning themes', 'error');
                }
            },
            error: function() {
                showNotification('Error assigning themes. Please try again.', 'error');
            }
        });
    }

    function unassignTheme(companyId) {
        $.ajax({
            url: `/super-admin/theme-assignments/companies/${companyId}/unassign`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Theme unassigned successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(response.message || 'Error unassigning theme', 'error');
                }
            },
            error: function() {
                showNotification('Error unassigning theme. Please try again.', 'error');
            }
        });
    }

    function loadStatistics() {
        $.ajax({
            url: '/super-admin/theme-assignments/stats',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    displayStatistics(response.stats);
                } else {
                    $('#statisticsContent').html('<p class="text-center text-muted">Error loading statistics</p>');
                }
            },
            error: function() {
                $('#statisticsContent').html('<p class="text-center text-muted">Error loading statistics</p>');
            }
        });
    }

    function displayStatistics(stats) {
        const html = `
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">${stats.total_themes}</h3>
                            <p class="text-muted">Total Themes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">${stats.companies_with_themes}</h3>
                            <p class="text-muted">Companies with Themes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning">${stats.companies_without_themes}</h3>
                            <p class="text-muted">Companies without Themes</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h5>Most Popular Themes</h5>
                <div class="list-group">
                    ${stats.most_popular_themes.map(theme => `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${theme.name}</strong>
                                <small class="text-muted d-block">${theme.category}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${theme.downloads_count} downloads</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        $('#statisticsContent').html(html);
    }

    function generateReport() {
        $.ajax({
            url: '/super-admin/theme-assignments/report',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    downloadReport(response.report);
                } else {
                    showNotification('Error generating report', 'error');
                }
            },
            error: function() {
                showNotification('Error generating report. Please try again.', 'error');
            }
        });
    }

    function downloadReport(report) {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(report, null, 2));
        const downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href", dataStr);
        downloadAnchorNode.setAttribute("download", "theme-assignment-report.json");
        document.body.appendChild(downloadAnchorNode);
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
        
        showNotification('Report downloaded successfully!', 'success');
    }

    // Notification helper
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 
                          type === 'info' ? 'alert-info' : 'alert-warning';
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-exclamation-circle' : 
                    type === 'info' ? 'fa-info-circle' : 'fa-exclamation-triangle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }
});
</script>
@endpush
