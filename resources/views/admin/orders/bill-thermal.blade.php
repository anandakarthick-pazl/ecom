<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill - {{ $order->order_number }}</title>
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
            @if($company['logo'] ?? false)
                <img src="{{ asset('storage/' . $company['logo']) }}" alt="{{ $company['name'] }}" class="company-logo">
            @else
                <div style="font-size: 18px;">🌿</div>
            @endif
            <div class="bold" style="font-size: 14px;">{{ strtoupper($company['name'] ?? 'HERBAL STORE') }}</div>
            {{-- <div>Natural & Organic Products</div> --}}
            @if($company['address'] ?? false)
                <div style="font-size: 10px;">{{ $company['address'] }}</div>
            @endif
            @if($company['phone'] ?? false)
                <div style="font-size: 10px;">Ph: {{ $company['phone'] }}</div>
            @endif
        </div>
        
        <!-- Order Info -->
        <div class="text-center">
            <div class="bold">ORDER BILL</div>
            <div class="dashed-line"></div>
        </div>
        
        <div style="font-size: 11px;">
            <div><strong>Order #:</strong> {{ $order->order_number }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</div>
            <div><strong>Customer:</strong> {{ $order->customer_name }}</div>
            @if($order->customer_mobile)
                <div><strong>Mobile:</strong> {{ $order->customer_mobile }}</div>
            @endif
            @if($order->customer_email)
                <div><strong>Email:</strong> {{ $order->customer_email }}</div>
            @endif
        </div>
        
        <div class="dashed-line"></div>
        
        <!-- Items -->
        <div>
            @foreach($order->items as $item)
                <div class="item-row">
                    <div class="item-details">
                        <div class="bold">{{ $item->product_name }}</div>
                        <div>{{ $item->quantity }} x ₹{{ number_format($item->price, 2) }}</div>
                        @if($item->tax_percentage > 0)
                            <div style="font-size: 9px; color: #666;">Tax: {{ $item->tax_percentage }}%</div>
                        @endif
                    </div>
                    <div class="item-price">
                        ₹{{ number_format($item->total, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            
            @if($order->cgst_amount > 0)
                <div class="total-row">
                    <span>CGST:</span>
                    <span>₹{{ number_format($order->cgst_amount, 2) }}</span>
                </div>
            @endif
            
            @if($order->sgst_amount > 0)
                <div class="total-row">
                    <span>SGST:</span>
                    <span>₹{{ number_format($order->sgst_amount, 2) }}</span>
                </div>
            @endif
            
            @if($order->discount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            
            @if($order->delivery_charge > 0)
                <div class="total-row">
                    <span>Delivery:</span>
                    <span>₹{{ number_format($order->delivery_charge, 2) }}</span>
                </div>
            @elseif($order->delivery_charge == 0 && !empty($order->delivery_address))
                <div class="total-row">
                    <span>Delivery:</span>
                    <span>FREE</span>
                </div>
            @endif
            
            <div class="final-total">
                <div class="total-row">
                    <span>TOTAL:</span>
                    <span>₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
            
            <div class="total-row">
                <span>Payment Status:</span>
                <span>{{ strtoupper($order->payment_status ?? 'PENDING') }}</span>
            </div>
            
            @if($order->payment_method)
                <div class="total-row">
                    <span>Payment Method:</span>
                    <span>{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                </div>
            @endif
        </div>
        
        @if($order->delivery_address)
            <div class="dashed-line"></div>
            <div style="font-size: 10px;">
                <strong>Delivery Address:</strong><br>
                {{ $order->delivery_address }}<br>
                {{ $order->city }}@if($order->state), {{ $order->state }}@endif {{ $order->pincode }}
            </div>
        @endif
        
        @if($order->notes)
            <div class="dashed-line"></div>
            <div style="font-size: 10px;">
                <strong>Order Notes:</strong> {{ $order->notes }}
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="bold">Thank you for your order!</div>
            <div>Visit us again soon</div>
            <br>
            <div style="font-size: 9px;">
                @if($company['phone'] ?? false)
                    For queries: {{ $company['phone'] }}<br>
                @endif
                @if($company['email'] ?? false)
                    Email: {{ $company['email'] }}
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