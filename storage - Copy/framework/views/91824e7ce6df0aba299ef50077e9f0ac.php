<?php $__env->startSection('title', 'Orders'); ?>
<?php $__env->startSection('page_title', 'Orders'); ?>

<?php $__env->startSection('page_actions'); ?>
<a href="<?php echo e(route('admin.orders.export')); ?>" class="btn btn-success">
    <i class="fas fa-download"></i> Export CSV
</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search orders...">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
                        <option value="shipped" <?php echo e(request('status') == 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                        <option value="delivered" <?php echo e(request('status') == 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="payment_status">
                        <option value="">All Payments</option>
                        <option value="pending" <?php echo e(request('payment_status') == 'pending' ? 'selected' : ''); ?>>Payment Pending</option>
                        <option value="processing" <?php echo e(request('payment_status') == 'processing' ? 'selected' : ''); ?>>Payment Processing</option>
                        <option value="paid" <?php echo e(request('payment_status') == 'paid' ? 'selected' : ''); ?>>Payment Success</option>
                        <option value="failed" <?php echo e(request('payment_status') == 'failed' ? 'selected' : ''); ?>>Payment Failed</option>
                        <option value="refunded" <?php echo e(request('payment_status') == 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="<?php echo e(request('date_from')); ?>" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="<?php echo e(request('date_to')); ?>" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if($orders->count() > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong><?php echo e($order->order_number); ?></strong>
                        </td>
                        <td>
                            <div>
                                <strong><?php echo e($order->customer_name); ?></strong>
                                <br><small class="text-muted"><?php echo e($order->customer_mobile); ?></small>
                                <?php if($order->customer_email): ?>
                                    <br><small class="text-success"><i class="fas fa-envelope"></i> <?php echo e($order->customer_email); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info"><?php echo e($order->items->count()); ?> items</span>
                        </td>
                        <td>
                            <strong>₹<?php echo e(number_format($order->total, 2)); ?></strong>
                        </td>
                        <td>
                            <div class="payment-info">
                                <?php
                                    $paymentIcon = match($order->payment_method) {
                                        'razorpay' => 'fas fa-credit-card',
                                        'cod' => 'fas fa-money-bill-wave',
                                        'bank_transfer' => 'fas fa-university',
                                        'upi' => 'fas fa-mobile-alt',
                                        default => 'fas fa-wallet'
                                    };
                                    
                                    $paymentStatusColor = match($order->payment_status) {
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'refunded' => 'secondary',
                                        default => 'secondary'
                                    };
                                    
                                    $paymentStatusText = match($order->payment_status) {
                                        'paid' => 'Success',
                                        'failed' => 'Failed',
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'refunded' => 'Refunded',
                                        default => 'Unknown'
                                    };
                                ?>
                                
                                <div class="d-flex align-items-center mb-1">
                                    <i class="<?php echo e($paymentIcon); ?> text-primary me-1"></i>
                                    <small><?php echo e(ucfirst(str_replace('_', ' ', $order->payment_method))); ?></small>
                                </div>
                                
                                <span class="badge bg-<?php echo e($paymentStatusColor); ?> d-flex align-items-center">
                                    <?php if($order->payment_status === 'paid'): ?>
                                        <i class="fas fa-check-circle me-1"></i>
                                    <?php elseif($order->payment_status === 'failed'): ?>
                                        <i class="fas fa-times-circle me-1"></i>
                                    <?php elseif($order->payment_status === 'processing'): ?>
                                        <i class="fas fa-clock me-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                    <?php endif; ?>
                                    <?php echo e($paymentStatusText); ?>

                                </span>
                                
                                <?php if($order->payment_status === 'failed' && $order->payment_details): ?>
                                    <?php
                                        $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
                                    ?>
                                    <?php if(isset($details['error'])): ?>
                                        <br><small class="text-danger">
                                            <i class="fas fa-info-circle"></i> 
                                            <?php echo e(Str::limit($details['error'], 30)); ?>

                                        </small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($order->status_color); ?>">
                                <?php echo e(ucfirst($order->status)); ?>

                            </span>
                        </td>
                        <td>
                            <?php echo e($order->created_at->format('M d, Y')); ?>

                            <br><small class="text-muted"><?php echo e($order->created_at->format('h:i A')); ?></small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('admin.orders.invoice', $order)); ?>" class="btn btn-outline-secondary" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if($order->customer_email): ?>
                                    <form action="<?php echo e(route('admin.orders.send-invoice', $order)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-success" title="Send Invoice Email"
                                                onclick="return confirm('Send invoice PDF to <?php echo e($order->customer_email); ?>?')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        <div class="pagination-container">
            <div class="pagination-info">
                <p class="small text-muted mb-0">
                    Showing <span class="fw-semibold text-primary"><?php echo e($orders->firstItem()); ?></span>
                    to <span class="fw-semibold text-primary"><?php echo e($orders->lastItem()); ?></span>
                    of <span class="fw-semibold text-primary"><?php echo e($orders->total()); ?></span>
                    orders
                </p>
            </div>
            <div class="pagination-nav">
                <?php echo e($orders->withQueryString()->links()); ?>

            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h5>No orders found</h5>
            <p class="text-muted">Orders will appear here once customers start placing them.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Status Summary Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4><?php echo e(\App\Models\Order::where('payment_status', 'paid')->count()); ?></h4>
                <p class="mb-0">Successful Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4><?php echo e(\App\Models\Order::where('payment_status', 'pending')->count()); ?></h4>
                <p class="mb-0">Pending Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4><?php echo e(\App\Models\Order::where('payment_status', 'failed')->count()); ?></h4>
                <p class="mb-0">Failed Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4><?php echo e(\App\Models\Order::where('payment_status', 'processing')->count()); ?></h4>
                <p class="mb-0">Processing Payments</p>
            </div>
        </div>
    </div>
</div>

<style>
.payment-info {
    min-width: 120px;
}
.badge {
    font-size: 0.7rem;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\ecom\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>