@extends('admin.layouts.app')

@section('title', 'Admin Color Test')
@section('page_title', 'Admin Sidebar Color Test')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">🎨 Admin Sidebar Color Test</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    This page helps verify that your admin sidebar colors are displaying correctly.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>🎨 Current Color Configuration</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="40%">Primary Color:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 30px; background-color: {{ $globalCompany->primary_color }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></div>
                                            <span><strong>{{ $globalCompany->primary_color }}</strong></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Secondary Color:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 30px; background-color: {{ $globalCompany->secondary_color }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></div>
                                            <span><strong>{{ $globalCompany->secondary_color }}</strong></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sidebar Color:</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 30px; background-color: {{ $globalCompany->sidebar_color }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></div>
                                            <span><strong>{{ $globalCompany->sidebar_color }}</strong></span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>✅ Sidebar Test Checklist</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check1">
                                    <label class="form-check-label" for="check1">
                                        Sidebar background is NOT white
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check2">
                                    <label class="form-check-label" for="check2">
                                        Menu text is visible (white/light colored)
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check3">
                                    <label class="form-check-label" for="check3">
                                        Menu items highlight on hover
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check4">
                                    <label class="form-check-label" for="check4">
                                        Active menu item is highlighted
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check5">
                                    <label class="form-check-label" for="check5">
                                        Company logo/name visible in header
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="check6">
                                    <label class="form-check-label" for="check6">
                                        Section headers are visible
                                    </label>
                                </div>
                                
                                <div class="text-center">
                                    <button class="btn btn-success btn-sm" onclick="checkAll()">
                                        ✅ All Good!
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="reportIssue()">
                                        ⚠️ Issues Found
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-8">
                        <h5>🔧 Troubleshooting Steps</h5>
                        <div class="accordion" id="troubleshootingAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Sidebar appears white/blank
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Go to <strong>Super Admin → Settings → General</strong></li>
                                            <li>Set a <strong>Primary Brand Color</strong> (e.g., #2c3e50)</li>
                                            <li>Save settings and refresh this page</li>
                                            <li>Clear browser cache if needed</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        Menu text not visible
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Clear browser cache (Ctrl+F5)</li>
                                            <li>Check if CSS files are loading properly</li>
                                            <li>Ensure the primary color is dark enough for white text</li>
                                            <li>Try using a darker color like #2c3e50 or #34495e</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        Colors not applying
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li>Clear application cache: <code>php artisan cache:clear</code></li>
                                            <li>Clear config cache: <code>php artisan config:clear</code></li>
                                            <li>Clear view cache: <code>php artisan view:clear</code></li>
                                            <li>Refresh browser and check again</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <h5>📊 Quick Stats</h5>
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $globalCompany->company_name ?: 'Admin Panel' }}</h3>
                                <p class="mb-0">Current Theme</p>
                            </div>
                        </div>
                        
                        <div class="card bg-secondary text-white mt-3">
                            <div class="card-body text-center">
                                <h4>✅ Fixed</h4>
                                <p class="mb-0">Sidebar Colors</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary me-2">
                        <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                    </a>
                    @if(auth()->check() && auth()->user()->is_super_admin ?? false)
                        <a href="{{ route('super-admin.settings.general') }}" class="btn btn-outline-primary">
                            <i class="fas fa-cog me-2"></i>Adjust Colors
                        </a>
                    @endif
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
    alert('Great! Your admin sidebar colors are working correctly! 🎉');
}

function reportIssue() {
    const unchecked = [];
    document.querySelectorAll('.form-check-input').forEach(function(checkbox, index) {
        if (!checkbox.checked) {
            unchecked.push(checkbox.nextElementSibling.textContent.trim());
        }
    });
    
    if (unchecked.length > 0) {
        alert('Issues found:\n\n' + unchecked.map((item, i) => `${i + 1}. ${item}`).join('\n') + '\n\nPlease check the troubleshooting steps below.');
    } else {
        alert('No specific issues selected. Please check the troubleshooting steps if you\'re experiencing problems.');
    }
}

// Auto-highlight current menu item for demo
document.addEventListener('DOMContentLoaded', function() {
    // This is just for demonstration - the actual highlighting is handled by the route checking in the main layout
    console.log('Admin sidebar color test page loaded');
    console.log('Current colors:', {
        primary: '{{ $globalCompany->primary_color }}',
        secondary: '{{ $globalCompany->secondary_color }}',
        sidebar: '{{ $globalCompany->sidebar_color }}'
    });
});
</script>
@endpush
