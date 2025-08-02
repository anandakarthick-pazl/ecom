@extends('admin.layouts.app')

@section('title', 'Offers')
@section('page_title', 'Offers')

@section('page_actions')
<a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Offer
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($offers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Offer Name</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Target</th>
                        <th>Validity</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $offer)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $offer->name }}</strong>
                                @if($offer->code)
                                    <br><span class="badge bg-info">{{ $offer->code }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $offer->discount_type_display ?? ucfirst($offer->type) }}</span>
                        </td>
                        <td>
                            {{ $offer->discount_value_display ?? ($offer->type === 'percentage' ? $offer->value . '%' : '₹' . number_format($offer->value, 2)) }}
                        </td>
                        <td>
                            @if($offer->type === 'category' && $offer->category)
                                <span class="badge bg-primary">{{ $offer->category->name }}</span>
                            @elseif($offer->type === 'product' && $offer->product)
                                <span class="badge bg-success">{{ Str::limit($offer->product->name, 20) }}</span>
                            @else
                                <span class="text-muted">All Products</span>
                            @endif
                        </td>
                        <td>
                            <small>
                                {{ $offer->start_date->format('M d') }} - {{ $offer->end_date->format('M d, Y') }}
                                <br>
                                @if($offer->end_date < today())
                                    <span class="text-danger">Expired</span>
                                @elseif($offer->start_date > today())
                                    <span class="text-warning">Upcoming</span>
                                @else
                                    <span class="text-success">Active Period</span>
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($offer->usage_limit)
                                {{ $offer->used_count }}/{{ $offer->usage_limit }}
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" style="width: {{ ($offer->used_count / $offer->usage_limit) * 100 }}%"></div>
                                </div>
                            @else
                                <span class="text-muted">{{ $offer->used_count }} used</span>
                            @endif
                        </td>
                        <td>
                            @if($offer->isValid())
                                <span class="badge bg-success">Valid</span>
                            @else
                                <span class="badge bg-danger">Invalid</span>
                            @endif
                            @if(!$offer->is_active)
                                <br><small class="text-muted">Disabled</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.offers.show', $offer) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $offer->is_active ? 'warning' : 'success' }}" title="{{ $offer->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $offer->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this offer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($enablePagination)
            <div class="d-flex justify-content-center mt-3">
                {{ $offers->links() }}
            </div>
        @endif
        @else
        <div class="text-center py-4">
            <i class="fas fa-percent fa-3x text-muted mb-3"></i>
            <h5>No offers found</h5>
            <p class="text-muted">Create your first offer to boost sales and attract customers.</p>
            <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Offer
            </a>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.progress {
    background: #e9ecef;
}
.progress-bar {
    background: #007bff;
}
</style>
@endpush
@endsection
