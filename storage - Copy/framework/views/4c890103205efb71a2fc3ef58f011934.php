<?php $__env->startSection('title', 'Track Your Order - Herbal Bliss'); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Track Your Order</h4>
                </div>
                <div class="card-body">
                    <?php if(!isset($orders)): ?>
                    <form method="POST" action="<?php echo e(route('track.order')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number *</label>
                            <input type="tel" class="form-control <?php $__errorArgs = ['mobile_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="mobile_number" name="mobile_number" value="<?php echo e(old('mobile_number')); ?>" 
                                   pattern="[0-9]{10}" maxlength="10" required placeholder="Enter your 10-digit mobile number">
                            <?php $__errorArgs = ['mobile_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_number" class="form-label">Order Number (Optional)</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['order_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="order_number" name="order_number" value="<?php echo e(old('order_number')); ?>" 
                                   placeholder="Enter order number for specific order">
                            <?php $__errorArgs = ['order_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track Order
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="mb-4">
                        <h6>Showing orders for: <strong><?php echo e(request('mobile_number')); ?></strong></h6>
                        <a href="<?php echo e(route('track.order')); ?>" class="btn btn-sm btn-outline-secondary">Track Different Number</a>
                    </div>

                    <?php if($orders->count() > 0): ?>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="order-card border rounded mb-4 p-3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-2">Order #<?php echo e($order->order_number); ?></h6>
                                    <p class="mb-1"><strong>Customer:</strong> <?php echo e($order->customer_name); ?></p>
                                    <p class="mb-1"><strong>Total:</strong> ₹<?php echo e(number_format($order->total, 2)); ?></p>
                                    <p class="mb-1"><strong>Order Date:</strong> <?php echo e($order->created_at->format('M d, Y h:i A')); ?></p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-<?php echo e($order->status_color); ?> mb-2"><?php echo e(ucfirst($order->status)); ?></span>
                                    <?php if($order->shipped_at): ?>
                                        <p class="mb-1"><small>Shipped: <?php echo e($order->shipped_at->format('M d, Y')); ?></small></p>
                                    <?php endif; ?>
                                    <?php if($order->delivered_at): ?>
                                        <p class="mb-1"><small>Delivered: <?php echo e($order->delivered_at->format('M d, Y')); ?></small></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Order Progress -->
                            <div class="order-progress mt-3">
                                <div class="progress mb-2" style="height: 8px;">
                                    <?php
                                        $progress = match($order->status) {
                                            'pending' => 25,
                                            'processing' => 50,
                                            'shipped' => 75,
                                            'delivered' => 100,
                                            'cancelled' => 0,
                                            default => 0
                                        };
                                    ?>
                                    <div class="progress-bar bg-<?php echo e($order->status === 'cancelled' ? 'danger' : 'success'); ?>" 
                                         style="width: <?php echo e($progress); ?>%"></div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <small class="text-<?php echo e($progress >= 25 ? 'success' : 'muted'); ?>">
                                            <i class="fas fa-check-circle"></i> Ordered
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-<?php echo e($progress >= 50 ? 'success' : 'muted'); ?>">
                                            <i class="fas fa-cogs"></i> Processing
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-<?php echo e($progress >= 75 ? 'success' : 'muted'); ?>">
                                            <i class="fas fa-truck"></i> Shipped
                                        </small>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-<?php echo e($progress >= 100 ? 'success' : 'muted'); ?>">
                                            <i class="fas fa-home"></i> Delivered
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="order-items mt-3">
                                <h6>Order Items:</h6>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo e($item->product_name); ?></strong>
                                        <br><small class="text-muted">Qty: <?php echo e($item->quantity); ?> × ₹<?php echo e(number_format($item->price, 2)); ?></small>
                                    </div>
                                    <div>
                                        <strong>₹<?php echo e(number_format($item->total, 2)); ?></strong>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            
                            <!-- Delivery Address -->
                            <div class="delivery-address mt-3">
                                <h6>Delivery Address:</h6>
                                <p class="mb-0"><?php echo e($order->delivery_address); ?>, <?php echo e($order->city); ?>, <?php echo e($order->state); ?> <?php echo e($order->pincode); ?></p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>No orders found</h5>
                            <p class="text-muted">We couldn't find any orders for this mobile number.</p>
                            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ROBUST jQuery waiting for Track Order Page
console.log('=== TRACK ORDER PAGE JQUERY CHECK ===');
console.log('Track Order Page - jQuery loaded:', typeof $ !== 'undefined');

// Function to wait for jQuery (same as layout)
function waitForTrackJQuery(callback, maxRetries = 50, currentRetry = 0) {
    if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
        console.log('✅ Track Order Page - jQuery loaded successfully on attempt:', currentRetry + 1);
        callback();
    } else if (currentRetry < maxRetries) {
        console.log('⏳ Track Order Page - Waiting for jQuery... attempt:', currentRetry + 1);
        setTimeout(() => {
            waitForTrackJQuery(callback, maxRetries, currentRetry + 1);
        }, 100);
    } else {
        console.error('❌ Track Order Page - FAILED: jQuery could not load after', maxRetries, 'attempts');
        console.error('Track order functionality will be limited.');
    }
}

// Initialize track order functionality only when jQuery is ready
waitForTrackJQuery(function() {
    console.log('🚀 Track Order Page - Initializing functionality...');
    
    // Auto-format mobile number
    $('#mobile_number').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });
    
    console.log('✅ Track order functionality initialized successfully!');
    console.log('===============================================');
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/track-order.blade.php ENDPATH**/ ?>