<?php $__env->startSection('title', 'Themes Management'); ?>
<?php $__env->startSection('page-title', 'Themes Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="themes-management">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="theme-icon me-3">
                        <i class="fas fa-palette fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h2 class="mb-0">Themes Gallery</h2>
                        <p class="text-muted mb-0">Manage beautiful ecommerce themes for your companies</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="importThemes">
                        <i class="fas fa-download me-2"></i>Import Themes
                    </button>
                    <a href="<?php echo e(route('super-admin.themes.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Theme
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-gradient-primary text-white">
                <div class="stats-content">
                    <div class="stats-number"><?php echo e($themes->total()); ?></div>
                    <div class="stats-label">Total Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-success text-white">
                <div class="stats-content">
                    <div class="stats-number"><?php echo e($themes->where('status', 'active')->count()); ?></div>
                    <div class="stats-label">Active Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-info text-white">
                <div class="stats-content">
                    <div class="stats-number"><?php echo e($themes->where('is_free', true)->count()); ?></div>
                    <div class="stats-label">Free Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-gradient-warning text-white">
                <div class="stats-content">
                    <div class="stats-number"><?php echo e($themes->where('is_free', false)->count()); ?></div>
                    <div class="stats-label">Premium Themes</div>
                    <div class="stats-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php $__currentLoopData = App\Models\SuperAdmin\Theme::CATEGORIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Layout Type</label>
                    <select class="form-select" id="layoutFilter">
                        <option value="">All Layouts</option>
                        <?php $__currentLoopData = App\Models\SuperAdmin\Theme::LAYOUT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select class="form-select" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="free">Free</option>
                        <option value="paid">Premium</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search themes...">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" id="sortPopular">
                                <i class="fas fa-fire me-1"></i>Popular
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="sortRating">
                                <i class="fas fa-star me-1"></i>Top Rated
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="sortLatest">
                                <i class="fas fa-clock me-1"></i>Latest
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary view-mode active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary view-mode" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Themes Grid -->
    <?php if($themes->count() > 0): ?>
        <div class="themes-grid" id="themesGrid">
            <?php $__currentLoopData = $themes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="theme-card" 
                 data-category="<?php echo e($theme->category); ?>" 
                 data-layout="<?php echo e($theme->layout_type); ?>"
                 data-status="<?php echo e($theme->status); ?>"
                 data-type="<?php echo e($theme->is_free ? 'free' : 'paid'); ?>"
                 data-name="<?php echo e(strtolower($theme->name)); ?>"
                 data-rating="<?php echo e($theme->rating); ?>"
                 data-downloads="<?php echo e($theme->downloads_count); ?>">
                
                <div class="theme-preview">
                    <!-- Theme Image -->
                    <div class="theme-image">
                        <?php if($theme->preview_image): ?>
                            <img src="<?php echo e(asset('images/' . $theme->preview_image)); ?>" alt="<?php echo e($theme->name); ?>">
                        <?php else: ?>
                            <div class="placeholder-image">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <div class="status-badge">
                            <span class="badge <?php echo e($theme->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e(ucfirst($theme->status)); ?>

                            </span>
                        </div>
                        
                        <!-- Price Badge -->
                        <div class="price-badge">
                            <?php if($theme->is_free): ?>
                                <span class="badge bg-primary">FREE</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">$<?php echo e(number_format($theme->price, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Color Scheme Preview -->
                        <div class="color-scheme">
                            <?php if($theme->color_scheme): ?>
                                <div class="color-dots">
                                    <?php $__currentLoopData = ['primary', 'secondary', 'accent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(isset($theme->color_scheme[$color])): ?>
                                            <span class="color-dot" style="background-color: <?php echo e($theme->color_scheme[$color]); ?>"></span>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Overlay Actions -->
                        <div class="theme-overlay">
                            <div class="theme-actions">
                                <?php if($theme->demo_url): ?>
                                    <a href="<?php echo e($theme->demo_url); ?>" target="_blank" class="btn btn-sm btn-light" title="Live Preview">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('super-admin.themes.show', $theme)); ?>" class="btn btn-sm btn-light" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('super-admin.themes.edit', $theme)); ?>" class="btn btn-sm btn-light" title="Edit Theme">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-light toggle-status-btn" 
                                        data-theme-id="<?php echo e($theme->id); ?>"
                                        data-current-status="<?php echo e($theme->status); ?>" 
                                        title="Toggle Status">
                                    <i class="fas <?php echo e($theme->status === 'active' ? 'fa-pause' : 'fa-play'); ?>"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Theme Info -->
                    <div class="theme-info">
                        <div class="theme-header">
                            <h5 class="theme-name"><?php echo e($theme->name); ?></h5>
                            <div class="theme-meta">
                                <span class="category-badge"><?php echo e($theme->category_name); ?></span>
                                <?php if($theme->layout_type): ?>
                                    <span class="layout-badge"><?php echo e($theme->layout_type_name); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="theme-description"><?php echo e(Str::limit($theme->description, 100)); ?></p>
                        
                        <!-- Rating and Stats -->
                        <div class="theme-stats">
                            <div class="rating">
                                <?php if($theme->rating): ?>
                                    <?php $__currentLoopData = $theme->rating_stars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <i class="<?php echo e($star); ?>"></i>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <span class="rating-text"><?php echo e($theme->rating); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">No rating</span>
                                <?php endif; ?>
                            </div>
                            <div class="downloads">
                                <i class="fas fa-download text-muted"></i>
                                <span><?php echo e(number_format($theme->downloads_count)); ?></span>
                            </div>
                        </div>
                        
                        <!-- Features -->
                        <?php if($theme->features && count($theme->features) > 0): ?>
                            <div class="theme-features">
                                <?php $__currentLoopData = array_slice($theme->features, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="feature-tag"><?php echo e($feature); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($theme->features) > 3): ?>
                                    <span class="more-features">+<?php echo e(count($theme->features) - 3); ?> more</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Author and Usage -->
                        <div class="theme-footer">
                            <div class="author">
                                <i class="fas fa-user-circle text-muted"></i>
                                <span><?php echo e($theme->author ?: 'Unknown'); ?></span>
                            </div>
                            <div class="usage">
                                <i class="fas fa-building text-muted"></i>
                                <span><?php echo e($theme->companies->count()); ?> companies</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="theme-actions-bottom">
                            <div class="btn-group w-100">
                                <a href="<?php echo e(route('super-admin.themes.show', $theme)); ?>" class="btn btn-outline-primary">
                                    Details
                                </a>
                                <a href="<?php echo e(route('super-admin.themes.edit', $theme)); ?>" class="btn btn-outline-secondary">
                                    Edit
                                </a>
                                <button class="btn btn-outline-danger delete-btn" 
                                        data-theme-id="<?php echo e($theme->id); ?>"
                                        data-theme-name="<?php echo e($theme->name); ?>"
                                        data-companies-count="<?php echo e($theme->companies->count()); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($themes->links()); ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-palette fa-4x text-muted"></i>
            </div>
            <h4 class="empty-title">No Themes Found</h4>
            <p class="empty-description">Start by creating your first beautiful theme or import existing ones.</p>
            <div class="empty-actions">
                <a href="<?php echo e(route('super-admin.themes.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Theme
                </a>
                <button class="btn btn-outline-primary" id="importThemes">
                    <i class="fas fa-download me-2"></i>Import Themes
                </button>
            </div>
        </div>
    <?php endif; ?>
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
                <p>Are you sure you want to delete the theme <strong id="themeName"></strong>?</p>
                <div class="alert alert-warning" id="companiesWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This theme is currently being used by <span id="companiesCount"></span> company(ies). 
                    Deleting it may affect those companies.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Theme</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import Themes Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Themes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="import-options">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="import-option">
                                <div class="import-icon">
                                    <i class="fas fa-seedling fa-2x text-success"></i>
                                </div>
                                <h6>Load Sample Themes</h6>
                                <p class="text-muted">Import beautiful pre-designed themes from our gallery</p>
                                <button class="btn btn-success" id="loadSampleThemes">Load Sample Themes</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="import-option">
                                <div class="import-icon">
                                    <i class="fas fa-upload fa-2x text-primary"></i>
                                </div>
                                <h6>Upload Theme Package</h6>
                                <p class="text-muted">Upload a theme package (.zip file) to import</p>
                                <input type="file" class="form-control" id="themePackage" accept=".zip">
                                <button class="btn btn-primary mt-2" id="uploadThemePackage">Upload Package</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.themes-management {
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

.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.theme-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.theme-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.theme-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.theme-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.theme-card:hover .theme-image img {
    transform: scale(1.05);
}

.placeholder-image {
    height: 100%;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.price-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}

.color-scheme {
    position: absolute;
    bottom: 10px;
    left: 10px;
    z-index: 2;
}

.color-dots {
    display: flex;
    gap: 5px;
}

.color-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.8);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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

.theme-card:hover .theme-overlay {
    opacity: 1;
}

.theme-actions {
    display: flex;
    gap: 10px;
}

.theme-actions .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-info {
    padding: 20px;
}

.theme-header {
    margin-bottom: 10px;
}

.theme-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.theme-meta {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.category-badge, .layout-badge {
    font-size: 0.75rem;
    padding: 3px 8px;
    border-radius: 12px;
    background: #f8f9fa;
    color: #666;
}

.theme-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 15px;
}

.theme-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.rating {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-text {
    font-size: 0.9rem;
    color: #666;
}

.downloads {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #666;
    font-size: 0.9rem;
}

.theme-features {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 15px;
}

.feature-tag {
    font-size: 0.7rem;
    padding: 2px 6px;
    background: #e9ecef;
    border-radius: 8px;
    color: #666;
}

.more-features {
    font-size: 0.7rem;
    color: #007bff;
    cursor: pointer;
}

.theme-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 0.8rem;
    color: #666;
}

.author, .usage {
    display: flex;
    align-items: center;
    gap: 5px;
}

.theme-actions-bottom .btn-group {
    display: flex;
}

.theme-actions-bottom .btn {
    border-radius: 0;
    font-size: 0.85rem;
}

.theme-actions-bottom .btn:first-child {
    border-radius: 8px 0 0 8px;
}

.theme-actions-bottom .btn:last-child {
    border-radius: 0 8px 8px 0;
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

.empty-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.view-mode {
    transition: all 0.3s ease;
}

.view-mode.active {
    background: #007bff;
    color: white;
}

.import-options {
    padding: 20px;
}

.import-option {
    text-align: center;
    padding: 30px;
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    height: 100%;
}

.import-icon {
    margin-bottom: 15px;
}

.import-option h6 {
    margin-bottom: 10px;
    color: #333;
}

.theme-card.hidden {
    display: none;
}

@media (max-width: 768px) {
    .themes-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-card {
        margin-bottom: 15px;
    }
    
    .theme-actions {
        flex-wrap: wrap;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Filter functionality
    function filterThemes() {
        const category = $('#categoryFilter').val().toLowerCase();
        const layout = $('#layoutFilter').val().toLowerCase();
        const status = $('#statusFilter').val().toLowerCase();
        const type = $('#typeFilter').val().toLowerCase();
        const search = $('#searchInput').val().toLowerCase();
        
        $('.theme-card').each(function() {
            const $card = $(this);
            const cardCategory = $card.data('category');
            const cardLayout = $card.data('layout');
            const cardStatus = $card.data('status');
            const cardType = $card.data('type');
            const cardName = $card.data('name');
            
            let show = true;
            
            if (category && cardCategory !== category) show = false;
            if (layout && cardLayout !== layout) show = false;
            if (status && cardStatus !== status) show = false;
            if (type && cardType !== type) show = false;
            if (search && !cardName.includes(search)) show = false;
            
            if (show) {
                $card.removeClass('hidden').show();
            } else {
                $card.addClass('hidden').hide();
            }
        });
    }
    
    // Filter event listeners
    $('#categoryFilter, #layoutFilter, #statusFilter, #typeFilter').on('change', filterThemes);
    $('#searchInput').on('input', filterThemes);
    
    // Sort functionality
    $('#sortPopular').on('click', function() {
        sortThemes('downloads');
    });
    
    $('#sortRating').on('click', function() {
        sortThemes('rating');
    });
    
    $('#sortLatest').on('click', function() {
        location.reload(); // Reload to get latest order
    });
    
    function sortThemes(criteria) {
        const $grid = $('#themesGrid');
        const $themes = $grid.find('.theme-card:not(.hidden)');
        
        $themes.sort(function(a, b) {
            const aValue = $(a).data(criteria);
            const bValue = $(b).data(criteria);
            return bValue - aValue;
        });
        
        $themes.detach().appendTo($grid);
    }
    
    // View mode toggle
    $('.view-mode').on('click', function() {
        const view = $(this).data('view');
        $('.view-mode').removeClass('active');
        $(this).addClass('active');
        
        if (view === 'list') {
            $('#themesGrid').addClass('list-view');
        } else {
            $('#themesGrid').removeClass('list-view');
        }
    });
    
    // Toggle status
    $('.toggle-status-btn').on('click', function() {
        const themeId = $(this).data('theme-id');
        const currentStatus = $(this).data('current-status');
        const $btn = $(this);
        const $icon = $btn.find('i');
        
        $.ajax({
            url: `/super-admin/themes/${themeId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
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
                    const $statusBadge = $btn.closest('.theme-card').find('.status-badge .badge');
                    $statusBadge.removeClass('bg-success bg-secondary')
                               .addClass(newStatus === 'active' ? 'bg-success' : 'bg-secondary')
                               .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    // Update card data attribute
                    $btn.closest('.theme-card').data('status', newStatus);
                    
                    // Show success message
                    showNotification('Theme status updated successfully!', 'success');
                }
            },
            error: function() {
                showNotification('Error updating theme status. Please try again.', 'error');
            }
        });
    });
    
    // Delete theme
    $('.delete-btn').on('click', function() {
        const themeId = $(this).data('theme-id');
        const themeName = $(this).data('theme-name');
        const companiesCount = $(this).data('companies-count');
        
        $('#themeName').text(themeName);
        $('#deleteForm').attr('action', `/super-admin/themes/${themeId}`);
        
        if (companiesCount > 0) {
            $('#companiesCount').text(companiesCount);
            $('#companiesWarning').show();
        } else {
            $('#companiesWarning').hide();
        }
        
        $('#deleteModal').modal('show');
    });
    
    // Import themes
    $('#importThemes').on('click', function() {
        $('#importModal').modal('show');
    });
    
    // Load sample themes
    $('#loadSampleThemes').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.text();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        
        $.ajax({
            url: '/super-admin/themes/load-samples',
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Sample themes loaded successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(response.message || 'Error loading sample themes', 'error');
                }
            },
            error: function() {
                showNotification('Error loading sample themes. Please try again.', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Upload theme package
    $('#uploadThemePackage').on('click', function() {
        const fileInput = document.getElementById('themePackage');
        const file = fileInput.files[0];
        
        if (!file) {
            showNotification('Please select a theme package file', 'error');
            return;
        }
        
        if (file.type !== 'application/zip') {
            showNotification('Please select a valid ZIP file', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('theme_package', file);
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        
        const $btn = $(this);
        const originalText = $btn.text();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Uploading...');
        
        $.ajax({
            url: '/super-admin/themes/upload-package',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('Theme package uploaded successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(response.message || 'Error uploading theme package', 'error');
                }
            },
            error: function() {
                showNotification('Error uploading theme package. Please try again.', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Notification helper
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/super-admin/themes/index.blade.php ENDPATH**/ ?>