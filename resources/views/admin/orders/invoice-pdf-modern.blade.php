<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15px;
            margin-top: 20px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #2c3e50;
            background: white;
        }
        
        /* Modern Header Design */
        .invoice-header {
            background: linear-gradient(135deg, {{ $company['primary_color'] ?? '#2c3e50' }} 0%, {{ $company['secondary_color'] ?? '#34495e' }} 100%);
            color: white;
            padding: 25px;
            margin: -15px -15px 0 -15px;
            position: relative;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .company-section {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        
        .invoice-section {
            display: table-cell;
            vertical-align: middle;
            width: 40%;
            text-align: right;
        }
        
        .company-logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-tagline {
            font-size: 12px;
            opacity: 0.9;
            font-style: italic;
        }
        
        .invoice-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .invoice-number {
            font-size: 18px;
            margin-top: 5px;
        }
        
        .invoice-date {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Customer & Order Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin: 25px 0;
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .info-box {
            display: table-cell;
            width: 50%;
            padding: 20px;
            vertical-align: top;
        }
        
        .info-box:first-child {
            border-right: 2px solid #e9ecef;
        }
        
        .info-title {
            font-size: 16px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2c3e50' }};
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-row {
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .info-label {
            display: inline-block;
            width: 100px;
            font-weight: 600;
            color: #6c757d;
        }
        
        .info-value {
            color: #2c3e50;
        }
        
        /* Modern Items Table */
        .items-container {
            margin: 25px 0;
        }
        
        .items-header {
            background: {{ $company['primary_color'] ?? '#2c3e50' }};
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 10px 10px 0 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }
        
        .items-table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .items-table td {
            padding: 12px 10px;
            font-size: 13px;
            border-bottom: 1px solid #f1f3f5;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .items-table tr:hover {
            background: #f8f9fa;
        }
        
        .product-name {
            font-weight: 600;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .product-details {
            font-size: 11px;
            color: #6c757d;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .price-original {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 11px;
        }
        
        .price-offer {
            color: #28a745;
            font-weight: 600;
            font-size: 14px;
        }
        
        .discount-badge {
            background: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 30px;
            display: table;
            width: 100%;
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
        
        .totals-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .total-label {
            display: table-cell;
            width: 60%;
            padding-right: 10px;
            color: #6c757d;
        }
        
        .total-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .total-row.discount {
            color: #dc3545;
        }
        
        .total-row.tax {
            color: #007bff;
        }
        
        .total-row.grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid {{ $company['primary_color'] ?? '#2c3e50' }};
            font-size: 18px;
        }
        
        .total-row.grand-total .total-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .total-row.grand-total .total-value {
            color: {{ $company['primary_color'] ?? '#2c3e50' }};
            font-size: 20px;
        }
        
        /* Notes Section */
        .notes-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .notes-title {
            font-weight: 600;
            color: #856404;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .notes-content {
            color: #856404;
            font-size: 12px;
        }
        
        /* Payment Status */
        .payment-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .payment-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .payment-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .payment-failed {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Footer Section */
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        
        .terms-conditions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .terms-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .terms-content {
            font-size: 11px;
            color: #6c757d;
            line-height: 1.6;
        }
        
        .footer-info {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, {{ $company['primary_color'] ?? '#2c3e50' }} 0%, {{ $company['secondary_color'] ?? '#34495e' }} 100%);
            color: white;
            margin: 0 -15px -15px -15px;
            border-radius: 0 0 10px 10px;
        }
        
        .footer-company {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .footer-contact {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .footer-thank {
            margin-top: 15px;
            font-size: 14px;
            font-style: italic;
        }
        
        /* QR Code Section */
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        
        /* Watermark for special statuses */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.06);
            z-index: -1;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @if($order->status === 'cancelled')
        <div class="watermark">CANCELLED</div>
    @elseif($order->payment_status === 'paid')
        <div class="watermark">PAID</div>
    @endif

    <!-- Modern Header -->
    <div class="invoice-header">
        <div class="header-content">
            <div class="company-section">
                @if(!empty($company['logo']))
                    @php
                        $logoPath = \App\Services\BillPDFService::getImageForPDF($company['logo'], $company);
                    @endphp
                    @if($logoPath)
                        <img src="{{ $logoPath }}" alt="{{ $company['name'] }}" class="company-logo">
                        <br>
                    @endif
                @endif
                <div class="company-name">{{ $company['name'] ?? 'Your Store' }}</div>
                @if(!empty($company['tagline']))
                    <div class="company-tagline">{{ $company['tagline'] }}</div>
                @endif
            </div>
            <div class="invoice-section">
                <div class="invoice-badge">
                    <div class="invoice-title">INVOICE</div>
                </div>
                <div class="invoice-number">#{{ $order->order_number }}</div>
                <div class="invoice-date">{{ now()->format('d M Y, h:i A') }}</div>
            </div>
        </div>
    </div>

    <!-- Customer & Order Information -->
    <div class="info-section">
        <div class="info-box">
            <div class="info-title">Bill To</div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $order->customer_name }}</span>
            </div>
            @if($order->customer_mobile)
                <div class="info-row">
                    <span class="info-label">Mobile:</span>
                    <span class="info-value">{{ $order->customer_mobile }}</span>
                </div>
            @endif
            @if($order->customer_email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $order->customer_email }}</span>
                </div>
            @endif
            @if($order->delivery_address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">
                        {{ $order->delivery_address }}<br>
                        {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                    </span>
                </div>
            @endif
        </div>
        <div class="info-box">
            <div class="info-title">Order Details</div>
            <div class="info-row">
                <span class="info-label">Order Date:</span>
                <span class="info-value">{{ $order->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order Status:</span>
                <span class="info-value">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment:</span>
                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="payment-status payment-{{ $order->payment_status }}">
                    {{ ucfirst($order->payment_status ?? 'pending') }}
                </span>
            </div>
            @if($company['gst_number'])
                <div class="info-row">
                    <span class="info-label">GST No:</span>
                    <span class="info-value">{{ $company['gst_number'] }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="items-container">
        <div class="items-header">Order Items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item Details</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Price</th>
                    <th style="width: 10%;" class="text-center">Discount</th>
                    <th style="width: 10%;" class="text-center">Tax</th>
                    <th style="width: 15%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                    @php
                        $unitPrice = $item->price ?? 0;
                        $quantity = $item->quantity ?? 0;
                        $taxAmount = $item->tax_amount ?? 0;
                        $itemTotal = $item->total ?? ($unitPrice * $quantity + $taxAmount);
                        $savings = $item->savings ?? 0;
                        $mrpPrice = $item->mrp_price ?? $unitPrice;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="product-name">{{ $item->product_name }}</div>
                            @if($item->product && $item->product->sku)
                                <div class="product-details">SKU: {{ $item->product->sku }}</div>
                            @endif
                            @if($item->offer_name)
                                <div class="product-details" style="color: #28a745;">
                                    <strong>Offer:</strong> {{ $item->offer_name }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $quantity }}</td>
                        <td class="text-right">
                            @if($mrpPrice > $unitPrice)
                                <div class="price-original">₹{{ number_format($mrpPrice, 2) }}</div>
                            @endif
                            <div class="price-offer">₹{{ number_format($unitPrice, 2) }}</div>
                        </td>
                        <td class="text-center">
                            @if($savings > 0)
                                <span class="discount-badge">-₹{{ number_format($savings, 0) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->tax_percentage > 0)
                                {{ $item->tax_percentage }}%
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>₹{{ number_format($itemTotal, 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-notes">
            @if($order->notes)
                <div class="notes-box">
                    <div class="notes-title">Order Notes</div>
                    <div class="notes-content">{{ $order->notes }}</div>
                </div>
            @endif
            
            <!-- QR Code or additional info can go here -->
        </div>
        
        <div class="summary-totals">
            <div class="totals-box">
                @php
                    $totalMrp = $order->items->sum('mrp_total');
                    $totalSavings = $order->items->sum('savings');
                @endphp
                
                @if($totalSavings > 0)
                    <div class="total-row">
                        <div class="total-label">Total MRP:</div>
                        <div class="total-value">₹{{ number_format($totalMrp, 2) }}</div>
                    </div>
                    <div class="total-row discount">
                        <div class="total-label">Total Savings:</div>
                        <div class="total-value">-₹{{ number_format($totalSavings, 2) }}</div>
                    </div>
                @endif
                
                <div class="total-row">
                    <div class="total-label">Subtotal:</div>
                    <div class="total-value">₹{{ number_format($order->subtotal, 2) }}</div>
                </div>
                
                @if($order->discount > 0)
                    <div class="total-row discount">
                        <div class="total-label">Additional Discount:</div>
                        <div class="total-value">-₹{{ number_format($order->discount, 2) }}</div>
                    </div>
                @endif
                
                @if($order->cgst_amount > 0)
                    <div class="total-row tax">
                        <div class="total-label">CGST ({{ $order->cgst_percentage ?? 9 }}%):</div>
                        <div class="total-value">₹{{ number_format($order->cgst_amount, 2) }}</div>
                    </div>
                @endif
                
                @if($order->sgst_amount > 0)
                    <div class="total-row tax">
                        <div class="total-label">SGST ({{ $order->sgst_percentage ?? 9 }}%):</div>
                        <div class="total-value">₹{{ number_format($order->sgst_amount, 2) }}</div>
                    </div>
                @endif
                
                @if($order->delivery_charge > 0)
                    <div class="total-row">
                        <div class="total-label">Delivery Charge:</div>
                        <div class="total-value">₹{{ number_format($order->delivery_charge, 2) }}</div>
                    </div>
                @elseif($order->delivery_charge == 0)
                    <div class="total-row">
                        <div class="total-label">Delivery Charge:</div>
                        <div class="total-value" style="color: #28a745;">FREE</div>
                    </div>
                @endif
                
                <div class="total-row grand-total">
                    <div class="total-label">Grand Total:</div>
                    <div class="total-value">₹{{ number_format($order->total, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer-section">
        <div class="terms-conditions">
            <div class="terms-title">Terms & Conditions</div>
            <div class="terms-content">
                1. This is a computer generated invoice and does not require a signature.<br>
                2. Goods once sold will not be taken back or exchanged unless defective.<br>
                3. All disputes are subject to {{ $company['city'] ?? 'local' }} jurisdiction only.<br>
                4. Thank you for your business!
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="footer-info">
        <div class="footer-company">{{ $company['name'] ?? 'Your Store' }}</div>
        @if($company['address'])
            <div class="footer-contact">{{ $company['address'] }}</div>
        @endif
        <div class="footer-contact">
            @if($company['phone'])
                Phone: {{ $company['phone'] }}
            @endif
            @if($company['phone'] && $company['email'])
                &nbsp;|&nbsp;
            @endif
            @if($company['email'])
                Email: {{ $company['email'] }}
            @endif
        </div>
        @if($company['website'])
            <div class="footer-contact">{{ $company['website'] }}</div>
        @endif
        <div class="footer-thank">Thank you for shopping with us!</div>
    </div>
</body>
</html>
