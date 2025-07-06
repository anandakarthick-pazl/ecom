<?php $__env->startSection('title', 'Checkout - Herbal Bliss'); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <h2 class="mb-4">Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkoutForm">
                <?php echo csrf_field(); ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="customer_name" name="customer_name" value="<?php echo e(old('customer_name')); ?>" required>
                                <?php $__errorArgs = ['customer_name'];
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
                            
                            <div class="col-md-6 mb-3">
                                <label for="customer_mobile" class="form-label">WhatsApp Mobile Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fab fa-whatsapp"></i> +91
                                    </span>
                                    <input type="tel" class="form-control <?php $__errorArgs = ['customer_mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="customer_mobile" name="customer_mobile" value="<?php echo e(old('customer_mobile')); ?>" 
                                           pattern="[0-9]{10}" maxlength="10" required 
                                           placeholder="9003096885">
                                    <?php $__errorArgs = ['customer_mobile'];
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
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Enter 10-digit mobile number (auto-saved as +91 prefix for WhatsApp)
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">
                                Email Address (Optional)
                                <i class="fas fa-info-circle text-primary" 
                                   data-bs-toggle="tooltip" 
                                   title="Enter email to receive order updates and invoice PDF"></i>
                            </label>
                            <input type="email" class="form-control <?php $__errorArgs = ['customer_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="customer_email" name="customer_email" value="<?php echo e(old('customer_email')); ?>" 
                                   placeholder="your@email.com">
                            <?php $__errorArgs = ['customer_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="text-muted">
                                <i class="fas fa-envelope"></i> Receive order updates and invoice PDF via email
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Delivery Address *</label>
                            <textarea class="form-control <?php $__errorArgs = ['delivery_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="delivery_address" name="delivery_address" rows="3" required><?php echo e(old('delivery_address')); ?></textarea>
                            <?php $__errorArgs = ['delivery_address'];
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
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="city" name="city" value="<?php echo e(old('city')); ?>" required>
                                <?php $__errorArgs = ['city'];
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
                            
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="state" name="state" value="<?php echo e(old('state')); ?>">
                                <?php $__errorArgs = ['state'];
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
                            
                            <div class="col-md-4 mb-3">
                                <label for="pincode" class="form-label">PIN Code *</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['pincode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="pincode" name="pincode" value="<?php echo e(old('pincode')); ?>" 
                                       pattern="[0-9]{6}" maxlength="6" required>
                                <?php $__errorArgs = ['pincode'];
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
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="notes" name="notes" rows="2" 
                                      placeholder="Any special instructions for delivery..."><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
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
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <?php if($paymentMethods->count() > 0): ?>
                            <div class="payment-methods">
                                <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check payment-method-option mb-3 p-3 border rounded">
                                        <input class="form-check-input" type="radio" 
                                               name="payment_method" 
                                               id="payment_<?php echo e($method->id); ?>" 
                                               value="<?php echo e($method->id); ?>"
                                               data-extra-charge="<?php echo e($method->extra_charge); ?>"
                                               data-extra-percentage="<?php echo e($method->extra_charge_percentage); ?>"
                                               <?php echo e(old('payment_method', $loop->first ? $method->id : '') == $method->id ? 'checked' : ''); ?>

                                               required>
                                        <label class="form-check-label w-100" for="payment_<?php echo e($method->id); ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>
                                                        <i class="<?php echo e($method->getIcon()); ?> text-<?php echo e($method->getColor()); ?>"></i>
                                                        <?php echo e($method->display_name); ?>

                                                    </strong>
                                                    <?php if($method->description): ?>
                                                        <br><small class="text-muted"><?php echo e($method->description); ?></small>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($method->type === 'bank_transfer' && $method->bank_details): ?>
                                                        <div class="bank-details mt-2 small text-muted" style="display: none;">
                                                            <strong>Bank Details:</strong><br>
                                                            Bank: <?php echo e($method->bank_details['bank_name'] ?? ''); ?><br>
                                                            Account: <?php echo e($method->bank_details['account_number'] ?? ''); ?><br>
                                                            IFSC: <?php echo e($method->bank_details['ifsc_code'] ?? ''); ?><br>
                                                            Name: <?php echo e($method->bank_details['account_name'] ?? ''); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($method->type === 'upi'): ?>
                                                        <div class="upi-details mt-2 small" style="display: none;">
                                                            <?php if($method->upi_id): ?>
                                                                <strong>UPI ID:</strong> <?php echo e($method->upi_id); ?><br>
                                                            <?php endif; ?>
                                                            <?php if($method->upi_qr_code): ?>
                                                                <img src="<?php echo e(Storage::url($method->upi_qr_code)); ?>" 
                                                                     alt="UPI QR Code" 
                                                                     class="img-fluid mt-2" 
                                                                     style="max-width: 150px;">
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($method->type === 'gpay'): ?>
                                                        <div class="gpay-details mt-2 small" style="display: none;">
                                                            <?php if($method->upi_id): ?>
                                                                <strong>Google Pay UPI ID:</strong> <?php echo e($method->upi_id); ?><br>
                                                            <?php endif; ?>
                                                            <?php if($method->upi_qr_code): ?>
                                                                <div class="text-center mt-2">
                                                                    <img src="<?php echo e(Storage::url($method->upi_qr_code)); ?>" 
                                                                         alt="Google Pay QR Code" 
                                                                         class="img-fluid border rounded" 
                                                                         style="max-width: 200px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                                    <br>
                                                                    <small class="text-muted mt-1 d-block">
                                                                        <i class="fab fa-google-pay text-danger"></i> 
                                                                        Scan with Google Pay to pay
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-end">
                                                    <?php if($method->extra_charge > 0 || $method->extra_charge_percentage > 0): ?>
                                                        <small class="text-muted">
                                                            <?php if($method->extra_charge > 0): ?>
                                                                +₹<?php echo e(number_format($method->extra_charge, 2)); ?>

                                                            <?php endif; ?>
                                                            <?php if($method->extra_charge_percentage > 0): ?>
                                                                <?php if($method->extra_charge > 0): ?> + <?php endif; ?>
                                                                <?php echo e($method->extra_charge_percentage); ?>%
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No payment methods available. Please contact support.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?php echo e($item->product->name); ?></h6>
                            <small class="text-muted">Qty: <?php echo e($item->quantity); ?> × ₹<?php echo e(number_format($item->price, 2)); ?></small>
                            <?php if($item->product->tax_percentage > 0): ?>
                                <br><small class="text-muted">GST: <?php echo e($item->product->tax_percentage); ?>% = ₹<?php echo e(number_format($item->product->getTaxAmount($item->price) * $item->quantity, 2)); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <strong>₹<?php echo e(number_format($item->total, 2)); ?></strong>
                            <?php if($item->product->tax_percentage > 0): ?>
                                <br><small class="text-muted">+Tax</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>₹<?php echo e(number_format($subtotal, 2)); ?></span>
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
                        
                        $grandTotal = $subtotal + $totalTax + $deliveryCharge - $discount;
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>CGST:</span>
                        <span>₹<?php echo e(number_format($cgstAmount, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>SGST:</span>
                        <span>₹<?php echo e(number_format($sgstAmount, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Tax:</span>
                        <span>₹<?php echo e(number_format($totalTax, 2)); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Charge:</span>
                        <span>
                            <?php if($deliveryCharge == 0): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                ₹<?php echo e(number_format($deliveryCharge, 2)); ?>

                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <?php if($deliveryInfo['enabled'] && $deliveryInfo['free_delivery_enabled'] && $deliveryInfo['amount_needed_for_free'] > 0): ?>
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-gift"></i> 
                            Add ₹<?php echo e(number_format($deliveryInfo['amount_needed_for_free'], 2)); ?> more for <strong>FREE delivery!</strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <span class="text-success">-₹<?php echo e(number_format($discount, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-2" id="payment-charge-row" style="display: none;">
                        <span>Payment Charge:</span>
                        <span id="payment-charge">+₹0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="grand-total">₹<?php echo e(number_format($grandTotal, 2)); ?></strong>
                    </div>
                    
                    <?php if($deliveryCharge == 0 && $subtotal >= 500): ?>
                        <div class="alert alert-success py-2">
                            <small><i class="fas fa-check"></i> You're getting FREE delivery!</small>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($deliveryInfo['enabled'] && $deliveryInfo['time_estimate']): ?>
                        <div class="alert alert-light py-2">
                            <small>
                                <i class="fas fa-clock text-primary"></i> 
                                <strong>Estimated Delivery:</strong> <?php echo e($deliveryInfo['time_estimate']); ?>

                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($deliveryInfo['enabled'] && $deliveryInfo['description']): ?>
                        <div class="alert alert-light py-2">
                            <small>
                                <i class="fas fa-info-circle text-info"></i> 
                                <?php echo e($deliveryInfo['description']); ?>

                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" form="checkoutForm" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-lock"></i> Place Order
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> Secure checkout with 256-bit SSL encryption
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <h6>Need Help?</h6>
                    <p class="mb-2">
                        <i class="fas fa-phone text-primary"></i> +91 9876543210
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope text-primary"></i> support@herbalbliss.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
/* Google Pay QR Code Display Styles */
.gpay-details {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dc3545;
    border-radius: 12px;
    padding: 15px;
    margin-top: 10px;
    animation: fadeIn 0.3s ease-in;
}

.gpay-details img {
    transition: transform 0.3s ease;
}

.gpay-details img:hover {
    transform: scale(1.05);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Payment method selection enhancement */
.payment-method-option {
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-method-option:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.payment-method-option input:checked + label {
    background-color: #e3f2fd;
    border-color: #2196f3;
}

/* WhatsApp Mobile Number Input Enhancement */
.input-group .input-group-text.bg-success {
    border-color: #28a745;
    font-weight: 600;
}

.input-group .form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.67-.17C3.14 6.55 3 6.11 3 6c0-.55-.45-1-1-1s-1 .45-1 1c0 .14.11.2.23.2C1.48 6.2 1.8 6.47 2.3 6.73z'/%3e%3c/svg%3e");
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Initialize tooltips
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize payment method selection
    updatePaymentCharge();
    
    // Show details for initially selected payment method
    const initiallySelected = $('input[name="payment_method"]:checked');
    if (initiallySelected.length > 0) {
        $('.bank-details, .upi-details, .gpay-details').hide();
        const selectedMethod = initiallySelected.closest('.payment-method-option');
        selectedMethod.find('.bank-details, .upi-details, .gpay-details').show();
    }
});

// Store base total
const baseTotal = <?php echo e($grandTotal); ?>;

// Handle payment method change
$('input[name="payment_method"]').on('change', function() {
    updatePaymentCharge();
    
    // Show/hide bank details, UPI details, and Google Pay details
    $('.bank-details, .upi-details, .gpay-details').hide();
    const selectedMethod = $(this).closest('.payment-method-option');
    selectedMethod.find('.bank-details, .upi-details, .gpay-details').show();
});

// Update payment charge
function updatePaymentCharge() {
    const selectedMethod = $('input[name="payment_method"]:checked');
    
    if (selectedMethod.length > 0) {
        const extraCharge = parseFloat(selectedMethod.data('extra-charge')) || 0;
        const extraPercentage = parseFloat(selectedMethod.data('extra-percentage')) || 0;
        
        let paymentCharge = extraCharge + (baseTotal * extraPercentage / 100);
        
        if (paymentCharge > 0) {
            $('#payment-charge-row').show();
            $('#payment-charge').text('+₹' + paymentCharge.toFixed(2));
        } else {
            $('#payment-charge-row').hide();
        }
        
        const grandTotal = baseTotal + paymentCharge;
        $('#grand-total').text('₹' + grandTotal.toFixed(2));
    }
}

// Auto-format mobile number (Indian format with +91)
$('#customer_mobile').on('input', function() {
    let value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    this.value = value;
    
    // Visual feedback for user
    if (value.length === 10) {
        $(this).removeClass('is-invalid').addClass('is-valid');
        $(this).next('.invalid-feedback').hide();
    } else {
        $(this).removeClass('is-valid');
    }
});

// Auto-format PIN code
$('#pincode').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});

// Form validation and mobile number formatting
$('#checkoutForm').on('submit', function(e) {
    const mobile = $('#customer_mobile').val();
    const pincode = $('#pincode').val();
    
    if (mobile.length !== 10) {
        e.preventDefault();
        showToast('Please enter a valid 10-digit mobile number', 'error');
        $('#customer_mobile').focus();
        return false;
    }
    
    if (pincode.length !== 6) {
        e.preventDefault();
        showToast('Please enter a valid 6-digit PIN code', 'error');
        $('#pincode').focus();
        return false;
    }
    
    // Check if payment method is selected
    if ($('input[name="payment_method"]:checked').length === 0) {
        e.preventDefault();
        showToast('Please select a payment method', 'error');
        return false;
    }
    
    // AUTO-ADD +91 TO MOBILE NUMBER FOR WHATSAPP
    const mobileWithCountryCode = '+91' + mobile;
    
    // Create a hidden field to send the formatted mobile number
    if ($('#formatted_mobile').length === 0) {
        $('<input>').attr({
            type: 'hidden',
            id: 'formatted_mobile',
            name: 'formatted_mobile',
            value: mobileWithCountryCode
        }).appendTo(this);
    } else {
        $('#formatted_mobile').val(mobileWithCountryCode);
    }
    
    console.log('Mobile number formatted for WhatsApp:', mobileWithCountryCode);
    
    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing Order...');
    
    // Prevent multiple submissions
    $(this).off('submit');
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\source_code\herbal-ecom\resources\views/checkout.blade.php ENDPATH**/ ?>