<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page { size: A4; margin: 10mm; }

        body {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.25;
            color: #222;
            background: white;
            margin: 0;
        }

        /* container */
        .page {
            width: 100%;
            box-sizing: border-box;
            padding: 6mm;
        }

        /* header */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid {{ $companySettings['primary_color'] ?? '#2d5016' }};
        }
        .company-logo { max-height: 56px; max-width: 200px; display: block; margin: 4px auto; }
        .company-name { font-size: 16px; font-weight: 700; color: {{ $companySettings['primary_color'] ?? '#2d5016' }}; margin: 2px 0; }
        .company-details { font-size: 9px; color: #666; }

        /* invoice info */
        .invoice-info { width: 100%; margin: 6px 0 10px 0; border-collapse: collapse; font-size: 9px; }
        .invoice-info td { vertical-align: top; padding: 2px 6px; }

        .section-title { font-weight: 700; color: {{ $companySettings['primary_color'] ?? '#2d5016' }}; margin-bottom: 4px; font-size: 10px; }

        /* items table */
        .items-table { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 6px; }
        .items-table th,
        .items-table td { border: 1px solid #e6e6e6; padding: 4px 6px; vertical-align: middle; }
        .items-table th { background: {{ $companySettings['primary_color'] ?? '#2d5016' }}; color: #fff; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .items-table td { background: #fff; }
        .items-table tbody tr:nth-child(even) td { background: #fbfbfb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .product-name { font-weight: 600; font-size: 9px; }
        .product-sku { font-size: 8px; color: #666; font-style: italic; }

        /* totals */
        .totals-section { float: right; width: 280px; margin-top: 6px; font-size: 9px; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 4px 6px; border-bottom: 1px solid #eee; }
        .totals-table tr.total-row td { background: {{ $companySettings['primary_color'] ?? '#2d5016' }}; color: #fff; font-weight: 700; padding: 6px; }

        /* notes & footer */
        .notes { margin-top: 8px; font-size: 9px; color: #444; background: #fbfbfb; padding: 6px; border-left: 4px solid {{ $companySettings['primary_color'] ?? '#2d5016' }}; }
        .footer { clear: both; margin-top: 10px; font-size: 8.5px; color: #666; text-align: center; padding-top: 6px; border-top: 1px dashed #e6e6e6; }

        /* page-break helper */
        .page-break { page-break-after: always; }

        /* Print adjustments */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 6mm; }
        }
    </style>
</head>
<body>
    <div class="page">

        <!-- Header -->
        <div class="header">
            @php
                // try companySettings logo then fallback to globalCompany ->company_logo
                $logoPath = null;
                if(!empty($companySettings['logo'])) {
                    // public storage expected: storage/<path>
                    $possible = public_path('storage/' . $companySettings['logo']);
                    if(file_exists($possible)) { $logoPath = asset('storage/' . $companySettings['logo']); }
                    elseif(file_exists(public_path($companySettings['logo']))) { $logoPath = asset($companySettings['logo']); }
                }
                if(!$logoPath && !empty($globalCompany->company_logo)) {
                    if(file_exists(public_path('storage/' . $globalCompany->company_logo))) {
                        $logoPath = asset('storage/' . $globalCompany->company_logo);
                    }
                }
            @endphp

            @if($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $companySettings['name'] ?? ($globalCompany->company_name ?? 'Company') }}" class="company-logo">
            @endif

            <div class="company-name">{{ $companySettings['name'] ?? ($globalCompany->company_name ?? 'Your Company') }}</div>
            @if(!empty($companySettings['address']))
                <div class="company-details">{{ $companySettings['address'] }}</div>
            @elseif(!empty($globalCompany->full_address))
                <div class="company-details">{{ $globalCompany->full_address }}</div>
            @endif
            <div class="company-details">
                @if(!empty($companySettings['email'])) Email: {{ $companySettings['email'] }} @endif
                @if(!empty($companySettings['phone'])) &nbsp;|&nbsp; Phone: {{ $companySettings['phone'] }} @endif
            </div>
            @if(!empty($companySettings['gst_number'])) <div class="company-details">GST: {{ $companySettings['gst_number'] }}</div> @endif

            <h3 class="invoice-title" style="margin:6px 0 2px 0;">TAX INVOICE</h3>
        </div>

        <!-- Invoice / Bill Info -->
        <table class="invoice-info">
            <tr>
                <td style="width:50%;">
                    <div class="section-title">Invoice Details</div>
                    <div style="font-size:9px; margin-top:2px;">
                        <strong>Invoice No:</strong> INV-{{ $order->order_number }}<br>
                        <strong>Invoice Date:</strong> {{ now()->format('d M, Y') }}<br>
                        <strong>Order Date:</strong> {{ optional($order->created_at)->format('d M, Y') }}<br>
                        <strong>Status:</strong>
                        <span style="font-weight:700; color:#333;">{{ ucfirst($order->status ?? 'N/A') }}</span><br>
                        @if(!empty($order->payment_method)) <strong>Payment:</strong> {{ ucfirst(str_replace('_',' ',$order->payment_method)) }} @endif
                    </div>
                </td>
                <td style="width:50%;">
                    <div class="section-title">Bill To</div>
                    <div style="font-size:9px; margin-top:2px;">
                        <strong>Name:</strong> {{ $order->customer_name ?? 'N/A' }}<br>
                        @if(!empty($order->customer_mobile)) <strong>Mobile:</strong> {{ $order->customer_mobile }}<br> @endif
                        @if(!empty($order->customer_email)) <strong>Email:</strong> {{ $order->customer_email }}<br> @endif
                        @if(!empty($order->delivery_address))
                            <strong>Address:</strong><br>
                            {{ $order->delivery_address }}<br>
                            @if(!empty($order->city) || !empty($order->state) || !empty($order->pincode))
                                {{ trim(($order->city ?? '') . (!empty($order->city) && (!empty($order->state) || !empty($order->pincode)) ? ', ' : '') . ($order->state ?? '') . (!empty($order->state) && !empty($order->pincode) ? ' - ' : '') . ($order->pincode ?? '')) }}
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:35%;">Product Details</th>
                    <th style="width:7%;" class="text-center">Qty</th>
                    <th style="width:10%;" class="text-right">MRP</th>
                    <th style="width:10%;" class="text-right">Offer</th>
                    <th style="width:7%;" class="text-center">Disc %</th>
                    <th style="width:7%;" class="text-center">Tax %</th>
                    <th style="width:9%;" class="text-right">Tax Amt</th>
                    <th style="width:10%;" class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $index => $item)
                @php
                    $quantity = $item->quantity ?? ($item->qty ?? 0);
                    $unitPrice = $item->price ?? ($item->unit_price ?? 0);
                    $mrpPrice = $item->mrp_price ?? 0;
                    $taxPercentage = $item->tax_percentage ?? 0;
                    $taxAmount = $item->tax_amount ?? 0;
                    $itemSubtotal = ($unitPrice * $quantity);
                    // If item->savings or effective discount exists use it else 0
                    $savings = $item->savings ?? 0;
                    $discountPercentage = $item->effective_discount_percentage ?? 0;
                    $lineTotal = $itemSubtotal + $taxAmount - ($savings);
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <div class="product-name">{{ $item->product_name ?? 'Product' }}</div>
                        @if(!empty($item->product) && !empty($item->product->sku))
                            <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                        @elseif(!empty($item->sku))
                            <div class="product-sku">SKU: {{ $item->sku }}</div>
                        @endif
                        @if(!empty($item->offer_name))
                            <div style="color:#c0392b; font-weight:700; font-size:9px;">🏷️ {{ $item->offer_name }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($quantity) }}</td>
                    <td class="text-right">₹{{ number_format($mrpPrice > 0 ? $mrpPrice : $unitPrice, 2) }}</td>
                    <td class="text-right">₹{{ number_format($unitPrice, 2) }}</td>
                    <td class="text-center">{{ $discountPercentage > 0 ? number_format($discountPercentage,1) . '%' : '-' }}</td>
                    <td class="text-center">{{ number_format($taxPercentage,1) }}%</td>
                    <td class="text-right">₹{{ number_format($taxAmount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($lineTotal, 2) }}</td>
                </tr>

                {{-- Insert page break and repeat header after every 20 items (but not after last) --}}
                @if((($loop->index + 1) % 20 === 0) && !$loop->last)
                    </tbody>
                </table>

                <div class="page-break"></div>

                <!-- repeat table header on new page -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:35%;">Product Details</th>
                            <th style="width:7%;" class="text-center">Qty</th>
                            <th style="width:10%;" class="text-right">MRP</th>
                            <th style="width:10%;" class="text-right">Offer</th>
                            <th style="width:7%;" class="text-center">Disc %</th>
                            <th style="width:7%;" class="text-center">Tax %</th>
                            <th style="width:9%;" class="text-right">Tax Amt</th>
                            <th style="width:10%;" class="text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                @endif
            @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section" aria-hidden="false">
            <table class="totals-table">
                @php
                    $subtotal = $order->subtotal ?? 0;
                    $totalDiscount = $order->discount ?? 0;
                    $cgst = $order->cgst_amount ?? 0;
                    $sgst = $order->sgst_amount ?? 0;
                    $igst = $order->igst_amount ?? 0;
                    $delivery = $order->delivery_charge ?? 0;
                    $grandTotal = $order->total ?? ($order->total_amount ?? 0);
                    $totalSavings = $order->items->sum('savings') ?? 0;
                @endphp

                @if($totalSavings > 0)
                    <tr>
                        <td>Total MRP:</td>
                        <td class="text-right">₹{{ number_format($order->items->sum(function($it){ return ($it->mrp_price ?? 0) * ($it->quantity ?? ($it->qty ?? 0)); }), 2) }}</td>
                    </tr>
                    <tr>
                        <td>You Saved (Offers):</td>
                        <td class="text-right">-₹{{ number_format($totalSavings, 2) }}</td>
                    </tr>
                @endif

                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹{{ number_format($subtotal, 2) }}</td>
                </tr>

                @if($totalDiscount > 0)
                    <tr>
                        <td>Additional Discount:</td>
                        <td class="text-right">-₹{{ number_format($totalDiscount, 2) }}</td>
                    </tr>
                @endif

                @if($cgst > 0)
                    <tr><td>CGST:</td><td class="text-right">₹{{ number_format($cgst, 2) }}</td></tr>
                @endif
                @if($sgst > 0)
                    <tr><td>SGST:</td><td class="text-right">₹{{ number_format($sgst, 2) }}</td></tr>
                @endif
                @if($igst > 0)
                    <tr><td>IGST:</td><td class="text-right">₹{{ number_format($igst, 2) }}</td></tr>
                @endif
                @if($delivery > 0)
                    <tr><td>Delivery Charge:</td><td class="text-right">₹{{ number_format($delivery, 2) }}</td></tr>
                @endif

                <tr class="total-row">
                    <td>Total Amount:</td>
                    <td class="text-right">₹{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <!-- Notes -->
        @if(!empty($order->notes) || !empty($order->admin_notes))
            <div class="notes">
                @if(!empty($order->notes))
                    <div><strong>Invoice Notes:</strong><br>{{ $order->notes }}</div>
                @endif
                @if(!empty($order->admin_notes))
                    <div style="margin-top:6px;"><strong>Admin Notes:</strong><br>{{ $order->admin_notes }}</div>
                @endif
            </div>
        @endif

        <!-- Terms -->
        <div class="notes" style="margin-top:8px;">
            <div style="font-size:9px;">
                <strong>Terms & Conditions:</strong><br>
                1. Payment due within 30 days.<br>
                2. Goods once sold cannot be returned without approval.<br>
                3. This is a computer-generated invoice.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>{{ $companySettings['name'] ?? ($globalCompany->company_name ?? 'Your Company') }}</strong><br>
            @if(!empty($companySettings['address'])) {{ $companySettings['address'] }}<br> @endif
            @if(!empty($companySettings['email'])) Email: {{ $companySettings['email'] }} @endif
            @if(!empty($companySettings['phone'])) &nbsp;|&nbsp; Phone: {{ $companySettings['phone'] }} @endif
            <div style="margin-top:6px;">Generated on {{ now()->format('d M Y, h:i A') }}</div>
        </div>

    </div>
</body>
</html>
