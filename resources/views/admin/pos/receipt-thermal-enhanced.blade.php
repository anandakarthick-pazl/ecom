<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 0;
            padding: 15px;
            line-height: 1.3;
            width: 80mm;
            max-width: 80mm;
        }
        .receipt-container {
            max-width: 72mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-logo {
            max-height: 40px;
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 9px;
            line-height: 1.2;
        }
        
        /* Enhanced Item Display */
        .item-section {
            margin-bottom: 8px;
            border-bottom: 1px dashed #666;
            padding-bottom: 6px;
        }
        
        .item-header {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .item-sku {
            font-size: 8px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .price-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            font-size: 10px;
        }
        
        .selling-price {
            font-weight: bold;
            color: #000;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #666;
            font-size: 9px;
        }
        
        .offer-savings {
            color: #000;
            font-size: 9px;
        }
        
        .discount-line {
            color: #000;
            font-size: 9px;
        }
        
        .tax-line {
            color: #666;
            font-size: 9px;
        }
        
        .item-total {
            font-weight: bold;
            font-size: 11px;
            text-align: right;
            margin-top: 3px;
            border-top: 1px solid #000;
            padding-top: 2px;
        }
        
        .totals {
            border-top: 2px solid #000;
            padding-top: 8px;
            margin-top: 12px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        .total-row.highlight {
            font-weight: bold;
            font-size: 11px;
        }
        .final-total {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 4px 0;
            font-weight: bold;
            font-size: 12px;
            margin: 4px 0;
        }
        .footer {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 12px;
            text-align: center;
            font-size: 9px;
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        
        /* Summary section */
        .summary-section {
            background-color: #f5f5f5;
            padding: 6px;
            margin: 8px 0;
            border: 1px solid #ddd;
        }
        
        .summary-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 4px;
            text-align: center;
        }
        
        .summary-item {
            font-size: 8px;
            margin-bottom: 2px;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 8px; 
                width: 80mm;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header text-center">
            @if(!empty($globalCompany->company_logo_pdf))
                <img src="{{ $globalCompany->company_logo_pdf }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
            @elseif(!empty($globalCompany->company_logo))
                <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
            @else
                <div style="font-size: 16px;">🌿</div>
            @endif
            <div class="company-name">{{ strtoupper($globalCompany->company_name ?? 'HERBAL STORE') }}</div>
            @if(!empty($globalCompany->full_address))
                <div class="company-details">{{ $globalCompany->full_address }}</div>
            @else
                @if(!empty($globalCompany->company_address))
                    <div class="company-details">{{ $globalCompany->company_address }}</div>
                @endif
            @endif
            @if(!empty($globalCompany->company_phone))
                <div class="company-details">Phone: {{ $globalCompany->company_phone }}</div>
            @endif
            @if(!empty($globalCompany->gst_number))
                <div class="company-details">GST No: {{ $globalCompany->gst_number }}</div>
            @endif
        </div>
        
        <!-- Sale Info -->
        <div class="text-center">
            <div class="bold">SALES RECEIPT</div>
            <div class="dashed-line"></div>
        </div>
        
        <div style="font-size: 9px;">
            <div><strong>Invoice #:</strong> {{ $sale->invoice_number }}</div>
            <div><strong>Date:</strong> {{ $sale->created_at->format('d/m/Y h:i A') }}</div>
            <div><strong>Cashier:</strong> {{ $sale->cashier->name ?? 'POS' }}</div>
            @if($sale->customer_name)
                <div><strong>Customer:</strong> {{ $sale->customer_name }}</div>
            @endif
            @if($sale->customer_phone)
                <div><strong>Phone:</strong> {{ $sale->customer_phone }}</div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Enhanced Items with Product-wise Details -->
        <div>
            @foreach($sale->items as $index => $item)
                @php
                    $unitPrice = $item->unit_price ?? 0;
                    $originalPrice = $item->original_price ?? $item->product->price ?? $unitPrice;
                    $quantity = $item->quantity ?? 1;
                    $lineTotal = $unitPrice * $quantity;
                    $itemDiscount = $item->discount_amount ?? 0;
                    $afterDiscount = $lineTotal - $itemDiscount;
                    $taxAmount = $item->tax_amount ?? 0;
                    $finalAmount = $afterDiscount + $taxAmount;
                    $hasOfferPrice = $originalPrice > $unitPrice && $originalPrice != $unitPrice;
                    $offerSavings = ($originalPrice - $unitPrice) * $quantity;
                @endphp
                
                <div class="item-section">
                    <!-- Product Name -->
                    <div class="item-header">{{ $index + 1 }}. {{ $item->product->name ?? $item->product_name }}</div>
                    
                    @if($item->product && $item->product->sku)
                        <div class="item-sku">SKU: {{ $item->product->sku }}</div>
                    @endif
                    
                    <!-- Quantity and Pricing -->
                    @if($hasOfferPrice)
                        <!-- When there's an offer price -->
                        <div class="price-line">
                            <span>{{ $quantity }} × Offer: ₹{{ number_format($unitPrice, 2) }} MRP {{ number_format($originalPrice, 2) }}</span>
                            <span class="selling-price">₹{{ number_format($lineTotal, 2) }}</span>
                        </div>
                        @if($item->offer_applied)
                            <div style="font-size: 8px; color: #000;">{{ $item->offer_applied }}</div>
                        @endif
                    @else
                        <!-- When selling price equals MRP -->
                        <div class="price-line">
                            <span>{{ $quantity }} × ₹{{ number_format($unitPrice, 2) }}</span>
                            <span class="selling-price">₹{{ number_format($lineTotal, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- Item Discount -->
                    @if($itemDiscount > 0)
                        <div class="price-line discount-line">
                            <span>Item Discount ({{ number_format($item->discount_percentage ?? 0, 1) }}%)</span>
                            <span>-₹{{ number_format($itemDiscount, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- After Discount -->
                    @if($itemDiscount > 0)
                        <div class="price-line">
                            <span>After Discount</span>
                            <span>₹{{ number_format($afterDiscount, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- Tax -->
                    @if($taxAmount > 0)
                        <div class="price-line tax-line">
                            <span>Tax ({{ $item->tax_percentage ?? 0 }}%)</span>
                            <span>+₹{{ number_format($taxAmount, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- Item Total -->
                    <div class="item-total">
                        Item Total: ₹{{ number_format($finalAmount, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="totals">
            @php
                $itemsGrossTotal = $sale->items->sum(function($item) {
                    $originalPrice = $item->original_price ?? $item->product->price ?? $item->unit_price;
                    return $originalPrice * $item->quantity;
                });
                $itemsSellingTotal = $sale->items->sum(function($item) {
                    return $item->unit_price * $item->quantity;
                });
                $totalOfferSavings = $itemsGrossTotal - $itemsSellingTotal;
                $totalItemDiscounts = $sale->items->sum('discount_amount');
            @endphp
            
            @if($totalOfferSavings > 0)
                <div class="total-row">
                    <span>Items MRP Total:</span>
                    <span>₹{{ number_format($itemsGrossTotal, 2) }}</span>
                </div>
                <div class="total-row" style="color: #000;">
                    <span>Offer Savings:</span>
                    <span>-₹{{ number_format($totalOfferSavings, 2) }}</span>
                </div>
            @endif
            
            <div class="total-row">
                <span>Items Selling Total:</span>
                <span>₹{{ number_format($itemsSellingTotal, 2) }}</span>
            </div>
            
            @if($totalItemDiscounts > 0)
                <div class="total-row">
                    <span>Item Discounts:</span>
                    <span>-₹{{ number_format($totalItemDiscounts, 2) }}</span>
                </div>
            @endif
            
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
                    <span>Bill Discount:</span>
                    <span>-₹{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            
            <div class="final-total">
                <div class="total-row">
                    <span>TOTAL:</span>
                    <span>₹{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
            
            <div class="total-row highlight">
                <span>Payment ({{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}):</span>
                <span>₹{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            
            @if($sale->change_amount > 0)
                <div class="total-row">
                    <span>Change:</span>
                    <span>₹{{ number_format($sale->change_amount, 2) }}</span>
                </div>
            @endif
            
            @php
                $totalSavings = $totalOfferSavings + $totalItemDiscounts + $sale->discount_amount;
            @endphp
            @if($totalSavings > 0)
                <div class="dashed-line"></div>
                <div class="total-row highlight" style="color: #000;">
                    <span>🎉 YOU SAVED:</span>
                    <span>₹{{ number_format($totalSavings, 2) }}</span>
                </div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items Summary -->
        <div class="text-center" style="font-size: 9px;">
            <div>Total Items: {{ $sale->items->sum('quantity') }}</div>
            <div>Products: {{ $sale->items->count() }}</div>
        </div>
        
        @if($sale->custom_tax_enabled && $sale->tax_notes)
            <div class="dashed-line"></div>
            <div style="font-size: 8px;">
                <strong>Tax Notes:</strong> {{ $sale->tax_notes }}
            </div>
        @endif
        
        @if($sale->notes)
            <div class="dashed-line"></div>
            <div style="font-size: 8px;">
                <strong>Sale Notes:</strong> {{ $sale->notes }}
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="bold" style="font-size: 10px;">Thank you for shopping!</div>
            <div>Visit us again soon</div>
            <br>
            <div style="font-size: 8px;">
                @if(!empty($globalCompany->company_phone))
                    For queries: {{ $globalCompany->company_phone }}<br>
                @endif
                @if(!empty($globalCompany->company_email))
                    Email: {{ $globalCompany->company_email }}<br>
                @endif
                Return within 7 days with receipt
            </div>
        </div>
        
        <!-- Print Button (only visible on screen) -->
        <div class="no-print text-center" style="margin-top: 15px;">
            <button onclick="window.print()" style="padding: 8px 15px; font-size: 12px;">
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