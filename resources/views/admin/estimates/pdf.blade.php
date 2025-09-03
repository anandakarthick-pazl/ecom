<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate #{{ $estimate->estimate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-logo {
            max-height: 80px;
            max-width: 200px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }

        .estimate-title {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .estimate-number {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .estimate-dates {
            font-size: 11px;
            color: #666;
        }

        .billing-section {
            display: table;
            width: 100%;
            margin: 30px 0;
        }

        .billing-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .billing-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }

        .billing-header {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .billing-details {
            font-size: 11px;
            line-height: 1.4;
        }

        .billing-details p {
            margin-bottom: 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            font-size: 10px;
        }

        .items-table th {
            background-color: #2563eb;
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
        }

        .items-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 9px;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .price-details {
            font-size: 8px;
            line-height: 1.3;
        }

        .original-price {
            text-decoration: line-through;
            color: #666;
            font-size: 8px;
        }

        .offer-price {
            color: #16a34a;
            font-weight: bold;
            font-size: 9px;
        }

        .discount-badge {
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: bold;
            display: inline-block;
            margin-top: 2px;
        }

        .tax-info {
            color: #666;
            font-size: 8px;
            margin-top: 2px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .totals-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .totals-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            font-size: 12px;
        }

        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #2563eb;
            border-bottom: 2px solid #2563eb;
            background-color: #f3f4f6;
        }

        .notes-section {
            margin-top: 30px;
        }

        .notes-header {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .notes-content {
            font-size: 11px;
            line-height: 1.5;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
            white-space: pre-wrap;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #374151;
        }

        .status-sent {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .status-accepted {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-expired {
            background-color: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        @media print {
            .container {
                padding: 0;
            }
            
            body {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                @if(isset($globalCompany->company_logo) && $globalCompany->company_logo)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="Company Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $globalCompany->company_name ?? 'Your Company' }}</div>
                <div class="company-details">
                    @if(isset($globalCompany->full_address) && $globalCompany->full_address)
                        <p>{{ $globalCompany->full_address }}</p>
                    @endif
                    @if(isset($globalCompany->contact_info) && $globalCompany->contact_info)
                        <p>{{ $globalCompany->contact_info }}</p>
                    @endif
                    @if(isset($globalCompany->gst_number) && $globalCompany->gst_number)
                        <p><strong>GST:</strong> {{ $globalCompany->gst_number }}</p>
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="estimate-title">ESTIMATE</div>
                <div class="estimate-number">#{{ $estimate->estimate_number }}</div>
                <div class="estimate-dates">
                    <p><strong>Date:</strong> {{ $estimate->estimate_date->format('F d, Y') }}</p>
                    <p><strong>Valid Until:</strong> {{ $estimate->valid_until->format('F d, Y') }}</p>
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-{{ $estimate->status }}">{{ ucfirst($estimate->status) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-left">
                <div class="billing-header">From:</div>
                <div class="billing-details">
                    <p><strong>{{ $globalCompany->company_name ?? 'Your Company' }}</strong></p>
                    @if(isset($globalCompany->full_address) && $globalCompany->full_address)
                        <p>{{ $globalCompany->full_address }}</p>
                    @endif
                    @if(isset($globalCompany->company_phone) && $globalCompany->company_phone)
                        <p>Phone: {{ $globalCompany->company_phone }}</p>
                    @endif
                    @if(isset($globalCompany->company_email) && $globalCompany->company_email)
                        <p>Email: {{ $globalCompany->company_email }}</p>
                    @endif
                </div>
            </div>
            <div class="billing-right">
                <div class="billing-header">To:</div>
                <div class="billing-details">
                    <p><strong>{{ $estimate->customer_name }}</strong></p>
                    @if($estimate->customer_address)
                        <p>{{ $estimate->customer_address }}</p>
                    @endif
                    @if($estimate->customer_phone)
                        <p>Phone: {{ $estimate->customer_phone }}</p>
                    @endif
                    @if($estimate->customer_email)
                        <p>Email: {{ $estimate->customer_email }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Item Description</th>
                    <th style="width: 8%" class="text-center">Qty</th>
                    <th style="width: 18%" class="text-right">Price Details</th>
                    <th style="width: 10%" class="text-right">Tax</th>
                    <th style="width: 12%" class="text-right">Line Total</th>
                    <th style="width: 12%" class="text-right">Total + Tax</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estimate->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name ?? 'Product' }}</strong>
                        @if($item->description)
                            <br><small style="color: #666; font-size: 8px;">{{ $item->description }}</small>
                        @endif
                        @if($item->product && $item->product->sku)
                            <br><small style="color: #666; font-size: 8px;">SKU: {{ $item->product->sku }}</small>
                        @endif
                        @if($item->product && $item->product->offer_details)
                            <br><small class="discount-badge">{{ $item->product->offer_details['offer_name'] ?? 'Special Offer' }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                    <td class="text-right price-details">
                        @if($item->discount_amount > 0)
                            <div class="original-price">MRP: ₹{{ number_format($item->mrp_price, 2) }}</div>
                            <div class="offer-price">₹{{ number_format($item->offer_price, 2) }}</div>
                            <div class="discount-badge">{{ $item->discount_percentage }}% OFF</div>
                            <div style="font-size: 7px; color: #666; margin-top: 2px;">Save: ₹{{ number_format($item->discount_amount, 2) }}</div>
                        @else
                            <div>₹{{ number_format($item->unit_price, 2) }}</div>
                        @endif
                    </td>
                    <td class="text-right tax-info">
                        @if($item->product && $item->product->item_tax_percentage > 0)
                            <div>{{ number_format($item->product->item_tax_percentage, 1) }}%</div>
                            <div>₹{{ number_format($item->tax_amount, 2) }}</div>
                        @else
                            <div>-</div>
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

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-left">
                @if($estimate->notes)
                <div class="notes-section">
                    <div class="notes-header">Notes:</div>
                    <div class="notes-content">{{ $estimate->notes }}</div>
                </div>
                @endif
            </div>
            <div class="totals-right">
                @php
                    $totalMrpValue = $estimate->items->sum(function($item) {
                        return ($item->mrp_price ?? $item->unit_price) * $item->quantity;
                    });
                    $totalDiscountAmount = $estimate->items->sum(function($item) {
                        return ($item->discount_amount ?? 0) * $item->quantity;
                    });
                    $totalTaxAmount = $estimate->items->sum('tax_amount') ?? 0;
                @endphp
                <table class="totals-table">
                    <tr>
                        <td><strong>Subtotal (Before Discounts):</strong></td>
                        <td class="text-right">₹{{ number_format($totalMrpValue, 2) }}</td>
                    </tr>
                    @if($totalDiscountAmount > 0)
                    <tr>
                        <td><strong>Product Discounts:</strong></td>
                        <td class="text-right" style="color: #16a34a;">-₹{{ number_format($totalDiscountAmount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Subtotal (After Product Discounts):</strong></td>
                        <td class="text-right">₹{{ number_format($estimate->subtotal, 2) }}</td>
                    </tr>
                    @if($totalTaxAmount > 0)
                    <tr>
                        <td><strong>Total Tax (GST):</strong></td>
                        <td class="text-right">₹{{ number_format($totalTaxAmount, 2) }}</td>
                    </tr>
                    @endif
                    @if($estimate->tax_amount > 0 && $estimate->tax_amount != $totalTaxAmount)
                    <tr>
                        <td><strong>Additional Tax:</strong></td>
                        <td class="text-right">₹{{ number_format($estimate->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($estimate->discount > 0)
                    <tr>
                        <td><strong>Additional Discount:</strong></td>
                        <td class="text-right" style="color: #16a34a;">-₹{{ number_format($estimate->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>Grand Total:</strong></td>
                        <td class="text-right"><strong>₹{{ number_format($estimate->total_amount, 2) }}</strong></td>
                    </tr>
                    @if($totalDiscountAmount > 0)
                    <tr style="border: none;">
                        <td colspan="2" class="text-right" style="color: #16a34a; font-size: 10px; padding-top: 5px;">
                            <strong>🎉 You saved ₹{{ number_format($totalDiscountAmount + $estimate->discount, 2) }} on this estimate!</strong>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Terms and Conditions -->
        @if($estimate->terms_conditions)
        <div class="notes-section">
            <div class="notes-header">Terms & Conditions:</div>
            <div class="notes-content">{{ $estimate->terms_conditions }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This estimate is valid until {{ $estimate->valid_until->format('F d, Y') }}</p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
            @if(isset($globalCompany->website) && $globalCompany->website)
                <p>{{ $globalCompany->website }}</p>
            @endif
        </div>
    </div>
</body>
</html>
