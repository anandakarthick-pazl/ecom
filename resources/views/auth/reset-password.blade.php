<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Reset Password' }} - {{ config('app.name') }}</title>
    
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
            background: linear-gradient(135deg, {{ $isAdmin ? '#1e3c72 0%, #2a5298 100%' : '#667eea 0%, #764ba2 100%' }});
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-password-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, {{ $isAdmin ? '#1e3c72 0%, #2a5298 100%' : '#667eea 0%, #764ba2 100%' }});
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
            border-color: {{ $isAdmin ? '#1e3c72' : '#667eea' }};
            box-shadow: 0 0 0 3px {{ $isAdmin ? 'rgba(30, 60, 114, 0.1)' : 'rgba(102, 126, 234, 0.1)' }};
            background: rgba(255, 255, 255, 0.95);
        }

        .form-floating label {
            color: #718096;
            font-weight: 500;
        }

        .btn-reset {
            background: linear-gradient(135deg, {{ $isAdmin ? '#1e3c72 0%, #2a5298 100%' : '#667eea 0%, #764ba2 100%' }});
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
            box-shadow: 0 10px 25px {{ $isAdmin ? 'rgba(30, 60, 114, 0.3)' : 'rgba(102, 126, 234, 0.3)' }};
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
            color: {{ $isAdmin ? '#1e3c72' : '#667eea' }};
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            color: {{ $isAdmin ? '#2a5298' : '#5a67d8' }};
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

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #cbd5e0;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .form-floating:focus-within .input-icon {
            color: {{ $isAdmin ? '#1e3c72' : '#667eea' }};
        }

        .password-strength {
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .strength-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
            width: 0%;
        }

        .strength-text {
            font-size: 12px;
            font-weight: 500;
        }

        .strength-weak {
            background: #e53e3e;
            color: #c53030;
        }

        .strength-medium {
            background: #ed8936;
            color: #c05621;
        }

        .strength-strong {
            background: #38a169;
            color: #22543d;
        }

        .password-requirements {
            background: rgba(245, 250, 255, 0.8);
            border: 1px solid {{ $isAdmin ? 'rgba(30, 60, 114, 0.2)' : 'rgba(102, 126, 234, 0.2)' }};
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }

        .password-requirements h6 {
            color: {{ $isAdmin ? '#1e3c72' : '#667eea' }};
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            margin-bottom: 6px;
            color: #4a5568;
        }

        .requirement-item i {
            width: 12px;
            transition: color 0.3s ease;
        }

        .requirement-item.valid i {
            color: #38a169;
        }

        .requirement-item.invalid i {
            color: #e53e3e;
        }

        @media (max-width: 480px) {
            .reset-password-container {
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

        .reset-password-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                @if($company)
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                @else
                    <i class="fas fa-{{ $isAdmin ? 'shield-alt' : 'key' }}"></i>
                @endif
            </div>
            <div class="company-name">
                {{ $company ? $company->name : config('app.name') }}
            </div>
            <div class="subtitle">{{ $isAdmin ? 'Administration Panel' : 'E-commerce Platform' }}</div>
            @if($isAdmin)
            <div class="admin-badge">
                <i class="fas fa-user-shield"></i>
                Admin Reset
            </div>
            @endif
        </div>

        <!-- Form Header -->
        <div class="form-header">
            <h2>
                <i class="fas fa-lock-open me-2" style="color: {{ $isAdmin ? '#1e3c72' : '#667eea' }};"></i>
                Reset Password
            </h2>
            <p>Create a new secure password for your {{ $isAdmin ? 'admin ' : '' }}account.</p>
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

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            @if($isAdmin)
                <input type="hidden" name="admin" value="1">
            @endif
            
            <div class="form-floating position-relative">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Enter your email address" value="{{ old('email') }}" required>
                <label for="email">Email Address</label>
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <div class="form-floating position-relative">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Enter new password" required>
                <label for="password">New Password</label>
                <i class="fas fa-eye input-icon" id="togglePassword" onclick="togglePasswordVisibility('password')"></i>
            </div>

            <!-- Password Strength Indicator -->
            <div class="password-strength" id="passwordStrength" style="display: none;">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <div class="strength-text" id="strengthText"></div>
            </div>

            <div class="form-floating position-relative">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                       placeholder="Confirm new password" required>
                <label for="password_confirmation">Confirm New Password</label>
                <i class="fas fa-eye input-icon" id="togglePasswordConfirm" onclick="togglePasswordVisibility('password_confirmation')"></i>
            </div>

            <button type="submit" class="btn btn-reset" id="resetBtn">
                <span class="btn-text">
                    <i class="fas fa-check me-2"></i>Reset Password
                </span>
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </form>

        <!-- Password Requirements -->
        <div class="password-requirements">
            <h6><i class="fas fa-info-circle me-2"></i>Password Requirements</h6>
            <div class="requirement-item" id="req-length">
                <i class="fas fa-times"></i>
                <span>At least 8 characters</span>
            </div>
            <div class="requirement-item" id="req-uppercase">
                <i class="fas fa-times"></i>
                <span>One uppercase letter</span>
            </div>
            <div class="requirement-item" id="req-lowercase">
                <i class="fas fa-times"></i>
                <span>One lowercase letter</span>
            </div>
            <div class="requirement-item" id="req-number">
                <i class="fas fa-times"></i>
                <span>One number</span>
            </div>
        </div>

        <!-- Back to Login -->
        <div class="back-to-login">
            <a href="{{ $isAdmin ? route('admin.login.form') : route('login') }}">
                <i class="fas fa-arrow-left"></i>
                Back to {{ $isAdmin ? 'Admin ' : '' }}Login
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const toggleIcon = inputId === 'password' ? document.getElementById('togglePassword') : document.getElementById('togglePasswordConfirm');
            
            if (input.type === 'password') {
                input.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                strengthIndicator.style.display = 'none';
                return;
            }
            
            strengthIndicator.style.display = 'block';
            
            const strength = calculatePasswordStrength(password);
            updatePasswordRequirements(password);
            
            strengthFill.style.width = strength.percentage + '%';
            strengthFill.className = 'strength-fill strength-' + strength.level;
            strengthText.textContent = 'Password Strength: ' + strength.text;
            strengthText.className = 'strength-text strength-' + strength.level;
        });

        function calculatePasswordStrength(password) {
            let score = 0;
            
            // Length check
            if (password.length >= 8) score += 25;
            
            // Uppercase check
            if (/[A-Z]/.test(password)) score += 25;
            
            // Lowercase check
            if (/[a-z]/.test(password)) score += 25;
            
            // Number check
            if (/[0-9]/.test(password)) score += 25;
            
            let level, text;
            if (score < 50) {
                level = 'weak';
                text = 'Weak';
            } else if (score < 75) {
                level = 'medium';
                text = 'Medium';
            } else {
                level = 'strong';
                text = 'Strong';
            }
            
            return { percentage: score, level: level, text: text };
        }

        function updatePasswordRequirements(password) {
            const requirements = {
                'req-length': password.length >= 8,
                'req-uppercase': /[A-Z]/.test(password),
                'req-lowercase': /[a-z]/.test(password),
                'req-number': /[0-9]/.test(password)
            };
            
            Object.keys(requirements).forEach(reqId => {
                const element = document.getElementById(reqId);
                const icon = element.querySelector('i');
                
                if (requirements[reqId]) {
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                } else {
                    element.classList.add('invalid');
                    element.classList.remove('valid');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                }
            });
        }

        // Form submission
        document.getElementById('resetPasswordForm').addEventListener('submit', function() {
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
