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
            padding: 10mm 15mm; /* Reduced top/bottom padding to prevent cutting */
            min-height: 277mm; /* Reduced height to ensure content fits */
            position: relative;
        }
        
        /* ENHANCED: Header Section with Better Logo Handling */
        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: right;
        }
        
        /* FIXED: Better Logo Handling */
        .company-logo {
            max-width: 80px;
            max-height: 80px;
            width: auto;
            height: auto;
            margin-bottom: 8px;
            display: block;
            object-fit: contain; /* Prevent logo distortion */
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            line-height: 1.2;
            word-wrap: break-word; /* Prevent text overflow */
        }
        
        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.3;
            word-wrap: break-word; /* Prevent text overflow */
        }
        
        .receipt-title {
            font-size: 26px;
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
        
        /* ENHANCED: Customer and Sale Info with Table Layout */
        .info-section {
            margin-bottom: 25px;
            display: table;
            width: 100%;
        }
        
        .info-left, .info-right {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .info-left {
            padding-right: 15px;
        }
        
        .info-right {
            padding-left: 15px;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 12px;
            height: auto;
            min-height: 80px;
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
        
        /* ENHANCED: Items Table with Better Spacing */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
            table-layout: fixed; /* Prevent table width issues */
        }
        
        .items-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
            word-wrap: break-word;
        }
        
        .items-table td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            vertical-align: top;
            word-wrap: break-word;
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
            word-wrap: break-word;
            line-height: 1.2;
        }
        
        .item-sku {
            font-size: 9px;
            color: #666;
        }
        
        /* ENHANCED: Totals Section with Better Layout */
        .totals-container {
            margin-top: 25px;
            display: table;
            width: 100%;
        }
        
        .totals-section {
            display: table-cell;
            width: 350px;
            text-align: right;
        }
        
        .totals-spacer {
            display: table-cell;
            width: auto;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-left: auto;
        }
        
        .totals-table td {
            padding: 8px 10px;
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
            font-size: 13px;
            font-weight: bold;
        }
        
        .grand-total td {
            border-bottom: none;
        }
        
        /* ENHANCED: Payment and Notes Sections */
        .payment-section, .notes-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            margin-top: 15px;
            page-break-inside: avoid; /* Prevent section from breaking across pages */
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
            display: table;
            width: 100%;
        }
        
        .payment-left, .payment-right {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .payment-left {
            padding-right: 15px;
        }
        
        .payment-right {
            padding-left: 15px;
        }
        
        /* ENHANCED: Savings Summary */
        .savings-section {
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 4px;
            padding: 12px;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        
        .savings-highlight {
            color: #27ae60;
            font-size: 14px;
            font-weight: bold;
        }
        
        /* ENHANCED: Footer with Better Positioning */
        .footer {
            border-top: 2px solid #2c3e50;
            padding-top: 15px;
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            page-break-inside: avoid;
        }
        
        .footer-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .footer p {
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        /* Utilities */
        .strikethrough {
            text-decoration: line-through;
            color: #999;
            font-size: 9px;
        }
        
        .discount-text {
            color: #dc3545;
            font-size: 9px;
        }
        
        .tax-text {
            color: #6c757d;
            font-size: 9px;
        }
        
        /* ENHANCED: Print Styles */
        @media print {
            .container {
                padding: 5mm 10mm;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .header {
                page-break-after: avoid;
            }
            .items-table {
                page-break-before: avoid;
                page-break-after: avoid;
            }
            .totals-container {
                page-break-before: avoid;
            }
        }
        
        /* ENHANCED: Page break control */
        .page-break {
            page-break-before: always;
        }
        
        .no-page-break {
            page-break-inside: avoid;
        }
        
        /* ENHANCED: Ensure content doesn't overflow */
        .content-wrapper {
            overflow: hidden;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content-wrapper">
            <!-- ENHANCED: Header with Better Logo Handling -->
            <div class="header">
                <div class="header-left">
                    @if(!empty($globalCompany->company_logo_base64))
                        {{-- Use base64 encoded logo for better PDF compatibility --}}
                        <img src="{{ $globalCompany->company_logo_base64 }}" alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" class="company-logo">
                    @elseif(!empty($globalCompany->company_logo_path) && file_exists($globalCompany->company_logo_path))
                        {{-- Fallback to file path if base64 not available --}}
                        <img src="{{ $globalCompany->company_logo_path }}" alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" class="company-logo">
                    @elseif(!empty($globalCompany->company_logo))
                        {{-- Try original logo path as last resort --}}
                        <img src="{{ public_path('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name ?? 'Company Logo' }}" class="company-logo">
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
                        @if(!empty($globalCompany->company_phone))
                            <strong>Phone:</strong> {{ $globalCompany->company_phone }}<br>
                        @endif
                        @if(!empty($globalCompany->company_email))
                            <strong>Email:</strong> {{ $globalCompany->company_email }}<br>
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
            </div>
            
            <!-- ENHANCED: Customer and Sale Information -->
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
            </div>
            
            <!-- ENHANCED: Items Table -->
            <div class="no-page-break">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="35%">Item Details</th>
                            <th width="8%" class="text-center">Qty</th>
                            <th width="12%" class="text-right">Rate</th>
                            <th width="12%" class="text-right">Discount</th>
                            <th width="10%" class="text-right">Tax</th>
                            <th width="13%" class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product->name ?? $item->product_name }}</div>
                                @if($item->product && $item->product->sku)
                                    <div class="item-sku">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">
                                @if(($item->discount_amount ?? 0) > 0)
                                    <div class="discount-text">-₹{{ number_format($item->discount_amount, 2) }}</div>
                                    <div class="discount-text">({{ number_format($item->discount_percentage ?? 0, 1) }}%)</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if(($item->tax_percentage ?? 0) > 0)
                                    <div>{{ $item->tax_percentage }}%</div>
                                    <div class="tax-text">₹{{ number_format($item->tax_amount ?? 0, 2) }}</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @php
                                    $itemGross = $item->quantity * $item->unit_price;
                                    $itemDiscount = $item->discount_amount ?? 0;
                                    $itemNet = $itemGross - $itemDiscount;
                                    $itemTotal = $itemNet + ($item->tax_amount ?? 0);
                                @endphp
                                @if($itemDiscount > 0)
                                    <div class="strikethrough">₹{{ number_format($itemGross, 2) }}</div>
                                @endif
                                <strong>₹{{ number_format($itemTotal, 2) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- ENHANCED: Totals Section -->
            <div class="totals-container">
                <div class="totals-spacer"></div>
                <div class="totals-section">
                    <table class="totals-table">
                        @php
                            $itemsSubtotal = $sale->items->sum(function($item) {
                                return $item->quantity * $item->unit_price;
                            });
                            $totalItemDiscounts = $sale->items->sum('discount_amount');
                        @endphp
                        
                        @if($totalItemDiscounts > 0)
                        <tr>
                            <td class="total-label">Items Gross Total:</td>
                            <td class="total-amount">₹{{ number_format($itemsSubtotal, 2) }}</td>
                        </tr>
                        
                        <tr>
                            <td class="total-label">Item Discounts:</td>
                            <td class="total-amount">-₹{{ number_format($totalItemDiscounts, 2) }}</td>
                        </tr>
                        @endif
                        
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
                            <td class="total-label">Additional Discount:</td>
                            <td class="total-amount">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        
                        <tr class="grand-total">
                            <td class="total-label">TOTAL AMOUNT:</td>
                            <td class="total-amount">₹{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- ENHANCED: Payment Information -->
            <div class="payment-section">
                <div class="section-title">Payment Details</div>
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
                            <strong>Sale Time:</strong> {{ $sale->created_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ENHANCED: Savings Summary -->
            @php
                $totalSavings = $sale->items->sum('discount_amount') + $sale->discount_amount;
                $hasDiscounts = $totalSavings > 0;
            @endphp
            @if($hasDiscounts)
            <div class="savings-section">
                <div class="section-title">Your Savings Summary</div>
                <div class="payment-grid">
                    <div class="payment-left">
                        <div class="payment-details">
                            @if($sale->items->sum('discount_amount') > 0)
                                <strong>Item Discounts:</strong> ₹{{ number_format($sale->items->sum('discount_amount'), 2) }}<br>
                            @endif
                            @if($sale->discount_amount > 0)
                                <strong>Additional Discount:</strong> ₹{{ number_format($sale->discount_amount, 2) }}<br>
                            @endif
                            <strong>Total Savings:</strong> ₹{{ number_format($totalSavings, 2) }}
                        </div>
                    </div>
                    <div class="payment-right">
                        <div style="text-align: right;">
                            @php
                                $originalTotal = $sale->items->sum(function($item) {
                                    return $item->quantity * $item->unit_price;
                                }) + $sale->tax_amount;
                                $savingsPercent = $originalTotal > 0 ? ($totalSavings / $originalTotal) * 100 : 0;
                            @endphp
                            <strong>You Saved:</strong><br>
                            <span class="savings-highlight">{{ number_format($savingsPercent, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- ENHANCED: Notes Sections -->
            @if($sale->custom_tax_enabled && $sale->tax_notes)
            <div class="notes-section" style="background-color: #e3f2fd; border-color: #2196f3;">
                <div class="section-title">Tax Notes</div>
                <div style="font-size: 10px;">{{ $sale->tax_notes }}</div>
            </div>
            @endif
            
            @if($sale->notes)
            <div class="notes-section" style="background-color: #fff3cd; border-color: #ffc107;">
                <div class="section-title">Sale Notes</div>
                <div style="font-size: 10px;">{{ $sale->notes }}</div>
            </div>
            @endif
            
            <!-- ENHANCED: Footer -->
            <div class="footer">
                <div class="footer-title">Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</div>
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
    </div>
</body>
</html>