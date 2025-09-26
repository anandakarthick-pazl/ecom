<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #ffffff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }
        
        /* Header Section */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #2d5016;
            padding-bottom: 15px;
        }
        
        .company-section {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .invoice-section {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-logo {
            max-height: 80px;
            max-width: 200px;
            margin-bottom: 10px;
            object-fit: contain;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .company-address {
            color: #666;
            line-height: 1.5;
            margin-bottom: 5px;
        }
        
        .company-contact {
            color: #666;
            font-size: 11px;
        }
        
        .gst-number {
            color: #2d5016;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2d5016;
        }
        
        .invoice-details table {
            width: 100%;
        }
        
        .invoice-details td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .invoice-details .label {
            font-weight: bold;
            color: #2d5016;
            width: 40%;
        }
        
        /* Customer Section */
        .customer-section {
            margin: 20px 0;
            display: table;
            width: 100%;
        }
        
        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .payment-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 30px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 3px;
        }
        
        .customer-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            border-left: 3px solid #28a745;
        }
        
        .payment-details {
            background: #fff3cd;
            padding: 12px;
            border-radius: 5px;
            border-left: 3px solid #ffc107;
        }
        
        /* Items Table */
        .items-section {
            margin: 25px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table thead {
            background: linear-gradient(135deg, #2d5016, #3e6b21);
            color: white;
        }
        
        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        .items-table th:last-child {
            border-right: none;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #f0f0f0;
            vertical-align: top;
        }
        
        .items-table td:last-child {
            border-right: none;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .items-table tbody tr:hover {
            background: #e8f5e8;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .product-name {
            font-weight: 600;
            color: #2d5016;
        }
        
        .product-sku {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }
        
        /* Totals Section */
        .totals-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        
        .totals-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .totals-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
            margin-left: auto;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        
        .totals-table .label {
            text-align: right;
            font-weight: 500;
            color: #555;
            width: 60%;
        }
        
        .totals-table .amount {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        
        .total-row {
            background: linear-gradient(135deg, #2d5016, #3e6b21);
            color: white;
            font-size: 14px;
        }
        
        .total-row td {
            border-bottom: none;
            padding: 12px;
        }
        
        /* Payment Information */
        .payment-summary {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        
        .payment-method {
            display: inline-block;
            background: #2d5016;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Notes Section */
        .notes-section {
            margin-top: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #6c757d;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2d5016;
            text-align: center;
            color: #666;
        }
        
        .thank-you {
            font-size: 18px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 10px;
        }
        
        .footer-note {
            font-size: 11px;
            line-height: 1.5;
        }
        
        /* Tax Details */
        .tax-details {
            font-size: 11px;
            color: #666;
            margin-top: 10px;
        }
        
        /* Discount highlighting */
        .discount-amount {
            color: #dc3545;
            font-weight: bold;
        }
        
        .tax-amount {
            color: #17a2b8;
            font-weight: bold;
        }
        
        /* Page break for long tables */
        .page-break {
            page-break-after: always;
        }
        
        /* Print specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="invoice-header">
            <div class="company-section">
                @if(isset($globalCompany->company_logo_pdf) && $globalCompany->company_logo_pdf)
                    <img src="{{ $globalCompany->company_logo_pdf }}" class="company-logo" alt="{{ $globalCompany->company_name }} Logo">
                @endif
                
                <div class="company-name">{{ $globalCompany->company_name ?? 'Your Store' }}</div>
                
                @if(isset($globalCompany->full_address) && $globalCompany->full_address)
                    <div class="company-address">{{ $globalCompany->full_address }}</div>
                @endif
                
                @if(isset($globalCompany->contact_info) && $globalCompany->contact_info)
                    <div class="company-contact">{{ $globalCompany->contact_info }}</div>
                @endif
                
                @if(isset($globalCompany->gst_number) && $globalCompany->gst_number)
                    <div class="gst-number">GST: {{ $globalCompany->gst_number }}</div>
                @endif
            </div>
            
            <div class="invoice-section">
                <div class="invoice-title">Invoice</div>
                <div class="invoice-details">
                    <table>
                        <tr>
                            <td class="label">Invoice #:</td>
                            <td><strong>{{ $sale->invoice_number }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Date:</td>
                            <td>{{ $sale->created_at->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Time:</td>
                            <td>{{ $sale->created_at->format('h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status:</td>
                            <td><span style="color: {{ $sale->status === 'completed' ? '#28a745' : '#dc3545' }}; font-weight: bold;">{{ ucfirst($sale->status) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Section -->
        <div class="customer-section">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="customer-info">
                    @if($sale->customer_name)
                        <div style="font-weight: bold; font-size: 14px; color: #2d5016;">{{ $sale->customer_name }}</div>
                        @if($sale->customer_phone)
                            <div style="margin-top: 5px;">📞 {{ $sale->customer_phone }}</div>
                        @endif
                    @else
                        <div style="font-weight: bold; color: #666;">Walk-in Customer</div>
                        <div style="font-size: 11px; color: #888; margin-top: 5px;">No customer details provided</div>
                    @endif
                </div>
            </div>
            
            <div class="payment-info">
                <div class="section-title">Payment Details</div>
                <div class="payment-details">
                    <div><strong>Method:</strong> <span class="payment-method">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span></div>
                    <div style="margin-top: 8px;"><strong>Paid Amount:</strong> ₹{{ number_format($sale->paid_amount, 2) }}</div>
                    @if($sale->change_amount > 0)
                        <div><strong>Change Given:</strong> ₹{{ number_format($sale->change_amount, 2) }}</div>
                    @endif
                    <div style="margin-top: 5px; font-size: 11px; color: #666;">
                        Cashier: {{ $sale->cashier->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <div class="section-title">Items Purchased</div>
            
            @foreach($sale->items->chunk(15) as $chunkIndex => $chunk)
                @if($chunkIndex > 0)
                    <div class="page-break"></div>
                @endif
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">Product Details</th>
                            <th style="width: 8%;">Qty</th>
                            <th style="width: 12%;">Unit Price</th>
                            <th style="width: 10%;">Discount</th>
                            <th style="width: 8%;">Tax %</th>
                            <th style="width: 10%;">Tax Amt</th>
                            <th style="width: 12%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $index => $item)
                            @php
                                $itemNumber = ($chunkIndex * 15) + $index + 1;
                                $gross = $item->quantity * $item->unit_price;
                                $discount = $item->discount_amount ?? 0;
                                $tax = $item->tax_amount ?? 0;
                                $total = $gross - $discount + $tax;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $itemNumber }}</td>
                                <td>
                                    <div class="product-name">{{ $item->product->name ?? $item->product_name }}</div>
                                    @if($item->product && $item->product->sku)
                                        <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">
                                    @if($discount > 0)
                                        <span class="discount-amount">-₹{{ number_format($discount, 2) }}</span>
                                        @if($item->discount_percentage)
                                            <br><small>({{ number_format($item->discount_percentage, 1) }}%)</small>
                                        @endif
                                    @else
                                        <span style="color: #ccc;">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->tax_percentage ?? 0 }}%</td>
                                <td class="text-right">
                                    @if($tax > 0)
                                        <span class="tax-amount">₹{{ number_format($tax, 2) }}</span>
                                    @else
                                        <span style="color: #ccc;">-</span>
                                    @endif
                                </td>
                                <td class="text-right"><strong>₹{{ number_format($total, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-left">
                @if($sale->notes)
                    <div class="notes-section">
                        <div class="section-title">Notes</div>
                        <p>{{ $sale->notes }}</p>
                    </div>
                @endif
                
                @if($sale->custom_tax_enabled && $sale->tax_notes)
                    <div class="tax-details">
                        <strong>Tax Notes:</strong> {{ $sale->tax_notes }}
                    </div>
                @endif
            </div>
            
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    
                    @if($sale->discount_amount > 0)
                        <tr>
                            <td class="label">Discount:</td>
                            <td class="amount discount-amount">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    
                    @if($sale->cgst_amount > 0 || $sale->sgst_amount > 0)
                        @if($sale->cgst_amount > 0)
                            <tr>
                                <td class="label">CGST:</td>
                                <td class="amount tax-amount">₹{{ number_format($sale->cgst_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if($sale->sgst_amount > 0)
                            <tr>
                                <td class="label">SGST:</td>
                                <td class="amount tax-amount">₹{{ number_format($sale->sgst_amount, 2) }}</td>
                            </tr>
                        @endif
                    @elseif($sale->tax_amount > 0)
                        <tr>
                            <td class="label">Tax:</td>
                            <td class="amount tax-amount">₹{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                    @endif
                    
                    <tr class="total-row">
                        <td class="label">TOTAL AMOUNT:</td>
                        <td class="amount">₹{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </table>
                
                <div class="payment-summary" style="margin-top: 15px;">
                    <div><strong>Amount Paid:</strong> ₹{{ number_format($sale->paid_amount, 2) }}</div>
                    @if($sale->change_amount > 0)
                        <div><strong>Change Returned:</strong> ₹{{ number_format($sale->change_amount, 2) }}</div>
                    @endif
                    <div style="margin-top: 5px; font-size: 11px;">
                        Payment via {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="thank-you">Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</div>
            <div class="footer-note">
                This invoice was generated on {{ now()->format('d/m/Y \a\t h:i A') }}<br>
                For any queries, please contact us at {{ (isset($globalCompany->company_phone) && $globalCompany->company_phone) ? $globalCompany->company_phone : ((isset($globalCompany->company_email) && $globalCompany->company_email) ? $globalCompany->company_email : 'our store') }}
            </div>
            
            @if(isset($globalCompany->gst_number) && $globalCompany->gst_number)
                <div style="margin-top: 10px; font-size: 10px; color: #888;">
                    This is a computer generated invoice. GST compliance ensured.
                </div>
            @endif
        </div>
    </div>
</body>
</html>