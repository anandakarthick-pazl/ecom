@extends('layouts.app')

@section('title', 'Settings Test Page')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🧪 Settings Configuration Test</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This page shows how your site settings are currently configured. You can use this to verify that your super admin settings are working correctly.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>🏢 Company Information</h5>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th width="40%">Company Name:</th>
                                        <td>
                                            <strong>{{ $globalCompany->company_name ?: 'Not Set' }}</strong>
                                            @if(!$globalCompany->company_name)
                                                <small class="text-muted d-block">Configure this in Super Admin → Settings → General</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Company Email:</th>
                                        <td>{{ $globalCompany->company_email ?: 'Not Set' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Company Phone:</th>
                                        <td>{{ $globalCompany->company_phone ?: 'Not Set' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Company Address:</th>
                                        <td>{{ $globalCompany->company_address ?: 'Not Set' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>🎨 Branding & Appearance</h5>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th width="40%">Company Logo:</th>
                                        <td>
                                            @if($globalCompany->company_logo)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                                         alt="Company Logo" 
                                                         style="max-height: 50px; max-width: 150px; object-fit: contain;" 
                                                         class="border rounded">
                                                </div>
                                                <small class="text-success">✅ Logo configured</small>
                                            @else
                                                <span class="text-muted">No logo set</span>
                                                <small class="text-muted d-block">Upload logo in Super Admin → Settings → General</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Primary Color:</th>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30px; height: 30px; background-color: {{ $globalCompany->primary_color ?: '#667eea' }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></div>
                                                <span>{{ $globalCompany->primary_color ?: '#667eea' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Secondary Color:</th>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30px; height: 30px; background-color: {{ $globalCompany->secondary_color ?: '#6b8e23' }}; border: 1px solid #ddd; border-radius: 4px; margin-right: 10px;"></div>
                                                <span>{{ $globalCompany->secondary_color ?: '#6b8e23' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>⚙️ Super Admin Settings Status</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th width="50%">Settings Cache:</th>
                                        <td>
                                            @if(!empty($superAdminSettings))
                                                <span class="badge bg-success">✅ Active</span>
                                            @else
                                                <span class="badge bg-warning">⚠️ No Settings Found</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Site Name from Super Admin:</th>
                                        <td>{{ $superAdminSettings['site_name'] ?? 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Admin Email from Super Admin:</th>
                                        <td>{{ $superAdminSettings['admin_email'] ?? 'Not configured' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Logo from Super Admin:</th>
                                        <td>{{ $superAdminSettings['site_logo'] ? 'Configured' : 'Not configured' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>🔧 How to Configure Settings:</h6>
                                    <ol class="small mb-0">
                                        <li>Go to <strong>Super Admin Panel</strong></li>
                                        <li>Navigate to <strong>Settings → General</strong></li>
                                        <li>Update the <strong>Site Name</strong> field</li>
                                        <li>Upload a <strong>Site Logo</strong></li>
                                        <li>Set your <strong>Admin Email</strong></li>
                                        <li>Configure <strong>Primary Brand Color</strong></li>
                                        <li>Save the settings</li>
                                        <li>Refresh this page to see changes</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>🌐 Current Page Display</h5>
                    <div class="alert alert-secondary">
                        <p><strong>Page Title:</strong> {{ $globalCompany->company_name ?: 'Your Store' }} - E-commerce Store</p>
                        <p><strong>Navigation Brand:</strong> 
                            @if($globalCompany->company_logo)
                                <img src="{{ asset('storage/' . $globalCompany->company_logo) }}" 
                                     alt="{{ $globalCompany->company_name }}" 
                                     style="height: 20px; width: auto; object-fit: contain; vertical-align: middle;">
                            @else
                                🏪
                            @endif
                            {{ $globalCompany->company_name ?: 'Your Store' }}
                        </p>
                        <p class="mb-0"><strong>Footer Copyright:</strong> © {{ date('Y') }} {{ $globalCompany->company_name ?: 'Your Store' }}. All rights reserved.</p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('shop') }}" class="btn btn-primary me-2">
                            <i class="fas fa-store me-2"></i>Back to Store
                        </a>
                        @if(auth()->check() && auth()->user()->is_super_admin)
                            <a href="{{ route('super-admin.settings.general') }}" class="btn btn-outline-primary">
                                <i class="fas fa-cog me-2"></i>Configure Settings
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
