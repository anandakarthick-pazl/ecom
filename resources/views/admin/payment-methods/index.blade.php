@extends('admin.layouts.app')

@section('title', 'Payment Methods')
@section('page_title', 'Payment Methods')

@section('page_actions')
<a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Add Payment Method
</a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($paymentMethods->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="payment-methods-table">
                    <thead>
                        <tr>
                            <th width="40"><i class="fas fa-sort"></i></th>
                            <th>Method</th>
                            <th>Display Name</th>
                            <th>Type</th>
                            <th>Charges</th>
                            <th>Min/Max Amount</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-methods">
                        @foreach($paymentMethods as $method)
                            <tr data-id="{{ $method->id }}">
                                <td class="handle">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($method->hasImage())
                                            <img src="{{ $method->getImageUrl() }}" 
                                                 class="me-2" 
                                                 style="max-height: 30px; max-width: 50px; object-fit: contain;" 
                                                 alt="{{ $method->display_name }}">
                                        @else
                                            <i class="{{ $method->getIcon() }} text-{{ $method->getColor() }} me-2"></i>
                                        @endif
                                        <div>
                                            <strong>{{ $method->display_name }}</strong>
                                            @if($method->description)
                                                <br><small class="text-muted">{{ Str::limit($method->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $method->display_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $method->getColor() }}">
                                        {{ ucfirst($method->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($method->extra_charge > 0 || $method->extra_charge_percentage > 0)
                                        <small>
                                            @if($method->extra_charge > 0)
                                                Fixed: ₹{{ number_format($method->extra_charge, 2) }}<br>
                                            @endif
                                            @if($method->extra_charge_percentage > 0)
                                                Percentage: {{ $method->extra_charge_percentage }}%
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No charges</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        @if($method->minimum_amount > 0)
                                            Min: ₹{{ number_format($method->minimum_amount, 2) }}<br>
                                        @endif
                                        @if($method->maximum_amount > 0)
                                            Max: ₹{{ number_format($method->maximum_amount, 2) }}
                                        @else
                                            <span class="text-muted">No limit</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-status" 
                                               type="checkbox" 
                                               data-id="{{ $method->id }}"
                                               {{ $method->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.payment-methods.edit', $method) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.payment-methods.destroy', $method) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                <p class="text-muted">No payment methods configured yet.</p>
                <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Payment Method
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Payment Method Information Cards -->
<div class="row mt-4">
    <div class="col-md-2">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-credit-card"></i> Razorpay
                </h6>
                <p class="card-text small">
                    Accept online payments via cards, UPI, wallets, and netbanking.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                </h6>
                <p class="card-text small">
                    Customers pay when they receive the product.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-university"></i> Bank Transfer
                </h6>
                <p class="card-text small">
                    Direct bank transfers to your account.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-mobile-alt"></i> UPI
                </h6>
                <p class="card-text small">
                    Instant payments via UPI ID or QR code.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fab fa-google-pay"></i> Google Pay
                </h6>
                <p class="card-text small">
                    Quick payments using Google Pay UPI.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle status
    $('.toggle-status').change(function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        
        $.ajax({
            url: `/admin/payment-methods/${id}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showToast('Payment method status updated successfully', 'success');
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                showToast('Failed to update status', 'error');
            }
        });
    });
    
    // Sortable for reordering
    if (document.getElementById('sortable-methods')) {
        const sortable = Sortable.create(document.getElementById('sortable-methods'), {
            handle: '.handle',
            animation: 150,
            onEnd: function (evt) {
                const items = [];
                $('#sortable-methods tr').each(function(index) {
                    items.push({
                        id: $(this).data('id'),
                        sort_order: index
                    });
                });
                
                $.ajax({
                    url: '{{ route("admin.payment-methods.update-sort-order") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        items: items
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Order updated successfully', 'success');
                        }
                    },
                    error: function() {
                        showToast('Failed to update order', 'error');
                    }
                });
            }
        });
    }
});

function showToast(message, type) {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    if(!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    $('.toast-container').append(toast);
    new bootstrap.Toast(toast[0]).show();
}
</script>
@endpush

@push('styles')
<style>
.handle {
    cursor: move;
}
.sortable-ghost {
    opacity: 0.5;
    background: #f8f9fa;
}
</style>
@endpush
