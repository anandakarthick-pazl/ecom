<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - Herbal Ecom Complete E-commerce Solution</title>
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
                        'slide-up': 'slideUp 0.8s ease-out',
                        'fade-in': 'fadeIn 1s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
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
    </style>
</head>
<body class="bg-gray-50 font-inter">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md border-b border-gray-200/50 z-50">
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
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Home</a>
                    <a href="{{ route('features') }}" class="text-primary-600 font-semibold">Features</a>
                    <a href="{{ route('pricing') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Pricing</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Contact</a>
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">Login</a>
                    <a href="{{ route('home') }}#register" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2.5 rounded-full hover:from-primary-700 hover:to-primary-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Get Started
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
                <a href="{{ route('home') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Home</a>
                <a href="{{ route('features') }}" class="block px-3 py-2 text-primary-600 font-semibold">Features</a>
                <a href="{{ route('pricing') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Pricing</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Contact</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:text-primary-600">Login</a>
                <a href="{{ route('home') }}#register" class="block mx-3 my-2 bg-primary-600 text-white px-4 py-2 rounded-lg text-center">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-primary-50 via-white to-primary-100 pt-16">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="blob absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-r from-primary-200 to-primary-300 opacity-20 animate-float"></div>
            <div class="blob absolute top-3/4 right-1/4 w-48 h-48 bg-gradient-to-r from-primary-300 to-primary-400 opacity-20 animate-float" style="animation-delay: -2s;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-4 py-2 bg-primary-100 text-primary-800 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-magic mr-2"></i>
                Comprehensive Feature Set
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                Everything You Need to
                <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                    Build & Scale
                </span>
            </h1>
            
            <p class="text-xl lg:text-2xl text-gray-600 mb-8 leading-relaxed max-w-4xl mx-auto">
                From e-commerce basics to advanced analytics, our platform provides all the tools 
                you need to create, manage, and grow a successful online business.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing.index') }}#register" class="bg-gradient-to-r from-primary-600 to-primary-700 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Free Trial
                </a>
                <a href="#features" class="flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-xl text-lg font-semibold hover:border-primary-600 hover:text-primary-600 transition-all duration-200">
                    <i class="fas fa-arrow-down mr-2"></i>
                    Explore Features
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    Powerful Features for
                    <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Every Business Need
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Our comprehensive platform covers every aspect of online business management, 
                    from storefront to backend operations.
                </p>
            </div>
            
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- E-commerce Core -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Complete Online Store</h3>
                    <p class="text-gray-600 mb-6">Full-featured e-commerce platform with everything needed to sell online successfully.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Product catalog management</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Advanced search & filtering</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Shopping cart & wishlist</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Secure checkout process</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Order management system</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Customer account portal</li>
                    </ul>
                </div>
                
                <!-- Inventory Management -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-boxes text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Advanced Inventory Control</h3>
                    <p class="text-gray-600 mb-6">Comprehensive inventory management with real-time tracking and automation.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Real-time stock tracking</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Purchase order management</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Supplier relationship management</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Automated low stock alerts</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Stock adjustment tools</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Goods receipt notes (GRN)</li>
                    </ul>
                </div>
                
                <!-- Point of Sale -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-cash-register text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Integrated POS System</h3>
                    <p class="text-gray-600 mb-6">Complete point-of-sale solution for seamless in-store operations.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>In-store sales processing</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Barcode scanning support</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Receipt printing & email</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Cash drawer integration</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Real-time inventory sync</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Sales reporting & analytics</li>
                    </ul>
                </div>
                
                <!-- Themes & Design -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-palette text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Professional Themes</h3>
                    <p class="text-gray-600 mb-6">Beautiful, industry-specific themes that make your store stand out.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>50+ professional themes</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Mobile-first responsive design</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Industry-specific layouts</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Easy color customization</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Live theme preview</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>SEO-optimized structure</li>
                    </ul>
                </div>
                
                <!-- Analytics & Reports -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Business Intelligence</h3>
                    <p class="text-gray-600 mb-6">Comprehensive analytics and reporting to drive business decisions.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Real-time sales analytics</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Customer behavior insights</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Inventory performance reports</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Financial summaries & P&L</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Custom report builder</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Export to Excel/PDF</li>
                    </ul>
                </div>
                
                <!-- User Management -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Team Collaboration</h3>
                    <p class="text-gray-600 mb-6">Advanced user management with role-based access control.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Role-based permissions</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Staff management system</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Activity logging & tracking</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Secure access control</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Multi-location support</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Department management</li>
                    </ul>
                </div>
                
                <!-- Payment Processing -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-credit-card text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Secure Payments</h3>
                    <p class="text-gray-600 mb-6">Multiple payment options with enterprise-grade security.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Multiple payment gateways</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>PCI-DSS compliant processing</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Subscription billing support</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Automated refund processing</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Split payment options</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Currency conversion</li>
                    </ul>
                </div>
                
                <!-- Customer Support -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">24/7 Expert Support</h3>
                    <p class="text-gray-600 mb-6">Dedicated support team to ensure your success every step of the way.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Live chat support</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Email ticket system</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Phone support (Enterprise)</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Comprehensive knowledge base</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Video tutorials & guides</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Dedicated account manager</li>
                    </ul>
                </div>
                
                <!-- Security & Compliance -->
                <div class="group bg-white p-8 rounded-2xl shadow-lg card-hover border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-r from-gray-600 to-gray-700 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Enterprise Security</h3>
                    <p class="text-gray-600 mb-6">Bank-level security and compliance to protect your business and customers.</p>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>SSL encryption everywhere</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Daily automated backups</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Advanced data protection</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>GDPR compliance tools</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>Two-factor authentication</li>
                        <li class="flex items-center"><i class="fas fa-check text-primary-500 mr-3"></i>SOC 2 Type II certified</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                    See How We
                    <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        Stack Up
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Compare our comprehensive feature set with other solutions in the market.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full bg-white rounded-2xl shadow-lg overflow-hidden">
                    <thead class="bg-gradient-to-r from-primary-600 to-primary-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Feature</th>
                            <th class="px-6 py-4 text-center font-semibold">Herbal Ecom</th>
                            <th class="px-6 py-4 text-center font-semibold">Competitor A</th>
                            <th class="px-6 py-4 text-center font-semibold">Competitor B</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 font-medium">E-commerce Store</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 font-medium">Inventory Management</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">POS System</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 font-medium">Advanced Analytics</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium">Multi-User Support</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 font-medium">24/7 Support</td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-times text-red-500 text-xl"></i></td>
                            <td class="px-6 py-4 text-center"><i class="fas fa-check text-primary-500 text-xl"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6">Ready to Experience These Features?</h2>
            <p class="text-xl mb-8 text-primary-100">
                Join thousands of businesses already using our comprehensive platform to scale their operations.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}#register" 
                   class="bg-white text-primary-700 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-100 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Free Trial
                </a>
                <a href="{{ route('contact') }}" 
                   class="border-2 border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-primary-700 transition-all duration-200">
                    <i class="fas fa-calendar mr-2"></i>
                    Schedule Demo
                </a>
            </div>
            <p class="text-sm text-primary-200 mt-6">
                No credit card required • 14-day free trial • Cancel anytime
            </p>
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
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6">Product</h4>
                    <ul class="space-y-3">
                        <li><a href="{{ route('features') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Features</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Themes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Integrations</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-6">Support</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-200">Contact Us</a></li>
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
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
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

        // Add scroll effect to cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all feature cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>
