@extends('layouts.admin')

@section('title', 'Enhanced Invoice Preview - Sale #' . $sale->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>
                        Enhanced Invoice Preview - Sale #{{ $sale->id }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.pos.sales') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Sales
                        </a>
                        <a href="{{ route('admin.pos.download-bill', $sale) }}" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </a>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Enhanced Invoice Design -->
                    <div class="invoice-container" id="invoice-content">
                        <!-- Invoice Header -->
                        <div class="invoice-header">
                            <div class="row">
                                <div class="col-md-6">
                                    @if($globalCompany->company_logo)
                                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                             alt="{{ $globalCompany->company_name }}" 
                                             class="company-logo">
                                    @endif
                                    <h3 class="company-name">{{ $globalCompany->company_name ?? 'Your Company' }}</h3>
                                    @if($globalCompany->company_address)
                                        <p class="company-address">{{ $globalCompany->company_address }}</p>
                                    @endif
                                    @if($globalCompany->company_phone)
                                        <p class="company-contact">Phone: {{ $globalCompany->company_phone }}</p>
                                    @endif
                                    @if($globalCompany->company_email)
                                        <p class="company-contact">Email: {{ $globalCompany->company_email }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6 text-end">
                                    <h2 class="invoice-title">INVOICE</h2>
                                    <div class="invoice-meta">
                                        <p><strong>Invoice #:</strong> {{ $sale->invoice_number ?? $sale->id }}</p>
                                        <p><strong>Date:</strong> {{ $sale->created_at->format('d M Y, h:i A') }}</p>
                                        <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
                                        @if($sale->customer)
                                            <p><strong>Customer:</strong> {{ $sale->customer->name }}</p>
                                            @if($sale->customer->phone)
                                                <p><strong>Phone:</strong> {{ $sale->customer->phone }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Invoice Items Table -->
                        <div class="invoice-items">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product->name }}</strong>
                                            @if($item->product->category)
                                                <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->price, 2) }}</td>
                                        <td>₹{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @if($sale->discount_amount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><strong>₹{{ number_format($sale->subtotal, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                        <td><strong>-₹{{ number_format($sale->discount_amount, 2) }}</strong></td>
                                    </tr>
                                    @endif
                                    @if($sale->tax_amount > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                        <td><strong>₹{{ number_format($sale->tax_amount, 2) }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr class="table-success">
                                        <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>₹{{ number_format($sale->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Invoice Footer -->
                        <div class="invoice-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Terms & Conditions:</h6>
                                    <ul class="small">
                                        <li>Payment is due within 30 days of invoice date</li>
                                        <li>Late payments may incur additional charges</li>
                                        <li>Goods once sold cannot be returned</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="small">
                                        <strong>Thank you for your business!</strong><br>
                                        Generated on {{ now()->format('d M Y, h:i A') }}
                                    </p>
                                    @if($globalCompany->gst_number)
                                        <p class="small">GST No: {{ $globalCompany->gst_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.invoice-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.company-logo {
    max-height: 80px;
    margin-bottom: 1rem;
}

.company-name {
    color: #2c3e50;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.company-address, .company-contact {
    margin-bottom: 0.25rem;
    color: #666;
}

.invoice-title {
    color: #e74c3c;
    font-weight: bold;
    margin-bottom: 1rem;
}

.invoice-meta p {
    margin-bottom: 0.5rem;
}

.invoice-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #eee;
}

.invoice-items {
    margin-bottom: 2rem;
}

.invoice-footer {
    border-top: 1px solid #eee;
    padding-top: 1rem;
}

@media print {
    .card-header, .btn-group {
        display: none !important;
    }
    
    .invoice-container {
        box-shadow: none;
        padding: 0;
    }
    
    body {
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endpush

@endsection