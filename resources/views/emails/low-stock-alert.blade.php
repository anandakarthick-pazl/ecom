<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Low Stock Alert</title>
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
            background: linear-gradient(135deg, #dc3545, #ffc107);
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
        .alert-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .product-list {
            margin: 20px 0;
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .stock-level {
            font-weight: bold;
            color: #dc3545;
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
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Low Stock Alert</h1>
            <p>Inventory Management System</p>
        </div>
        
        <div class="content">
            <h2>Attention Required: Low Stock Products</h2>
            
            <div class="alert-info">
                <h3>⚠️ Stock Alert</h3>
                <p>The following {{ count($lowStockProducts) }} product(s) are running low in stock and need immediate attention:</p>
            </div>
            
            <div class="product-list">
                <h3>Products Requiring Restock</h3>
                @foreach($lowStockProducts as $product)
                    <div class="product-item">
                        <div>
                            <strong>{{ $product->name }}</strong><br>
                            <small>SKU: {{ $product->sku ?? 'N/A' }}</small><br>
                            <small>Category: {{ $product->category->name ?? 'N/A' }}</small>
                        </div>
                        <div style="text-align: right;">
                            <div class="stock-level">{{ $product->stock_quantity ?? 0 }} units</div>
                            <small>Min. Required: {{ \App\Models\AppSetting::get('low_stock_threshold', 10) }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="background: #e8f4fd; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h4 style="color: #0c5460; margin: 0 0 10px 0;">Recommended Actions:</h4>
                <ul style="margin: 0; color: #0c5460;">
                    <li>Review supplier contacts and place purchase orders</li>
                    <li>Update product availability on the website if needed</li>
                    <li>Consider temporarily disabling products that are out of stock</li>
                    <li>Update low stock threshold if necessary</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('admin.inventory.low-stock') }}" class="btn">View Inventory Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p>{{ $globalCompany->company_name ?? 'Herbal Bliss' }} - Inventory Management System</p>
            <p>This is an automated alert. Please take necessary action to maintain adequate stock levels.</p>
            <p>Generated on: {{ now()->format('M d, Y H:i A') }}</p>
        </div>
    </div>
</body>
</html>
