@extends('super-admin.layouts.app')

@section('title', 'Menu Management')
@section('page-title', 'Super Admin Menu Management')

@section('content')
<div class="container-fluid">
    <!-- Menu Statistics Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📊 Menu Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card success text-center p-3 rounded">
                                <h3 class="mb-2">{{ $menuStats['enabled'] }}</h3>
                                <p class="mb-1">Enabled Menus</p>
                                <small>{{ $menuStats['enabled_percentage'] }}% of total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card danger text-center p-3 rounded">
                                <h3 class="mb-2">{{ $menuStats['disabled'] }}</h3>
                                <p class="mb-1">Disabled Menus</p>
                                <small>{{ $menuStats['disabled_percentage'] }}% of total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card text-center p-3 rounded">
                                <h3 class="mb-2">{{ $menuStats['total'] }}</h3>
                                <p class="mb-1">Total Menus</p>
                                <small>Available in system</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $menuStats['enabled_percentage'] }}%" 
                                         aria-valuenow="{{ $menuStats['enabled_percentage'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">Menu Activation Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚡ Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('super-admin.menu-management.enable-all') }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2" 
                                        onclick="return confirm('Are you sure you want to enable ALL menus? This will show all navigation items.')">
                                    <i class="fas fa-check-circle"></i> Enable All Menus
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('super-admin.menu-management.disable-all') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2" 
                                        onclick="return confirm('Are you sure you want to disable ALL non-essential menus? Only core menus will remain visible.')">
                                    <i class="fas fa-eye-slash"></i> Disable Non-Essential
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('super-admin.menu-management.reset-recommended') }}">
                                @csrf
                                <button type="submit" class="btn btn-info w-100 mb-2" 
                                        onclick="return confirm('Are you sure you want to reset to recommended settings? This will show only commonly working features.')">
                                    <i class="fas fa-undo"></i> Reset to Recommended
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary w-100 mb-2" onclick="selectAllMenus()">
                                <i class="fas fa-check-square"></i> Select All Visible
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Configuration Form -->
    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('super-admin.menu-management.update') }}" id="menuConfigForm">
                @csrf
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">🔧 Menu Configuration</h5>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleAllSections()">
                                <i class="fas fa-expand-alt"></i> Toggle All Sections
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($groupedMenus as $categoryName => $menus)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header" data-bs-toggle="collapse" 
                                             data-bs-target="#category-{{ Str::slug($categoryName) }}" 
                                             aria-expanded="true" 
                                             style="cursor: pointer; background-color: #f8f9fa;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chevron-down"></i>
                                                {{ $categoryName }} 
                                                <span class="badge bg-primary ms-2">
                                                    {{ collect($menus)->where('enabled', true)->count() }}/{{ count($menus) }}
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="collapse show" id="category-{{ Str::slug($categoryName) }}">
                                            <div class="card-body">
                                                @foreach($menus as $menuKey => $menuData)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input menu-checkbox" 
                                                               type="checkbox" 
                                                               value="1" 
                                                               id="menu_{{ $menuKey }}" 
                                                               name="menus[{{ $menuKey }}]"
                                                               data-category="{{ Str::slug($categoryName) }}"
                                                               {{ $menuData['enabled'] ? 'checked' : '' }}>
                                                        <label class="form-check-label d-flex justify-content-between align-items-center w-100" 
                                                               for="menu_{{ $menuKey }}">
                                                            <span>{{ $menuData['label'] }}</span>
                                                            @if($menuData['enabled'])
                                                                <span class="badge bg-success">Enabled</span>
                                                            @else
                                                                <span class="badge bg-secondary">Disabled</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                                
                                                <!-- Category Actions -->
                                                <div class="mt-3 pt-3 border-top">
                                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                                            onclick="enableCategoryMenus('{{ Str::slug($categoryName) }}')">
                                                        <i class="fas fa-check"></i> Enable All
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                            onclick="disableCategoryMenus('{{ Str::slug($categoryName) }}')">
                                                        <i class="fas fa-times"></i> Disable All
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Menu Configuration
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Reset Changes
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Changes will take effect immediately after saving.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">ℹ️ Help & Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>What does this do?</h6>
                            <p class="text-muted small">
                                This interface allows you to control which menu items appear in the super admin navigation. 
                                Disabled items will be completely hidden from the sidebar menu, making the interface cleaner 
                                and showing only functional features.
                            </p>
                            
                            <h6>Recommendations:</h6>
                            <ul class="text-muted small">
                                <li>Start with "Reset to Recommended" to show only commonly working features</li>
                                <li>Enable additional features as they become functional</li>
                                <li>Keep essential menus (Dashboard, Settings, Users) always enabled</li>
                                <li>Disable placeholder or non-functional menu items</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Color Legend:</h6>
                            <ul class="list-unstyled small">
                                <li><span class="badge bg-success">Enabled</span> - Menu item is visible in navigation</li>
                                <li><span class="badge bg-secondary">Disabled</span> - Menu item is hidden from navigation</li>
                            </ul>
                            
                            <h6>Quick Actions:</h6>
                            <ul class="text-muted small">
                                <li><strong>Enable All:</strong> Show all available menu items</li>
                                <li><strong>Disable Non-Essential:</strong> Hide all except core functionality</li>
                                <li><strong>Reset to Recommended:</strong> Use curated list of working features</li>
                                <li><strong>Select All Visible:</strong> Check all currently displayed categories</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
});

function selectAllMenus() {
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateStatistics();
}

function enableCategoryMenus(category) {
    const checkboxes = document.querySelectorAll(`[data-category="${category}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateStatistics();
}

function disableCategoryMenus(category) {
    const checkboxes = document.querySelectorAll(`[data-category="${category}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateStatistics();
}

function toggleAllSections() {
    const collapses = document.querySelectorAll('[id^="category-"]');
    const isAnyExpanded = Array.from(collapses).some(collapse => 
        collapse.classList.contains('show')
    );
    
    collapses.forEach(collapse => {
        if (isAnyExpanded) {
            bootstrap.Collapse.getInstance(collapse)?.hide();
        } else {
            new bootstrap.Collapse(collapse, { show: true });
        }
    });
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes? This will revert to the last saved configuration.')) {
        window.location.reload();
    }
}

function updateStatistics() {
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    const total = checkboxes.length;
    const enabled = Array.from(checkboxes).filter(cb => cb.checked).length;
    const disabled = total - enabled;
    
    // Update any live statistics if needed
    console.log(`Statistics: ${enabled} enabled, ${disabled} disabled, ${total} total`);
}

// Add event listeners to update statistics when checkboxes change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('menu-checkbox')) {
        updateStatistics();
        
        // Update the label badge
        const label = e.target.nextElementSibling;
        const badge = label.querySelector('.badge');
        if (e.target.checked) {
            badge.className = 'badge bg-success';
            badge.textContent = 'Enabled';
        } else {
            badge.className = 'badge bg-secondary';
            badge.textContent = 'Disabled';
        }
        
        // Update category counter
        const category = e.target.dataset.category;
        const categoryCheckboxes = document.querySelectorAll(`[data-category="${category}"]`);
        const categoryEnabled = Array.from(categoryCheckboxes).filter(cb => cb.checked).length;
        const categoryTotal = categoryCheckboxes.length;
        
        const categoryHeader = document.querySelector(`#category-${category}`).previousElementSibling;
        const categoryBadge = categoryHeader.querySelector('.badge');
        categoryBadge.textContent = `${categoryEnabled}/${categoryTotal}`;
    }
});

// Auto-save functionality (optional)
let autoSaveTimeout;
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('menu-checkbox')) {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Uncomment to enable auto-save
            // document.getElementById('menuConfigForm').submit();
        }, 3000); // Auto-save after 3 seconds of no changes
    }
});
</script>

<style>
.stat-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border-radius: 10px;
}

.stat-card.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-card.danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

.card-header[data-bs-toggle="collapse"] {
    transition: background-color 0.3s ease;
}

.card-header[data-bs-toggle="collapse"]:hover {
    background-color: #e9ecef !important;
}

.form-check-label {
    cursor: pointer;
    font-size: 0.9rem;
}

.badge {
    font-size: 0.7rem;
}

.border {
    border: 1px solid #dee2e6 !important;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>
@endsection