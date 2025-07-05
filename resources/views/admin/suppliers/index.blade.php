@extends('admin.layouts.app')

@section('title', 'Suppliers')
@section('page_title', 'Supplier Management')

@section('page_actions')
<a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Supplier
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.suppliers.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search suppliers...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($suppliers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Credit Terms</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $supplier->display_name }}</strong>
                                @if($supplier->company_name && $supplier->name !== $supplier->company_name)
                                    <br><small class="text-muted">Contact: {{ $supplier->name }}</small>
                                @endif
                                @if($supplier->gst_number)
                                    <br><span class="badge bg-info">GST: {{ $supplier->gst_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-phone text-primary"></i> {{ $supplier->phone }}
                                @if($supplier->mobile)
                                    <br><i class="fas fa-mobile text-success"></i> {{ $supplier->mobile }}
                                @endif
                                @if($supplier->email)
                                    <br><i class="fas fa-envelope text-info"></i> {{ $supplier->email }}
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $supplier->city }}@if($supplier->state), {{ $supplier->state }}@endif
                            <br><small class="text-muted">{{ $supplier->pincode }}</small>
                        </td>
                        <td>
                            <div>
                                <strong>Credit Limit:</strong> ₹{{ number_format($supplier->credit_limit, 0) }}
                                <br><strong>Credit Days:</strong> {{ $supplier->credit_days }} days
                            </div>
                        </td>
                        <td>
                            @if($supplier->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.toggle-status', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $supplier->is_active ? 'warning' : 'success' }}" title="{{ $supplier->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $supplier->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
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

        <div class="d-flex justify-content-center mt-3">
            {{ $suppliers->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
            <h5>No suppliers found</h5>
            <p class="text-muted">Start by adding your first supplier to manage procurement.</p>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Supplier
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
