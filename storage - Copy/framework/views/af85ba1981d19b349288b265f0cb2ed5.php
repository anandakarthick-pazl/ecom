<?php $__env->startSection('title', 'Shopping Cart - Herbal Bliss'); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h2 class="mb-4">Shopping Cart</h2>
    
    <?php if($cartItems->count() > 0): ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cart-item border-bottom py-3" data-product-id="<?php echo e($item->product_id); ?>">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <?php if($item->product->featured_image): ?>
                                    <img src="<?php echo e(Storage::url($item->product->featured_image)); ?>" class="img-fluid rounded" alt="<?php echo e($item->product->name); ?>">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4">
                                <h6 class="mb-1"><?php echo e($item->product->name); ?></h6>
                                <small class="text-muted"><?php echo e($item->product->category->name); ?></small>
                                <?php if($item->product->weight): ?>
                                    <br><small class="text-muted"><?php echo e($item->product->weight); ?> <?php echo e($item->product->weight_unit); ?></small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-2 text-center">
                                <strong>₹<?php echo e(number_format($item->price, 2)); ?></strong>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(<?php echo e($item->product_id); ?>, <?php echo e($item->quantity - 1); ?>)">-</button>
                                    <input type="number" class="form-control text-center quantity-input" value="<?php echo e($item->quantity); ?>" min="1" max="<?php echo e($item->product->stock); ?>" data-product-id="<?php echo e($item->product_id); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(<?php echo e($item->product_id); ?>, <?php echo e($item->quantity + 1); ?>)">+</button>
                                </div>
                                <small class="text-muted">Max: <?php echo e($item->product->stock); ?></small>
                            </div>
                            
                            <div class="col-md-1 text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo e($item->product_id); ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <?php if($item->product->tax_percentage > 0): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Tax: <?php echo e($item->product->tax_percentage); ?>% 
                                        (CGST: ₹<?php echo e(number_format($item->product->getCgstAmount($item->price) * $item->quantity, 2)); ?> + 
                                        SGST: ₹<?php echo e(number_format($item->product->getSgstAmount($item->price) * $item->quantity, 2)); ?>)
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Subtotal: ₹<span class="item-total"><?php echo e(number_format($item->total, 2)); ?></span></strong>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <div class="mt-3">
                <button class="btn btn-outline-secondary" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear Cart
                </button>
                <a href="<?php echo e(route('shop')); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">₹<?php echo e(number_format($subtotal, 2)); ?></span>
                    </div>
                    
                    <?php
                        // Calculate tax amounts
                        $totalTax = 0;
                        $cgstAmount = 0;
                        $sgstAmount = 0;
                        
                        foreach($cartItems as $item) {
                            $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                            $totalTax += $itemTax;
                            $cgstAmount += ($itemTax / 2);
                            $sgstAmount += ($itemTax / 2);
                        }
                        
                        $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                        $grandTotal = $subtotal + $totalTax + $deliveryCharge;
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST:</span>
                        <span id="cgst-amount">₹<?php echo e(number_format($cgstAmount, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST:</span>
                        <span id="sgst-amount">₹<?php echo e(number_format($sgstAmount, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Tax:</span>
                        <span id="total-tax">₹<?php echo e(number_format($totalTax, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery:</span>
                        <span id="delivery-charge">
                            <?php if($subtotal >= 500): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                ₹50.00
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="cart-total">
                            ₹<?php echo e(number_format($grandTotal, 2)); ?>

                        </strong>
                    </div>
                    
                    <?php if($subtotal < 500): ?>
                        <div class="alert alert-info py-2">
                            <small>Add ₹<?php echo e(number_format(500 - $subtotal, 2)); ?> more for FREE delivery!</small>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('checkout')); ?>" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
        <h4>Your cart is empty</h4>
        <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
        <a href="<?php echo e(route('shop')); ?>" class="btn btn-primary">
            <i class="fas fa-leaf"></i> Start Shopping
        </a>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function updateCartQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    $.ajax({
        url: '<?php echo e(route("cart.update")); ?>',
        method: 'PUT',
        data: {
            product_id: productId,
            quantity: quantity,
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(response) {
            if(response.success) {
                location.reload(); // Reload to update cart display
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('Something went wrong!', 'error');
        }
    });
}

function removeFromCart(productId) {
    if(confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: '<?php echo e(route("cart.remove")); ?>',
            method: 'DELETE',
            data: {
                product_id: productId,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                    updateCartCount();
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                showToast('Something went wrong!', 'error');
            }
        });
    }
}

function clearCart() {
    if(confirm('Are you sure you want to clear your entire cart?')) {
        $.ajax({
            url: '<?php echo e(route("cart.clear")); ?>',
            method: 'DELETE',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                    updateCartCount();
                    showToast(response.message, 'success');
                }
            },
            error: function() {
                showToast('Something went wrong!', 'error');
            }
        });
    }
}

// Handle quantity input changes
$(document).on('change', '.quantity-input', function() {
    const productId = $(this).data('product-id');
    const quantity = parseInt($(this).val());
    updateCartQuantity(productId, quantity);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/cart.blade.php ENDPATH**/ ?>