<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Multiple Receipts - {{ $sales->count() }} Receipts</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 5px;
        }
        .receipt-container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 2px;
        }
        .receipt {
            width: 48%;
            min-width: 180px;
            max-width: 200px;
            margin-bottom: 5px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background: white;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
        }
        .company-logo {
            max-height: 25px;
            margin-bottom: 2px;
        }
        .company-name {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
        }
        .receipt-title {
            font-size: 9px;
            font-weight: bold;
            margin: 2px 0 0 0;
        }
        .receipt-details {
            margin-bottom: 8px;
            font-size: 7px;
        }
        .receipt-details p {
            margin: 1px 0;
        }
        .customer-info {
            margin-bottom: 8px;
            padding: 4px;
            background-color: #f5f5f5;
            border-radius: 2px;
            font-size: 7px;
        }
        .customer-info h4 {
            margin: 0 0 3px 0;
            font-size: 8px;
        }
        .customer-info p {
            margin: 1px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7px;
        }
        .items-table th,
        .items-table td {
            border-bottom: 1px solid #ddd;
            padding: 2px 1px;
            text-align: left;
        }
        .items-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 6px;
        }
        .items-table .qty,
        .items-table .price,
        .items-table .total {
            text-align: right;
        }
        .item-name {
            font-weight: bold;
            font-size: 7px;
        }
        .item-sku {
            font-size: 6px;
            color: #666;
        }
        .discount-text {
            color: #dc3545;
            font-size: 6px;
        }
        .strikethrough {
            text-decoration: line-through;
            color: #999;
        }
        .totals {
            margin-top: 8px;
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 7px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 1px 0;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 8px;
            border-top: 1px solid #333;
            padding-top: 2px;
        }
        .payment-info {
            margin-top: 8px;
            padding: 4px;
            background-color: #f5f5f5;
            border-radius: 2px;
            font-size: 7px;
        }
        .payment-info h4 {
            margin: 0 0 3px 0;
            font-size: 8px;
        }
        .payment-info p {
            margin: 1px 0;
        }
        .savings-summary {
            margin-top: 8px;
            padding: 4px;
            background-color: #e8f5e8;
            border-radius: 2px;
            border: 1px solid #c3e6c3;
            font-size: 7px;
        }
        .savings-summary h4 {
            margin: 0 0 3px 0;
            color: #27ae60;
            font-size: 8px;
        }
        .savings-summary p {
            margin: 1px 0;
        }
        .savings-highlight {
            color: #27ae60;
            font-size: 9px;
            font-weight: bold;
        }
        .receipt-footer {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 6px;
            color: #666;
        }
        .receipt-footer p {
            margin: 1px 0;
        }
        .dashed-line {
            border-bottom: 1px dashed #333;
            margin: 5px 0;
        }
        /* Print styles */
        @media print {
            body {
                padding: 0;
            }
            .receipt {
                break-inside: avoid;
            }
        }
        /* Responsive layout for different page sizes */
        @media (max-width: 600px) {
            .receipt {
                width: 100%;
            }
        }
        @media (min-width: 1200px) {
            .receipt {
                width: 23%;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        @foreach($sales as $sale)
            <div class="receipt">
                <div class="receipt-header">
                    @if ($globalCompany->company_logo_pdf)
                        <img src="{{ $globalCompany->company_logo_pdf }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
                    @elseif ($globalCompany->company_logo ?? false)
                        <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
                    @else
                        <div style="font-size: 16px; margin-bottom: 2px;">🌿</div>
                    @endif
                    <div class="company-name">{{ $globalCompany->company_name ?? 'Herbal Store' }}</div>
                    @if(!empty($globalCompany->company_phone))
                        <div style="font-size: 7px; color: #666;">{{ $globalCompany->company_phone }}</div>
                    @endif
                    <div class="receipt-title">RECEIPT</div>
                </div>

                <div class="receipt-details">
                    <p><strong>No:</strong> {{ $sale->invoice_number }}</p>
                    <p><strong>Date:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                    @if($sale->cashier)
                        <p><strong>Cashier:</strong> {{ $sale->cashier->name }}</p>
                    @endif
                </div>

                <div class="customer-info">
                    <h4>Customer</h4>
                    @if($sale->customer_name || $sale->customer_phone)
                        @if($sale->customer_name)
                            <p><strong>Name:</strong> {{ Str::limit($sale->customer_name, 15) }}</p>
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
                            <th width="50%">Item</th>
                            <th width="15%" class="qty">Qty</th>
                            <th width="20%" class="price">Price</th>
                            <th width="15%" class="total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items->take(5) as $item)
                            <tr>
                                <td>
                                    <div class="item-name">{{ Str::limit($item->product->name ?? $item->product_name, 20) }}</div>
                                    @if(($item->discount_amount ?? 0) > 0)
                                        <div class="discount-text">Disc: Rs.{{ number_format($item->discount_amount, 2) }}</div>
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
                        @if($sale->items->count() > 5)
                            <tr>
                                <td colspan="4" style="text-align: center; font-style: italic;">
                                    ... and {{ $sale->items->count() - 5 }} more items
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="dashed-line"></div>

                <div class="totals">
                    <table>
                        <tr>
                            <td>Subtotal:</td>
                            <td style="text-align: right;">Rs.{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        
                        @if($sale->cgst_amount > 0 || $sale->sgst_amount > 0)
                            <tr>
                                <td>Tax:</td>
                                <td style="text-align: right;">Rs.{{ number_format($sale->cgst_amount + $sale->sgst_amount, 2) }}</td>
                            </tr>
                        @endif
                        
                        @if($sale->discount_amount > 0)
                            <tr>
                                <td>Discount:</td>
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
                    <h4>Payment</h4>
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
                        <h4>Savings</h4>
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

                <div class="receipt-footer">
                    <p><strong>Thank you!</strong></p>
                    <p>{{ $globalCompany->company_name ?? 'Herbal Store' }}</p>
                    <p>{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
