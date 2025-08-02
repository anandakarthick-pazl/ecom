@extends('admin.layouts.app')

@section('title', 'Point of Sale')
@section('page_title', 'Point of Sale System')

@section('page_actions')
<a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">
    <i class="fas fa-list"></i> Sales History
</a>
@if(isset($activeOffers) && $activeOffers->count() > 0)
<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#activeOffersModal">
    <i class="fas fa-tag"></i> Active Offers ({{ $activeOffers->count() }})
</button>
@endif
@endsection

@push('styles')
<style>
    .pos-container {
        height: calc(100vh - 180px);
        background: #f8f9fa;
    }
    
    .left-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .right-panel {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .product-filters {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 10px 10px 0 0;
        flex-shrink: 0;
    }
    
    .products-area {
        flex: 1;
        padding: 15px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .products-table-container {
        flex: 1;
        overflow: hidden;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
    }
    
    .products-table {
        margin-bottom: 0;
        flex: 1;
        height: 100%;
    }
    
    .products-table th {
        background: #2d5016;
        color: white;
        font-weight: 600;
        text-align: center;
        font-size: 12px;
        padding: 8px 6px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .products-table td {
        text-align: center;
        padding: 8px 6px;
        font-size: 12px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }
    
    .product-row {
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .product-row:hover {
        background-color: #f8f9fc;
        transform: scale(1.01);
    }
    
    .product-row.has-offer {
        background: linear-gradient(135deg, #fff 0%, #fff5f5 100%);
        border-left: 3px solid #dc3545;
    }
    
    .product-row.out-of-stock {
        background-color: #f8f8f8;
        color: #6c757d;
        cursor: not-allowed;
    }
    
    .product-name {
        font-weight: 600;
        color: #333;
        text-align: left;
        max-width: 120px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .product-price {
        font-weight: bold;
        color: #2d5016;
    }
    
    .price-original {
        color: #6c757d;
        text-decoration: line-through;
        font-size: 11px;
        margin-right: 5px;
    }
    
    .price-discounted {
        color: #dc3545;
        font-weight: bold;
    }
    
    .offer-badge {
        background: #dc3545;
        color: white;
        padding: 1px 4px;
        border-radius: 3px;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .stock-badge {
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
    }
    
    .stock-good {
        background: #d4edda;
        color: #155724;
    }
    
    .stock-low {
        background: #fff3cd;
        color: #856404;
    }
    
    .stock-out {
        background: #f8d7da;
        color: #721c24;
    }
    
    .category-badge {
        background: #e9ecef;
        color: #495057;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 10px;
    }
    
    .pagination-wrapper {
        padding: 10px 15px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 8px 8px;
        flex-shrink: 0;
    }
    
    .cart-header {
        background: #2d5016;
        color: white;
        padding: 15px;
        border-radius: 10px 10px 0 0;
        text-align: center;
        flex-shrink: 0;
    }
    
    .cart-items {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        max-height: calc(100vh - 400px);
        min-height: 200px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .cart-item {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 8px;
        border-left: 3px solid #2d5016;
    }
    
    .cart-item.has-offer {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #f8f9fa 0%, #fff5f5 100%);
    }
    
    .cart-item-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        font-size: 13px;
    }
    
    .cart-item-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #6c757d;
    }
    
    .cart-item-total {
        font-weight: bold;
        color: #2d5016;
        font-size: 14px;
        text-align: right;
        margin-top: 4px;
    }
    
    .cart-summary {
        padding: 15px;
        background: #f8f9fa;
        border-top: 3px solid #2d5016;
        flex-shrink: 0;
        border-radius: 0 0 10px 10px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 3px 0;
        font-size: 13px;
    }
    
    .summary-row.total {
        border-top: 2px solid #2d5016;
        padding-top: 8px;
        font-weight: bold;
        font-size: 16px;
        color: #2d5016;
    }
    
    .checkout-btn {
        background: #2d5016;
        border: none;
        color: white;
        padding: 12px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: bold;
        width: 100%;
        transition: all 0.3s;
    }
    
    .checkout-btn:hover {
        background: #6b8e23;
    }
    
    .checkout-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }
    
    .empty-cart {
        text-align: center;
        padding: 30px 15px;
        color: #6c757d;
    }
    
    .filter-form {
        display: flex;
        gap: 10px;
        align-items: end;
    }
    
    .filter-form .form-group {
        margin-bottom: 0;
        flex: 1;
    }
    
    .filter-form .btn {
        height: 38px;
    }
    
    .products-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding: 8px 12px;
        background: #e9ecef;
        border-radius: 6px;
        font-size: 12px;
        flex-shrink: 0;
    }
    
    .quantity-controls {
        white-space: nowrap;
    }
    
    .qty-input {
        border: 1px solid #ced4da;
        -moz-appearance: textfield;
    }
    
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .qty-decrease, .qty-increase {
        min-width: 24px;
        border-color: #ced4da;
    }
    
    .qty-decrease:hover, .qty-increase:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }
    
    /* Custom pagination styles */
    .pagination .page-link {
        color: #2d5016;
        border-color: #2d5016;
        font-size: 12px;
        padding: 4px 8px;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #2d5016;
        border-color: #2d5016;
    }
    
    .pagination .page-link:hover {
        color: #fff;
        background-color: #2d5016;
        border-color: #2d5016;
    }
</style>
@endpush

@section('content')
<div class="pos-container">
    <div class="row h-100 g-3">
        <!-- Left Panel - Products -->
        <div class="col-lg-8">
            <div class="left-panel">
                <!-- Product Filters -->
                <div class="product-filters">
                    <form method="GET" action="{{ route('admin.pos.index') }}" class="filter-form">
                        <div class="form-group">
                            <label class="form-label small">Search Products</label>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by name, barcode, SKU...">
                        </div>
                        <div class="form-group">
                            <label class="form-label small">Category</label>
                            <select name="category" class="form-control form-control-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.pos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </form>
                </div>
                
                <!-- Products Area -->
                <div class="products-area">
                    <!-- Products Info -->
                    <div class="products-info">
                        <span><strong>{{ $products->total() }}</strong> products found</span>
                        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
                    </div>
                    
                    <!-- Products Table -->
                    <div class="products-table-container">
                        <div class="table-wrapper" style="flex: 1; overflow-y: auto;">
                            <table class="table table-sm table-hover products-table">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">Product</th>
                                        <th style="width: 24%;">Category</th>
                                        <th style="width: 13%;">Price</th>
                                        <th style="width: 8%;">Stock</th>
                                        {{-- <th style="width: 12%;">Barcode</th> --}}
                                        <th style="width: 35%;">Quantity & Add</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($products as $product)
                                    <tr class="product-row {{ $product->has_offer ? 'has-offer' : '' }} {{ $product->stock <= 0 ? 'out-of-stock' : '' }}" 
                                        data-product-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        data-effective-price="{{ $product->effective_price }}"
                                        data-stock="{{ $product->stock }}"
                                        data-has-offer="{{ $product->has_offer ? 'true' : 'false' }}"
                                        data-discount-percentage="{{ $product->discount_percentage ?? 0 }}"
                                        data-tax-percentage="{{ $product->tax_percentage ?? 0 }}">
                                        
                                        <td class="product-name" title="{{ $product->name }}">
                                            <strong>{{ Str::limit($product->name, 20) }}</strong>
                                            @if($product->sku)
                                                <br><small class="text-muted">{{ $product->sku }}</small>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($product->category)
                                                <span class="category-badge">{{ Str::limit($product->category->name, 12) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        <td class="product-price">
                                            @if($product->has_offer && $product->effective_price < $product->price)
                                                <span class="price-original">₹{{ number_format($product->price, 2) }}</span><br>
                                                <span class="price-discounted">₹{{ number_format($product->effective_price, 2) }}</span>
                                                @if($product->discount_percentage)
                                                    <br><span class="offer-badge">{{ $product->discount_percentage }}% OFF</span>
                                                @endif
                                            @else
                                                ₹{{ number_format($product->price, 2) }}
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($product->stock > 10)
                                                <span class="stock-badge stock-good">{{ $product->stock }}</span>
                                            @elseif($product->stock > 0)
                                                <span class="stock-badge stock-low">{{ $product->stock }}</span>
                                            @else
                                                <span class="stock-badge stock-out">Out</span>
                                            @endif
                                        </td>
                                        
                                        {{-- <td>
                                            @if($product->barcode)
                                                <small>{{ $product->barcode }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td> --}}
                                        
                                        <td>
                                            @if($product->stock > 0)
                                                <div class="quantity-controls d-flex align-items-center justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-decrease" 
                                                            data-product-id="{{ $product->id }}" style="padding: 2px 6px;">
                                                        <i class="fas fa-minus" style="font-size: 10px;"></i>
                                                    </button>
                                                    <input type="number" class="form-control form-control-sm qty-input text-center mx-1" 
                                                           data-product-id="{{ $product->id }}" 
                                                           value="1" min="1" max="{{ $product->stock }}" 
                                                           style="width: 45px; height: 24px; font-size: 11px; padding: 2px;">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-increase" 
                                                            data-product-id="{{ $product->id }}" style="padding: 2px 6px;">
                                                        <i class="fas fa-plus" style="font-size: 10px;"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success add-btn ms-2" 
                                                            data-product-id="{{ $product->id }}" style="padding: 3px 8px; font-size: 10px;">
                                                        <i class="fas fa-cart-plus"></i> Add
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled style="padding: 3px 8px; font-size: 10px;">
                                                    <i class="fas fa-times"></i> Out
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-search text-muted mb-2" style="font-size: 24px;"></i>
                                            <p class="text-muted mb-0">No products found</p>
                                            @if(request('search') || request('category'))
                                                <a href="{{ route('admin.pos.index') }}" class="btn btn-link btn-sm">Clear filters</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pagination-wrapper">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                            </small>
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Right Panel - Cart & Checkout -->
        <div class="col-lg-4">
            <div class="right-panel">
                <!-- Cart Header -->
                <div class="cart-header">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart"></i> Current Order
                    </h6>
                    <small>Click products to add to cart</small>
                </div>
                
                <!-- Cart Items -->
                <div class="cart-items" id="cartItems">
                    <div class="empty-cart" id="emptyCart">
                        <i class="fas fa-shopping-cart mb-2" style="font-size: 24px; opacity: 0.5;"></i>
                        <h6>Cart is empty</h6>
                        <p class="small">Click on products to add them to your cart</p>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="cart-summary">
                    <!-- Discount & Tax Inputs -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small">Discount</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="discountAmount" 
                                       value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Tax</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="taxAmount" 
                                       value="0" min="0" step="0.01" readonly>
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
                    
                    <div class="summary-row" id="offerSavingsRow" style="display: none;">
                        <span class="text-success"><i class="fas fa-tag"></i> Savings:</span>
                        <span class="text-success">-₹<span id="offerSavings">0.00</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Discount:</span>
                        <span class="text-success">-₹<span id="discountDisplay">0.00</span></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>₹<span id="taxDisplay">0.00</span></span>
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

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-credit-card"></i> Checkout
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Customer Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label small">Customer Name</label>
                        <input type="text" class="form-control form-control-sm" id="customerName" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Phone Number</label>
                        <input type="text" class="form-control form-control-sm" id="customerPhone" placeholder="Optional">
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label small">Payment Method</label>
                    <div class="row">
                        <div class="col-3">
                            <div class="form-check text-center p-2 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="cash" value="cash" checked>
                                <label class="form-check-label w-100" for="cash">
                                    <i class="fas fa-money-bill-wave d-block"></i>
                                    <small>Cash</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check text-center p-2 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="card" value="card">
                                <label class="form-check-label w-100" for="card">
                                    <i class="fas fa-credit-card d-block"></i>
                                    <small>Card</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check text-center p-2 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="upi" value="upi">
                                <label class="form-check-label w-100" for="upi">
                                    <i class="fas fa-mobile-alt d-block"></i>
                                    <small>UPI</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check text-center p-2 border rounded">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="gpay" value="gpay">
                                <label class="form-check-label w-100" for="gpay">
                                    <i class="fab fa-google-pay d-block"></i>
                                    <small>GPay</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Commission Section -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0 small">Commission Tracking</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="commissionEnabled">
                            <label class="form-check-label" for="commissionEnabled">
                                <small>Enable Commission</small>
                            </label>
                        </div>
                    </div>
                    
                    <div id="commissionSection" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Reference Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="referenceName" 
                                       placeholder="Enter reference person name">
                                <div class="form-text">Name of person eligible for commission</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Commission % <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="commissionPercentage" 
                                           min="0" max="100" step="0.01" placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text">Commission percentage (0-100%)</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label small">Commission Notes</label>
                                <input type="text" class="form-control form-control-sm" id="commissionNotes" 
                                       placeholder="Optional notes about commission" maxlength="500">
                            </div>
                        </div>
                        <div class="alert alert-info py-2">
                            <div class="d-flex justify-content-between small">
                                <span><i class="fas fa-info-circle"></i> Commission Amount:</span>
                                <strong>₹<span id="commissionAmount">0.00</span></strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label small">Amount to Pay</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" id="paidAmount" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Change</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="text" class="form-control" id="changeAmount" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded text-center">
                    <h6>Order Total: ₹<span id="checkoutTotal">0.00</span></h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="processSaleBtn">
                    <i class="fas fa-check-circle"></i> Complete Sale
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
    let selectedProduct = null;
    
    // Initialize
    updateCartDisplay();
    
    // Quantity control event handlers
    $(document).on('click', '.qty-decrease', function() {
        const productId = $(this).data('product-id');
        const input = $(`.qty-input[data-product-id="${productId}"]`);
        const currentValue = parseInt(input.val());
        const minValue = parseInt(input.attr('min'));
        
        if (currentValue > minValue) {
            input.val(currentValue - 1);
        }
    });
    
    $(document).on('click', '.qty-increase', function() {
        const productId = $(this).data('product-id');
        const input = $(`.qty-input[data-product-id="${productId}"]`);
        const currentValue = parseInt(input.val());
        const maxValue = parseInt(input.attr('max'));
        
        if (currentValue < maxValue) {
            input.val(currentValue + 1);
        }
    });
    
    // Validate quantity input
    $(document).on('input', '.qty-input', function() {
        const value = parseInt($(this).val());
        const min = parseInt($(this).attr('min'));
        const max = parseInt($(this).attr('max'));
        
        if (value < min) {
            $(this).val(min);
        } else if (value > max) {
            $(this).val(max);
        }
    });
    
    // Add to cart with quantity
    $(document).on('click', '.add-btn', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(`.qty-input[data-product-id="${productId}"]`).val()) || 1;
        const row = $(`.product-row[data-product-id="${productId}"]`);
        
        const product = {
            id: productId,
            name: row.data('name'),
            price: parseFloat(row.data('price')),
            effective_price: parseFloat(row.data('effective-price')),
            stock: parseInt(row.data('stock')),
            has_offer: row.data('has-offer') === 'true',
            discount_percentage: parseInt(row.data('discount-percentage')) || 0,
            tax_percentage: parseFloat(row.data('tax-percentage')) || 0
        };
        
        addToCart(product, quantity);
        
        // Reset quantity to 1 after adding
        $(`.qty-input[data-product-id="${productId}"]`).val(1);
    });
    
    // Calculation updates
    $('#discountAmount, #paidAmount').on('input', updateCalculations);
    
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
    
    // Commission toggle
    $('#commissionEnabled').change(function() {
        const isEnabled = $(this).is(':checked');
        
        if (isEnabled) {
            $('#commissionSection').show();
            $('#referenceName').focus();
        } else {
            $('#commissionSection').hide();
            clearCommissionFields();
        }
    });
    
    // Commission calculation
    $('#commissionPercentage').on('input', calculateCommission);
    $('#referenceName, #commissionPercentage').on('input', function() {
        calculateCommission();
        validateCommissionFields();
    });
    
    function addToCart(product, quantity) {
        const existingItem = cart.find(item => item.product_id === product.id);
        
        if (existingItem) {
            const newQuantity = existingItem.quantity + quantity;
            if (newQuantity > product.stock) {
                showToast(`Cannot add ${quantity} more. Max available: ${product.stock - existingItem.quantity}`, 'error');
                return;
            }
            existingItem.quantity = newQuantity;
        } else {
            cart.push({
                product_id: product.id,
                name: product.name,
                price: product.effective_price,
                original_price: product.price,
                quantity: quantity,
                stock: product.stock,
                tax_percentage: product.tax_percentage || 0,
                has_offer: product.has_offer || false,
                offer_savings: product.has_offer ? (product.price - product.effective_price) * quantity : 0
            });
        }
        
        updateCartDisplay();
        
        let message = `Added ${quantity}x ${product.name} to cart`;
        if (product.has_offer) {
            const savings = (product.price - product.effective_price) * quantity;
            message += ` (Saved ₹${savings.toFixed(2)})`;
        }
        
        showToast(message, 'success');
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
                const itemTotal = item.price * item.quantity;
                const hasOfferClass = item.has_offer ? 'has-offer' : '';
                
                let offerInfo = '';
                if (item.has_offer && item.offer_savings > 0) {
                    offerInfo = `<div class="text-success small"><i class="fas fa-tag"></i> Savings: ₹${item.offer_savings.toFixed(2)}</div>`;
                }
                
                const cartItemHtml = `
                    <div class="cart-item ${hasOfferClass}">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-details">
                            <span>₹${item.price.toFixed(2)} × ${item.quantity}</span>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        ${offerInfo}
                        <div class="cart-item-total">₹${itemTotal.toFixed(2)}</div>
                    </div>
                `;
                cartContainer.append(cartItemHtml);
            });
            
            $('#checkoutBtn').prop('disabled', false);
        }
        
        updateCalculations();
    }
    
    function updateCalculations() {
        let subtotal = 0;
        let totalOfferSavings = 0;
        let totalTax = 0;
        
        cart.forEach(item => {
            const itemGross = item.price * item.quantity;
            subtotal += itemGross;
            totalOfferSavings += (item.offer_savings || 0);
            totalTax += (itemGross * item.tax_percentage) / 100;
        });
        
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const discount = parseFloat($('#discountAmount').val()) || 0;
        const total = subtotal - discount + totalTax;
        
        $('#totalItems').text(totalItems);
        $('#subtotal').text(subtotal.toFixed(2));
        $('#discountDisplay').text(discount.toFixed(2));
        $('#taxAmount').val(totalTax.toFixed(2));
        $('#taxDisplay').text(totalTax.toFixed(2));
        $('#totalAmount').text(total.toFixed(2));
        
        if (totalOfferSavings > 0) {
            $('#offerSavings').text(totalOfferSavings.toFixed(2));
            $('#offerSavingsRow').show();
        } else {
            $('#offerSavingsRow').hide();
        }
    }
    
    function showCheckoutModal() {
        const total = parseFloat($('#totalAmount').text());
        $('#checkoutTotal').text(total.toFixed(2));
        $('#paidAmount').val('');
        $('#changeAmount').val('0.00');
        $('#customerName, #customerPhone').val('');
        $('#cash').prop('checked', true);
        
        // Reset commission fields
        $('#commissionEnabled').prop('checked', false);
        $('#commissionSection').hide();
        clearCommissionFields();
        
        $('#checkoutModal').modal('show');
    }
    
    function updateCheckoutCalculations() {
        const total = parseFloat($('#checkoutTotal').text());
        const paid = parseFloat($('#paidAmount').val()) || 0;
        const change = Math.max(0, paid - total);
        
        $('#changeAmount').val(change.toFixed(2));
    }
    
    function processSale() {
        // Validate commission fields if enabled
        if ($('#commissionEnabled').is(':checked')) {
            if (!validateCommissionFields()) {
                return;
            }
        }
        
        const saleData = {
            items: cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                unit_price: item.price
            })),
            customer_name: $('#customerName').val(),
            customer_phone: $('#customerPhone').val(),
            tax_amount: parseFloat($('#taxAmount').val()) || 0,
            discount_amount: parseFloat($('#discountAmount').val()) || 0,
            paid_amount: parseFloat($('#paidAmount').val()) || 0,
            payment_method: $('input[name="paymentMethod"]:checked').val(),
            // Commission fields
            commission_enabled: $('#commissionEnabled').is(':checked') ? '1' : '0',
            reference_name: $('#referenceName').val(),
            commission_percentage: parseFloat($('#commissionPercentage').val()) || 0,
            commission_notes: $('#commissionNotes').val(),
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
                    
                    // Show success message with commission info
                    let successMessage = 'Sale completed successfully!';
                    if (response.commission_created) {
                        successMessage += ' Commission tracking enabled.';
                    }
                    
                    showToast(successMessage, 'success');
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
    
    // Update paid amount calculation
    $('#paidAmount').on('input', updateCheckoutCalculations);
    
    // Commission utility functions
    function calculateCommission() {
        const totalAmount = parseFloat($('#checkoutTotal').text()) || 0;
        const commissionPercentage = parseFloat($('#commissionPercentage').val()) || 0;
        const commissionAmount = (totalAmount * commissionPercentage) / 100;
        
        $('#commissionAmount').text(commissionAmount.toFixed(2));
        
        return commissionAmount;
    }
    
    function validateCommissionFields() {
        const referenceName = $('#referenceName').val().trim();
        const commissionPercentage = parseFloat($('#commissionPercentage').val()) || 0;
        
        if (!referenceName) {
            showToast('Please enter reference name for commission tracking!', 'error');
            $('#referenceName').focus();
            return false;
        }
        
        if (commissionPercentage <= 0 || commissionPercentage > 100) {
            showToast('Please enter a valid commission percentage (0.01-100)!', 'error');
            $('#commissionPercentage').focus();
            return false;
        }
        
        return true;
    }
    
    function clearCommissionFields() {
        $('#referenceName').val('');
        $('#commissionPercentage').val('');
        $('#commissionNotes').val('');
        $('#commissionAmount').text('0.00');
    }
});
</script>
@endpush