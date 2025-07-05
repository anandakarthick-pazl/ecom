<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Welcome to Your New E-Commerce Store!</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $company->name }}!</h2>
            
            <p>Congratulations! Your e-commerce store has been successfully created on our platform.</p>
            
            <h3>📋 Your Store Details:</h3>
            <ul>
                <li><strong>Store Name:</strong> {{ $company->name }}</li>
                <li><strong>Domain:</strong> {{ $company->domain }}</li>
                <li><strong>Theme:</strong> {{ $company->theme->name }}</li>
                <li><strong>Package:</strong> {{ $company->package->name }}</li>
                <li><strong>Trial Ends:</strong> {{ $company->trial_ends_at->format('M d, Y') }}</li>
            </ul>
            
            <h3>🔑 Your Admin Login Details:</h3>
            <ul>
                <li><strong>Email:</strong> {{ $email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
                <li><strong>Login URL:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></li>
            </ul>
            
            <p><strong>⚠️ Important:</strong> Please change your password after your first login for security.</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="button">Access Your Admin Panel</a>
            </div>
            
            <h3>🚀 Next Steps:</h3>
            <ol>
                <li>Log in to your admin panel</li>
                <li>Customize your store settings</li>
                <li>Add your products</li>
                <li>Configure payment methods</li>
                <li>Launch your store!</li>
            </ol>
            
            <p>If you have any questions, our support team is here to help you succeed.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} EcomPlatform. All rights reserved.</p>
            <p>Need help? Contact us at support@ecomplatform.com</p>
        </div>
    </div>
</body>
</html>
