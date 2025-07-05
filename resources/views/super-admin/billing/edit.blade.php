@extends('super-admin.layouts.app')

@section('title', 'Edit Billing Record')
@section('page-title', 'Edit Billing Record')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Billing Record: {{ $billing->invoice_number }}
                </h5>
                <div>
                    <a href="{{ route('super-admin.billing.show', $billing) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('super-admin.billing.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Billing
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($billing->status === 'paid')
                    <div class="alert alert-warning">
                        <i class="fas fa-lock me-2"></i>
                        <strong>Notice:</strong> This is a paid record. Some fields may be restricted from editing to maintain financial integrity.
                    </div>
                @endif
                
                <form action="{{ route('super-admin.billing.update', $billing) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Billing Information -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company</label>
                                            <div class="form-control-plaintext">
                                                <div class="d-flex align-items-center">
                                                    @if($billing->company->logo)
                                                        <img src="{{ asset('storage/' . $billing->company->logo) }}" 
                                                             class="rounded me-2" width="32" height="32" 
                                                             style="object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 32px; height: 32px; font-size: 12px;">
                                                            {{ strtoupper(substr($billing->company->name, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $billing->company->name }}</strong>
                                                        <br><small class="text-muted">{{ $billing->company->domain }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Company cannot be changed after creation.</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Package</label>
                                            <div class="form-control-plaintext">
                                                <span class="badge bg-info">{{ $billing->package->name }}</span>
                                                <br><small class="text-muted">${{ number_format($billing->package->price, 2) }}/{{ $billing->package->billing_cycle }}</small>
                                            </div>
                                            <small class="form-text text-muted">Package cannot be changed after creation.</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" value="{{ old('amount', $billing->amount) }}" 
                                                       min="0" step="0.01" required {{ $billing->status === 'paid' ? 'readonly' : '' }}>
                                                @error('amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if($billing->status === 'paid')
                                                <small class="form-text text-muted">Amount cannot be changed for paid records.</small>
                                            @endif
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                @foreach(App\Models\SuperAdmin\Billing::STATUSES as $key => $name)
                                                    <option value="{{ $key }}" {{ old('status', $billing->status) == $key ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" name="payment_method">
                                                <option value="">Select Method</option>
                                                <option value="credit_card" {{ old('payment_method', $billing->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="paypal" {{ old('payment_method', $billing->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="bank_transfer" {{ old('payment_method', $billing->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ old('payment_method', $billing->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="cash" {{ old('payment_method', $billing->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="other" {{ old('payment_method', $billing->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="transaction_id" class="form-label">Transaction ID</label>
                                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                                   id="transaction_id" name="transaction_id" 
                                                   value="{{ old('transaction_id', $billing->transaction_id) }}">
                                            @error('transaction_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Reference ID from payment processor.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Billing Dates -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Dates</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_date" class="form-label">Billing Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('billing_date') is-invalid @enderror" 
                                                   id="billing_date" name="billing_date" 
                                                   value="{{ old('billing_date', $billing->billing_date->format('Y-m-d')) }}" 
                                                   required {{ $billing->status === 'paid' ? 'readonly' : '' }}>
                                            @error('billing_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                                   id="due_date" name="due_date" 
                                                   value="{{ old('due_date', $billing->due_date->format('Y-m-d')) }}" required>
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    @if($billing->paid_at)
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Paid Date</label>
                                                <div class="form-control-plaintext">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        {{ $billing->paid_at->format('M d, Y g:i A') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="4">{{ old('notes', $billing->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Billing Summary -->
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Billing Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h5>{{ $billing->invoice_number }}</h5>
                                        <p class="text-muted">{{ $billing->company->name }}</p>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Package:</span>
                                        <span>{{ $billing->package->name }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Amount:</span>
                                        <strong class="preview-amount">${{ number_format($billing->amount, 2) }}</strong>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Billing Cycle:</span>
                                        <span>{{ ucfirst($billing->billing_cycle) }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Status:</span>
                                        <span class="preview-status">
                                            <span class="badge {{ $billing->status === 'paid' ? 'bg-success' : ($billing->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ $billing->status_name }}
                                            </span>
                                        </span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Billing Date:</span>
                                        <span>{{ $billing->billing_date->format('M d, Y') }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Due Date:</span>
                                        <span class="preview-due-date">{{ $billing->due_date->format('M d, Y') }}</span>
                                    </div>
                                    
                                    @if($billing->isOverdue() && $billing->status === 'pending')
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Overdue</strong><br>
                                            <small>{{ $billing->due_date->diffForHumans() }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($billing->status === 'pending')
                                            <button type="button" class="btn btn-success btn-sm" onclick="markAsPaid()">
                                                <i class="fas fa-check me-2"></i>Mark as Paid
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('super-admin.billing.invoice', $billing) }}" 
                                           class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-file-invoice me-2"></i>View Invoice
                                        </a>
                                        
                                        <a href="{{ route('super-admin.companies.show', $billing->company) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-building me-2"></i>View Company
                                        </a>
                                        
                                        @if($billing->status !== 'paid')
                                            <hr>
                                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                <i class="fas fa-trash me-2"></i>Delete Record
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Record Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Record Information</h6>
                                </div>
                                <div class="card-body">
                                    <small class="text-muted">
                                        <strong>Created:</strong><br>
                                        {{ $billing->created_at->format('M d, Y g:i A') }}<br>
                                        {{ $billing->created_at->diffForHumans() }}
                                    </small>
                                    
                                    <hr>
                                    
                                    <small class="text-muted">
                                        <strong>Last Updated:</strong><br>
                                        {{ $billing->updated_at->format('M d, Y g:i A') }}<br>
                                        {{ $billing->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super-admin.billing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Billing Record
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete billing record <strong>{{ $billing->invoice_number }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('super-admin.billing.destroy', $billing) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update preview when amount changes
    $('#amount').on('input', function() {
        const amount = $(this).val() || '0';
        $('.preview-amount').text('$' + parseFloat(amount).toFixed(2));
    });
    
    // Update preview when status changes
    $('#status').on('change', function() {
        const status = $(this).val();
        const statusText = $(this).find('option:selected').text();
        let badgeClass = 'bg-secondary';
        
        switch(status) {
            case 'paid':
                badgeClass = 'bg-success';
                break;
            case 'pending':
                badgeClass = 'bg-warning text-dark';
                break;
            case 'overdue':
                badgeClass = 'bg-danger';
                break;
        }
        
        $('.preview-status').html(`<span class="badge ${badgeClass}">${statusText}</span>`);
    });
    
    // Update preview when due date changes
    $('#due_date').on('change', function() {
        const dueDate = $(this).val();
        if (dueDate) {
            const formattedDate = new Date(dueDate).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            $('.preview-due-date').text(formattedDate);
        }
    });
    
    // Mark as paid function
    window.markAsPaid = function() {
        if (confirm('Mark this billing record as paid? This will set the status to "Paid" and record the current timestamp.')) {
            $('#status').val('paid');
            $('#status').trigger('change');
            
            // Optionally submit the form or make an AJAX call
            $('form').submit();
        }
    };
});
</script>
@endpush
