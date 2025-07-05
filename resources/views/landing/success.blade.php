<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-green-500 text-6xl mb-6">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                Welcome to Herbal Ecom!
            </h1>
            
            <p class="text-gray-600 mb-6">
                Your store <strong>{{ $company->name }}</strong> has been created successfully!
            </p>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800 mb-2">
                    <strong>Your store URL:</strong>
                </p>
                <a href="http://{{ $company->domain }}" 
                   class="text-green-600 hover:text-green-800 font-medium break-all"
                   target="_blank">
                    http://{{ $company->domain }}
                </a>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800 mb-2">
                    <strong>Admin Panel:</strong>
                </p>
                <a href="http://{{ $company->domain }}/admin" 
                   class="text-blue-600 hover:text-blue-800 font-medium"
                   target="_blank">
                    Access Admin Panel
                </a>
            </div>
            
            <div class="text-left bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">Next Steps:</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        Check your email for login credentials
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        Login to your admin panel
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        Add your first products
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                        Customize your store settings
                    </li>
                </ul>
            </div>
            
            <div class="space-y-3">
                <a href="http://{{ $company->domain }}/admin" 
                   class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300 inline-block">
                    Go to Admin Panel
                </a>
                
                <a href="http://{{ $company->domain }}" 
                   class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-700 transition duration-300 inline-block">
                    View Your Store
                </a>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Your {{ $company->package->trial_days }}-day free trial has started. 
                    No credit card required until trial ends.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
