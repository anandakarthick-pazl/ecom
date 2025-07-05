<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }
        
        .invoice-info {
            text-align: right;
            flex-shrink: 0;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            font-size: 11px;
            color: #666;
        }
        
        .invoice-details strong {
            color: #333;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .billing-info {
            flex: 1;
            padding-right: 20px;
        }
        
        .billing-info h3 {
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .billing-info p {
            margin-bottom: 3px;
            font-size: 11px;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .order-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        
        .order-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        
        .order-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary-table {
            width: 300px;
            margin-left: auto;
            margin-bottom: 20px;
        }
        
        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }
        
        .summary-table .total-row {
            border-top: 2px solid #2c3e50;
            font-weight: bold;
            font-size: 12px;
            background-color: #f8f9fa;
        }
        
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
        
        .payment-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .payment-status.paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .payment-status.failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-badge.processing {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge.shipped {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-badge.delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        @media print {
            .container {
                padding: 10px;
            }
            
            .header {
                margin-bottom: 15px;
            }
            
            .billing-section {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    @if($company['logo'])
                        <img src="{{ asset('storage/' . $company['logo']) }}" alt="Company Logo" class="company-logo">
                    @endif
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-details">
                        @if($company['address'])
                            {{ $company['address'] }}<br>
                        @endif
                        @if($company['phone'])
                            Phone: {{ $company['phone'] }}<br>
                        @endif
                        @if($company['email'])
                            Email: {{ $company['email'] }}<br>
                        @endif
                        @if($company['website'])
                            Website: {{ $company['website'] }}<br>
                        @endif
                        @if($company['gst_number'])
                            GST: {{ $company['gst_number'] }}
                        @endif
                    </div>
                </div>
                <div class="invoice-info">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-details">
                        <strong>Invoice No:</strong> {{ $order->order_number }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('d M Y') }}<br>
                        <strong>Status:</strong> <span class="status-badge {{ $order->status }}">{{ ucfirst($order->status) }}</span><br>
                        <strong>Payment:</strong> <span class="payment-status {{ $order->payment_status ?? 'pending' }}">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-info">
                <h3>Bill To</h3>
                <p><strong>{{ $order->customer_name }}</strong></p>
                @if($order->customer_email)
                    <p>Email: {{ $order->customer_email }}</p>
                @endif
                @if($order->customer_mobile)
                    <p>Phone: {{ $order->customer_mobile }}</p>
                @endif
                @if($order->delivery_address)
                    <p>{{ $order->delivery_address }}</p>
                @endif
                @if($order->city || $order->state || $order->pincode)
                    <p>
                        {{ $order->city }}
                        @if($order->state && $order->city), {{ $order->state }}@endif
                        @if($order->pincode) - {{ $order->pincode }}@endif
                    </p>
                @endif
            </div>
            <div class="billing-info">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                @if($order->shipped_at)
                    <p><strong>Shipped Date:</strong> {{ $order->shipped_at->format('d M Y, h:i A') }}</p>
                @endif
                @if($order->delivered_at)
                    <p><strong>Delivered Date:</strong> {{ $order->delivered_at->format('d M Y, h:i A') }}</p>
                @endif
                @if($order->payment_transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $order->payment_transaction_id }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <table class="order-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Product</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Rate</th>
                    <th width="15%" class="text-right">Tax</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product_sku)
                            <br><small>SKU: {{ $item->product_sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $company['currency'] }}{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $company['currency'] }}{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $company['currency'] }}{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order Summary -->
        <table class="summary-table">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">{{ $company['currency'] }}{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td><strong>Discount:</strong></td>
                <td class="text-right">-{{ $company['currency'] }}{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            @if($order->delivery_charge > 0)
            <tr>
                <td><strong>Delivery Charge:</strong></td>
                <td class="text-right">{{ $company['currency'] }}{{ number_format($order->delivery_charge, 2) }}</td>
            </tr>
            @endif
            @if($order->cgst_amount > 0)
            <tr>
                <td><strong>CGST:</strong></td>
                <td class="text-right">{{ $company['currency'] }}{{ number_format($order->cgst_amount, 2) }}</td>
            </tr>
            @endif
            @if($order->sgst_amount > 0)
            <tr>
                <td><strong>SGST:</strong></td>
                <td class="text-right">{{ $company['currency'] }}{{ number_format($order->sgst_amount, 2) }}</td>
            </tr>
            @endif
            @if($order->tax_amount > 0)
            <tr>
                <td><strong>{{ $company['tax_name'] }}:</strong></td>
                <td class="text-right">{{ $company['currency'] }}{{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>{{ $company['currency'] }}{{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>

        <!-- Notes -->
        @if($order->notes)
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 14px; color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px;">Notes</h3>
            <p style="font-size: 11px; color: #666;">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice. For any queries, please contact us at {{ $company['email'] ?? $company['phone'] }}.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
