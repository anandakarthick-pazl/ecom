<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 20px;
            margin-top: 40px;
        }
        body {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid {{ $company['primary_color'] ?? '#2d5016' }};
        }
        .company-logo {
            max-height: 80px;
            max-width: 200px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin: 5px 0;
        }
        .company-details {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        .invoice-title {
            font-size: 20px;
            color: #666;
            margin: 15px 0 0 0;
            font-weight: bold;
        }
        .invoice-info {
            width: 100%;
            margin-bottom: 25px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin: 0 0 8px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }
        .detail-line {
            margin: 3px 0;
            font-size: 11px;
        }
        .detail-line strong {
            display: inline-block;
            width: 80px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .items-table th {
            background-color: {{ $company['primary_color'] ?? '#2d5016' }};
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .product-name {
            font-weight: bold;
            font-size: 10px;
        }
        .product-sku {
            font-size: 8px;
            color: #666;
            font-style: italic;
        }
        .totals-section {
            float: right;
            width: 350px;
            margin-top: 15px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .label-col {
            text-align: left;
            font-weight: bold;
        }
        .totals-table .amount-col {
            text-align: right;
            min-width: 80px;
        }
        .totals-table .subtotal-row td {
            border-top: 1px solid #ccc;
            font-weight: bold;
        }
        .totals-table .discount-row td {
            color: #d63384;
        }
        .totals-table .tax-row td {
            color: #0d6efd;
        }
        .totals-table .total-row td {
            background-color: {{ $company['primary_color'] ?? '#2d5016' }};
            color: white;
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid {{ $company['primary_color'] ?? '#2d5016' }};
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
            clear: both;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .currency {
            font-family: 'Arial', sans-serif;
        }
        .notes-section {
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid {{ $company['primary_color'] ?? '#2d5016' }};
        }
        .notes-title {
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin-bottom: 8px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if($order->status === 'cancelled')
        <div class="watermark">CANCELLED</div>
    @elseif($order->payment_status === 'paid')
        <div class="watermark">PAID</div>
    @endif

    <!-- Header Section -->
    <div class="header">
        @if(!empty($company['logo']))
            @php
                $logoPath = null;
                $possiblePaths = [
                    public_path('storage/' . $company['logo']),
                    storage_path('app/public/' . $company['logo']),
                    public_path($company['logo'])
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $logoPath = $path;
                        break;
                    }
                }
            @endphp
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $company['name'] }}" class="company-logo">
            @endif
        @endif
        
        <h1 class="company-name">{{ $company['name'] ?? 'Your Company' }}</h1>
        
        @if(!empty($company['address']))
            <div class="company-details">{{ $company['address'] }}</div>
        @endif
        
        <div class="company-details">
            @if(!empty($company['email']))
                Email: {{ $company['email'] }}
            @endif
            @if(!empty($company['email']) && !empty($company['phone']))
                &nbsp;|&nbsp;
            @endif
            @if(!empty($company['phone']))
                Phone: {{ $company['phone'] }}
            @endif
        </div>
        
        @if(!empty($company['gst_number']))
            <div class="company-details"><strong>GST No:</strong> {{ $company['gst_number'] }}</div>
        @endif
        
        @if(!empty($company['website']))
            <div class="company-details">{{ $company['website'] }}</div>
        @endif
        
        <h2 class="invoice-title">TAX INVOICE</h2>
    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <table>
            <tr>
                <td>
                    <div class="section-title">Invoice Details</div>
                    <div class="detail-line">
                        <strong>Invoice No:</strong> INV-{{ $order->order_number }}
                    </div>
                    <div class="detail-line">
                        <strong>Invoice Date:</strong> {{ now()->format('d M, Y') }}
                    </div>
                    <div class="detail-line">
                        <strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}
                    </div>
                    <div class="detail-line">
                        <strong>Status:</strong> 
                        <span class="status-badge status-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    @if(!empty($order->payment_method))
                        <div class="detail-line">
                            <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        </div>
                    @endif
                </td>
                <td>
                    <div class="section-title">Bill To</div>
                    <div class="detail-line">
                        <strong>Name:</strong> {{ $order->customer_name ?? 'N/A' }}
                    </div>
                    @if(!empty($order->customer_mobile))
                        <div class="detail-line">
                            <strong>Mobile:</strong> {{ $order->customer_mobile }}
                        </div>
                    @endif
                    @if(!empty($order->customer_email))
                        <div class="detail-line">
                            <strong>Email:</strong> {{ $order->customer_email }}
                        </div>
                    @endif
                    @if(!empty($order->delivery_address))
                        <div class="detail-line">
                            <strong>Address:</strong><br>
                            {{ $order->delivery_address }}
                            @if(!empty($order->city) || !empty($order->state) || !empty($order->pincode))
                                <br>
                                {{ trim(($order->city ?? '') . (!empty($order->city) && (!empty($order->state) || !empty($order->pincode)) ? ', ' : '') . ($order->state ?? '') . (!empty($order->state) && !empty($order->pincode) ? ' - ' : '') . ($order->pincode ?? '')) }}
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">Product Details</th>
                <th style="width: 8%;" class="text-center">Qty</th>
                <th style="width: 12%;" class="text-right">Unit Price</th>
                <th style="width: 8%;" class="text-center">Tax %</th>
                <th style="width: 12%;" class="text-right">Tax Amount</th>
                <th style="width: 10%;" class="text-right">Discount</th>
                <th style="width: 15%;" class="text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                @php
                    $unitPrice = $item->price ?? 0;
                    $quantity = $item->quantity ?? 0;
                    $taxPercentage = $item->tax_percentage ?? 0;
                    $taxAmount = $item->tax_amount ?? 0;
                    
                    // Calculate item discount (proportional from order discount)
                    $itemSubtotal = $unitPrice * $quantity;
                    $orderSubtotal = $order->subtotal ?? 0;
                    $orderDiscount = $order->discount ?? 0;
                    $itemDiscount = $orderSubtotal > 0 ? ($itemSubtotal / $orderSubtotal) * $orderDiscount : 0;
                    
                    // Calculate line total
                    $lineTotal = $itemSubtotal + $taxAmount - $itemDiscount;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name ?? 'Product' }}</div>
                        @if(!empty($item->product->sku))
                            <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                        @endif
                        @if(!empty($item->product_description))
                            <div class="product-sku">{{ $item->product_description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($quantity) }}</td>
                    <td class="text-right">RS {{ number_format($unitPrice, 2) }}</td>
                    <td class="text-center">{{ number_format($taxPercentage, 1) }}%</td>
                    <td class="text-right">RS {{ number_format($taxAmount, 2) }}</td>
                    <td class="text-right">
                        @if($itemDiscount > 0)
                            RS {{ number_format($itemDiscount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">RS {{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Section -->
    <div class="totals-section">
        <table class="totals-table">
            @php
                $subtotal = $order->subtotal ?? 0;
                $totalDiscount = $order->discount ?? 0;
                $cgstAmount = $order->cgst_amount ?? 0;
                $sgstAmount = $order->sgst_amount ?? 0;
                $igstAmount = $order->igst_amount ?? 0;
                $deliveryCharge = $order->delivery_charge ?? 0;
                $totalAmount = $order->total ?? 0;
            @endphp
            
            <tr class="subtotal-row">
                <td class="label-col">Subtotal:</td>
                <td class="amount-col">RS {{ number_format($subtotal, 2) }}</td>
            </tr>
            
            @if($totalDiscount > 0)
                <tr class="discount-row">
                    <td class="label-col">Total Discount:</td>
                    <td class="amount-col">-RS {{ number_format($totalDiscount, 2) }}</td>
                </tr>
            @endif
            
            @if($cgstAmount > 0)
                <tr class="tax-row">
                    <td class="label-col">CGST:</td>
                    <td class="amount-col">RS {{ number_format($cgstAmount, 2) }}</td>
                </tr>
            @endif
            
            @if($sgstAmount > 0)
                <tr class="tax-row">
                    <td class="label-col">SGST:</td>
                    <td class="amount-col">RS {{ number_format($sgstAmount, 2) }}</td>
                </tr>
            @endif
            
            @if($igstAmount > 0)
                <tr class="tax-row">
                    <td class="label-col">IGST:</td>
                    <td class="amount-col">RS {{ number_format($igstAmount, 2) }}</td>
                </tr>
            @endif
            
            @if($deliveryCharge > 0)
                <tr>
                    <td class="label-col">Delivery Charge:</td>
                    <td class="amount-col">RS {{ number_format($deliveryCharge, 2) }}</td>
                </tr>
            @endif
            
            <tr class="total-row">
                <td class="label-col">Total Amount:</td>
                <td class="amount-col">RS {{ number_format($totalAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <!-- Order Notes -->
    @if(!empty($order->notes) || !empty($order->admin_notes))
        <div class="notes-section">
            @if(!empty($order->notes))
                <div class="notes-title">Invoice Notes:</div>
                <div>{{ $order->notes }}</div>
            @endif
            @if(!empty($order->admin_notes))
                <div class="notes-title" style="margin-top: 10px;">Admin Notes:</div>
                <div>{{ $order->admin_notes }}</div>
            @endif
        </div>
    @endif

    <!-- Terms & Conditions -->
    <div class="notes-section" style="margin-top: 20px;">
        <div class="notes-title">Terms & Conditions:</div>
        <div style="font-size: 9px; line-height: 1.3;">
            1. Payment is due within 30 days of invoice date.<br>
            2. Goods once sold cannot be returned without prior approval.<br>
            3. All disputes are subject to local jurisdiction.<br>
            4. This is a computer-generated invoice and does not require a physical signature.
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="margin-bottom: 10px;">
            <strong>{{ $company['name'] ?? 'Your Company' }}</strong>
        </div>
        @if(!empty($company['address']))
            <div>{{ $company['address'] }}</div>
        @endif
        <div>
            @if(!empty($company['email']))
                Email: {{ $company['email'] }}
            @endif
            @if(!empty($company['email']) && !empty($company['phone']))
                &nbsp;|&nbsp;
            @endif
            @if(!empty($company['phone']))
                Phone: {{ $company['phone'] }}
            @endif
        </div>
        @if(!empty($company['gst_number']))
            <div>GST No: {{ $company['gst_number'] }}</div>
        @endif
        <div style="margin-top: 15px; font-size: 9px;">
            Thank you for your business! | Generated on {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>
</body>
</html>
