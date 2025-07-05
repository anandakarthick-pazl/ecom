<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, {{ $company['primary_color'] ?? '#2c3e50' }}, {{ $company['secondary_color'] ?? '#34495e' }});
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin: 10px 0;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: {{ $company['primary_color'] ?? '#2c3e50' }};
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($company['logo'])
                <img src="{{ asset('storage/' . $company['logo']) }}" alt="{{ $company['name'] }}" style="max-height: 60px; margin-bottom: 10px;">
            @else
                <div style="font-size: 40px; margin-bottom: 10px;">🏪</div>
            @endif
            <h1>{{ $company['name'] ?? 'Your Store' }}</h1>
            <p>Order Status Update</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $order->customer_name }},</h2>
            
            <p>{{ $statusMessage }}</p>
            
            <div class="order-info">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Current Status:</strong> 
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
                <p><strong>Total Amount:</strong> ₹{{ number_format($order->total, 2) }}</p>
                
                @if($order->status === 'shipped' && $order->shipped_at)
                    <p><strong>Shipped Date:</strong> {{ $order->shipped_at->format('M d, Y') }}</p>
                @endif
                
                @if($order->status === 'delivered' && $order->delivered_at)
                    <p><strong>Delivered Date:</strong> {{ $order->delivered_at->format('M d, Y') }}</p>
                @endif
            </div>
            
            <div class="order-items">
                <h3>Order Items</h3>
                @foreach($order->items as $item)
                    <div class="item">
                        <div>
                            <strong>{{ $item->product_name }}</strong><br>
                            <small>Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}</small>
                        </div>
                        <div>
                            <strong>₹{{ number_format($item->quantity * $item->price, 2) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('track.order') }}" class="btn">Track Your Order</a>
            </div>
            
            @if($order->status === 'delivered')
                <div style="background: #e8f5e8; padding: 15px; border-radius: 6px; margin: 20px 0;">
                    <h4 style="color: {{ $company['primary_color'] ?? '#2c3e50' }}; margin: 0 0 10px 0;">Thank you for choosing us!</h4>
                    <p style="margin: 0;">We hope you love your products. If you have any questions or concerns, please don't hesitate to contact us.</p>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p><strong>{{ $company['name'] ?? 'Your Store' }}</strong></p>
            @if($company['address'])
                <p>{{ $company['address'] }}</p>
            @endif
            <p>
                @if($company['email'])
                    Email: {{ $company['email'] }}
                @endif
                @if($company['email'] && $company['phone'])
                    | 
                @endif
                @if($company['phone'])
                    Phone: {{ $company['phone'] }}
                @endif
            </p>
        </div>
    </div>
</body>
</html>
