<?php $__env->startSection('title', 'Categories'); ?>
<?php $__env->startSection('page_title', 'Categories'); ?>

<?php $__env->startSection('page_actions'); ?>
<a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Category
</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body">
        <?php if($categories->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent Category</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if($category->image): ?>
                                    <img src="<?php echo e(Storage::url($category->image)); ?>" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="<?php echo e($category->name); ?>">
                                <?php endif; ?>
                                <div>
                                    <strong><?php echo e($category->name); ?></strong>
                                    <br><small class="text-muted"><?php echo e($category->slug); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($category->parent): ?>
                                <span class="badge bg-secondary"><?php echo e($category->parent->name); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Root Category</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info"><?php echo e($category->products_count ?? $category->products->count()); ?></span>
                        </td>
                        <td>
                            <?php if($category->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($category->sort_order); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.categories.show', $category)); ?>" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            <?php echo e($categories->links()); ?>

        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h5>No categories found</h5>
            <p class="text-muted">Start by creating your first category.</p>
            <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Category
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>