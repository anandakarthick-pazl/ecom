<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .company-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .invoice-title {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        
        .company-logo {
            height: 60px;
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
            line-height: 1.5;
        }
        
        .invoice-label {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 12px;
            color: #666;
        }
        
        /* Customer & Shipping Section */
        .info-section {
            margin-bottom: 30px;
            display: table;
            width: 100%;
        }
        
        .info-box {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .info-box.right {
            margin-left: 4%;
        }
        
        .info-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }
        
        .info-content {
            font-size: 11px;
            line-height: 1.6;
        }
        
        .info-content strong {
            color: #2c3e50;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background: #2c3e50;
            color: white;
        }
        
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        
        .items-table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .items-table tbody tr:hover {
            background: #e9ecef;
        }
        
        .item-name {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .item-sku {
            font-size: 10px;
            color: #666;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .summary-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .summary-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table tr {
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-table td {
            padding: 8px 10px;
            font-size: 12px;
        }
        
        .summary-label {
            text-align: left;
            color: #666;
        }
        
        .summary-value {
            text-align: right;
            font-weight: bold;
            color: #333;
        }
        
        .summary-total {
            background: #2c3e50;
            color: white;
        }
        
        .summary-total td {
            padding: 12px 10px;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* Notes Section */
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #856404;
        }
        
        .notes-content {
            font-size: 11px;
            color: #856404;
        }
        
        /* Terms Section */
        .terms-section {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .terms-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .terms-content {
            font-size: 10px;
            line-height: 1.5;
            color: #666;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #2c3e50;
            text-align: center;
        }
        
        .footer-content {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 10px;
            color: #666;
        }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        /* Print Specific */
        @media print {
            .invoice-container {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
        
        /* Watermark for unpaid */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(255, 0, 0, 0.1);
            font-weight: bold;
            z-index: -1;
        }
        
        /* Tax breakdown table */
        .tax-breakdown {
            margin-top: 10px;
            padding: 10px;
            background: #f0f8ff;
            border-radius: 5px;
        }
        
        .tax-breakdown-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .strikethrough {
            text-decoration: line-through;
            color: #999;
        }
        
        .savings-text {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($order->payment_status !== 'paid')
        <div class="watermark">UNPAID</div>
    @endif
    
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    @if($company && isset($company->logo) && $company->logo && file_exists(public_path('storage/' . $company->logo)))
                        <img src="{{ public_path('storage/' . $company->logo) }}" alt="{{ $company->company_name ?? 'Company' }}" class="company-logo">
                    @endif
                    <div class="company-name">{{ isset($company->company_name) ? $company->company_name : config('app.name', 'Your Company Name') }}</div>
                    <div class="company-details">
                        @if($company)
                            {{ isset($company->address) ? $company->address : 'Company Address' }}<br>
                            @if(isset($company->city) || isset($company->state) || isset($company->pincode))
                                {{ isset($company->city) ? $company->city : '' }}{{ (isset($company->city) && isset($company->state)) ? ', ' : '' }}{{ isset($company->state) ? $company->state : '' }}{{ isset($company->pincode) ? ' - ' . $company->pincode : '' }}<br>
                            @endif
                            @if(isset($company->phone))
                                Phone: {{ $company->phone }}<br>
                            @endif
                            @if(isset($company->email))
                                Email: {{ $company->email }}<br>
                            @endif
                            @if(isset($company->gst_number))
                                GST: {{ $company->gst_number }}<br>
                            @endif
                            @if(isset($company->website))
                                Website: {{ $company->website }}
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
                
                <div class="invoice-title">
                    <div class="invoice-label">INVOICE</div>
                    <div class="invoice-number">Invoice #: {{ $order->order_number }}</div>
                    <div class="invoice-date">Date: {{ $order->created_at->format('d M Y') }}</div>
                    <div class="invoice-date">Time: {{ $order->created_at->format('h:i A') }}</div>
                    <div style="margin-top: 10px;">
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success">PAID</span>
                        @elseif($order->payment_status === 'pending')
                            <span class="badge badge-warning">PENDING</span>
                        @else
                            <span class="badge badge-danger">{{ strtoupper($order->payment_status ?? 'UNPAID') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer & Shipping Information -->
        <div class="info-section">
            <div class="info-box">
                <div class="info-title">Bill To:</div>
                <div class="info-content">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}<br>
                    Phone: {{ $order->customer_mobile }}<br>
                    @if($order->customer_email)
                        Email: {{ $order->customer_email }}
                    @endif
                </div>
            </div>
            
            <div class="info-box right">
                <div class="info-title">Ship To:</div>
                <div class="info-content">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->delivery_address }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}<br>
                    Phone: {{ $order->customer_mobile }}
                </div>
            </div>
        </div>
        
        <!-- Order Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="35%">Product Description</th>
                    <th width="10%" class="text-center">HSN/SAC</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="12%" class="text-right">Unit Price</th>
                    <th width="10%" class="text-center">GST %</th>
                    <th width="18%" class="text-right">Total</th>
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
                    $product = isset($item->product) ? $item->product : null;
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
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if(isset($item->product_sku) && $item->product_sku)
                            <div class="item-sku">SKU: {{ $item->product_sku }}</div>
                        @endif
                        @if($hasDiscount)
                            <div class="savings-text" style="font-size: 10px;">
                                Discount Applied: {{ round((($originalPrice - $effectivePrice) / $originalPrice) * 100) }}% OFF
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ ($product && isset($product->hsn_code)) ? $product->hsn_code : '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">
                        @if($hasDiscount)
                            <span class="strikethrough">₹{{ number_format($originalPrice, 2) }}</span><br>
                            <strong>₹{{ number_format($effectivePrice, 2) }}</strong>
                        @else
                            ₹{{ number_format($item->price, 2) }}
                        @endif
                    </td>
                    <td class="text-center">{{ isset($item->tax_percentage) ? $item->tax_percentage : 0 }}%</td>
                    <td class="text-right">
                        @if($hasDiscount)
                            <span class="strikethrough">₹{{ number_format($originalPrice * $item->quantity, 2) }}</span><br>
                        @endif
                        <strong>₹{{ number_format($item->total, 2) }}</strong>
                        @if($itemSavings > 0)
                            <div class="savings-text" style="font-size: 10px;">
                                Saved: ₹{{ number_format($itemSavings, 2) }}
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-left">
                @if($order->notes)
                    <div class="notes-section">
                        <div class="notes-title">Order Notes:</div>
                        <div class="notes-content">{{ $order->notes }}</div>
                    </div>
                @endif
                
                <!-- Tax Breakdown -->
                @if($order->cgst_amount > 0 || $order->sgst_amount > 0)
                <div class="tax-breakdown">
                    <div class="tax-breakdown-title">GST Breakdown (Included in Total):</div>
                    <table style="width: 100%; font-size: 11px;">
                        <tr>
                            <td>CGST ({{ number_format($order->cgst_amount > 0 ? 9 : 0, 1) }}%):</td>
                            <td style="text-align: right;">₹{{ number_format($order->cgst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>SGST ({{ number_format($order->sgst_amount > 0 ? 9 : 0, 1) }}%):</td>
                            <td style="text-align: right;">₹{{ number_format($order->sgst_amount, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; border-top: 1px solid #ddd;">
                            <td>Total GST:</td>
                            <td style="text-align: right;">₹{{ number_format($order->cgst_amount + $order->sgst_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>
            
            <div class="summary-right">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Subtotal:</td>
                        <td class="summary-value">
                            @if($totalSavings > 0)
                                <span class="strikethrough">₹{{ number_format($totalOriginalAmount, 2) }}</span><br>
                            @endif
                            ₹{{ number_format($order->subtotal, 2) }}
                        </td>
                    </tr>
                    
                    @if($totalSavings > 0)
                    <tr>
                        <td class="summary-label">Total Savings:</td>
                        <td class="summary-value savings-text">-₹{{ number_format($totalSavings, 2) }}</td>
                    </tr>
                    @endif
                    
                    @if($order->discount > 0)
                    <tr>
                        <td class="summary-label">Coupon Discount:</td>
                        <td class="summary-value savings-text">-₹{{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    
                    @if(isset($order->delivery_charge) && $order->delivery_charge > 0)
                    <tr>
                        <td class="summary-label">Delivery Charge:</td>
                        <td class="summary-value">₹{{ number_format($order->delivery_charge, 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr class="summary-total">
                        <td class="summary-label" style="color: white;">Grand Total:</td>
                        <td class="summary-value" style="color: white;">₹{{ number_format($order->total, 2) }}</td>
                    </tr>
                </table>
                
                <div style="margin-top: 10px; padding: 10px; background: #e8f5e9; border-radius: 5px;">
                    <strong style="font-size: 11px;">Payment Method:</strong><br>
                    <span style="font-size: 12px;">
                        {{ ucfirst(str_replace('_', ' ', isset($order->payment_method) ? $order->payment_method : 'N/A')) }}
                    </span><br>
                    <strong style="font-size: 11px;">Payment Status:</strong><br>
                    <span style="font-size: 12px;">
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success">PAID</span>
                        @else
                            <span class="badge badge-warning">{{ strtoupper(isset($order->payment_status) ? $order->payment_status : 'PENDING') }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Terms and Conditions -->
        <div class="terms-section">
            <div class="terms-title">Terms & Conditions:</div>
            <div class="terms-content">
                1. Goods once sold will not be taken back or exchanged unless defective.<br>
                2. All disputes are subject to {{ (isset($company->city) && $company->city) ? $company->city : 'local' }} jurisdiction only.<br>
                3. Payment should be made within the due date mentioned.<br>
                4. Interest @18% p.a. will be charged on overdue payments.<br>
                5. E. & O.E.
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Customer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <strong>Thank you for your business!</strong><br>
                This is a computer-generated invoice and does not require a physical signature.<br>
                For any queries, please contact us at {{ (isset($company->email) && $company->email) ? $company->email : 'support@company.com' }} or {{ (isset($company->phone) && $company->phone) ? $company->phone : 'phone number' }}
            </div>
        </div>
    </div>
</body>
</html>
