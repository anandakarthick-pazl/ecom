<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        /* Base Styles for PDF Generation */
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        
        .invoice-container {
            width: 100%;
            padding: 20px;
        }
        
        /* Header */
        .invoice-header {
            border-bottom: 2px solid #2d5016;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header-table {
            width: 100%;
        }
        
        .company-info {
            text-align: left;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 5px;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        /* Info Section */
        .info-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            vertical-align: top;
            padding: 5px;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th {
            background-color: #2d5016;
            color: white;
            padding: 10px 5px;
            text-align: left;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 20px;
        }
        
        .summary-table {
            width: 300px;
            float: right;
        }
        
        .summary-table td {
            padding: 5px;
            font-size: 11px;
        }
        
        .summary-table .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #2d5016;
            color: white;
        }
        
        .summary-table .total-row td {
            padding: 8px;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }
        
        .clearfix {
            clear: both;
        }
        
        /* Thank You */
        .thank-you {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background-color: #f0f8f0;
            border: 1px solid #d4edda;
        }
        
        .thank-you-title {
            font-size: 16px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <table class="header-table">
                <tr>
                    <td class="company-info" style="width: 60%;">
                        <div class="company-name">{{ $company['name'] ?? 'Your Company' }}</div>
                        <div>{{ $company['address'] ?? '' }}</div>
                        @if(isset($company['phone']) && $company['phone'])
                            <div>Phone: {{ $company['phone'] }}</div>
                        @endif
                        @if(isset($company['email']) && $company['email'])
                            <div>Email: {{ $company['email'] }}</div>
                        @endif
                        @if(isset($company['gst_number']) && $company['gst_number'])
                            <div>GST: {{ $company['gst_number'] }}</div>
                        @endif
                    </td>
                    <td class="invoice-info" style="width: 40%;">
                        <div class="invoice-title">TAX INVOICE</div>
                        <div><strong>{{ $order->order_number }}</strong></div>
                        <div>Date: {{ $order->created_at->format('d M Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Customer & Order Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="section-title">Bill To</div>
                        <strong>{{ $order->customer_name }}</strong><br>
                        Phone: {{ $order->customer_mobile }}<br>
                        @if($order->customer_email)
                            Email: {{ $order->customer_email }}<br>
                        @endif
                        @if($order->delivery_address)
                            {{ $order->delivery_address }}<br>
                            {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                        @endif
                    </td>
                    <td style="width: 50%;">
                        <div class="section-title">Order Details</div>
                        <strong>Status:</strong> {{ ucfirst($order->status) }}<br>
                        <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
                        <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}<br>
                        @if($order->transaction_id)
                            <strong>Transaction ID:</strong> {{ $order->transaction_id }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Product</th>
                    <th style="width: 10%;" class="text-center">HSN</th>
                    <th style="width: 10%;" class="text-right">MRP</th>
                    <th style="width: 10%;" class="text-right">Price</th>
                    <th style="width: 8%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-center">Tax</th>
                    <th style="width: 12%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product && $item->product->category)
                            <br><small>{{ $item->product->category->name }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->product && $item->product->hsn_code)
                            {{ $item->product->hsn_code }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if(isset($item->mrp_price) && $item->mrp_price > 0)
                            Rs.{{ number_format($item->mrp_price, 2) }}
                        @else
                            Rs.{{ number_format($item->price, 2) }}
                        @endif
                    </td>
                    <td class="text-right">
                        Rs.{{ number_format($item->price, 2) }}
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">
                        @if(isset($item->tax_percentage) && $item->tax_percentage > 0)
                            {{ $item->tax_percentage }}%
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        <strong>Rs.{{ number_format($item->total, 2) }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">Rs.{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-Rs.{{ number_format($order->discount, 2) }}</td>
                </tr>
                @endif
                @if($order->tax > 0)
                <tr>
                    <td>Tax (GST):</td>
                    <td class="text-right">Rs.{{ number_format($order->tax, 2) }}</td>
                </tr>
                @endif
                @if($order->shipping_charge > 0)
                <tr>
                    <td>Shipping:</td>
                    <td class="text-right">Rs.{{ number_format($order->shipping_charge, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Grand Total:</td>
                    <td class="text-right">Rs.{{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <div class="clearfix"></div>
        
        <!-- Thank You -->
        <div class="thank-you">
            <div class="thank-you-title">Thank You for Your Business!</div>
            <div>We appreciate your trust in us.</div>
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 33%;">
                        <strong>Terms & Conditions</strong><br>
                        All disputes subject to local jurisdiction<br>
                        Goods once sold will not be taken back
                    </td>
                    <td style="width: 33%;">
                        @if(isset($company['bank_name']) || isset($company['account_number']))
                            <strong>Bank Details</strong><br>
                            @if(isset($company['bank_name']))
                                Bank: {{ $company['bank_name'] }}<br>
                            @endif
                            @if(isset($company['account_number']))
                                A/C: {{ $company['account_number'] }}<br>
                            @endif
                            @if(isset($company['ifsc_code']))
                                IFSC: {{ $company['ifsc_code'] }}
                            @endif
                        @endif
                    </td>
                    <td style="width: 33%; text-align: right;">
                        <strong>Authorized Signatory</strong><br><br><br>
                        _____________________<br>
                        For {{ $company['name'] ?? 'Company' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>