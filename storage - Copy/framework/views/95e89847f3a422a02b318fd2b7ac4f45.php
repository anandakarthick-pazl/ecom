<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-primary" style="background: blue !important;">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white"><?php echo e($totalOrders); ?></h3>
                    <p class="mb-0 text-white">Total Orders</p>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">₹<?php echo e(number_format($totalRevenue, 0)); ?></h3>
                    <p class="mb-0 text-white">Total Revenue</p>
                </div>
                <div>
                    <i class="fas fa-rupee-sign fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white"><?php echo e($totalCustomers); ?></h3>
                    <p class="mb-0 text-white">Total Customers</p>
                </div>
                <div>
                    <i class="fas fa-users fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white"><?php echo e($totalProducts); ?></h3>
                    <p class="mb-0 text-white">Total Products</p>
                </div>
                <div>
                    <i class="fas fa-box fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Today's Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo e($todayOrders); ?></h4>
                            <p class="text-muted mb-0">Orders Today</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">₹<?php echo e(number_format($todayRevenue, 0)); ?></h4>
                        <p class="text-muted mb-0">Revenue Today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">This Month's Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo e($monthlyOrders); ?></h4>
                            <p class="text-muted mb-0">Orders This Month</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">₹<?php echo e(number_format($monthlyRevenue, 0)); ?></h4>
                        <p class="text-muted mb-0">Revenue This Month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Orders</h5>
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if($recentOrders->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="text-decoration-none">
                                        <?php echo e($order->order_number); ?>

                                    </a>
                                </td>
                                <td><?php echo e($order->customer_name); ?></td>
                                <td>₹<?php echo e(number_format($order->total, 2)); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($order->status_color); ?>">
                                        <?php echo e(ucfirst($order->status)); ?>

                                    </span>
                                </td>
                                <td><?php echo e($order->created_at->format('M d, Y')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">No orders yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Low Stock Alert</h5>
                <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-sm btn-outline-warning">Manage</a>
            </div>
            <div class="card-body">
                <?php if($lowStockProducts->count() > 0): ?>
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><?php echo e(Str::limit($product->name, 20)); ?></h6>
                            <small class="text-muted"><?php echo e($product->category->name); ?></small>
                        </div>
                        <span class="badge bg-<?php echo e($product->stock == 0 ? 'danger' : 'warning'); ?>">
                            <?php echo e($product->stock); ?> left
                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-3">All products are well stocked!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Chart -->
<?php if($orderStatusStats->count() > 0): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Status Overview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $orderStatusStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-2 col-sm-4 text-center mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-<?php echo e($status == 'pending' ? 'warning' : 
                                ($status == 'processing' ? 'info' : 
                                ($status == 'shipped' ? 'primary' : 
                                ($status == 'delivered' ? 'success' : 'danger')))); ?>"><?php echo e($count); ?></h4>
                            <p class="text-muted mb-0"><?php echo e(ucfirst($status)); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>