<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Section - Landing Page Management</title>
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
                <a class="nav-link" href="{{ route('super-admin.landing-page.index') }}">
                    <i class="fas fa-arrow-left"></i> Back to Landing Page
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Pricing Section Management</h1>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Pricing Section</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.landing-page.pricing.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="section_title" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="section_title" name="section_title" 
                                   value="{{ $pricingSection->section_title ?? 'Choose Your Plan' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="section_subtitle" class="form-label">Section Subtitle</label>
                            <input type="text" class="form-control" id="section_subtitle" name="section_subtitle" 
                                   value="{{ $pricingSection->section_subtitle ?? 'Select the perfect plan for your needs' }}" required>
                        </div>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="show_packages" name="show_packages" 
                               {{ ($pricingSection->show_packages ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_packages">
                            Display packages on landing page
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.landing-page.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Current Packages Preview -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Current Packages Preview</h5>
            </div>
            <div class="card-body">
                @if(isset($packages) && $packages->count() > 0)
                    <div class="row">
                        @foreach($packages as $package)
                        <div class="col-md-4 mb-3">
                            <div class="card {{ $package->is_popular ? 'border-primary' : '' }}">
                                @if($package->is_popular)
                                <div class="card-header bg-primary text-white text-center">
                                    <small>Most Popular</small>
                                </div>
                                @endif
                                <div class="card-body text-center">
                                    <h5>{{ $package->name }}</h5>
                                    <h3>${{ number_format($package->price, 0) }}</h3>
                                    <p class="text-muted">per {{ $package->billing_cycle }}</p>
                                    <p>{{ $package->description }}</p>
                                    <button class="btn btn-outline-primary">Choose Plan</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5>No Packages Available</h5>
                        <p class="text-muted">Create packages first to display them on the pricing section.</p>
                        <a href="{{ route('super-admin.packages.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Packages
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
