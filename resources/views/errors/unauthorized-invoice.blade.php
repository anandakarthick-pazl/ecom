@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-danger mb-3">Access Denied</h3>
                    <p class="text-muted mb-4">
                        {{ $message ?? 'You are not authorized to access this invoice.' }}
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('track.order') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Track Order
                        </a>
                        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home"></i> Back to Shop
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
