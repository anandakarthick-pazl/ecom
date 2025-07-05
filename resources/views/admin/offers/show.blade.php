@extends('admin.layouts.app')

@section('title', 'Offer: ' . $offer->name)
@section('page_title', 'Offer: ' . $offer->name)

@section('page_actions')
<div class="btn-group">
    <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
    <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $offer->name }}</h4>
                        @if($offer->code)
                            <p class="mb-2">
                                <span class="badge bg-info fs-6">{{ $offer->code }}</span>
                            </p>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Type:</strong> {{ ucfirst($offer->type) }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Value:</strong> 
                                @if($offer->type === 'percentage')
                                    {{ $offer->value }}%
                                @else
                                    ₹{{ number_format($offer->value, 2) }}
                                @endif
                            </div>
                        </div>
                        
                        @if($offer->minimum_amount)
                        <div class="mb-3">
                            <strong>Minimum Order Amount:</strong> ₹{{ number_format($offer->minimum_amount, 2) }}
                        </div>
                        @endif
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Start Date:</strong> {{ $offer->start_date->format('M d, Y') }}
                            </div>
                            <div class="col-sm-6">
                                <strong>End Date:</strong> {{ $offer->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <strong>Status:</strong>
                                @if($offer->isValid())
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <strong>Created:</strong> {{ $offer->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        
                        @if($offer->type === 'category' && $offer->category)
                        <div class="mb-3">
                            <strong>Target Category:</strong>
                            <span class="badge bg-primary">{{ $offer->category->name }}</span>
                        </div>
                        @endif
                        
                        @if($offer->type === 'product' && $offer->product)
                        <div class="mb-3">
                            <strong>Target Product:</strong>
                            <span class="badge bg-success">{{ $offer->product->name }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="bg-light p-4 rounded">
                                <h3 class="text-primary mb-0">
                                    @if($offer->type === 'percentage')
                                        {{ $offer->value }}%
                                    @else
                                        ₹{{ number_format($offer->value, 0) }}
                                    @endif
                                </h3>
                                <p class="text-muted mb-0">
                                    @if($offer->type === 'percentage')
                                        Percentage Off
                                    @else
                                        Fixed Discount
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Usage Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Usage Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info">{{ $offer->used_count }}</h4>
                            <p class="text-muted mb-0">Times Used</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">
                                @if($offer->usage_limit)
                                    {{ $offer->usage_limit - $offer->used_count }}
                                @else
                                    ∞
                                @endif
                            </h4>
                            <p class="text-muted mb-0">Remaining Uses</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">
                                @if($offer->end_date < today())
                                    0
                                @else
                                    {{ $offer->end_date->diffInDays(today()) }}
                                @endif
                            </h4>
                            <p class="text-muted mb-0">Days Remaining</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">
                                @if($offer->usage_limit)
                                    {{ round(($offer->used_count / $offer->usage_limit) * 100) }}%
                                @else
                                    N/A
                                @endif
                            </h4>
                            <p class="text-muted mb-0">Usage Rate</p>
                        </div>
                    </div>
                </div>
                
                @if($offer->usage_limit)
                <div class="mt-3">
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ ($offer->used_count / $offer->usage_limit) * 100 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $offer->used_count }}/{{ $offer->usage_limit }} uses</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>Quick Actions</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Offer
                    </a>
                    
                    <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $offer->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $offer->is_active ? 'eye-slash' : 'eye' }}"></i> 
                            {{ $offer->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash"></i> Delete Offer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h6>Offer Validity</h6>
                @if($offer->start_date > today())
                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i> Starts in {{ $offer->start_date->diffInDays(today()) }} days
                    </div>
                @elseif($offer->end_date < today())
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Expired {{ $offer->end_date->diffForHumans() }}
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Currently Active
                    </div>
                @endif
                
                @if($offer->usage_limit && $offer->used_count >= $offer->usage_limit)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Usage limit reached
                    </div>
                @endif
            </div>
        </div>
        
        @if($offer->code)
        <div class="card mt-3">
            <div class="card-body">
                <h6>Coupon Code</h6>
                <div class="input-group">
                    <input type="text" class="form-control" value="{{ $offer->code }}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('{{ $offer->code }}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <small class="text-muted">Share this code with customers</small>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Coupon code copied to clipboard!');
    });
}
</script>
@endpush
@endsection
