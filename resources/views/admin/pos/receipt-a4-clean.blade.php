<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #2d5016;
            margin-bottom: 20px;
        }

        .company-info {
            width: 50%;
        }

        .company-logo {
            max-height: 80px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d5016;
            margin: 10px 0;
        }

        .receipt-details {
            width: 45%;
            text-align: right;
        }

        .receipt-details h3 {
            margin-bottom: 10px;
            color: #2d5016;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background: #2d5016;
            color: #fff;
        }

        .qty,
        .price,
        .total {
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }

        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .totals td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .totals .total-row {
            background: #f8f9fa;
            font-weight: bold;
        }

        .footer {
            clear: both;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-info">
            @if ($globalCompany->company_logo)
                @php
                    $logoPath = public_path('storage/' . $globalCompany->company_logo);
                    $logoData = base64_encode(file_get_contents($logoPath));
                @endphp
                <img src="data:image/png;base64,{{ $logoData }}" class="company-logo" alt="Logo">
            @endif

            <h1 class="company-name">{{ $globalCompany->company_name ?? 'Herbal Store' }}</h1>
            <p>{{ $globalCompany->company_address }}</p>
            <p>
                @if (!empty($globalCompany->city))
                    {{ $globalCompany->city }}
                @endif
                @if (!empty($globalCompany->state))
                    , {{ $globalCompany->state }}
                @endif
                @if (!empty($globalCompany->postal_code))
                    - {{ $globalCompany->postal_code }}
                @endif
            </p>
            <p>Email: {{ $globalCompany->company_email }} | Phone: {{ $globalCompany->company_phone }}</p>
            @if ($globalCompany->gst_number)
                <p>GST: {{ $globalCompany->gst_number }}</p>
            @endif
        </div>

        <div class="receipt-details">
            <h3>Receipt</h3>
            <p><strong>Number:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $sale->created_at->format('d M, Y') }}</p>
            <p><strong>Time:</strong> {{ $sale->created_at->format('h:i A') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($sale->status) }}</p>
            @if ($sale->customer_name)
                <p><strong>Customer:</strong> {{ $sale->customer_name }}</p>
            @endif
            @if ($sale->customer_phone)
                <p><strong>Phone:</strong> {{ $sale->customer_phone }}</p>
            @endif
        </div>
    </div>

    @foreach ($sale->items->chunk(10) as $chunk)
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="qty">Qty</th>
                    <th class="price">Unit Price</th>
                    <th class="price">Discount</th>
                    <th class="price">Tax %</th>
                    <th class="price">Tax Amt</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $item)
                    @php
                        $gross = $item->quantity * $item->unit_price;
                        $discount = $item->discount_amount ?? 0;
                        $tax = $item->tax_amount ?? 0;
                        $total = $gross - $discount + $tax;
                    @endphp
                    <tr>
                        <td>{{ $item->product->name ?? $item->product_name }}</td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="price">Rs.{{ number_format($item->unit_price, 2) }}</td>
                        <td class="price">
                            @if ($discount > 0)
                                -Rs.{{ number_format($discount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="price">{{ $item->tax_percentage ?? 0 }}%</td>
                        <td class="price">Rs.{{ number_format($tax, 2) }}</td>
                        <td class="total">Rs.{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">Rs.{{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            @if ($sale->cgst_amount > 0)
                <tr>
                    <td>CGST:</td>
                    <td style="text-align: right;">Rs.{{ number_format($sale->cgst_amount, 2) }}</td>
                </tr>
            @endif
            @if ($sale->sgst_amount > 0)
                <tr>
                    <td>SGST:</td>
                    <td style="text-align: right;">Rs.{{ number_format($sale->sgst_amount, 2) }}</td>
                </tr>
            @endif
            @if ($sale->discount_amount > 0)
                <tr>
                    <td>Discount:</td>
                    <td style="text-align: right;">-Rs.{{ number_format($sale->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td><strong>Total:</strong></td>
                <td style="text-align: right;"><strong>Rs.{{ number_format($sale->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</p>
        <p>Generated on {{ now()->format('d/m/Y h:i A') }}</p>
    </div>

</body>

</html>