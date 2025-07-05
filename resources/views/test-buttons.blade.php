@extends('layouts.app')

@section('title', 'Button Visibility Test')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🔘 Button Visibility Test Page</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This page tests all button types to ensure they are visible with proper contrast and styling.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>🎨 Solid Buttons</h5>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button class="btn btn-primary">Primary</button>
                                <button class="btn btn-secondary">Secondary</button>
                                <button class="btn btn-success">Success</button>
                                <button class="btn btn-info">Info</button>
                                <button class="btn btn-warning">Warning</button>
                                <button class="btn btn-danger">Danger</button>
                                <button class="btn btn-light">Light</button>
                                <button class="btn btn-dark">Dark</button>
                            </div>
                            
                            <h5>📝 Outline Buttons</h5>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <button class="btn btn-outline-primary">Outline Primary</button>
                                <button class="btn btn-outline-secondary">Outline Secondary</button>
                                <button class="btn btn-outline-success">Outline Success</button>
                                <button class="btn btn-outline-info">Outline Info</button>
                                <button class="btn btn-outline-warning">Outline Warning</button>
                                <button class="btn btn-outline-danger">Outline Danger</button>
                            </div>
                            
                            <h5>📐 Button Sizes</h5>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                                <button class="btn btn-primary btn-sm">Small</button>
                                <button class="btn btn-primary">Regular</button>
                                <button class="btn btn-primary btn-lg">Large</button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>🛒 E-commerce Buttons</h5>
                            <div class="d-flex flex-column gap-2 mb-4">
                                <button class="btn btn-primary">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <button class="btn btn-success">
                                    <i class="fas fa-credit-card me-2"></i>Buy Now
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-heart me-2"></i>Add to Wishlist
                                </button>
                                <button class="btn btn-info">
                                    <i class="fas fa-search me-2"></i>Quick View
                                </button>
                            </div>
                            
                            <h5>⚙️ Admin Action Buttons</h5>
                            <div class="d-flex flex-column gap-2 mb-4">
                                <button class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create New
                                </button>
                                <button class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                                <button class="btn btn-secondary">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>✅ Button Visibility Checklist</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check1">
                                <label class="form-check-label" for="check1">
                                    Primary buttons are visible (not white on white)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check2">
                                <label class="form-check-label" for="check2">
                                    All button text is readable
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check3">
                                <label class="form-check-label" for="check3">
                                    Buttons have proper colors and borders
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check4">
                                <label class="form-check-label" for="check4">
                                    Hover effects work correctly
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check5">
                                <label class="form-check-label" for="check5">
                                    Outline buttons are visible
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="check6">
                                <label class="form-check-label" for="check6">
                                    Icons and text are properly aligned
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>🎨 Current Color Configuration</h5>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td><strong>Primary Color:</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 20px; height: 20px; background-color: {{ $globalCompany->primary_color }}; border: 1px solid #ddd; border-radius: 3px; margin-right: 8px;"></div>
                                                {{ $globalCompany->primary_color }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Secondary Color:</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 20px; height: 20px; background-color: {{ $globalCompany->secondary_color }}; border: 1px solid #ddd; border-radius: 3px; margin-right: 8px;"></div>
                                                {{ $globalCompany->secondary_color }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="alert alert-secondary">
                                <small>
                                    <strong>Note:</strong> If your Super Admin primary color is white or very light, 
                                    the system automatically uses dark fallback colors (#2c3e50) to ensure button visibility.
                                </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h5>🔧 Interactive Test</h5>
                            <p>Click these buttons to test interactivity:</p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button class="btn btn-primary" onclick="showAlert('Primary button clicked!')">Test Primary</button>
                                <button class="btn btn-success" onclick="showAlert('Success button clicked!')">Test Success</button>
                                <button class="btn btn-outline-primary" onclick="showAlert('Outline Primary clicked!')">Test Outline</button>
                                <button class="btn btn-warning" onclick="showAlert('Warning button clicked!')">Test Warning</button>
                            </div>
                            
                            <div class="text-center">
                                <button class="btn btn-success me-2" onclick="allGood()">
                                    ✅ All Buttons Working!
                                </button>
                                <button class="btn btn-warning me-2" onclick="reportIssue()">
                                    ⚠️ Found Issues
                                </button>
                                <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                                    🏠 Back to Store
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional test styles */
.gap-2 {
    gap: 0.5rem !important;
}

.btn:focus {
    outline: 2px solid rgba(0,123,255,0.5);
    outline-offset: 2px;
}
</style>
@endsection

@push('scripts')
<script>
function showAlert(message) {
    alert(message);
}

function allGood() {
    // Check all checkboxes
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.checked = true;
    });
    
    alert('Excellent! All buttons are working correctly. ✅\n\nYour button visibility issue has been resolved!');
}

function reportIssue() {
    const issues = [];
    const checkboxes = document.querySelectorAll('.form-check-input');
    
    checkboxes.forEach((checkbox, index) => {
        if (!checkbox.checked) {
            const label = checkbox.nextElementSibling.textContent.trim();
            issues.push(label);
        }
    });
    
    if (issues.length > 0) {
        alert('Issues found:\n\n' + issues.map((issue, i) => `${i + 1}. ${issue}`).join('\n') + 
              '\n\nSolution: Clear browser cache (Ctrl+F5) and check Super Admin color settings.');
    } else {
        alert('No specific issues selected. If you\'re still having problems:\n\n' +
              '1. Clear browser cache (Ctrl+F5)\n' +
              '2. Check Super Admin → Settings → General\n' +
              '3. Ensure primary color is dark enough\n' +
              '4. Try incognito/private browsing mode');
    }
}

// Test hover effects
document.addEventListener('DOMContentLoaded', function() {
    console.log('Button test page loaded');
    console.log('Primary color:', '{{ $globalCompany->primary_color }}');
    console.log('Secondary color:', '{{ $globalCompany->secondary_color }}');
    
    // Add click feedback to all buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});
</script>

<style>
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
@endpush
