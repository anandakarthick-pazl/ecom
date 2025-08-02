<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Back in Stock</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .emoji {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .product-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #28a745;
        }
        .product-name {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .price {
            font-size: 24px;
            font-weight: 700;
            color: #28a745;
        }
        .stock-info {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .cta-section {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        .urgency {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .urgency .icon {
            color: #f39c12;
            margin-right: 8px;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .footer a {
            color: #20c997;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #20c997;
            font-size: 20px;
            text-decoration: none;
        }
        .unsubscribe {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                max-width: calc(100% - 20px);
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .product-details {
                flex-direction: column;
                align-items: flex-start;
            }
            .cta-button {
                padding: 12px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <span class="emoji">🎉</span>
            <h1>Great News!</h1>
            <p>Your waited product is back in stock</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $customer_name }},
            </div>

            <p>We have some exciting news for you! The product you were waiting for is now available again.</p>

            <!-- Product Card -->
            <div class="product-card">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-details">
                    <div>
                        @if($product->discount_price)
                            <span class="price">₹{{ number_format($product->discount_price, 2) }}</span>
                            <span style="text-decoration: line-through; color: #6c757d; margin-left: 10px;">₹{{ number_format($product->price, 2) }}</span>
                            <span style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-left: 10px;">
                                {{ $product->discount_percentage }}% OFF
                            </span>
                        @else
                            <span class="price">₹{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <div class="stock-info">
                        ✅ {{ $product->stock }} in stock
                    </div>
                </div>
                
                @if($product->short_description)
                    <p style="margin-top: 15px; color: #6c757d; font-size: 14px;">{{ $product->short_description }}</p>
                @endif
            </div>

            <!-- Urgency Message -->
            <div class="urgency">
                <span class="icon">⚡</span>
                <strong>Limited Stock Alert:</strong> Only {{ $product->stock }} items available. Order now before it's sold out again!
            </div>

            <!-- Call to Action -->
            <div class="cta-section">
                <a href="{{ route('product', $product->slug) }}" class="cta-button">
                    🛒 Order Now
                </a>
                <p style="margin-top: 15px; font-size: 14px; color: #6c757d;">
                    Or visit our website: <a href="{{ route('shop') }}" style="color: #28a745;">{{ config('app.url') }}</a>
                </p>
            </div>

            <div style="background: #e3f2fd; border-radius: 8px; padding: 20px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #1976d2;">💡 Why Choose Us?</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>✅ Genuine products with quality guarantee</li>
                    <li>🚚 Fast and secure delivery</li>
                    <li>💯 Customer satisfaction guaranteed</li>
                    <li>📞 24/7 customer support</li>
                </ul>
            </div>

            <p style="margin-top: 30px;">
                Thank you for choosing us! We appreciate your patience and look forward to serving you.
            </p>
            
            <p style="margin-bottom: 0;">
                Best regards,<br>
                <strong>{{ config('app.name', 'Green Valley Herbs') }} Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h3 style="margin-top: 0;">{{ config('app.name', 'Green Valley Herbs') }}</h3>
            
            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="WhatsApp">📱</a>
                <a href="#" title="Website">🌐</a>
            </div>
            
            <p>
                📧 Email: {{ config('mail.from.address') }}<br>
                📞 Phone: +91 9876543210<br>
                🌐 Website: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
            
            <div class="unsubscribe">
                <p>You received this email because you subscribed to stock notifications for this product.</p>
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
