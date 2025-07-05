<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page Management - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('super-admin.dashboard') }}">
                <i class="fas fa-crown"></i> Super Admin
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('super-admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </div>
            <div class="navbar-nav ms-auto">
                <form method="POST" action="{{ route('super-admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Landing Page Management</h1>
            <a href="{{ route('landing.index') }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Landing Page
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="row">
            <!-- Hero Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-home text-primary"></i> Hero Section
                        </h5>
                        <a href="{{ route('super-admin.landing-page.hero') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Manage the main hero section with title, subtitle, and call-to-action buttons.</p>
                        <div class="small">
                            <strong>Includes:</strong> Title, Subtitle, CTA Buttons, Background Image/Gradient
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-star text-warning"></i> Features Section
                        </h5>
                        <a href="{{ route('super-admin.landing-page.features') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Configure the features showcase section with icons and descriptions.</p>
                        <div class="small">
                            <strong>Includes:</strong> Feature List, Icons, Descriptions, Layout Options
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign text-success"></i> Pricing Section
                        </h5>
                        <a href="{{ route('super-admin.landing-page.pricing') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Manage pricing display and package selection options.</p>
                        <div class="small">
                            <strong>Includes:</strong> Package Display, Pricing Tables, Feature Lists
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Section -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope text-info"></i> Contact Section
                        </h5>
                        <a href="{{ route('super-admin.landing-page.contact') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Configure contact information and social media links.</p>
                        <div class="small">
                            <strong>Includes:</strong> Contact Info, Social Links, Contact Form
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Overview -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> Current Settings Overview
                </h5>
            </div>
            <div class="card-body">
                @if(isset($sections) && $sections->count() > 0)
                    <div class="row">
                        @foreach($sections as $sectionName => $sectionSettings)
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="text-capitalize">{{ $sectionName }}</h6>
                                <small class="text-muted">{{ $sectionSettings->count() }} settings configured</small>
                                <div class="mt-2">
                                    @foreach($sectionSettings->take(3) as $setting)
                                    <div class="small">
                                        <strong>{{ $setting->key }}:</strong> 
                                        {{ Str::limit($setting->value, 30) }}
                                    </div>
                                    @endforeach
                                    @if($sectionSettings->count() > 3)
                                    <div class="small text-muted">... and {{ $sectionSettings->count() - 3 }} more</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h5>No Landing Page Settings Configured</h5>
                        <p class="text-muted">Start by editing one of the sections above to configure your landing page.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('landing.index') }}" class="btn btn-outline-info w-100" target="_blank">
                            <i class="fas fa-eye"></i> Preview Landing Page
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('super-admin.packages.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-box"></i> Manage Packages
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('super-admin.themes.index') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-palette"></i> Manage Themes
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('super-admin.companies.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-building"></i> View Companies
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
