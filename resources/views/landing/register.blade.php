<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbal Ecom - Complete E-commerce Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'fade-in': 'fadeIn 1s ease-out',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(16, 185, 129, 0.3)' },
                            '100%': { boxShadow: '0 0 30px rgba(16, 185, 129, 0.6)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(50px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    },
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob 7s infinite;
        }
        @keyframes blob {
            0% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
            100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        }
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
            transform: scaleX(0);
            transform-origin: left;
            z-index: 1000;
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50 font-inter">
    <!-- Scroll Indicator -->
    <div class="scroll-indicator" id="scrollIndicator"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-gray-200/50 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-leaf text-white text-lg"></i>
                    </div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Herbal Ecom
                    </h1>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('features') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Features</a>
                    <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Pricing</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Contact</a>
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Login</a>
                    <a href="#register" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2.5 rounded-full hover:from-primary-700 hover:to-primary-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Start Free Trial
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-gray-700 hover:text-primary-600" id="mobileMenuBtn">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden bg-white border-t border-gray-200 hidden" id="mobileMenu">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="{{ route('features') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Features</a>
                <a href="{{ route('pricing') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Pricing</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Contact</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Login</a>
                <a href="#register" class="block mx-3 my-2 bg-primary-600 text-white px-4 py-2 rounded-lg text-center">Start Free Trial</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-100">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="blob absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-r from-primary-200 to-primary-300 opacity-20 animate-float"></div>
            <div class="blob absolute top-3/4 right-1/4 w-48 h-48 bg-gradient-to-r from-primary-300 to-primary-400 opacity-20 animate-float" style="animation-delay: -2s;"></div>
            <div class="blob absolute top-1/2 right-1/3 w-32 h-32 bg-gradient-to-r from-primary-400 to-primary-500 opacity-20 animate-float" style="animation-delay: -4s;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Content -->
                <div class="text-center lg:text-left animate-slide-up">
                    <div class="inline-flex items-center px-4 py-2 bg-primary-100 text-primary-800 rounded-full text-sm font-medium mb-6">
                        <i class="fas fa-rocket mr-2"></i>
                        New: AI-Powered Analytics Dashboard
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                        Launch Your
                        <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                            Dream Store
                        </span>
                        in Minutes
                    </h1>
                    
                    <p class="text-xl lg:text-2xl text-gray-600 mb-8 leading-relaxed">
                        Complete e-commerce platform with inventory management, POS system, and stunning themes. 
                        Start selling online today with zero setup fees.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="#register" class="group relative bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 animate-glow">
                            <i class="fas fa-play mr-2"></i>
                            Start Free Trial
                            <span class="absolute inset-0 bg-white opacity-20 rounded-xl transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                        </a>
                        <a href="#features" class="group flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-xl text-lg font-semibold hover:border-primary-600 hover:text-primary-600 transition-all duration-200">
                            <i class="fas fa-play-circle mr-2"></i>
                            View Features
                        </a>
                    </div>
                    
                    <div class="flex items-center gap-8 text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-primary-500 mr-2"></i>
                            14-day free trial
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-primary-500 mr-2"></i>
                            No credit card required
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-primary-500 mr-2"></i>
                            Cancel anytime
                        </div>
                    </div>
                </div>

                <!-- Hero Image/Dashboard Preview -->
                <div class="relative animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="relative z-10">
                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform rotate-3 hover:rotate-0 transition-transform duration-500">
                            <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                    <div class="flex-1 text-center text-white font-medium">Dashboard</div>
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-primary-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-primary-700">$12.5K</div>
                                        <div class="text-sm text-gray-600">Revenue</div>
                                    </div>
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-blue-700">342</div>
                                        <div class="text-sm text-gray-600">Orders</div>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg">
                                        <div class="text-2xl font-bold text-purple-700">1.2K</div>
                                        <div class="text-sm text-gray-600">Customers</div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 h-32 rounded-lg flex items-center justify-center">
                                    <div class="text-gray-400">
                                        <i class="fas fa-chart-line text-4xl"></i>
                                        <div class="text-sm mt-2">Analytics Chart</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating elements -->
                    <div class="absolute -top-8 -right-8 w-16 h-16 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center animate-bounce-slow">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <div class="absolute -bottom-8 -left-8 w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center animate-float">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 border-2 border-gray-400 rounded-full flex justify-center">
                <div class="w-1 h-3 bg-gray-400 rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2" data-count="10000">0</div>
                    <div class="text-gray-600 font-medium">Active Stores</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2" data-count="50000">0</div>
                    <div class="text-gray-600 font-medium">Products Sold</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2" data-count="99">0</div>
                    <div class="text-gray-600 font-medium">Uptime %</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl lg:text-5xl font-bold text-primary-600 mb-2" data-count="24">0</div>
                    <div class="text-gray-600 font-medium">Support Hours</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-primary-100 text-primary-800 rounded-full text-sm font-medium mb-6">
                    <i class="fas fa-magic mr-2"></i>
                    Powerful Features
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Everything You Need to
                    <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Succeed Online
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From product management to customer analytics, we've got all the tools you need to build and grow your online business.
                </p>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Complete E-commerce</h3>
                    <p class="text-gray-600 mb-6">Full-featured online store with shopping cart, secure checkout, order management, and customer accounts.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Product catalog</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Secure payments</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Order tracking</li>
                    </ul>
                </div>
                
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-boxes text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Inventory Management</h3>
                    <p class="text-gray-600 mb-6">Advanced inventory tracking with supplier management, purchase orders, and automated stock alerts.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Stock tracking</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Supplier management</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Low stock alerts</li>
                    </ul>
                </div>
                
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-cash-register text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Point of Sale</h3>
                    <p class="text-gray-600 mb-6">Built-in POS system for in-store sales with real-time inventory synchronization and receipt printing.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>In-store sales</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Receipt printing</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Inventory sync</li>
                    </ul>
                </div>
                
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-palette text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Beautiful Themes</h3>
                    <p class="text-gray-600 mb-6">Choose from professionally designed themes optimized for different industries and customize to match your brand.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>50+ themes</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Mobile responsive</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Easy customization</li>
                    </ul>
                </div>
                
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Analytics & Reports</h3>
                    <p class="text-gray-600 mb-6">Comprehensive analytics dashboard with sales reports, customer insights, and performance metrics.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Sales analytics</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Customer insights</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Export reports</li>
                    </ul>
                </div>
                
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">24/7 Support</h3>
                    <p class="text-gray-600 mb-6">Dedicated support team available around the clock to help you succeed with live chat, email, and phone support.</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Live chat</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Email support</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-2"></i>Phone support</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 bg-primary-100 text-primary-800 rounded-full text-sm font-medium mb-6">
                    <i class="fas fa-tag mr-2"></i>
                    Simple Pricing
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Choose the Perfect Plan for
                    <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Your Business
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Start with our free trial and scale as you grow. All plans include 24/7 support and a 30-day money-back guarantee.
                </p>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8">
                @if(isset($packages) && count($packages) > 0)
                    @foreach($packages as $package)
                    <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 card-hover {{ $package->is_popular ?? false ? 'border-primary-500 transform scale-105' : '' }}">
                        @if($package->is_popular ?? false)
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-full text-sm font-semibold">
                                Most Popular
                            </span>
                        </div>
                        @endif
                        
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                            <div class="text-5xl font-bold text-gray-900 mb-2">
                                ${{ number_format($package->price, 0) }}
                                <span class="text-lg text-gray-600">/{{ $package->billing_cycle }}</span>
                            </div>
                            <p class="text-gray-600">{{ $package->trial_days }} days free trial</p>
                        </div>
                        
                        <ul class="space-y-4 mb-8">
                            @if($package->features)
                                @foreach($package->features as $feature)
                                <li class="flex items-center">
                                    <i class="fas fa-check text-primary-500 mr-3"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            @endif
                        </ul>
                        
                        <a href="#register" 
                           class="w-full {{ $package->is_popular ?? false ? 'bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 shadow-lg' : 'bg-primary-600 hover:bg-primary-700' }} text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 text-center block"
                           data-package="{{ $package->id }}">
                            Start Free Trial
                        </a>
                    </div>
                    @endforeach
                @else
                    <!-- Default pricing if no packages -->
                    <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 card-hover">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                            <div class="text-5xl font-bold text-gray-900 mb-2">
                                $29<span class="text-lg text-gray-600">/month</span>
                            </div>
                            <p class="text-gray-600">Perfect for small businesses</p>
                        </div>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Up to 100 products</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Basic themes</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Email support</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>SSL certificate</li>
                        </ul>
                        
                        <button class="w-full bg-primary-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200">
                            Start Free Trial
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl border-2 border-primary-500 p-8 card-hover relative transform scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-full text-sm font-semibold">
                                Most Popular
                            </span>
                        </div>
                        
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                            <div class="text-5xl font-bold text-gray-900 mb-2">
                                $79<span class="text-lg text-gray-600">/month</span>
                            </div>
                            <p class="text-gray-600">Best for growing businesses</p>
                        </div>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Unlimited products</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Premium themes</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Priority support</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Advanced analytics</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Inventory management</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>POS system</li>
                        </ul>
                        
                        <button class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg">
                            Start Free Trial
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 card-hover">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                            <div class="text-5xl font-bold text-gray-900 mb-2">
                                $199<span class="text-lg text-gray-600">/month</span>
                            </div>
                            <p class="text-gray-600">For large enterprises</p>
                        </div>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Everything in Professional</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>AI-powered insights</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>24/7 phone support</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Custom integrations</li>
                            <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Dedicated account manager</li>
                        </ul>
                        
                        <button class="w-full bg-primary-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200">
                            Contact Sales
                        </button>
                    </div>
                @endif
            </div>

            <!-- Money Back Guarantee -->
            <div class="text-center mt-12">
                <div class="inline-flex items-center px-6 py-3 bg-green-50 text-green-800 rounded-full">
                    <i class="fas fa-shield-alt mr-2"></i>
                    30-day money-back guarantee on all plans
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section id="register" class="py-20 bg-gradient-to-br from-primary-50 to-primary-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Ready to Start Your
                    <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Success Story?
                    </span>
                </h2>
                <p class="text-xl text-gray-600">Join thousands of entrepreneurs who chose Herbal Ecom to build their online empire</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('company.register') }}" method="POST" class="bg-white rounded-2xl shadow-2xl p-8 lg:p-12">
                @csrf
                
                <!-- Company Information -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-building text-primary-600 mr-3"></i>
                        Company Information
                    </h3>
                    
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="Acme Corporation" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Store URL</label>
                            <div class="flex">
                                <input type="text" name="company_slug" value="{{ old('company_slug') }}" 
                                       class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-l-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                       placeholder="mystore" required>
                                <div class="px-4 py-3 bg-gray-100 border-2 border-l-0 border-gray-200 rounded-r-xl text-gray-600 flex items-center">
                                    .yourdomain.com
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="+1 (555) 123-4567">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="New York">
                        </div>
                    </div>
                </div>

                <!-- Admin Account -->
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user-shield text-primary-600 mr-3"></i>
                        Admin Account
                    </h3>
                    
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Full Name</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="John Doe" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Email Address</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="john@acme.com" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Password</label>
                            <input type="password" name="admin_password" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="••••••••" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Confirm Password</label>
                            <input type="password" name="admin_password_confirmation" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200" 
                                   placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <!-- Package Selection -->
                @if(isset($packages) && count($packages) > 0)
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-box text-primary-600 mr-3"></i>
                        Select Package
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        @foreach($packages as $package)
                        <label class="cursor-pointer">
                            <input type="radio" name="package_id" value="{{ $package->id }}" 
                                   class="sr-only" {{ old('package_id') == $package->id ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-primary-500 transition package-option">
                                <h4 class="font-semibold">{{ $package->name }}</h4>
                                <p class="text-2xl font-bold text-primary-600">${{ number_format($package->price, 0) }}</p>
                                <p class="text-sm text-gray-600">{{ $package->trial_days }} days free</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Theme Selection -->
                @if(isset($themes) && count($themes) > 0)
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-palette text-primary-600 mr-3"></i>
                        Choose Theme
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        @foreach($themes as $theme)
                        <label class="cursor-pointer">
                            <input type="radio" name="theme_id" value="{{ $theme->id }}" 
                                   class="sr-only" {{ old('theme_id') == $theme->id ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-primary-500 transition theme-option">
                                @if($theme->preview_image)
                                <img src="{{ asset('storage/' . $theme->preview_image) }}" alt="{{ $theme->name }}" class="w-full h-32 object-cover rounded mb-2">
                                @endif
                                <h4 class="font-semibold">{{ $theme->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $theme->description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Terms -->
                <div class="mb-8">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" value="1" class="mt-1 mr-4 w-5 h-5 text-primary-600 border-2 border-gray-300 rounded focus:ring-primary-500" required>
                        <span class="text-gray-700">
                            I agree to the <a href="#" class="text-primary-600 hover:underline font-semibold">Terms of Service</a> 
                            and <a href="#" class="text-primary-600 hover:underline font-semibold">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-4 px-8 rounded-xl text-xl font-bold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                    <i class="fas fa-rocket mr-3"></i>
                    Start My Free Trial
                </button>
                
                <p class="text-center text-gray-600 mt-6">
                    No credit card required • 14-day free trial • Cancel anytime
                </p>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-8 mb-12">
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold">Herbal Ecom</h3>
                    </div>
                    <p class="text-gray-400 text-lg mb-6 max-w-md">
                        The complete e-commerce solution that grows with your business. From startup to enterprise, we've got you covered.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-200">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-200">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('features') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Features</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Themes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Integrations</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">API</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">System Status</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Community</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Training</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 mb-4 md:mb-0">
                        © 2025 Herbal Ecom. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white/90');
                navbar.classList.remove('bg-white/80');
            } else {
                navbar.classList.add('bg-white/80');
                navbar.classList.remove('bg-white/90');
            }

            // Update scroll indicator
            const scrollIndicator = document.getElementById('scrollIndicator');
            const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            scrollIndicator.style.transform = `scaleX(${scrollPercent / 100})`;
        });

        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animated counters
        function animateCounters() {
            const counters = document.querySelectorAll('[data-count]');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString();
                    }
                }, 16);
            });
        }

        // Trigger counter animation when section comes into view
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('[data-count]')?.closest('section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Form enhancements
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('transform', 'scale-[1.02]');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('transform', 'scale-[1.02]');
            });
        });

        // Package and theme selection
        document.querySelectorAll('input[name="package_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.package-option').forEach(option => {
                    option.classList.remove('border-primary-500', 'bg-primary-50');
                    option.classList.add('border-gray-200');
                });
                if (this.checked) {
                    const option = this.parentElement.querySelector('.package-option');
                    option.classList.remove('border-gray-200');
                    option.classList.add('border-primary-500', 'bg-primary-50');
                }
            });
        });

        document.querySelectorAll('input[name="theme_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.theme-option').forEach(option => {
                    option.classList.remove('border-primary-500', 'bg-primary-50');
                    option.classList.add('border-gray-200');
                });
                if (this.checked) {
                    const option = this.parentElement.querySelector('.theme-option');
                    option.classList.remove('border-gray-200');
                    option.classList.add('border-primary-500', 'bg-primary-50');
                }
            });
        });

        // Auto-generate slug from company name
        document.querySelector('input[name="company_name"]')?.addEventListener('input', function(e) {
            const slug = e.target.value.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            const slugInput = document.querySelector('input[name="company_slug"]');
            if (slugInput) {
                slugInput.value = slug;
            }
        });

        // Parallax effect for hero background elements
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const blobs = document.querySelectorAll('.blob');
            
            blobs.forEach((blob, index) => {
                const speed = (index + 1) * 0.5;
                blob.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    </script>
</body>
</html>
