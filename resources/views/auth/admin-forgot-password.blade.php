<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin - Forgot Password' }} - {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .admin-forgot-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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

        .admin-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            gap: 6px;
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
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        .form-floating label {
            color: #718096;
            font-weight: 500;
        }

        .btn-admin-reset {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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

        .btn-admin-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 60, 114, 0.3);
        }

        .btn-admin-reset:active {
            transform: translateY(0);
        }

        .btn-admin-reset .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-admin-reset .spinner-border {
            width: 20px;
            height: 20px;
            display: none;
        }

        .btn-admin-reset.loading .btn-text {
            opacity: 0;
        }

        .btn-admin-reset.loading .spinner-border {
            display: inline-block;
        }

        .back-to-login {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-login a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            color: #2a5298;
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
            color: #1e3c72;
        }

        .admin-security-note {
            background: rgba(30, 60, 114, 0.05);
            border: 1px solid rgba(30, 60, 114, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
            text-align: center;
        }

        .admin-security-note i {
            color: #1e3c72;
            margin-bottom: 8px;
        }

        .admin-security-note p {
            color: #4a5568;
            font-size: 13px;
            margin: 0;
            line-height: 1.4;
        }

        .admin-privileges {
            background: rgba(255, 248, 240, 0.8);
            border: 1px solid rgba(237, 137, 54, 0.2);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-privileges i {
            color: #ed8936;
        }

        .admin-privileges p {
            color: #744210;
            font-size: 13px;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .admin-forgot-container {
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

        .admin-forgot-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="admin-forgot-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                @if($company)
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                @else
                    <i class="fas fa-shield-alt"></i>
                @endif
            </div>
            <div class="company-name">
                {{ $company ? $company->name : config('app.name') }}
            </div>
            <div class="subtitle">Administration Panel</div>
            <div class="admin-badge">
                <i class="fas fa-user-shield"></i>
                Admin Access
            </div>
        </div>

        <!-- Form Header -->
        <div class="form-header">
            <h2><i class="fas fa-key me-2" style="color: #1e3c72;"></i>Admin Password Reset</h2>
            <p>Enter your admin email address to receive a secure password reset link.</p>
        </div>

        <!-- Admin Privileges Notice -->
        <div class="admin-privileges">
            <i class="fas fa-exclamation-triangle"></i>
            <p>This reset is for admin accounts only. Requires admin privileges.</p>
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
        <form method="POST" action="{{ route('admin.password.email') }}" id="adminForgotPasswordForm">
            @csrf
            
            <div class="form-floating position-relative">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your admin email address" value="{{ old('email') }}" required>
                <label for="email">Admin Email Address</label>
                <i class="fas fa-envelope email-icon"></i>
            </div>

            <button type="submit" class="btn btn-admin-reset" id="adminResetBtn">
                <span class="btn-text">
                    <i class="fas fa-shield-alt me-2"></i>Send Admin Reset Link
                </span>
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </form>

        <!-- Security Note -->
        <div class="admin-security-note">
            <i class="fas fa-shield-alt fa-lg"></i>
            <p><strong>Enhanced Security:</strong> Admin password reset links are valid for 24 hours and include additional security verification.</p>
        </div>

        <!-- Back to Login -->
        <div class="back-to-login">
            <a href="{{ route('admin.login.form') }}">
                <i class="fas fa-arrow-left"></i>
                Back to Admin Login
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('adminForgotPasswordForm').addEventListener('submit', function() {
            const button = document.getElementById('adminResetBtn');
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
