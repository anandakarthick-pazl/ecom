<?php $__env->startSection('title', 'Companies Management'); ?>
<?php $__env->startSection('page-title', 'Companies Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>All Companies
                </h5>
                <a href="<?php echo e(route('super-admin.companies.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Company
                </a>
            </div>
            <div class="card-body">
                <?php if($companies->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Logo</th>
                                    <th>Company Name</th>
                                    <th>Domain</th>
                                    <th>Email</th>
                                    <th>Theme</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Trial Ends</th>
                                    <th>Created</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($company->id); ?></td>
                                    <td>
                                        <?php if($company->logo): ?>
                                            <img src="<?php echo e(asset('storage/' . $company->logo)); ?>" 
                                                 class="rounded" width="40" height="40" 
                                                 style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; font-size: 14px;">
                                                <?php echo e(strtoupper(substr($company->name, 0, 2))); ?>

                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo e($company->name); ?></strong>
                                        <?php if($company->phone): ?>
                                            <br><small class="text-muted"><?php echo e($company->phone); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="http://<?php echo e($company->domain); ?>" target="_blank" class="text-decoration-none">
                                            <?php echo e($company->domain); ?>

                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    </td>
                                    <td><?php echo e($company->email); ?></td>
                                    <td>
                                        <?php if($company->theme): ?>
                                            <span class="badge bg-info"><?php echo e($company->theme->name); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Theme</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($company->package): ?>
                                            <span class="badge bg-success"><?php echo e($company->package->name); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Package</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                data-company-id="<?php echo e($company->id); ?>" 
                                                style="width: auto; min-width: 100px;">
                                            <option value="active" <?php echo e($company->status === 'active' ? 'selected' : ''); ?>>
                                                Active
                                            </option>
                                            <option value="inactive" <?php echo e($company->status === 'inactive' ? 'selected' : ''); ?>>
                                                Inactive
                                            </option>
                                            <option value="suspended" <?php echo e($company->status === 'suspended' ? 'selected' : ''); ?>>
                                                Suspended
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if($company->trial_ends_at): ?>
                                            <span class="badge <?php echo e($company->trial_ends_at->isPast() ? 'bg-danger' : 'bg-warning'); ?>">
                                                <?php echo e($company->trial_ends_at->format('M d, Y')); ?>

                                            </span>
                                            <?php if(!$company->trial_ends_at->isPast()): ?>
                                                <br><small class="text-muted">
                                                    <?php echo e($company->trial_ends_at->diffForHumans()); ?>

                                                </small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Trial</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo e($company->created_at->format('M d, Y')); ?>

                                            <br><?php echo e($company->created_at->diffForHumans()); ?>

                                        </small>
                                    </td>
                                    <td class="actions-column">
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('super-admin.companies.show', $company)); ?>" 
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('super-admin.companies.edit', $company)); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($company->trial_ends_at && !$company->trial_ends_at->isPast()): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning extend-trial-btn" 
                                                        data-company-id="<?php echo e($company->id); ?>" title="Extend Trial">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-company-id="<?php echo e($company->id); ?>" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($companies->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Companies Found</h5>
                        <p class="text-muted">Start by adding your first company to the system.</p>
                        <a href="<?php echo e(route('super-admin.companies.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add First Company
                        </a>
                    </div>
                <?php endif; ?>
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
            <form id="extendTrialForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="extend_days" class="form-label">Extend trial by (days):</label>
                        <input type="number" class="form-control" id="extend_days" name="days" 
                               min="1" max="365" value="30" required>
                        <small class="form-text text-muted">Enter number of days to extend the trial period.</small>
                    </div>
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
                <p>Are you sure you want to delete this company? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will also delete all associated users, data, and configurations.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Company</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Status change handler
    $('.status-select').on('change', function() {
        const companyId = $(this).data('company-id');
        const status = $(this).val();
        const selectElement = $(this);
        
        $.ajax({
            url: `/super-admin/companies/${companyId}/status`,
            method: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle me-2"></i>Company status updated successfully!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    $('.content-wrapper').prepend(alert);
                    
                    // Auto-hide after 3 seconds
                    setTimeout(() => alert.fadeOut(), 3000);
                }
            },
            error: function() {
                alert('Error updating company status. Please try again.');
                // Reset to previous value
                selectElement.val(selectElement.data('original-value'));
            }
        });
    });
    
    // Store original values for rollback
    $('.status-select').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    // Extend trial handler
    let currentCompanyId = null;
    $('.extend-trial-btn').on('click', function() {
        currentCompanyId = $(this).data('company-id');
        $('#extendTrialModal').modal('show');
    });
    
    $('#extendTrialForm').on('submit', function(e) {
        e.preventDefault();
        const days = $('#extend_days').val();
        
        $.ajax({
            url: `/super-admin/companies/${currentCompanyId}/extend-trial`,
            method: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                days: days
            },
            success: function(response) {
                if (response.success) {
                    $('#extendTrialModal').modal('hide');
                    location.reload(); // Refresh to show updated trial date
                }
            },
            error: function() {
                alert('Error extending trial period. Please try again.');
            }
        });
    });
    
    // Delete handler
    $('.delete-btn').on('click', function() {
        const companyId = $(this).data('company-id');
        $('#deleteForm').attr('action', `/super-admin/companies/${companyId}`);
        $('#deleteModal').modal('show');
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/super-admin/companies/index.blade.php ENDPATH**/ ?>