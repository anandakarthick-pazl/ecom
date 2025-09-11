<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .invoice-header { border-bottom: 2px solid #2d5016; padding-bottom: 20px; margin-bottom: 30px; }
        .company-logo { max-height: 80px; margin-bottom: 10px; }
        .company-name { color: #2d5016; font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .invoice-title { font-size: 18px; color: #666; }
        .invoice-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .customer-details, .order-summary { margin-bottom: 30px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background: #2d5016; color: white; }
        .total-section { text-align: right; }
        .total-row { font-weight: bold; font-size: 16px; }
        .print-btn { margin-bottom: 20px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="print-btn">
        <button onclick="window.print()" style="background: #2d5016; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            Print Invoice
        </button>
    </div>

    <div class="invoice-header">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                @if($globalCompany->company_logo ?? false)
                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" alt="{{ $globalCompany->company_name }}" class="company-logo">
                @else
                    <div style="font-size: 32px; margin-bottom: 10px;">🌿</div>
                @endif
                <div class="company-name">{{ $globalCompany->company_name ?? 'Herbal Bliss' }}</div>
                {{-- <div style="color: #666; margin-bottom: 10px;">Natural & Organic Products</div> --}}
                @if($globalCompany->company_address ?? false)
                    <div>{{ $globalCompany->company_address }}</div>
                @endif
                <div>Email: {{ $globalCompany->company_email ?? 'info@herbalbliss.com' }}</div>
                <div>Phone: {{ $globalCompany->company_phone ?? '+91 9876543210' }}</div>
                @if($globalCompany->gst_number ?? false)
                    <div><strong>GST No:</strong> {{ $globalCompany->gst_number }}</div>
                @endif
            </div>
            <div style="text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div><strong>{{ $order->order_number }}</strong></div>
                <div>Date: {{ $order->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="invoice-details">
        <div style="display: flex; justify-content: space-between;">
            <div>
                <strong>Bill To:</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_mobile }}<br>
                {{ $order->delivery_address }}<br>
                {{ $order->city }}, {{ $order->state }} {{ $order->pincode }}
            </div>
            <div style="text-align: right;">
                <strong>Order Status:</strong> {{ ucfirst($order->status) }}<br>
                <strong>Payment Method:</strong> Cash on Delivery<br>
                @if($order->shipped_at)
                    <strong>Shipped:</strong> {{ $order->shipped_at->format('M d, Y') }}<br>
                @endif
                @if($order->delivered_at)
                    <strong>Delivered:</strong> {{ $order->delivered_at->format('M d, Y') }}<br>
                @endif
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>MRP</th>
                <th>Offer Price</th>
                <th>Discount</th>
                <th>Quantity</th>
                <th>Tax %</th>
                <th>Tax Amount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if($item->product)
                        <br><small style="color: #666;">{{ $item->product->category->name ?? '' }}</small>
                    @endif
                    @if($item->offer_name)
                        <br><small style="color: #e74c3c; font-weight: bold;">🏷️ {{ $item->offer_name }}</small>
                    @endif
                </td>
                <td>
                    @if($item->mrp_price > 0)
                        ₹{{ number_format($item->mrp_price, 2) }}
                    @else
                        ₹{{ number_format($item->price, 2) }}
                    @endif
                </td>
                <td>
                    @if($item->mrp_price > 0 && $item->mrp_price > $item->price)
                        <span style="color: #27ae60; font-weight: bold;">₹{{ number_format($item->price, 2) }}</span>
                    @else
                        ₹{{ number_format($item->price, 2) }}
                    @endif
                </td>
                <td>
                    @if($item->hasDiscount())
                        <span style="color: #e74c3c; font-weight: bold;">
                            @if($item->effective_discount_percentage > 0)
                                {{ number_format($item->effective_discount_percentage, 1) }}% OFF
                            @endif
                            @if($item->savings > 0)
                                <br><small>Save ₹{{ number_format($item->savings, 2) }}</small>
                            @endif
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->tax_percentage }}%</td>
                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                <td>₹{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table style="width: 300px; margin-left: auto;">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($order->subtotal, 2) }}</strong></td>
            </tr>
            @php
                $totalMrp = $order->items->sum('mrp_total');
                $totalSavings = $order->items->sum('savings');
            @endphp
            @if($totalSavings > 0)
            <tr>
                <td><strong>Total MRP:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($totalMrp, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>You Saved:</strong></td>
                <td style="text-align: right; color: #e74c3c;"><strong>-₹{{ number_format($totalSavings, 2) }}</strong></td>
            </tr>
            @endif
            @if($order->discount > 0)
            <tr>
                <td><strong>Additional Discount:</strong></td>
                <td style="text-align: right; color: green;"><strong>-₹{{ number_format($order->discount, 2) }}</strong></td>
            </tr>
            @endif
            <tr>
                <td><strong>CGST:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($order->cgst_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>SGST:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($order->sgst_amount, 2) }}</strong></td>
            </tr>
            {{-- <tr>
                <td><strong>Delivery Charge:</strong></td>
                <td style="text-align: right;">
                    <strong>
                        @if($order->delivery_charge == 0)
                            FREE
                        @else
                            ₹{{ number_format($order->delivery_charge, 2) }}
                        @endif
                    </strong>
                </td>
            </tr> --}}
            <tr class="total-row" style="border-top: 2px solid #2d5016;">
                <td><strong>Total:</strong></td>
                <td style="text-align: right;"><strong>₹{{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($order->notes)
    <div style="margin-top: 30px;">
        <strong>Customer Notes:</strong><br>
        {{ $order->notes }}
    </div>
    @endif

    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
        <p>Thank you for choosing {{ $globalCompany->company_name ?? 'Herbal Bliss' }}!</p>
        <p>For any queries, please contact us at {{ $globalCompany->company_email ?? 'info@herbalbliss.com' }} or {{ $globalCompany->company_phone ?? '+91 9876543210' }}</p>
        @if($globalCompany->company_address ?? false)
            <p>{{ $globalCompany->company_address }}</p>
        @endif
    </div>
</body>
</html>
