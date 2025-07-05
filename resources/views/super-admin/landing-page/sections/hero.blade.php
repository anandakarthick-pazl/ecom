<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Section - Landing Page Management</title>
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
        <h1>Hero Section Management</h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Hero Section</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('super-admin.landing-page.hero.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Main Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ $heroSection->title ?? 'Build Your E-commerce Empire' }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subtitle" class="form-label">Subtitle</label>
                                <textarea class="form-control" id="subtitle" name="subtitle" rows="2" required>{{ $heroSection->subtitle ?? 'Create stunning online stores with our powerful platform' }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $heroSection->description ?? 'Get started today and launch your business in minutes with our intuitive tools and beautiful themes.' }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_text" class="form-label">Call-to-Action Text</label>
                                        <input type="text" class="form-control" id="cta_text" name="cta_text" 
                                               value="{{ $heroSection->cta_text ?? 'Start Free Trial' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_link" class="form-label">Call-to-Action Link</label>
                                        <input type="url" class="form-control" id="cta_link" name="cta_link" 
                                               value="{{ $heroSection->cta_link ?? '/register' }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="background_image" class="form-label">Background Image (Optional)</label>
                                <input type="file" class="form-control" id="background_image" name="background_image" accept="image/*">
                                <small class="form-text text-muted">Upload a background image for the hero section. Leave empty to use gradient.</small>
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
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="bg-gradient-primary text-white p-4 rounded">
                            <h3>{{ $heroSection->title ?? 'Build Your E-commerce Empire' }}</h3>
                            <p>{{ $heroSection->subtitle ?? 'Create stunning online stores with our powerful platform' }}</p>
                            <button class="btn btn-light">
                                {{ $heroSection->cta_text ?? 'Start Free Trial' }}
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Keep the title under 60 characters for best impact</li>
                            <li>The subtitle should explain your value proposition</li>
                            <li>Use action-oriented CTA text like "Get Started" or "Try Free"</li>
                            <li>Images should be at least 1920x1080 pixels</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
