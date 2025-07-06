<!DOCTYPE html>
<html>
<head>
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .item-details {
            flex: 1;
        }
        .item-price {
            text-align: right;
            min-width: 60px;
        }
        .totals {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 15px;
            text-align: center;
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .company-logo {
            max-height: 50px;
            margin-bottom: 5px;
        }
        @media print {
            body { margin: 0; padding: 10px; }
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
                <div style="font-size: 20px;">🌿</div>
            @endif
            <div class="bold" style="font-size: 16px;">{{ strtoupper($globalCompany->company_name ?? 'HERBAL BLISS') }}</div>
            <div>Natural & Organic Products</div>
            @if($globalCompany->company_address ?? false)
                <div>{{ $globalCompany->company_address }}</div>
            @endif
            @if($globalCompany->company_phone ?? false)
                <div>Phone: {{ $globalCompany->company_phone }}</div>
            @endif
            @if($globalCompany->gst_number ?? false)
                <div>GST No: {{ $globalCompany->gst_number }}</div>
            @endif
        </div>
        
        <!-- Sale Info -->
        <div class="text-center">
            <div class="bold">SALES RECEIPT</div>
            <div class="dashed-line"></div>
        </div>
        
        <div>
            <div><strong>Invoice #:</strong> {{ $sale->invoice_number }}</div>
            <div><strong>Date:</strong> {{ $sale->created_at->format('M d, Y h:i A') }}</div>
            <div><strong>Cashier:</strong> {{ $sale->cashier->name }}</div>
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
                        @if(($item->discount_amount ?? 0) > 0)
                            <div style="font-size: 10px; color: #dc3545;">Item Discount: -₹{{ number_format($item->discount_amount, 2) }} ({{ number_format($item->discount_percentage ?? 0, 1) }}%)</div>
                        @endif
                        @if(($item->tax_percentage ?? 0) > 0)
                            <div style="font-size: 10px; color: #666;">Tax: {{ $item->tax_percentage }}% = ₹{{ number_format($item->tax_amount ?? 0, 2) }}</div>
                        @endif
                    </div>
                    <div class="item-price">
                        @php
                            $itemGross = $item->quantity * $item->unit_price;
                            $itemDiscount = $item->discount_amount ?? 0;
                            $itemNet = $itemGross - $itemDiscount;
                            $itemTotal = $itemNet + ($item->tax_amount ?? 0);
                        @endphp
                        @if($itemDiscount > 0)
                            <div style="font-size: 10px; text-decoration: line-through; color: #999;">₹{{ number_format($itemGross, 2) }}</div>
                        @endif
                        ₹{{ number_format($itemTotal, 2) }}
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
            
            @if($sale->tax_amount > 0)
                <div class="total-row">
                    <span>CGST:</span>
                    <span>₹{{ number_format($sale->cgst_amount, 2) }}</span>
                </div>
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
                <span>Payment ({{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}{{ $sale->payment_method === 'gpay' ? 'Pay' : ($sale->payment_method === 'phonepe' ? 'Pe' : '') }}):</span>
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
            <div>Total Items: {{ $sale->total_items }}</div>
            <div>Products: {{ $sale->items->count() }}</div>
        </div>
        
        @if($sale->custom_tax_enabled && $sale->tax_notes)
            <div class="dashed-line"></div>
            <div>
                <strong>Tax Notes:</strong> {{ $sale->tax_notes }}
            </div>
        @endif
        
        @if($sale->notes)
            <div class="dashed-line"></div>
            <div>
                <strong>Sale Notes:</strong> {{ $sale->notes }}
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="bold">Thank you for shopping with us!</div>
            <div>Visit us again soon</div>
            <br>
            <div style="font-size: 10px;">
                @if($globalCompany->company_phone ?? false)
                    For any queries, call {{ $globalCompany->company_phone }}<br>
                @endif
                @if($globalCompany->company_email ?? false)
                    or email: {{ $globalCompany->company_email }}
                @endif
            </div>
            <br>
            <div style="font-size: 10px;">
                Return Policy: Items can be returned within<br>
                7 days with original receipt
            </div>
        </div>
        
        <!-- Print Button (only visible on screen) -->
        <div class="no-print text-center" style="margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">
                🖨️ Print Receipt
            </button>
        </div>
    </div>
    
    <script>
        // Auto-print when loaded in print mode
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>
