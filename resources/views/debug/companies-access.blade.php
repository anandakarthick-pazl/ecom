<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies Access Guide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-globe"></i> Multi-Tenant Custom Domain Access Guide</h2>
                        <p class="mb-0"><small>Each customer gets their own professional domain</small></p>
                    </div>
                    <div class="card-body">
                        
                        @foreach($companies as $company)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0">{{ $company->name }}</h4>
                                <small class="text-muted">ID: {{ $company->id }} | Domain: {{ $company->domain }} | Status: {{ $company->status }}</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-cog text-primary"></i> Admin Panel Access</h5>
                                        <div class="list-group mb-3">
                                            <a href="{{ route('login') }}" class="list-group-item list-group-item-action">
                                                <i class="fas fa-sign-in-alt"></i> Main Login
                                                <small class="d-block text-muted">Select "{{ $company->name }}" from dropdown</small>
                                            </a>
                                            @if($company->domain)
                                            <a href="http://{{ $company->domain }}:8000/admin/login" class="list-group-item list-group-item-action">
                                                <i class="fas fa-link"></i> Custom Domain
                                                <small class="d-block text-muted">http://{{ $company->domain }}:8000/admin/login</small>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fas fa-store text-success"></i> Ecommerce Store</h5>
                                        <div class="list-group mb-3">
                                            @if($company->domain)
                                            <a href="http://{{ $company->domain }}:8000/shop" class="list-group-item list-group-item-action">
                                                <i class="fas fa-shopping-cart"></i> Store
                                                <small class="d-block text-muted">http://{{ $company->domain }}:8000/shop</small>
                                            </a>
                                            <a href="http://{{ $company->domain }}:8000/debug-tenant" class="list-group-item list-group-item-action">
                                                <i class="fas fa-bug"></i> Debug Info
                                                <small class="d-block text-muted">Test domain resolution</small>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @php
                                    $adminUser = \App\Models\User::where('company_id', $company->id)
                                                                 ->whereIn('role', ['admin', 'manager'])
                                                                 ->first();
                                @endphp
                                
                                @if($adminUser)
                                <div class="alert alert-info">
                                    <i class="fas fa-user"></i> <strong>Admin Credentials:</strong><br>
                                    Email: {{ $adminUser->email }}<br>
                                    Password: <code>password123</code> (default)<br>
                                    Role: {{ $adminUser->role }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Custom Domain Setup Required</h5>
                            <p>This system uses <strong>custom domains</strong> for each company. Add these entries to your hosts file:</p>
                            <pre><code># Multi-tenant SaaS - Custom Domains
@foreach($companies as $company)
@if($company->domain)
127.0.0.1 {{ $company->domain }}
@endif
@endforeach</code></pre>
                            <p><strong>Windows:</strong> <code>C:\Windows\System32\drivers\etc\hosts</code></p>
                            <p><strong>Mac/Linux:</strong> <code>/etc/hosts</code></p>
                            <p><small>Remember to restart your browser after editing the hosts file.</small></p>
                        </div>
                        
                        <div class="alert alert-success">
                            <h5><i class="fas fa-lightbulb"></i> Quick Test</h5>
                            <p>After setting up custom domains, test with these credentials:</p>
                            <ul>
                                @if($companies->count() > 0)
                                @php $firstCompany = $companies->first(); @endphp
                                <li>Domain: <code>{{ $firstCompany->domain ?? 'localhost:8000' }}</code></li>
                                <li>Email: <code>{{ $firstCompany->email ?? 'admin@example.com' }}</code></li>
                                <li>Password: <code>password123</code></li>
                                @else
                                <li>Run setup script to create companies first</li>
                                @endif
                            </ul>
                            <p><small>Or use the main login at <a href="{{ route('login') }}">{{ route('login') }}</a></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
