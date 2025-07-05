<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ $company->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #2d5016, #6b8e23);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d5016, #6b8e23);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="company-logo">
                                @if($company->logo)
                                    <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                @else
                                    🌿
                                @endif
                            </div>
                            <h3 class="text-primary">{{ $company->name }}</h3>
                            <h5>Admin Login</h5>
                            <p class="text-muted">Sign in to manage your store</p>
                        </div>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ url('/admin/login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required autofocus
                                       placeholder="Enter your email">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="Enter your password">
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to {{ $company->name }}
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure login for {{ $company->name }} administrators
                            </small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="http://{{ $company->domain }}:8000/shop" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Back to Store
                            </a>
                            <span class="mx-2">|</span>
                            <a href="{{ route('login') }}" class="text-muted">
                                <i class="fas fa-users me-1"></i>Other Login Options
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-focus email field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
    </script>
</body>
</html>
