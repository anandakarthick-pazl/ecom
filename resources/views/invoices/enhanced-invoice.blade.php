<!DOCTYPE html>

<?php #333
//echo"<pre>";print_R($company);exit;
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }

        /* Header Section */
        .invoice-header {
            border-bottom: 3px solid {{ $company['primary_color'] ?? '#2d5016' }};
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .company-header {
            display: table;
            width: 100%;
        }

        .company-logo {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }

        .company-logo img {
            max-width: 150px;
            max-height: 80px;
        }

        .company-details {
            display: table-cell;
            width: 70%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        /* Invoice Title */
        .invoice-title {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: {{ $company['primary_color'] ?? '#2d5016' }};
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Invoice Info Section */
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-details,
        .customer-details {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
        }

        .invoice-details {
            margin-right: 2%;
        }

        .info-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin-bottom: 10px;
            border-bottom: 2px solid {{ $company['primary_color'] ?? '#2d5016' }};
            padding-bottom: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #555;
            font-size: 11px;
        }

        .info-value {
            display: table-cell;
            width: 60%;
            color: #333;
            font-size: 11px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background: {{ $company['primary_color'] ?? '#2d5016' }};
            color: white;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid {{ $company['primary_color'] ?? '#2d5016' }};
        }

        .items-table td {
            /* padding: 8px 10px; */
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .items-table tbody tr:hover {
            background: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .item-name {
            font-weight: bold;
            color: #333;
        }

        .item-code {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 20px;
            border-top: 2px solid {{ $company['primary_color'] ?? '#2d5016' }};
            padding-top: 15px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 20px;
            font-size: 12px;
            color: #555;
        }

        .summary-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 12px;
            color: #333;
            font-weight: bold;
        }

        .total-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid {{ $company['primary_color'] ?? '#2d5016' }};
        }

        .total-row .summary-label {
            font-size: 16px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
        }

        .total-row .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
        }

        /* Footer Section */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }

        .footer-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .bank-details,
        .terms-conditions {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }

        .bank-details {
            padding-right: 20px;
        }

        .footer-title {
            font-size: 13px;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .footer-content {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .customer-signature,
        .authorized-signature {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
            color: #555;
        }

        /* Thank You Message */
        .thank-you {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background: #f0f8ff;
            border: 1px solid #b0d4ff;
            border-radius: 5px;
        }

        .thank-you h3 {
            color: {{ $company['primary_color'] ?? '#2d5016' }};
            margin-bottom: 5px;
            font-size: 16px;
        }

        .thank-you p {
            color: #666;
            font-size: 11px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0, 0, 0, 0.05);
            font-weight: bold;
            z-index: -1;
        }

        @media print {
            .invoice-container {
                padding: 0;
            }

            .items-table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Watermark -->
        @if ($order->payment_status !== 'paid')
            <div class="watermark">UNPAID</div>
        @endif

        <!-- Header Section -->
        <div class="invoice-header">
            <div class="company-header">
                <div class="company-logo">
                    @if(!empty($company['logo']))
    @php
        $logoPath = public_path('storage/logos/' . basename($company['logo']));
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }
    @endphp

    @if($logoBase64)
        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="{{ $company['name'] }}">
    @else
        <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}">
    @endif
@endif


                </div>
                <div class="company-details">
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="company-address">
                        @if (!empty($company['address']))
                            <div>{{ $company['address'] }}</div>
                        @endif
                        @if (!empty($company['phone']))
                            <div>Phone: {{ $company['phone'] }}</div>
                        @endif
                        @if (!empty($company['email']))
                            <div>Email: {{ $company['email'] }}</div>
                        @endif
                        @if (!empty($company['website']))
                            <div>Website: {{ $company['website'] }}</div>
                        @endif
                        @if (!empty($company['gst_number']))
                            <div><strong>GST No: {{ $company['gst_number'] }}</strong></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            Estimate INVOICE
        </div>

        <!-- Invoice Info Section -->
        <div class="invoice-info">
            <div class="invoice-details">
                <div class="info-title">Estimate Details</div>
                <div class="info-row">
                    <div class="info-label">Estimate No:</div>
                    <div class="info-value">{{ $order->order_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date:</div>
                    <div class="info-value">{{ $order->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Time:</div>
                    <div class="info-value">{{ $order->created_at->format('h:i A') }}</div>
                </div>

            </div>

            <div class="customer-details">
                <div class="info-title">Bill To</div>
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value"><strong>{{ $order->customer_name }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Mobile:</div>
                    <div class="info-value">{{ $order->customer_mobile }}</div>
                </div>
                @if (!empty($order->customer_email))
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $order->customer_email }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">
                        {{ $order->delivery_address }}<br>
                        {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">S.No</th>
                    <th style="width: 30%;">Item Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 20%;" class="text-center">Offer price</th>
                    <th style="width: 20%;" class="text-center">Rate</th>
                    <th style="width: 10%;" class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalQuantity = 0;
                    $totalTaxAmount = 0;
                    $totalAmount = 0;
                    $totalOriginalAmount = 0;
                    $totalSavings = 0;
                    $total = 0;
                @endphp

                @foreach ($order->items as $index => $item)
                    @php
                        // Get product for offer details (SAME LOGIC AS order-success page)
                        $product = $item->product;
                        $offerDetails = null;
                        $hasOffer = false;
                        $effectivePrice = $item->price;
                        $originalPrice = $item->price;
                        $discountPercentage = 0;
                        $offerSource = null;
                        $itemSavings = 0;

                        // If product exists, get offer details
                        if ($product) {
                            $offerDetails = $product->getOfferDetails();
                            $hasOffer = $offerDetails !== null;
                            $originalPrice = $product->price;

                            if ($hasOffer) {
                                $effectivePrice = $offerDetails['discounted_price'];
                                $discountPercentage = $offerDetails['discount_percentage'];
                                $offerSource = $offerDetails['source'];
                                $itemSavings = $offerDetails['savings'];
                            } else {
                                // Check if item price is different from product price (indicating a discount was applied)
                                if ($item->price < $product->price) {
                                    $hasOffer = true;
                                    $effectivePrice = $item->price;
                                    $originalPrice = $product->price;
                                    $itemSavings = $originalPrice - $effectivePrice;
                                    $discountPercentage = ($itemSavings / $originalPrice) * 100;
                                }
                            }
                        } else {
                            // If no product found, check for discount info in item
                            if (isset($item->original_price) && $item->original_price > $item->price) {
                                $hasOffer = true;
                                $originalPrice = $item->original_price;
                                $effectivePrice = $item->price;
                                $itemSavings = $originalPrice - $effectivePrice;
                                $discountPercentage = ($itemSavings / $originalPrice) * 100;
                            }
                        }

                        $totalQuantity += $item->quantity;
                        $itemTax = ($item->tax_amount ?? 0) * $item->quantity;
                        $totalTaxAmount += $itemTax;
                        $totalAmount += $item->total;
                        $totalOriginalAmount += $originalPrice * $item->quantity;
                        $totalSavings += $itemSavings * $item->quantity;

                        if ($hasOffer) {
                            $total += $effectivePrice * $item->quantity;
                        } else {
                            $total += $item->price * $item->quantity;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>


                        </td>

                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            @if ($hasOffer)
                                <div style="color: green; font-weight: bold;">
                                    {{ $company['currency'] ?? '₹' }}{{ number_format($effectivePrice, 2) }}
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="text-center">

                            {{ $company['currency'] ?? '₹' }}{{ number_format($item->originalPrice, 2) }}

                        </td>

                        <td class="text-center">
                            @if ($hasOffer)
                                {{ $company['currency'] ?? '₹' }}{{ number_format($effectivePrice * $item->quantity, 2) }}
                            @else
                                {{ $company['currency'] ?? '₹' }}{{ number_format($item->price * $item->quantity, 2) }}
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-table">



                <div class="summary-row total-row">
                    <div class="summary-label">Grand Total:</div>
                    <div class="summary-value">{{ $company['currency'] ?? '₹' }}{{ number_format($total, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="invoice-footer">


            <!-- Signature Section -->
            <div class="signature-section">
                <div class="customer-signature">
                    <div class="signature-line">Customer Signature</div>
                </div>
                <div style="display: table-cell; width: 10%;"></div>
                <div class="authorized-signature">
                    <div class="signature-line">Authorized Signature</div>
                </div>
            </div>

            <!-- Thank You Message -->

        </div>
    </div>
</body>

</html>
