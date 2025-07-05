<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features Section - Landing Page Management</title>
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
        <h1>Features Section Management</h1>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Features Section</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.landing-page.features.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="section_title" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="section_title" name="section_title" 
                                   value="{{ $featuresSection->section_title ?? 'Powerful Features' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="section_subtitle" class="form-label">Section Subtitle</label>
                            <input type="text" class="form-control" id="section_subtitle" name="section_subtitle" 
                                   value="{{ $featuresSection->section_subtitle ?? 'Everything you need to succeed' }}" required>
                        </div>
                    </div>
                    
                    <h6>Features List</h6>
                    <div id="features-container">
                        <!-- Default features -->
                        <div class="feature-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Icon (FontAwesome)</label>
                                    <input type="text" class="form-control" name="features[0][icon]" 
                                           value="fas fa-rocket" placeholder="fas fa-rocket">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="features[0][title]" 
                                           value="Easy Setup" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="features[0][description]" 
                                           value="Get started in minutes with our intuitive setup" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger w-100 remove-feature">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="feature-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Icon (FontAwesome)</label>
                                    <input type="text" class="form-control" name="features[1][icon]" 
                                           value="fas fa-palette" placeholder="fas fa-palette">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="features[1][title]" 
                                           value="Beautiful Themes" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="features[1][description]" 
                                           value="Choose from professional themes designed for conversion" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger w-100 remove-feature">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="feature-item border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Icon (FontAwesome)</label>
                                    <input type="text" class="form-control" name="features[2][icon]" 
                                           value="fas fa-chart-bar" placeholder="fas fa-chart-bar">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="features[2][title]" 
                                           value="Analytics & Reports" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Description</label>
                                    <input type="text" class="form-control" name="features[2][description]" 
                                           value="Track your success with detailed analytics and reports" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-outline-danger w-100 remove-feature">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary mb-4" id="add-feature">
                        <i class="fas fa-plus"></i> Add Feature
                    </button>
                    
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

    <script>
        let featureIndex = 3;
        
        document.getElementById('add-feature').addEventListener('click', function() {
            const container = document.getElementById('features-container');
            const newFeature = document.createElement('div');
            newFeature.className = 'feature-item border rounded p-3 mb-3';
            newFeature.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Icon (FontAwesome)</label>
                        <input type="text" class="form-control" name="features[${featureIndex}][icon]" 
                               placeholder="fas fa-star">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="features[${featureIndex}][title]" 
                               placeholder="Feature Title" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="features[${featureIndex}][description]" 
                               placeholder="Feature description" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger w-100 remove-feature">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newFeature);
            featureIndex++;
        });
        
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-feature') || e.target.parentElement.classList.contains('remove-feature')) {
                const featureItem = e.target.closest('.feature-item');
                if (featureItem) {
                    featureItem.remove();
                }
            }
        });
    </script>
</body>
</html>
