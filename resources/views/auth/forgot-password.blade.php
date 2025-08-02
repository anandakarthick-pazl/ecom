<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Forgot Password' }} - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-password-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #718096;
            font-size: 14px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #1a202c;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #718096;
            font-size: 15px;
            line-height: 1.5;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-floating .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-floating label {
            color: #718096;
            font-weight: 500;
        }

        .btn-reset {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        .btn-reset .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-reset .spinner-border {
            width: 20px;
            height: 20px;
            display: none;
        }

        .btn-reset.loading .btn-text {
            opacity: 0;
        }

        .btn-reset.loading .spinner-border {
            display: inline-block;
        }

        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
            padding: 16px 20px;
        }

        .alert-danger {
            background: rgba(254, 226, 226, 0.8);
            color: #c53030;
            border-left: 4px solid #e53e3e;
        }

        .alert-success {
            background: rgba(240, 253, 244, 0.8);
            color: #22543d;
            border-left: 4px solid #38a169;
        }

        .email-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #cbd5e0;
            transition: color 0.3s ease;
        }

        .form-floating:focus-within .email-icon {
            color: #667eea;
        }

        .security-note {
            background: rgba(245, 250, 255, 0.8);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            text-align: center;
        }

        .security-note i {
            color: #667eea;
            margin-bottom: 8px;
        }

        .security-note p {
            color: #4a5568;
            font-size: 13px;
            margin: 0;
            line-height: 1.4;
        }

        @media (max-width: 480px) {
            .forgot-password-container {
                padding: 30px 25px;
                margin: 10px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
            
            .company-name {
                font-size: 20px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .forgot-password-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                @if($company)
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                @else
                    <i class="fas fa-store"></i>
                @endif
            </div>
            <div class="company-name">
                {{ $company ? $company->name : config('app.name') }}
            </div>
            <div class="subtitle">E-commerce Platform</div>
        </div>

        <!-- Form Header -->
        <div class="form-header">
            <h2><i class="fas fa-key me-2" style="color: #667eea;"></i>Forgot Password</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <!-- Alert Messages -->
        @if (session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <!-- Forgot Password Form -->
        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf
            
            <div class="form-floating position-relative">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your email address" value="{{ old('email') }}" required>
                <label for="email">Email Address</label>
                <i class="fas fa-envelope email-icon"></i>
            </div>

            <button type="submit" class="btn btn-reset" id="resetBtn">
                <span class="btn-text">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                </span>
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </form>

        <!-- Security Note -->
        <div class="security-note">
            <i class="fas fa-shield-alt fa-lg"></i>
            <p>We'll email you a secure link to reset your password. The link will expire in 24 hours for your security.</p>
        </div>

        <!-- Back to Login -->
        <div class="back-to-login">
            <a href="{{ $company ? route('admin.login.form') : route('login') }}">
                <i class="fas fa-arrow-left"></i>
                Back to Login
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
            const button = document.getElementById('resetBtn');
            button.classList.add('loading');
            button.disabled = true;
            
            // Re-enable button after 5 seconds to prevent permanent disabled state
            setTimeout(() => {
                button.classList.remove('loading');
                button.disabled = false;
            }, 5000);
        });

        // Auto focus on email input
        document.getElementById('email').focus();
    </script>
</body>
</html>
