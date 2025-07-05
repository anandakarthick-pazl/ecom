@extends('admin.layouts.app')

@section('title', 'Estimates')
@section('page_title', 'Estimate Management')

@section('page_actions')
<a href="{{ route('admin.estimates.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Create Estimate
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.estimates.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search estimates...">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($estimates->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Estimate #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Valid Until</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estimates as $estimate)
                    <tr>
                        <td>
                            <strong>{{ $estimate->estimate_number }}</strong>
                            <br><small class="text-muted">{{ $estimate->items->count() }} items</small>
                        </td>
                        <td>
                            <strong>{{ $estimate->customer_name }}</strong>
                            @if($estimate->customer_email)
                                <br><small class="text-muted">{{ $estimate->customer_email }}</small>
                            @endif
                            @if($estimate->customer_phone)
                                <br><small class="text-muted">{{ $estimate->customer_phone }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $estimate->estimate_date->format('M d, Y') }}
                            <br><small class="text-muted">{{ $estimate->estimate_date->diffForHumans() }}</small>
                        </td>
                        <td>
                            {{ $estimate->valid_until->format('M d, Y') }}
                            @if($estimate->is_expired)
                                <br><span class="badge bg-warning">Expired</span>
                            @elseif($estimate->valid_until < today()->addDays(3))
                                <br><span class="badge bg-info">Expiring Soon</span>
                            @endif
                        </td>
                        <td>
                            <strong>₹{{ number_format($estimate->total_amount, 2) }}</strong>
                            @if($estimate->discount > 0)
                                <br><small class="text-success">-₹{{ number_format($estimate->discount, 2) }} discount</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $estimate->status_color }}">
                                {{ ucfirst($estimate->status) }}
                            </span>
                            @if($estimate->sent_at)
                                <br><small class="text-muted">Sent {{ $estimate->sent_at->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.estimates.show', $estimate) }}" class="btn btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($estimate->status === 'draft')
                                    <a href="{{ route('admin.estimates.edit', $estimate) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($estimate->status === 'draft')
                                            <li>
                                                <form action="{{ route('admin.estimates.update-status', $estimate) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="sent">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-paper-plane"></i> Send to Customer
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if(in_array($estimate->status, ['sent']))
                                            <li>
                                                <form action="{{ route('admin.estimates.update-status', $estimate) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check"></i> Mark Accepted
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('admin.estimates.duplicate', $estimate) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-copy"></i> Duplicate
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $estimates->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-calculator fa-3x text-muted mb-3"></i>
            <h5>No estimates found</h5>
            <p class="text-muted">Create your first estimate to start quoting customers.</p>
            <a href="{{ route('admin.estimates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Estimate
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
