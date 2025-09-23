<!DOCTYPE html>
<html>
<head>
    <?php 

// echo"<pre>";print_R($sale->items);exit;

?>
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
            {{-- <div>Natural & Organic Products</div> --}}
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
        
        <!-- Enhanced Items with Product-wise Details -->
        <div>
            @foreach($sale->items as $index => $item)
                @php
                    $unitPrice = $item->unit_price ?? 0;
                    $offerAmount = $item->original_price ?? 0;
                    $mrp = $item->product->price;
                    $originalPrice = $item->product->price;
                    $quantity = $item->quantity ?? 1;
                    $lineTotal = $unitPrice * $quantity;
                    $itemDiscount = $item->discount_amount ?? 0;
                    $afterDiscount = $lineTotal - $itemDiscount;
                    $taxAmount = $item->tax_amount ?? 0;
                    $finalAmount = $afterDiscount ;
                    $hasOfferPrice = $originalPrice > $unitPrice && $originalPrice != $unitPrice;
                  
                    $offerSavings = ($originalPrice - $unitPrice) * $quantity;
                @endphp
                
                <div style="margin-bottom: 8px; border-bottom: 1px dashed #666; padding-bottom: 6px;">
                    <!-- Product Name -->
                    <div class="bold" style="font-size: 11px; margin-bottom: 2px;">{{ $index + 1 }}. {{ $item->product->name ?? $item->product_name }}</div>
                    
                    @if($item->product && $item->product->sku)
                        <div style="font-size: 8px; color: #666; margin-bottom: 3px;">SKU: {{ $item->product->sku }}</div>
                    @endif
                    
                    <!-- Quantity and Pricing -->
                    @if($hasOfferPrice)
                        <!-- When there's an offer price -->
                        <div class="item-row">
                            <span>{{ $quantity }} × ₹{{ number_format($unitPrice, 2) }}(Offer) - MRP {{ number_format($originalPrice, 2) }}</span>
                            <span class="bold">₹{{ number_format($lineTotal, 2) }}</span>
                        </div>
                        @if($item->offer_applied)
                            <div style="font-size: 8px; color: #000;">{{ $item->offer_applied }}</div>
                        @endif
                    @else
                        <!-- When selling price equals MRP -->
                        <div class="item-row">
                            <span>{{ $quantity }} × ₹{{ number_format($unitPrice, 2) }}(MRP)</span>
                            <span class="bold">₹{{ number_format($lineTotal, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- Item Discount -->
                    @if($itemDiscount > 0)
                        <div class="item-row" style="color: #000; font-size: 9px;">
                            <span>Item Discount ({{ number_format($item->discount_percentage ?? 0, 1) }}%)</span>
                            <span>-₹{{ number_format($itemDiscount, 2) }}</span>
                        </div>
                        <div class="item-row">
                            <span>After Discount</span>
                            <span>₹{{ number_format($afterDiscount, 2) }}</span>
                        </div>
                    @endif
                    
                    <!-- Tax -->
                    @if($taxAmount > 0)
                        <div class="item-row" style="color: #666; font-size: 9px;">
                            <span>Tax ({{ $item->tax_percentage ?? 0 }}%)</span>
                            <span> Tax included in MRP</span>
                        </div>
                    @endif
                    
                    <!-- Item Total -->
                    <div style="font-weight: bold; font-size: 11px; text-align: right; margin-top: 3px; border-top: 1px solid #000; padding-top: 2px;">
                        Item Total: ₹{{ number_format($finalAmount, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="totals">
            @php
                $itemsGrossTotal = $sale->items->sum(function($item) {
                    $originalPrice = $item->product->price;
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
                    <span>₹{{ number_format($sale->total_amount-($sale->cgst_amount+$sale->sgst_amount), 2) }}</span>
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
