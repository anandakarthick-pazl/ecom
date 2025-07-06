<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Herbal Ecom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('super-admin.dashboard')); ?>">
                <i class="fas fa-crown"></i> Super Admin
            </a>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="<?php echo e(route('super-admin.logout')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Super Admin Dashboard</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-warning">
                <?php echo e($error); ?>

            </div>
        <?php endif; ?>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Companies</h6>
                                <h3><?php echo e($stats['total_companies']); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Companies</h6>
                                <h3><?php echo e($stats['active_companies']); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Trial Companies</h6>
                                <h3><?php echo e($stats['trial_companies']); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Revenue</h6>
                                <h3>$<?php echo e(number_format($stats['total_revenue'], 0)); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Companies -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Companies</h5>
                    </div>
                    <div class="card-body">
                        <?php if($recentCompanies->count() > 0): ?>
                            <?php $__currentLoopData = $recentCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong><?php echo e($company->name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($company->email); ?></small>
                                </div>
                                <span class="badge bg-<?php echo e($company->status === 'active' ? 'success' : 'warning'); ?>">
                                    <?php echo e(ucfirst($company->status)); ?>

                                </span>
                            </div>
                            <?php if(!$loop->last): ?><hr><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <p class="text-muted">No companies registered yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('super-admin.landing-page.index')); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-plus"></i> Manage Landing Page
                            </a>
                            <a href="<?php echo e(route('super-admin.themes.index')); ?>" class="btn btn-outline-info">
                                <i class="fas fa-palette"></i> Manage Themes
                            </a>
                            <a href="<?php echo e(route('super-admin.packages.index')); ?>" class="btn btn-outline-success">
                                <i class="fas fa-box"></i> Manage Packages
                            </a>
                            <a href="<?php echo e(route('super-admin.settings.index')); ?>" class="btn btn-outline-warning">
                                <i class="fas fa-cog"></i> System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Laravel Version:</strong> <?php echo e(app()->version()); ?>

                    </div>
                    <div class="col-md-4">
                        <strong>PHP Version:</strong> <?php echo e(phpversion()); ?>

                    </div>
                    <div class="col-md-4">
                        <strong>Server Time:</strong> <?php echo e(now()->format('Y-m-d H:i:s')); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\source_code\ecom\resources\views/super-admin/dashboard.blade.php ENDPATH**/ ?>