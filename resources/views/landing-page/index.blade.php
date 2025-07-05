<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $heroSettings['title'] ?? 'Multi-Tenant E-Commerce Platform' }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .navbar {
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-gradient" href="/">
                <i class="fas fa-crown me-2"></i>EcomPlatform
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#themes">Themes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="#get-started" class="btn btn-outline-primary me-2">Get Started</a>
                    <a href="/super-admin/login" class="btn btn-gradient">Admin Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        {{ $heroSettings['title'] ?? 'Launch Your E-Commerce Empire' }}
                    </h1>
                    <p class="lead mb-4">
                        {{ $heroSettings['subtitle'] ?? 'Create stunning online stores with our multi-tenant e-commerce platform. Choose from 10+ premium themes and start selling in minutes.' }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#get-started" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2"></i>Start Free Trial
                        </a>
                        <a href="#themes" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-eye me-2"></i>View Themes
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="opacity-75">
                            <i class="fas fa-check me-2"></i>15-day free trial
                            <i class="fas fa-check me-2 ms-3"></i>No credit card required
                            <i class="fas fa-check me-2 ms-3"></i>24/7 support
                        </small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-white bg-opacity-25 text-white text-center p-3">
                                <i class="fas fa-store fa-2x mb-2"></i>
                                <h5>Multi-Store</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-white bg-opacity-25 text-white text-center p-3">
                                <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                <h5>Mobile Ready</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-white bg-opacity-25 text-white text-center p-3">
                                <i class="fas fa-palette fa-2x mb-2"></i>
                                <h5>10+ Themes</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-white bg-opacity-25 text-white text-center p-3">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h5>Analytics</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Themes Section -->
    <section id="themes" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient mb-3">Beautiful Themes for Every Business</h2>
                <p class="lead text-muted">Choose from our collection of professionally designed themes</p>
            </div>
            
            <div class="row g-4">
                @forelse($themes as $theme)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-img-top bg-primary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-palette fa-3x text-white"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $theme->name }}</h5>
                            <p class="card-text text-muted">{{ $theme->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">{{ $theme->category_name ?? $theme->category }}</span>
                                @if($theme->is_free)
                                    <span class="text-success fw-bold">Free</span>
                                @else
                                    <span class="text-primary fw-bold">${{ $theme->price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center">
                        <h4>Themes coming soon!</h4>
                        <p class="text-muted">We're preparing amazing themes for your business.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient mb-3">Simple, Transparent Pricing</h2>
                <p class="lead text-muted">Choose the perfect plan for your business needs</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                @forelse($packages as $package)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 {{ $package->is_popular ? 'border-primary' : '' }}">
                        @if($package->is_popular)
                            <div class="card-header bg-primary text-white text-center">
                                <strong>Most Popular</strong>
                            </div>
                        @endif
                        <div class="card-body text-center p-4">
                            <h3 class="mb-3">{{ $package->name }}</h3>
                            <div class="mb-3">
                                <span class="display-4 fw-bold">${{ number_format($package->price) }}</span>
                                <span class="text-muted">/ {{ $package->billing_cycle }}</span>
                            </div>
                            <p class="mb-4">{{ $package->description }}</p>
                            
                            @if($package->features)
                            <ul class="list-unstyled mb-4">
                                @foreach($package->features as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>{{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            
                            <a href="#get-started" class="btn {{ $package->is_popular ? 'btn-primary' : 'btn-outline-primary' }} btn-lg w-100">
                                Start {{ $package->trial_days }}-Day Trial
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center">
                        <h4>Pricing packages coming soon!</h4>
                        <p class="text-muted">We're preparing flexible pricing options for your business.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Get Started Section -->
    <section id="get-started" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="fw-bold text-gradient mb-3">Ready to Get Started?</h2>
                                <p class="lead text-muted">Join thousands of businesses using our platform</p>
                            </div>
                            
                            <div class="text-center">
                                <a href="/super-admin/login" class="btn btn-gradient btn-lg me-3">
                                    <i class="fas fa-user-shield me-2"></i>Super Admin Login
                                </a>
                                <a href="/admin/login" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-user me-2"></i>Company Login
                                </a>
                            </div>
                            
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>Secure platform • 99.9% uptime • 24/7 support
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient mb-3">Get in Touch</h2>
                <p class="lead text-muted">Have questions? We'd love to hear from you.</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row g-4 text-center">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                <h5>Email Us</h5>
                                <p class="text-muted">{{ $contactSettings['email'] ?? 'contact@ecomplatform.com' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                                <h5>Call Us</h5>
                                <p class="text-muted">{{ $contactSettings['phone'] ?? '+1 (555) 123-4567' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                                <h5>Visit Us</h5>
                                <p class="text-muted">{{ $contactSettings['address'] ?? '123 Business St, City, State 12345' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h5 class="text-gradient mb-3">
                        <i class="fas fa-crown me-2"></i>EcomPlatform
                    </h5>
                    <p class="text-light opacity-75">
                        The ultimate multi-tenant e-commerce platform for modern businesses.
                    </p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="text-light opacity-75 mb-0">&copy; {{ date('Y') }} EcomPlatform. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
