<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} is Back in Stock!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .emoji {
            font-size: 36px;
            margin-bottom: 10px;
            display: block;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            color: #2c5aa0;
            margin-bottom: 20px;
        }
        .product-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background-color: #f8f9fa;
        }
        .product-name {
            font-size: 22px;
            font-weight: 600;
            color: #2c5aa0;
            margin-bottom: 10px;
        }
        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            flex-wrap: wrap;
        }
        .price {
            font-size: 24px;
            font-weight: 700;
            color: #28a745;
        }
        .stock-info {
            background-color: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .discount-badge {
            background-color: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 18px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #0056b3, #003d82);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        }
        .urgency-message {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .urgency-message strong {
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6c757d;
            text-decoration: none;
        }
        .unsubscribe {
            margin-top: 20px;
            font-size: 12px;
            color: #adb5bd;
        }
        .unsubscribe a {
            color: #adb5bd;
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header h1 {
                font-size: 24px;
            }
            .product-details {
                flex-direction: column;
                align-items: flex-start;
            }
            .price {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="emoji">🎉</span>
            <h1>Great News!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">Your requested item is back in stock</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $customerName }},
            </div>

            <p>We have fantastic news! The product you've been waiting for is now available again.</p>

            <div class="product-card">
                <div class="product-name">{{ $product->name }}</div>
                
                <div class="product-details">
                    <div class="price">₹{{ number_format($product->final_price, 2) }}</div>
                    <div>
                        <span class="stock-info">{{ $product->stock }} in stock</span>
                        @if($product->discount_percentage > 0)
                            <span class="discount-badge">{{ $product->discount_percentage }}% OFF</span>
                        @endif
                    </div>
                </div>

                @if($product->description)
                <p style="color: #6c757d; margin: 15px 0;">
                    {{ Str::limit($product->description, 150) }}
                </p>
                @endif
            </div>

            <div class="urgency-message">
                <strong>⚡ Act Fast!</strong> This item was out of stock before and might sell out quickly again. We recommend ordering soon to avoid disappointment.
            </div>

            <div style="text-align: center;">
                <a href="{{ $productUrl }}" class="cta-button">
                    🛒 Order Now
                </a>
            </div>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <h3 style="color: #2c5aa0; margin-bottom: 15px;">Why choose {{ $companyName }}?</h3>
                <ul style="color: #6c757d; padding-left: 20px;">
                    <li>✅ Premium quality products</li>
                    <li>✅ Fast and secure delivery</li>
                    <li>✅ Easy returns and exchanges</li>
                    <li>✅ 24/7 customer support</li>
                </ul>
            </div>

            <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: center;">
                <h4 style="color: #1976d2; margin-bottom: 10px;">Need Help?</h4>
                <p style="margin: 5px 0; color: #424242;">📧 Email us: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
                <p style="margin: 5px 0; color: #424242;">🌐 Visit our website: <a href="{{ $companyUrl }}">{{ $companyName }}</a></p>
            </div>
        </div>

        <div class="footer">
            <p><strong>{{ $companyName }}</strong></p>
            <p>Thank you for choosing us for your shopping needs!</p>
            
            <div class="social-links">
                <a href="{{ $companyUrl }}">🌐 Website</a>
                <a href="mailto:{{ $supportEmail }}">📧 Contact Us</a>
            </div>

            <div class="unsubscribe">
                <p>You received this email because you requested to be notified when "{{ $product->name }}" was back in stock.</p>
                <p><a href="{{ $unsubscribeUrl }}">Unsubscribe from this product's notifications</a></p>
            </div>
        </div>
    </div>
</body>
</html>
