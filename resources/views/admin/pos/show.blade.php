@extends('admin.layouts.app')

@section('title', 'Sale Details')
@section('page_title', 'Sale Details - ' . $sale->invoice_number)

@section('page_actions')
<a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Sales
</a>
<a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-primary" target="_blank">
    <i class="fas fa-receipt"></i> View Receipt
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sale Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Invoice Number:</dt>
                            <dd class="col-sm-8">{{ $sale->invoice_number }}</dd>
                            
                            <dt class="col-sm-4">Sale Date:</dt>
                            <dd class="col-sm-8">{{ $sale->created_at->format('M d, Y h:i A') }}</dd>
                            
                            <dt class="col-sm-4">Cashier:</dt>
                            <dd class="col-sm-8">{{ $sale->cashier->name }}</dd>
                            
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-{{ $sale->status_color }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6">
                        <dl class="row">
                            @if($sale->customer_name)
                                <dt class="col-sm-4">Customer:</dt>
                                <dd class="col-sm-8">{{ $sale->customer_name }}</dd>
                            @endif
                            
                            @if($sale->customer_phone)
                                <dt class="col-sm-4">Phone:</dt>
                                <dd class="col-sm-8">{{ $sale->customer_phone }}</dd>
                            @endif
                            
                            <dt class="col-sm-4">Payment Method:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                </span>
                            </dd>
                            
                            <dt class="col-sm-4">Total Items:</dt>
                            <dd class="col-sm-8">{{ $sale->total_items }}</dd>
                        </dl>
                    </div>
                </div>
                
                @if($sale->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Notes:</strong>
                            <p class="text-muted">{{ $sale->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sale Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Sale Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Tax %</th>
                                <th class="text-end">Tax Amount</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        @if($item->product->category)
                                            <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ $item->tax_percentage }}%</td>
                                    <td class="text-end">₹{{ number_format($item->tax_amount, 2) }}</td>
                                    <td class="text-end">₹{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payment Summary</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-6">Subtotal:</dt>
                    <dd class="col-sm-6 text-end">₹{{ number_format($sale->subtotal, 2) }}</dd>
                    
                    @if($sale->tax_amount > 0)
                        <dt class="col-sm-6">CGST:</dt>
                        <dd class="col-sm-6 text-end">₹{{ number_format($sale->cgst_amount, 2) }}</dd>
                        
                        <dt class="col-sm-6">SGST:</dt>
                        <dd class="col-sm-6 text-end">₹{{ number_format($sale->sgst_amount, 2) }}</dd>
                    @endif
                    
                    @if($sale->discount_amount > 0)
                        <dt class="col-sm-6">Discount:</dt>
                        <dd class="col-sm-6 text-end text-success">-₹{{ number_format($sale->discount_amount, 2) }}</dd>
                    @endif
                    
                    <hr>
                    
                    <dt class="col-sm-6"><strong>Total:</strong></dt>
                    <dd class="col-sm-6 text-end"><strong>₹{{ number_format($sale->total_amount, 2) }}</strong></dd>
                    
                    <hr>
                    
                    <dt class="col-sm-6">Paid Amount:</dt>
                    <dd class="col-sm-6 text-end">₹{{ number_format($sale->paid_amount, 2) }}</dd>
                    
                    @if($sale->change_amount > 0)
                        <dt class="col-sm-6">Change:</dt>
                        <dd class="col-sm-6 text-end text-info">₹{{ number_format($sale->change_amount, 2) }}</dd>
                    @endif
                </dl>
                
                <div class="text-center mt-3">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Payment Status: {{ ucfirst($sale->payment_status) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        @if($sale->status === 'completed')
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.pos.receipt', $sale) }}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-print"></i> Print Receipt
                        </a>
                        
                        <button type="button" class="btn btn-warning" onclick="showRefundModal({{ $sale->id }})">
                            <i class="fas fa-undo"></i> Process Refund
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
