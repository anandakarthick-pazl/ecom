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
        .login-modal {
            backdrop-filter: blur(10px);
            background: rgba(0, 0, 0, 0.5);
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
                    
                    <!-- Login Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center text-gray-700 hover:text-primary-600 transition-colors duration-200 font-medium">
                            Login
                            <i class="fas fa-chevron-down ml-1 text-sm"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <div class="p-4">
                                <div class="space-y-3">
                                    <button onclick="openLoginModal('super_admin')" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200 group">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-crown text-white"></i>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-semibold text-gray-900">Super Admin</div>
                                            <div class="text-sm text-gray-600">Platform Management</div>
                                        </div>
                                        <i class="fas fa-arrow-right ml-auto text-gray-400 group-hover:text-primary-600"></i>
                                    </button>
                                    
                                    <button onclick="openLoginModal('admin')" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200 group">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-user-tie text-white"></i>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-semibold text-gray-900">Admin</div>
                                            <div class="text-sm text-gray-600">Store Management</div>
                                        </div>
                                        <i class="fas fa-arrow-right ml-auto text-gray-400 group-hover:text-primary-600"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
                <div class="border-t border-gray-200 my-2"></div>
                <button onclick="openLoginModal('super_admin')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-primary-600">Super Admin Login</button>
                <button onclick="openLoginModal('admin')" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-primary-600">Admin Login</button>
                <a href="#register" class="block mx-3 my-2 bg-primary-600 text-white px-4 py-2 rounded-lg text-center">Start Free Trial</a>
            </div>
        </div>
    </nav>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 z-50 hidden login-modal">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div id="modalIcon" class="w-10 h-10 rounded-lg flex items-center justify-center">
                                <!-- Icon will be set by JavaScript -->
                            </div>
                            <div>
                                <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
                                <p id="modalSubtitle" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                        <button onclick="closeLoginModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" action="{{ route('login.post') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" id="loginType" name="login_type" value="">
                        
                        <!-- Company Selection (for Admin) -->
                        <div id="companySelection" class="hidden">
                            <label for="company_slug" class="block text-sm font-semibold text-gray-700 mb-2">
                                Select Company Domain
                            </label>
                            <select name="company_slug" id="company_slug" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200">
                                <option value="">Choose your company...</option>
                                @if(isset($companies) && count($companies) > 0)
                                    @foreach($companies as $company)
                                        <option value="{{ $company->slug }}">
                                            {{ $company->name }} ({{ $company->slug }}.yourdomain.com)
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="email" name="email" required
                                       class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200"
                                       placeholder="Enter your email">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" required
                                       class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200"
                                       placeholder="Enter your password">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-2 border-gray-300 rounded focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 px-6 rounded-xl text-lg font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </form>

                    <!-- Quick Links -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have a store yet?
                            <a href="#register" onclick="closeLoginModal()" class="text-primary-600 hover:text-primary-800 font-semibold">
                                Create your store now
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <button onclick="openLoginModal('admin')" class="group flex items-center justify-center px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-xl text-lg font-semibold hover:border-primary-600 hover:text-primary-600 transition-all duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login to Your Store
                        </button>
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

    <!-- Quick Access Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Quick Access</h2>
                <p class="text-gray-600">Choose your access level to get started</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <!-- Super Admin Access -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 text-center card-hover border border-purple-200">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-crown text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Super Admin</h3>
                    <p class="text-gray-600 mb-6">Manage the entire platform, companies, billing, and system settings.</p>
                    <button onclick="openLoginModal('super_admin')" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200">
                        Access Platform
                    </button>
                </div>

                <!-- Admin Access -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 text-center card-hover border border-blue-200">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Store Admin</h3>
                    <p class="text-gray-600 mb-6">Manage your store, products, orders, and customers with full control.</p>
                    <button onclick="openLoginModal('admin')" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                        Access Store
                    </button>
                </div>

                <!-- New Store -->
                <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl p-8 text-center card-hover border border-primary-200">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Create New Store</h3>
                    <p class="text-gray-600 mb-6">Start your e-commerce journey with our 14-day free trial.</p>
                    <a href="#register" class="w-full inline-block bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 px-6 rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 transition-all duration-200">
                        Start Free Trial
                    </a>
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
        // Login Modal Functions
        function openLoginModal(type) {
            const modal = document.getElementById('loginModal');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalSubtitle = document.getElementById('modalSubtitle');
            const loginType = document.getElementById('loginType');
            const companySelection = document.getElementById('companySelection');
            const submitBtn = document.getElementById('submitBtn');
            
            modal.classList.remove('hidden');
            loginType.value = type;
            
            if (type === 'super_admin') {
                modalIcon.className = 'w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center';
                modalIcon.innerHTML = '<i class="fas fa-crown text-white"></i>';
                modalTitle.textContent = 'Super Admin Login';
                modalSubtitle.textContent = 'Platform Management Access';
                companySelection.classList.add('hidden');
                submitBtn.innerHTML = '<i class="fas fa-crown mr-2"></i>Access Platform';
                submitBtn.className = 'w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 px-6 rounded-xl text-lg font-semibold hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl';
            } else {
                modalIcon.className = 'w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center';
                modalIcon.innerHTML = '<i class="fas fa-user-tie text-white"></i>';
                modalTitle.textContent = 'Admin Login';
                modalSubtitle.textContent = 'Store Management Access';
                companySelection.classList.remove('hidden');
                submitBtn.innerHTML = '<i class="fas fa-store mr-2"></i>Access Store';
                submitBtn.className = 'w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl text-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl';
            }
            
            // Focus first input
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 100);
        }
        
        function closeLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.add('hidden');
            
            // Reset form
            document.getElementById('loginForm').reset();
        }
        
        // Close modal when clicking outside
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoginModal();
            }
        });

        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginType = document.getElementById('loginType').value;
            const companySlug = document.getElementById('company_slug');
            
            if (loginType === 'admin' && !companySlug.value) {
                e.preventDefault();
                alert('Please select a company domain for admin login');
                companySlug.focus();
                return;
            }
        });

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
