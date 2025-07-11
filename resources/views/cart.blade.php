@extends('layouts.app')

@section('title', 'Shopping Cart - Herbal Bliss')

@push('styles')
    <style>
        /* Ensure Order Summary is always visible */
        #order-summary-card {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        #order-summary-body {
            display: block !important;
            visibility: visible !important;
        }

        /* Improve quantity button accessibility */
        .quantity-btn {
            min-width: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-input {
            min-width: 60px;
        }

        /* Add loading state for quantity buttons */
        .quantity-btn.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .quantity-btn.loading::after {
            content: '';
            width: 12px;
            height: 12px;
            border: 2px solid #fff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: absolute;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Ensure cart totals are visible */
        .cart-totals {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .cart-total-row.grand-total {
            font-weight: bold;
            font-size: 1.1em;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        /* Detailed Product Breakdown Styles */
        .product-summary-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px !important;
        }

        .product-name {
            font-size: 0.95rem;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .product-calculation {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .product-subtotal {
            font-size: 0.9rem;
            color: #2c3e50;
            text-align: right;
        }

        #detailed-product-breakdown {
            max-height: 300px;
            overflow-y: auto;
        }

        #detailed-product-breakdown::-webkit-scrollbar {
            width: 4px;
        }

        #detailed-product-breakdown::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        #detailed-product-breakdown::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 2px;
        }

        #detailed-product-breakdown::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@section('content')
    <div class="container my-5">
        <h2 class="mb-4">Shopping Cart</h2>

        @if ($cartItems->count() > 0)
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            @foreach ($cartItems as $item)
                                <div class="cart-item border-bottom py-3" data-product-id="{{ $item->product_id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            @if ($item->product->featured_image)
                                                <img src="{{ $item->product->featured_image_url }}"
                                                    class="img-fluid rounded" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                    style="height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-4">
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <small class="text-muted">{{ $item->product->category->name }}</small>
                                            @if ($item->product->weight)
                                                <br><small class="text-muted">{{ $item->product->weight }}
                                                    {{ $item->product->weight_unit }}</small>
                                            @endif
                                        </div>

                                        <div class="col-md-2 text-center">
                                            <strong>₹{{ number_format($item->price, 2) }}</strong>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary decrement-btn" type="button"
                                                    onclick="decrementQuantity({{ $item->product_id }})"
                                                    data-product-id="{{ $item->product_id }}"
                                                    title="Decrease quantity">-</button>
                                                <input type="number" class="form-control text-center quantity-input"
                                                    value="{{ $item->quantity }}" min="1"
                                                    max="{{ $item->product->stock }}"
                                                    data-product-id="{{ $item->product_id }}" title="Quantity">
                                                <button class="btn btn-outline-secondary increment-btn" type="button"
                                                    onclick="incrementQuantity({{ $item->product_id }})"
                                                    data-product-id="{{ $item->product_id }}"
                                                    title="Increase quantity">+</button>
                                            </div>
                                            <small class="text-muted">Max: {{ $item->product->stock }}</small>
                                        </div>

                                        <div class="col-md-1 text-center">
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="removeFromCart({{ $item->product_id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            @if ($item->product->tax_percentage > 0)
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Tax:
                                                    {{ $item->product->tax_percentage }}%
                                                    (CGST:
                                                    ₹{{ number_format($item->product->getCgstAmount($item->price) * $item->quantity, 2) }}
                                                    +
                                                    SGST:
                                                    ₹{{ number_format($item->product->getSgstAmount($item->price) * $item->quantity, 2) }})
                                                </small>
                                            @endif
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <strong>Subtotal: ₹<span
                                                    class="item-total">{{ number_format($item->total, 2) }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-outline-secondary" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Clear Cart
                        </button>
                        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card" id="order-summary-card" style="display: block !important;">
                        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                        </div>
                        <div class="order-totals" style="
    padding: 10px;
">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span>
                            </div>

                            @php
                                // Calculate tax amounts with error handling
                                $totalTax = 0;
                                $cgstAmount = 0;
                                $sgstAmount = 0;

                                try {
                                    foreach ($cartItems as $item) {
                                        if ($item->product && method_exists($item->product, 'getTaxAmount')) {
                                            $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                                            $totalTax += $itemTax;
                                            $cgstAmount += $itemTax / 2;
                                            $sgstAmount += $itemTax / 2;
                                        }
                                    }
                                } catch (\Exception $e) {
                                    \Log::error('Error calculating tax amounts: ' . $e->getMessage());
                                    // Set default values if calculation fails
                                    $totalTax = 0;
                                    $cgstAmount = 0;
                                    $sgstAmount = 0;
                                }

                                $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                                $grandTotal = $subtotal + $totalTax + $deliveryCharge;
                            @endphp

                            {{-- Debug output (remove in production) --}}
                            @if (config('app.debug'))
                                <!-- Debug: Subtotal = {{ $subtotal }}, Tax = {{ $totalTax }}, Delivery = {{ $deliveryCharge }}, Total = {{ $grandTotal }} -->
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span>CGST:</span>
                                <span id="cgst-amount">₹{{ number_format($cgstAmount, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>SGST:</span>
                                <span id="sgst-amount">₹{{ number_format($sgstAmount, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Tax:</span>
                                <span id="total-tax">₹{{ number_format($totalTax, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Charge:</span>
                                <span id="delivery-charge">
                                    @if ($subtotal >= 500)
                                        <span class="text-success">FREE</span>
                                    @else
                                        ₹50.00
                                    @endif
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Payment Charge:</span>
                                <span id="payment-charge">+₹<span id="payment-charge-amount">0.00</span></span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong id="cart-total">
                                    ₹{{ number_format($grandTotal, 2) }}
                                </strong>
                            </div>
                        </div>
                        <div class="card-body" id="order-summary-body" style="display: block !important;">

                            {{-- Detailed Product Breakdown --}}
                            <div id="detailed-product-breakdown">
                                @foreach ($cartItems as $item)
                                    @php
                                        $itemTaxAmount = $item->product->getTaxAmount($item->price) * $item->quantity;
                                        $itemSubtotal = $item->price * $item->quantity;
                                    @endphp
                                    <div class="product-summary-item mb-3 pb-2 border-bottom"
                                        data-product-id="{{ $item->product_id }}">
                                        <div class="product-name">
                                            <strong>{{ $item->product->name }}</strong>
                                        </div>
                                        <div class="product-calculation text-muted small">
                                            Qty: <span class="item-qty">{{ $item->quantity }}</span> × ₹<span
                                                class="item-price">{{ number_format($item->price, 2) }}</span>
                                            @if ($item->product->tax_percentage > 0)
                                                GST: {{ $item->product->tax_percentage }}% = ₹<span
                                                    class="item-tax">{{ number_format($itemTaxAmount, 2) }}</span>
                                            @endif
                                        </div>
                                        <div class="product-subtotal">
                                            <strong>₹<span
                                                    class="item-subtotal">{{ number_format($itemSubtotal, 2) }}</span></strong>
                                            +Tax
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-3">

                            {{-- Order Totals Section --}}
                            <div class="order-totals">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="cart-subtotal">₹{{ number_format($subtotal, 2) }}</span>
                                </div>

                                @php
                                    // Calculate tax amounts with error handling
                                    $totalTax = 0;
                                    $cgstAmount = 0;
                                    $sgstAmount = 0;

                                    try {
                                        foreach ($cartItems as $item) {
                                            if ($item->product && method_exists($item->product, 'getTaxAmount')) {
                                                $itemTax = $item->product->getTaxAmount($item->price) * $item->quantity;
                                                $totalTax += $itemTax;
                                                $cgstAmount += $itemTax / 2;
                                                $sgstAmount += $itemTax / 2;
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Error calculating tax amounts: ' . $e->getMessage());
                                        // Set default values if calculation fails
                                        $totalTax = 0;
                                        $cgstAmount = 0;
                                        $sgstAmount = 0;
                                    }

                                    $deliveryCharge = $subtotal >= 500 ? 0 : 50;
                                    $grandTotal = $subtotal + $totalTax + $deliveryCharge;
                                @endphp

                                {{-- Debug output (remove in production) --}}
                                @if (config('app.debug'))
                                    <!-- Debug: Subtotal = {{ $subtotal }}, Tax = {{ $totalTax }}, Delivery = {{ $deliveryCharge }}, Total = {{ $grandTotal }} -->
                                @endif

                                <div class="d-flex justify-content-between mb-2">
                                    <span>CGST:</span>
                                    <span id="cgst-amount">₹{{ number_format($cgstAmount, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>SGST:</span>
                                    <span id="sgst-amount">₹{{ number_format($sgstAmount, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Tax:</span>
                                    <span id="total-tax">₹{{ number_format($totalTax, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Delivery Charge:</span>
                                    <span id="delivery-charge">
                                        @if ($subtotal >= 500)
                                            <span class="text-success">FREE</span>
                                        @else
                                            ₹50.00
                                        @endif
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Charge:</span>
                                    <span id="payment-charge">+₹<span id="payment-charge-amount">0.00</span></span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong id="cart-total">
                                        ₹{{ number_format($grandTotal, 2) }}
                                    </strong>
                                </div>
                            </div>

                            @if ($subtotal < 500)
                                <div class="alert alert-info py-2">
                                    <small>Add ₹{{ number_format(500 - $subtotal, 2) }} more for FREE delivery!</small>
                                </div>
                            @endif

                            {{-- Minimum Order Validation Alert --}}
                            @if (isset($minOrderValidationSettings) &&
                                    $minOrderValidationSettings['min_order_validation_enabled'] &&
                                    !$minOrderValidation['valid']
                            )
                                <div class="alert alert-warning py-3 mb-3" id="min-order-alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>{{ $minOrderValidation['message'] }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Current total: ₹{{ number_format($minOrderValidation['current_amount'], 2) }} |
                                        Add ₹{{ number_format($minOrderValidation['shortfall'], 2) }} more
                                    </small>
                                </div>
                            @endif

                            {{-- Proceed to Checkout Button --}}
                            @if (isset($minOrderValidationSettings) &&
                                    $minOrderValidationSettings['min_order_validation_enabled'] &&
                                    !$minOrderValidation['valid']
                            )
                                {{-- Disabled checkout button when minimum order not met --}}
                                <button class="btn btn-secondary btn-lg w-100" disabled id="checkout-btn">
                                    <i class="fas fa-lock me-2"></i>
                                    Minimum Order ₹{{ number_format($minOrderValidationSettings['min_order_amount'], 0) }}
                                </button>
                                <small class="text-muted d-block text-center mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    Add more items to proceed to checkout
                                </small>
                            @else
                                {{-- Enabled checkout button --}}
                                <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100"
                                    id="checkout-btn">
                                    <i class="fas fa-lock me-2"></i> Proceed to Checkout
                                </a>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary">
                    <i class="fas fa-leaf"></i> Start Shopping
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Debug logging for cart functionality
            console.log('Cart page JavaScript loading...');

            // Minimum order validation settings with proper fallbacks
            const minOrderValidationEnabled =
                {{ isset($minOrderValidationSettings) && $minOrderValidationSettings['min_order_validation_enabled'] ? 'true' : 'false' }};
            const minOrderAmount =
                {{ isset($minOrderValidationSettings) && $minOrderValidationSettings['min_order_amount'] ? $minOrderValidationSettings['min_order_amount'] : 0 }};
            const minOrderMessage = @json(isset($minOrderValidationSettings) && $minOrderValidationSettings['min_order_message']
                    ? $minOrderValidationSettings['min_order_message']
                    : 'Minimum order amount not met');

            console.log('Min order validation settings:', {
                enabled: minOrderValidationEnabled,
                amount: minOrderAmount,
                message: minOrderMessage
            });

            function updateCartQuantity(productId, quantity) {
                console.log('updateCartQuantity called:', {
                    productId,
                    quantity
                });

                // Ensure minimum quantity is 1
                if (quantity < 1) {
                    console.log('Quantity less than 1, setting to 1');
                    quantity = 1;
                }

                // Get the current quantity to check if it's actually changing
                const currentQuantity = parseInt($(`[data-product-id="${productId}"] .quantity-input`).val());
                console.log('Current quantity:', currentQuantity, 'New quantity:', quantity);

                if (currentQuantity === quantity) {
                    console.log('No change needed, quantities are the same');
                    return; // No change needed
                }

                // Update the input field immediately for better UX
                $(`[data-product-id="${productId}"] .quantity-input`).val(quantity);
                console.log('Input field updated to:', quantity);

                $.ajax({
                    url: '{{ route('cart.update') }}',
                    method: 'PUT',
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('AJAX response received:', response);

                        // Clear the debounce flag
                        quantityUpdateInProgress[productId] = false;

                        if (response.success) {
                            console.log('Cart update successful');

                            // Update individual item total
                            updateItemTotal(productId, quantity);

                            // Update Order Summary with complete cart data
                            if (response.cart_data) {
                                console.log('Updating Order Summary with cart data:', response.cart_data);
                                updateOrderSummary(response.cart_data);
                                // Also update the detailed breakdown from DOM as fallback
                                updateDetailedBreakdownFromDOM();
                            } else {
                                // Fallback to old method if cart_data not available
                                console.log('Using fallback cart update method');
                                updateCartTotals(response.cart_total);
                                checkMinimumOrder(response.cart_total);
                                updateDetailedBreakdownFromDOM();
                            }

                            updateCartCount();
                            showToast('Cart updated successfully!', 'success');
                        } else {
                            console.log('Cart update failed:', response.message);
                            // Revert the input field if update failed
                            $(`[data-product-id="${productId}"] .quantity-input`).val(currentQuantity);
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', {
                            xhr,
                            status,
                            error
                        });
                        console.error('Response text:', xhr.responseText);

                        // Clear the debounce flag
                        quantityUpdateInProgress[productId] = false;

                        // Revert the input field if update failed
                        $(`[data-product-id="${productId}"] .quantity-input`).val(currentQuantity);
                        showToast('Something went wrong! Please try again.', 'error');
                    }
                });
            }

            // Function to update individual item total
            function updateItemTotal(productId, quantity) {
                const itemRow = $(`[data-product-id="${productId}"]`);
                const priceText = itemRow.find('.col-md-2.text-center strong').text();
                const price = parseFloat(priceText.replace('₹', '').replace(',', ''));
                const itemTotal = price * quantity;

                // Update the item total display
                itemRow.find('.item-total').text(itemTotal.toFixed(2));

                // Update tax amounts for this item if tax is displayed
                const taxInfo = itemRow.find('small:contains("Tax:")');
                if (taxInfo.length) {
                    const taxText = taxInfo.text();
                    const taxMatch = taxText.match(/Tax: ([0-9.]+)%/);
                    if (taxMatch) {
                        const taxPercentage = parseFloat(taxMatch[1]);
                        const cgstAmount = (price * taxPercentage / 200) * quantity; // CGST is half of total tax
                        const sgstAmount = (price * taxPercentage / 200) * quantity; // SGST is half of total tax

                        // Update the tax display
                        const newTaxText = taxInfo.text().replace(
                            /CGST: ₹[0-9,.]+ \+ SGST: ₹[0-9,.]+/,
                            `CGST: ₹${cgstAmount.toFixed(2)} + SGST: ₹${sgstAmount.toFixed(2)}`
                        );
                        taxInfo.html(newTaxText.replace(/Tax: ([0-9.]+)%/,
                            `<i class="fas fa-info-circle"></i> Tax: ${taxPercentage}%`));
                    }
                }
            }

            // Enhanced function to update complete Order Summary with detailed breakdown
            function updateOrderSummary(cartData) {
                console.log('updateOrderSummary called with:', cartData);

                try {
                    // Update detailed product breakdown if cart data includes item details
                    if (cartData.items) {
                        updateDetailedProductBreakdown(cartData.items);
                    }

                    // Update subtotal
                    $('#cart-subtotal').text('₹' + parseFloat(cartData.subtotal).toFixed(2));
                    console.log('Updated subtotal:', cartData.subtotal);

                    // Update tax amounts
                    $('#cgst-amount').text('₹' + parseFloat(cartData.cgst_amount).toFixed(2));
                    $('#sgst-amount').text('₹' + parseFloat(cartData.sgst_amount).toFixed(2));
                    $('#total-tax').text('₹' + parseFloat(cartData.total_tax).toFixed(2));
                    console.log('Updated tax amounts - CGST:', cartData.cgst_amount, 'SGST:', cartData.sgst_amount, 'Total:',
                        cartData.total_tax);

                    // Update delivery charge
                    if (cartData.delivery_charge === 0) {
                        $('#delivery-charge').html('<span class="text-success">FREE</span>');
                    } else {
                        $('#delivery-charge').text('₹' + parseFloat(cartData.delivery_charge).toFixed(2));
                    }
                    console.log('Updated delivery charge:', cartData.delivery_charge);

                    // Update payment charge (if available)
                    const paymentCharge = cartData.payment_charge || 0;
                    $('#payment-charge-amount').text(paymentCharge.toFixed(2));

                    // Update grand total
                    $('#cart-total').text('₹' + parseFloat(cartData.grand_total).toFixed(2));
                    console.log('Updated grand total:', cartData.grand_total);

                    // Update free delivery message
                    const freeDeliveryAlert = $('.alert-info').first();
                    if (cartData.subtotal < 500) {
                        const amountNeeded = 500 - cartData.subtotal;
                        if (freeDeliveryAlert.length) {
                            freeDeliveryAlert.html(`<small>Add ₹${amountNeeded.toFixed(2)} more for FREE delivery!</small>`)
                                .show();
                        }
                    } else {
                        if (freeDeliveryAlert.length) {
                            freeDeliveryAlert.hide();
                        }
                    }

                    // Update minimum order validation
                    if (cartData.min_order_validation && cartData.min_order_settings) {
                        updateMinimumOrderValidation(cartData.min_order_validation, cartData.min_order_settings);
                    }

                    console.log('Order Summary update completed successfully');

                } catch (error) {
                    console.error('Error updating Order Summary:', error);
                    // Fallback to old method
                    updateCartTotals(cartData.subtotal || cartData.grand_total);
                }
            }

            // Function to update detailed product breakdown
            function updateDetailedProductBreakdown(items) {
                console.log('updateDetailedProductBreakdown called with:', items);

                if (!items || !Array.isArray(items)) {
                    console.log('No items data provided, updating from DOM');
                    updateDetailedBreakdownFromDOM();
                    return;
                }

                // Update each product item in the breakdown
                items.forEach(item => {
                    const productBreakdown = $(`.product-summary-item[data-product-id="${item.product_id}"]`);
                    if (productBreakdown.length > 0) {
                        // Update quantity
                        productBreakdown.find('.item-qty').text(item.quantity);

                        // Update price
                        productBreakdown.find('.item-price').text(parseFloat(item.price).toFixed(2));

                        // Update tax amount
                        if (item.tax_amount) {
                            productBreakdown.find('.item-tax').text(parseFloat(item.tax_amount).toFixed(2));
                        }

                        // Update subtotal
                        productBreakdown.find('.item-subtotal').text(parseFloat(item.subtotal).toFixed(2));
                    }
                });
            }

            // Function to update detailed breakdown from DOM (fallback)
            function updateDetailedBreakdownFromDOM() {
                console.log('updateDetailedBreakdownFromDOM called');

                $('.product-summary-item').each(function() {
                    const productId = $(this).data('product-id');
                    const cartItem = $(`.cart-item[data-product-id="${productId}"]`);

                    if (cartItem.length > 0) {
                        const quantity = cartItem.find('.quantity-input').val();
                        const priceText = cartItem.find('.col-md-2.text-center strong').text();
                        const price = parseFloat(priceText.replace('₹', '').replace(',', ''));

                        // Update quantity in breakdown
                        $(this).find('.item-qty').text(quantity);

                        // Calculate and update subtotal
                        const subtotal = price * quantity;
                        $(this).find('.item-subtotal').text(subtotal.toFixed(2));

                        // Update tax if available
                        const taxInfo = cartItem.find('small:contains("Tax:")');
                        if (taxInfo.length) {
                            const taxText = taxInfo.text();
                            const taxMatch = taxText.match(/Tax: ([0-9.]+)%/);
                            if (taxMatch) {
                                const taxPercentage = parseFloat(taxMatch[1]);
                                const taxAmount = (price * taxPercentage / 100) * quantity;
                                $(this).find('.item-tax').text(taxAmount.toFixed(2));
                            }
                        }
                    }
                });
            }

            // Enhanced minimum order validation function
            function updateMinimumOrderValidation(validation, settings) {
                console.log('updateMinimumOrderValidation called:', {
                    validation,
                    settings
                });

                const checkoutBtnContainer = $('#checkout-btn').parent();
                const minOrderAlert = $('#min-order-alert');

                if (!validation.valid) {
                    const shortfall = validation.shortfall || 0;
                    const currentAmount = validation.current_amount || 0;
                    const message = validation.message || settings.min_order_message || 'Minimum order amount not met';

                    // Show/update minimum order alert
                    if (minOrderAlert.length > 0) {
                        minOrderAlert.html(`
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>${message}</strong>
                <br>
                <small class="text-muted">
                    Current total: ₹${currentAmount.toFixed(2)} | 
                    Add ₹${shortfall.toFixed(2)} more
                </small>
            `).show();
                    } else {
                        // Create the alert if it doesn't exist
                        const alertHtml = `
                <div class="alert alert-warning py-3 mb-3" id="min-order-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${message}</strong>
                    <br>
                    <small class="text-muted">
                        Current total: ₹${currentAmount.toFixed(2)} | 
                        Add ₹${shortfall.toFixed(2)} more
                    </small>
                </div>
            `;
                        checkoutBtnContainer.before(alertHtml);
                    }

                    // Replace with disabled button
                    $('#checkout-btn').replaceWith(`
            <button class="btn btn-secondary btn-lg w-100" disabled id="checkout-btn">
                <i class="fas fa-lock me-2"></i> 
                Minimum Order ₹${settings.min_order_amount ? settings.min_order_amount.toFixed(0) : '0'}
            </button>
        `);

                    // Add help text if not exists
                    if (!$('.text-muted:contains("Add more items")').length) {
                        $('#checkout-btn').after(`
                <small class="text-muted d-block text-center mt-2">
                    <i class="fas fa-info-circle"></i> 
                    Add more items to proceed to checkout
                </small>
            `);
                    }
                } else {
                    // Hide minimum order alert
                    minOrderAlert.hide();

                    // Replace with enabled button
                    $('#checkout-btn').replaceWith(`
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100" id="checkout-btn">
                <i class="fas fa-lock me-2"></i> Proceed to Checkout
            </a>
        `);

                    // Remove help text
                    $('.text-muted:contains("Add more items")').remove();
                }
            }

            // Function to update cart totals in the UI (Legacy support)
            function updateCartTotals(newSubtotal) {
                console.log('updateCartTotals (legacy) called with subtotal:', newSubtotal);

                // Update subtotal
                $('#cart-subtotal').text('₹' + parseFloat(newSubtotal).toFixed(2));

                // Calculate and update delivery charge
                const deliveryCharge = newSubtotal >= 500 ? 0 : 50;
                if (deliveryCharge === 0) {
                    $('#delivery-charge').html('<span class="text-success">FREE</span>');
                } else {
                    $('#delivery-charge').text('₹50.00');
                }

                // Update free delivery message
                const freeDeliveryAlert = $('.alert-info').first();
                if (newSubtotal < 500) {
                    const amountNeeded = 500 - newSubtotal;
                    if (freeDeliveryAlert.length) {
                        freeDeliveryAlert.html(`<small>Add ₹${amountNeeded.toFixed(2)} more for FREE delivery!</small>`).show();
                    }
                } else {
                    if (freeDeliveryAlert.length) {
                        freeDeliveryAlert.hide();
                    }
                }

                // Recalculate tax amounts based on current cart items
                let totalTax = 0;
                let cgstAmount = 0;
                let sgstAmount = 0;

                $('.cart-item').each(function() {
                    const productId = $(this).data('product-id');
                    const quantity = $(this).find('.quantity-input').val();
                    const priceText = $(this).find('.col-md-2.text-center strong').text();
                    const price = parseFloat(priceText.replace('₹', '').replace(',', ''));

                    // Get tax percentage from the item's tax display (if available)
                    const taxInfo = $(this).find('small:contains("Tax:")');
                    if (taxInfo.length) {
                        const taxText = taxInfo.text();
                        const taxMatch = taxText.match(/Tax: ([0-9.]+)%/);
                        if (taxMatch) {
                            const taxPercentage = parseFloat(taxMatch[1]);
                            const itemTax = (price * taxPercentage / 100) * quantity;
                            totalTax += itemTax;
                            cgstAmount += (itemTax / 2);
                            sgstAmount += (itemTax / 2);
                        }
                    }
                });

                // Update tax amounts
                $('#cgst-amount').text('₹' + cgstAmount.toFixed(2));
                $('#sgst-amount').text('₹' + sgstAmount.toFixed(2));
                $('#total-tax').text('₹' + totalTax.toFixed(2));

                // Calculate and update grand total
                const grandTotal = newSubtotal + totalTax + deliveryCharge;
                $('#cart-total').text('₹' + grandTotal.toFixed(2));

                console.log('Legacy cart totals updated successfully');
            }

            // Function to check minimum order amount and update UI (Legacy support)
            function checkMinimumOrder(cartTotal) {
                console.log('checkMinimumOrder (legacy) called with total:', cartTotal);

                if (!minOrderValidationEnabled) {
                    console.log('Minimum order validation is disabled');
                    return;
                }

                const checkoutBtnContainer = $('#checkout-btn').parent();
                const minOrderAlert = $('#min-order-alert');

                if (cartTotal < minOrderAmount) {
                    const shortfall = minOrderAmount - cartTotal;

                    // Show/update minimum order alert
                    if (minOrderAlert.length > 0) {
                        minOrderAlert.html(`
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>${minOrderMessage}</strong>
                <br>
                <small class="text-muted">
                    Current total: ₹${cartTotal.toFixed(2)} | 
                    Add ₹${shortfall.toFixed(2)} more
                </small>
            `).show();
                    } else {
                        // Create the alert if it doesn't exist
                        const alertHtml = `
                <div class="alert alert-warning py-3 mb-3" id="min-order-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${minOrderMessage}</strong>
                    <br>
                    <small class="text-muted">
                        Current total: ₹${cartTotal.toFixed(2)} | 
                        Add ₹${shortfall.toFixed(2)} more
                    </small>
                </div>
            `;
                        checkoutBtnContainer.before(alertHtml);
                    }

                    // Replace with disabled button
                    $('#checkout-btn').replaceWith(`
            <button class="btn btn-secondary btn-lg w-100" disabled id="checkout-btn">
                <i class="fas fa-lock me-2"></i> 
                Minimum Order ₹${minOrderAmount.toFixed(0)}
            </button>
        `);

                    // Add help text if not exists
                    if (!$('.text-muted:contains("Add more items")').length) {
                        $('#checkout-btn').after(`
                <small class="text-muted d-block text-center mt-2">
                    <i class="fas fa-info-circle"></i> 
                    Add more items to proceed to checkout
                </small>
            `);
                    }
                } else {
                    // Hide minimum order alert
                    minOrderAlert.hide();

                    // Replace with enabled button
                    $('#checkout-btn').replaceWith(`
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100" id="checkout-btn">
                <i class="fas fa-lock me-2"></i> Proceed to Checkout
            </a>
        `);

                    // Remove help text
                    $('.text-muted:contains("Add more items")').remove();
                }

                console.log('Legacy minimum order check completed');
            }

            function removeFromCart(productId) {
                if (confirm('Are you sure you want to remove this item from cart?')) {
                    $.ajax({
                        url: '{{ route('cart.remove') }}',
                        method: 'DELETE',
                        data: {
                            product_id: productId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Remove the cart item from DOM
                                $(`[data-product-id="${productId}"]`).fadeOut(300, function() {
                                    $(this).remove();

                                    // Check if cart is empty
                                    if ($('.cart-item').length === 0) {
                                        location.reload(); // Reload to show empty cart message
                                    } else {
                                        // Update Order Summary with server data if available
                                        if (response.cart_data) {
                                            updateOrderSummary(response.cart_data);
                                        } else {
                                            // Fallback to legacy method
                                            updateCartTotals(response.cart_total);
                                            checkMinimumOrder(response.cart_total);
                                        }

                                        // Remove the corresponding item from detailed breakdown
                                        $(`.product-summary-item[data-product-id="${productId}"]`).fadeOut(
                                            300,
                                            function() {
                                                $(this).remove();
                                            });
                                    }
                                });

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
                if (confirm('Are you sure you want to clear your entire cart?')) {
                    $.ajax({
                        url: '{{ route('cart.clear') }}',
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
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

            // Function to update cart totals in the UI
            function updateCartTotals(newSubtotal) {
                // Update subtotal
                $('#cart-subtotal').text('₹' + parseFloat(newSubtotal).toFixed(2));

                // Calculate and update delivery charge
                const deliveryCharge = newSubtotal >= 500 ? 0 : 50;
                if (deliveryCharge === 0) {
                    $('#delivery-charge').html('<span class="text-success">FREE</span>');
                } else {
                    $('#delivery-charge').text('₹50.00');
                }

                // Update free delivery message
                const freeDeliveryAlert = $('.alert-info').first();
                if (newSubtotal < 500) {
                    const amountNeeded = 500 - newSubtotal;
                    if (freeDeliveryAlert.length) {
                        freeDeliveryAlert.html(`<small>Add ₹${amountNeeded.toFixed(2)} more for FREE delivery!</small>`).show();
                    }
                } else {
                    if (freeDeliveryAlert.length) {
                        freeDeliveryAlert.hide();
                    }
                }

                // Recalculate tax amounts based on current cart items
                let totalTax = 0;
                let cgstAmount = 0;
                let sgstAmount = 0;

                $('.cart-item').each(function() {
                    const productId = $(this).data('product-id');
                    const quantity = $(this).find('.quantity-input').val();
                    const priceText = $(this).find('.col-md-2.text-center strong').text();
                    const price = parseFloat(priceText.replace('₹', '').replace(',', ''));

                    // Get tax percentage from the item's tax display (if available)
                    const taxInfo = $(this).find('small:contains("Tax:")');
                    if (taxInfo.length) {
                        const taxText = taxInfo.text();
                        const taxMatch = taxText.match(/Tax: ([0-9.]+)%/);
                        if (taxMatch) {
                            const taxPercentage = parseFloat(taxMatch[1]);
                            const itemTax = (price * taxPercentage / 100) * quantity;
                            totalTax += itemTax;
                            cgstAmount += (itemTax / 2);
                            sgstAmount += (itemTax / 2);
                        }
                    }
                });

                // Update tax amounts
                $('#cgst-amount').text('₹' + cgstAmount.toFixed(2));
                $('#sgst-amount').text('₹' + sgstAmount.toFixed(2));
                $('#total-tax').text('₹' + totalTax.toFixed(2));

                // Calculate and update grand total
                const grandTotal = newSubtotal + totalTax + deliveryCharge;
                $('#cart-total').text('₹' + grandTotal.toFixed(2));
            }

            // Function to check minimum order amount and update UI
            function checkMinimumOrder(cartTotal) {
                if (!minOrderValidationEnabled) {
                    return;
                }

                const checkoutBtnContainer = $('#checkout-btn').parent();
                const minOrderAlert = $('#min-order-alert');

                if (cartTotal < minOrderAmount) {
                    const shortfall = minOrderAmount - cartTotal;

                    // Show/update minimum order alert
                    if (minOrderAlert.length > 0) {
                        minOrderAlert.html(`
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>${minOrderMessage}</strong>
                <br>
                <small class="text-muted">
                    Current total: ₹${cartTotal.toFixed(2)} | 
                    Add ₹${shortfall.toFixed(2)} more
                </small>
            `).show();
                    } else {
                        // Create the alert if it doesn't exist
                        const alertHtml = `
                <div class="alert alert-warning py-3 mb-3" id="min-order-alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${minOrderMessage}</strong>
                    <br>
                    <small class="text-muted">
                        Current total: ₹${cartTotal.toFixed(2)} | 
                        Add ₹${shortfall.toFixed(2)} more
                    </small>
                </div>
            `;
                        checkoutBtnContainer.before(alertHtml);
                    }

                    // Replace with disabled button
                    checkoutBtnContainer.html(`
            <button class="btn btn-secondary btn-lg w-100" disabled id="checkout-btn">
                <i class="fas fa-lock me-2"></i> 
                Minimum Order ₹${minOrderAmount.toFixed(0)}
            </button>
            <small class="text-muted d-block text-center mt-2">
                <i class="fas fa-info-circle"></i> 
                Add more items to proceed to checkout
            </small>
        `);
                } else {
                    // Hide minimum order alert
                    minOrderAlert.hide();

                    // Replace with enabled button
                    checkoutBtnContainer.html(`
            <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg w-100" id="checkout-btn">
                <i class="fas fa-lock me-2"></i> Proceed to Checkout
            </a>
        `);
                }
            }

            // Handle quantity input changes
            $(document).on('change', '.quantity-input', function() {
                const productId = $(this).data('product-id');
                let quantity = parseInt($(this).val());

                // Validate quantity
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    $(this).val(quantity);
                }

                const maxQuantity = parseInt($(this).attr('max'));
                if (quantity > maxQuantity) {
                    quantity = maxQuantity;
                    $(this).val(quantity);
                    showToast(`Maximum available quantity is ${maxQuantity}`, 'warning');
                }

                updateCartQuantity(productId, quantity);
            });

            // Debounce mechanism to prevent rapid clicking
            let quantityUpdateInProgress = {};

            // Handle plus/minus button clicks
            window.incrementQuantity = function(productId) {
                // Prevent rapid clicking
                if (quantityUpdateInProgress[productId]) {
                    console.log('Update already in progress for product:', productId);
                    return;
                }

                const input = $(`[data-product-id="${productId}"] .quantity-input`);
                const currentValue = parseInt(input.val());
                const maxValue = parseInt(input.attr('max'));

                if (currentValue < maxValue) {
                    quantityUpdateInProgress[productId] = true;
                    updateCartQuantity(productId, currentValue + 1);
                } else {
                    showToast(`Maximum available quantity is ${maxValue}`, 'warning');
                }
            };

            window.decrementQuantity = function(productId) {
                console.log('decrementQuantity called for product ID:', productId);

                // Prevent rapid clicking
                if (quantityUpdateInProgress[productId]) {
                    console.log('Update already in progress for product:', productId);
                    return;
                }

                const input = $(`[data-product-id="${productId}"] .quantity-input`);
                console.log('Input element found:', input.length > 0);

                if (input.length === 0) {
                    console.error('Quantity input not found for product ID:', productId);
                    showToast('Error: Could not find quantity input', 'error');
                    return;
                }

                const currentValue = parseInt(input.val());
                console.log('Current value before decrement:', currentValue);

                if (isNaN(currentValue)) {
                    console.error('Current value is not a number:', input.val());
                    input.val(1);
                    return;
                }

                if (currentValue > 1) {
                    const newValue = currentValue - 1;
                    console.log('Decrementing to:', newValue);
                    quantityUpdateInProgress[productId] = true;
                    updateCartQuantity(productId, newValue);
                } else {
                    console.log('Cannot decrement below 1');
                    showToast('Minimum quantity is 1. Use the remove button to delete the item.', 'info');
                }
            };

            // Initialize on page load
            $(document).ready(function() {
                console.log('Document ready, initializing cart functionality...');

                // Ensure Order Summary is visible and properly initialized
                const orderSummaryCard = $('#order-summary-card');
                const orderSummaryBody = $('#order-summary-body');

                if (orderSummaryCard.length > 0) {
                    console.log('Order Summary card found - ensuring visibility');
                    orderSummaryCard.show().css({
                        'display': 'block !important',
                        'visibility': 'visible !important',
                        'opacity': '1 !important'
                    });

                    if (orderSummaryBody.length > 0) {
                        orderSummaryBody.show().css({
                            'display': 'block !important',
                            'visibility': 'visible !important'
                        });
                        console.log('Order Summary body made visible');
                    }
                } else {
                    console.error('Order Summary card not found! Checking for fallback...');
                    // Fallback: look for any card containing "Order Summary"
                    const fallbackCard = $('.card-header:contains("Order Summary")').closest('.card');
                    if (fallbackCard.length > 0) {
                        console.log('Found Order Summary via fallback method');
                        fallbackCard.show();
                    }
                }

                // Verify that all quantity buttons are properly initialized
                $('.cart-item').each(function() {
                    const productId = $(this).data('product-id');
                    const minusBtn = $(this).find('button:contains("-")');
                    const plusBtn = $(this).find('button:contains("+")');

                    console.log('Product ID:', productId, 'Minus button found:', minusBtn.length,
                        'Plus button found:', plusBtn.length);

                    // Verify buttons have onclick handlers (they should be in HTML already)
                    if (minusBtn.length > 0) {
                        const onclickAttr = minusBtn.attr('onclick');
                        if (!onclickAttr) {
                            console.warn('Missing onclick attribute for minus button on product:', productId);
                        }
                    }

                    if (plusBtn.length > 0) {
                        const onclickAttr = plusBtn.attr('onclick');
                        if (!onclickAttr) {
                            console.warn('Missing onclick attribute for plus button on product:', productId);
                        }
                    }
                });

                // Initial check for minimum order on page load
                if (minOrderValidationEnabled) {
                    const currentSubtotal = parseFloat('{{ $subtotal }}');
                    console.log('Checking minimum order validation for subtotal:', currentSubtotal);
                    checkMinimumOrder(currentSubtotal);
                }

                // Add loading states to quantity buttons (removed duplicate event listeners)
                $('.cart-item').each(function() {
                    const productId = $(this).data('product-id');

                    // Add hover effects to quantity buttons
                    $(this).find('.btn-outline-secondary').hover(
                        function() {
                            $(this).addClass('btn-secondary').removeClass('btn-outline-secondary');
                        },
                        function() {
                            $(this).addClass('btn-outline-secondary').removeClass('btn-secondary');
                        }
                    );

                    // Note: Removed fallback event listeners to prevent double-firing
                    // The onclick attributes in HTML are working correctly
                    console.log('Initialized quantity buttons for product:', productId);
                });

                // Prevent form submission on Enter key in quantity inputs
                $('.quantity-input').on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        e.preventDefault();
                        $(this).blur(); // Trigger change event
                    }
                });
            });

            // Enhanced toast function with different types and better error handling
            if (typeof window.showToast === 'undefined') {
                window.showToast = function(message, type = 'success') {
                    console.log('showToast called:', {
                        message,
                        type
                    });

                    const toastId = 'toast-' + Date.now();
                    let iconClass, bgClass;

                    switch (type) {
                        case 'success':
                            iconClass = 'fa-check-circle';
                            bgClass = 'bg-success';
                            break;
                        case 'error':
                            iconClass = 'fa-exclamation-circle';
                            bgClass = 'bg-danger';
                            break;
                        case 'warning':
                            iconClass = 'fa-exclamation-triangle';
                            bgClass = 'bg-warning';
                            break;
                        case 'info':
                            iconClass = 'fa-info-circle';
                            bgClass = 'bg-info';
                            break;
                        default:
                            iconClass = 'fa-check-circle';
                            bgClass = 'bg-success';
                    }

                    // Ensure toast container exists
                    if ($('.toast-container').length === 0) {
                        console.log('Toast container not found, creating one');
                        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
                    }

                    const toast = $(`
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${iconClass} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);

                    $('.toast-container').append(toast);

                    try {
                        const bsToast = new bootstrap.Toast(toast[0], {
                            delay: 4000
                        });
                        bsToast.show();

                        // Remove from DOM after hiding
                        toast[0].addEventListener('hidden.bs.toast', function() {
                            $(this).remove();
                        });
                    } catch (e) {
                        console.error('Error showing toast:', e);
                        // Fallback to simple alert if bootstrap toast fails
                        alert(message);
                    }

                    // Trigger animation if enabled and success
                    if (type === 'success' && window.triggerCrackers) {
                        window.triggerCrackers();
                    }
                };
            }
        </script>
    @endpush
@endsection
