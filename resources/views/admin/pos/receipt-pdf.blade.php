<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            line-height: 1.3;
            max-width: 280px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .company-logo {
            max-height: 40px;
            margin-bottom: 5px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }
        
        .item-details {
            flex: 1;
            padding-right: 5px;
        }
        
        .item-price {
            text-align: right;
            min-width: 50px;
        }
        
        .totals {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }
        
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 4px 0;
            font-weight: bold;
            font-size: 13px;
        }
        
        .footer {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
        }
        
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        
        @media print {
            body { margin: 0; padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header text-center">
            @if($globalCompany->company_logo ?? false)
                <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
            @else
                <div style="font-size: 18px;">🌿</div>
            @endif
            <div class="bold" style="font-size: 14px;">{{ strtoupper($globalCompany->company_name ?? 'HERBAL STORE') }}</div>
            <div>Natural & Organic Products</div>
            @if($globalCompany->company_address ?? false)
                <div style="font-size: 10px;">{{ $globalCompany->company_address }}</div>
            @endif
            @if($globalCompany->company_phone ?? false)
                <div style="font-size: 10px;">Ph: {{ $globalCompany->company_phone }}</div>
            @endif
            @if($globalCompany->gst_number ?? false)
                <div style="font-size: 10px;">GST: {{ $globalCompany->gst_number }}</div>
            @endif
        </div>
        
        <!-- Sale Info -->
        <div class="text-center">
            <div class="bold">SALES RECEIPT</div>
            <div class="dashed-line"></div>
        </div>
        
        <div style="font-size: 11px;">
            <div><strong>Invoice #:</strong> {{ $sale->invoice_number }}</div>
            <div><strong>Date:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}</div>
            <div><strong>Cashier:</strong> {{ $sale->cashier->name ?? 'POS' }}</div>
            @if($sale->customer_name)
                <div><strong>Customer:</strong> {{ $sale->customer_name }}</div>
            @endif
            @if($sale->customer_phone)
                <div><strong>Phone:</strong> {{ $sale->customer_phone }}</div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items -->
        <div>
            @foreach($sale->items as $item)
                <div class="item-row">
                    <div class="item-details">
                        <div class="bold">{{ $item->product->name ?? $item->product_name }}</div>
                        <div>{{ $item->quantity }} x ₹{{ number_format($item->unit_price, 2) }}</div>
                        @if($item->tax_percentage > 0)
                            <div style="font-size: 9px; color: #666;">Tax: {{ $item->tax_percentage }}%</div>
                        @endif
                    </div>
                    <div class="item-price">
                        ₹{{ number_format(($item->quantity * $item->unit_price), 2) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            
            @if($sale->cgst_amount > 0)
                <div class="total-row">
                    <span>CGST:</span>
                    <span>₹{{ number_format($sale->cgst_amount, 2) }}</span>
                </div>
            @endif
            
            @if($sale->sgst_amount > 0)
                <div class="total-row">
                    <span>SGST:</span>
                    <span>₹{{ number_format($sale->sgst_amount, 2) }}</span>
                </div>
            @endif
            
            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            
            <div class="final-total">
                <div class="total-row">
                    <span>TOTAL:</span>
                    <span>₹{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
            
            <div class="total-row">
                <span>Payment ({{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}):</span>
                <span>₹{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            
            @if($sale->change_amount > 0)
                <div class="total-row">
                    <span>Change:</span>
                    <span>₹{{ number_format($sale->change_amount, 2) }}</span>
                </div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items Summary -->
        <div class="text-center">
            <div>Total Items: {{ $sale->total_items ?? $sale->items->sum('quantity') }}</div>
            <div>Products: {{ $sale->items->count() }}</div>
        </div>
        
        @if($sale->notes)
            <div class="dashed-line"></div>
            <div style="font-size: 10px;">
                <strong>Notes:</strong> {{ $sale->notes }}
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="bold">Thank you for shopping with us!</div>
            <div>Visit us again soon</div>
            <br>
            <div style="font-size: 9px;">
                @if($globalCompany->company_phone ?? false)
                    For queries: {{ $globalCompany->company_phone }}<br>
                @endif
                @if($globalCompany->company_email ?? false)
                    Email: {{ $globalCompany->company_email }}
                @endif
            </div>
            <br>
            <div style="font-size: 9px;">
                Return Policy: Items can be returned<br>
                within 7 days with original receipt
            </div>
        </div>
    </div>
</body>
</html>