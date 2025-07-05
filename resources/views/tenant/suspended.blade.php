<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended - {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-red-500 text-6xl mb-6">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                Account Suspended
            </h1>
            
            <p class="text-gray-600 mb-6">
                The account for <strong>{{ $company->name }}</strong> has been suspended.
            </p>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-red-800 mb-2">Reason for Suspension:</h3>
                <p class="text-sm text-red-700">
                    @if($company->trial_ends_at && $company->trial_ends_at->isPast() && (!$company->subscription_ends_at || $company->subscription_ends_at->isPast()))
                        Your trial period has expired and no active subscription was found.
                    @elseif($company->subscription_ends_at && $company->subscription_ends_at->isPast())
                        Your subscription has expired.
                    @else
                        Account has been suspended by administrator.
                    @endif
                </p>
            </div>
            
            <div class="text-left bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-3">To Reactivate Your Account:</h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-phone text-blue-600 mr-2 mt-1"></i>
                        Contact our support team
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-credit-card text-blue-600 mr-2 mt-1"></i>
                        Update your billing information
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-refresh text-blue-600 mr-2 mt-1"></i>
                        Renew your subscription
                    </li>
                </ul>
            </div>
            
            <div class="space-y-3">
                <a href="mailto:support@yourdomain.com" 
                   class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 inline-block">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Support
                </a>
                
                <a href="tel:+1234567890" 
                   class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300 inline-block">
                    <i class="fas fa-phone mr-2"></i>
                    Call Support
                </a>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Account ID: {{ $company->id }} | 
                    @if($company->trial_ends_at)
                        Trial ended: {{ $company->trial_ends_at->format('M d, Y') }}
                    @endif
                    @if($company->subscription_ends_at)
                        Subscription ended: {{ $company->subscription_ends_at->format('M d, Y') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</body>
</html>
