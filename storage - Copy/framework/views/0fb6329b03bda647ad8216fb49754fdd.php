<?php $__env->startSection('title', 'Order: ' . $order->order_number); ?>
<?php $__env->startSection('page_title', 'Order: ' . $order->order_number); ?>

<?php $__env->startSection('page_actions'); ?>
<div class="btn-group">
    <a href="<?php echo e(route('admin.orders.invoice', $order)); ?>" class="btn btn-secondary">
        <i class="fas fa-print"></i> Print Invoice
    </a>
    <?php if($order->customer_email): ?>
        <form action="<?php echo e(route('admin.orders.send-invoice', $order)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-success" 
                    onclick="return confirm('Send invoice PDF to <?php echo e($order->customer_email); ?>?')">
                <i class="fas fa-paper-plane"></i> Send Invoice
            </button>
        </form>
    <?php endif; ?>
    
    <a href="<?php echo e(route('admin.orders.download-bill', $order)); ?>" class="btn btn-info">
        <i class="fas fa-download"></i> Download Bill
    </a>
    
    <?php if($order->customer_mobile): ?>
        <button type="button" class="btn btn-whatsapp" id="whatsapp-bill-btn" 
                data-order-id="<?php echo e($order->id); ?>" 
                data-customer-phone="<?php echo e($order->customer_mobile); ?>">
            <i class="fab fa-whatsapp"></i> Send Bill via WhatsApp
        </button>
    <?php endif; ?>
    <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <!-- Order Details -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Order Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Details</h6>
                        <p class="mb-1"><strong>Name:</strong> <?php echo e($order->customer_name); ?></p>
                        <p class="mb-1"><strong>Mobile:</strong> <?php echo e($order->customer_mobile); ?></p>
                        <?php if($order->customer_email): ?>
                            <p class="mb-1">
                                <strong>Email:</strong> 
                                <span class="text-success">
                                    <i class="fas fa-envelope"></i> <?php echo e($order->customer_email); ?>

                                </span>
                            </p>
                        <?php else: ?>
                            <p class="mb-1">
                                <strong>Email:</strong> 
                                <span class="text-muted">
                                    <i class="fas fa-times-circle"></i> Not provided
                                </span>
                            </p>
                        <?php endif; ?>
                        <p class="mb-3"><strong>Customer ID:</strong> #<?php echo e($order->customer_id); ?></p>
                        
                        <h6>Delivery Address</h6>
                        <address>
                            <?php echo e($order->delivery_address); ?><br>
                            <?php echo e($order->city); ?><?php if($order->state): ?>, <?php echo e($order->state); ?><?php endif; ?> <?php echo e($order->pincode); ?>

                        </address>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Order Details</h6>
                        <p class="mb-1"><strong>Order Number:</strong> <?php echo e($order->order_number); ?></p>
                        <p class="mb-1"><strong>Order Date:</strong> <?php echo e($order->created_at->format('M d, Y h:i A')); ?></p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-<?php echo e($order->status_color); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                        </p>
                        
                        <?php if($order->shipped_at): ?>
                            <p class="mb-1"><strong>Shipped At:</strong> <?php echo e($order->shipped_at->format('M d, Y h:i A')); ?></p>
                        <?php endif; ?>
                        
                        <?php if($order->delivered_at): ?>
                            <p class="mb-1"><strong>Delivered At:</strong> <?php echo e($order->delivered_at->format('M d, Y h:i A')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($order->notes): ?>
                <hr>
                <h6>Customer Notes</h6>
                <p class="text-muted"><?php echo e($order->notes); ?></p>
                <?php endif; ?>
                
                <?php if($order->admin_notes): ?>
                <hr>
                <h6>Admin Notes</h6>
                <p class="text-muted"><?php echo e($order->admin_notes); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Payment Information</h6>
                <?php
                    $paymentStatusColor = match($order->payment_status) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'refunded' => 'secondary',
                        default => 'secondary'
                    };
                ?>
                <span class="badge bg-<?php echo e($paymentStatusColor); ?> fs-6">
                    <?php if($order->payment_status === 'paid'): ?>
                        <i class="fas fa-check-circle"></i> Payment Success
                    <?php elseif($order->payment_status === 'failed'): ?>
                        <i class="fas fa-times-circle"></i> Payment Failed
                    <?php elseif($order->payment_status === 'processing'): ?>
                        <i class="fas fa-clock"></i> Payment Processing
                    <?php elseif($order->payment_status === 'refunded'): ?>
                        <i class="fas fa-undo"></i> Payment Refunded
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle"></i> Payment Pending
                    <?php endif; ?>
                </span>
            </div>
            <div class="card-body">
                <?php
                    $paymentIcon = match($order->payment_method) {
                        'razorpay' => 'fas fa-credit-card',
                        'cod' => 'fas fa-money-bill-wave',
                        'bank_transfer' => 'fas fa-university',
                        'upi' => 'fas fa-mobile-alt',
                        'gpay' => 'fab fa-google-pay',
                        default => 'fas fa-wallet'
                    };
                    
                    $paymentMethodName = match($order->payment_method) {
                        'razorpay' => 'Online Payment (Razorpay)',
                        'cod' => 'Cash on Delivery',
                        'bank_transfer' => 'Bank Transfer',
                        'upi' => 'UPI Payment',
                        'gpay' => 'Google Pay (G Pay)',
                        default => ucfirst(str_replace('_', ' ', $order->payment_method))
                    };
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payment Method</h6>
                        <p class="mb-2">
                            <i class="<?php echo e($paymentIcon); ?> text-primary"></i> 
                            <strong><?php echo e($paymentMethodName); ?></strong>
                        </p>
                        
                        <h6>Payment Status</h6>
                        <p class="mb-2">
                            <span class="badge bg-<?php echo e($paymentStatusColor); ?> p-2">
                                <?php echo e(ucfirst(str_replace('_', ' ', $order->payment_status))); ?>

                            </span>
                        </p>
                        
                        <?php if($order->payment_transaction_id): ?>
                            <h6>Transaction ID</h6>
                            <p class="mb-2">
                                <code><?php echo e($order->payment_transaction_id); ?></code>
                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('<?php echo e($order->payment_transaction_id); ?>')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <?php if($order->payment_details): ?>
                            <?php
                                $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
                            ?>
                            
                            <h6>Payment Details</h6>
                            <div class="payment-details">
                                <?php if(isset($details['razorpay_payment_id'])): ?>
                                    <p class="mb-1">
                                        <strong>Razorpay Payment ID:</strong> 
                                        <code><?php echo e($details['razorpay_payment_id']); ?></code>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['razorpay_order_id'])): ?>
                                    <p class="mb-1">
                                        <strong>Razorpay Order ID:</strong> 
                                        <code><?php echo e($details['razorpay_order_id']); ?></code>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['payment_method'])): ?>
                                    <p class="mb-1">
                                        <strong>Payment Method:</strong> 
                                        <?php echo e(ucfirst($details['payment_method'])); ?>

                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['bank'])): ?>
                                    <p class="mb-1">
                                        <strong>Bank:</strong> 
                                        <?php echo e($details['bank']); ?>

                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['wallet'])): ?>
                                    <p class="mb-1">
                                        <strong>Wallet:</strong> 
                                        <?php echo e($details['wallet']); ?>

                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['verified_at'])): ?>
                                    <p class="mb-1">
                                        <strong>Verified At:</strong> 
                                        <?php echo e(\Carbon\Carbon::parse($details['verified_at'])->format('M d, Y h:i A')); ?>

                                    </p>
                                <?php endif; ?>
                                
                                <?php if(isset($details['error']) && $order->payment_status === 'failed'): ?>
                                    <div class="alert alert-danger mt-2">
                                        <strong>Error:</strong> <?php echo e($details['error']); ?>

                                        <?php if(isset($details['error_details'])): ?>
                                            <br><small><?php echo e($details['error_details']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Order Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Tax %</th>
                                <th>Tax Amount</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($item->product && $item->product->featured_image): ?>
                                            <img src="<?php echo e(Storage::url($item->product->featured_image)); ?>" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo e($item->product_name); ?></strong>
                                            <?php if($item->product): ?>
                                                <br><small class="text-muted"><?php echo e($item->product->category->name ?? ''); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>₹<?php echo e(number_format($item->price, 2)); ?></td>
                                <td><?php echo e($item->quantity); ?></td>
                                <td><?php echo e($item->tax_percentage); ?>%</td>
                                <td>₹<?php echo e(number_format($item->tax_amount, 2)); ?></td>
                                <td><strong>₹<?php echo e(number_format($item->total, 2)); ?></strong></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                <td><strong>₹<?php echo e(number_format($order->subtotal, 2)); ?></strong></td>
                            </tr>
                            <?php if($order->discount > 0): ?>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                                <td><strong class="text-success">-₹<?php echo e(number_format($order->discount, 2)); ?></strong></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="5" class="text-end"><strong>CGST:</strong></td>
                                <td><strong>₹<?php echo e(number_format($order->cgst_amount, 2)); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>SGST:</strong></td>
                                <td><strong>₹<?php echo e(number_format($order->sgst_amount, 2)); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Delivery Charge:</strong></td>
                                <td><strong>
                                    <?php if($order->delivery_charge == 0): ?>
                                        <span class="text-success">FREE</span>
                                    <?php else: ?>
                                        ₹<?php echo e(number_format($order->delivery_charge, 2)); ?>

                                    <?php endif; ?>
                                </strong></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                <td><strong>₹<?php echo e(number_format($order->total, 2)); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Order Status Update -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Update Order Status</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?php echo e($order->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="processing" <?php echo e($order->status == 'processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="shipped" <?php echo e($order->status == 'shipped' ? 'selected' : ''); ?>>Shipped</option>
                            <option value="delivered" <?php echo e($order->status == 'delivered' ? 'selected' : ''); ?>>Delivered</option>
                            <option value="cancelled" <?php echo e($order->status == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Add internal notes..."><?php echo e($order->admin_notes); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment Status Update -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Update Payment Status</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.orders.update-payment-status', $order)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            <option value="pending" <?php echo e($order->payment_status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="processing" <?php echo e($order->payment_status == 'processing' ? 'selected' : ''); ?>>Processing</option>
                            <option value="paid" <?php echo e($order->payment_status == 'paid' ? 'selected' : ''); ?>>Paid</option>
                            <option value="failed" <?php echo e($order->payment_status == 'failed' ? 'selected' : ''); ?>>Failed</option>
                            <option value="refunded" <?php echo e($order->payment_status == 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Payment Notes</label>
                        <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" placeholder="Add payment-related notes..."></textarea>
                        <small class="text-muted">These notes will be added to admin notes with timestamp</small>
                    </div>
                    
                    <?php if($order->payment_status !== 'paid'): ?>
                        <div class="alert alert-info p-2 mb-3">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Marking as "Paid" will automatically update order status to "Processing" if currently pending.
                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-credit-card"></i> Update Payment Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Payment Summary</h6>
            </div>
            <div class="card-body text-center">
                <?php if($order->payment_status === 'paid'): ?>
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Payment Successful</h5>
                    <p class="text-muted">₹<?php echo e(number_format($order->total, 2)); ?> received</p>
                <?php elseif($order->payment_status === 'failed'): ?>
                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Payment Failed</h5>
                    <p class="text-muted">Amount: ₹<?php echo e(number_format($order->total, 2)); ?></p>
                <?php elseif($order->payment_status === 'processing'): ?>
                    <i class="fas fa-clock fa-3x text-info mb-3"></i>
                    <h5 class="text-info">Payment Processing</h5>
                    <p class="text-muted">Amount: ₹<?php echo e(number_format($order->total, 2)); ?></p>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Payment Pending</h5>
                    <p class="text-muted">Amount: ₹<?php echo e(number_format($order->total, 2)); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Order Timeline -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Order Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Placed</h6>
                            <p class="timeline-description"><?php echo e($order->created_at->format('M d, Y h:i A')); ?></p>
                        </div>
                    </div>
                    
                    <?php if($order->payment_status === 'paid'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Payment Received</h6>
                            <p class="timeline-description">Payment successful</p>
                        </div>
                    </div>
                    <?php elseif($order->payment_status === 'failed'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Payment Failed</h6>
                            <p class="timeline-description">Payment could not be processed</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($order->status != 'pending'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Processing Started</h6>
                            <p class="timeline-description">Order is being processed</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($order->shipped_at): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Shipped</h6>
                            <p class="timeline-description"><?php echo e($order->shipped_at->format('M d, Y h:i A')); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($order->delivered_at): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Delivered</h6>
                            <p class="timeline-description"><?php echo e($order->delivered_at->format('M d, Y h:i A')); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($order->status == 'cancelled'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Cancelled</h6>
                            <p class="timeline-description">Order has been cancelled</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Customer Info -->
        <?php if($order->customer): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Customer Summary</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Total Orders:</strong> <?php echo e($order->customer->total_orders); ?></p>
                <p class="mb-2"><strong>Total Spent:</strong> ₹<?php echo e(number_format($order->customer->total_spent, 2)); ?></p>
                <p class="mb-0"><strong>Customer Since:</strong> <?php echo e($order->customer->created_at->format('M Y')); ?></p>
                
                <hr>
                
                <a href="<?php echo e(route('admin.customers.show', $order->customer)); ?>" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-user"></i> View Customer Details
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
// Debug: Check if jQuery is loaded
console.log('Order Page - jQuery loaded:', typeof $ !== 'undefined');
console.log('Order Page - Document ready starting');

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Transaction ID copied to clipboard', 'success');
    });
}

// WhatsApp Status Check
let whatsappStatus = { configured: false, enabled: false };
  
// Check WhatsApp status on page load
$(document).ready(function() {
    console.log('Order Page - Document ready, calling checkWhatsAppStatus');
    checkWhatsAppStatus();
});

function checkWhatsAppStatus() {
    console.log('checkWhatsAppStatus() called');
    console.log('Making request to:', '<?php echo e(route("admin.orders.whatsapp-status")); ?>');
    
    $.get('<?php echo e(route("admin.orders.whatsapp-status")); ?>')
        .done(function(response) {
            console.log('WhatsApp status response:', response);
            whatsappStatus = response;
            updateWhatsAppButton();
        })
        .fail(function(xhr, status, error) {
            console.log('WhatsApp status check failed:', xhr, status, error);
            console.log('Response text:', xhr.responseText);
            $('#whatsapp-bill-btn').prop('disabled', true)
                .attr('title', 'WhatsApp status check failed: ' + error)
                .html('<i class="fab fa-whatsapp"></i> WhatsApp Check Failed');
        });
}

function updateWhatsAppButton() {
    console.log('updateWhatsAppButton() called with status:', whatsappStatus);
    const btn = $('#whatsapp-bill-btn');
    
    if (btn.length === 0) {
        console.log('WhatsApp button not found in DOM');
        return;
    }
    
    console.log('Button found, updating state...');
    
    if (!whatsappStatus.configured) {
        console.log('WhatsApp not configured');
        btn.prop('disabled', true)
            .attr('title', 'WhatsApp is not configured for this company')
            .html('<i class="fab fa-whatsapp"></i> WhatsApp Not Configured');
    } else if (!whatsappStatus.enabled) {
        console.log('WhatsApp disabled');
        btn.prop('disabled', true)
            .attr('title', 'WhatsApp is disabled for this company')
            .html('<i class="fab fa-whatsapp"></i> WhatsApp Disabled');
    } else {
        console.log('WhatsApp enabled and configured');
        btn.prop('disabled', false)
            .attr('title', 'Send bill via WhatsApp to ' + btn.data('customer-phone'))
            .html('<i class="fab fa-whatsapp"></i> Send Bill via WhatsApp');
    }
}

// WhatsApp Bill Send Function
$('#whatsapp-bill-btn').on('click', function() {
    console.log('WhatsApp bill button clicked');
    
    if (!whatsappStatus.configured || !whatsappStatus.enabled) {
        console.log('WhatsApp not properly configured or enabled');
        showToast('WhatsApp is not properly configured or enabled', 'error');
        return;
    }
    
    const orderId = $(this).data('order-id');
    const customerPhone = $(this).data('customer-phone');
    
    console.log('Order ID:', orderId);
    console.log('Customer Phone:', customerPhone);
    
    // Show custom message modal
    showWhatsAppMessageModal(orderId, customerPhone);
});

function showWhatsAppMessageModal(orderId, customerPhone) {
    console.log('showWhatsAppMessageModal called');
    const modal = `
        <div class="modal fade" id="whatsappMessageModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fab fa-whatsapp text-success"></i> Send Bill via WhatsApp
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Sending to:</label>
                            <div class="alert alert-info">
                                <i class="fas fa-phone"></i> ${customerPhone}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="whatsapp-message" class="form-label">Custom Message (Optional)</label>
                            <textarea class="form-control" id="whatsapp-message" rows="4" 
                                      placeholder="Leave empty to use default message template..."></textarea>
                            <small class="text-muted">The bill PDF will be automatically attached to the message.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-whatsapp" onclick="sendWhatsAppBill(${orderId})">
                            <i class="fab fa-whatsapp"></i> Send Bill
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    $('#whatsappMessageModal').remove();
    
    // Add modal to body and show
    $('body').append(modal);
    $('#whatsappMessageModal').modal('show');
    console.log('Modal should now be visible');
}

function sendWhatsAppBill(orderId) {
    console.log('sendWhatsAppBill called for order:', orderId);
    const message = $('#whatsapp-message').val();
    const btn = $('#whatsappMessageModal .btn-whatsapp');
    
    console.log('Custom message:', message);
    
    // Show loading state
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
    
    const url = `<?php echo e(route('admin.orders.send-whatsapp-bill', ':id')); ?>`.replace(':id', orderId);
    console.log('Sending AJAX request to:', url);
    
    // Send request
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            message: message,
            _token: '<?php echo e(csrf_token()); ?>'
        },
        beforeSend: function() {
            console.log('AJAX request started');
        },
        success: function(response) {
            console.log('AJAX success response:', response);
            if (response.success) {
                showToast('Bill sent successfully via WhatsApp!', 'success');
                $('#whatsappMessageModal').modal('hide');
                
                // Show success details
                setTimeout(function() {
                    showToast(`Message sent to ${response.sent_to}`, 'info');
                }, 1000);
            } else {
                showToast(response.message || 'Failed to send WhatsApp message', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX error:', xhr, status, error);
            console.log('Response text:', xhr.responseText);
            let errorMessage = 'Failed to send WhatsApp message';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    console.log('Could not parse error response');
                }
            }
            showToast(errorMessage, 'error');
        },
        complete: function() {
            console.log('AJAX request completed');
            btn.prop('disabled', false).html('<i class="fab fa-whatsapp"></i> Send Bill');
        }
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    console.log('showToast called:', message, type);
    const toastClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const toast = `
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    const toastElement = $('.toast').last();
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        toastElement.remove();
    }, 5000);
    
    // Also show browser alert as fallback for errors
    if (type === 'error') {
        alert('Error: ' + message);
    }
}

// Test WhatsApp button click with simple alert
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - vanilla JS');
    
    const whatsappBtn = document.getElementById('whatsapp-bill-btn');
    if (whatsappBtn) {
        console.log('WhatsApp button found via vanilla JS');
        whatsappBtn.addEventListener('click', function() {
            console.log('WhatsApp button clicked via vanilla JS');
            // Uncomment the line below to test basic button functionality
            // alert('WhatsApp button works!');
        });
    } else {
        console.log('WhatsApp button NOT found via vanilla JS');
    }
});
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-title {
    font-size: 14px;
    margin-bottom: 5px;
}

.timeline-description {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 0;
}

.payment-details code {
    color: #0d6efd;
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
}

/* Payment Status Update Form Styling */
.card:has(form[action*="payment-status"]) {
    border-left: 4px solid #ffc107;
    background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
}

.card:has(form[action*="payment-status"]) .card-header {
    background: linear-gradient(135deg, #ffc107 0%, #ffb700 100%);
    color: white;
    border-bottom: 1px solid #ffb700;
}

.card:has(form[action*="payment-status"]) .btn-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ffb700 100%);
    border: none;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
    transition: all 0.3s ease;
}

.card:has(form[action*="payment-status"]) .btn-warning:hover {
    background: linear-gradient(135deg, #ffb700 0%, #ffa000 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.4);
}

/* Payment status badge styling */
.badge.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
}

.badge.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%) !important;
}

/* WhatsApp Button Styling */
.btn-whatsapp {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
    color: white;
    border: none;
    box-shadow: 0 2px 4px rgba(37, 211, 102, 0.3);
    transition: all 0.3s ease;
}

.btn-whatsapp:hover {
    background: linear-gradient(135deg, #128c7e 0%, #075e54 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(37, 211, 102, 0.4);
}

.btn-whatsapp:disabled {
    background: #6c757d;
    color: #fff;
    transform: none;
    box-shadow: none;
}

.btn-whatsapp:disabled:hover {
    background: #6c757d;
    color: #fff;
    transform: none;
    box-shadow: none;
}

/* Modal styling for WhatsApp */
#whatsappMessageModal .modal-header {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
    color: white;
    border-bottom: none;
}

#whatsappMessageModal .btn-close {
    filter: brightness(0) invert(1);
}

/* Toast styling */
.toast {
    border-radius: 10px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>