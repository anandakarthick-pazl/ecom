<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print Receipt - {{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }
        
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                font-size: 11px;
            }
            .no-print { 
                display: none !important; 
            }
            .print-only {
                display: block !important;
            }
        }
        
        @media screen {
            body {
                max-width: 80mm;
                margin: 20px auto;
                padding: 10px;
                border: 1px solid #ccc;
                background: white;
            }
            .print-only {
                display: none;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .company-logo {
            max-height: 40px;
            margin-bottom: 5px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .company-details {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .order-info {
            font-size: 11px;
            margin: 10px 0;
        }
        
        .order-info div {
            margin: 2px 0;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 10px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 2px;
        }
        
        .item-details {
            flex: 1;
            padding-right: 5px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .item-meta {
            font-size: 9px;
            color: #666;
        }
        
        .item-price {
            text-align: right;
            min-width: 50px;
            font-weight: bold;
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
            font-size: 12px;
            background: #f0f0f0;
        }
        
        .footer {
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 10px;
            text-align: center;
            font-size: 9px;
        }
        
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
            height: 1px;
        }
        
        .address-section {
            margin: 10px 0;
            font-size: 9px;
            border: 1px dotted #ccc;
            padding: 5px;
        }
        
        .status-info {
            background: #f9f9f9;
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #ddd;
        }
        
        /* Print buttons for screen view */
        .print-controls {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Print Controls (visible only on screen) -->
    <div class="print-controls no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
        <button class="print-btn" onclick="window.close()" style="background: #6c757d;">✕ Close</button>
    </div>
    
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            @php
                // Get company data - prioritize from companies table
                $companyName = $companySettings['name'] ?? 'YOUR STORE';
                $companyAddress = $companySettings['address'] ?? '';
                $companyPhone = $companySettings['phone'] ?? '';
                $companyEmail = $companySettings['email'] ?? '';
                $companyGst = $companySettings['gst_number'] ?? '';
                $companyLogo = $companySettings['logo'] ?? '';
                
                // Handle logo path
                $logoPath = null;
                if (!empty($companyLogo)) {
                    $possiblePaths = [
                        public_path('storage/' . $companyLogo),
                        storage_path('app/public/' . $companyLogo),
                        public_path($companyLogo)
                    ];
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $logoPath = asset('storage/' . $companyLogo);
                            break;
                        }
                    }
                }
            @endphp
            
            {{-- Company Logo --}}
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $companyName }}" class="company-logo">
            @endif
            
            {{-- Company Name --}}
            <div class="company-name">{{ strtoupper($companyName) }}</div>
            
            {{-- Company Address --}}
            @if(!empty($companyAddress))
                <div class="company-details">{{ $companyAddress }}</div>
            @endif
            
            {{-- Company Phone --}}
            @if(!empty($companyPhone))
                <div class="company-details">Ph: {{ $companyPhone }}</div>
            @endif
            
            {{-- Company Email --}}
            @if(!empty($companyEmail))
                <div class="company-details">{{ $companyEmail }}</div>
            @endif
            
            {{-- GST Number --}}
            @if(!empty($companyGst))
                <div class="company-details">GST: {{ $companyGst }}</div>
            @endif
        </div>
        
        <!-- Receipt Title -->
        <div class="text-center">
            <div class="bold" style="font-size: 13px;">RECEIPT</div>
            <div class="dashed-line"></div>
        </div>
        
        <!-- Order Information -->
        <div class="order-info">
            <div><strong>Receipt #:</strong> {{ $order->order_number }}</div>
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
        <div class="items-section">
            @foreach($order->items as $item)
                <div class="item-row">
                    <div class="item-details">
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->offer_name)
                            <div style="font-size: 8px; color: #666; font-weight: bold;">🏷️ {{ $item->offer_name }}</div>
                        @endif
                        <div class="item-meta">
                            {{ $item->quantity }} x 
                            @if($item->mrp_price > 0 && $item->mrp_price > $item->price)
                                <span style="text-decoration: line-through; color: #888;">₹{{ number_format($item->mrp_price, 2) }}</span>
                                <strong>₹{{ number_format($item->price, 2) }}</strong>
                            @else
                                ₹{{ number_format($item->price, 2) }}
                            @endif
                            @if($item->tax_percentage > 0)
                                (Tax: {{ $item->tax_percentage }}%)
                            @endif
                        </div>
                        @if($item->savings > 0)
                            <div style="font-size: 8px; color: #27ae60; font-weight: bold;">Saved: ₹{{ number_format($item->savings, 2) }}</div>
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
            @php
                $totalMrp = $order->items->sum('mrp_total');
                $totalSavings = $order->items->sum('savings');
            @endphp
            
            @if($totalSavings > 0)
                <div class="total-row">
                    <span>Total MRP:</span>
                    <span>₹{{ number_format($totalMrp, 2) }}</span>
                </div>
                <div class="total-row" style="color: #27ae60; font-weight: bold;">
                    <span>You Saved:</span>
                    <span>-₹{{ number_format($totalSavings, 2) }}</span>
                </div>
            @endif
            
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
        </div>
        
        <!-- Payment & Status Info -->
        <div class="status-info">
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
            
            <div class="total-row">
                <span>Order Status:</span>
                <span>{{ strtoupper($order->status) }}</span>
            </div>
        </div>
        
        <!-- Delivery Address -->
        @if($order->delivery_address)
            <div class="dashed-line"></div>
            <div class="address-section">
                <strong>Delivery Address:</strong><br>
                {{ $order->delivery_address }}<br>
                {{ $order->city }}@if($order->state), {{ $order->state }}@endif {{ $order->pincode }}
            </div>
        @endif
        
        <!-- Order Notes -->
        @if($order->notes)
            <div class="dashed-line"></div>
            <div style="font-size: 10px;">
                <strong>Order Notes:</strong><br>
                {{ $order->notes }}
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="dashed-line"></div>
            <div class="bold">Thank you for your order!</div>
            <div>Visit us again soon</div>
            @if(!empty($companyPhone))
                <br><div>For queries: {{ $companyPhone }}</div>
            @endif
            @if(!empty($companyEmail))
                <div>Email: {{ $companyEmail }}</div>
            @endif
            <br>
            <div style="font-size: 8px;">
                Printed on {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    <script>
        // Auto-print when page loads (only if opened in new window)
        window.addEventListener('load', function() {
            // Check if this is a popup/new window
            if (window.opener || window.history.length === 1) {
                // Small delay to ensure page is fully rendered
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });
        
        // Handle after print events
        window.addEventListener('afterprint', function() {
            // Close window after printing if it's a popup
            if (window.opener) {
                setTimeout(function() {
                    window.close();
                }, 1000);
            }
        });
        
        // Handle print cancellation
        window.addEventListener('beforeprint', function() {
            console.log('Print dialog opened');
        });
    </script>
</body>
</html>