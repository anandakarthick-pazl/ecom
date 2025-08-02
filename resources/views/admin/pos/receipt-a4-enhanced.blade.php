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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .container {
            width: 100%;
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            padding: 15mm;
            min-height: 297mm; /* A4 height */
            position: relative;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .header-left {
            width: 65%;
            float: left;
            vertical-align: top;
        }
        
        .header-right {
            width: 35%;
            float: right;
            text-align: right;
        }
        
        .company-logo {
            max-width: 60px;
            max-height: 60px;
            margin-bottom: 8px;
            display: block;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            line-height: 1.2;
        }
        
        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.3;
        }
        
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .receipt-details {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
        }
        
        .receipt-details strong {
            color: #333;
        }
        
        .clearfix {
            clear: both;
        }
        
        /* Customer and Sale Info */
        .info-section {
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .info-left, .info-right {
            width: 48%;
            vertical-align: top;
        }
        
        .info-left {
            float: left;
        }
        
        .info-right {
            float: right;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 12px;
        }
        
        .info-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        
        .info-content {
            font-size: 10px;
            line-height: 1.4;
        }
        
        /* Enhanced Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .items-table th {
            background-color: #2c3e50;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
            font-size: 9px;
        }
        
        .items-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9px;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .item-sku {
            font-size: 8px;
            color: #666;
        }
        
        /* Price breakdown styling */
        .price-breakdown {
            font-size: 8px;
            color: #666;
            line-height: 1.2;
        }
        
        .selling-price {
            font-weight: bold;
            color: #27ae60;
            font-size: 9px;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 8px;
        }
        
        .discount-highlight {
            color: #dc3545;
            font-weight: bold;
            font-size: 8px;
        }
        
        .tax-highlight {
            color: #6c757d;
            font-size: 8px;
        }
        
        /* Amount columns */
        .amount-column {
            min-width: 65px;
            font-weight: bold;
        }
        
        /* Enhanced Totals Section */
        .totals-container {
            margin-top: 25px;
            overflow: hidden;
        }
        
        .totals-section {
            width: 350px;
            float: right;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .totals-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        
        .total-label {
            text-align: left;
            font-weight: normal;
            width: 60%;
        }
        
        .total-amount {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        
        .grand-total {
            background-color: #2c3e50;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        
        .grand-total td {
            border-bottom: none;
        }
        
        /* Product Summary Section */
        .product-summary {
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 4px;
            padding: 12px;
            margin-top: 15px;
            clear: both;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .summary-item {
            font-size: 9px;
            line-height: 1.3;
        }
        
        .summary-item strong {
            color: #27ae60;
        }
        
        /* Payment and Notes Sections */
        .payment-section, .notes-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            margin-top: 15px;
            clear: both;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .payment-details {
            font-size: 10px;
            line-height: 1.4;
        }
        
        .payment-grid {
            overflow: hidden;
        }
        
        .payment-left, .payment-right {
            width: 48%;
        }
        
        .payment-left {
            float: left;
        }
        
        .payment-right {
            float: right;
        }
        
        /* Footer */
        .footer {
            border-top: 2px solid #2c3e50;
            padding-top: 15px;
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            clear: both;
        }
        
        .footer-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .footer p {
            margin-bottom: 5px;
        }
        
        /* Print Styles */
        @media print {
            .container {
                padding: 10mm;
            }
            .no-print {
                display: none;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
        
        /* Page break control */
        .page-break {
            page-break-before: always;
        }
        
        .no-page-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if(!empty($globalCompany->company_logo_pdf))
                    <img src="{{ $globalCompany->company_logo_pdf }}" alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" class="company-logo">
                @elseif(!empty($globalCompany->company_logo))
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" class="company-logo">
                @endif
                <div class="company-name">{{ $globalCompany->company_name ?? 'Herbal Store' }}</div>
                <div class="company-details">
                    @if(!empty($globalCompany->full_address))
                        {{ $globalCompany->full_address }}<br>
                    @else
                        @if(!empty($globalCompany->company_address))
                            {{ $globalCompany->company_address }}<br>
                        @endif
                        @if(!empty($globalCompany->city))
                            {{ $globalCompany->city }}
                            @if(!empty($globalCompany->state)), {{ $globalCompany->state }}@endif
                            @if(!empty($globalCompany->postal_code)) - {{ $globalCompany->postal_code }}@endif
                            <br>
                        @endif
                    @endif
                    @if(!empty($globalCompany->contact_info))
                        {{ $globalCompany->contact_info }}<br>
                    @else
                        @if(!empty($globalCompany->company_phone))
                            <strong>Phone:</strong> {{ $globalCompany->company_phone }}<br>
                        @endif
                        @if(!empty($globalCompany->company_email))
                            <strong>Email:</strong> {{ $globalCompany->company_email }}<br>
                        @endif
                    @endif
                    @if(!empty($globalCompany->gst_number))
                        <strong>GST No:</strong> {{ $globalCompany->gst_number }}
                    @endif
                </div>
            </div>
            
            <div class="header-right">
                <div class="receipt-title">RECEIPT</div>
                <div class="receipt-details">
                    <strong>Receipt No:</strong> {{ $sale->invoice_number }}<br>
                    <strong>Date:</strong> {{ $sale->created_at->format('d/m/Y') }}<br>
                    <strong>Time:</strong> {{ $sale->created_at->format('h:i A') }}<br>
                    @if($sale->cashier)
                        <strong>Cashier:</strong> {{ $sale->cashier->name }}
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <!-- Customer and Sale Information -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <div class="info-title">Customer Information</div>
                    <div class="info-content">
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
                </div>
            </div>
            
            <div class="info-right">
                <div class="info-box">
                    <div class="info-title">Sale Information</div>
                    <div class="info-content">
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}<br>
                        <strong>Status:</strong> {{ ucfirst($sale->status) }}<br>
                        <strong>Total Items:</strong> {{ $sale->items->sum('quantity') }}<br>
                        <strong>Products:</strong> {{ $sale->items->count() }}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <!-- Enhanced Items Table with Product-wise Details -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="25%">Product Details</th>
                    <th width="6%" class="text-center">Qty</th>
                    <th width="12%" class="text-right">Selling Price</th>
                    <th width="12%" class="text-right">Line Total</th>
                    <th width="10%" class="text-right">Discount</th>
                    <th width="10%" class="text-right">Tax</th>
                    <th width="12%" class="text-right">Final Amount</th>
                    <th width="13%" class="text-right">Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                @php
                    $unitPrice = $item->unit_price ?? 0;
                    $originalPrice = $item->original_price ?? $item->product->price ?? $unitPrice;
                    $quantity = $item->quantity ?? 1;
                    $lineTotal = $unitPrice * $quantity;
                    $itemDiscount = $item->discount_amount ?? 0;
                    $afterDiscount = $lineTotal - $itemDiscount;
                    $taxAmount = $item->tax_amount ?? 0;
                    $finalAmount = $afterDiscount + $taxAmount;
                    $hasDiscount = $itemDiscount > 0;
                    $hasTax = $taxAmount > 0;
                    $hasOfferPrice = $originalPrice > $unitPrice && $originalPrice != $unitPrice;
                @endphp
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? $item->product_name }}</div>
                        @if($item->product && $item->product->sku)
                            <div class="item-sku">SKU: {{ $item->product->sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $quantity }}</td>
                    <td class="text-right">
                        @if($hasOfferPrice)
                            <div class="selling-price">Offer ₹{{ number_format($unitPrice, 2) }}</div>
                            <div class="price-breakdown">
                                <span class="original-price">MRP: ₹{{ number_format($originalPrice, 2) }}</span>
                            </div>
                            @if($item->offer_applied)
                                <div class="price-breakdown" style="color: #27ae60;">{{ $item->offer_applied }}</div>
                            @endif
                        @else
                            <div class="selling-price">₹{{ number_format($unitPrice, 2) }}</div>
                            {{-- <div class="price-breakdown">(Selling Price)</div> --}}
                        @endif
                    </td>
                    <td class="text-right amount-column">
                        <strong>MRP ₹{{ number_format($lineTotal, 2) }}</strong>
                    </td>
                    <td class="text-right">
                        @if($hasDiscount)
                            <div class="discount-highlight">-₹{{ number_format($itemDiscount, 2) }}</div>
                            <div class="price-breakdown">({{ number_format($item->discount_percentage ?? 0, 1) }}%)</div>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($hasTax)
                            <div class="tax-highlight">₹{{ number_format($taxAmount, 2) }}</div>
                            <div class="price-breakdown">({{ $item->tax_percentage ?? 0 }}%)</div>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td class="text-right amount-column">
                        <strong>₹{{ number_format($afterDiscount, 2) }}</strong>
                    </td>
                    <td class="text-right amount-column" style="background-color: #f0f8f0;">
                        <strong style="color: #27ae60;">₹{{ number_format($finalAmount, 2) }}</strong>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Enhanced Totals Section -->
        <div class="totals-container">
            <div class="totals-section">
                <table class="totals-table">
                    @php
                        $itemsGrossTotal = $sale->items->sum(function($item) {
                            $originalPrice = $item->original_price ?? $item->product->price ?? $item->unit_price;
                            return $originalPrice * $item->quantity;
                        });
                        $itemsSellingTotal = $sale->items->sum(function($item) {
                            return $item->unit_price * $item->quantity;
                        });
                        $totalItemDiscounts = $sale->items->sum('discount_amount');
                        $totalOfferSavings = $itemsGrossTotal - $itemsSellingTotal;
                    @endphp
                    
                    @if($totalOfferSavings > 0)
                    <tr>
                        <td class="total-label">Items MRP Total:</td>
                        <td class="total-amount">₹{{ number_format($itemsGrossTotal, 2) }}</td>
                    </tr>
                    
                    <tr style="background-color: #e8f5e8;">
                        <td class="total-label">Offer Savings:</td>
                        <td class="total-amount" style="color: #27ae60;">-₹{{ number_format($totalOfferSavings, 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td class="total-label">Items Selling Total:</td>
                        <td class="total-amount">₹{{ number_format($itemsSellingTotal, 2) }}</td>
                    </tr>
                    
                    @if($totalItemDiscounts > 0)
                    <tr>
                        <td class="total-label">Additional Item Discounts:</td>
                        <td class="total-amount">-₹{{ number_format($totalItemDiscounts, 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td class="total-label">Subtotal (After Discounts):</td>
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
                        <td class="total-label">Bill Discount:</td>
                        <td class="total-amount">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    
                    <tr class="grand-total">
                        <td class="total-label">FINAL AMOUNT:</td>
                        <td class="total-amount">₹{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <!-- Payment Information -->
        <div class="payment-section">
            <div class="section-title">💳 Payment Details</div>
            <div class="payment-grid">
                <div class="payment-left">
                    <div class="payment-details">
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}<br>
                        <strong>Amount Paid:</strong> ₹{{ number_format($sale->paid_amount, 2) }}
                        @if($sale->change_amount > 0)
                            <br><strong>Change Given:</strong> ₹{{ number_format($sale->change_amount, 2) }}
                        @endif
                    </div>
                </div>
                <div class="payment-right">
                    <div class="payment-details">
                        <strong>Sale Date:</strong> {{ $sale->created_at->format('d M Y') }}<br>
                        <strong>Sale Time:</strong> {{ $sale->created_at->format('h:i A') }}<br>
                        @php
                            $totalSavings = $totalOfferSavings + $totalItemDiscounts + $sale->discount_amount;
                        @endphp
                        @if($totalSavings > 0)
                            <strong style="color: #27ae60;">Total Savings:</strong> <span style="color: #27ae60;">₹{{ number_format($totalSavings, 2) }}</span>
                        @endif
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        <!-- Notes Sections -->
        @if($sale->custom_tax_enabled && $sale->tax_notes)
        <div class="notes-section" style="background-color: #e3f2fd; border-color: #2196f3;">
            <div class="section-title">📝 Tax Notes</div>
            <div style="font-size: 10px;">{{ $sale->tax_notes }}</div>
        </div>
        @endif
        
        @if($sale->notes)
        <div class="notes-section" style="background-color: #fff3cd; border-color: #ffc107;">
            <div class="section-title">📋 Sale Notes</div>
            <div style="font-size: 10px;">{{ $sale->notes }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">🙏 Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</div>
            <p>
                @if(!empty($globalCompany->company_phone))
                    For any queries, please call {{ $globalCompany->company_phone }}
                @endif
                @if(!empty($globalCompany->company_phone) && !empty($globalCompany->company_email))
                    or 
                @endif
                @if(!empty($globalCompany->company_email))
                    email us at {{ $globalCompany->company_email }}
                @endif
            </p>
            <p><strong>Return Policy:</strong> Items can be returned within 7 days with original receipt</p>
            <p><em>This is a computer generated receipt and does not require signature.</em></p>
            <p style="margin-top: 10px; font-size: 9px;">Generated on {{ now()->format('d/m/Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>