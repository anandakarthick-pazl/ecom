<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Herbal Ecom SaaS</title>
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
                    <a href="{{ route('landing.pricing') }}" class="text-gray-700 hover:text-green-600">Pricing</a>
                    <a href="{{ route('landing.contact') }}" class="text-gray-700 hover:text-green-600 font-semibold">Contact</a>
                    <a href="{{ route('landing.index') }}#register" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contact Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Get in Touch</h1>
                <p class="text-xl text-gray-600">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('landing.contact.submit') }}" method="POST">
                        @csrf
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   required>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" rows="5" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                      required>{{ old('message') }}</textarea>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                            Send Message
                        </button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div>
                    <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6">Contact Information</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-envelope text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Email</h4>
                                    <p class="text-gray-600">support@herbalecom.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Phone</h4>
                                    <p class="text-gray-600">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-map-marker-alt text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Address</h4>
                                    <p class="text-gray-600">123 Business Street<br>Tech City, TC 12345</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-clock text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Business Hours</h4>
                                    <p class="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Need Help?</h3>
                        <p class="text-gray-600 mb-4">Check out our frequently asked questions or browse our help documentation.</p>
                        <div class="space-y-3">
                            <a href="#" class="block text-green-600 hover:text-green-700 font-medium">
                                <i class="fas fa-question-circle mr-2"></i> FAQ
                            </a>
                            <a href="#" class="block text-green-600 hover:text-green-700 font-medium">
                                <i class="fas fa-book mr-2"></i> Documentation
                            </a>
                            <a href="#" class="block text-green-600 hover:text-green-700 font-medium">
                                <i class="fas fa-video mr-2"></i> Video Tutorials
                            </a>
                        </div>
                    </div>
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
