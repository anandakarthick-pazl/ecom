<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo e($company->name); ?></title>
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
<body class="bg-gradient-to-br from-primary-50 via-white to-primary-100 min-h-screen font-inter">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="blob absolute top-1/4 left-1/4 w-64 h-64 bg-gradient-to-r from-primary-200 to-primary-300 opacity-20"></div>
        <div class="blob absolute top-3/4 right-1/4 w-48 h-48 bg-gradient-to-r from-primary-300 to-primary-400 opacity-20" style="animation-delay: -2s;"></div>
        <div class="blob absolute top-1/2 right-1/3 w-32 h-32 bg-gradient-to-r from-primary-400 to-primary-500 opacity-20" style="animation-delay: -4s;"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="inline-flex items-center space-x-3 mb-8">
                    <?php if($company->logo): ?>
                        <img src="<?php echo e(asset('storage/' . $company->logo)); ?>" 
                             alt="<?php echo e($company->name); ?>" 
                             class="w-12 h-12 rounded-xl object-cover">
                    <?php else: ?>
                        <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-store text-white text-xl"></i>
                        </div>
                    <?php endif; ?>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                        <?php echo e($company->name); ?>

                    </h1>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-shield text-blue-600 mr-3"></i>
                    Admin Portal
                </h2>
                <p class="text-gray-600">
                    Administrative access to your store
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                <?php if($errors->any()): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                        <ul class="space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <?php echo e($error); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Admin Login Type Badge -->
                <div class="mb-6 text-center">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Administrator Access for <?php echo e($company->name); ?>

                    </span>
                </div>

                <form action="<?php echo e(route('admin.login.submit')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Admin Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user-shield text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required
                                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Enter your admin email">
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                   class="w-full pl-12 pr-12 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-0 transition-colors duration-200 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Enter your password">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-2 border-gray-300 rounded focus:ring-primary-500" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                            <span class="ml-2 text-sm text-gray-700">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-6 rounded-xl text-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In as Admin
                    </button>
                </form>

                <!-- Footer Links -->
                <div class="mt-8 text-center space-y-4">
                    <div class="border-t border-gray-200 pt-6">
                        <div class="bg-blue-50 rounded-xl p-4">
                            <div class="flex items-center justify-center space-x-2 text-blue-700">
                                <i class="fas fa-store"></i>
                                <span class="font-semibold"><?php echo e($company->name); ?> Admin Panel</span>
                            </div>
                            <p class="text-sm text-blue-600 mt-1">
                                Full administrative access to manage your store
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex justify-center space-x-6 text-sm text-gray-500">
                        <a href="<?php echo e(route('shop')); ?>" class="hover:text-gray-700 flex items-center">
                            <i class="fas fa-shopping-bag mr-1"></i>
                            View Store
                        </a>
                        <a href="<?php echo e(route('login')); ?>" class="hover:text-gray-700 flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            Regular Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Company Info Card -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 text-center border border-gray-200">
                <i class="fas fa-shield-alt text-blue-600 text-2xl mb-2"></i>
                <div class="text-sm font-semibold text-gray-900">Secure Admin Access</div>
                <div class="text-xs text-gray-500 mt-1">Protected with advanced security measures</div>
            </div>
        </div>
    </div>

    <script>
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

        // Enhanced focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('transform', 'scale-[1.02]');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('transform', 'scale-[1.02]');
            });
        });
    </script>
</body>
</html><?php /**PATH D:\source_code\herbal-ecom\resources\views/auth/tenant-admin-login.blade.php ENDPATH**/ ?>