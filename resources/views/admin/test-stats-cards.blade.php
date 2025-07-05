@extends('admin.layouts.app')

@section('title', 'Stats Cards Test')
@section('page_title', 'Dashboard Stats Cards Test')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">📊 Stats Cards Visibility Test</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This page tests all the stats cards to ensure text is visible with proper contrast on colored backgrounds.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Test Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-primary">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">123</h3>
                    <p class="mb-0 text-white">Primary Card Test</p>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-success">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">₹45,678</h3>
                    <p class="mb-0 text-white">Success Card Test</p>
                </div>
                <div>
                    <i class="fas fa-rupee-sign fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-info">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">89</h3>
                    <p class="mb-0 text-white">Info Card Test</p>
                </div>
                <div>
                    <i class="fas fa-users fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-warning">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">456</h3>
                    <p class="mb-0 text-white">Warning Card Test</p>
                </div>
                <div>
                    <i class="fas fa-box fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card bg-danger">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">12</h3>
                    <p class="mb-0 text-white">Danger Card Test</p>
                </div>
                <div>
                    <i class="fas fa-exclamation-triangle fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">78</h3>
                    <p class="mb-0 text-white">Custom Gradient Test</p>
                </div>
                <div>
                    <i class="fas fa-chart-line fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">34</h3>
                    <p class="mb-0 text-white">Custom Pink Test</p>
                </div>
                <div>
                    <i class="fas fa-heart fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0 text-white">567</h3>
                    <p class="mb-0 text-white">Custom Blue Test</p>
                </div>
                <div>
                    <i class="fas fa-star fa-2x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">✅ Stats Cards Checklist</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Text Visibility:</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check1">
                            <label class="form-check-label" for="check1">
                                Numbers are clearly visible (white on colored background)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check2">
                            <label class="form-check-label" for="check2">
                                Labels are clearly visible (white text)
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check3">
                            <label class="form-check-label" for="check3">
                                Icons are visible (subtle white)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="check4">
                            <label class="form-check-label" for="check4">
                                No white text on white/light backgrounds
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Visual Effects:</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check5">
                            <label class="form-check-label" for="check5">
                                Cards have gradient backgrounds
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check6">
                            <label class="form-check-label" for="check6">
                                Cards lift up slightly on hover
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="check7">
                            <label class="form-check-label" for="check7">
                                Cards have subtle shadow effects
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="check8">
                            <label class="form-check-label" for="check8">
                                Rounded corners and modern appearance
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button class="btn btn-success me-2" onclick="checkAll()">
                        ✅ All Cards Working!
                    </button>
                    <button class="btn btn-warning me-2" onclick="reportIssue()">
                        ⚠️ Found Issues
                    </button>
                    <button class="btn btn-info" onclick="testColors()">
                        🎨 Test Your Colors
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">🎨 Current Colors</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Primary:</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 20px; height: 20px; background-color: {{ $globalCompany->primary_color }}; border: 1px solid #ddd; border-radius: 3px; margin-right: 8px;"></div>
                                    <small>{{ $globalCompany->primary_color }}</small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Secondary:</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div style="width: 20px; height: 20px; background-color: {{ $globalCompany->secondary_color }}; border: 1px solid #ddd; border-radius: 3px; margin-right: 8px;"></div>
                                    <small>{{ $globalCompany->secondary_color }}</small>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-3">
                    <h6>Quick Fix Steps:</h6>
                    <ol class="small">
                        <li>Clear browser cache (Ctrl+F5)</li>
                        <li>Check Super Admin color settings</li>
                        <li>Ensure primary color is dark enough</li>
                        <li>Verify CSS is loading properly</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">🔧 Troubleshooting Guide</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="troubleshootingAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                Text still not visible on cards
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                            <div class="accordion-body">
                                <strong>Solution Steps:</strong>
                                <ol>
                                    <li><strong>Clear Cache:</strong> Run <code>php artisan cache:clear && php artisan view:clear</code></li>
                                    <li><strong>Browser Cache:</strong> Hard refresh (Ctrl+F5) or try incognito mode</li>
                                    <li><strong>CSS Inspection:</strong> Check browser dev tools for CSS conflicts</li>
                                    <li><strong>Color Settings:</strong> Verify Super Admin → Settings → General has proper colors set</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Cards appear plain/no gradients
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                            <div class="accordion-body">
                                <strong>This indicates CSS isn't fully loading:</strong>
                                <ol>
                                    <li>Check browser console for CSS errors</li>
                                    <li>Ensure Bootstrap 5.3.0 is loading properly</li>
                                    <li>Verify admin layout CSS is being processed</li>
                                    <li>Check if CSS minification is causing issues</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                Hover effects not working
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                            <div class="accordion-body">
                                <strong>JavaScript/CSS interaction issues:</strong>
                                <ol>
                                    <li>Check if hover effects are being overridden by other CSS</li>
                                    <li>Verify CSS transitions are supported in your browser</li>
                                    <li>Test in different browsers (Chrome, Firefox, Safari)</li>
                                    <li>Disable browser extensions that might interfere with CSS</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checkAll() {
    document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
        checkbox.checked = true;
    });
    
    // Show success message
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Excellent!</strong> All stats cards are working perfectly! Your dashboard should now have proper contrast and visibility.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.querySelector('.col-12').insertAdjacentHTML('afterbegin', alertHtml);
}

function reportIssue() {
    const unchecked = [];
    document.querySelectorAll('.form-check-input').forEach(function(checkbox, index) {
        if (!checkbox.checked) {
            const label = checkbox.nextElementSibling.textContent.trim();
            unchecked.push(label);
        }
    });
    
    if (unchecked.length > 0) {
        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Issues found:</strong><br>
                ${unchecked.map((item, i) => `${i + 1}. ${item}`).join('<br>')}
                <br><br>Please check the troubleshooting guide below for solutions.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.querySelector('.col-12').insertAdjacentHTML('afterbegin', alertHtml);
    } else {
        alert('No specific issues selected. Please check the troubleshooting steps if you\'re experiencing problems.');
    }
}

function testColors() {
    // Get current colors from CSS variables
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim();
    const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim();
    
    const alertHtml = `
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-palette me-2"></i>
            <strong>Current Color Configuration:</strong><br>
            Primary: <span style="color: ${primaryColor}; font-weight: bold;">${primaryColor}</span><br>
            Secondary: <span style="color: ${secondaryColor}; font-weight: bold;">${secondaryColor}</span><br>
            <br>
            <small>These colors are used for the stats card backgrounds. White text is used on all colored backgrounds for optimal contrast.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.querySelector('.col-12').insertAdjacentHTML('afterbegin', alertHtml);
}

// Auto-dismiss alerts after 10 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        });
    }, 10000);
});
</script>
@endpush
