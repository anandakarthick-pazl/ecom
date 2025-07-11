<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Invoice</title>
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
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .invoice-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
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
        .company-info {
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($company['logo']) && !empty($company['logo']))
                <img src="{{ asset('storage/' . $company['logo']) }}" alt="{{ $company['name'] ?? 'Your Store' }}" class="logo">
            @else
                <div style="font-size: 40px; margin-bottom: 10px;">🏪</div>
            @endif
            <h1>{{ $company['name'] ?? 'Your Store' }}</h1>
            <p>Order Invoice</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $order->customer_name ?? 'Valued Customer' }},</h2>
            
            <p>Thank you for your order! Please find your invoice details below.</p>
            
            <div class="invoice-info">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> {{ $order->order_number ?? 'N/A' }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</p>
                <p><strong>Total Amount:</strong> ₹{{ number_format($order->total ?? 0, 2) }}</p>
                <p><strong>Payment Status:</strong> Paid</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('track.order') }}" class="btn">Track Your Order</a>
            </div>
            
            <p>If you have any questions about your order or invoice, please don't hesitate to contact us.</p>
            
            <div class="company-info">
                <h4>{{ $company['name'] ?? 'Your Store' }}</h4>
                @if(isset($company['address']) && !empty($company['address']))
                    <p><strong>Address:</strong> {{ $company['address'] }}</p>
                @endif
                @if(isset($company['phone']) && !empty($company['phone']))
                    <p><strong>Phone:</strong> {{ $company['phone'] }}</p>
                @endif
                @if(isset($company['email']) && !empty($company['email']))
                    <p><strong>Email:</strong> {{ $company['email'] }}</p>
                @endif
                @if(isset($company['gst_number']) && !empty($company['gst_number']))
                    <p><strong>GST No:</strong> {{ $company['gst_number'] }}</p>
                @endif
            </div>
        </div>
        
        <div class="footer">
            <p><strong>{{ $company['name'] ?? 'Your Store' }}</strong></p>
            @if(isset($company['address']) && !empty($company['address']))
                <p>{{ $company['address'] }}</p>
            @endif
            <p>
                @if(isset($company['email']) && !empty($company['email']))
                    Email: {{ $company['email'] }}
                @endif
                @if(isset($company['email']) && !empty($company['email']) && isset($company['phone']) && !empty($company['phone']))
                    | 
                @endif
                @if(isset($company['phone']) && !empty($company['phone']))
                    Phone: {{ $company['phone'] }}
                @endif
                @if((isset($company['email']) && !empty($company['email']) || isset($company['phone']) && !empty($company['phone'])) && isset($company['gst_number']) && !empty($company['gst_number']))
                    <br>
                @endif
                @if(isset($company['gst_number']) && !empty($company['gst_number']))
                    GST No: {{ $company['gst_number'] }}
                @endif
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated email. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>