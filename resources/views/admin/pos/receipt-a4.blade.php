<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
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
        
        .receipt-info {
            text-align: right;
            flex-shrink: 0;
        }
        
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 10px;
        }
        
        .receipt-details {
            font-size: 11px;
            color: #666;
        }
        
        .receipt-details strong {
            color: #333;
        }
        
        .customer-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .customer-info {
            flex: 1;
            padding-right: 20px;
        }
        
        .customer-info h3 {
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .sale-info {
            flex: 1;
        }
        
        .sale-info h3 {
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table th,
        .items-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .totals-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        
        .totals-table .total-label {
            text-align: left;
            font-weight: normal;
        }
        
        .totals-table .total-amount {
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            background-color: #2c3e50;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        
        .payment-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            clear: both;
        }
        
        .payment-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .payment-details {
            display: flex;
            justify-content: space-between;
        }
        
        .footer {
            border-top: 2px solid #2c3e50;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        
        .footer h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    @if($globalCompany->company_logo ?? false)
                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
                    @endif
                    <div class="company-name">{{ $globalCompany->company_name ?? 'Herbal Store' }}</div>
                    <div class="company-details">
                        @if($globalCompany->company_address ?? false)
                            {{ $globalCompany->company_address }}<br>
                        @endif
                        @if($globalCompany->company_phone ?? false)
                            Phone: {{ $globalCompany->company_phone }}<br>
                        @endif
                        @if($globalCompany->company_email ?? false)
                            Email: {{ $globalCompany->company_email }}<br>
                        @endif
                        @if($globalCompany->gst_number ?? false)
                            GST: {{ $globalCompany->gst_number }}
                        @endif
                    </div>
                </div>
                
                <div class="receipt-info">
                    <div class="receipt-title">RECEIPT</div>
                    <div class="receipt-details">
                        <strong>Receipt #:</strong> {{ $sale->invoice_number }}<br>
                        <strong>Date:</strong> {{ $sale->created_at->format('d/m/Y') }}<br>
                        <strong>Time:</strong> {{ $sale->created_at->format('h:i A') }}<br>
                        <strong>Cashier:</strong> {{ $sale->cashier->name ?? 'POS User' }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="customer-section">
            <div class="customer-info">
                <h3>Customer Information</h3>
                @if($sale->customer_name || $sale->customer_phone)
                    @if($sale->customer_name)
                        <strong>Name:</strong> {{ $sale->customer_name }}<br>
                    @endif
                    @if($sale->customer_phone)
                        <strong>Phone:</strong> {{ $sale->customer_phone }}<br>
                    @endif
                @else
                    <em>Walk-in Customer</em>
                @endif
            </div>
            
            <div class="sale-info">
                <h3>Sale Information</h3>
                <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}<br>
                <strong>Status:</strong> {{ ucfirst($sale->status) }}<br>
                <strong>Total Items:</strong> {{ $sale->total_items ?? $sale->items->sum('quantity') }}
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name ?? $item->product_name }}</strong>
                        @if($item->product && $item->product->sku)
                            <br><small>SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">
                        @if($item->tax_percentage > 0)
                            {{ $item->tax_percentage }}%<br>
                            <small>₹{{ number_format($item->tax_amount ?? 0, 2) }}</small>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">₹{{ number_format(($item->quantity * $item->unit_price), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">₹{{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                
                @if($sale->cgst_amount > 0)
                <tr>
                    <td class="total-label">CGST:</td>
                    <td class="total-amount">₹{{ number_format($sale->cgst_amount, 2) }}</td>
                </tr>
                @endif
                
                @if($sale->sgst_amount > 0)
                <tr>
                    <td class="total-label">SGST:</td>
                    <td class="total-amount">₹{{ number_format($sale->sgst_amount, 2) }}</td>
                </tr>
                @endif
                
                @if($sale->discount_amount > 0)
                <tr>
                    <td class="total-label">Discount:</td>
                    <td class="total-amount">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
                @endif
                
                <tr class="grand-total">
                    <td class="total-label">TOTAL:</td>
                    <td class="total-amount">₹{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Payment Information -->
        <div class="payment-info">
            <h4>Payment Details</h4>
            <div class="payment-details">
                <div>
                    <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}<br>
                    <strong>Amount Paid:</strong> ₹{{ number_format($sale->paid_amount, 2) }}
                    @if($sale->change_amount > 0)
                        <br><strong>Change Given:</strong> ₹{{ number_format($sale->change_amount, 2) }}
                    @endif
                </div>
                <div>
                    <strong>Sale Date:</strong> {{ $sale->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>
        
        @if($sale->notes)
        <div style="margin-top: 20px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Notes:</strong> {{ $sale->notes }}
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <h4>Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</h4>
            <p>
                @if($globalCompany->company_phone ?? false)
                    For any queries, please call {{ $globalCompany->company_phone }}
                @endif
                @if(($globalCompany->company_phone ?? false) && ($globalCompany->company_email ?? false))
                    or 
                @endif
                @if($globalCompany->company_email ?? false)
                    email us at {{ $globalCompany->company_email }}
                @endif
            </p>
            <p><strong>Return Policy:</strong> Items can be returned within 7 days with original receipt</p>
            <p><em>This is a computer generated receipt and does not require signature.</em></p>
        </div>
    </div>
</body>
</html>