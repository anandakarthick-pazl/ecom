@extends('admin.layouts.app')
<?php

// echo"<pre>";print_R($order->items);exit;
?>
@section('page_actions')
    <div class="btn-group invoice-actions-group">
        @php
            $companyId = session('selected_company_id');
            $defaultBillFormat = \App\Models\AppSetting::getForTenant('default_bill_format', $companyId) ?? 'a4_sheet';
            $isThermal = $defaultBillFormat === 'thermal';
            $formatLabel = $isThermal ? 'Receipt (Thermal)' : 'Invoice (A4)';
            $formatIcon = $isThermal ? 'fa-receipt' : 'fa-file-invoice';
        @endphp

        <!-- Print Invoice -->
        <a href="{{ route('admin.orders.print-invoice', $order) }}?format={{ $defaultBillFormat }}" class="btn btn-secondary"
            target="_blank" title="Print {{ $formatLabel }}" onclick="openPrintWindow(this.href); return false;">
            <i class="fas {{ $formatIcon }}"></i> Print {{ $formatLabel }}
        </a>

        <!-- Send Invoice via Email -->
        @if ($order->customer_email)
            <form action="{{ route('admin.orders.send-invoice', $order) }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="format" value="{{ $defaultBillFormat }}">
                <button type="submit" class="btn btn-success"
                    onclick="return confirm('Send {{ strtolower($formatLabel) }} to {{ $order->customer_email }}?')"
                    title="Send {{ $formatLabel }} via Email">
                    <i class="fas fa-paper-plane"></i> Send {{ $formatLabel }}
                </button>
            </form>
        @else
            <button type="button" class="btn btn-success" disabled title="Customer email not available">
                <i class="fas fa-paper-plane"></i> Send {{ $formatLabel }}
            </button>
        @endif

        <!-- Download Invoice -->
        {{-- <a href="{{ route('admin.orders.download-invoice', $order) }}?format={{ $defaultBillFormat }}" 
           class="btn btn-info"
           title="Download {{ $formatLabel }}">
            <i class="fas fa-download"></i> Download {{ $formatLabel }}
        </a> --}}

        <!-- Send via WhatsApp -->
        @if ($order->customer_mobile)
            <button type="button" class="btn btn-whatsapp" id="whatsapp-bill-btn" data-order-id="{{ $order->id }}"
                data-customer-phone="{{ $order->customer_mobile }}" data-format="{{ $defaultBillFormat }}"
                title="Send {{ $formatLabel }} via WhatsApp">
                <i class="fab fa-whatsapp"></i> Send via WhatsApp
            </button>
        @else
            <button type="button" class="btn btn-whatsapp" disabled title="Customer mobile not available">
                <i class="fab fa-whatsapp"></i> Send via WhatsApp
            </button>
        @endif

        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <!-- Format Settings Info -->
    {{-- <div class="format-info-badge mt-2">
        <small class="text-muted">
            <i class="fas fa-cog"></i> Current format: 
            <span class="badge bg-{{ $isThermal ? 'warning' : 'primary' }}">{{ $formatLabel }}</span>
            <a href="{{ route('admin.settings.index') }}#bill-format" class="text-decoration-none ms-2">
                <i class="fas fa-edit"></i> Change Format
            </a>
        </small>
    </div> --}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart"></i> Order Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="section-title">Customer Details</h6>
                            <div class="customer-info">
                                <p class="mb-1">
                                    <strong><i class="fas fa-user"></i> Name:</strong>
                                    {{ $order->customer_name }}
                                </p>
                                <p class="mb-1">
                                    <strong><i class="fas fa-phone"></i> Mobile:</strong>
                                    <span class="text-primary">{{ $order->customer_mobile }}</span>
                                </p>
                                @if ($order->customer_email)
                                    <p class="mb-1">
                                        <strong><i class="fas fa-envelope"></i> Email:</strong>
                                        <span class="text-success">{{ $order->customer_email }}</span>
                                    </p>
                                @else
                                    <p class="mb-1">
                                        <strong><i class="fas fa-envelope"></i> Email:</strong>
                                        <span class="text-muted">Not provided</span>
                                    </p>
                                @endif
                                <p class="mb-3">
                                    <strong><i class="fas fa-id-badge"></i> Customer ID:</strong>
                                    #{{ $order->customer_id }}
                                </p>
                            </div>

                            <h6 class="section-title">Delivery Address</h6>
                            <div class="delivery-address">
                                <address class="mb-0">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    {{ $order->delivery_address }}<br>
                                    {{ $order->city }}@if ($order->state)
                                        , {{ $order->state }}
                                    @endif {{ $order->pincode }}
                                </address>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="section-title">Order Details</h6>
                            <div class="order-info">
                                <p class="mb-1">
                                    <strong><i class="fas fa-hashtag"></i> Order Number:</strong>
                                    <span class="badge bg-dark">{{ $order->order_number }}</span>
                                </p>
                                <p class="mb-1">
                                    <strong><i class="fas fa-calendar"></i> Order Date:</strong>
                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                </p>
                                <p class="mb-1">
                                    <strong><i class="fas fa-info-circle"></i> Status:</strong>
                                    <span class="badge bg-{{ $order->status_color }} status-badge">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>

                                @if ($order->shipped_at)
                                    <p class="mb-1">
                                        <strong><i class="fas fa-shipping-fast"></i> Shipped At:</strong>
                                        {{ $order->shipped_at->format('M d, Y h:i A') }}
                                    </p>
                                @endif

                                @if ($order->delivered_at)
                                    <p class="mb-1">
                                        <strong><i class="fas fa-check-circle"></i> Delivered At:</strong>
                                        {{ $order->delivered_at->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($order->notes)
                        <hr>
                        <h6 class="section-title">Customer Notes</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-sticky-note"></i>
                            {{ $order->notes }}
                        </div>
                    @endif

                    @if ($order->admin_notes)
                        <hr>
                        <h6 class="section-title">Admin Notes</h6>
                        <div class="alert alert-warning">
                            <i class="fas fa-user-shield"></i>
                            {{ $order->admin_notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Payment Information -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-credit-card"></i> Payment Information
                    </h6>
                    @php
                        $paymentStatusColor = match ($order->payment_status) {
                            'paid' => 'success',
                            'failed' => 'danger',
                            'pending' => 'warning',
                            'processing' => 'info',
                            'refunded' => 'secondary',
                            default => 'secondary',
                        };

                        $paymentStatusIcon = match ($order->payment_status) {
                            'paid' => 'fa-check-circle',
                            'failed' => 'fa-times-circle',
                            'pending' => 'fa-clock',
                            'processing' => 'fa-spinner',
                            'refunded' => 'fa-undo',
                            default => 'fa-question-circle',
                        };
                    @endphp
                    <span class="badge bg-{{ $paymentStatusColor }} fs-6 payment-status-badge">
                        <i class="fas {{ $paymentStatusIcon }}"></i>
                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @php
                                $paymentIcon = match ($order->payment_method) {
                                    'razorpay' => 'fas fa-credit-card',
                                    'cod' => 'fas fa-money-bill-wave',
                                    'bank_transfer' => 'fas fa-university',
                                    'upi' => 'fas fa-mobile-alt',
                                    'gpay' => 'fab fa-google-pay',
                                    default => 'fas fa-wallet',
                                };

                                $paymentMethodName = match ($order->payment_method) {
                                    'razorpay' => 'Online Payment (Razorpay)',
                                    'cod' => 'Cash on Delivery',
                                    'bank_transfer' => 'Bank Transfer',
                                    'upi' => 'UPI Payment',
                                    'gpay' => 'Google Pay (G Pay)',
                                    default => ucfirst(str_replace('_', ' ', $order->payment_method)),
                                };
                            @endphp

                            <div class="payment-method-info">
                                <h6 class="section-title">Payment Method</h6>
                                <p class="mb-2">
                                    <i class="{{ $paymentIcon }} text-primary"></i>
                                    <strong>{{ $paymentMethodName }}</strong>
                                </p>

                                @if ($order->payment_transaction_id)
                                    <h6 class="section-title">Transaction ID</h6>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $order->payment_transaction_id }}" readonly>
                                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                            onclick="copyToClipboard('{{ $order->payment_transaction_id }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if ($order->payment_details)
                                @php
                                    $details = is_string($order->payment_details)
                                        ? json_decode($order->payment_details, true)
                                        : $order->payment_details;
                                @endphp

                                <h6 class="section-title">Payment Details</h6>
                                <div class="payment-details">
                                    @if (isset($details['razorpay_payment_id']))
                                        <p class="mb-1">
                                            <strong>Razorpay Payment ID:</strong>
                                            <code>{{ $details['razorpay_payment_id'] }}</code>
                                        </p>
                                    @endif

                                    @if (isset($details['razorpay_order_id']))
                                        <p class="mb-1">
                                            <strong>Razorpay Order ID:</strong>
                                            <code>{{ $details['razorpay_order_id'] }}</code>
                                        </p>
                                    @endif

                                    @if (isset($details['payment_method']))
                                        <p class="mb-1">
                                            <strong>Payment Method:</strong>
                                            {{ ucfirst($details['payment_method']) }}
                                        </p>
                                    @endif

                                    @if (isset($details['bank']))
                                        <p class="mb-1">
                                            <strong>Bank:</strong>
                                            {{ $details['bank'] }}
                                        </p>
                                    @endif

                                    @if (isset($details['verified_at']))
                                        <p class="mb-1">
                                            <strong>Verified At:</strong>
                                            {{ \Carbon\Carbon::parse($details['verified_at'])->format('M d, Y h:i A') }}
                                        </p>
                                    @endif

                                    @if (isset($details['error']) && $order->payment_status === 'failed')
                                        <div class="alert alert-danger mt-2">
                                            <strong>Error:</strong> {{ $details['error'] }}
                                            @if (isset($details['error_details']))
                                                <br><small>{{ $details['error_details'] }}</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Information -->
            @if ($order->commission_enabled)
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-percentage"></i> Commission Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="section-title">Reference Details</h6>
                                <p class="mb-1">
                                    <strong><i class="fas fa-user-tie"></i> Reference Name:</strong>
                                    <span class="text-primary">{{ $order->reference_name }}</span>
                                </p>
                                <p class="mb-1">
                                    <strong><i class="fas fa-percent"></i> Commission Rate:</strong>
                                    <span
                                        class="badge bg-info">{{ number_format($order->commission_percentage, 2) }}%</span>
                                </p>
                                @if ($order->commission_notes)
                                    <p class="mb-0">
                                        <strong><i class="fas fa-sticky-note"></i> Notes:</strong>
                                        <span class="text-muted">{{ $order->commission_notes }}</span>
                                    </p>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h6 class="section-title">Commission Calculation</h6>
                                @php
                                    $commissionAmount = ($order->total * $order->commission_percentage) / 100;
                                @endphp
                                <div class="commission-calculation">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Order Total:</span>
                                        <span>₹{{ number_format($order->total, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Commission Rate:</span>
                                        <span>{{ number_format($order->commission_percentage, 2) }}%</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Commission Amount:</strong>
                                        <strong
                                            class="text-success fs-5">₹{{ number_format($commissionAmount, 2) }}</strong>
                                    </div>
                                </div>

                                @if ($order->commission)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Commission Status:
                                            <span
                                                class="badge bg-{{ $order->commission->status_color }}">{{ $order->commission->status_text }}</span>
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($order->commission)
                            <hr>
                            <div class="commission-record-info">
                                <h6 class="section-title">Commission Record</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <strong>Record ID:</strong> #{{ $order->commission->id }}<br>
                                            <strong>Created:</strong>
                                            {{ $order->commission->created_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($order->commission->paid_at)
                                            <small class="text-success">
                                                <strong>Paid At:</strong>
                                                {{ $order->commission->paid_at->format('M d, Y h:i A') }}<br>
                                                @if ($order->commission->paidBy)
                                                    <strong>Paid By:</strong> {{ $order->commission->paidBy->name }}
                                                @endif
                                            </small>
                                        @else
                                            <small class="text-warning">
                                                <i class="fas fa-clock"></i> Commission payment pending
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Note:</strong> Commission record not found. This may need to be created manually.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Enhanced Order Items -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list-alt"></i> Order Items ({{ $order->items->count() }} items)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                     <th>Quantity</th>
                                    <th>MRP</th>
                                    <th>Offer Price</th>
                                   
                                    {{-- <th>Tax</th> --}}
                                    {{-- <th>Tax Amount</th> --}}
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalDiscount = $totalMRP = 0;
                                @endphp
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($item->product && $item->product->featured_image)
                                                    <img src="{{ Storage::url($item->product->featured_image) }}"
                                                        class="me-3 rounded"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="me-3 rounded bg-light d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if ($item->product && $item->product->category)
                                                        <br><small class="text-muted">
                                                            <i class="fas fa-tag"></i>
                                                            {{ $item->product->category->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                          <td>
                                            <span class="badge bg-primary">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">₹{{ number_format($item->mrp_price, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">₹{{ number_format($item->price, 2) }}</span>
                                        </td>
                                      
                                        {{-- <td>{{ $item->tax_percentage }}%</td>
                                        <td>₹{{ number_format($item->tax_amount, 2) }}</td> --}}
                                        <td class="text-end">
                                            <strong class="text-success">₹{{ number_format($item->price*$item->quantity, 2) }}</strong>
                                        </td>
                                    </tr>
                                    @php
                                        $totalDiscount += $item->discount_amount*$item->quantity;
                                        $totalMRP += $item->mrp_price*$item->quantity;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Totals -->
                    <div class="border-top bg-light p-3">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <td class="text-end fw-semibold">Total MRP:</td>
                                        <td class="text-end">₹{{ number_format($totalMRP, 2) }}</td>
                                    </tr>
                                    @if ($totalDiscount > 0)
                                        <tr>
                                            <td class="text-end fw-semibold">Discount:</td>
                                            <td class="text-end text-success">-₹{{ number_format($totalDiscount, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    {{-- <tr>
                                        <td class="text-end fw-semibold">CGST:</td>
                                        <td class="text-end">₹{{ number_format($order->cgst_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-semibold">SGST:</td>
                                        <td class="text-end">₹{{ number_format($order->sgst_amount, 2) }}</td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <td class="text-end fw-semibold">Delivery:</td>
                                        <td class="text-end">
                                            @if ($order->delivery_charge == 0)
                                                <span class="text-success fw-bold">FREE</span>
                                            @else
                                                ₹{{ number_format($order->delivery_charge, 2) }}
                                            @endif
                                        </td>
                                    </tr> --}}
                                    <tr class="table-success">
                                        <td class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold fs-5">₹{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Order Status Update -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-edit"></i> Update Order Status
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                    <i class="fas fa-clock"></i> Pending
                                </option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                    <i class="fas fa-cogs"></i> Processing
                                </option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
                                    <i class="fas fa-shipping-fast"></i> Shipped
                                </option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle"></i> Delivered
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle"></i> Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                placeholder="Add internal notes...">{{ $order->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Status Update -->
            <div class="card mt-3 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-credit-card"></i> Update Payment Status
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select class="form-select" id="payment_status" name="payment_status" required>
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="processing" {{ $order->payment_status == 'processing' ? 'selected' : '' }}>
                                    Processing
                                </option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                    Paid
                                </option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                    Failed
                                </option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>
                                    Refunded
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_notes" class="form-label">Payment Notes</label>
                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3"
                                placeholder="Add payment-related notes..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-dollar-sign"></i> Update Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Summary Card -->
            <div class="card mt-3 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Payment Summary
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if ($order->payment_status === 'paid')
                        <div class="payment-status-display text-success">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5>Payment Successful</h5>
                            <p class="text-muted">₹{{ number_format($order->total, 2) }} received</p>
                        </div>
                    @elseif($order->payment_status === 'failed')
                        <div class="payment-status-display text-danger">
                            <i class="fas fa-times-circle fa-3x mb-3"></i>
                            <h5>Payment Failed</h5>
                            <p class="text-muted">₹{{ number_format($order->total, 2) }}</p>
                        </div>
                    @elseif($order->payment_status === 'processing')
                        <div class="payment-status-display text-info">
                            <i class="fas fa-clock fa-3x mb-3"></i>
                            <h5>Payment Processing</h5>
                            <p class="text-muted">₹{{ number_format($order->total, 2) }}</p>
                        </div>
                    @else
                        <div class="payment-status-display text-warning">
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <h5>Payment Pending</h5>
                            <p class="text-muted">₹{{ number_format($order->subtotal, 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card mt-3 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history"></i> Order Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Order Placed</h6>
                                <p class="timeline-description">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if ($order->payment_status === 'paid')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Payment Received</h6>
                                    <p class="timeline-description">Payment successful</p>
                                </div>
                            </div>
                        @elseif($order->payment_status === 'failed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Payment Failed</h6>
                                    <p class="timeline-description">Payment could not be processed</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status != 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Processing Started</h6>
                                    <p class="timeline-description">Order is being processed</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->shipped_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Shipped</h6>
                                    <p class="timeline-description">{{ $order->shipped_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->delivered_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Delivered</h6>
                                    <p class="timeline-description">{{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->status == 'cancelled')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Order Cancelled</h6>
                                    <p class="timeline-description">Order has been cancelled</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            @if ($order->customer)
                <div class="card mt-3 shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-user-circle"></i> Customer Summary
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="customer-stats">
                            <p class="mb-2">
                                <strong><i class="fas fa-shopping-bag"></i> Total Orders:</strong>
                                <span class="badge bg-info">{{ $order->customer->total_orders ?? 0 }}</span>
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-rupee-sign"></i> Total Spent:</strong>
                                <span
                                    class="text-success fw-bold">₹{{ number_format($order->customer->total_spent ?? 0, 2) }}</span>
                            </p>
                            <p class="mb-3">
                                <strong><i class="fas fa-calendar-alt"></i> Customer Since:</strong>
                                {{ $order->customer->created_at->format('M Y') }}
                            </p>
                        </div>

                        <a href="{{ route('admin.customers.show', $order->customer) }}"
                            class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-eye"></i> View Customer Details
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- WhatsApp Modal -->
    <div class="modal fade" id="whatsappMessageModal" tabindex="-1" aria-labelledby="whatsappModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="whatsappModalLabel">
                        <i class="fab fa-whatsapp"></i> Send Invoice via WhatsApp
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sending to:</label>
                        <div class="alert alert-info">
                            <i class="fas fa-phone"></i>
                            <span id="recipient-phone">{{ $order->customer_mobile }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Format:</label>
                        <div class="alert alert-secondary">
                            <i class="fas {{ $formatIcon }}"></i>
                            <span id="document-format">{{ $formatLabel }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="whatsapp-message" class="form-label fw-bold">Custom Message (Optional)</label>
                        <textarea class="form-control" id="whatsapp-message" rows="4"
                            placeholder="Leave empty to use default message template..."></textarea>
                        <small class="text-muted">The invoice will be automatically attached to the message.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="send-whatsapp-btn">
                        <i class="fab fa-whatsapp"></i> Send Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Enhanced styling for order view - COMPACT VERSION */
        .invoice-actions-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-bottom: 1rem;
        }

        .invoice-actions-group .btn {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.7rem;
            line-height: 1.1;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .invoice-actions-group .btn i {
            font-size: 0.65rem;
            margin: 0;
        }

        .invoice-actions-group .btn-secondary {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .invoice-actions-group .btn-success {
            background: #198754;
            border-color: #198754;
            color: white;
        }

        .invoice-actions-group .btn-info {
            background: #0dcaf0;
            border-color: #0dcaf0;
            color: #000;
        }

        .invoice-actions-group .btn-outline-secondary {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border: 1px solid #6c757d;
            color: #6c757d;
        }

        .invoice-actions-group .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        /* Compact WhatsApp button */
        .btn-whatsapp {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp:hover {
            background: linear-gradient(135deg, #128c7e 0%, #075e54 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(37, 211, 102, 0.4);
        }

        .btn-whatsapp:disabled {
            background: #6c757d;
            color: #fff;
            transform: none;
            box-shadow: none;
        }

        .btn-whatsapp i {
            font-size: 0.65rem;
        }

        .format-info-badge {
            margin-top: 0.5rem;
        }

        .format-info-badge small {
            font-size: 0.7rem;
        }

        .format-info-badge .badge {
            font-size: 0.65rem;
            padding: 0.2em 0.5em;
        }

        .format-info-badge a {
            font-size: 0.7rem;
        }

        .format-info-badge i {
            font-size: 0.6rem;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.25rem;
        }

        .customer-info p,
        .order-info p {
            margin-bottom: 0.5rem;
        }

        .status-badge {
            font-size: 0.85em;
            padding: 0.4em 0.6em;
        }

        .payment-status-badge {
            font-size: 1rem;
            padding: 0.5em 0.8em;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .delivery-address {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1rem;
            border-radius: 4px;
        }

        .payment-details code {
            color: #e83e8c;
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        /* Timeline styles */
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
            background: linear-gradient(to bottom, #007bff, #28a745);
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
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .timeline-description {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Card hover effects */
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        /* Table styling */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Payment status display */
        .payment-status-display i {
            opacity: 0.8;
        }

        .payment-status-display h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* Customer stats */
        .customer-stats .badge {
            font-size: 0.8em;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .invoice-actions-group {
                gap: 0.2rem;
            }

            .invoice-actions-group .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.65rem;
                flex: 1;
                min-width: 80px;
            }

            .invoice-actions-group .btn i {
                font-size: 0.6rem;
            }
        }

        /* Loading states */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading i {
            animation: fa-spin 1s infinite linear;
        }

        /* Success/error messages */
        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('Enhanced Order Page - Document ready');

            // Initialize WhatsApp functionality
            initializeWhatsApp();

            // Initialize copy functionality
            initializeCopyButtons();

            // Initialize form validations
            initializeFormValidations();
        });

        /**
         * Open print window with proper dimensions and auto-print
         */
        function openPrintWindow(url) {
            console.log('Opening print window:', url);

            // Determine window size based on format
            const isA4 = url.includes('format=a4_sheet') || (!url.includes('format=thermal'));

            let windowFeatures;
            if (isA4) {
                // A4 format - larger window
                windowFeatures =
                    'width=900,height=1100,scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no';
            } else {
                // Thermal format - smaller window
                windowFeatures =
                    'width=400,height=800,scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no';
            }

            // Center the window
            const left = (screen.width - (isA4 ? 900 : 400)) / 2;
            const top = (screen.height - (isA4 ? 1100 : 800)) / 2;
            windowFeatures += `,left=${left},top=${top}`;

            // Open the print window
            const printWindow = window.open(url, 'printWindow', windowFeatures);

            if (printWindow) {
                // Focus the print window
                printWindow.focus();

                // Show success message
                showToast('Print window opened successfully', 'success');
            } else {
                // Popup blocked
                showToast('Print window was blocked. Please allow popups and try again.', 'error');

                // Fallback: open in new tab
                window.open(url, '_blank');
            }
        }

        /**
         * Initialize WhatsApp functionality
         */
        function initializeWhatsApp() {
            console.log('Initializing WhatsApp functionality');

            // Check WhatsApp status on page load
            checkWhatsAppStatus();

            // WhatsApp button click handler
            $('#whatsapp-bill-btn').on('click', function(e) {
                e.preventDefault();
                console.log('WhatsApp button clicked');

                const orderId = $(this).data('order-id');
                const customerPhone = $(this).data('customer-phone');
                const format = $(this).data('format');

                if (!orderId || !customerPhone) {
                    showToast('Missing order or customer information', 'error');
                    return;
                }

                showWhatsAppModal(orderId, customerPhone, format);
            });

            // Modal send button
            $(document).on('click', '#send-whatsapp-btn', function() {
                const orderId = $('#whatsapp-bill-btn').data('order-id');
                const message = $('#whatsapp-message').val();
                sendWhatsAppInvoice(orderId, message);
            });
        }

        /**
         * Check WhatsApp configuration status
         */
        function checkWhatsAppStatus() {
            console.log('Checking WhatsApp status');

            $.get('{{ route('admin.orders.whatsapp-status') }}')
                .done(function(response) {
                    console.log('WhatsApp status response:', response);
                    updateWhatsAppButton(response);
                })
                .fail(function(xhr) {
                    console.error('WhatsApp status check failed:', xhr);
                    $('#whatsapp-bill-btn').prop('disabled', true)
                        .attr('title', 'WhatsApp status check failed')
                        .addClass('btn-secondary')
                        .removeClass('btn-whatsapp');
                });
        }

        /**
         * Update WhatsApp button based on status
         */
        function updateWhatsAppButton(status) {
            const btn = $('#whatsapp-bill-btn');

            if (!status.configured || !status.enabled) {
                btn.prop('disabled', true)
                    .attr('title', status.message || 'WhatsApp not available')
                    .addClass('btn-secondary')
                    .removeClass('btn-whatsapp');
            } else {
                btn.prop('disabled', false)
                    .attr('title', 'Send invoice via WhatsApp')
                    .addClass('btn-whatsapp')
                    .removeClass('btn-secondary');
            }
        }

        /**
         * Show WhatsApp modal
         */
        function showWhatsAppModal(orderId, customerPhone, format) {
            console.log('Showing WhatsApp modal', {
                orderId,
                customerPhone,
                format
            });

            // Update modal content
            $('#recipient-phone').text(customerPhone);
            $('#document-format').text(format === 'thermal' ? 'Receipt (Thermal)' : 'Invoice (A4)');
            $('#whatsapp-message').val('');

            // Show modal
            $('#whatsappMessageModal').modal('show');
        }

        /**
         * Send WhatsApp invoice
         */
        function sendWhatsAppInvoice(orderId, message) {
            console.log('Sending WhatsApp invoice', {
                orderId,
                message
            });

            const btn = $('#send-whatsapp-btn');
            const originalText = btn.html();

            // Show loading state
            btn.prop('disabled', true)
                .addClass('loading')
                .html('<i class="fas fa-spinner fa-spin"></i> Sending...');

            const url = `{{ route('admin.orders.send-whatsapp-bill', ':id') }}`.replace(':id', orderId);
            const format = $('#whatsapp-bill-btn').data('format');

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    message: message,
                    format: format,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('WhatsApp send success:', response);

                    if (response.success) {
                        showToast('Invoice sent successfully via WhatsApp!', 'success');
                        $('#whatsappMessageModal').modal('hide');

                        // Show additional success info
                        setTimeout(() => {
                            showToast(`Message sent to ${response.sent_to}`, 'info');
                        }, 1000);
                    } else {
                        showToast(response.message || 'Failed to send WhatsApp message', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('WhatsApp send error:', xhr);

                    let errorMessage = 'Failed to send WhatsApp message';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showToast(errorMessage, 'error');
                },
                complete: function() {
                    // Reset button
                    btn.prop('disabled', false)
                        .removeClass('loading')
                        .html(originalText);
                }
            });
        }

        /**
         * Initialize copy to clipboard functionality
         */
        function initializeCopyButtons() {
            // Copy transaction ID
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('Transaction ID copied to clipboard', 'success');
                }).catch(function() {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showToast('Transaction ID copied to clipboard', 'success');
                });
            };
        }

        /**
         * Initialize form validations
         */
        function initializeFormValidations() {
            // Status update form
            $('form[action*="update-status"]').on('submit', function(e) {
                const status = $('#status').val();
                const orderNumber = '{{ $order->order_number }}';

                if (!confirm(
                        `Are you sure you want to change the order status to "${status.toUpperCase()}" for order ${orderNumber}?`
                        )) {
                    e.preventDefault();
                    return false;
                }
            });

            // Payment status update form
            $('form[action*="payment-status"]').on('submit', function(e) {
                const paymentStatus = $('#payment_status').val();
                const orderNumber = '{{ $order->order_number }}';

                if (!confirm(
                        `Are you sure you want to change the payment status to "${paymentStatus.toUpperCase()}" for order ${orderNumber}?`
                        )) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        /**
         * Show toast notification
         */
        function showToast(message, type = 'info') {
            console.log('Showing toast:', {
                message,
                type
            });

            const toastClass = {
                'success': 'bg-success',
                'error': 'bg-danger',
                'warning': 'bg-warning',
                'info': 'bg-info'
            } [type] || 'bg-info';

            const iconClass = {
                'success': 'fa-check-circle',
                'error': 'fa-times-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            } [type] || 'fa-info-circle';

            const toast = $(`
        <div class="toast align-items-center text-white ${toastClass} border-0 position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${iconClass} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        onclick="$(this).closest('.toast').remove()"></button>
            </div>
        </div>
    `);

            $('body').append(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);

            // Also show browser alert for errors (as fallback)
            if (type === 'error') {
                setTimeout(() => {
                    alert('Error: ' + message);
                }, 100);
            }
        }

        /**
         * Enhanced loading states for buttons
         */
        $(document).on('click', '.btn[type="submit"]', function() {
            const btn = $(this);
            const form = btn.closest('form');

            // Add loading state
            setTimeout(() => {
                if (!form[0].checkValidity()) return;

                btn.prop('disabled', true)
                    .addClass('loading')
                    .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            }, 100);
        });

        /**
         * Auto-refresh order status (optional)
         */
        function enableAutoRefresh() {
            setInterval(() => {
                // Check if order is in a pending state that might change
                const currentStatus = '{{ $order->status }}';
                const currentPaymentStatus = '{{ $order->payment_status }}';

                if (['pending', 'processing'].includes(currentStatus) || ['pending', 'processing'].includes(
                        currentPaymentStatus)) {

                    console.log('Auto-refreshing order status...');
                    // You could implement an AJAX call to refresh just the status section
                }
            }, 30000); // Check every 30 seconds
        }

        // Uncomment to enable auto-refresh
        // enableAutoRefresh();
    </script>
@endpush
