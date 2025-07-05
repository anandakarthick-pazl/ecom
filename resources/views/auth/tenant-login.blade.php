@extends('layouts.auth')

@section('title', 'Admin Login - ' . $company->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" 
                             alt="{{ $company->name }}" 
                             style="height: 40px; margin-right: 10px;">
                    @else
                        <i class="fas fa-store"></i>
                    @endif
                    {{ $company->name }}
                </h4>
                <small>Admin Dashboard Access</small>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.login.post') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password">
                        
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <div class="alert alert-light">
                        <i class="fas fa-store"></i>
                        <strong>{{ $company->name }}</strong> Admin Panel<br>
                        <small class="text-muted">Manage your store, products, orders, and customers</small>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-shopping-bag"></i> View Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
