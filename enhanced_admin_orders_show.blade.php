@extends('admin.layouts.app')

@section('title', 'Order: ' . $order->order_number)
@section('page_title', 'Order: ' . $order->order_number)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-secondary">
        <i class="fas fa-print"></i> Print Invoice
    </a>
    @if($order->customer_email)
        <form action="{{ route('admin.orders.send-invoice', $order) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success" 
                    onclick="return confirm('Send invoice PDF to {{ $order->customer_email }}?')">
                <i class="fas fa-paper-plane"></i> Send Invoice
            </button>
        </form>
    @endif
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>
@endsection

@section('content')
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
                        <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                        <p class="mb-1"><strong>Mobile:</strong> {{ $order->customer_mobile }}</p>
                        @if($order->customer_email)
                            <p class="mb-1">
                                <strong>Email:</strong> 
                                <span class="text-success">
                                    <i class="fas fa-envelope"></i> {{ $order->customer_email }}
                                </span>
                            </p>
                        @else
                            <p class="mb-1">
                                <strong>Email:</strong> 
                                <span class="text-muted">
                                    <i class="fas fa-times-circle"></i> Not provided
                                </span>
                            </p>
                        @endif
                        <p class="mb-3"><strong>Customer ID:</strong> #{{ $order->customer_id }}</p>
                        
                        <h6>Delivery Address</h6>
                        <address>
                            {{ $order->delivery_address }}<br>
                            {{ $order->city }}@if($order->state), {{ $order->state }}@endif {{ $order->pincode }}
                        </address>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Order Details</h6>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                        </p>
                        
                        @if($order->shipped_at)
                            <p class="mb-1"><strong>Shipped At:</strong> {{ $order->shipped_at->format('M d, Y h:i A') }}</p>
                        @endif
                        
                        @if($order->delivered_at)
                            <p class="mb-1"><strong>Delivered At:</strong> {{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                        @endif
                    </div>
                </div>
                
                @if($order->notes)
                <hr>
                <h6>Customer Notes</h6>
                <p class="text-muted">{{ $order->notes }}</p>
                @endif
                
                @if($order->admin_notes)
                <hr>
                <h6>Admin Notes</h6>
                <p class="text-muted">{{ $order->admin_notes }}</p>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Payment Information</h6>
                @php
                    $paymentStatusColor = match($order->payment_status) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'refunded' => 'secondary',
                        default => 'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $paymentStatusColor }} fs-6">
                    @if($order->payment_status === 'paid')
                        <i class="fas fa-check-circle"></i> Payment Success
                    @elseif($order->payment_status === 'failed')
                        <i class="fas fa-times-circle"></i> Payment Failed
                    @elseif($order->payment_status === 'processing')
                        <i class="fas fa-clock"></i> Payment Processing
                    @elseif($order->payment_status === 'refunded')
                        <i class="fas fa-undo"></i> Payment Refunded
                    @else
                        <i class="fas fa-exclamation-circle"></i> Payment Pending
                    @endif
                </span>
            </div>
            <div class="card-body">
                @php
                    $paymentIcon = match($order->payment_method) {
                        'razorpay' => 'fas fa-credit-card',
                        'cod' => 'fas fa-money-bill-wave',
                        'bank_transfer' => 'fas fa-university',
                        'upi' => 'fas fa-mobile-alt',
                        default => 'fas fa-wallet'
                    };
                    
                    $paymentMethodName = match($order->payment_method) {
                        'razorpay' => 'Online Payment (Razorpay)',
                        'cod' => 'Cash on Delivery',
                        'bank_transfer' => 'Bank Transfer',
                        'upi' => 'UPI Payment',
                        default => ucfirst(str_replace('_', ' ', $order->payment_method))
                    };
                @endphp
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payment Method</h6>
                        <p class="mb-2">
                            <i class="{{ $paymentIcon }} text-primary"></i> 
                            <strong>{{ $paymentMethodName }}</strong>
                        </p>
                        
                        <h6>Payment Status</h6>
                        <p class="mb-2">
                            <span class="badge bg-{{ $paymentStatusColor }} p-2">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                            </span>
                        </p>
                        
                        @if($order->payment_transaction_id)
                            <h6>Transaction ID</h6>
                            <p class="mb-2">
                                <code>{{ $order->payment_transaction_id }}</code>
                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $order->payment_transaction_id }}')">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </p>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        @if($order->payment_details)
                            @php
                                $details = is_string($order->payment_details) ? json_decode($order->payment_details, true) : $order->payment_details;
                            @endphp
                            
                            <h6>Payment Details</h6>
                            <div class="payment-details">
                                @if(isset($details['razorpay_payment_id']))
                                    <p class="mb-1">
                                        <strong>Razorpay Payment ID:</strong> 
                                        <code>{{ $details['razorpay_payment_id'] }}</code>
                                    </p>
                                @endif
                                
                                @if(isset($details['razorpay_order_id']))
                                    <p class="mb-1">
                                        <strong>Razorpay Order ID:</strong> 
                                        <code>{{ $details['razorpay_order_id'] }}</code>
                                    </p>
                                @endif
                                
                                @if(isset($details['payment_method']))
                                    <p class="mb-1">
                                        <strong>Payment Method:</strong> 
                                        {{ ucfirst($details['payment_method']) }}
                                    </p>
                                @endif
                                
                                @if(isset($details['bank']))
                                    <p class="mb-1">
                                        <strong>Bank:</strong> 
                                        {{ $details['bank'] }}
                                    </p>
                                @endif
                                
                                @if(isset($details['wallet']))
                                    <p class="mb-1">
                                        <strong>Wallet:</strong> 
                                        {{ $details['wallet'] }}
                                    </p>
                                @endif
                                
                                @if(isset($details['verified_at']))
                                    <p class="mb-1">
                                        <strong>Verified At:</strong> 
                                        {{ \Carbon\Carbon::parse($details['verified_at'])->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                                
                                @if(isset($details['error']) && $order->payment_status === 'failed')
                                    <div class="alert alert-danger mt-2">
                                        <strong>Error:</strong> {{ $details['error'] }}
                                        @if(isset($details['error_details']))
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
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->featured_image)
                                            <img src="{{ Storage::url($item->product->featured_image) }}" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $item->product_name }}</strong>
                                            @if($item->product)
                                                <br><small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->tax_percentage }}%</td>
                                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                <td><strong>₹{{ number_format($order->subtotal, 2) }}</strong></td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                                <td><strong class="text-success">-₹{{ number_format($order->discount, 2) }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="5" class="text-end"><strong>CGST:</strong></td>
                                <td><strong>₹{{ number_format($order->cgst_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>SGST:</strong></td>
                                <td><strong>₹{{ number_format($order->sgst_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Delivery Charge:</strong></td>
                                <td><strong>
                                    @if($order->delivery_charge == 0)
                                        <span class="text-success">FREE</span>
                                    @else
                                        ₹{{ number_format($order->delivery_charge, 2) }}
                                    @endif
                                </strong></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                <td><strong>₹{{ number_format($order->total, 2) }}</strong></td>
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
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Add internal notes...">{{ $order->admin_notes }}</textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Update Status
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
                @if($order->payment_status === 'paid')
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">Payment Successful</h5>
                    <p class="text-muted">₹{{ number_format($order->total, 2) }} received</p>
                @elseif($order->payment_status === 'failed')
                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Payment Failed</h5>
                    <p class="text-muted">Amount: ₹{{ number_format($order->total, 2) }}</p>
                @elseif($order->payment_status === 'processing')
                    <i class="fas fa-clock fa-3x text-info mb-3"></i>
                    <h5 class="text-info">Payment Processing</h5>
                    <p class="text-muted">Amount: ₹{{ number_format($order->total, 2) }}</p>
                @else
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Payment Pending</h5>
                    <p class="text-muted">Amount: ₹{{ number_format($order->total, 2) }}</p>
                @endif
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
                            <p class="timeline-description">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($order->payment_status === 'paid')
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
                    
                    @if($order->status != 'pending')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Processing Started</h6>
                            <p class="timeline-description">Order is being processed</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->shipped_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Shipped</h6>
                            <p class="timeline-description">{{ $order->shipped_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->delivered_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Order Delivered</h6>
                            <p class="timeline-description">{{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->status == 'cancelled')
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
        @if($order->customer)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Customer Summary</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Total Orders:</strong> {{ $order->customer->total_orders }}</p>
                <p class="mb-2"><strong>Total Spent:</strong> ₹{{ number_format($order->customer->total_spent, 2) }}</p>
                <p class="mb-0"><strong>Customer Since:</strong> {{ $order->customer->created_at->format('M Y') }}</p>
                
                <hr>
                
                <a href="{{ route('admin.customers.show', $order->customer) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-user"></i> View Customer Details
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Transaction ID copied to clipboard', 'success');
    });
}
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
</style>
@endsection