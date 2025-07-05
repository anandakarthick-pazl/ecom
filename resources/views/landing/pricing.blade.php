<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Herbal Ecom SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-green-600">Herbal Ecom</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('landing.index') }}" class="text-gray-700 hover:text-green-600">Home</a>
                    <a href="{{ route('landing.features') }}" class="text-gray-700 hover:text-green-600">Features</a>
                    <a href="{{ route('landing.pricing') }}" class="text-gray-700 hover:text-green-600 font-semibold">Pricing</a>
                    <a href="{{ route('landing.contact') }}" class="text-gray-700 hover:text-green-600">Contact</a>
                    <a href="{{ route('landing.index') }}#register" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Pricing Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h1>
                <p class="text-xl text-gray-600">Choose the perfect plan for your business needs</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($packages as $package)
                <div class="bg-white rounded-lg shadow-md p-8 {{ $package->is_popular ? 'ring-2 ring-green-500 relative' : '' }}">
                    @if($package->is_popular)
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <div class="bg-green-500 text-white text-sm font-semibold px-3 py-1 rounded-full">
                            Most Popular
                        </div>
                    </div>
                    @endif
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $package->name }}</h3>
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        ${{ number_format($package->price, 0) }}
                        <span class="text-lg text-gray-600">/{{ $package->billing_cycle }}</span>
                    </div>
                    <p class="text-gray-600 mb-6">{{ $package->trial_days }} days free trial</p>
                    
                    <p class="text-gray-700 mb-6">{{ $package->description }}</p>
                    
                    <ul class="space-y-3 mb-8">
                        @if($package->features)
                            @foreach($package->features as $feature)
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-3"></i>
                                {{ $feature }}
                            </li>
                            @endforeach
                        @endif
                    </ul>
                    
                    <a href="{{ route('landing.index') }}#register" 
                       class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300 text-center block">
                        Start Free Trial
                    </a>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">All plans include:</p>
                <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-600">
                    <span class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Free SSL Certificate</span>
                    <span class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> 99.9% Uptime Guarantee</span>
                    <span class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> 24/7 Support</span>
                    <span class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Money Back Guarantee</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold text-green-400 mb-4">Herbal Ecom</h3>
            <p class="text-gray-400">Complete ecommerce solution for your business</p>
        </div>
    </footer>
</body>
</html>
