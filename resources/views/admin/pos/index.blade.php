@extends('admin.layouts.app')

@section('title', 'Point of Sale')
@section('page_title', 'Point of Sale System')

@section('page_actions')
<a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">
    <i class="fas fa-list"></i> Sales History
</a>
@endsection

@push('styles')
<link href="{{ asset('css/pos-enhanced-tax.css') }}" rel="stylesheet">
<style>
    .pos-container {
        height: calc(100vh - 200px);
        background: #f8f9fa;
    }
    
    .left-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
    }
    
    .right-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .category-sidebar {
        width: 200px;
        background: #2d5016;
        border-radius: 10px 0 0 10px;
        color: white;
        overflow-y: auto;
    }
    
    .category-item {
        padding: 15px 20px;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .category-item:hover,
    .category-item.active {
        background: rgba(255,255,255,0.1);
        border-left: 4px solid #6b8e23;
    }
    
    .category-item i {
        width: 20px;
        text-align: center;
    }
    
    .products-area {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }
    
    .product-search {
        margin-bottom: 20px;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
        max-height: calc(100vh - 350px);
        overflow-y: auto;
        padding: 10px 0;
    }
    
    .product-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    
    .product-card:hover {
        border-color: #2d5016;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(45, 80, 22, 0.2);
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        color: #6c757d;
        font-size: 24px;
    }
    
    .product-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .product-price {
        color: #2d5016;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .product-stock {
        color: #6c757d;
        font-size: 12px;
    }
    
    .product-stock.out-of-stock {
        color: #dc3545;
        font-weight: bold;
    }
    
    .cart-header {
        background: #2d5016;
        color: white;
        padding: 20px;
        border-radius: 10px 10px 0 0;
        text-align: center;
    }
    
    .cart-items {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        border-bottom: 1px solid #e9ecef;
    }
    
    .cart-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        border-left: 4px solid #2d5016;
    }
    
    .cart-item-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .cart-item-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #6c757d;
    }
    
    .cart-item-total {
        font-weight: bold;
        color: #2d5016;
        font-size: 16px;
        text-align: right;
        margin-top: 5px;
    }
    
    .quantity-badge {
        background: #2d5016;
        color: white;
        border-radius: 15px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: bold;
        margin-left: 5px;
    }
    
    .cart-summary {
        padding: 20px;
        background: #f8f9fa;
        border-top: 3px solid #2d5016;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
    }
    
    .summary-row.total {
        border-top: 2px solid #2d5016;
        padding-top: 10px;
        font-weight: bold;
        font-size: 18px;
        color: #2d5016;
    }
    
    .discount-tax-inputs {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
    }
    
    .checkout-btn {
        background: #2d5016;
        border: none;
        color: white;
        padding: 15px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        width: 100%;
        transition: all 0.3s;
    }
    
    .checkout-btn:hover {
        background: #6b8e23;
        transform: translateY(-1px);
    }
    
    .checkout-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }
    
    .empty-cart {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    
    .empty-cart i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    @media (max-width: 1200px) {
        .category-sidebar {
            width: 150px;
        }
        
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .pos-container {
            height: auto;
        }
        
        .category-sidebar {
            width: 100%;
            border-radius: 10px 10px 0 0;
        }
        
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="row h-100 g-3">
        <!-- Left Panel - Categories & Products -->
        <div class="col-lg-8">
            <div class="left-panel">
                <!-- Category Sidebar -->
                <div class="category-sidebar">
                    <div class="p-3 border-bottom border-light border-opacity-25">
                        <h6 class="mb-0 text-center">
                            <i class="fas fa-tags"></i> Categories
                        </h6>
                    </div>
                    
                    <div class="category-item active" data-category="all">
                        <i class="fas fa-th-large"></i>
                        <span>All Products</span>
                    </div>
                    
                    @foreach($products as $categoryName => $categoryProducts)
                        <div class="category-item" data-category="{{ $categoryName }}">
                            <i class="fas fa-leaf"></i>
                            <span>{{ $categoryName }}</span>
                        </div>
                    @endforeach
                </div>
                
                <!-- Products Area -->
                <div class="products-area">
                    <!-- Search Bar -->
                    <div class="product-search">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="productSearch" 
                                   placeholder="Search products by name or barcode...">
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="products-grid" id="productsGrid">
                        @foreach($products as $categoryName => $categoryProducts)
                            @foreach($categoryProducts as $product)
                                <div class="product-card" 
                                     data-product-id="{{ $product->id }}" 
                                     data-category="{{ $categoryName }}"
                                     data-name="{{ strtolower($product->name) }}"
                                     data-barcode="{{ $product->barcode ?? '' }}">
                                    
                                    <div class="product-image">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                    
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="product-price">₹{{ number_format($product->price, 2) }}</div>
                                    
                                    <div class="product-stock {{ $product->stock <= 0 ? 'out-of-stock' : '' }}">
                                        @if($product->stock > 0)
                                            <i class="fas fa-boxes"></i> {{ $product->stock }} in stock
                                        @else
                                            <i class="fas fa-times-circle"></i> Out of stock
                                        @endif
                                    </div>
                                    
                                    @if($product->barcode)
                                        <div class="text-muted" style="font-size: 10px; margin-top: 5px;">
                                            {{ $product->barcode }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Cart & Checkout -->
        <div class="col-lg-4">
            <div class="right-panel">
                <!-- Cart Header -->
                <div class="cart-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart"></i> Current Order
                    </h5>
                    <small>Select products to add to cart</small>
                </div>
                
                <!-- Cart Items -->
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart" id="emptyCart">
                        <i class="fas fa-shopping-cart"></i>
                        <h6>Cart is empty</h6>
                        <p>Click on products to add them to your cart</p>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <!-- Discount & Tax Inputs -->
                    <div class="discount-tax-inputs">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Discount</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="discountAmount" 
                                           value="0" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0">Tax</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customTaxToggle">
                                        <label class="form-check-label" for="customTaxToggle">
                                            <small>Manual</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="taxAmount" 
                                           value="0" min="0" step="0.01" readonly>
                                </div>
                                <div class="mt-2" id="taxNotesSection" style="display: none;">
                                    <input type="text" class="form-control form-control-sm" id="taxNotes" 
                                           placeholder="Tax notes..." maxlength="500">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Rows -->
                    <div class="summary-row">
                        <span>Items:</span>
                        <span id="totalItems">0</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>₹<span id="subtotal">0.00</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span class="text-success">-₹<span id="discountDisplay">0.00</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>CGST:</span>
                        <span>₹<span id="cgstDisplay">0.00</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>SGST:</span>
                        <span>₹<span id="sgstDisplay">0.00</span></span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>₹<span id="totalAmount">0.00</span></span>
                    </div>
                    
                    <!-- Checkout Button -->
                    <button type="button" class="checkout-btn" id="checkoutBtn" disabled>
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Add to Cart
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <div class="product-image d-inline-flex">
                        <i class="fas fa-seedling"></i>
                    </div>
                </div>
                
                <h6 id="modalProductName" class="mb-2"></h6>
                <p class="text-muted mb-1">Price: ₹<span id="modalProductPrice"></span></p>
                <p class="text-info mb-4">Available: <span id="modalProductStock"></span> units</p>
                
                <div class="row justify-content-center">
                    <div class="col-8">
                        <label class="form-label">Quantity</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="decreaseQty">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="productQuantity" 
                                   value="1" min="1">
                            <button type="button" class="btn btn-outline-secondary" id="increaseQty">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h5 class="text-primary">Total: ₹<span id="modalTotal">0.00</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="addToCartBtn">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-credit-card"></i> Checkout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="customerPhone" placeholder="Optional">
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-4">
                    <label class="form-label">Payment Method</label>
                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check h-100 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="cash" value="cash" checked>
                                <label class="form-check-label w-100" for="cash">
                                    <i class="fas fa-money-bill-wave d-block mb-1"></i>
                                    Cash
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check h-100 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="card" value="card">
                                <label class="form-check-label w-100" for="card">
                                    <i class="fas fa-credit-card d-block mb-1"></i>
                                    Card
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check h-100 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="upi" value="upi">
                                <label class="form-check-label w-100" for="upi">
                                    <i class="fas fa-mobile-alt d-block mb-1"></i>
                                    UPI
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check h-100 p-3 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="gpay" value="gpay">
                                <label class="form-check-label w-100" for="gpay">
                                    <i class="fab fa-google-pay d-block mb-1"></i>
                                    GPay
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" id="paidAmount" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Change</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="text" class="form-control" id="changeAmount" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <h5 class="text-center">Order Total: ₹<span id="checkoutTotal">0.00</span></h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-lg" id="processSaleBtn">
                    <i class="fas fa-check-circle"></i> Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Item Discount Modal -->
<div class="modal fade" id="itemDiscountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-percent"></i> Edit Item Discount
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="itemDiscountItemName" class="text-primary">Product Name</h6>
                    <div class="row text-sm">
                        <div class="col-6">Unit Price: ₹<span id="itemDiscountUnitPrice">0.00</span></div>
                        <div class="col-6">Quantity: <span id="itemDiscountQuantity">0</span></div>
                    </div>
                    <div class="text-muted">Subtotal: ₹<span id="itemDiscountSubtotal">0.00</span></div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Discount Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" id="itemDiscountAmount" 
                                   min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Discount Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="itemDiscountPercentage" 
                                   min="0" max="100" step="0.01" placeholder="0.00">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <div class="d-flex justify-content-between">
                        <strong>Net Amount:</strong>
                        <span>₹<span id="itemDiscountNetAmount">0.00</span></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyItemDiscountBtn">
                    <i class="fas fa-check"></i> Apply Discount
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert">
        <div class="toast-body" id="toastMessage">
            <!-- Toast message will be inserted here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let cart = [];
    let allProducts = @json($products->flatten());
    let selectedProduct = null;
    
    // Ensure all products have proper data types
    allProducts = allProducts.map(product => ({
        ...product,
        price: parseFloat(product.price) || 0,
        stock: parseInt(product.stock) || 0,
        tax_percentage: parseFloat(product.tax_percentage) || 0
    }));
    
    console.log('Products loaded:', allProducts.length);
    
    // Initialize
    updateCartDisplay();
    
    // Category filtering
    $('.category-item').click(function() {
        const category = $(this).data('category');
        
        $('.category-item').removeClass('active');
        $(this).addClass('active');
        
        filterProducts(category);
    });
    
    // Product search
    $('#productSearch').on('input', function() {
        const search = $(this).val().toLowerCase();
        searchProducts(search);
    });
    
    // Product click - open quantity modal
    $(document).on('click', '.product-card', function() {
        if ($(this).hasClass('out-of-stock')) {
            showToast('Product is out of stock!', 'error');
            return;
        }
        
        const productId = $(this).data('product-id');
        selectedProduct = allProducts.find(p => p.id == productId);
        
        if (selectedProduct && selectedProduct.stock > 0) {
            // Ensure price is a number
            selectedProduct.price = parseFloat(selectedProduct.price);
            selectedProduct.stock = parseInt(selectedProduct.stock);
            
            showQuantityModal(selectedProduct);
        } else {
            showToast('Product not found or out of stock!', 'error');
        }
    });
    
    // Quantity modal controls
    $('#decreaseQty').click(function() {
        const current = parseInt($('#productQuantity').val());
        if (current > 1) {
            $('#productQuantity').val(current - 1);
            updateModalTotal();
        }
    });
    
    $('#increaseQty').click(function() {
        const current = parseInt($('#productQuantity').val());
        const max = parseInt($('#productQuantity').attr('max'));
        if (current < max) {
            $('#productQuantity').val(current + 1);
            updateModalTotal();
        }
    });
    
    $('#productQuantity').on('input', function() {
        const value = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));
        
        if (value > max) {
            $(this).val(max);
        } else if (value < 1) {
            $(this).val(1);
        }
        
        updateModalTotal();
    });
    
    // Add to cart
    $('#addToCartBtn').click(function() {
        if (!selectedProduct) return;
        
        const quantity = parseInt($('#productQuantity').val()) || 1;
        addToCart(selectedProduct, quantity);
        $('#quantityModal').modal('hide');
    });
    
    // Calculation updates
    $('#discountAmount, #paidAmount').on('input', updateCalculations);
    
    // Custom tax toggle
    $('#customTaxToggle').change(function() {
        const isCustom = $(this).is(':checked');
        
        if (isCustom) {
            $('#taxAmount').removeAttr('readonly').focus();
            $('#taxNotesSection').show();
            showToast('Manual tax mode enabled. You can now edit the tax amount.', 'success');
        } else {
            $('#taxAmount').attr('readonly', true);
            $('#taxNotesSection').hide();
            $('#taxNotes').val('');
            showToast('Auto tax mode enabled. Tax will be calculated automatically.', 'success');
            updateCalculations(); // Recalculate with auto tax
        }
    });
    
    // Manual tax input
    $('#taxAmount').on('input', function() {
        if ($('#customTaxToggle').is(':checked')) {
            updateCalculationsWithCustomTax();
        }
    });
    
    // Checkout
    $('#checkoutBtn').click(function() {
        if (cart.length === 0) {
            showToast('Cart is empty!', 'error');
            return;
        }
        
        showCheckoutModal();
    });
    
    // Process sale
    $('#processSaleBtn').click(function() {
        const totalAmount = parseFloat($('#checkoutTotal').text());
        const paidAmount = parseFloat($('#paidAmount').val()) || 0;
        
        if (paidAmount < totalAmount) {
            showToast('Paid amount is insufficient!', 'error');
            return;
        }
        
        processSale();
    });
    
    // Payment method change
    $('input[name="paymentMethod"]').change(function() {
        const totalAmount = parseFloat($('#checkoutTotal').text());
        const paymentMethod = $(this).val();
        
        // Auto-fill exact amount for digital payments
        if (paymentMethod !== 'cash') {
            $('#paidAmount').val(totalAmount.toFixed(2));
        } else {
            $('#paidAmount').val('');
        }
        
        updateCheckoutCalculations();
    });
    
    function filterProducts(category) {
        if (category === 'all') {
            $('.product-card').show();
        } else {
            $('.product-card').hide();
            $(`.product-card[data-category="${category}"]`).show();
        }
    }
    
    function searchProducts(search) {
        if (search === '') {
            $('.product-card').show();
            return;
        }
        
        $('.product-card').each(function() {
            const name = $(this).data('name');
            const barcode = $(this).data('barcode');
            
            if (name.includes(search) || barcode.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    function showQuantityModal(product) {
        // Double check the product data
        if (!product || !product.name || isNaN(product.price)) {
            showToast('Invalid product data!', 'error');
            console.error('Invalid product:', product);
            return;
        }
        
        $('#modalProductName').text(product.name);
        $('#modalProductPrice').text(product.price.toFixed(2));
        $('#modalProductStock').text(product.stock);
        $('#productQuantity').attr('max', product.stock).val(1);
        updateModalTotal();
        $('#quantityModal').modal('show');
        
        setTimeout(() => {
            $('#productQuantity').focus().select();
        }, 500);
    }
    
    function updateModalTotal() {
        if (selectedProduct && !isNaN(selectedProduct.price)) {
            const quantity = parseInt($('#productQuantity').val()) || 1;
            const total = selectedProduct.price * quantity;
            $('#modalTotal').text(total.toFixed(2));
        } else {
            $('#modalTotal').text('0.00');
        }
    }
    
    function addToCart(product, quantity) {
        if (!product || isNaN(product.price)) {
            showToast('Invalid product data!', 'error');
            return;
        }
        
        const existingItem = cart.find(item => item.product_id === product.id);
        
        if (existingItem) {
            const newQuantity = existingItem.quantity + quantity;
            if (newQuantity > product.stock) {
                showToast(`Cannot add ${quantity} more. Maximum available: ${product.stock - existingItem.quantity}`, 'error');
                return;
            }
            existingItem.quantity = newQuantity;
        } else {
            cart.push({
                product_id: product.id,
                name: product.name,
                price: product.price, // Already converted to number
                quantity: quantity,
                stock: product.stock,
                tax_percentage: product.tax_percentage || 0,
                discount_amount: 0, // Initialize item-level discount
                discount_percentage: 0
            });
        }
        
        updateCartDisplay();
        showToast(`Added ${quantity}x ${product.name} to cart`, 'success');
    }
    
    function updateCartDisplay() {
        const cartContainer = $('#cartItems');
        const emptyCart = $('#emptyCart');
        
        if (cart.length === 0) {
            emptyCart.show();
            $('#checkoutBtn').prop('disabled', true);
        } else {
            emptyCart.hide();
            cartContainer.empty();
            
            cart.forEach((item, index) => {
                const itemSubtotal = item.price * item.quantity;
                const itemDiscountAmount = item.discount_amount || 0;
                const itemNetAmount = itemSubtotal - itemDiscountAmount;
                
                const cartItemHtml = `
                    <div class="cart-item">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-details">
                            <span>₹${item.price.toFixed(2)} × ${item.quantity}</span>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="editItemDiscount(${index})" title="Edit Discount">
                                <i class="fas fa-percent"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        ${itemDiscountAmount > 0 ? `<div class="text-success small">Discount: -₹${itemDiscountAmount.toFixed(2)}</div>` : ''}
                        <div class="cart-item-total">₹${itemNetAmount.toFixed(2)}</div>
                    </div>
                `;
                cartContainer.append(cartItemHtml);
            });
            
            $('#checkoutBtn').prop('disabled', false);
        }
        
        updateCalculations();
    }
    
    function updateCalculations() {
        // Calculate subtotal considering item-level discounts
        let subtotal = 0;
        cart.forEach(item => {
            const itemGross = item.price * item.quantity;
            const itemDiscount = item.discount_amount || 0;
            subtotal += (itemGross - itemDiscount);
        });
        
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const additionalDiscount = parseFloat($('#discountAmount').val()) || 0; // Sale-level discount
        
        let totalTax = 0;
        
        // Check if custom tax is enabled
        if ($('#customTaxToggle').is(':checked')) {
            // Use manual tax amount
            totalTax = parseFloat($('#taxAmount').val()) || 0;
        } else {
            // Calculate tax based on products' tax percentages (after item discounts)
            cart.forEach(item => {
                const itemGross = item.price * item.quantity;
                const itemDiscount = item.discount_amount || 0;
                const itemNet = itemGross - itemDiscount;
                const itemTax = (itemNet * item.tax_percentage) / 100;
                totalTax += itemTax;
            });
            
            // Update tax amount field with calculated value
            $('#taxAmount').val(totalTax.toFixed(2));
        }
        
        const cgst = totalTax / 2;
        const sgst = totalTax / 2;
        const total = subtotal - additionalDiscount + totalTax;
        
        $('#totalItems').text(totalItems);
        $('#subtotal').text(subtotal.toFixed(2));
        $('#discountDisplay').text(additionalDiscount.toFixed(2));
        $('#cgstDisplay').text(cgst.toFixed(2));
        $('#sgstDisplay').text(sgst.toFixed(2));
        $('#totalAmount').text(total.toFixed(2));
    }
    
    function updateCalculationsWithCustomTax() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const discount = parseFloat($('#discountAmount').val()) || 0;
        const totalTax = parseFloat($('#taxAmount').val()) || 0;
        
        const cgst = totalTax / 2;
        const sgst = totalTax / 2;
        const total = subtotal - discount + totalTax;
        
        $('#totalItems').text(totalItems);
        $('#subtotal').text(subtotal.toFixed(2));
        $('#discountDisplay').text(discount.toFixed(2));
        $('#cgstDisplay').text(cgst.toFixed(2));
        $('#sgstDisplay').text(sgst.toFixed(2));
        $('#totalAmount').text(total.toFixed(2));
    }
    
    function showCheckoutModal() {
        const total = parseFloat($('#totalAmount').text());
        $('#checkoutTotal').text(total.toFixed(2));
        $('#paidAmount').val('');
        $('#changeAmount').val('0.00');
        $('#customerName, #customerPhone').val('');
        $('#cash').prop('checked', true);
        $('#checkoutModal').modal('show');
        
        setTimeout(() => {
            $('#customerName').focus();
        }, 500);
    }
    
    function updateCheckoutCalculations() {
        const total = parseFloat($('#checkoutTotal').text());
        const paid = parseFloat($('#paidAmount').val()) || 0;
        const change = Math.max(0, paid - total);
        
        $('#changeAmount').val(change.toFixed(2));
    }
    
    function processSale() {
        const saleData = {
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                unit_price: item.price, // Already a number
                discount_amount: item.discount_amount || 0 // Include item-level discount
            })),
            customer_name: $('#customerName').val(),
            customer_phone: $('#customerPhone').val(),
            tax_amount: parseFloat($('#taxAmount').val()) || 0,
            custom_tax_enabled: $('#customTaxToggle').is(':checked'),
            custom_tax_amount: $('#customTaxToggle').is(':checked') ? (parseFloat($('#taxAmount').val()) || 0) : 0,
            tax_notes: $('#customTaxToggle').is(':checked') ? $('#taxNotes').val() : '',
            discount_amount: parseFloat($('#discountAmount').val()) || 0,
            paid_amount: parseFloat($('#paidAmount').val()) || 0,
            payment_method: $('input[name="paymentMethod"]:checked').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $('#processSaleBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: '{{ route("admin.pos.store") }}',
            method: 'POST',
            data: saleData,
            success: function(response) {
                if (response.success) {
                    $('#checkoutModal').modal('hide');
                    showToast('Sale completed successfully!', 'success');
                    resetPOS();
                    
                    // Open receipt in new window
                    window.open('{{ url("admin/pos/receipt") }}/' + response.sale_id, '_blank');
                } else {
                    showToast('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('Error: ' + (response ? response.message : 'Something went wrong!'), 'error');
            },
            complete: function() {
                $('#processSaleBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Complete Sale');
            }
        });
    }
    
    function resetPOS() {
        cart = [];
        $('#discountAmount, #taxAmount').val('0');
        $('#customTaxToggle').prop('checked', false);
        $('#taxAmount').attr('readonly', true);
        $('#taxNotesSection').hide();
        $('#taxNotes').val('');
        updateCartDisplay();
    }
    
    function showToast(message, type = 'success') {
        const toastEl = $('#liveToast');
        const iconClass = type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-danger';
        
        $('#toastMessage').html(`<i class="fas ${iconClass} me-2"></i>${message}`);
        
        const toast = new bootstrap.Toast(toastEl[0]);
        toast.show();
    }
    
    // Global functions
    window.removeFromCart = function(index) {
        cart.splice(index, 1);
        updateCartDisplay();
        showToast('Item removed from cart', 'success');
    };
    
    window.editItemDiscount = function(index) {
        const item = cart[index];
        if (!item) return;
        
        $('#itemDiscountModal .modal-title').html(`<i class="fas fa-percent"></i> Edit Discount - ${item.name}`);
        $('#itemDiscountItemName').text(item.name);
        $('#itemDiscountUnitPrice').text(item.price.toFixed(2));
        $('#itemDiscountQuantity').text(item.quantity);
        $('#itemDiscountSubtotal').text((item.price * item.quantity).toFixed(2));
        
        $('#itemDiscountAmount').val(item.discount_amount || 0);
        $('#itemDiscountPercentage').val(item.discount_percentage || 0);
        
        $('#itemDiscountModal').data('item-index', index).modal('show');
        updateItemDiscountPreview();
    };
    
    function updateItemDiscountPreview() {
        const index = $('#itemDiscountModal').data('item-index');
        const item = cart[index];
        if (!item) return;
        
        const subtotal = item.price * item.quantity;
        const discountAmount = parseFloat($('#itemDiscountAmount').val()) || 0;
        const discountPercentage = parseFloat($('#itemDiscountPercentage').val()) || 0;
        
        let finalDiscountAmount = discountAmount;
        
        // If percentage is being used, calculate amount
        if (discountPercentage > 0 && $('#itemDiscountPercentage').is(':focus')) {
            finalDiscountAmount = (subtotal * discountPercentage) / 100;
            $('#itemDiscountAmount').val(finalDiscountAmount.toFixed(2));
        }
        
        // If amount is being used, calculate percentage
        if (discountAmount > 0 && $('#itemDiscountAmount').is(':focus')) {
            const calculatedPercentage = subtotal > 0 ? (discountAmount / subtotal) * 100 : 0;
            $('#itemDiscountPercentage').val(calculatedPercentage.toFixed(2));
        }
        
        const netAmount = subtotal - finalDiscountAmount;
        $('#itemDiscountNetAmount').text(netAmount.toFixed(2));
    }
    
    function applyItemDiscount() {
        const index = $('#itemDiscountModal').data('item-index');
        const item = cart[index];
        if (!item) return;
        
        const discountAmount = parseFloat($('#itemDiscountAmount').val()) || 0;
        const discountPercentage = parseFloat($('#itemDiscountPercentage').val()) || 0;
        
        const maxDiscount = item.price * item.quantity;
        
        if (discountAmount > maxDiscount) {
            showToast('Discount amount cannot exceed item total!', 'error');
            return;
        }
        
        cart[index].discount_amount = discountAmount;
        cart[index].discount_percentage = discountPercentage;
        
        updateCartDisplay();
        $('#itemDiscountModal').modal('hide');
        showToast('Item discount applied successfully!', 'success');
    }
    
    // Item discount modal event handlers
    $('#itemDiscountAmount, #itemDiscountPercentage').on('input', updateItemDiscountPreview);
    $('#applyItemDiscountBtn').click(applyItemDiscount);
    
    // Update paid amount calculation
    $('#paidAmount').on('input', updateCheckoutCalculations);
});
</script>
@endpush
