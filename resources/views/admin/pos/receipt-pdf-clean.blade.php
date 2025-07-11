<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 5px;
            width: 80mm;
            max-width: 80mm;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .company-logo {
            max-height: 60px;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0 0 0;
        }
        .receipt-details {
            margin-bottom: 15px;
        }
        .receipt-details p {
            margin: 2px 0;
            font-size: 11px;
        }
        .customer-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 3px;
        }
        .customer-info h3 {
            margin: 0 0 5px 0;
            font-size: 13px;
        }
        .customer-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .items-table th,
        .items-table td {
            border-bottom: 1px solid #ddd;
            padding: 5px 2px;
            text-align: left;
        }
        .items-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        .items-table .qty,
        .items-table .price,
        .items-table .total {
            text-align: right;
        }
        .item-name {
            font-weight: bold;
            font-size: 10px;
        }
        .item-sku {
            font-size: 8px;
            color: #666;
        }
        .discount-text {
            color: #dc3545;
            font-size: 9px;
        }
        .tax-text {
            color: #6c757d;
            font-size: 9px;
        }
        .strikethrough {
            text-decoration: line-through;
            color: #999;
        }
        .totals {
            margin-top: 15px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .totals table {
            width: 100%;
            font-size: 11px;
        }
        .totals td {
            padding: 3px 0;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .payment-info {
            margin-top: 15px;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 3px;
        }
        .payment-info h3 {
            margin: 0 0 5px 0;
            font-size: 13px;
        }
        .payment-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        .savings-summary {
            margin-top: 15px;
            padding: 8px;
            background-color: #e8f5e8;
            border-radius: 3px;
            border: 1px solid #c3e6c3;
        }
        .savings-summary h3 {
            margin: 0 0 5px 0;
            color: #27ae60;
            font-size: 13px;
        }
        .savings-summary p {
            margin: 2px 0;
            font-size: 11px;
        }
        .savings-highlight {
            color: #27ae60;
            font-size: 14px;
            font-weight: bold;
        }
        .notes-section {
            margin-top: 15px;
            padding: 8px;
            background-color: #fff3cd;
            border-radius: 3px;
            border: 1px solid #ffc107;
        }
        .notes-section h3 {
            margin: 0 0 5px 0;
            color: #856404;
            font-size: 13px;
        }
        .notes-section p {
            margin: 2px 0;
            font-size: 11px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .footer p {
            margin: 3px 0;
        }
        .footer-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        .dashed-line {
            border-bottom: 1px dashed #333;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        @if ($globalCompany->company_logo_pdf)
            <img src="{{ $globalCompany->company_logo_pdf }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
        @elseif ($globalCompany->company_logo ?? false)
            <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
        @else
            <div style="font-size: 24px; margin-bottom: 5px;">🌿</div>
        @endif
        <div class="company-name">{{ $globalCompany->company_name ?? 'Herbal Store' }}</div>
        @if(!empty($globalCompany->company_address))
            <div style="font-size: 10px; color: #666; margin: 2px 0;">{{ $globalCompany->company_address }}</div>
        @endif
        @if(!empty($globalCompany->company_phone))
            <div style="font-size: 10px; color: #666; margin: 2px 0;">Ph: {{ $globalCompany->company_phone }}</div>
        @endif
        @if(!empty($globalCompany->gst_number))
            <div style="font-size: 10px; color: #666; margin: 2px 0;">GST: {{ $globalCompany->gst_number }}</div>
        @endif
        <div class="receipt-title">RECEIPT</div>
    </div>

    <div class="receipt-details">
        <p><strong>Receipt No:</strong> {{ $sale->invoice_number }}</p>
        <p><strong>Date:</strong> {{ $sale->created_at->format('d/m/Y h:i A') }}</p>
        @if($sale->cashier)
            <p><strong>Cashier:</strong> {{ $sale->cashier->name }}</p>
        @endif
    </div>

    <div class="customer-info">
        <h3>Customer Details</h3>
        @if($sale->customer_name || $sale->customer_phone)
            @if($sale->customer_name)
                <p><strong>Name:</strong> {{ $sale->customer_name }}</p>
            @endif
            @if($sale->customer_phone)
                <p><strong>Phone:</strong> {{ $sale->customer_phone }}</p>
            @endif
        @else
            <p><em>Walk-in Customer</em></p>
        @endif
        <p><strong>Items:</strong> {{ $sale->items->sum('quantity') }} | <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</p>
    </div>

    <div class="dashed-line"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="40%">Item</th>
                <th width="15%" class="qty">Qty</th>
                <th width="20%" class="price">Price</th>
                <th width="25%" class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? $item->product_name }}</div>
                        @if($item->product && $item->product->sku)
                            <div class="item-sku">{{ $item->product->sku }}</div>
                        @endif
                        @if(($item->discount_amount ?? 0) > 0)
                            <div class="discount-text">Disc: Rs.{{ number_format($item->discount_amount, 2) }}</div>
                        @endif
                        @if(($item->tax_percentage ?? 0) > 0)
                            <div class="tax-text">Tax: {{ $item->tax_percentage }}%</div>
                        @endif
                    </td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">Rs.{{ number_format($item->unit_price, 2) }}</td>
                    <td class="total">
                        @php
                            $itemGross = $item->quantity * $item->unit_price;
                            $itemDiscount = $item->discount_amount ?? 0;
                            $itemNet = $itemGross - $itemDiscount;
                            $itemTotal = $itemNet + ($item->tax_amount ?? 0);
                        @endphp
                        @if($itemDiscount > 0)
                            <div class="strikethrough">Rs.{{ number_format($itemGross, 2) }}</div>
                        @endif
                        <strong>Rs.{{ number_format($itemTotal, 2) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <div class="totals">
        <table>
            @php
                $itemsSubtotal = $sale->items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                });
                $totalItemDiscounts = $sale->items->sum('discount_amount');
            @endphp
            
            @if($totalItemDiscounts > 0)
                <tr>
                    <td>Gross Total:</td>
                    <td style="text-align: right;">Rs.{{ number_format($itemsSubtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Item Discounts:</td>
                    <td style="text-align: right;">-Rs.{{ number_format($totalItemDiscounts, 2) }}</td>
                </tr>
            @endif
            
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Rs.{{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            
            @if($sale->cgst_amount > 0)
                <tr>
                    <td>CGST:</td>
                    <td style="text-align: right;">Rs.{{ number_format($sale->cgst_amount, 2) }}</td>
                </tr>
            @endif
            
            @if($sale->sgst_amount > 0)
                <tr>
                    <td>SGST:</td>
                    <td style="text-align: right;">Rs.{{ number_format($sale->sgst_amount, 2) }}</td>
                </tr>
            @endif
            
            @if($sale->discount_amount > 0)
                <tr>
                    <td>Additional Discount:</td>
                    <td style="text-align: right;">-Rs.{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
            @endif
            
            <tr class="total-row">
                <td><strong>TOTAL:</strong></td>
                <td style="text-align: right;"><strong>Rs.{{ number_format($sale->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="payment-info">
        <h3>Payment Details</h3>
        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</p>
        <p><strong>Paid:</strong> Rs.{{ number_format($sale->paid_amount, 2) }}</p>
        @if($sale->change_amount > 0)
            <p><strong>Change:</strong> Rs.{{ number_format($sale->change_amount, 2) }}</p>
        @endif
    </div>

    <!-- Savings Summary -->
    @php
        $totalSavings = $sale->items->sum('discount_amount') + $sale->discount_amount;
        $hasDiscounts = $totalSavings > 0;
    @endphp
    @if($hasDiscounts)
        <div class="savings-summary">
            <h3>Your Savings</h3>
            @if($sale->items->sum('discount_amount') > 0)
                <p><strong>Item Discounts:</strong> Rs.{{ number_format($sale->items->sum('discount_amount'), 2) }}</p>
            @endif
            @if($sale->discount_amount > 0)
                <p><strong>Additional Discount:</strong> Rs.{{ number_format($sale->discount_amount, 2) }}</p>
            @endif
            <p><strong>Total Saved:</strong> Rs.{{ number_format($totalSavings, 2) }}</p>
            @php
                $originalTotal = $sale->items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                }) + $sale->tax_amount;
                $savingsPercent = $originalTotal > 0 ? ($totalSavings / $originalTotal) * 100 : 0;
            @endphp
            <p style="text-align: center;"><span class="savings-highlight">{{ number_format($savingsPercent, 1) }}% OFF</span></p>
        </div>
    @endif

    <!-- Notes Sections -->
    @if($sale->custom_tax_enabled && $sale->tax_notes)
        <div class="notes-section">
            <h3>Tax Notes</h3>
            <p>{{ $sale->tax_notes }}</p>
        </div>
    @endif

    @if($sale->notes)
        <div class="notes-section">
            <h3>Sale Notes</h3>
            <p>{{ $sale->notes }}</p>
        </div>
    @endif

    <div class="dashed-line"></div>

    <div class="footer">
        <p class="footer-title">Thank you for shopping with us!</p>
        <p>{{ $globalCompany->company_name ?? 'Herbal Store' }}</p>
        @if(!empty($globalCompany->company_phone))
            <p>Call: {{ $globalCompany->company_phone }}</p>
        @endif
        @if(!empty($globalCompany->company_email))
            <p>Email: {{ $globalCompany->company_email }}</p>
        @endif
        <p><strong>Return Policy:</strong> 7 days with receipt</p>
        <p><em>Computer generated receipt</em></p>
        <p style="margin-top: 10px; font-size: 8px;">{{ now()->format('d/m/Y h:i A') }}</p>
    </div>
</body>
</html>
