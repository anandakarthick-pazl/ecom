<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2d5016;
        }
        .company-logo {
            max-height: 100px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2d5016;
            margin: 0;
        }
        .invoice-title {
            font-size: 24px;
            color: #666;
            margin: 10px 0 0 0;
        }
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-info > div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details h3,
        .customer-details h3 {
            margin: 0 0 10px 0;
            color: #2d5016;
            font-size: 16px;
        }
        .invoice-details p,
        .customer-details p {
            margin: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #2d5016;
            color: white;
            font-weight: bold;
        }
        .items-table .qty,
        .items-table .price,
        .items-table .total {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
            border-top: 2px solid #2d5016;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        @if($globalCompany->company_logo ?? false)
            <img src="{{ public_path('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
        @else
            <div style="font-size: 48px; margin-bottom: 10px;">🌿</div>
        @endif
        <h1 class="company-name">{{ $globalCompany->company_name ?? 'Herbal Bliss' }}</h1>
        {{-- <p style="margin: 5px 0; color: #666;">Natural & Organic Products</p> --}}
        @if($globalCompany->company_address ?? false)
            <p style="margin: 5px 0; color: #666;">{{ $globalCompany->company_address }}</p>
        @endif
        <p style="margin: 5px 0; color: #666;">Email: {{ $globalCompany->company_email ?? 'info@herbalbliss.com' }} | Phone: {{ $globalCompany->company_phone ?? '+91 9876543210' }}</p>
        @if($globalCompany->gst_number ?? false)
            <p style="margin: 5px 0; color: #666;"><strong>GST No:</strong> {{ $globalCompany->gst_number }}</p>
        @endif
        <h2 class="invoice-title">INVOICE</h2>
    </div>

    <div class="invoice-info">
        <div class="invoice-details">
            <h3>Invoice Details</h3>
            <p><strong>Invoice Number:</strong> INV-{{ $order->order_number }}</p>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Invoice Date:</strong> {{ now()->format('d M, Y') }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
        </div>
        
        <div class="customer-details">
            <h3>Customer Details</h3>
            <p><strong>Name:</strong> {{ $order->customer_name }}</p>
            <p><strong>Mobile:</strong> {{ $order->customer_mobile }}</p>
            @if($order->customer && $order->customer->email)
                <p><strong>Email:</strong> {{ $order->customer->email }}</p>
            @endif
            <p><strong>Delivery Address:</strong><br>
                {{ $order->delivery_address }}<br>
                {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
            </p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="qty">Qty</th>
                <th class="price">Unit Price</th>
                <th class="price">Tax %</th>
                <th class="price">Tax Amt</th>
                <th class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name }}</strong>
                        @if($item->product && $item->product->sku)
                            <br><small>SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">₹{{ number_format($item->price, 2) }}</td>
                    <td class="price">{{ $item->tax_percentage }}%</td>
                    <td class="price">₹{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="total">₹{{ number_format($item->quantity * $item->price + $item->tax_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td style="text-align: right;">-₹{{ number_format($order->discount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>CGST:</td>
                <td style="text-align: right;">₹{{ number_format($order->cgst_amount, 2) }}</td>
            </tr>
            <tr>
                <td>SGST:</td>
                <td style="text-align: right;">₹{{ number_format($order->sgst_amount, 2) }}</td>
            </tr>
            @if($order->delivery_charge > 0)
                <tr>
                    <td>Delivery Charge:</td>
                    <td style="text-align: right;">₹{{ number_format($order->delivery_charge, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td><strong>Total Amount:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($order->notes)
        <div style="margin-top: 30px;">
            <h3 style="color: #2d5016;">Order Notes:</h3>
            <p>{{ $order->notes }}</p>
        </div>
    @endif

    <div class="footer">
        <p><strong>{{ $globalCompany->company_name ?? 'Herbal Bliss' }}</strong></p>
        @if($globalCompany->company_address ?? false)
            <p>{{ $globalCompany->company_address }}</p>
        @endif
        <p>
            @if($globalCompany->company_email ?? false)
                Email: {{ $globalCompany->company_email }}
            @endif
            @if(($globalCompany->company_email ?? false) && ($globalCompany->company_phone ?? false))
                | 
            @endif
            @if($globalCompany->company_phone ?? false)
                Phone: {{ $globalCompany->company_phone }}
            @endif
        </p>
        <p style="margin-top: 20px;">Thank you for your business!</p>
    </div>
</body>
</html>
