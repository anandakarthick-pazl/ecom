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
            font-family: 'Courier New', 'DejaVu Sans Mono', monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: white;
            width: 80mm;
            max-width: 80mm;
            margin: 0;
            padding: 3mm; /* Reduced padding to prevent cutting */
        }
        
        .receipt-container {
            width: 100%;
            max-width: 74mm; /* Reduced width to ensure content fits */
            overflow: hidden; /* Prevent horizontal overflow */
            word-wrap: break-word;
        }
        
        /* Text Alignment */
        .text-center { 
            text-align: center; 
        }
        .text-right { 
            text-align: right; 
        }
        .text-left { 
            text-align: left; 
        }
        .bold { 
            font-weight: bold; 
        }
        
        /* ENHANCED: Header Section with Better Logo Handling */
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
            text-align: center;
        }
        
        /* FIXED: Better Logo Handling for Thermal */
        .company-logo {
            max-width: 60px; /* Reduced size for thermal */
            max-height: 50px; /* Reduced height */
            width: auto;
            height: auto;
            margin: 0 auto 4px auto;
            display: block;
            object-fit: contain; /* Prevent logo distortion */
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            word-wrap: break-word;
            line-height: 1.2;
        }
        
        .company-details {
            font-size: 9px;
            line-height: 1.2;
            margin-bottom: 1px;
            word-wrap: break-word;
        }
        
        /* ENHANCED: Sale Information */
        .sale-info {
            font-size: 10px;
            margin-bottom: 8px;
        }
        
        .sale-info div {
            margin-bottom: 1px;
            word-wrap: break-word;
        }
        
        /* ENHANCED: Divider Lines */
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
            width: 100%;
        }
        
        .solid-line {
            border-top: 1px solid #000;
            margin: 6px 0;
            width: 100%;
        }
        
        /* ENHANCED: Items Section */
        .items-section {
            margin-bottom: 8px;
        }
        
        .item-row {
            margin-bottom: 4px;
            font-size: 10px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
            word-wrap: break-word;
            line-height: 1.2;
            overflow: hidden; /* Prevent text overflow */
        }
        
        .item-details {
            margin-bottom: 1px;
        }
        
        .item-price {
            text-align: right;
            font-weight: bold;
        }
        
        .item-extras {
            font-size: 8px;
            color: #333;
            margin-bottom: 1px;
            word-wrap: break-word;
        }
        
        /* ENHANCED: Price Display with Table Layout */
        .price-line {
            display: table;
            width: 100%;
            margin-bottom: 1px;
            table-layout: fixed; /* Prevent layout issues */
        }
        
        .price-label {
            display: table-cell;
            text-align: left;
            vertical-align: top;
            width: 60%; /* Fixed width */
            word-wrap: break-word;
            padding-right: 5px;
        }
        
        .price-value {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            font-weight: bold;
            width: 40%; /* Fixed width */
            word-wrap: break-word;
        }
        
        /* ENHANCED: Totals Section */
        .totals {
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 8px;
            font-size: 10px;
        }
        
        .total-row {
            margin-bottom: 2px;
        }
        
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 4px 0;
            margin: 6px 0;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* ENHANCED: Payment Section */
        .payment-section {
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 8px;
            font-size: 10px;
        }
        
        /* ENHANCED: Summary Section */
        .summary-section {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            font-size: 9px;
            text-align: center;
        }
        
        /* ENHANCED: Notes Section */
        .notes-section {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            font-size: 8px;
            word-wrap: break-word;
        }
        
        /* ENHANCED: Footer */
        .footer {
            border-top: 1px solid #000;
            padding-top: 6px;
            margin-top: 12px;
            text-align: center;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .footer-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 4px;
        }
        
        /* Discount Highlight */
        .discount-highlight {
            color: #333;
            font-size: 8px;
        }
        
        .savings-highlight {
            font-weight: bold;
            font-size: 9px;
        }
        
        /* Strikethrough */
        .strikethrough {
            text-decoration: line-through;
            color: #666;
            font-size: 8px;
        }
        
        /* ENHANCED: Prevent Text Overflow */
        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .text-wrap {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }
        
        /* ENHANCED: Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 2mm;
                width: 80mm;
            }
            .no-print {
                display: none;
            }
            .receipt-container {
                max-width: 76mm;
            }
        }
        
        /* ENHANCED: Ensure content fits within thermal width */
        @page {
            size: 80mm auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- ENHANCED: Header with Better Logo Handling -->
        <div class="header text-center">
            @if(!empty($globalCompany->company_logo_base64))
                {{-- Use base64 encoded logo for better PDF compatibility --}}
                <img src="{{ $globalCompany->company_logo_base64 }}" alt="{{ $globalCompany->company_name ?? 'Logo' }}" class="company-logo">
            @elseif(!empty($globalCompany->company_logo_path) && file_exists($globalCompany->company_logo_path))
                {{-- Fallback to file path if base64 not available --}}
                <img src="{{ $globalCompany->company_logo_path }}" alt="{{ $globalCompany->company_name ?? 'Logo' }}" class="company-logo">
            @elseif(!empty($globalCompany->company_logo))
                {{-- Try original logo path as last resort --}}
                <img src="{{ public_path('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name ?? 'Logo' }}" class="company-logo">
            @else
                {{-- Fallback icon if no logo --}}
                <div style="font-size: 16px; margin-bottom: 4px;">🌿</div>
            @endif
            
            <div class="company-name text-wrap">{{ strtoupper($globalCompany->company_name ?? 'HERBAL STORE') }}</div>
            
            @if(!empty($globalCompany->company_address))
                <div class="company-details text-wrap">{{ $globalCompany->company_address }}</div>
            @endif
            
            @if(!empty($globalCompany->city))
                <div class="company-details text-wrap">
                    {{ $globalCompany->city }}@if(!empty($globalCompany->state)), {{ $globalCompany->state }}@endif
                </div>
            @endif
            
            @if(!empty($globalCompany->company_phone))
                <div class="company-details">Ph: {{ $globalCompany->company_phone }}</div>
            @endif
            
            @if(!empty($globalCompany->company_email))
                <div class="company-details text-wrap">{{ $globalCompany->company_email }}</div>
            @endif
            
            @if(!empty($globalCompany->gst_number))
                <div class="company-details">GST: {{ $globalCompany->gst_number }}</div>
            @endif
        </div>
        
        <!-- Sale Information -->
        <div class="text-center">
            <div class="bold" style="font-size: 12px;">SALES RECEIPT</div>
        </div>
        
        <div class="dashed-line"></div>
        
        <div class="sale-info">
            <div><strong>Invoice:</strong> {{ $sale->invoice_number }}</div>
            <div><strong>Date:</strong> {{ $sale->created_at->format('d M Y, h:i A') }}</div>
            @if($sale->cashier)
                <div class="text-wrap"><strong>Cashier:</strong> {{ $sale->cashier->name }}</div>
            @endif
            @if($sale->customer_name)
                <div class="text-wrap"><strong>Customer:</strong> {{ $sale->customer_name }}</div>
            @endif
            @if($sale->customer_phone)
                <div><strong>Phone:</strong> {{ $sale->customer_phone }}</div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- ENHANCED: Items with Better Layout -->
        <div class="items-section">
            @foreach($sale->items as $index => $item)
                <div class="item-row">
                    <div class="item-name text-wrap">{{ $item->product->name ?? $item->product_name }}</div>
                    
                    <div class="price-line">
                        <div class="price-label">{{ $item->quantity }} x ₹{{ number_format($item->unit_price, 2) }}</div>
                        <div class="price-value">
                            @php
                                $itemGross = $item->quantity * $item->unit_price;
                                $itemDiscount = $item->discount_amount ?? 0;
                                $itemNet = $itemGross - $itemDiscount;
                                $itemTotal = $itemNet + ($item->tax_amount ?? 0);
                            @endphp
                            @if($itemDiscount > 0)
                                <div class="strikethrough">₹{{ number_format($itemGross, 2) }}</div>
                            @endif
                            ₹{{ number_format($itemTotal, 2) }}
                        </div>
                    </div>
                    
                    @if(($item->discount_amount ?? 0) > 0)
                        <div class="item-extras discount-highlight text-wrap">
                            Item Disc: -₹{{ number_format($item->discount_amount, 2) }} ({{ number_format($item->discount_percentage ?? 0, 1) }}%)
                        </div>
                    @endif
                    
                    @if(($item->tax_percentage ?? 0) > 0)
                        <div class="item-extras">
                            Tax {{ $item->tax_percentage }}%: ₹{{ number_format($item->tax_amount ?? 0, 2) }}
                        </div>
                    @endif
                    
                    @if($index < $sale->items->count() - 1)
                        <div style="border-top: 1px dotted #ccc; margin: 3px 0;"></div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- ENHANCED: Totals -->
        <div class="totals">
            @php
                $itemsSubtotal = $sale->items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                });
                $totalItemDiscounts = $sale->items->sum('discount_amount');
            @endphp
            
            @if($totalItemDiscounts > 0)
                <div class="price-line total-row">
                    <div class="price-label">Items Gross:</div>
                    <div class="price-value">₹{{ number_format($itemsSubtotal, 2) }}</div>
                </div>
                
                <div class="price-line total-row">
                    <div class="price-label">Item Discounts:</div>
                    <div class="price-value">-₹{{ number_format($totalItemDiscounts, 2) }}</div>
                </div>
            @endif
            
            <div class="price-line total-row">
                <div class="price-label">Subtotal:</div>
                <div class="price-value">₹{{ number_format($sale->subtotal, 2) }}</div>
            </div>
            
            @if($sale->cgst_amount > 0)
                <div class="price-line total-row">
                    <div class="price-label">CGST:</div>
                    <div class="price-value">₹{{ number_format($sale->cgst_amount, 2) }}</div>
                </div>
            @endif
            
            @if($sale->sgst_amount > 0)
                <div class="price-line total-row">
                    <div class="price-label">SGST:</div>
                    <div class="price-value">₹{{ number_format($sale->sgst_amount, 2) }}</div>
                </div>
            @endif
            
            @if($sale->discount_amount > 0)
                <div class="price-line total-row">
                    <div class="price-label">Sale Discount:</div>
                    <div class="price-value">-₹{{ number_format($sale->discount_amount, 2) }}</div>
                </div>
            @endif
        </div>
        
        <!-- ENHANCED: Final Total -->
        <div class="final-total">
            <div class="price-line">
                <div class="price-label">TOTAL:</div>
                <div class="price-value">₹{{ number_format($sale->total_amount, 2) }}</div>
            </div>
        </div>
        
        <!-- ENHANCED: Payment Details -->
        <div class="payment-section">
            <div class="price-line">
                <div class="price-label">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}:</div>
                <div class="price-value">₹{{ number_format($sale->paid_amount, 2) }}</div>
            </div>
            
            @if($sale->change_amount > 0)
                <div class="price-line">
                    <div class="price-label">Change:</div>
                    <div class="price-value">₹{{ number_format($sale->change_amount, 2) }}</div>
                </div>
            @endif
        </div>
        
        <!-- ENHANCED: Summary -->
        <div class="summary-section text-center">
            <div><strong>Items:</strong> {{ $sale->items->sum('quantity') }} | <strong>Products:</strong> {{ $sale->items->count() }}</div>
            
            @php
                $totalSavings = $sale->items->sum('discount_amount') + $sale->discount_amount;
            @endphp
            @if($totalSavings > 0)
                <div class="savings-highlight" style="margin-top: 4px;">
                    You Saved: ₹{{ number_format($totalSavings, 2) }}
                    @php
                        $originalTotal = $sale->items->sum(function($item) {
                            return $item->quantity * $item->unit_price;
                        }) + $sale->tax_amount;
                        $savingsPercent = $originalTotal > 0 ? ($totalSavings / $originalTotal) * 100 : 0;
                    @endphp
                    ({{ number_format($savingsPercent, 1) }}%)
                </div>
            @endif
        </div>
        
        <!-- ENHANCED: Notes -->
        @if($sale->custom_tax_enabled && $sale->tax_notes)
            <div class="notes-section">
                <div class="bold">Tax Notes:</div>
                <div class="text-wrap">{{ $sale->tax_notes }}</div>
            </div>
        @endif
        
        @if($sale->notes)
            <div class="notes-section">
                <div class="bold">Sale Notes:</div>
                <div class="text-wrap">{{ $sale->notes }}</div>
            </div>
        @endif
        
        <!-- ENHANCED: Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="footer-title">Thank you for shopping!</div>
            <div>Visit us again soon</div>
            
            @if(!empty($globalCompany->company_phone) || !empty($globalCompany->company_email))
                <div style="margin-top: 6px;">
                    @if(!empty($globalCompany->company_phone))
                        Ph: {{ $globalCompany->company_phone }}
                    @endif
                    @if(!empty($globalCompany->company_phone) && !empty($globalCompany->company_email))
                        <br>
                    @endif
                    @if(!empty($globalCompany->company_email))
                        <div class="text-wrap">{{ $globalCompany->company_email }}</div>
                    @endif
                </div>
            @endif
            
            <div style="margin-top: 6px;">
                Return Policy: Items can be returned<br>
                within 7 days with original receipt
            </div>
            
            <div style="margin-top: 6px; font-size: 7px;">
                Generated: {{ now()->format('d/m/Y h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>