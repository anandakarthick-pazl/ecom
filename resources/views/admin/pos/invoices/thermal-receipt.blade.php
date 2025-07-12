<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 5mm;
            size: 80mm auto; /* 80mm thermal paper width */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            width: 70mm;
            margin: 0 auto;
        }
        
        .receipt-container {
            width: 100%;
            max-width: 70mm;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .company-logo {
            max-height: 40px;
            max-width: 60mm;
            margin-bottom: 5px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .company-details {
            font-size: 9px;
            line-height: 1.2;
        }
        
        /* Invoice Info */
        .invoice-info {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .invoice-number {
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Customer Info */
        .customer-info {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        .customer-name {
            font-weight: bold;
            font-size: 11px;
        }
        
        /* Items Table */
        .items-section {
            margin-bottom: 8px;
        }
        
        .section-header {
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        
        .item-row {
            margin-bottom: 4px;
            font-size: 10px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        
        .item-price-row {
            display: flex;
            justify-content: space-between;
            margin-top: 1px;
        }
        
        /* Totals */
        .totals-section {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-bottom: 8px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .grand-total {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 0;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Payment Info */
        .payment-section {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        .payment-method {
            font-weight: bold;
            text-transform: uppercase;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            font-size: 9px;
            line-height: 1.3;
        }
        
        .thank-you {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
        }
        
        .footer-info {
            margin-top: 5px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        
        /* Special formatting */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-small { font-size: 8px; }
        
        /* Dotted lines for spacing */
        .separator {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        
        /* Print specific */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            @if(isset($globalCompany->company_logo_pdf) && $globalCompany->company_logo_pdf)
                <img src="{{ $globalCompany->company_logo_pdf }}" class="company-logo" alt="Logo">
            @endif
            
            <div class="company-name">{{ $globalCompany->company_name ?? 'Your Store' }}</div>
            
            <div class="company-details">
                @if(isset($globalCompany->full_address) && $globalCompany->full_address)
                    {{ $globalCompany->full_address }}<br>
                @endif
                
                @if(isset($globalCompany->company_phone) && $globalCompany->company_phone)
                    Tel: {{ $globalCompany->company_phone }}<br>
                @endif
                
                @if(isset($globalCompany->gst_number) && $globalCompany->gst_number)
                    GST: {{ $globalCompany->gst_number }}
                @endif
            </div>
        </div>

        <!-- Invoice Information -->
        <div class="invoice-info">
            <div class="invoice-row">
                <span>Receipt #:</span>
                <span class="invoice-number">{{ $sale->invoice_number }}</span>
            </div>
            <div class="invoice-row">
                <span>Date:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="invoice-row">
                <span>Cashier:</span>
                <span>{{ $sale->cashier->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Customer Information -->
        @if($sale->customer_name || $sale->customer_phone)
            <div class="customer-info">
                @if($sale->customer_name)
                    <div class="customer-name">Customer: {{ $sale->customer_name }}</div>
                @endif
                @if($sale->customer_phone)
                    <div>Phone: {{ $sale->customer_phone }}</div>
                @endif
            </div>
        @endif

        <!-- Items -->
        <div class="items-section">
            <div class="section-header">ITEMS PURCHASED</div>
            
            @foreach($sale->items as $item)
                @php
                    $gross = $item->quantity * $item->unit_price;
                    $discount = $item->discount_amount ?? 0;
                    $tax = $item->tax_amount ?? 0;
                    $total = $gross - $discount + $tax;
                @endphp
                
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name ?? $item->product_name }}</div>
                    
                    <div class="item-details">
                        <span>{{ $item->quantity }} x ₹{{ number_format($item->unit_price, 2) }}</span>
                        <span>₹{{ number_format($gross, 2) }}</span>
                    </div>
                    
                    @if($discount > 0 || $tax > 0)
                        <div class="item-price-row text-small">
                            @if($discount > 0)
                                <span>Discount:</span>
                                <span>-₹{{ number_format($discount, 2) }}</span>
                            @endif
                        </div>
                        
                        @if($tax > 0)
                            <div class="item-price-row text-small">
                                <span>Tax ({{ $item->tax_percentage ?? 0 }}%):</span>
                                <span>₹{{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="item-price-row font-bold">
                            <span>Item Total:</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                    @endif
                </div>
                
                @if(!$loop->last)
                    <div class="separator"></div>
                @endif
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            
            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            
            @if($sale->tax_amount > 0)
                @if($sale->cgst_amount > 0 && $sale->sgst_amount > 0)
                    <div class="total-row">
                        <span>CGST:</span>
                        <span>₹{{ number_format($sale->cgst_amount, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>SGST:</span>
                        <span>₹{{ number_format($sale->sgst_amount, 2) }}</span>
                    </div>
                @else
                    <div class="total-row">
                        <span>Tax:</span>
                        <span>₹{{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                @endif
            @endif
            
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>₹{{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="payment-section">
            <div class="total-row">
                <span>Payment Method:</span>
                <span class="payment-method">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
            </div>
            
            <div class="total-row">
                <span>Amount Paid:</span>
                <span>₹{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            
            @if($sale->change_amount > 0)
                <div class="total-row">
                    <span>Change:</span>
                    <span>₹{{ number_format($sale->change_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">THANK YOU!</div>
            <div>Visit Again</div>
            
            @if($sale->notes)
                <div style="margin: 5px 0;">
                    <strong>Note:</strong> {{ $sale->notes }}
                </div>
            @endif
            
            <div class="footer-info">
                <div>Generated: {{ now()->format('d/m/Y H:i') }}</div>
                @if(isset($globalCompany->company_email) && $globalCompany->company_email)
                    <div>{{ $globalCompany->company_email }}</div>
                @endif
                @if(isset($globalCompany->website) && $globalCompany->website)
                    <div>{{ $globalCompany->website }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>