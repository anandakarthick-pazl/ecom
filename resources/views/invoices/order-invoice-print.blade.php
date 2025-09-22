<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                font-size: 12pt;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            @page {
                margin: 0.5in;
            }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            color: #333;
        }
        
        .invoice-header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-logo {
            max-height: 80px;
            max-width: 200px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            text-align: right;
        }
        
        .invoice-details {
            text-align: right;
            color: #666;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box h5 {
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .table-invoice {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        .table-invoice thead {
            background: #2c3e50;
            color: white;
        }
        
        .table-invoice th {
            font-weight: 600;
            font-size: 13px;
            padding: 12px;
        }
        
        .table-invoice td {
            padding: 10px;
            font-size: 12px;
        }
        
        .table-invoice tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .summary-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .total-row {
            background: #2c3e50;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .terms-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 30px;
            border-radius: 5px;
        }
        
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 30%;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(255, 0, 0, 0.1);
            font-weight: bold;
            z-index: -1;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .strikethrough {
            text-decoration: line-through;
            color: #999;
        }
        
        .text-success {
            color: #28a745 !important;
        }
    </style>
</head>
<body onload="window.print()">
    @if($order->payment_status !== 'paid')
        <div class="watermark">UNPAID</div>
    @endif
    
    <button class="btn btn-primary no-print print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Print Invoice
    </button>
    
    <div class="container">
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    @if(isset($company->logo) && $company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->company_name ?? 'Company' }}" class="company-logo mb-3">
                    @endif
                    <div class="company-name">{{ $company->company_name ?? config('app.name', 'Your Company Name') }}</div>
                    <div class="company-details">
                        @if($company)
                            {{ $company->address ?? 'Company Address' }}<br>
                            @if(isset($company->city) || isset($company->state) || isset($company->pincode))
                                {{ $company->city ?? '' }}{{ isset($company->city) && isset($company->state) ? ', ' : '' }}{{ $company->state ?? '' }}{{ isset($company->pincode) ? ' - ' . $company->pincode : '' }}<br>
                            @endif
                            @if(isset($company->phone))
                                <strong>Phone:</strong> {{ $company->phone }}<br>
                            @endif
                            @if(isset($company->email))
                                <strong>Email:</strong> {{ $company->email }}<br>
                            @endif
                            @if(isset($company->gst_number))
                                <strong>GST:</strong> {{ $company->gst_number }}<br>
                            @endif
                            @if(isset($company->website))
                                <strong>Website:</strong> {{ $company->website }}
                            @endif
                        @else
                            {{ config('app.name', 'Company Name') }}<br>
                            Address Line 1<br>
                            City, State - Pincode<br>
                            Phone: +91 XXXXXXXXXX<br>
                            Email: info@company.com
                        @endif
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-details">
                        <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
                        <strong>Time:</strong> {{ $order->created_at->format('h:i A') }}<br>
                        <div class="mt-2">
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">PAID</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning">PENDING</span>
                            @else
                                <span class="badge bg-danger">{{ strtoupper($order->payment_status ?? 'UNPAID') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-box">
                    <h5>Bill To:</h5>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}<br>
                    <strong>Phone:</strong> {{ $order->customer_mobile }}<br>
                    @if($order->customer_email)
                        <strong>Email:</strong> {{ $order->customer_email }}
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <h5>Ship To:</h5>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}<br>
                    <strong>Phone:</strong> {{ $order->customer_mobile }}
                </div>
            </div>
        </div>
        
        <table class="table table-bordered table-invoice">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Product Description</th>
                    <th width="10%" class="text-center">HSN/SAC</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="12%" class="text-end">Unit Price</th>
                    <th width="10%" class="text-center">GST %</th>
                    <th width="18%" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalOriginalAmount = 0;
                    $totalSavings = 0;
                @endphp
                
                @foreach($order->items as $index => $item)
                @php
                    // Get product and offer details
                    $product = $item->product ?? null;
                    $originalPrice = $product ? $product->price : $item->price;
                    $effectivePrice = $item->price;
                    $hasDiscount = $originalPrice > $effectivePrice;
                    $itemSavings = $hasDiscount ? ($originalPrice - $effectivePrice) * $item->quantity : 0;
                    
                    $totalOriginalAmount += $originalPrice * $item->quantity;
                    $totalSavings += $itemSavings;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if(isset($item->product_sku) && $item->product_sku)
                            <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                        @endif
                        @if($hasDiscount)
                            <br><small class="text-success">
                                Discount: {{ round((($originalPrice - $effectivePrice) / $originalPrice) * 100) }}% OFF
                            </small>
                        @endif
                    </td>
                    <td class="text-center">{{ $product->hsn_code ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">
                        @if($hasDiscount)
                            <span class="strikethrough">₹{{ number_format($originalPrice, 2) }}</span><br>
                            <strong>₹{{ number_format($effectivePrice, 2) }}</strong>
                        @else
                            ₹{{ number_format($item->price, 2) }}
                        @endif
                    </td>
                    <td class="text-center">{{ $item->tax_percentage ?? 0 }}%</td>
                    <td class="text-end">
                        @if($hasDiscount)
                            <span class="strikethrough">₹{{ number_format($originalPrice * $item->quantity, 2) }}</span><br>
                        @endif
                        <strong>₹{{ number_format($item->total, 2) }}</strong>
                        @if($itemSavings > 0)
                            <br><small class="text-success">Saved: ₹{{ number_format($itemSavings, 2) }}</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="row">
            <div class="col-md-6">
                @if($order->notes)
                    <div class="alert alert-info">
                        <strong>Order Notes:</strong><br>
                        {{ $order->notes }}
                    </div>
                @endif
                
                @if($order->cgst_amount > 0 || $order->sgst_amount > 0)
                <div class="info-box">
                    <h6>GST Breakdown (Included in Total):</h6>
                    <div class="row">
                        <div class="col-6">CGST (9%):</div>
                        <div class="col-6 text-end">₹{{ number_format($order->cgst_amount, 2) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6">SGST (9%):</div>
                        <div class="col-6 text-end">₹{{ number_format($order->sgst_amount, 2) }}</div>
                    </div>
                    <div class="row fw-bold border-top pt-2 mt-2">
                        <div class="col-6">Total GST:</div>
                        <div class="col-6 text-end">₹{{ number_format($order->cgst_amount + $order->sgst_amount, 2) }}</div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="col-md-6">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>
                            @if($totalSavings > 0)
                                <span class="strikethrough">₹{{ number_format($totalOriginalAmount, 2) }}</span>
                            @endif
                            ₹{{ number_format($order->subtotal, 2) }}
                        </span>
                    </div>
                    
                    @if($totalSavings > 0)
                    <div class="summary-row text-success">
                        <span>Total Savings:</span>
                        <span><strong>-₹{{ number_format($totalSavings, 2) }}</strong></span>
                    </div>
                    @endif
                    
                    @if($order->discount > 0)
                    <div class="summary-row text-success">
                        <span>Coupon Discount:</span>
                        <span>-₹{{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    
                    @if(isset($order->delivery_charge) && $order->delivery_charge > 0)
                    <div class="summary-row">
                        <span>Delivery Charge:</span>
                        <span>₹{{ number_format($order->delivery_charge, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="total-row d-flex justify-content-between">
                        <span>Grand Total:</span>
                        <span>₹{{ number_format($order->total, 2) }}</span>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}<br>
                        <strong>Payment Status:</strong> 
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success">PAID</span>
                        @else
                            <span class="badge bg-warning">{{ strtoupper($order->payment_status ?? 'PENDING') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="terms-box">
            <h6>Terms & Conditions:</h6>
            <small>
                1. Goods once sold will not be taken back or exchanged unless defective.<br>
                2. All disputes are subject to {{ isset($company->city) ? $company->city : 'local' }} jurisdiction only.<br>
                3. Payment should be made within the due date mentioned.<br>
                4. Interest @18% p.a. will be charged on overdue payments.<br>
                5. E. & O.E.
            </small>
        </div>
        
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <small>Customer Signature</small>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <small>Prepared By</small>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <small>Authorized Signature</small>
            </div>
        </div>
        
        <div class="text-center mt-5 pb-3">
            <strong>Thank you for your business!</strong><br>
            <small>This is a computer-generated invoice and does not require a physical signature.</small>
        </div>
    </div>
</body>
</html>
