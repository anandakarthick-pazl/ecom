<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #2c3e50;
            background: white;
        }
        
        /* Container */
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        /* Header Section */
        .invoice-header {
            position: relative;
            padding: 30px 0;
            border-bottom: 3px solid #2d5016;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .company-section, .invoice-section {
            display: table-cell;
            vertical-align: top;
        }
        
        .company-section {
            width: 60%;
        }
        
        .invoice-section {
            width: 40%;
            text-align: right;
        }
        
        .company-logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d5016;
            margin-bottom: 8px;
        }
        
        .company-details {
            font-size: 11px;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .company-details div {
            margin: 2px 0;
        }
        
        .invoice-badge {
            display: inline-block;
            background: #2d5016;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .invoice-number {
            font-size: 18px;
            font-weight: 700;
            color: #2d5016;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 12px;
            color: #6c757d;
        }
        
        /* Customer & Order Info */
        .info-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }
        
        .info-column:first-child {
            padding-left: 0;
            border-right: 1px solid #e9ecef;
        }
        
        .info-column:last-child {
            padding-right: 0;
        }
        
        .info-title {
            font-size: 13px;
            font-weight: 600;
            color: #2d5016;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-content {
            font-size: 11px;
            color: #495057;
            line-height: 1.6;
        }
        
        .info-content strong {
            color: #2c3e50;
            display: inline-block;
            min-width: 90px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 5px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-paid { background: #d4edda; color: #155724; }
        
        /* Items Table */
        .items-section {
            margin-bottom: 25px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table thead {
            background: #2d5016;
        }
        
        .items-table th {
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 12px 10px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }
        
        .items-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .items-table td {
            padding: 12px 10px;
            font-size: 11px;
            color: #495057;
            vertical-align: top;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2px;
        }
        
        .product-meta {
            font-size: 10px;
            color: #6c757d;
        }
        
        .offer-tag {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            margin-top: 3px;
        }
        
        .price-original {
            text-decoration: line-through;
            color: #adb5bd;
            font-size: 10px;
        }
        
        .price-offer {
            color: #28a745;
            font-weight: 600;
        }
        
        .discount-badge {
            background: #ffedcc;
            color: #ff8800;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Summary Section */
        .summary-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .summary-notes {
            display: table-cell;
            width: 50%;
            padding-right: 30px;
            vertical-align: top;
        }
        
        .summary-totals {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .notes-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
        }
        
        .notes-title {
            font-size: 12px;
            font-weight: 600;
            color: #2d5016;
            margin-bottom: 8px;
        }
        
        .notes-content {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .totals-table {
            width: 100%;
            font-size: 11px;
        }
        
        .totals-table tr {
            border-bottom: 1px solid #f1f3f4;
        }
        
        .totals-table td {
            padding: 8px 10px;
        }
        
        .totals-table .total-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .totals-table .total-value {
            text-align: right;
            color: #495057;
            font-weight: 600;
        }
        
        .totals-table .grand-total {
            background: #2d5016;
            color: white;
            font-size: 14px;
            font-weight: 700;
        }
        
        .totals-table .grand-total td {
            padding: 12px 10px;
            border: none;
        }
        
        .savings-highlight {
            color: #28a745;
            font-weight: 600;
        }
        
        /* Footer Section */
        .invoice-footer {
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
            margin-top: 30px;
        }
        
        .footer-content {
            display: table;
            width: 100%;
        }
        
        .footer-column {
            display: table-cell;
            width: 33.33%;
            padding: 0 15px;
            vertical-align: top;
        }
        
        .footer-title {
            font-size: 11px;
            font-weight: 600;
            color: #2d5016;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .footer-text {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .signature-line {
            border-top: 1px solid #dee2e6;
            margin-top: 40px;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        
        /* Thank You Message */
        .thank-you {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f0f8f0;
            border-radius: 8px;
        }
        
        .thank-you-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d5016;
            margin-bottom: 5px;
        }
        
        .thank-you-message {
            font-size: 11px;
            color: #6c757d;
        }
        
        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .invoice-container {
                width: 100%;
                max-width: none;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="invoice-header">
            <div class="header-content">
                <div class="company-section">
                    @if(isset($company['logo']) && $company['logo'])
                        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" class="company-logo">
                    @endif
                    <div class="company-name">{{ $company['name'] ?? 'Your Company' }}</div>
                    <div class="company-details">
                        @if(isset($company['address']) && $company['address'])
                            <div>{{ $company['address'] }}</div>
                        @endif
                        @if(isset($company['phone']) && $company['phone'])
                            <div>📞 {{ $company['phone'] }}</div>
                        @endif
                        @if(isset($company['email']) && $company['email'])
                            <div>✉️ {{ $company['email'] }}</div>
                        @endif
                        @if(isset($company['gst_number']) && $company['gst_number'])
                            <div><strong>GST:</strong> {{ $company['gst_number'] }}</div>
                        @endif
                        @if(isset($company['website']) && $company['website'])
                            <div>🌐 {{ $company['website'] }}</div>
                        @endif
                    </div>
                </div>
                <div class="invoice-section">
                    <div class="invoice-badge">TAX INVOICE</div>
                    <div class="invoice-number">{{ $order->order_number }}</div>
                    <div class="invoice-date">Date: {{ $order->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Customer & Order Information -->
        <div class="info-section">
            <div class="info-grid">
                <div class="info-column">
                    <div class="info-title">Bill To</div>
                    <div class="info-content">
                        <strong>{{ $order->customer_name }}</strong><br>
                        📱 {{ $order->customer_mobile }}<br>
                        @if($order->customer_email)
                            ✉️ {{ $order->customer_email }}<br>
                        @endif
                        @if($order->delivery_address)
                            📍 {{ $order->delivery_address }}<br>
                            {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                        @endif
                    </div>
                </div>
                <div class="info-column">
                    <div class="info-title">Order Details</div>
                    <div class="info-content">
                        <strong>Order Status:</strong> 
                        <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span><br>
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
                        <strong>Payment Status:</strong> 
                        <span class="status-badge status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span><br>
                        @if($order->transaction_id)
                            <strong>Transaction ID:</strong> {{ $order->transaction_id }}<br>
                        @endif
                        @if($order->shipped_at)
                            <strong>Shipped:</strong> {{ $order->shipped_at->format('d M Y') }}<br>
                        @endif
                        @if($order->delivered_at)
                            <strong>Delivered:</strong> {{ $order->delivered_at->format('d M Y') }}<br>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 35%">Product Details</th>
                        <th style="width: 10%" class="text-center">HSN</th>
                        <th style="width: 10%" class="text-right">MRP</th>
                        <th style="width: 10%" class="text-right">Price</th>
                        <th style="width: 8%" class="text-center">Qty</th>
                        <th style="width: 10%" class="text-center">Tax</th>
                        <th style="width: 12%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item->product_name }}</div>
                            @if($item->product && $item->product->category)
                                <div class="product-meta">Category: {{ $item->product->category->name }}</div>
                            @endif
                            @if($item->product && $item->product->sku)
                                <div class="product-meta">SKU: {{ $item->product->sku }}</div>
                            @endif
                            @if($item->offer_name)
                                <span class="offer-tag">🏷️ {{ $item->offer_name }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $item->product->hsn_code ?? '-' }}
                        </td>
                        <td class="text-right">
                            @if($item->mrp_price > 0 && $item->mrp_price > $item->price)
                                <div class="price-original">₹{{ number_format($item->mrp_price, 2) }}</div>
                            @else
                                ₹{{ number_format($item->price, 2) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->mrp_price > 0 && $item->mrp_price > $item->price)
                                <div class="price-offer">₹{{ number_format($item->price, 2) }}</div>
                                @if($item->effective_discount_percentage > 0)
                                    <span class="discount-badge">{{ number_format($item->effective_discount_percentage, 0) }}% OFF</span>
                                @endif
                            @else
                                ₹{{ number_format($item->price, 2) }}
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            @if($item->tax_percentage > 0)
                                {{ $item->tax_percentage }}%<br>
                                <small>₹{{ number_format($item->tax_amount, 2) }}</small>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>₹{{ number_format($item->total, 2) }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-notes">
                @if($order->customer_notes || $order->admin_notes)
                <div class="notes-box">
                    <div class="notes-title">Notes</div>
                    <div class="notes-content">
                        @if($order->customer_notes)
                            <strong>Customer Notes:</strong> {{ $order->customer_notes }}<br>
                        @endif
                        @if($order->admin_notes)
                            <strong>Admin Notes:</strong> {{ $order->admin_notes }}
                        @endif
                    </div>
                </div>
                @endif
            </div>
            <div class="summary-totals">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="total-value">₹{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @php
                        $totalSavings = 0;
                        foreach($order->items as $item) {
                            if(method_exists($item, 'getSavingsAttribute')) {
                                $totalSavings += $item->savings;
                            } elseif(isset($item->savings)) {
                                $totalSavings += $item->savings;
                            }
                        }
                    @endphp
                    @if($totalSavings > 0)
                    <tr>
                        <td class="total-label">Total Savings</td>
                        <td class="total-value savings-highlight">-₹{{ number_format($totalSavings, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->discount > 0)
                    <tr>
                        <td class="total-label">
                            Discount
                            @if($order->coupon_code)
                                ({{ $order->coupon_code }})
                            @endif
                        </td>
                        <td class="total-value savings-highlight">-₹{{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->tax > 0)
                    <tr>
                        <td class="total-label">Tax (GST)</td>
                        <td class="total-value">₹{{ number_format($order->tax, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->shipping_charge > 0)
                    <tr>
                        <td class="total-label">Shipping Charge</td>
                        <td class="total-value">₹{{ number_format($order->shipping_charge, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total">
                        <td>Grand Total</td>
                        <td class="text-right">₹{{ number_format($order->total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Thank You Message -->
        <div class="thank-you">
            <div class="thank-you-title">Thank You for Your Business!</div>
            <div class="thank-you-message">
                We appreciate your trust in us. If you have any questions about this invoice, please contact us.
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="invoice-footer">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-title">Terms & Conditions</div>
                    <div class="footer-text">
                        • All disputes are subject to local jurisdiction<br>
                        • Goods once sold will not be taken back<br>
                        • E & O.E.
                    </div>
                </div>
                <div class="footer-column">
                    <div class="footer-title">Bank Details</div>
                    <div class="footer-text">
                        @if(isset($company['bank_name']))
                            Bank: {{ $company['bank_name'] }}<br>
                        @endif
                        @if(isset($company['account_number']))
                            A/C: {{ $company['account_number'] }}<br>
                        @endif
                        @if(isset($company['ifsc_code']))
                            IFSC: {{ $company['ifsc_code'] }}
                        @endif
                    </div>
                </div>
                <div class="footer-column">
                    <div class="footer-title">Authorized Signatory</div>
                    <div class="footer-text">
                        <br><br><br>
                        <div class="signature-line">
                            For {{ $company['name'] ?? 'Company Name' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>