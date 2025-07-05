<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Trial Expiry Alert</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $company->name }}!</h2>
            
            @if($daysRemaining > 0)
                <div class="warning">
                    <strong>Your trial expires in {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }}!</strong>
                </div>
                
                <p>Your free trial period is coming to an end. To continue using our platform without interruption, please upgrade to a paid plan.</p>
                
                <h3>📊 Your Account Details:</h3>
                <ul>
                    <li><strong>Store Name:</strong> {{ $company->name }}</li>
                    <li><strong>Current Package:</strong> {{ $company->package->name }}</li>
                    <li><strong>Trial Ends:</strong> {{ $company->trial_ends_at->format('M d, Y') }}</li>
                    <li><strong>Days Remaining:</strong> {{ $daysRemaining }}</li>
                </ul>
                
                <p><strong>Don't lose access to your store!</strong> Upgrade now to keep your business running smoothly.</p>
            @else
                <div class="warning">
                    <strong>Your trial has expired!</strong>
                </div>
                
                <p>Your free trial period has ended. Your store has been temporarily suspended. Please upgrade to a paid plan to reactivate your store.</p>
                
                <h3>📊 Your Account Details:</h3>
                <ul>
                    <li><strong>Store Name:</strong> {{ $company->name }}</li>
                    <li><strong>Current Package:</strong> {{ $company->package->name }}</li>
                    <li><strong>Trial Expired:</strong> {{ $company->trial_ends_at->format('M d, Y') }}</li>
                    <li><strong>Status:</strong> Suspended</li>
                </ul>
                
                <p><strong>Reactivate your store today!</strong> Choose a plan that fits your business needs.</p>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="#" class="button">Upgrade Now</a>
            </div>
            
            <h3>💼 Available Plans:</h3>
            <ul>
                <li><strong>Starter:</strong> $29.99/month - Perfect for small businesses</li>
                <li><strong>Professional:</strong> $59.99/month - Ideal for growing businesses</li>
                <li><strong>Enterprise:</strong> $149.99/month - For large businesses</li>
            </ul>
            
            <p>Questions? Our support team is ready to help you choose the right plan.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} EcomPlatform. All rights reserved.</p>
            <p>Need help? Contact us at support@ecomplatform.com</p>
        </div>
    </div>
</body>
</html>
