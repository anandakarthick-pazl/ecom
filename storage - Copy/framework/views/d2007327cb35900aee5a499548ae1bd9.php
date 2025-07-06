<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - Herbal Ecom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 2px solid #3b82f6;
        }
        
        .logo {
            font-size: 2rem;
            color: #1e3a8a;
            margin-bottom: 1rem;
        }
        
        .super-admin-badge {
            background: linear-gradient(45deg, #dc2626, #ef4444);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="logo">🔐 Herbal Ecom</div>
                            <div class="super-admin-badge">SUPER ADMIN</div>
                            <h4>System Administration</h4>
                            <p class="text-muted">Sign in to access system controls</p>
                        </div>
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div><?php echo e($error); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo e(route('super-admin.login.post')); ?>">
                            <?php echo csrf_field(); ?>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo e(old('email')); ?>" required autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Sign In as Super Admin
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="<?php echo e(route('landing.index')); ?>" class="text-muted">
                                <i class="fas fa-arrow-left"></i> Back to Main Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\source_code\ecom\resources\views/super-admin/auth/login.blade.php ENDPATH**/ ?>