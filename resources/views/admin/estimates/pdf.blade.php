<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate #{{ $estimate->estimate_number }}</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Base Styles - Optimized for PDF */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container for A4 PDF */
        .container {
            width: 100%;
            max-width: 100%;
            padding: 20px 25px;
            margin: 0;
        }

        /* Header Section */
        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
        }

        .header-content {
            width: 100%;
        }

        .header-top {
            width: 100%;
            margin-bottom: 10px;
        }

        .header-bottom {
            width: 100%;
            display: table;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        /* Logo and Company Info */
        .company-logo {
            max-height: 60px;
            max-width: 150px;
            margin-bottom: 8px;
            display: block;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 9pt;
            color: #555;
            line-height: 1.4;
        }

        .company-details p {
            margin: 2px 0;
        }

        /* Estimate Details */
        .estimate-title {
            font-size: 18pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .estimate-number {
            font-size: 14pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .estimate-info {
            font-size: 10pt;
            line-height: 1.5;
        }

        .estimate-info p {
            margin: 3px 0;
        }

        .estimate-info strong {
            font-weight: 600;
            color: #333;
        }

        /* Billing Section */
        .billing-section {
            width: 100%;
            margin: 20px 0;
            display: table;
        }

        .billing-from,
        .billing-to {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }

        .billing-from {
            padding-right: 15px;
        }

        .billing-to {
            padding-left: 15px;
        }

        .billing-title {

            border-bottom: 1px solid #ddd;
            /* text-align: right; */
            /* width: 100%; */
        }

        .billing-details {
            font-size: 10pt;
            line-height: 1.5;
        }

        .billing-details p {
            margin: 3px 0;
        }

        .billing-details strong {
            font-weight: bold;
            color: #111;
            display: block;
            margin-bottom: 3px;
        }

        /* Items Table */
        .items-table {
            width: 97%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }

        .items-table thead {
            background-color: #2563eb;
        }

        .items-table th {
            color: white;
            font-weight: bold;
            padding: 8px 5px;
            text-align: center;
            border: 1px solid #2563eb;
            font-size: 9pt;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .items-table td {
            padding: 6px 5px;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
            font-size: 9pt;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Column Widths */
        .col-sno {
            width: 5%;
        }

        .col-item {
            width: 35%;
        }

        .col-qty {
            width: 8%;
        }

        .col-mrp {
            width: 12%;
        }

        .col-offer {
            width: 12%;
        }

        .col-tax {
            width: 10%;
        }

        .col-line {
            width: 9%;
        }

        .col-total {
            width: 9%;
        }

        /* Totals Section */
        .totals-section {
            width: 98%;
            margin-top: 20px;
            display: table;
        }

        .notes-area {
            display: table-cell;
            width: 55%;
            padding-right: 20px;
            vertical-align: top;
        }

        .totals-area {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            font-size: 10pt;
        }

        .totals-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .totals-table td {
            padding: 6px 8px;
        }

        .totals-table td:first-child {
            text-align: left;
            font-weight: 500;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .totals-table .subtotal-row {
            background-color: #f8f9fa;
        }

        .totals-table .discount-row td:last-child {
            color: #28a745;
        }

        .totals-table .total-row {
            background-color: #2563eb;
            color: white;
            font-size: 12pt;
            font-weight: bold;
        }

        .totals-table .total-row td {
            padding: 10px 8px;
        }

        /* Notes and Terms */
        .notes-box,
        .terms-box {
            margin-bottom: 15px;
        }

        .notes-title,
        .terms-title {
            font-size: 11pt;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .notes-content,
        .terms-content {
            font-size: 9pt;
            line-height: 1.5;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 3px solid #2563eb;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        /* Print and PDF Optimization */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                padding: 15px 20px;
            }

            .items-table {
                page-break-inside: avoid;
            }

            .totals-section {
                page-break-inside: avoid;
            }

            .notes-box,
            .terms-box {
                page-break-inside: avoid;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>

<body>
    <div class="container">


        <!-- BILLING INFORMATION -->
        <div class="billing-section">
            <div class="billing-from">
                {{-- <div class="billing-title">From</div> --}}
                <div class="billing-details">
                    @if ($globalCompany->company_logo)
                        @php
                            $logoPath = public_path('storage/' . $globalCompany->company_logo);
                            if (file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            }
                        @endphp
                        @if (isset($logoData))
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" class="company-logo"
                                alt="Logo">
                        @endif
                    @endif
                    <strong>{{ $globalCompany->company_name ?? 'Your Company' }}</strong>
                    @if ($globalCompany->company_address)
                        <p>{{ $globalCompany->company_address }}</p>
                    @endif
                    @if ($globalCompany->company_phone)
                        <p>Phone: {{ $globalCompany->company_phone }}</p>
                    @endif
                    @if ($globalCompany->company_email)
                        <p>Email: {{ $globalCompany->company_email }}</p>
                    @endif
                    {{-- <div class="estimate-number">#{{ $estimate->estimate_number }}</div>
                    <div class="estimate-info">
                        <p><strong>Date:</strong> {{ $estimate->estimate_date->format('d M, Y') }}</p>
                        <p><strong>Valid Until:</strong> {{ $estimate->valid_until->format('d M, Y') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($estimate->status) }}</p>
                    </div> --}}
                </div>
            </div>
            <div class="billing-to">
                <div class="billing-title">#{{ $estimate->estimate_number }}
                    <p><strong>Date:</strong> {{ $estimate->estimate_date->format('d M, Y') }}</p>
                </div>
                <div class="billing-details">
                    <strong>{{ $estimate->customer_name }}</strong>
                    @if ($estimate->customer_address)
                        <p>{{ $estimate->customer_address }}</p>
                    @endif
                    @if ($estimate->customer_phone)
                        <p>Phone: {{ $estimate->customer_phone }}</p>
                    @endif
                    @if ($estimate->customer_email)
                        <p>Email: {{ $estimate->customer_email }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-sno">#</th>
                    <th class="col-item text-left">Item</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-mrp">MRP</th>
                    <th class="col-offer">Offer</th>
                    <th class="col-tax">Tax</th>
                    <th class="col-line">Line Total</th>
                    <th class="col-total">Total+Tax</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estimate->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">{{ $item->product->name ?? 'Product' }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">₹{{ number_format($item->mrp_price, 2) }}</td>
                        <td class="text-right" @if ($item->mrp_price && $item->mrp_price > $item->offer_price) style="color: #28a745;" @endif>
                            @if ($item->mrp_price && $item->mrp_price > $item->offer_price)
                                ₹{{ number_format($item->mrp_price - $item->offer_price, 2) }}
                            @else
                                -
                            @endif
                        </td>


                        <td class="text-center">
                            @if ($item->product && $item->product->item_tax_percentage > 0)
                                {{ number_format($item->product->item_tax_percentage, 0) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">₹{{ number_format($item->total_price, 2) }}</td>
                        <td class="text-right">
                            <strong>₹{{ number_format($item->line_total_with_tax ?? $item->total_price, 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALS AND NOTES -->
        <div class="totals-section">
            <div class="notes-area">
                @if ($estimate->notes)
                    <div class="notes-box">
                        <div class="notes-title">Notes</div>
                        <div class="notes-content">{{ $estimate->notes }}</div>
                    </div>
                @endif
            </div>
            <div class="totals-area">
                @php
                    $totalMrpValue = $estimate->items->sum(fn($i) => ($i->mrp_price ?? $i->unit_price) * $i->quantity);
                    $totalDiscountAmount = $estimate->items->sum(fn($i) => ($i->discount_amount ?? 0) * $i->quantity);
                    $totalTaxAmount = $estimate->items->sum('tax_amount') ?? 0;
                @endphp
                <table class="totals-table">
                    <tr class="subtotal-row">
                        <td>Subtotal (Before Discounts)</td>
                        <td>₹{{ number_format($totalMrpValue, 2) }}</td>
                    </tr>
                    @if ($totalDiscountAmount > 0)
                        <tr class="discount-row">
                            <td>Product Discounts</td>
                            <td>-₹{{ number_format($totalDiscountAmount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="subtotal-row">
                        <td>Subtotal (After Discounts)</td>
                        <td>₹{{ number_format($estimate->subtotal, 2) }}</td>
                    </tr>
                    @if ($totalTaxAmount > 0)
                        <tr>
                            <td>Total Tax (GST)</td>
                            <td>₹{{ number_format($totalTaxAmount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Grand Total</td>
                        <td>₹{{ number_format($estimate->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TERMS & CONDITIONS -->
        @if ($estimate->terms_conditions)
            <div class="terms-box">
                <div class="terms-title">Terms & Conditions</div>
                <div class="terms-content">{{ $estimate->terms_conditions }}</div>
            </div>
        @endif

        <!-- FOOTER -->

    </div>
</body>

</html>
