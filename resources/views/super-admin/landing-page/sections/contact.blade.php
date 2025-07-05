<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Section - Landing Page Management</title>
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
        <h1>Contact Section Management</h1>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Contact Section</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.landing-page.contact.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="section_title" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="section_title" name="section_title" 
                                   value="{{ $contactSection->section_title ?? 'Get In Touch' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="section_subtitle" class="form-label">Section Subtitle</label>
                            <input type="text" class="form-control" id="section_subtitle" name="section_subtitle" 
                                   value="{{ $contactSection->section_subtitle ?? 'We\'d love to hear from you' }}" required>
                        </div>
                    </div>
                    
                    <h6>Contact Information</h6>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ $contactSection->email ?? 'hello@yourdomain.com' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ $contactSection->phone ?? '+1 (555) 123-4567' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ $contactSection->address ?? '123 Business Street\nCity, State 12345' }}</textarea>
                        </div>
                    </div>
                    
                    <h6>Social Media Links</h6>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="social_facebook" class="form-label">Facebook URL</label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                                   value="{{ $contactSection->facebook_url ?? '' }}" placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="col-md-3">
                            <label for="social_twitter" class="form-label">Twitter URL</label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter" 
                                   value="{{ $contactSection->twitter_url ?? '' }}" placeholder="https://twitter.com/yourhandle">
                        </div>
                        <div class="col-md-3">
                            <label for="social_linkedin" class="form-label">LinkedIn URL</label>
                            <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" 
                                   value="{{ $contactSection->linkedin_url ?? '' }}" placeholder="https://linkedin.com/company/yourcompany">
                        </div>
                        <div class="col-md-3">
                            <label for="social_instagram" class="form-label">Instagram URL</label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                                   value="{{ $contactSection->instagram_url ?? '' }}" placeholder="https://instagram.com/yourhandle">
                        </div>
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
        
        <!-- Preview -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Preview</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                        <h6>Email</h6>
                        <p>{{ $contactSection->email ?? 'hello@yourdomain.com' }}</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-phone fa-2x text-primary mb-2"></i>
                        <h6>Phone</h6>
                        <p>{{ $contactSection->phone ?? '+1 (555) 123-4567' }}</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-map-marker-alt fa-2x text-primary mb-2"></i>
                        <h6>Address</h6>
                        <p>{!! nl2br($contactSection->address ?? '123 Business Street\nCity, State 12345') !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
