<?php
// echo "<pre>";print_r($globalCompany);exit;
$logo = '';

if (!empty($globalCompany->company_logo)) {
    // Build absolute storage path
    $logoPath = storage_path('app/public/' . $globalCompany->company_logo);

    if (file_exists($logoPath)) {
        if (file_exists($logoPath)) {
            $logoUrl = asset('storage/' . $globalCompany->company_logo);
            $logo = '<img src="' . $logoUrl . '" style="height:50px; width:auto;">';
        }
        //$logoSrc = 'data:image/' . $logoType . ';base64,' . $logoData;

        
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 12mm;
            size: A4;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
        }

        /* Header */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #2d5016;
            padding-bottom: 8px;
        }

        .company-section {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .invoice-section {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            max-height: 60px;
            margin-bottom: 6px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 4px;
        }

        .company-address,
        .company-contact {
            font-size: 10px;
            color: #666;
            line-height: 1.3;
        }

        .gst-number {
            font-size: 11px;
            font-weight: bold;
            color: #2d5016;
            margin-top: 3px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 6px;
        }

        .invoice-details {
            background: #f8f9fa;
            padding: 8px;
            border-left: 3px solid #2d5016;
            border-radius: 4px;
            font-size: 10px;
        }

        .invoice-details td {
            padding: 2px 0;
        }

        /* Customer + Payment */
        .customer-section {
            margin: 12px 0;
            display: table;
            width: 100%;
        }

        .bill-to,
        .payment-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .payment-info {
            padding-left: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        .customer-info,
        .payment-details {
            padding: 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .customer-info {
            background: #f8f9fa;
            border-left: 3px solid #28a745;
        }

        .payment-details {
            background: #fff3cd;
            border-left: 3px solid #ffc107;
        }

        /* Items */
        .items-section {
            margin: 15px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .items-table th {
            padding: 6px;
            background: #2d5016;
            color: #fff;
            text-transform: uppercase;
            font-size: 10px;
        }

        .items-table td {
            padding: 6px;
            border-bottom: 1px solid #e0e0e0;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .product-name {
            font-weight: 600;
            color: #2d5016;
        }

        /* Totals */
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 15px;
        }

        .totals-left {
            display: table-cell;
            width: 60%;
        }

        .totals-right {
            display: table-cell;
            width: 40%;
        }

        .totals-table {
            width: 100%;
            font-size: 11px;
        }

        .totals-table td {
            padding: 6px;
        }

        .total-row {
            background: #2d5016;
            color: #fff;
            font-weight: bold;
        }

        .total-row td {
            padding: 8px;
        }

        /* Notes */
        .notes-section {
            margin-top: 8px;
            background: #f8f9fa;
            padding: 8px;
            border-left: 3px solid #6c757d;
            font-size: 10px;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #2d5016;
            text-align: center;
            font-size: 10px;
        }

        .thank-you {
            font-size: 14px;
            font-weight: bold;
            color: #2d5016;
            margin-bottom: 5px;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-section">
                @if ($globalCompany->company_logo)
                  <?php echo $logo;?>
                @endif
                <div class="company-name">{{ $globalCompany->company_name ?? 'Your Store' }}</div>
                <div class="company-address">{{ $globalCompany->company_address ?? '' }}</div>
                <div class="company-contact">{{ $globalCompany->mobile_number ?? '' }}</div>
                <div class="company-contact">What's App : {{ $globalCompany->whatsapp_number ?? '' }}</div>
                <div class="company-contact">G Pay : {{ $globalCompany->gpay_number ?? '' }}</div>
                @if ($globalCompany->gst_number)
                    <div class="gst-number">GST: {{ $globalCompany->gst_number }}</div>
                @endif
            </div>
            <div class="invoice-section">
                <div class="invoice-title">Invoice</div>
                <div class="invoice-details">
                    <table>
                        <tr>
                            <td>Invoice #:</td>
                            <td><b>{{ $sale->invoice_number }}</b></td>
                        </tr>
                        <tr>
                            <td>Date:</td>
                            <td>{{ $sale->created_at->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td>Time:</td>
                            <td>{{ $sale->created_at->format('h:i A') }}</td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td><b
                                    style="color:{{ $sale->status === 'completed' ? '#28a745' : '#dc3545' }}">{{ ucfirst($sale->status) }}</b>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer + Payment -->
        <div class="customer-section">
            <div class="bill-to">
                <div class="section-title">Bill To</div>
                <div class="customer-info">
                    <b>{{ $sale->customer_name ?? 'Walk-in Customer' }}</b>
                    @if ($sale->customer_phone)
                        <div>📞 {{ $sale->customer_phone }}</div>
                    @endif
                </div>
            </div>
            <div class="payment-info">
                <div class="section-title">Payment</div>
                <div class="payment-details">
                    <div><b>Method:</b> {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</div>
                    <div><b>Paid:</b> ₹{{ number_format($sale->paid_amount, 2) }}</div>
                    @if ($sale->change_amount > 0)
                        <div><b>Change:</b> ₹{{ number_format($sale->change_amount, 2) }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="items-section">
            <div class="section-title">Items Purchased</div>
            @foreach ($sale->items->chunk(20) as $chunkIndex => $chunk)
                @if ($chunkIndex > 0)
                    <div class="page-break"></div>
                @endif
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Tax %</th>
                            <th>Tax Amt</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($chunk as $index => $item)
                            @php
                                $num = $chunkIndex * 20 + $index + 1;
                                $gross = $item->quantity * $item->unit_price;
                                $disc = $item->discount_amount ?? 0;
                                $tax = $item->tax_amount ?? 0;
                                $total = $gross - $disc + $tax;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $num }}</td>
                                <td>
                                    <div class="product-name">{{ $item->product->name ?? $item->product_name }}</div>
                                    @if ($item->product && $item->product->sku)
                                        <small>SKU: {{ $item->product->sku }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">{{ $disc > 0 ? '-₹' . number_format($disc, 2) : '-' }}</td>
                                <td class="text-center">{{ $item->tax_percentage ?? 0 }}%</td>
                                <td class="text-right">{{ $tax > 0 ? '₹' . number_format($tax, 2) : '-' }}</td>
                                <td class="text-right"><b>₹{{ number_format($total, 2) }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-left">
                @if ($sale->notes)
                    <div class="notes-section"><b>Notes:</b> {{ $sale->notes }}</div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if ($sale->discount_amount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if ($sale->tax_amount > 0)
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">₹{{ number_format($sale->tax_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total:</td>
                        <td class="text-right">₹{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <div class="thank-you">Thank you for shopping with {{ $globalCompany->company_name ?? 'us' }}!</div>
            <div>This invoice was generated on {{ now()->format('d/m/Y h:i A') }}</div>
        </div>
    </div>
</body>

</html>
