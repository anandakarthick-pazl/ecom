<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isAdmin ? 'Admin Password Reset' : 'Password Reset' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: {{ $isAdmin ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)' }};
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #4a5568;
        }
        .reset-button {
            display: inline-block;
            background: {{ $isAdmin ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)' }};
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }
        .alternative-link {
            margin-top: 30px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 4px solid #4f46e5;
        }
        .alternative-link p {
            margin: 0;
            font-size: 14px;
            color: #718096;
        }
        .alternative-link code {
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
        }
        .security-note {
            margin-top: 30px;
            padding: 20px;
            background: #fef5e7;
            border-radius: 8px;
            border-left: 4px solid #f6ad55;
        }
        .security-note h3 {
            margin: 0 0 10px 0;
            color: #c05621;
            font-size: 16px;
        }
        .security-note p {
            margin: 0;
            font-size: 14px;
            color: #c05621;
        }
        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #718096;
        }
        .footer .company-name {
            font-weight: 600;
            color: #4a5568;
        }
        .expiry-notice {
            margin-top: 20px;
            padding: 15px;
            background: #e6fffa;
            border-radius: 6px;
            border-left: 4px solid #38b2ac;
        }
        .expiry-notice p {
            margin: 0;
            font-size: 14px;
            color: #2c7a7b;
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .content {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 20px;
            }
            .reset-button {
                display: block;
                text-align: center;
                margin: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <!-- Header -->
            <div class="header">
                <h1>
                    @if($isAdmin)
                        🔐 Admin Password Reset
                    @else
                        🔑 Password Reset
                    @endif
                </h1>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="greeting">
                    Hello {{ $user->name }},
                </div>

                <div class="message">
                    @if($isAdmin)
                        We received a request to reset your admin account password. If you made this request, click the button below to reset your password:
                    @else
                        We received a request to reset your account password. If you made this request, click the button below to reset your password:
                    @endif
                </div>

                <!-- Reset Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" class="reset-button">
                        Reset Password
                    </a>
                </div>

                <!-- Expiry Notice -->
                <div class="expiry-notice">
                    <p><strong>⏰ Important:</strong> This reset link will expire in 24 hours for security reasons.</p>
                </div>

                <!-- Alternative Link -->
                <div class="alternative-link">
                    <p><strong>Can't click the button?</strong> Copy and paste this link into your browser:</p>
                    <p><code>{{ $resetUrl }}</code></p>
                </div>

                <!-- Security Note -->
                <div class="security-note">
                    <h3>🛡️ Security Notice</h3>
                    <p>If you did not request this password reset, please ignore this email or contact support if you have concerns. Your account remains secure.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>
                    This email was sent by <span class="company-name">{{ config('app.name') }}</span><br>
                    If you have any questions, please contact our support team.
                </p>
                <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
