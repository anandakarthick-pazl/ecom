@extends('admin.layouts.app')

@section('title', 'Theme Test Page')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Theme Color Test</h1>
    </div>

    @php
        // Get current company
        $company = null;
        if (auth()->user() && auth()->user()->company_id) {
            $company = \App\Models\SuperAdmin\Company::find(auth()->user()->company_id);
        } elseif (session('selected_company_id')) {
            $company = \App\Models\SuperAdmin\Company::find(session('selected_company_id'));
        }
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tailwind Color Classes Test</h6>
                </div>
                <div class="card-body">
                    <h4>Background Colors</h4>
                    <div class="mb-3">
                        <div class="p-3 mb-2 bg-primary text-white rounded">
                            This uses bg-primary (#2d5016) - Dark Green
                        </div>
                        <div class="p-3 mb-2 bg-secondary text-white rounded">
                            This uses bg-secondary (#4a7c28) - Medium Green
                        </div>
                        <div class="p-3 mb-2 bg-accent text-white rounded">
                            This uses bg-accent (#8fb548) - Light Green
                        </div>
                    </div>

                    <h4>Text Colors</h4>
                    <div class="mb-3">
                        <p class="text-primary">This text uses text-primary color</p>
                        <p class="text-secondary">This text uses text-secondary color</p>
                        <p class="text-accent">This text uses text-accent color</p>
                    </div>

                    <h4>Buttons with Theme Colors</h4>
                    <div class="mb-3">
                        <button class="bg-primary text-white px-4 py-2 rounded mr-2 hover:bg-secondary">
                            Primary Button
                        </button>
                        <button class="bg-secondary text-white px-4 py-2 rounded mr-2 hover:bg-accent">
                            Secondary Button
                        </button>
                        <button class="bg-accent text-white px-4 py-2 rounded hover:bg-primary">
                            Accent Button
                        </button>
                    </div>

                    <h4>Company Information Display (From Companies Table)</h4>
                    <div class="bg-gray-100 p-4 rounded">
                        @if($company)
                            <h5 class="text-primary font-bold">{{ $company->name }}</h5>
                            <p><strong>Email:</strong> {{ $company->email }}</p>
                            <p><strong>Phone:</strong> {{ $company->phone ?: 'Not set' }}</p>
                            <p><strong>Address:</strong> 
                                @if($company->address || $company->city || $company->state || $company->postal_code)
                                    {{ $company->address }} {{ $company->city }} {{ $company->state }} {{ $company->postal_code }}
                                @else
                                    Not set
                                @endif
                            </p>
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                     alt="Company Logo" class="mt-2" style="max-height: 100px;">
                            @else
                                <p class="text-muted">No logo uploaded</p>
                            @endif
                        @else
                            <p class="text-danger">No company found. Please ensure you're logged in with a company account.</p>
                        @endif
                    </div>

                    <h4 class="mt-4">Theme Settings (From App Settings)</h4>
                    <div class="bg-gray-100 p-4 rounded">
                        <p><strong>Primary Color:</strong> <span class="badge" style="background-color: {{ \App\Models\AppSetting::get('primary_color', '#2d5016') }}; color: white;">{{ \App\Models\AppSetting::get('primary_color', '#2d5016') }}</span></p>
                        <p><strong>Secondary Color:</strong> <span class="badge" style="background-color: {{ \App\Models\AppSetting::get('secondary_color', '#4a7c28') }}; color: white;">{{ \App\Models\AppSetting::get('secondary_color', '#4a7c28') }}</span></p>
                        <p><strong>Sidebar Color:</strong> <span class="badge" style="background-color: {{ \App\Models\AppSetting::get('sidebar_color', '#2d5016') }}; color: white;">{{ \App\Models\AppSetting::get('sidebar_color', '#2d5016') }}</span></p>
                        <p><strong>Theme Mode:</strong> {{ \App\Models\AppSetting::get('theme_mode', 'light') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-primary {
    background-color: {{ \App\Models\AppSetting::get('primary_color', '#2d5016') }} !important;
}

.bg-secondary {
    background-color: {{ \App\Models\AppSetting::get('secondary_color', '#4a7c28') }} !important;
}

.bg-accent {
    background-color: #8fb548 !important;
}

.text-primary {
    color: {{ \App\Models\AppSetting::get('primary_color', '#2d5016') }} !important;
}

.text-secondary {
    color: {{ \App\Models\AppSetting::get('secondary_color', '#4a7c28') }} !important;
}

.text-accent {
    color: #8fb548 !important;
}
</style>
@endpush
@endsection
